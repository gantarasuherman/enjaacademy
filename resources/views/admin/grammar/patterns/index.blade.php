@extends('layouts.app')
@section('title', __('Pola Grammar'))

@section('content')
    <x-page-header :title="__('Pola Grammar')" :description="__('Setiap pola tergolong satu kategori, dan bisa punya beberapa contoh kalimat serta kesalahan umum.')">
        <x-slot:actions>
            @can('grammar.create')
                <a href="{{ route('admin.grammar.patterns.create') }}" class="btn-primary">
                    <x-icon name="plus" class="size-4" /> {{ __('Pola baru') }}
                </a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div class="card-header flex-wrap">
            <h2 class="font-semibold">{{ __('Daftar pola') }}</h2>

            <form method="GET" class="flex flex-wrap gap-2">
                <select name="category" class="input w-56 text-sm">
                    <option value="">{{ __('Semua kategori') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                            {{ $category->level?->name }} — {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari…') }}" class="input w-40 text-sm">
                <button class="btn-secondary text-sm">{{ __('Filter') }}</button>
            </form>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Pola') }}</th>
                        <th>{{ __('Level') }}</th>
                        <th>{{ __('Kategori') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($patterns as $pattern)
                        <tr>
                            <td class="font-medium">{{ $pattern->title }}</td>
                            <td><span class="badge bg-indigo-100 text-indigo-700 dark:bg-indigo-500/15">{{ $pattern->category?->level?->name }}</span></td>
                            <td class="text-slate-500">{{ $pattern->category?->name }}</td>
                            <td>
                                @if ($pattern->is_published)
                                    <span class="badge bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15">{{ __('Terbit') }}</span>
                                @else
                                    <span class="badge bg-slate-200 text-slate-600 dark:bg-slate-700">{{ __('Draf') }}</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    @can('grammar.update')
                                        <a href="{{ route('admin.grammar.patterns.edit', $pattern) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('grammar.delete')
                                        <form method="POST" action="{{ route('admin.grammar.patterns.destroy', $pattern) }}"
                                              onsubmit="return confirm('{{ __('Hapus pola ini?') }}')">
                                            @csrf @method('DELETE')
                                            <button class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Hapus') }}</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-10 text-center text-sm text-slate-500">{{ __('Belum ada pola yang cocok.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($patterns->hasPages())
            <div class="border-t border-slate-200 px-5 py-3 dark:border-slate-800">{{ $patterns->links() }}</div>
        @endif
    </div>
@endsection
