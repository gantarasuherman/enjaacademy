@extends('layouts.app')
@section('title', __('Kosakata (Kuis Harian)'))

@section('content')
    <x-page-header :title="__('Kosakata (Kuis Harian)')" :description="__('Bank kosakata yang jadi sumber soal Kuis Harian (Inggris, wajib) dan materi kosakata Jepang. Target Inggris: ±1.000 Beginner, 2.000 Elementary, 3.000 Intermediate, 5.000 Upper-Intermediate, 10.000+ Advanced.')">
        <x-slot:actions>
            @can('vocabulary-words.create')
                <a href="{{ route('admin.vocabulary-words.template') }}" class="btn-secondary">
                    <x-icon name="download" class="size-4" /> {{ __('Unduh Template') }}
                </a>
                <button type="button" onclick="document.getElementById('import-panel').classList.toggle('hidden')" class="btn-secondary">
                    <x-icon name="upload" class="size-4" /> {{ __('Impor Excel') }}
                </button>
                <a href="{{ route('admin.vocabulary-words.create') }}" class="btn-primary">
                    <x-icon name="plus" class="size-4" /> {{ __('Kata baru') }}
                </a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    @can('vocabulary-words.create')
        <div id="import-panel" class="card mb-6 p-5 {{ $errors->has('file') ? '' : 'hidden' }}">
            <h2 class="mb-1 font-semibold">{{ __('Impor kosakata dari Excel/CSV') }}</h2>
            <p class="help mb-3">{{ __('Unduh template di atas dulu untuk lihat kolom yang dipakai (bahasa, kata, arti, level, dst). File .xlsx dari Excel bisa langsung disimpan sebagai .csv sebelum diunggah.') }}</p>
            <form method="POST" action="{{ route('admin.vocabulary-words.import') }}" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3">
                @csrf
                <div class="flex-1">
                    <label for="file" class="label">{{ __('File CSV') }}</label>
                    <input id="file" name="file" type="file" accept=".csv,.txt" required class="input py-1.5 text-sm">
                    @error('file') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <button class="btn-primary">{{ __('Impor') }}</button>
            </form>
        </div>
    @endcan

    <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
        @foreach ($languages as $language)
            <div class="card p-4 text-center">
                <p class="text-xs text-slate-500">{{ $language->name }}</p>
                <p class="mt-1 font-display text-xl font-bold">{{ number_format($totalsByLanguage[$language->name] ?? 0) }}</p>
            </div>
        @endforeach
    </div>

    <div
        class="card"
        @can('vocabulary-words.delete')
        x-data="{
            selected: [],
            allIds: @js($words->pluck('id')),
            get allSelected() { return this.allIds.length > 0 && this.selected.length === this.allIds.length; },
            toggleAll() { this.selected = this.allSelected ? [] : [...this.allIds]; },
        }"
        @endcan
    >
        <div class="card-header flex-wrap">
            <h2 class="font-semibold">{{ __('Daftar kosakata') }}</h2>

            <form method="GET" class="flex flex-wrap gap-2">
                <select name="language" class="input w-40 text-sm">
                    <option value="">{{ __('Semua bahasa') }}</option>
                    @foreach ($languages as $language)
                        <option value="{{ $language->id }}" @selected(request('language') == $language->id)>{{ $language->name }}</option>
                    @endforeach
                </select>
                <select name="level" class="input w-48 text-sm">
                    <option value="">{{ __('Semua level') }}</option>
                    @foreach ($levelsByLanguage as $langSlug => $levelGroup)
                        <optgroup label="{{ ucfirst($langSlug) }}">
                            @foreach ($levelGroup as $level)
                                <option value="{{ $level }}" @selected(request('level') === $level)>{{ $level }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari kata atau arti…') }}" class="input w-48 text-sm">
                <button class="btn-secondary text-sm">{{ __('Filter') }}</button>
            </form>
        </div>

        @can('vocabulary-words.delete')
            <div x-show="selected.length > 0" x-cloak
                 class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-rose-50 px-5 py-3 text-sm dark:border-slate-800 dark:bg-rose-500/10">
                <span><span x-text="selected.length"></span> {{ __('kata dipilih') }}</span>
                <form method="POST" action="{{ route('admin.vocabulary-words.bulk-destroy') }}"
                      @submit="if (! confirm(`{{ __('Hapus') }} ${selected.length} {{ __('kata terpilih? Tindakan ini tidak bisa dibatalkan.') }}`)) $event.preventDefault()">
                    @csrf @method('DELETE')
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button class="btn-ghost px-3 py-1.5 text-xs text-rose-600">{{ __('Hapus Terpilih') }}</button>
                </form>
            </div>
        @endcan

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        @can('vocabulary-words.delete')
                            <th class="w-8">
                                <input type="checkbox" :checked="allSelected" @change="toggleAll()"
                                       aria-label="{{ __('Pilih semua') }}" class="rounded border-slate-300 text-brand-600">
                            </th>
                        @endcan
                        <th>{{ __('Kata') }}</th>
                        <th>{{ __('Arti') }}</th>
                        <th>{{ __('Level') }}</th>
                        <th>{{ __('Bahasa') }}</th>
                        <th class="text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($words as $word)
                        <tr>
                            @can('vocabulary-words.delete')
                                <td>
                                    <input type="checkbox" value="{{ $word->id }}" x-model="selected"
                                           aria-label="{{ __('Pilih') }} {{ $word->word }}" class="rounded border-slate-300 text-brand-600">
                                </td>
                            @endcan
                            <td class="font-medium">
                                {{ $word->word }}
                                @if ($word->phonetic)
                                    <span class="block font-mono text-xs text-slate-400">{{ $word->phonetic }}</span>
                                @endif
                            </td>
                            <td class="text-slate-500">{{ $word->meaning_id }}</td>
                            <td><span class="badge bg-indigo-100 text-indigo-700 dark:bg-indigo-500/15">{{ $word->level }}</span></td>
                            <td class="text-slate-500">{{ $word->language?->name }}</td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    @can('vocabulary-words.update')
                                        <a href="{{ route('admin.vocabulary-words.edit', $word) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('vocabulary-words.delete')
                                        <form method="POST" action="{{ route('admin.vocabulary-words.destroy', $word) }}"
                                              onsubmit="return confirm('{{ __('Hapus kata ini?') }}')">
                                            @csrf @method('DELETE')
                                            <button class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Hapus') }}</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ auth()->user()->can('vocabulary-words.delete') ? 6 : 5 }}" class="py-10 text-center text-sm text-slate-500">{{ __('Belum ada kata yang cocok.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($words->hasPages())
            <div class="border-t border-slate-200 px-5 py-3 dark:border-slate-800">{{ $words->links() }}</div>
        @endif
    </div>
@endsection
