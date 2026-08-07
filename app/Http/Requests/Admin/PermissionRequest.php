<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class PermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $permission = $this->route('permission');

        return $permission instanceof Permission
            ? $this->user()->can('update', $permission)
            : $this->user()->can('create', Permission::class);
    }

    public function rules(): array
    {
        $permissionId = $this->route('permission')?->id;

        return [
            // `{module}.{action}` is a hard contract: the menu builder, the
            // policies and the matrix grouping all rely on the dot format.
            'name' => [
                'required', 'string', 'max:150', 'regex:/^[a-z0-9_\-]+\.[a-z0-9_\-]+$/',
                Rule::unique('permissions', 'name')->ignore($permissionId),
            ],
            'guard_name' => ['nullable', 'string', 'max:50'],
            'roles' => ['array'],
            'roles.*' => ['integer', Rule::exists('roles', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => __('Permission names must follow the {module}.{action} format, e.g. "kanji.view".'),
        ];
    }
}
