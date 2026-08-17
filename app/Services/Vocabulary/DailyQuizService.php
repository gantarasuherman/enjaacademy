<?php

declare(strict_types=1);

namespace App\Services\Vocabulary;

use App\Models\DailyQuizAttempt;
use App\Models\DailyQuizQuestion;
use App\Models\Language;
use App\Models\User;
use App\Models\UserWeakWord;
use App\Models\VocabularyWord;
use App\Models\VocabularyWordExample;
use App\Services\Gamification\ProgressService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * The mandatory pre-learning quiz: 20 questions a day (default), sampled
 * deterministically from the vocabulary bank so the same user reloading the
 * page mid-quiz sees the same questions, but a fresh set appears each day.
 *
 * Question language follows the student's active course (see
 * `resolveLanguageFor()`), falling back to English for students who haven't
 * enrolled in anything yet.
 */
class DailyQuizService
{
    public const DEFAULT_QUESTION_COUNT = 20;

    public const MASTERY_STREAK = 3;

    private const TYPES = ['multiple_choice', 'fill_blank', 'matching', 'true_false', 'context'];

    public function __construct(private readonly ProgressService $progress) {}

    public function status(User $user): array
    {
        $today = today();
        $attempt = DailyQuizAttempt::where('user_id', $user->id)->where('quiz_date', $today)->first();

        return [
            'required' => true,
            'hasAttemptToday' => $attempt !== null,
            'completedToday' => $attempt?->completed_at !== null,
            'skippedToday' => (bool) $attempt?->skipped,
            'satisfiedToday' => $attempt !== null && $attempt->isFinished(),
        ];
    }

    /** Idempotent per (user, day) — reloading mid-quiz returns the same in-progress attempt. */
    public function getOrCreateTodayAttempt(User $user): DailyQuizAttempt
    {
        $today = today();

        $existing = DailyQuizAttempt::with('questions.word.examples')
            ->where('user_id', $user->id)
            ->where('quiz_date', $today)
            ->first();

        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($user, $today) {
            $attempt = DailyQuizAttempt::create([
                'user_id' => $user->id,
                'quiz_date' => $today,
                'total_questions' => 0,
                'correct_count' => 0,
                'score' => 0,
            ]);

            $this->generateQuestions($user, $attempt, $today);

            return $attempt->fresh(['questions.word.examples']);
        });
    }

    public function skip(User $user): DailyQuizAttempt
    {
        $today = today();

        $existing = DailyQuizAttempt::where('user_id', $user->id)->where('quiz_date', $today)->first();

        if ($existing && $existing->isFinished()) {
            throw ValidationException::withMessages([
                'quiz' => __('You have already finished or skipped today\'s quiz.'),
            ]);
        }

        return DB::transaction(function () use ($user, $today, $existing) {
            $attempt = $existing ?? DailyQuizAttempt::create([
                'user_id' => $user->id,
                'quiz_date' => $today,
            ]);

            $attempt->questions()->delete();
            $attempt->update(['skipped' => true, 'completed_at' => now()]);

            return $attempt;
        });
    }

    /**
     * @param  array<int, array{questionId: int, answer: string}>  $answers
     */
    public function submit(User $user, DailyQuizAttempt $attempt, array $answers): DailyQuizAttempt
    {
        if ($attempt->user_id !== $user->id) {
            abort(403);
        }

        if ($attempt->isFinished()) {
            throw ValidationException::withMessages(['quiz' => __('This quiz has already been submitted.')]);
        }

        return DB::transaction(function () use ($user, $attempt, $answers) {
            $questions = $attempt->questions()->with('word')->get()->keyBy('id');
            $correct = 0;

            foreach ($answers as $row) {
                /** @var DailyQuizQuestion|null $question */
                $question = $questions->get((int) $row['questionId']);

                if (! $question) {
                    continue;
                }

                $given = (string) ($row['answer'] ?? '');
                $isCorrect = $this->grade($question, $given);
                $correct += $isCorrect ? 1 : 0;

                $question->update(['given_answer' => $given, 'is_correct' => $isCorrect]);

                $this->trackWeakWord($attempt->user_id, $question->vocabulary_word_id, $isCorrect);
            }

            $total = $questions->count();

            // Base completion XP is guaranteed (the quiz is mandatory every
            // day) — the perfect-score bonus mirrors how QuizService rewards
            // a flawless attempt, so acing it feels worth the extra effort.
            $xp = (int) config('admin.gamification.xp.daily_quiz_completed');
            if ($total > 0 && $correct === $total) {
                $xp += (int) config('admin.gamification.xp.daily_quiz_perfect_bonus');
            }

            $attempt->update([
                'total_questions' => $total,
                'correct_count' => $correct,
                'score' => $total > 0 ? (int) round($correct / $total * 100) : 0,
                'earned_xp' => $xp,
                'completed_at' => now(),
            ]);

            $this->progress->awardXp($user, $xp, __('Daily quiz completed'), $attempt);

            return $attempt->fresh(['questions.word']);
        });
    }

    private function grade(DailyQuizQuestion $question, string $given): bool
    {
        $payload = $question->payload;
        $normalise = fn (string $v) => trim(mb_strtolower($v));

        return match ($question->type) {
            'fill_blank' => $normalise($given) === $normalise((string) $payload['answer']),
            default => $normalise($given) === $normalise((string) $payload['correctAnswer']),
        };
    }

    private function trackWeakWord(int $userId, int $wordId, bool $isCorrect): void
    {
        $weak = UserWeakWord::firstOrNew(['user_id' => $userId, 'vocabulary_word_id' => $wordId]);

        if (! $isCorrect) {
            $weak->wrong_count = ($weak->wrong_count ?? 0) + 1;
            $weak->correct_streak = 0;
            $weak->last_wrong_at = now();
            $weak->mastered = false;
            $weak->save();

            return;
        }

        // Only track a growing correct-streak (towards mastery) for words that
        // are already flagged weak — a word the user has never missed doesn't
        // need a row here at all.
        if ($weak->exists) {
            $weak->correct_streak++;
            $weak->mastered = $weak->correct_streak >= self::MASTERY_STREAK;
            $weak->save();
        }
    }

    private function generateQuestions(User $user, DailyQuizAttempt $attempt, Carbon $date): void
    {
        $language = $this->resolveLanguageFor($user);
        $seed = crc32("{$user->id}-{$date->toDateString()}");

        $words = $this->pickWords($language->id, self::DEFAULT_QUESTION_COUNT, $seed);

        if ($words->isEmpty()) {
            return;
        }

        // A wider decoy pool for building wrong-answer options, sampled with a
        // different seed so it doesn't just mirror today's own word list.
        $decoyPool = $this->pickWords($language->id, 60, $seed + 1)->reject(
            fn (VocabularyWord $w) => $words->contains('id', $w->id)
        )->values();

        // Fresh deterministic stream for payload-building randomness (e.g. the
        // true/false coin flip) — separate from the seeds `pickWords()` already
        // consumed above, so this stays reproducible regardless of pool size.
        mt_srand($seed + 2);
        $types = self::TYPES;

        foreach ($words->values() as $index => $word) {
            $type = $types[$index % count($types)];
            $decoys = $decoyPool->slice(($index * 3) % max($decoyPool->count() - 3, 1), 3)->values();

            DailyQuizQuestion::create([
                'daily_quiz_attempt_id' => $attempt->id,
                'vocabulary_word_id' => $word->id,
                'type' => $type,
                'payload' => $this->buildPayload($word, $type, $decoys),
                'sort_order' => $index,
            ]);
        }

        $attempt->update(['total_questions' => $words->count()]);
    }

    /**
     * Which language the Daily Quiz draws from: the student's active course
     * first, then their most recently enrolled course, then English as a
     * last resort — so quiz generation always succeeds even for a student
     * who hasn't enrolled in anything yet.
     */
    private function resolveLanguageFor(User $user): Language
    {
        $activeLanguage = $user->activeModule?->language;

        if ($activeLanguage) {
            return $activeLanguage;
        }

        $recentEnrollment = $user->enrollments()
            ->with('learningModule.language')
            ->latest('enrolled_at')
            ->first();

        if ($recentEnrollment?->learningModule?->language) {
            return $recentEnrollment->learningModule->language;
        }

        return Language::where('slug', 'english')->firstOrFail();
    }

    /** @return Collection<int, VocabularyWord> */
    private function pickWords(int $languageId, int $count, int $seed): Collection
    {
        $ids = VocabularyWord::where('language_id', $languageId)->orderBy('id')->pluck('id')->all();

        if ($ids === []) {
            return collect();
        }

        $picked = $this->seededSample($ids, $count, $seed);
        $words = VocabularyWord::with('examples')->whereIn('id', $picked)->get()->keyBy('id');

        return collect($picked)->map(fn ($id) => $words->get($id))->filter()->values();
    }

    /**
     * Deterministic partial Fisher-Yates: the same $seed always yields the
     * same subset in the same order (needed for the "user_id + date as seed"
     * requirement), without pulling every row's full data just to shuffle —
     * `$ids` here is a plain array of ids, not hydrated models.
     *
     * @param  array<int, int>  $ids
     * @return array<int, int>
     */
    private function seededSample(array $ids, int $count, int $seed): array
    {
        mt_srand($seed);

        $pool = array_values($ids);
        $poolSize = count($pool);
        $take = min($count, $poolSize);
        $picked = [];

        for ($i = 0; $i < $take; $i++) {
            $index = mt_rand(0, $poolSize - 1 - $i);
            $picked[] = $pool[$index];
            $pool[$index] = $pool[$poolSize - 1 - $i];
        }

        return $picked;
    }

    private function buildPayload(VocabularyWord $word, string $type, Collection $decoys): array
    {
        $example = $word->examples->first();

        return match ($type) {
            'multiple_choice' => $this->multipleChoicePayload($word, $decoys),
            'true_false' => $this->trueFalsePayload($word, $decoys),
            'fill_blank' => $this->fillBlankPayload($word, $example),
            'context' => $this->contextPayload($word, $decoys, $example),
            'matching' => $this->matchingPayload($word, $decoys),
            default => $this->multipleChoicePayload($word, $decoys),
        };
    }

    private function multipleChoicePayload(VocabularyWord $word, Collection $decoys): array
    {
        $options = $decoys->pluck('meaning_id')->push($word->meaning_id)->shuffle()->values()->all();

        return [
            'prompt' => "Apa arti dari kata \"{$word->word}\"?",
            'options' => $options,
            'correctAnswer' => $word->meaning_id,
        ];
    }

    private function trueFalsePayload(VocabularyWord $word, Collection $decoys): array
    {
        $showCorrect = (bool) mt_rand(0, 1);
        $shownMeaning = $showCorrect ? $word->meaning_id : ($decoys->first()?->meaning_id ?? $word->meaning_id);

        return [
            'prompt' => "\"{$word->word}\" berarti \"{$shownMeaning}\". Benar atau salah?",
            'options' => ['Benar', 'Salah'],
            'correctAnswer' => $showCorrect ? 'Benar' : 'Salah',
        ];
    }

    private function fillBlankPayload(VocabularyWord $word, ?VocabularyWordExample $example): array
    {
        $blanked = $this->blankOutWord($word->word, $example?->sentence_en);
        $hint = $this->buildLetterHint($word->word);

        if ($blanked !== null) {
            return [
                'prompt' => "Lengkapi kalimat berikut: \"{$blanked}\"",
                'hint' => $hint,
                'answer' => $word->word,
            ];
        }

        return [
            'prompt' => "Tuliskan kata yang berarti: \"{$word->meaning_id}\"",
            'hint' => $hint,
            'answer' => $word->word,
        ];
    }

    /**
     * A hangman-style letter pattern (e.g. "p__________e") — reveals only
     * the first/last letter of each space-separated token, safe to send to
     * the client since the middle letters (the actual guess) stay hidden.
     * Short tokens (<=2 letters) are fully masked instead, since revealing
     * both ends of a 2-letter word reveals the whole word.
     */
    private function buildLetterHint(string $word): string
    {
        return collect(explode(' ', $word))
            ->map(function (string $token) {
                $len = mb_strlen($token);

                if ($len <= 2) {
                    return str_repeat('_', $len);
                }

                return mb_substr($token, 0, 1).str_repeat('_', $len - 2).mb_substr($token, -1);
            })
            ->implode(' ');
    }

    private function contextPayload(VocabularyWord $word, Collection $decoys, ?VocabularyWordExample $example): array
    {
        $options = $decoys->pluck('word')->push($word->word)->shuffle()->values()->all();
        $blanked = $this->blankOutWord($word->word, $example?->sentence_en);

        if ($blanked !== null) {
            return [
                'prompt' => "Pilih kata yang tepat untuk melengkapi kalimat: \"{$blanked}\"",
                'options' => $options,
                'correctAnswer' => $word->word,
            ];
        }

        return [
            'prompt' => "Kata mana yang berarti \"{$word->meaning_id}\"?",
            'options' => $options,
            'correctAnswer' => $word->word,
        ];
    }

    /**
     * Replaces `$word` in `$sentence` with a blank, returning null (not the
     * unmodified sentence) if it can't find an exact match — e.g. a phrasal
     * verb/idiom whose example sentence uses a different inflection
     * ("came across" vs. the dictionary form "come across"). Silently
     * falling back to the un-blanked sentence would leak the answer in the
     * question text, so callers must fall back to a different question shape
     * entirely when this returns null.
     */
    private function blankOutWord(string $word, ?string $sentence): ?string
    {
        if ($sentence === null) {
            return null;
        }

        $blanked = preg_replace('/\b'.preg_quote($word, '/').'\b/i', '____', $sentence, 1, $count);

        return $count > 0 ? $blanked : null;
    }

    private function matchingPayload(VocabularyWord $word, Collection $decoys): array
    {
        // Simplification: a full multi-pair matching board would need several
        // words to share one graded question, which doesn't fit the
        // one-question-per-word schema used for weak-word tracking. Instead
        // this shows the primary word alongside 3 decoy (word, meaning) pairs
        // as visual "matching" noise — only the primary word's own pairing is
        // graded, the decoys are display-only and never scored individually.
        $pairs = $decoys->take(3)->map(fn (VocabularyWord $w) => ['word' => $w->word, 'meaning' => $w->meaning_id])
            ->push(['word' => $word->word, 'meaning' => $word->meaning_id])
            ->shuffle()
            ->values()
            ->all();

        return [
            'prompt' => "Cocokkan kata \"{$word->word}\" dengan artinya.",
            'pairs' => $pairs,
            'correctAnswer' => $word->meaning_id,
        ];
    }
}
