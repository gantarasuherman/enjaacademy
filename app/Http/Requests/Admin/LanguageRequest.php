<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LanguageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $language = $this->route('language');

        return $language instanceof Language
            ? $this->user()->can('update', $language)
            : $this->user()->can('create', Language::class);
    }

    public function rules(): array
    {
        $id = $this->route('language')?->id;

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:60', Rule::unique('languages', 'slug')->ignore($id)],
            'code' => ['required', 'string', 'max:10'],
            'flag' => ['nullable', 'string', 'max:10'],
            'icon' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
