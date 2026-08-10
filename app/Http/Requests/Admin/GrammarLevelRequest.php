<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\GrammarLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GrammarLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        $level = $this->route('grammar_level');

        return $level instanceof GrammarLevel
            ? $this->user()->can('update', $level)
            : $this->user()->can('create', GrammarLevel::class);
    }

    public function rules(): array
    {
        $level = $this->route('grammar_level');
        $language = $this->input('language', $level?->language ?? 'japanese');
        $track = $this->input('track', $level?->track ?? 'grammar');

        return [
            'name' => ['required', 'string', 'max:100'],
            'language' => ['required', 'string', Rule::in(['japanese', 'english'])],
            'track' => ['required', 'string', Rule::in(['grammar', 'structure'])],
            'slug' => [
                'nullable', 'string', 'max:100',
                Rule::unique('grammar_levels', 'slug')
                    ->where(fn ($q) => $q->where('language', $language)->where('track', $track))
                    ->ignore($level?->id),
            ],
            'color' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
