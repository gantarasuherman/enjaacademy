@extends('layouts.app')
@section('title', __('Bahasa'))

@section('content')
    <x-page-header :title="__('Bahasa')" :description="__('Bahasa menaungi modul pembelajaran. Menambah bahasa baru cukup dari sini.')">
        <x-slot:actions>
            @can('languages.create')
                <a href="{{ route('admin.languages.create') }}" class="btn-primary">
                    <x-icon name="plus" class="size-4" /> {{ __('Bahasa baru') }}
                </a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold">{{ __('Daftar bahasa') }}</h2>
            <form method="GET" class="flex gap-2">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari…') }}" class="input w-40 text-sm">
                <button class="btn-secondary text-sm">{{ __('Cari') }}</button>
            </form>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Bahasa') }}</th>
                        <th>{{ __('Kode') }}</th>
                        <th class="text-right">{{ __('Modul') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($languages as $language)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <span class="text-lg">{{ $language->flag }}</span>
                                    <div>
                                        <p class="font-medium">{{ $language->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $language->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td><code class="text-xs">{{ $language->code }}</code></td>
                            <td class="text-right font-mono">{{ $language->modules_count }}</td>
                            <td>
                                @if ($language->is_active)
                                    <span class="badge bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15">{{ __('Aktif') }}</span>
                                @else
                                    <span class="badge bg-slate-200 text-slate-600 dark:bg-slate-700">{{ __('Nonaktif') }}</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    @can('languages.update')
                                        <a href="{{ route('admin.languages.edit', $language) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('languages.delete')
                                        <form method="POST" action="{{ route('admin.languages.destroy', $language) }}"
                                              onsubmit="return confirm('{{ __('Hapus bahasa ini?') }}')">
                                            @csrf @method('DELETE')
                                            <button class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Hapus') }}</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-10 text-center text-sm text-slate-500">{{ __('Belum ada bahasa.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($languages->hasPages())
            <div class="border-t border-slate-200 px-5 py-3 dark:border-slate-800">{{ $languages->links() }}</div>
        @endif
    </div>
@endsection
