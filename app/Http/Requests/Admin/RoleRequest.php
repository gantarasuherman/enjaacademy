<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $role = $this->route('role');

        return $role instanceof Role
            ? $this->user()->can('update', $role)
            : $this->user()->can('create', Role::class);
    }

    public function rules(): array
    {
        $roleId = $this->route('role')?->id;

        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('roles', 'name')->ignore($roleId)->where('guard_name', $this->guard()),
            ],
            'guard_name' => ['nullable', 'string', 'max:50'],
            'permissions' => ['array'],
            'permissions.*' => ['integer', Rule::exists('permissions', 'id')],
            'menus' => ['array'],
            'menus.*' => ['integer', Rule::exists('menus', 'id')],
        ];
    }

    private function guard(): string
    {
        return (string) $this->input('guard_name', config('auth.defaults.guard', 'web'));
    }
}
