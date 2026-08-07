@extends('layouts.app')
@section('title', $permission->exists ? __('Edit permission') : __('Permission baru'))

@section('content')
    <x-page-header
        :title="$permission->exists ? __('Edit permission') : __('Permission baru')"
        :description="__('Nama wajib memakai format {module}.{action}, misalnya kanji.view.')"
        :back="route('admin.permissions.index')"
        :back-label="__('Kembali ke permission')"
    />

    <form method="POST"
          action="{{ $permission->exists ? route('admin.permissions.update', $permission) : route('admin.permissions.store') }}"
          class="max-w-2xl space-y-6">
        @csrf
        @if ($permission->exists) @method('PUT') @endif

        <div class="card p-5">
            <div class="space-y-4">
                <div>
                    <label for="name" class="label">{{ __('Nama permission') }} <span class="text-rose-500">*</span></label>
                    <input id="name" name="name" value="{{ old('name', $permission->name) }}" required
                           list="module-names" placeholder="kanji.view"
                           class="input font-mono @error('name') border-rose-400 @enderror">
                    <datalist id="module-names">
                        @foreach ($modules as $module)
                            @foreach ($actions as $action)
                                <option value="{{ $module }}.{{ $action }}"></option>
                            @endforeach
                        @endforeach
                    </datalist>
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    <p class="help">{{ __('Modul yang ada: :list', ['list' => $modules->implode(', ')]) }}</p>
                </div>

                <div>
                    <label for="guard_name" class="label">{{ __('Guard') }}</label>
                    <input id="guard_name" name="guard_name"
                           value="{{ old('guard_name', $permission->guard_name ?? config('auth.defaults.guard')) }}"
                           class="input" readonly>
                </div>
            </div>
        </div>

        <div class="card p-5">
            <h2 class="mb-1 font-semibold">{{ __('Berikan ke role') }}</h2>
            <p class="mb-4 text-xs text-slate-500">
                {{ __('Role :super tidak perlu dicentang — sudah otomatis punya semuanya.', ['super' => config('admin.super_role')]) }}
            </p>

            <div class="grid gap-2 sm:grid-cols-2">
                @foreach ($roles as $role)
                    <label class="flex items-center gap-2 rounded-lg border border-slate-200 p-2.5 text-sm dark:border-slate-700">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                               @checked(in_array($role->id, old('roles', $assigned), true))
                               @disabled($role->name === config('admin.super_role'))
                               class="rounded border-slate-300 text-brand-600">
                        {{ $role->name }}
                        @if ($role->name === config('admin.super_role'))
                            <span class="ml-auto text-xs text-emerald-600">{{ __('otomatis') }}</span>
                        @endif
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.permissions.index') }}" class="btn-secondary">{{ __('Batal') }}</a>
            <button type="submit" class="btn-primary">
                {{ $permission->exists ? __('Simpan') : __('Buat permission') }}
            </button>
        </div>
    </form>
@endsection
