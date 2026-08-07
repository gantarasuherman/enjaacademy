@extends('layouts.app')
@section('title', $user->exists ? __('Edit pengguna') : __('Pengguna baru'))

@section('content')
    <x-page-header
        :title="$user->exists ? __('Edit pengguna') : __('Pengguna baru')"
        :back="route('admin.users.index')"
        :back-label="__('Kembali ke pengguna')"
    />

    <form method="POST"
          action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}"
          enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-3">
        @csrf
        @if ($user->exists) @method('PUT') @endif

        <div class="space-y-6 lg:col-span-2">
            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Identitas') }}</h2>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="label">{{ __('Nama lengkap') }} <span class="text-rose-500">*</span></label>
                        <input id="name" name="name" value="{{ old('name', $user->name) }}" required
                               class="input @error('name') border-rose-400 @enderror">
                        @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="username" class="label">{{ __('Username') }}</label>
                        <input id="username" name="username" value="{{ old('username', $user->username) }}"
                               class="input @error('username') border-rose-400 @enderror">
                        @error('username') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="label">{{ __('Email') }} <span class="text-rose-500">*</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                               class="input @error('email') border-rose-400 @enderror">
                        @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="label">{{ __('Telepon') }}</label>
                        <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="input">
                    </div>

                    <div>
                        <label for="birth_date" class="label">{{ __('Tanggal lahir') }}</label>
                        <input id="birth_date" name="birth_date" type="date"
                               value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}" class="input">
                    </div>

                    <div>
                        <label for="gender" class="label">{{ __('Jenis kelamin') }}</label>
                        <select id="gender" name="gender" class="input">
                            <option value="">{{ __('— tidak diisi —') }}</option>
                            @foreach (['male' => __('Laki-laki'), 'female' => __('Perempuan'), 'other' => __('Lainnya')] as $value => $label)
                                <option value="{{ $value }}" @selected(old('gender', $user->gender) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="bio" class="label">{{ __('Bio') }}</label>
                        <textarea id="bio" name="bio" rows="3" class="input">{{ old('bio', $user->bio) }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="avatar" class="label">{{ __('Foto profil') }}</label>
                        <input id="avatar" name="avatar" type="file" accept="image/*" class="input py-1.5">
                        @error('avatar') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <h2 class="mb-1 font-semibold">{{ __('Kata sandi') }}</h2>
                <p class="mb-4 text-xs text-slate-500">
                    {{ $user->exists ? __('Kosongkan jika tidak ingin mengubah.') : __('Wajib diisi untuk akun baru.') }}
                </p>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="label">{{ __('Kata sandi') }}</label>
                        <input id="password" name="password" type="password" autocomplete="new-password"
                               class="input @error('password') border-rose-400 @enderror">
                        @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="label">{{ __('Ulangi kata sandi') }}</label>
                        <input id="password_confirmation" name="password_confirmation" type="password"
                               autocomplete="new-password" class="input">
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Role') }}</h2>

                <div class="space-y-2">
                    @foreach ($roles as $role)
                        <label class="flex items-center gap-2.5 rounded-lg border border-slate-200 p-2.5 text-sm dark:border-slate-700">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                   @checked(in_array($role->name, old('roles', $assigned), true))
                                   class="rounded border-slate-300 text-brand-600">
                            {{ $role->name }}
                        </label>
                    @endforeach
                </div>
                @error('roles') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Status & preferensi') }}</h2>

                <div class="space-y-3">
                    <label class="flex items-center gap-2.5 text-sm">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                               @checked(old('is_active', $user->is_active ?? true))
                               class="rounded border-slate-300 text-brand-600">
                        {{ __('Akun aktif') }}
                    </label>

                    <label class="flex items-center gap-2.5 text-sm">
                        <input type="hidden" name="mark_verified" value="0">
                        <input type="checkbox" name="mark_verified" value="1"
                               @checked(old('mark_verified', (bool) $user->email_verified_at))
                               class="rounded border-slate-300 text-brand-600">
                        {{ __('Tandai email terverifikasi') }}
                    </label>

                    <div>
                        <label for="locale" class="label">{{ __('Bahasa') }}</label>
                        <select id="locale" name="locale" class="input">
                            @foreach (['id' => 'Bahasa Indonesia', 'en' => 'English'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('locale', $user->locale ?? 'id') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="timezone" class="label">{{ __('Zona waktu') }}</label>
                        <input id="timezone" name="timezone"
                               value="{{ old('timezone', $user->timezone ?? config('app.timezone')) }}" class="input">
                    </div>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1">
                    {{ $user->exists ? __('Simpan') : __('Buat pengguna') }}
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">{{ __('Batal') }}</a>
            </div>
        </div>
    </form>
@endsection
