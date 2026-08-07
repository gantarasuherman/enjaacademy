<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Menu;
use Illuminate\Foundation\Http\FormRequest;

class MenuReorderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('reorder', Menu::class);
    }

    public function rules(): array
    {
        return [
            'tree' => ['required', 'array'],
            'tree.*.id' => ['required', 'integer', 'exists:menus,id'],
            'tree.*.children' => ['sometimes', 'array'],
        ];
    }

    /** @return array<int, array{id:int, children?:array}> */
    public function tree(): array
    {
        return (array) $this->input('tree', []);
    }
}
