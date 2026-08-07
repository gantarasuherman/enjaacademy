@extends('layouts.app')
@section('title', __('Permissions'))

@section('content')
    <x-page-header
        :title="__('Permissions')"
        :description="__('Semua hak akses memakai format {module}.{action}. Modul baru cukup dibuatkan permission-nya di sini — tanpa deploy.')"
    >
        <x-slot:actions>
            @can('permissions.create')
                <a href="{{ route('admin.permissions.create') }}" class="btn-primary">
                    <x-icon name="plus" class="size-4" /> {{ __('Permission baru') }}
                </a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    {{-- Bulk generator: the single action that makes a new module usable --}}
    @can('permissions.create')
        <div class="card mb-6 p-5">
            <h2 class="mb-1 font-semibold">{{ __('Buat set permission untuk modul baru') }}</h2>
            <p class="mb-4 text-xs text-slate-500">
                {{ __('Sekali klik menghasilkan {module}.view, .create, .update, .delete, .export, .import. Permission yang sudah ada dilewati.') }}
            </p>

            <form method="POST" action="{{ route('admin.permissions.generate') }}" class="flex flex-wrap items-end gap-3">
                @csrf

                <div class="min-w-48 flex-1">
                    <label for="module" class="label">{{ __('Nama modul') }}</label>
                    <input id="module" name="module" required pattern="[a-z0-9_\-]+"
                           placeholder="kanji" class="input font-mono text-sm">
                    <p class="help">{{ __('Huruf kecil, angka, strip, atau garis bawah.') }}</p>
                </div>

                <div class="flex flex-wrap gap-3 pb-2">
                    @foreach (config('admin.permission_actions') as $action)
                        <label class="flex items-center gap-1.5 text-sm">
                            <input type="checkbox" name="actions[]" value="{{ $action }}"
                                   @checked(in_array($action, ['view', 'create', 'update', 'delete'], true))
                                   class="rounded border-slate-300 text-brand-600">
                            {{ $action }}
                        </label>
                    @endforeach
                </div>

                <button type="submit" class="btn-primary">{{ __('Generate') }}</button>
            </form>
        </div>
    @endcan

    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold">{{ __('Daftar permission') }}</h2>

            <form method="GET" class="flex flex-wrap gap-2">
                <select name="module" class="input w-40 text-sm" onchange="this.form.submit()">
                    <option value="">{{ __('Semua modul') }}</option>
                    @foreach ($modules as $module)
                        <option value="{{ $module }}" @selected(request('module') === $module)>{{ $module }}</option>
                    @endforeach
                </select>
                <input type="search" name="search" value="{{ request('search') }}"
                       placeholder="{{ __('Cari…') }}" class="input w-40 text-sm">
                <button class="btn-secondary text-sm">{{ __('Cari') }}</button>
            </form>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Nama') }}</th>
                        <th>{{ __('Modul') }}</th>
                        <th>{{ __('Aksi') }}</th>
                        <th class="text-right">{{ __('Role') }}</th>
                        <th class="text-right">{{ __('Kelola') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($permissions as $permission)
                        <tr>
                            <td><code class="text-xs">{{ $permission->name }}</code></td>
                            <td class="text-slate-500">{{ Str::before($permission->name, '.') }}</td>
                            <td>
                                <span class="badge bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                    {{ Str::after($permission->name, '.') }}
                                </span>
                            </td>
                            <td class="text-right font-mono text-sm">{{ $permission->roles_count }}</td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    @can('permissions.update')
                                        <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('permissions.delete')
                                        <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}"
                                              onsubmit="return confirm('{{ __('Hapus permission ini?') }}')">
                                            @csrf @method('DELETE')
                                            <button class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Hapus') }}</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-10 text-center text-sm text-slate-500">{{ __('Tidak ada permission yang cocok.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($permissions->hasPages())
            <div class="border-t border-slate-200 px-5 py-3 dark:border-slate-800">{{ $permissions->links() }}</div>
        @endif
    </div>
@endsection
