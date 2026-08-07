@extends('layouts.app')
@section('title', __('Roles'))

@section('content')
    <x-page-header :title="__('Roles')" :description="__('Kelompok hak akses. Satu pengguna bisa memegang lebih dari satu role.')">
        <x-slot:actions>
            @can('roles.view')
                <a href="{{ route('admin.roles.matrix') }}" class="btn-secondary">{{ __('Matriks permission') }}</a>
            @endcan
            @can('roles.create')
                <a href="{{ route('admin.roles.create') }}" class="btn-primary">
                    <x-icon name="plus" class="size-4" /> {{ __('Role baru') }}
                </a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold">{{ __('Daftar role') }}</h2>
            <form method="GET" class="flex gap-2">
                <input type="search" name="search" value="{{ request('search') }}"
                       placeholder="{{ __('Cari role…') }}" class="input w-48 text-sm">
                <button class="btn-secondary text-sm">{{ __('Cari') }}</button>
            </form>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Role') }}</th>
                        <th class="text-right">{{ __('Permission') }}</th>
                        <th class="text-right">{{ __('Pengguna') }}</th>
                        <th class="text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        @php $protected = $role->name === $superRole; @endphp

                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium">{{ $role->name }}</span>
                                    @if ($protected)
                                        <span class="badge bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">
                                            {{ __('dilindungi') }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500">guard: {{ $role->guard_name }}</p>
                            </td>
                            <td class="text-right font-mono text-sm">
                                {{ $protected ? __('semua') : $role->permissions_count }}
                            </td>
                            <td class="text-right font-mono text-sm">{{ $role->users_count }}</td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    @can('roles.update')
                                        <a href="{{ route('admin.roles.permissions', $role) }}" class="btn-ghost px-2 py-1 text-xs">
                                            {{ __('Permission') }}
                                        </a>
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn-ghost px-2 py-1 text-xs">
                                            {{ __('Edit') }}
                                        </a>
                                    @endcan

                                    @can('roles.delete')
                                        @unless ($protected)
                                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                                                  onsubmit="return confirm('{{ __('Hapus role ini?') }}')">
                                                @csrf @method('DELETE')
                                                <button class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Hapus') }}</button>
                                            </form>
                                        @endunless
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-10 text-center text-sm text-slate-500">{{ __('Belum ada role.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($roles->hasPages())
            <div class="border-t border-slate-200 px-5 py-3 dark:border-slate-800">{{ $roles->links() }}</div>
        @endif
    </div>
@endsection
