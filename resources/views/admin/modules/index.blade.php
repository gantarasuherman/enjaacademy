@extends('layouts.app')
@section('title', __('Modul Pembelajaran'))

@section('content')
    <x-page-header
        :title="__('Modul Pembelajaran')"
        :description="__('Hiragana, Kanji, TOEFL, dan lainnya adalah baris di tabel ini — bukan kelas PHP. Modul baru bisa ditambah tanpa deploy.')"
    >
        <x-slot:actions>
            @can('modules.create')
                <a href="{{ route('admin.modules.create') }}" class="btn-primary">
                    <x-icon name="plus" class="size-4" /> {{ __('Modul baru') }}
                </a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div class="card-header flex-wrap">
            <h2 class="font-semibold">{{ __('Daftar modul') }}</h2>

            <form method="GET" class="flex flex-wrap gap-2">
                <select name="language" class="input w-40 text-sm">
                    <option value="">{{ __('Semua bahasa') }}</option>
                    @foreach ($languages as $language)
                        <option value="{{ $language->id }}" @selected(request('language') == $language->id)>{{ $language->name }}</option>
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
                        <th>{{ __('Modul') }}</th>
                        <th>{{ __('Bahasa') }}</th>
                        <th>{{ __('Tipe konten') }}</th>
                        <th>{{ __('Harga') }}</th>
                        <th>{{ __('Prefix permission') }}</th>
                        <th class="text-right">{{ __('Materi') }}</th>
                        <th class="text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($modules as $module)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <x-icon :name="$module->icon" class="size-4 shrink-0 text-slate-400" />
                                    <div class="min-w-0">
                                        <p class="truncate font-medium">{{ $module->name }}</p>
                                        <p class="truncate text-xs text-slate-500">{{ $module->slug }}</p>
                                    </div>
                                    @unless ($module->is_active)
                                        <span class="badge bg-slate-200 text-slate-600 dark:bg-slate-700">{{ __('nonaktif') }}</span>
                                    @endunless
                                </div>
                            </td>
                            <td class="text-slate-500">{{ $module->language?->name }}</td>
                            <td><span class="badge bg-slate-100 text-slate-600 dark:bg-slate-800">{{ $module->content_type }}</span></td>
                            <td>
                                @if ($module->is_paid)
                                    <span class="badge bg-amber-100 text-amber-700 dark:bg-amber-500/15">Rp {{ number_format((int) $module->price, 0, ',', '.') }}</span>
                                @else
                                    <span class="badge bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15">{{ __('Gratis') }}</span>
                                @endif
                            </td>
                            <td><code class="text-xs text-amber-600 dark:text-amber-400">{{ $module->permission_prefix }}.*</code></td>
                            <td class="text-right font-mono">{{ $module->lessons_count }}</td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    @can('modules.view')
                                        <a href="{{ route('admin.modules.show', $module) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Detail') }}</a>
                                    @endcan
                                    @can('modules.update')
                                        <a href="{{ route('admin.modules.edit', $module) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('modules.delete')
                                        <form method="POST" action="{{ route('admin.modules.destroy', $module) }}"
                                              onsubmit="return confirm('{{ __('Hapus modul ini beserta materinya?') }}')">
                                            @csrf @method('DELETE')
                                            <button class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Hapus') }}</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-10 text-center text-sm text-slate-500">{{ __('Belum ada modul.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($modules->hasPages())
            <div class="border-t border-slate-200 px-5 py-3 dark:border-slate-800">{{ $modules->links() }}</div>
        @endif
    </div>
@endsection
