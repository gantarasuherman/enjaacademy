<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VocabularyWordResource;
use App\Models\DailyQuizQuestion;
use App\Models\Language;
use App\Models\VocabularyWord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VocabularyWordController extends Controller
{
    /**
     * Search/filter the vocabulary bank — same pool the (English) Daily Quiz
     * samples from, plus the Japanese words imported from lesson content.
     * `language` is a slug ("english"/"japanese"); omitted, all languages mix.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $languageId = $request->filled('language')
            ? Language::where('slug', $request->query('language'))->value('id')
            : null;

        $words = VocabularyWord::query()
            ->with('examples')
            ->when($languageId, fn ($q) => $q->where('language_id', $languageId))
            ->forLevel($request->query('level'))
            ->search($request->query('search'))
            ->orderBy('word')
            ->paginate(min((int) $request->query('perPage', 30), 100));

        return VocabularyWordResource::collection($words);
    }

    /**
     * Per-level progress: how many distinct words in each level the user has
     * ever answered correctly in a Daily Quiz, against that level's total —
     * e.g. "Beginner 450/1000".
     */
    public function progress(Request $request): JsonResponse
    {
        $language = Language::where('slug', 'english')->first();

        $totals = VocabularyWord::query()
            ->when($language, fn ($q) => $q->where('language_id', $language->id))
            ->selectRaw('level, count(*) as total')
            ->groupBy('level')
            ->pluck('total', 'level');

        $learned = DailyQuizQuestion::query()
            ->join('daily_quiz_attempts', 'daily_quiz_attempts.id', '=', 'daily_quiz_questions.daily_quiz_attempt_id')
            ->join('vocabulary_words', 'vocabulary_words.id', '=', 'daily_quiz_questions.vocabulary_word_id')
            ->where('daily_quiz_attempts.user_id', $request->user()->id)
            ->where('daily_quiz_questions.is_correct', true)
            ->selectRaw('vocabulary_words.level, count(distinct daily_quiz_questions.vocabulary_word_id) as learned')
            ->groupBy('vocabulary_words.level')
            ->pluck('learned', 'level');

        $levels = ['Beginner', 'Elementary', 'Intermediate', 'Upper-Intermediate', 'Advanced'];

        return response()->json(['data' => collect($levels)->map(fn ($level) => [
            'level' => $level,
            'total' => (int) ($totals[$level] ?? 0),
            'learned' => (int) ($learned[$level] ?? 0),
        ])->values()]);
    }
}
