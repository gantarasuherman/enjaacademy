@extends('layouts.app')
@section('title', __('Materi'))

@section('content')
    <x-page-header :title="__('Materi')" :description="__('Konten belajar per modul, lengkap dengan item (kana, kanji, kosakata, poin grammar).')">
        <x-slot:actions>
            @can('lessons.create')
                <a href="{{ route('admin.lessons.template') }}" class="btn-secondary">{{ __('Template CSV') }}</a>
                <a href="{{ route('admin.lessons.create') }}" class="btn-primary">
                    <x-icon name="plus" class="size-4" /> {{ __('Materi baru') }}
                </a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div class="card-header flex-wrap">
            <h2 class="font-semibold">{{ __('Daftar materi') }}</h2>

            <form method="GET" class="flex flex-wrap gap-2">
                <select name="module" class="input w-44 text-sm">
                    <option value="">{{ __('Semua modul') }}</option>
                    @foreach ($modules as $module)
                        <option value="{{ $module->id }}" @selected(request('module') == $module->id)>{{ $module->name }}</option>
                    @endforeach
                </select>

                <select name="is_published" class="input w-32 text-sm">
                    <option value="">{{ __('Semua status') }}</option>
                    <option value="1" @selected(request('is_published') === '1')>{{ __('Terbit') }}</option>
                    <option value="0" @selected(request('is_published') === '0')>{{ __('Draf') }}</option>
                </select>

                <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari…') }}" class="input w-40 text-sm">
                <button class="btn-secondary text-sm">{{ __('Filter') }}</button>
            </form>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Judul') }}</th>
                        <th>{{ __('Modul') }}</th>
                        <th>{{ __('Level') }}</th>
                        <th class="text-right">{{ __('Item') }}</th>
                        <th class="text-right">{{ __('XP') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lessons as $lesson)
                        <tr>
                            <td>
                                <p class="font-medium">{{ $lesson->title }}</p>
                                <p class="truncate text-xs text-slate-500">{{ Str::limit($lesson->summary, 60) }}</p>
                            </td>
                            <td class="text-slate-500">{{ $lesson->module?->name }}</td>
                            <td>
                                @if ($lesson->level)
                                    <span class="badge bg-slate-100 font-mono text-slate-600 dark:bg-slate-800">{{ $lesson->level }}</span>
                                @endif
                            </td>
                            <td class="text-right font-mono">{{ $lesson->items_count }}</td>
                            <td class="text-right font-mono">{{ $lesson->xp_reward }}</td>
                            <td>
                                @if ($lesson->is_published)
                                    <span class="badge bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15">{{ __('Terbit') }}</span>
                                @else
                                    <span class="badge bg-slate-200 text-slate-600 dark:bg-slate-700">{{ __('Draf') }}</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    @can('lessons.view')
                                        <a href="{{ route('admin.lessons.export', $lesson) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Export') }}</a>
                                    @endcan
                                    @can('lessons.update')
                                        <a href="{{ route('admin.lessons.edit', $lesson) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('lessons.delete')
                                        <form method="POST" action="{{ route('admin.lessons.destroy', $lesson) }}"
                                              onsubmit="return confirm('{{ __('Hapus materi ini?') }}')">
                                            @csrf @method('DELETE')
                                            <button class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Hapus') }}</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-10 text-center text-sm text-slate-500">{{ __('Tidak ada materi yang cocok.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($lessons->hasPages())
            <div class="border-t border-slate-200 px-5 py-3 dark:border-slate-800">{{ $lessons->links() }}</div>
        @endif
    </div>
@endsection
