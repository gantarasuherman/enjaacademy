<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\GrammarPattern;
use Illuminate\Foundation\Http\FormRequest;

class GrammarPatternRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pattern = $this->route('grammar_pattern');

        return $pattern instanceof GrammarPattern
            ? $this->user()->can('update', $pattern)
            : $this->user()->can('create', GrammarPattern::class);
    }

    public function rules(): array
    {
        return [
            'grammar_category_id' => ['required', 'integer', 'exists:grammar_categories,id'],
            'title' => ['required', 'string', 'max:200'],
            'title_romaji' => ['nullable', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200'],
            'explanation' => ['required', 'string'],
            'formula' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['boolean'],

            'items' => ['array'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.type' => ['required_with:items.*.sentence', 'in:example,mistake'],
            'items.*.sentence' => ['nullable', 'string'],
            'items.*.romaji' => ['nullable', 'string'],
            'items.*.translation' => ['nullable', 'string'],
            'items.*.correction' => ['nullable', 'string'],
            'items.*.correction_romaji' => ['nullable', 'string'],
            'items.*.note' => ['nullable', 'string'],
        ];
    }
}
