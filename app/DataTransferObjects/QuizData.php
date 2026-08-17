<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class QuizData extends BaseData
{
    public function __construct(
        public readonly ?int $moduleId,
        public readonly ?int $lessonId,
        public readonly string $title,
        public readonly ?string $slug,
        public readonly ?string $description,
        public readonly ?string $level,
        public readonly string $category,
        public readonly string $difficulty,
        public readonly ?int $timeLimitSeconds,
        public readonly int $passScore,
        public readonly int $xpReward,
        public readonly ?int $maxAttempts,
        public readonly bool $shuffleQuestions,
        public readonly bool $shuffleOptions,
        public readonly bool $showExplanation,
        public readonly bool $isPublished,
        /**
         * Nested question payload:
         * [['question' => ..., 'type' => ..., 'options' => [['label' => ..., 'is_correct' => bool]]]]
         *
         * @var array<int, array<string, mixed>>
         */
        public readonly array $questions = [],
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        $title = (string) $request->string('title');

        return new static(
            moduleId: $request->filled('learning_module_id') ? (int) $request->input('learning_module_id') : null,
            lessonId: $request->filled('lesson_id') ? (int) $request->input('lesson_id') : null,
            title: $title,
            slug: $request->filled('slug') ? Str::slug((string) $request->string('slug')) : Str::slug($title),
            description: $request->input('description'),
            level: $request->input('level'),
            category: (string) $request->input('category', 'quiz'),
            difficulty: (string) $request->input('difficulty', 'easy'),
            timeLimitSeconds: $request->filled('time_limit_minutes')
                ? (int) $request->input('time_limit_minutes') * 60
                : null,
            passScore: (int) $request->input('pass_score', 70),
            xpReward: (int) $request->input('xp_reward', 50),
            maxAttempts: $request->filled('max_attempts') ? (int) $request->input('max_attempts') : null,
            shuffleQuestions: $request->boolean('shuffle_questions'),
            shuffleOptions: $request->boolean('shuffle_options'),
            showExplanation: $request->boolean('show_explanation'),
            isPublished: $request->boolean('is_published'),
            questions: (array) $request->input('questions', []),
        );
    }

    public function toArray(): array
    {
        return [
            'learning_module_id' => $this->moduleId,
            'lesson_id' => $this->lessonId,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'level' => $this->level,
            'category' => $this->category,
            'difficulty' => $this->difficulty,
            'time_limit_seconds' => $this->timeLimitSeconds,
            'pass_score' => $this->passScore,
            'xp_reward' => $this->xpReward,
            'max_attempts' => $this->maxAttempts,
            'shuffle_questions' => $this->shuffleQuestions,
            'shuffle_options' => $this->shuffleOptions,
            'show_explanation' => $this->showExplanation,
            'is_published' => $this->isPublished,
        ];
    }
}
