@extends('layouts.app')
@section('title', __('Users'))

@section('content')
    <x-page-header :title="__('Users')" :description="__('Kelola akun, status aktif, dan role pengguna.')">
        <x-slot:actions>
            @can('users.view')
                <a href="{{ route('admin.users.export', request()->query()) }}" class="btn-secondary">{{ __('Export CSV') }}</a>
            @endcan
            @can('users.create')
                <a href="{{ route('admin.users.create') }}" class="btn-primary">
                    <x-icon name="plus" class="size-4" /> {{ __('Pengguna baru') }}
                </a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div class="card-header flex-wrap">
            <h2 class="font-semibold">{{ __('Daftar pengguna') }}</h2>

            <form method="GET" class="flex flex-wrap gap-2">
                <input type="search" name="search" value="{{ request('search') }}"
                       placeholder="{{ __('Nama, email, username…') }}" class="input w-48 text-sm">

                <select name="role" class="input w-36 text-sm">
                    <option value="">{{ __('Semua role') }}</option>
                    @foreach ($roles as $value => $label)
                        <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="is_active" class="input w-32 text-sm">
                    <option value="">{{ __('Semua status') }}</option>
                    <option value="1" @selected(request('is_active') === '1')>{{ __('Aktif') }}</option>
                    <option value="0" @selected(request('is_active') === '0')>{{ __('Nonaktif') }}</option>
                </select>

                <button class="btn-secondary text-sm">{{ __('Filter') }}</button>
            </form>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Pengguna') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Login terakhir') }}</th>
                        <th class="text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatar_url }}" alt="" class="size-9 shrink-0 rounded-full object-cover">
                                    <div class="min-w-0">
                                        <p class="truncate font-medium">{{ $user->name }}</p>
                                        <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($user->roles as $role)
                                        <span class="badge bg-brand-100 text-brand-700 dark:bg-brand-500/15 dark:text-brand-300">{{ $role->name }}</span>
                                    @empty
                                        <span class="text-xs text-slate-400">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                @if ($user->is_active)
                                    <span class="badge bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">{{ __('Aktif') }}</span>
                                @else
                                    <span class="badge bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300">{{ __('Nonaktif') }}</span>
                                @endif

                                @unless ($user->email_verified_at)
                                    <span class="badge bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">{{ __('Belum verifikasi') }}</span>
                                @endunless
                            </td>
                            <td class="text-xs text-slate-500">
                                {{ $user->last_login_at?->diffForHumans() ?? __('belum pernah') }}
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    @can('users.view')
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Detail') }}</a>
                                    @endcan

                                    @can('users.update')
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Edit') }}</a>

                                        @unless ($user->is(auth()->user()))
                                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                                @csrf
                                                <button class="btn-ghost px-2 py-1 text-xs">
                                                    {{ $user->is_active ? __('Nonaktifkan') : __('Aktifkan') }}
                                                </button>
                                            </form>
                                        @endunless
                                    @endcan

                                    @can('users.delete')
                                        @unless ($user->is(auth()->user()))
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                  onsubmit="return confirm('{{ __('Hapus pengguna ini?') }}')">
                                                @csrf @method('DELETE')
                                                <button class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Hapus') }}</button>
                                            </form>
                                        @endunless
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-10 text-center text-sm text-slate-500">{{ __('Tidak ada pengguna yang cocok.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-slate-200 px-5 py-3 dark:border-slate-800">{{ $users->links() }}</div>
        @endif
    </div>
@endsection
