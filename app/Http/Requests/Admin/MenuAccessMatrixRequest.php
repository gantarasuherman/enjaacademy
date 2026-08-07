<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Menu;
use Illuminate\Foundation\Http\FormRequest;

class MenuAccessMatrixRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', Menu::class)
            || $this->user()->can('menus.update');
    }

    public function rules(): array
    {
        return [
            'matrix' => ['array'],
            'matrix.*' => ['array'],
            'matrix.*.*' => ['integer', 'exists:menus,id'],
        ];
    }

    /** @return array<int, array<int, int>> roleId => menuIds */
    public function matrix(): array
    {
        return (array) $this->input('matrix', []);
    }
}
