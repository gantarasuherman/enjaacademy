<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Menu;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class MenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        $menu = $this->route('menu');

        return $menu instanceof Menu
            ? $this->user()->can('update', $menu)
            : $this->user()->can('create', Menu::class);
    }

    public function rules(): array
    {
        $menuId = $this->route('menu')?->id;

        return [
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('menus', 'slug')->ignore($menuId)],
            'parent_id' => ['nullable', 'integer', Rule::exists('menus', 'id')],
            'icon' => ['nullable', 'string', 'max:100'],
            'route_name' => ['nullable', 'string', 'max:150'],
            'route_params' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:2048'],
            'type' => ['required', Rule::in(array_keys(config('admin.menu.types')))],
            'position' => ['required', Rule::in(array_keys(config('admin.menu.positions')))],
            'badge' => ['nullable', 'string', 'max:40'],
            'badge_color' => ['nullable', Rule::in(config('admin.menu.badge_colors'))],
            'permission_name' => ['nullable', 'string', 'max:150', Rule::exists('permissions', 'name')],
            'role_default' => ['nullable', 'string', 'max:150', Rule::exists('roles', 'name')],
            'target' => ['required', Rule::in(array_keys(config('admin.menu.targets')))],
            'is_visible' => ['boolean'],
            'is_active' => ['boolean'],
            'is_sidebar' => ['boolean'],
            'is_topbar' => ['boolean'],
            'is_footer' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'description' => ['nullable', 'string', 'max:255'],
            'roles' => ['array'],
            'roles.*' => ['integer', Rule::exists('roles', 'id')],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $type = $this->input('type');

            // A clickable entry needs somewhere to go; headers and dividers
            // deliberately have neither.
            if (in_array($type, ['menu', 'external'], true)
                && blank($this->input('route_name'))
                && blank($this->input('url'))
                && blank($this->input('parent_id')) === false) {
                return; // a child grouping node is allowed to be a pure branch
            }

            if ($type === 'external' && blank($this->input('url'))) {
                $validator->errors()->add('url', __('An external menu needs a URL.'));
            }

            if ($this->filled('route_name') && ! \Illuminate\Support\Facades\Route::has((string) $this->input('route_name'))) {
                $validator->errors()->add('route_name', __('Route ":route" is not registered.', [
                    'route' => $this->input('route_name'),
                ]));
            }

            $menu = $this->route('menu');

            if ($menu instanceof Menu && (int) $this->input('parent_id') === $menu->id) {
                $validator->errors()->add('parent_id', __('A menu cannot be its own parent.'));
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_visible' => $this->boolean('is_visible'),
            'is_active' => $this->boolean('is_active'),
            'is_sidebar' => $this->boolean('is_sidebar'),
            'is_topbar' => $this->boolean('is_topbar'),
            'is_footer' => $this->boolean('is_footer'),
        ]);
    }
}
