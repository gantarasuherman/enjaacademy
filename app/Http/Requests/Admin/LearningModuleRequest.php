<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\LearningModule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LearningModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $module = $this->route('module');

        return $module instanceof LearningModule
            ? $this->user()->can('update', $module)
            : $this->user()->can('create', LearningModule::class);
    }

    public function rules(): array
    {
        $id = $this->route('module')?->id;

        return [
            'language_id' => ['required', 'integer', Rule::exists('languages', 'id')],
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:150', Rule::unique('learning_modules', 'slug')->ignore($id)],
            'icon' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:30'],
            'content_type' => ['required', Rule::in([
                'kana', 'kanji', 'vocabulary', 'grammar', 'conversation',
                'listening', 'speaking', 'reading', 'writing', 'exam', 'video',
            ])],
            'permission_prefix' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9_\-]+$/'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_paid' => ['boolean'],
            'price' => ['nullable', 'required_if:is_paid,1', 'integer', 'min:0'],
            'generate_permissions' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'permission_prefix.regex' => __('Use lowercase letters, numbers, dashes or underscores only — this becomes "{prefix}.view" and friends.'),
        ];
    }
}
