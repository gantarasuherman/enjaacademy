<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Permission\Models\Role;

class PermissionMatrixRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('managePermissions', Role::class);
    }

    public function rules(): array
    {
        return [
            'matrix' => ['array'],
            'matrix.*' => ['array'],
            'matrix.*.*' => ['integer'],
        ];
    }

    /** @return array<int, array<int, int>> roleId => permissionIds */
    public function matrix(): array
    {
        return (array) $this->input('matrix', []);
    }
}
