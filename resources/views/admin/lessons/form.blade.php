@extends('layouts.app')
@section('title', $lesson->exists ? __('Edit materi') : __('Materi baru'))

@section('content')
    <x-page-header
        :title="$lesson->exists ? __('Edit materi') : __('Materi baru')"
        :back="route('admin.lessons.index')"
        :back-label="__('Kembali ke materi')"
    />

    {{-- CSV import lives outside the main form so it can post separately --}}
    @if ($lesson->exists)
        @can('lessons.create')
            <div class="card mb-6 p-5">
                <h2 class="mb-1 font-semibold">{{ __('Impor item dari CSV') }}</h2>
                <p class="mb-4 text-xs text-slate-500">
                    {{ __('Kolom wajib: term. Opsional: reading, romaji, meaning, example, example_meaning.') }}
                </p>

                <form method="POST" action="{{ route('admin.lessons.import', $lesson) }}"
                      enctype="multipart/form-data" class="flex flex-wrap items-end gap-3">
                    @csrf
                    <div class="min-w-56 flex-1">
                        <input type="file" name="file" accept=".csv,text/csv" required class="input py-1.5">
                        @error('file') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="btn-secondary">{{ __('Impor') }}</button>
                    <a href="{{ route('admin.lessons.template') }}" class="btn-ghost text-sm">{{ __('Unduh template') }}</a>
                </form>
            </div>
        @endcan
    @endif

    <form method="POST"
          action="{{ $lesson->exists ? route('admin.lessons.update', $lesson) : route('admin.lessons.store') }}"
          enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-3">
        @csrf
        @if ($lesson->exists) @method('PUT') @endif

        <div class="space-y-6 lg:col-span-2">
            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Informasi materi') }}</h2>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="title" class="label">{{ __('Judul') }} <span class="text-rose-500">*</span></label>
                        <input id="title" name="title" value="{{ old('title', $lesson->title) }}" required
                               class="input @error('title') border-rose-400 @enderror">
                        @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="learning_module_id" class="label">{{ __('Modul') }} <span class="text-rose-500">*</span></label>
                        <select id="learning_module_id" name="learning_module_id" required class="input">
                            @foreach ($modules as $module)
                                <option value="{{ $module->id }}" @selected((int) old('learning_module_id', $lesson->learning_module_id) === $module->id)>
                                    {{ $module->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('learning_module_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="level" class="label">{{ __('Level') }}</label>
                        <input id="level" name="level" value="{{ old('level', $lesson->level) }}"
                               class="input font-mono" placeholder="N5 / A2 / B1">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="summary" class="label">{{ __('Ringkasan') }}</label>
                        <textarea id="summary" name="summary" rows="2" class="input">{{ old('summary', $lesson->summary) }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="content" class="label">{{ __('Isi materi') }}</label>
                        <textarea id="content" name="content" rows="8" class="input font-mono text-sm">{{ old('content', $lesson->content) }}</textarea>
                        <p class="help">{{ __('Boleh HTML sederhana: <p>, <strong>, <ul>, <table>.') }}</p>
                    </div>

                    <div>
                        <label for="cover_image" class="label">{{ __('Gambar sampul') }}</label>
                        <input id="cover_image" name="cover_image" type="file" accept="image/*" class="input py-1.5">
                    </div>

                    <div>
                        <label for="audio" class="label">{{ __('Audio') }}</label>
                        <input id="audio" name="audio" type="file" accept="audio/*" class="input py-1.5">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="video_url" class="label">{{ __('URL video') }}</label>
                        <input id="video_url" name="video_url" type="url" value="{{ old('video_url', $lesson->video_url) }}"
                               class="input" placeholder="https://youtube.com/watch?v=…">
                    </div>
                </div>
            </div>

            {{-- Item builder --}}
            <div class="card p-5" x-data="lessonItemBuilder({ initial: @js($items->map->only(['id', 'term', 'reading', 'romaji', 'meaning', 'example', 'example_meaning'])->values()) })">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-semibold">{{ __('Item materi') }}</h2>
                        <p class="text-xs text-slate-500">
                            {{ __('Satu baris = satu kana, kanji, kosakata, atau poin grammar.') }}
                            <span x-text="`${filled} terisi`"></span>
                        </p>
                    </div>
                    <button type="button" @click="add()" class="btn-secondary text-sm">
                        <x-icon name="plus" class="size-4" /> {{ __('Tambah baris') }}
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="rounded-lg border border-slate-200 p-3 dark:border-slate-700">
                            <div class="mb-2 flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-400" x-text="`#${index + 1}`"></span>

                                <div class="flex gap-1">
                                    <button type="button" @click="move(index, -1)" class="btn-ghost px-1.5 py-0.5 text-xs" title="{{ __('Naik') }}">↑</button>
                                    <button type="button" @click="move(index, 1)" class="btn-ghost px-1.5 py-0.5 text-xs" title="{{ __('Turun') }}">↓</button>
                                    <button type="button" @click="duplicate(index)" class="btn-ghost px-1.5 py-0.5 text-xs">{{ __('Salin') }}</button>
                                    <button type="button" @click="remove(index)" class="btn-ghost px-1.5 py-0.5 text-xs text-rose-600">{{ __('Hapus') }}</button>
                                </div>
                            </div>

                            <input type="hidden" :name="`items[${index}][id]`" :value="item.id">

                            <div class="grid gap-2 sm:grid-cols-3">
                                <input data-item-term :name="`items[${index}][term]`" x-model="item.term"
                                       placeholder="{{ __('Term (wajib)') }}" class="input text-sm">
                                <input :name="`items[${index}][reading]`" x-model="item.reading"
                                       placeholder="{{ __('Bacaan / furigana') }}" class="input text-sm">
                                <input :name="`items[${index}][romaji]`" x-model="item.romaji"
                                       placeholder="{{ __('Romaji / fonetik') }}" class="input text-sm">
                                <input :name="`items[${index}][meaning]`" x-model="item.meaning"
                                       placeholder="{{ __('Arti') }}" class="input text-sm sm:col-span-3">
                                <input :name="`items[${index}][example]`" x-model="item.example"
                                       placeholder="{{ __('Contoh kalimat') }}" class="input text-sm sm:col-span-2">
                                <input :name="`items[${index}][example_meaning]`" x-model="item.example_meaning"
                                       placeholder="{{ __('Arti contoh') }}" class="input text-sm">
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Pengaturan') }}</h2>

                <div class="space-y-4">
                    <div>
                        <label for="estimated_minutes" class="label">{{ __('Estimasi menit') }}</label>
                        <input id="estimated_minutes" name="estimated_minutes" type="number" min="1"
                               value="{{ old('estimated_minutes', $lesson->estimated_minutes ?? 10) }}" class="input">
                    </div>

                    <div>
                        <label for="xp_reward" class="label">{{ __('XP reward') }}</label>
                        <input id="xp_reward" name="xp_reward" type="number" min="0"
                               value="{{ old('xp_reward', $lesson->xp_reward ?? 20) }}" class="input">
                    </div>

                    <div>
                        <label for="sort_order" class="label">{{ __('Urutan') }}</label>
                        <input id="sort_order" name="sort_order" type="number" min="0"
                               value="{{ old('sort_order', $lesson->sort_order ?? 0) }}" class="input">
                    </div>

                    <label class="flex items-center gap-2.5 text-sm">
                        <input type="hidden" name="is_published" value="0">
                        <input type="checkbox" name="is_published" value="1"
                               @checked(old('is_published', $lesson->is_published ?? false))
                               class="rounded border-slate-300 text-brand-600">
                        {{ __('Terbitkan materi') }}
                    </label>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1">
                    {{ $lesson->exists ? __('Simpan') : __('Buat materi') }}
                </button>
                <a href="{{ route('admin.lessons.index') }}" class="btn-secondary">{{ __('Batal') }}</a>
            </div>
        </div>
    </form>
@endsection
