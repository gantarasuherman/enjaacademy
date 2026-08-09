<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\GrammarCategory;
use Illuminate\Foundation\Http\FormRequest;

class GrammarCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $category = $this->route('grammar_category');

        return $category instanceof GrammarCategory
            ? $this->user()->can('update', $category)
            : $this->user()->can('create', GrammarCategory::class);
    }

    public function rules(): array
    {
        return [
            'grammar_level_id' => ['required', 'integer', 'exists:grammar_levels,id'],
            'parent_id' => ['nullable', 'integer', 'exists:grammar_categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
