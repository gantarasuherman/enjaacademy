@extends('layouts.app')
@section('title', __('Materi Video'))

@section('content')
    <x-page-header :title="__('Materi Video')" :description="__('Materi berbentuk video (YouTube/Vimeo) dengan bab bertimestamp — terpisah dari daftar Materi biasa supaya formnya tidak bercampur dengan tipe konten lain.')">
        <x-slot:actions>
            @can('lessons.create')
                <a href="{{ route('admin.video-lessons.create') }}" class="btn-primary">
                    <x-icon name="plus" class="size-4" /> {{ __('Materi video baru') }}
                </a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    @if ($modules->isEmpty())
        <div class="card mb-6 p-5 text-sm">
            {{ __('Belum ada Modul dengan Tipe konten "video". Buat dulu di') }}
            <a href="{{ route('admin.modules.create') }}" class="font-semibold text-primary hover:underline">{{ __('Modul Pembelajaran') }}</a>.
        </div>
    @endif

    <div class="card">
        <div class="card-header flex-wrap">
            <h2 class="font-semibold">{{ __('Daftar materi video') }}</h2>

            <form method="GET" class="flex flex-wrap gap-2">
                <select name="module" class="input w-44 text-sm">
                    <option value="">{{ __('Semua modul video') }}</option>
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
                        <th class="text-right">{{ __('Bab') }}</th>
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
                                <p class="truncate text-xs text-slate-500">{{ Str::limit($lesson->video_url, 60) }}</p>
                            </td>
                            <td class="text-slate-500">{{ $lesson->module?->name }}</td>
                            <td class="text-right">{{ $lesson->items_count }}</td>
                            <td class="text-right">{{ $lesson->xp_reward }}</td>
                            <td>
                                @if ($lesson->is_published)
                                    <span class="badge bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15">{{ __('Terbit') }}</span>
                                @else
                                    <span class="badge bg-slate-200 text-slate-600 dark:bg-slate-700">{{ __('Draf') }}</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    @can('lessons.update')
                                        <a href="{{ route('admin.video-lessons.edit', $lesson) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('lessons.delete')
                                        <form method="POST" action="{{ route('admin.video-lessons.destroy', $lesson) }}"
                                              onsubmit="return confirm('{{ __('Hapus materi video ini?') }}')">
                                            @csrf @method('DELETE')
                                            <button class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Hapus') }}</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-10 text-center text-sm text-slate-500">{{ __('Belum ada materi video.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($lessons->hasPages())
            <div class="border-t border-slate-200 px-5 py-3 dark:border-slate-800">{{ $lessons->links() }}</div>
        @endif
    </div>
@endsection
