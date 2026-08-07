<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class UserData extends BaseData
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $username,
        public readonly ?string $password,
        public readonly ?string $phone,
        public readonly ?string $bio,
        public readonly ?string $birthDate,
        public readonly ?string $gender,
        public readonly string $locale,
        public readonly string $timezone,
        public readonly bool $isActive,
        public readonly bool $markVerified,
        public readonly ?UploadedFile $avatar,
        /** @var array<int, string> */
        public readonly array $roles = [],
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        return new static(
            name: (string) $request->string('name'),
            email: (string) $request->string('email'),
            username: $request->input('username'),
            password: $request->filled('password') ? (string) $request->string('password') : null,
            phone: $request->input('phone'),
            bio: $request->input('bio'),
            birthDate: $request->input('birth_date'),
            gender: $request->input('gender'),
            locale: (string) $request->input('locale', config('app.locale')),
            timezone: (string) $request->input('timezone', config('app.timezone')),
            isActive: $request->boolean('is_active'),
            markVerified: $request->boolean('mark_verified'),
            avatar: $request->file('avatar'),
            roles: array_values(array_filter((array) $request->input('roles', []))),
        );
    }

    public function toArray(): array
    {
        return $this->withoutNulls([
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'password' => $this->password,
            'phone' => $this->phone,
            'bio' => $this->bio,
            'birth_date' => $this->birthDate,
            'gender' => $this->gender,
            'locale' => $this->locale,
            'timezone' => $this->timezone,
        ]) + ['is_active' => $this->isActive];
    }
}
