<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Quiz;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class QuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        $quiz = $this->route('quiz');

        return $quiz instanceof Quiz
            ? $this->user()->can('update', $quiz)
            : $this->user()->can('create', Quiz::class);
    }

    public function rules(): array
    {
        $id = $this->route('quiz')?->id;

        return [
            'learning_module_id' => ['nullable', 'integer', Rule::exists('learning_modules', 'id')],
            'lesson_id' => ['nullable', 'integer', Rule::exists('lessons', 'id')],
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', Rule::unique('quizzes', 'slug')->ignore($id)],
            'description' => ['nullable', 'string', 'max:2000'],
            'level' => ['nullable', 'string', 'max:20'],
            'difficulty' => ['required', Rule::in(['easy', 'medium', 'hard'])],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1', 'max:480'],
            'pass_score' => ['required', 'integer', 'min:0', 'max:100'],
            'xp_reward' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'max_attempts' => ['nullable', 'integer', 'min:1', 'max:100'],
            'shuffle_questions' => ['boolean'],
            'shuffle_options' => ['boolean'],
            'show_explanation' => ['boolean'],
            'is_published' => ['boolean'],

            'questions' => ['array'],
            'questions.*.id' => ['nullable', 'integer'],
            'questions.*.question' => ['required_with:questions', 'string', 'max:2000'],
            'questions.*.type' => ['required_with:questions', Rule::in(['multiple_choice', 'true_false', 'fill_blank', 'matching'])],
            'questions.*.explanation' => ['nullable', 'string', 'max:2000'],
            'questions.*.correct_text' => ['nullable', 'string', 'max:255'],
            'questions.*.score' => ['nullable', 'integer', 'min:1', 'max:100'],
            'questions.*.options' => ['array'],
            'questions.*.options.*.label' => ['required_with:questions.*.options', 'string', 'max:500'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach ((array) $this->input('questions', []) as $index => $question) {
                $type = $question['type'] ?? 'multiple_choice';

                if ($type === 'fill_blank') {
                    if (blank($question['correct_text'] ?? null)) {
                        $validator->errors()->add("questions.{$index}.correct_text", __('A fill-in-the-blank question needs an answer key.'));
                    }

                    continue;
                }

                $options = collect($question['options'] ?? [])->filter(fn ($o) => filled($o['label'] ?? null));

                if ($options->count() < 2) {
                    $validator->errors()->add("questions.{$index}.options", __('Provide at least two options.'));
                }

                if ($options->filter(fn ($o) => filter_var($o['is_correct'] ?? false, FILTER_VALIDATE_BOOLEAN))->isEmpty()) {
                    $validator->errors()->add("questions.{$index}.options", __('Mark one option as the correct answer.'));
                }
            }

            if ($this->boolean('is_published') && empty($this->input('questions'))
                && ! $this->route('quiz')?->questions()->exists()) {
                $validator->errors()->add('is_published', __('A quiz needs at least one question before it can be published.'));
            }
        });
    }
}
