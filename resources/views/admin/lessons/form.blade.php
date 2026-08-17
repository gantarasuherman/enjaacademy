@extends('layouts.app')
@section('title', $lesson->exists ? __('Edit materi') : __('Materi baru'))

@php
    $lessonData = [
        'id' => $lesson->id,
        'title' => $lesson->title,
        'slug' => $lesson->slug,
        'learning_module_id' => $lesson->learning_module_id,
        'level' => $lesson->level,
        'summary' => $lesson->summary,
        'content' => $lesson->content,
        'translated_content' => $lesson->translated_content,
        'video_url' => $lesson->video_url,
        'estimated_minutes' => $lesson->estimated_minutes,
        'xp_reward' => $lesson->xp_reward,
        'sort_order' => $lesson->sort_order,
        'is_published' => $lesson->is_published,
    ];
@endphp

@section('content')
    <div
        x-data="lessonBuilder({
            lesson: @js($lessonData),
            items: @js($items->map->only(['id', 'term', 'reading', 'romaji', 'meaning', 'example', 'example_meaning', 'extra'])->values()),
            moduleContentType: @js($lesson->module?->content_type),
            moduleContentTypes: @js($modules->pluck('content_type', 'id')),
            moduleLanguages: @js($modules->pluck('language.name', 'id')),
            moduleLanguageSlugs: @js($modules->pluck('language.slug', 'id')),
            lessonExists: @js($lesson->exists),
            draftKey: @js($lesson->exists ? $lesson->slug : 'new'),
            justSaved: @js((bool) session('success')),
        })"
        x-init="init()"
    >
        <x-page-header
            :title="$lesson->exists ? __('Edit materi') : __('Materi baru')"
            :back="route('admin.lessons.index')"
            :back-label="__('Kembali ke materi')"
        >
            <x-slot:actions>
                <span class="badge" :class="isPublished ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15' : 'bg-slate-200 text-slate-600 dark:bg-slate-700'"
                      x-text="isPublished ? '{{ __('Diterbitkan') }}' : '{{ __('Draft') }}'"></span>

                <span class="self-center text-xs text-slate-400" x-show="lastSavedAt" x-cloak>
                    <x-icon name="check" class="inline size-3.5" />
                    {{ __('Tersimpan otomatis') }} <span x-text="lastSavedAt"></span>
                </span>

                <button type="button" @click="activeDrawer = 'preview'" class="btn-secondary text-sm">
                    <x-icon name="eye" class="size-4" /> {{ __('Pratinjau') }}
                </button>
            </x-slot:actions>
        </x-page-header>

        {{-- Autosave draft found banner --}}
        <div x-show="restorableDraft" x-cloak class="card mb-6 flex flex-wrap items-center justify-between gap-3 border-amber-300 bg-amber-50 p-4 text-sm dark:border-amber-500/30 dark:bg-amber-500/10">
            <span>{{ __('Draf tersimpan otomatis ditemukan di perangkat ini.') }}</span>
            <div class="flex gap-2">
                <button type="button" @click="restoreDraft()" class="btn-secondary text-xs">{{ __('Pulihkan') }}</button>
                <button type="button" @click="dismissDraft()" class="btn-ghost text-xs">{{ __('Abaikan') }}</button>
            </div>
        </div>

        <form id="lesson-form" x-ref="form" method="POST"
              action="{{ $lesson->exists ? route('admin.lessons.update', $lesson) : route('admin.lessons.store') }}"
              enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-3">
            @csrf
            @if ($lesson->exists) @method('PUT') @endif
            <input type="hidden" name="is_published" :value="isPublished ? 1 : 0">

            <div class="space-y-6 lg:col-span-2">
                {{-- ---------------------------------------------- Informasi Dasar --}}
                <div class="card p-5">
                    <h2 class="mb-4 font-semibold">{{ __('Informasi Dasar') }}</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="title" class="label">{{ __('Judul Materi') }} <span class="text-rose-500">*</span></label>
                            <input id="title" name="title" x-model="title" required
                                   class="input text-base @error('title') border-rose-400 @enderror" placeholder="{{ __('Mengenal Hiragana') }}">
                            @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="label">{{ __('Modul') }} <span class="text-rose-500">*</span></label>
                                <select name="learning_module_id" x-model="moduleId" required class="input">
                                    @foreach ($modules as $module)
                                        <option value="{{ $module->id }}">{{ $module->name }}</option>
                                    @endforeach
                                </select>
                                @error('learning_module_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="level" class="label">{{ __('Level') }}</label>
                                <input id="level" name="level" x-model="level" class="input font-mono" placeholder="N5 / A2 / B1">
                            </div>
                        </div>

                        <div>
                            <label for="summary" class="label">{{ __('Ringkasan') }}</label>
                            <textarea id="summary" name="summary" x-model="summary" rows="2" class="input"
                                      placeholder="{{ __('Apa itu hiragana...') }}"></textarea>
                        </div>
                    </div>
                </div>

                {{-- ---------------------------------------------------- Video (materi tipe Video) --}}
                <div class="card p-5" x-show="moduleContentType === 'video'" x-cloak>
                    <h2 class="mb-1 font-semibold">
                        <x-icon name="play" class="inline size-4" /> {{ __('URL Video') }}
                    </h2>
                    <p class="help mb-3">{{ __('Link YouTube atau Vimeo. Tambahkan Item Pembelajaran bertipe "Bab Video" di bawah untuk membuat chapter dengan timestamp yang bisa diklik peserta.') }}</p>
                    {{-- No `name` here — shares `x-model="videoUrl"` with the field in "Opsi Tambahan" below, which is the one that actually submits. --}}
                    <input x-model="videoUrl" type="url" class="input" placeholder="https://youtube.com/watch?v=…">
                    @error('video_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- ---------------------------------------------------- Isi Materi --}}
                <div class="card p-5">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <h2 class="font-semibold">{{ __('Isi Materi') }}</h2>
                        <div class="flex items-center gap-3">
                            <p class="text-xs text-slate-400">
                                <span x-text="contentWordCount"></span> {{ __('kata') }} ·
                                <span x-text="contentParagraphCount"></span> {{ __('paragraf') }}
                            </p>
                            <button type="button" @click="activeDrawer = 'ai-content'" class="btn-ghost text-xs">
                                <x-icon name="sparkles" class="size-3.5" /> {{ __('Buat dengan AI') }}
                            </button>
                        </div>
                    </div>

                    <textarea id="content" name="content" x-ref="contentEditor" x-model="content" rows="12" class="input font-mono text-sm"></textarea>
                    <p class="help" x-show="moduleContentType === 'reading'">{{ __('Untuk bacaan (Reading), pisahkan tiap paragraf dengan satu baris kosong — dipakai fitur hover-terjemahan. Editor teks kaya tidak dipakai di sini agar format ini tidak rusak.') }}</p>
                    @error('content') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- ---------------------------------------------- Item Pembelajaran --}}
                <div class="card p-5">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h2 class="font-semibold">{{ __('Item Pembelajaran') }}</h2>
                            <p class="text-xs text-slate-500">
                                {{ __('Tambahkan kosakata, kana, kanji, grammar, dll.') }}
                                <span x-show="items.length" x-text="`— ${items.length} item`"></span>
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="openAddItem()" class="btn-secondary text-sm">
                                <x-icon name="plus" class="size-4" /> {{ __('Tambah Item') }}
                            </button>
                            <button type="button" @click="openAiItemsDrawer()" class="btn-secondary text-sm">
                                <x-icon name="sparkles" class="size-4" /> {{ __('Buat dengan AI') }}
                            </button>
                            <button type="button" @click="openImport()" class="btn-secondary text-sm">
                                <x-icon name="upload" class="size-4" /> {{ __('Impor Massal') }}
                            </button>
                            @if ($lesson->exists)
                                <a href="{{ route('admin.lessons.export', $lesson) }}" class="btn-ghost text-sm">{{ __('Ekspor CSV') }}</a>
                            @endif
                        </div>
                    </div>

                    <p x-show="totalProblems" x-cloak class="mb-3 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
                        <template x-for="(item, index) in items" :key="item._uid">
                            <template x-for="issue in problems(item, index)" :key="issue">
                                <span class="block">⚠ <span x-text="issue"></span></span>
                            </template>
                        </template>
                    </p>

                    <div x-show="items.length === 0" x-cloak class="rounded-lg border border-dashed border-slate-300 p-8 text-center text-sm text-slate-400 dark:border-slate-700">
                        {{ __('Belum ada item. Tambahkan satu per satu, impor massal, atau buat dengan AI.') }}
                    </div>

                    <div x-ref="itemList" class="space-y-2">
                        <template x-for="(item, index) in items" :key="item._uid">
                            <div x-show="index >= itemsPageStart && index < itemsPageEnd"
                                 class="lesson-item-card flex items-start gap-2 rounded-lg border border-slate-200 p-3 dark:border-slate-700">
                                <span data-item-handle class="mt-1 cursor-grab text-slate-300 hover:text-slate-500 active:cursor-grabbing dark:text-slate-600">
                                    <x-icon name="grip" class="size-4" />
                                </span>

                                <button type="button" @click="openEditItem(index)" class="min-w-0 flex-1 text-left">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="badge bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300" x-text="templateFor(item.type)"></span>
                                        <span class="font-display font-semibold" x-text="item.term || '(kosong)'"></span>
                                        <span class="ipa text-xs text-slate-400" x-show="item.reading" x-text="item.reading"></span>
                                        <span class="text-xs text-slate-400" x-show="item.romaji" x-text="item.romaji"></span>
                                    </div>
                                    <p class="mt-0.5 truncate text-sm text-slate-500" x-show="item.meaning" x-text="item.meaning"></p>
                                    <p class="mt-0.5 truncate text-xs text-slate-400" x-show="item.example" x-text="`${item.example}${item.example_meaning ? ' — ' + item.example_meaning : ''}`"></p>
                                </button>

                                <div x-data="{ open: false }" class="relative shrink-0">
                                    <button type="button" @click="open = !open" class="btn-ghost px-2 py-1 text-xs" :aria-expanded="open">⋮</button>
                                    <div x-show="open" x-cloak @click.outside="open = false" x-transition
                                         class="absolute right-0 z-10 mt-1 w-36 overflow-hidden rounded-lg border border-slate-200 bg-white py-1 shadow-lg dark:border-slate-800 dark:bg-slate-900">
                                        <button type="button" @click="openEditItem(index); open = false" class="block w-full px-3 py-1.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-800">{{ __('Edit') }}</button>
                                        <button type="button" @click="duplicateItem(index); open = false" class="block w-full px-3 py-1.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-800">{{ __('Duplikat') }}</button>
                                        <button type="button" @click="removeItem(index); open = false" class="block w-full px-3 py-1.5 text-left text-sm text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10">{{ __('Hapus') }}</button>
                                    </div>
                                </div>

                                {{-- Hidden inputs — the actual submitted payload, matching LessonRequest/syncItems exactly --}}
                                <input type="hidden" :name="`items[${index}][id]`" :value="item.id">
                                <input type="hidden" :name="`items[${index}][term]`" :value="item.term">
                                <input type="hidden" :name="`items[${index}][reading]`" :value="item.reading">
                                <input type="hidden" :name="`items[${index}][romaji]`" :value="item.romaji">
                                <input type="hidden" :name="`items[${index}][meaning]`" :value="item.meaning">
                                <input type="hidden" :name="`items[${index}][example]`" :value="item.example">
                                <input type="hidden" :name="`items[${index}][example_meaning]`" :value="item.example_meaning">
                                <template x-for="entry in Object.entries(item.extra || {})" :key="entry[0]">
                                    <input type="hidden" :name="`items[${index}][extra][${entry[0]}]`" :value="entry[1]">
                                </template>
                            </div>
                        </template>
                    </div>

                    <div x-show="itemsTotalPages > 1" x-cloak class="mt-3 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                        <p>
                            <span x-text="items.length ? itemsPageStart + 1 : 0"></span>–<span x-text="itemsPageEnd"></span>
                            {{ __('dari') }} <span x-text="items.length"></span> {{ __('item') }}
                        </p>
                        <div class="flex items-center gap-1">
                            <button type="button" class="btn-ghost px-2 py-1 text-xs disabled:cursor-not-allowed disabled:opacity-40"
                                    :disabled="itemsPage === 1" @click="goToItemsPage(itemsPage - 1)">
                                ‹ {{ __('Sebelumnya') }}
                            </button>
                            <span class="px-2 font-medium text-slate-600 dark:text-slate-300" x-text="`${itemsPage} / ${itemsTotalPages}`"></span>
                            <button type="button" class="btn-ghost px-2 py-1 text-xs disabled:cursor-not-allowed disabled:opacity-40"
                                    :disabled="itemsPage === itemsTotalPages" @click="goToItemsPage(itemsPage + 1)">
                                {{ __('Berikutnya') }} ›
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ---------------------------------------------------- Opsi Tambahan --}}
                <div class="card p-5" x-data="{ open: advancedOpen }">
                    <button type="button" @click="open = !open" class="flex w-full items-center justify-between text-left font-semibold">
                        {{ __('Opsi Tambahan') }}
                        <span class="inline-block transition" :class="open ? 'rotate-180' : ''">
                            <x-icon name="chevron-down" class="size-4" />
                        </span>
                    </button>

                    <div x-show="open" x-collapse class="mt-4 space-y-4">
                        <div>
                            <label for="slug" class="label">{{ __('Slug') }}</label>
                            <input id="slug" name="slug" x-model="slug" @input="markSlugTouched()" class="input font-mono text-sm">
                            <p class="help">{{ __('Dipakai di URL pembelajar — otomatis dari judul kecuali diubah manual di sini.') }}</p>
                            @error('slug') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="label">{{ __('Terjemahan Bahasa Indonesia') }}</label>
                            <div class="mb-2 flex items-center justify-between">
                                <p class="help mt-0">{{ __('Dipakai fitur hover-terjemahan di halaman Reading.') }}</p>
                                <button type="button" @click="activeDrawer = 'ai-translation'" class="btn-ghost text-xs">
                                    <x-icon name="sparkles" class="size-3.5" /> {{ __('Generate Terjemahan') }}
                                </button>
                            </div>
                            <textarea name="translated_content" x-ref="translatedContentEditor" x-model="translatedContent" rows="6" class="input font-mono text-sm"></textarea>
                        </div>

                        <div>
                            <h3 class="label mb-2">{{ __('Media') }}</h3>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label for="cover_image" class="label text-xs">
                                        <x-icon name="image" class="inline size-3.5" /> {{ __('Gambar sampul') }}
                                    </label>
                                    <input id="cover_image" name="cover_image" type="file" accept="image/*" class="input py-1.5 text-sm">
                                    @if ($lesson->cover_image)
                                        <img src="{{ asset('storage/'.$lesson->cover_image) }}" class="mt-2 h-16 rounded-lg object-cover" alt="">
                                    @endif
                                </div>
                                <div>
                                    <label for="audio" class="label text-xs">
                                        <x-icon name="music" class="inline size-3.5" /> {{ __('Audio') }}
                                    </label>
                                    <input id="audio" name="audio" type="file" accept="audio/*" class="input py-1.5 text-sm">
                                    @if ($lesson->audio_path)
                                        <audio src="{{ asset('storage/'.$lesson->audio_path) }}" controls class="mt-2 h-8 w-full"></audio>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="label text-xs">
                                    <x-icon name="play" class="inline size-3.5" /> {{ __('URL video') }}
                                </label>
                                <input name="video_url" x-model="videoUrl" type="url" class="input" placeholder="https://youtube.com/watch?v=…">
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label class="label">{{ __('Estimasi menit') }}</label>
                                <input name="estimated_minutes" type="number" min="1" x-model.number="estimatedMinutes" class="input">
                            </div>
                            <div>
                                <label class="label">{{ __('XP reward') }}</label>
                                <input name="xp_reward" type="number" min="0" x-model.number="xpReward" class="input">
                            </div>
                            <div>
                                <label class="label">{{ __('Urutan') }}</label>
                                <input name="sort_order" type="number" min="0" x-model.number="sortOrder" class="input">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="card p-5">
                    <div class="flex flex-col gap-2">
                        <button type="button" @click.prevent="submitAs(0)" class="btn-secondary">{{ __('Simpan Draft') }}</button>
                        <button type="button" @click.prevent="submitAs(1)" class="btn-primary">{{ __('Publish') }}</button>
                        <a href="{{ route('admin.lessons.index') }}" class="btn-ghost text-center">{{ __('Batal') }}</a>
                    </div>
                </div>
            </div>
        </form>

        {{-- ============================================================ Drawer: Tambah/Edit Item --}}
        <div x-show="activeDrawer === 'item'" x-cloak class="drawer-backdrop" @click="closeDrawer()"></div>
        <div x-ref="itemDrawer" x-show="activeDrawer === 'item'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" class="drawer-panel" @keydown.escape.window="closeDrawer()">
            <template x-if="draft">
                <div>
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="font-semibold" x-text="editingIndex === null ? '{{ __('Tambah Item') }}' : '{{ __('Edit Item') }}'"></h2>
                        <button type="button" @click="closeDrawer()" class="btn-ghost px-2 py-1">✕</button>
                    </div>

                    <div class="mb-4">
                        <p class="label">{{ __('Jenis Item') }}</p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="option in itemTypes" :key="option.value">
                                <button type="button" @click="pickType(option.value)"
                                        class="type-pill" :class="draft.type === option.value ? 'is-active' : ''"
                                        x-text="option.label"></button>
                            </template>
                        </div>
                        <p class="help" x-show="!itemTypes.some(t => t.value === draft.type)">
                            {{ __('Tipe tersimpan:') }} <span class="font-mono" x-text="draft.type"></span> — {{ __('tidak dikenal editor ini, ditampilkan sebagai Custom.') }}
                        </p>
                    </div>

                    <div class="space-y-3">
                        <template x-for="field in currentDrawerFields" :key="field.key">
                            <div>
                                <label class="label" x-text="field.label"></label>
                                <textarea x-show="field.multiline" rows="2" class="input text-sm"
                                          :value="getField(field.target)" @input="setField(field.target, $event.target.value)"></textarea>
                                <input x-show="!field.multiline" type="text" class="input text-sm"
                                       :placeholder="field.placeholder ?? ''"
                                       :value="getField(field.target)" @input="setField(field.target, $event.target.value)">
                            </div>
                        </template>
                    </div>

                    <div class="mt-6 flex gap-2">
                        <button type="button" @click="closeDrawer()" class="btn-secondary flex-1">{{ __('Batal') }}</button>
                        <button type="button" @click="saveDrawer()" class="btn-primary flex-1">{{ __('Simpan Item') }}</button>
                    </div>
                </div>
            </template>
        </div>

        {{-- ============================================================ Drawer: Impor Massal --}}
        <div x-show="activeDrawer === 'import'" x-cloak class="drawer-backdrop" @click="closeDrawer()"></div>
        <div x-show="activeDrawer === 'import'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" class="drawer-panel">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold">{{ __('Impor Massal') }}</h2>
                <button type="button" @click="closeDrawer()" class="btn-ghost px-2 py-1">✕</button>
            </div>

            <div class="mb-4 flex gap-2 border-b border-slate-200 dark:border-slate-800">
                <button type="button" @click="importTab = 'paste'" class="border-b-2 px-3 py-2 text-sm" :class="importTab === 'paste' ? 'border-brand-500 font-semibold' : 'border-transparent text-slate-500'">{{ __('Tempel Teks') }}</button>
                @if ($lesson->exists)
                    <button type="button" @click="importTab = 'csv'" class="border-b-2 px-3 py-2 text-sm" :class="importTab === 'csv' ? 'border-brand-500 font-semibold' : 'border-transparent text-slate-500'">{{ __('Unggah CSV') }}</button>
                @endif
            </div>

            <div x-show="importTab === 'paste'">
                <label class="label">{{ __('Impor sebagai tipe') }}</label>
                <select x-model="importType" class="input mb-3">
                    <template x-for="option in itemTypes" :key="option.value">
                        <option :value="option.value" x-text="option.label"></option>
                    </template>
                </select>

                <label class="label">{{ __('Tempel Data') }}</label>
                <textarea x-model="importText" rows="8" class="input font-mono text-sm"
                          placeholder="あ | a | huruf a&#10;い | i | huruf i"></textarea>
                <p class="help">{{ __('Satu baris = satu item. Pisahkan kolom dengan | atau tab, urutan kolom mengikuti field tipe yang dipilih.') }}</p>

                <p class="mt-3 text-sm font-medium" x-show="importText.trim()">
                    <span x-text="importRows.length"></span> {{ __('item ditemukan') }} ✓
                </p>

                <button type="button" @click="confirmImport()" :disabled="!importRows.length" class="btn-primary mt-4 w-full disabled:opacity-50">{{ __('Impor') }}</button>
            </div>

            {{--
                A real, separate <form> — deliberately a sibling of #lesson-form (which
                closes well above this point in the DOM), never nested inside it. HTML
                forbids nested <form> elements; nesting this here would silently break
                either this upload or the main Simpan/Publish submit.
            --}}
            @if ($lesson->exists)
                <div x-show="importTab === 'csv'">
                    <p class="help mb-3">{{ __('Kolom wajib: term. Opsional: reading, romaji, meaning, example, example_meaning.') }}</p>
                    <a href="{{ route('admin.lessons.template') }}" class="btn-ghost mb-3 inline-block text-sm">{{ __('Unduh template') }}</a>

                    <form method="POST" action="{{ route('admin.lessons.import', $lesson) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file" accept=".csv,text/csv" required class="input py-1.5">
                        @error('file') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        <button type="submit" class="btn-primary mt-3 w-full">{{ __('Impor CSV') }}</button>
                    </form>
                </div>
            @endif
        </div>

        {{-- ============================================================ Drawer: Buat Item dengan AI --}}
        <div x-show="activeDrawer === 'ai-items'" x-cloak class="drawer-backdrop" @click="activeDrawer = null; aiMessage = null; aiGeneratedItems = []"></div>
        <div x-show="activeDrawer === 'ai-items'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" class="drawer-panel">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold"><x-icon name="sparkles" class="inline size-4" /> {{ __('Buat Materi dengan AI') }}</h2>
                <button type="button" @click="activeDrawer = null; aiMessage = null; aiGeneratedItems = []" class="btn-ghost px-2 py-1">✕</button>
            </div>

            <p class="help mb-3">
                {{ __('Topik, Level, dan Jenis sudah diisi otomatis dari Informasi Dasar (termasuk Bahasa modul) — ubah kalau perlu.') }}
                <span x-show="content.trim()">{{ __('"Isi Materi" yang sudah ditulis juga dipakai sebagai acuan supaya item yang dibuat sesuai isinya, bukan cuma menebak dari topik.') }}</span>
            </p>

            {{-- Form — hidden once results are in review, "Generate Ulang" brings it back --}}
            <div x-show="aiGeneratedItems.length === 0">
                <div class="space-y-3">
                    <div>
                        <label class="label">{{ __('Topik') }}</label>
                        <input x-model="aiItemsForm.topic" class="input" placeholder="{{ __('Mengenal Hiragana') }}">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label">{{ __('Level') }}</label>
                            <input x-model="aiItemsForm.level" class="input" placeholder="N5">
                        </div>
                        <div>
                            <label class="label">{{ __('Jumlah item') }}</label>
                            <input type="number" min="1" max="30" x-model.number="aiItemsForm.count" class="input">
                        </div>
                    </div>
                    <div>
                        <p class="label">{{ __('Jenis') }}</p>
                        <div class="flex flex-wrap gap-3 text-sm">
                            <template x-for="option in itemTypes.filter(t => ['kana', 'kanji', 'kosakata', 'grammar', 'kalimat'].includes(t.value))" :key="option.value">
                                <label class="flex items-center gap-1.5">
                                    <input type="checkbox" :checked="aiItemsForm.types[option.value]" @change="aiItemsForm.types[option.value] = $event.target.checked; aiItemsTypesTouched = true">
                                    <span x-text="option.label"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                    <div>
                        <label class="label">{{ __('Bahasa penjelasan') }}</label>
                        <input x-model="aiItemsForm.language" class="input">
                    </div>
                </div>

                <div x-show="aiMessage" x-cloak class="mt-4 rounded-lg bg-slate-100 px-3 py-2 text-sm text-slate-600 dark:bg-slate-800 dark:text-slate-300" x-text="aiMessage"></div>

                <button type="button" @click="generateItems('{{ route('admin.lessons.ai.generate-items') }}')" :disabled="aiLoading || !aiItemsForm.topic.trim()" class="btn-primary mt-4 w-full disabled:opacity-50">
                    <x-icon name="sparkles" class="size-4" /> <span x-text="aiLoading ? '{{ __('Memproses…') }}' : '{{ __('Generate') }}'"></span>
                </button>
            </div>

            {{-- Review — generated items, editable after acceptance via the normal item cards --}}
            <div x-show="aiGeneratedItems.length > 0">
                <p class="help mb-3">
                    <span x-text="aiGeneratedItems.length"></span> {{ __('item dibuat. Hapus yang tidak diinginkan, lalu terima sisanya — kamu tetap bisa mengedit satu per satu setelah diterima.') }}
                </p>

                <div class="max-h-[50vh] space-y-2 overflow-y-auto">
                    <template x-for="(item, index) in aiGeneratedItems" :key="item._uid">
                        <div class="flex items-start justify-between gap-2 rounded-lg border border-slate-200 p-2.5 text-sm dark:border-slate-700">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="badge bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300" x-text="templateFor(item.type)"></span>
                                    <span class="font-display font-semibold" x-text="item.term"></span>
                                    <span class="text-xs text-slate-400" x-show="item.romaji" x-text="item.romaji"></span>
                                </div>
                                <p class="truncate text-slate-500" x-show="item.meaning" x-text="item.meaning"></p>
                            </div>
                            <button type="button" @click="removeGeneratedItem(index)" class="btn-ghost shrink-0 px-2 py-1 text-xs text-rose-600">{{ __('Hapus') }}</button>
                        </div>
                    </template>
                </div>

                <div class="mt-4 flex gap-2">
                    <button type="button" @click="aiGeneratedItems = []" class="btn-secondary flex-1">{{ __('Generate Ulang') }}</button>
                    <button type="button" @click="acceptGeneratedItems()" class="btn-primary flex-1">
                        <x-icon name="check" class="size-4" /> {{ __('Terima Semua') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- ============================================================ Drawer: Generate Terjemahan --}}
        <div x-show="activeDrawer === 'ai-translation'" x-cloak class="drawer-backdrop" @click="activeDrawer = null; aiMessage = null; aiGeneratedTranslation = null"></div>
        <div x-show="activeDrawer === 'ai-translation'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" class="drawer-panel">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold"><x-icon name="translate" class="inline size-4" /> {{ __('Generate Terjemahan') }}</h2>
                <button type="button" @click="activeDrawer = null; aiMessage = null; aiGeneratedTranslation = null" class="btn-ghost px-2 py-1">✕</button>
            </div>

            <template x-if="aiGeneratedTranslation === null">
                <div>
                    <p class="help">{{ __('Menerjemahkan "Isi Materi" saat ini ke Bahasa Indonesia.') }}</p>

                    <div x-show="aiMessage" x-cloak class="mt-4 rounded-lg bg-slate-100 px-3 py-2 text-sm text-slate-600 dark:bg-slate-800 dark:text-slate-300" x-text="aiMessage"></div>

                    <button type="button" @click="generateTranslation('{{ route('admin.lessons.ai.generate-translation') }}')" :disabled="aiLoading || !content.trim()" class="btn-primary mt-4 w-full disabled:opacity-50">
                        <x-icon name="sparkles" class="size-4" /> <span x-text="aiLoading ? '{{ __('Memproses…') }}' : '{{ __('Generate Terjemahan') }}'"></span>
                    </button>
                    <p class="help" x-show="!content.trim()">{{ __('Isi "Isi Materi" terlebih dahulu.') }}</p>
                </div>
            </template>

            <template x-if="aiGeneratedTranslation !== null">
                <div>
                    <label class="label">{{ __('Hasil terjemahan — bisa diedit sebelum dipakai') }}</label>
                    <textarea x-model="aiGeneratedTranslation" rows="14" class="input font-mono text-sm"></textarea>

                    <div class="mt-4 flex gap-2">
                        <button type="button" @click="aiGeneratedTranslation = null" class="btn-secondary flex-1">{{ __('Generate Ulang') }}</button>
                        <button type="button" @click="useGeneratedTranslation()" class="btn-primary flex-1">
                            <x-icon name="check" class="size-4" /> {{ __('Gunakan Terjemahan Ini') }}
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- ============================================================ Drawer: Buat Isi Materi dengan AI --}}
        <div x-show="activeDrawer === 'ai-content'" x-cloak class="drawer-backdrop" @click="activeDrawer = null; aiMessage = null; aiGeneratedContent = null"></div>
        <div x-show="activeDrawer === 'ai-content'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" class="drawer-panel">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold"><x-icon name="sparkles" class="inline size-4" /> {{ __('Buat Isi Materi dengan AI') }}</h2>
                <button type="button" @click="activeDrawer = null; aiMessage = null; aiGeneratedContent = null" class="btn-ghost px-2 py-1">✕</button>
            </div>

            <template x-if="aiGeneratedContent === null">
                <div>
                    <div class="space-y-3">
                        <div>
                            <label class="label">{{ __('Judul Materi') }}</label>
                            <input :value="title" disabled class="input bg-slate-50 text-slate-500 dark:bg-slate-800">
                            <p class="help">{{ __('Judul, Level, dan Bahasa modul diambil otomatis dari Informasi Dasar di atas.') }}</p>
                        </div>
                        <div x-show="moduleLanguage" x-cloak>
                            <label class="label">{{ __('Bahasa (dari Modul)') }}</label>
                            <input :value="moduleLanguage" disabled class="input bg-slate-50 text-slate-500 dark:bg-slate-800">
                        </div>
                        <div>
                            <label class="label">{{ __('Fokus/instruksi tambahan (opsional)') }}</label>
                            <textarea x-model="aiContentForm.focus" rows="2" class="input text-sm" placeholder="{{ __('Mis. fokus ke penggunaan sehari-hari, sertakan 3 contoh kalimat, dst.') }}"></textarea>
                        </div>
                    </div>

                    <div x-show="aiMessage" x-cloak class="mt-4 rounded-lg bg-slate-100 px-3 py-2 text-sm text-slate-600 dark:bg-slate-800 dark:text-slate-300" x-text="aiMessage"></div>

                    <button type="button" @click="generateContent('{{ route('admin.lessons.ai.generate-content') }}')" :disabled="aiLoading || !title.trim()" class="btn-primary mt-4 w-full disabled:opacity-50">
                        <x-icon name="sparkles" class="size-4" /> <span x-text="aiLoading ? '{{ __('Memproses…') }}' : '{{ __('Generate') }}'"></span>
                    </button>
                    <p class="help" x-show="!title.trim()">{{ __('Isi Judul Materi terlebih dahulu.') }}</p>
                </div>
            </template>

            <template x-if="aiGeneratedContent !== null">
                <div>
                    <label class="label">{{ __('Ringkasan — bisa diedit sebelum dipakai') }}</label>
                    <textarea x-model="aiGeneratedContent.summary" rows="2" class="input text-sm"></textarea>

                    <label class="label mt-3">{{ __('Isi Materi — bisa diedit sebelum dipakai') }}</label>
                    <textarea x-model="aiGeneratedContent.content" rows="12" class="input font-mono text-sm"></textarea>
                    <p class="help" x-show="moduleContentType === 'reading'">{{ __('Paragraf sudah dipisah baris kosong, sesuai format hover-terjemahan Reading.') }}</p>

                    <div class="mt-4 flex gap-2">
                        <button type="button" @click="aiGeneratedContent = null" class="btn-secondary flex-1">{{ __('Generate Ulang') }}</button>
                        <button type="button" @click="useGeneratedContent()" class="btn-primary flex-1">
                            <x-icon name="check" class="size-4" /> {{ __('Gunakan Isi Ini') }}
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- ============================================================ Panel Pratinjau --}}
        <div x-show="activeDrawer === 'preview'" x-cloak class="drawer-backdrop" @click="activeDrawer = null"></div>
        <div x-show="activeDrawer === 'preview'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" class="drawer-panel max-w-xl">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold">{{ __('Pratinjau') }}</h2>
                <button type="button" @click="activeDrawer = null" class="btn-ghost px-2 py-1">✕</button>
            </div>

            <p class="mb-4 text-xs text-slate-400">{{ __('Perkiraan tampilan untuk peserta — bukan replika presisi.') }}</p>

            <h1 class="font-display text-2xl font-extrabold" x-text="title || '{{ __('(Tanpa judul)') }}'"></h1>
            <p class="text-sm text-slate-400" x-text="level"></p>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300" x-text="summary"></p>

            <template x-if="content">
                <article class="mt-5 space-y-3 text-sm">
                    <template x-for="(paragraph, i) in content.split(/\n\s*\n/)" :key="i">
                        <p x-text="paragraph"></p>
                    </template>
                </article>
            </template>

            <div class="mt-6 space-y-2" x-show="items.length">
                <h3 class="label">{{ __('Item') }}</h3>
                <template x-for="item in items" :key="item._uid">
                    <div class="rounded-lg border border-slate-200 p-3 text-sm dark:border-slate-700">
                        <p class="font-display font-bold" x-text="item.term"></p>
                        <p class="text-xs text-slate-400" x-show="item.reading || item.romaji" x-text="[item.reading, item.romaji].filter(Boolean).join(' · ')"></p>
                        <p class="text-slate-500" x-show="item.meaning" x-text="item.meaning"></p>
                    </div>
                </template>
            </div>
        </div>
    </div>
@endsection
