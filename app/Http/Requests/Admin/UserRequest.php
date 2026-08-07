<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->route('user');

        return $user instanceof User
            ? $this->user()->can('update', $user)
            : $this->user()->can('create', User::class);
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;
        $isCreate = $userId === null;

        return [
            'name' => ['required', 'string', 'max:150'],
            'username' => ['nullable', 'string', 'max:60', 'alpha_dash', Rule::unique('users', 'username')->ignore($userId)],
            'email' => ['required', 'email', 'max:180', Rule::unique('users', 'email')->ignore($userId)],
            'password' => [$isCreate ? 'required' : 'nullable', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'locale' => ['nullable', 'string', 'max:5'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
            'mark_verified' => ['boolean'],
            'roles' => ['array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
        ];
    }

    public function attributes(): array
    {
        return [
            'is_active' => __('active status'),
            'mark_verified' => __('email verified'),
        ];
    }
}
