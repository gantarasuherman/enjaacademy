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
        $id = $this->route('grammar_level')?->id;

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('grammar_levels', 'slug')->ignore($id)],
            'color' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
