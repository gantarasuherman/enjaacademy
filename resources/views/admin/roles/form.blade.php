@extends('layouts.app')
@section('title', $role->exists ? __('Edit role') : __('Role baru'))

@section('content')
    <x-page-header
        :title="$role->exists ? __('Edit role') : __('Role baru')"
        :description="__('Tentukan permission dan menu yang boleh diakses role ini.')"
        :back="route('admin.roles.index')"
        :back-label="__('Kembali ke role')"
    />

    @if ($isSuper)
        <x-alert type="warning" class="mb-5">
            {{ __('Role :name dilindungi: namanya tidak bisa diubah dan permission-nya selalu penuh lewat Gate::before.', ['name' => $role->name]) }}
        </x-alert>
    @endif

    <form method="POST" action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}"
          class="space-y-6">
        @csrf
        @if ($role->exists) @method('PUT') @endif

        <div class="card p-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="name" class="label">{{ __('Nama role') }} <span class="text-rose-500">*</span></label>
                    <input id="name" name="name" value="{{ old('name', $role->name) }}" required
                           @disabled($isSuper) class="input @error('name') border-rose-400 @enderror">
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="guard_name" class="label">{{ __('Guard') }}</label>
                    <input id="guard_name" name="guard_name"
                           value="{{ old('guard_name', $role->guard_name ?? config('auth.defaults.guard')) }}"
                           class="input" readonly>
                    <p class="help">{{ __('Biarkan "web" kecuali kamu menambahkan guard lain.') }}</p>
                </div>
            </div>
        </div>

        {{-- Permissions --}}
        <div class="card p-5" x-data="{ open: {} }">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="font-semibold">{{ __('Permission') }}</h2>
                    <p class="text-xs text-slate-500">{{ __('Format {module}.{action} — dikelompokkan per modul.') }}</p>
                </div>
                @unless ($isSuper)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox"
                               @click="$el.closest('form').querySelectorAll('input[name=\'permissions[]\']').forEach(el => el.checked = $el.checked)"
                               class="rounded border-slate-300 text-brand-600">
                        {{ __('Pilih semua') }}
                    </label>
                @endunless
            </div>

            @if ($isSuper)
                <p class="rounded-lg bg-emerald-50 p-4 text-sm text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                    {{ __('Role ini otomatis memiliki seluruh permission — sekarang dan yang ditambahkan di masa depan.') }}
                </p>
            @else
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($grouped as $module => $permissions)
                        <div class="rounded-lg border border-slate-200 p-3 dark:border-slate-700">
                            <div class="mb-2 flex items-center justify-between">
                                <p class="text-sm font-semibold">{{ $module }}</p>
                                <label class="flex items-center gap-1.5 text-xs text-slate-500">
                                    <input type="checkbox"
                                           @click="$el.closest('div.rounded-lg').querySelectorAll('input[type=checkbox][name]').forEach(el => el.checked = $el.checked)"
                                           class="rounded border-slate-300 text-brand-600">
                                    {{ __('semua') }}
                                </label>
                            </div>

                            <div class="grid grid-cols-2 gap-1.5">
                                @foreach ($permissions as $permission)
                                    <label class="flex items-center gap-2 text-xs">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                               @checked(in_array($permission->id, old('permissions', $assigned), true))
                                               class="rounded border-slate-300 text-brand-600">
                                        <span class="truncate">{{ Str::after($permission->name, '.') }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Menu access --}}
        <div class="card p-5">
            <h2 class="mb-1 font-semibold">{{ __('Akses menu') }}</h2>
            <p class="mb-4 text-xs text-slate-500">
                {{ __('Centang menu yang khusus untuk role ini. Menu tanpa centang apa pun hanya bergantung pada permission-nya.') }}
            </p>

            <div class="max-h-80 space-y-1 overflow-y-auto rounded-lg border border-slate-200 p-3 dark:border-slate-700">
                @include('admin.roles.partials.menu-checkboxes', ['nodes' => $tree, 'depth' => 0, 'assigned' => $assignedMenus])
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.roles.index') }}" class="btn-secondary">{{ __('Batal') }}</a>
            <button type="submit" class="btn-primary">
                {{ $role->exists ? __('Simpan perubahan') : __('Buat role') }}
            </button>
        </div>
    </form>
@endsection
