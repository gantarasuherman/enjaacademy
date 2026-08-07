<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Achievement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AchievementRequest extends FormRequest
{
    public function authorize(): bool
    {
        $achievement = $this->route('achievement');

        return $achievement instanceof Achievement
            ? $this->user()->can('update', $achievement)
            : $this->user()->can('create', Achievement::class);
    }

    public function rules(): array
    {
        $id = $this->route('achievement')?->id;

        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:150', Rule::unique('achievements', 'slug')->ignore($id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:100'],
            'badge_color' => ['nullable', 'string', 'max:30'],
            'criteria_type' => ['required', Rule::in([
                'xp_total', 'level', 'lessons_completed', 'quizzes_completed',
                'perfect_quizzes', 'streak_days', 'flashcards_reviewed', 'manual',
            ])],
            'criteria_value' => ['required', 'integer', 'min:0'],
            'xp_reward' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
