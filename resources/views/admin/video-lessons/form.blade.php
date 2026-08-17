@extends('layouts.app')
@section('title', $lesson->exists ? __('Edit materi video') : __('Materi video baru'))

@php
    $lessonData = [
        'title' => $lesson->title,
        'slug' => $lesson->slug,
        'learning_module_id' => $lesson->learning_module_id,
        'level' => $lesson->level,
        'summary' => $lesson->summary,
        'video_url' => $lesson->video_url,
        'estimated_minutes' => $lesson->estimated_minutes,
        'xp_reward' => $lesson->xp_reward,
        'sort_order' => $lesson->sort_order,
        'is_published' => $lesson->is_published,
    ];
@endphp

@section('content')
    <div
        x-data="videoLessonBuilder({
            lesson: @js($lessonData),
            chapters: @js($chapters->map->only(['id', 'term', 'extra'])->values()),
        })"
    >
        <x-page-header
            :title="$lesson->exists ? __('Edit materi video') : __('Materi video baru')"
            :back="route('admin.video-lessons.index')"
            :back-label="__('Kembali ke materi video')"
        >
            <x-slot:actions>
                <span class="badge" :class="isPublished ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15' : 'bg-slate-200 text-slate-600 dark:bg-slate-700'"
                      x-text="isPublished ? '{{ __('Diterbitkan') }}' : '{{ __('Draft') }}'"></span>
            </x-slot:actions>
        </x-page-header>

        <form x-ref="form" method="POST"
              action="{{ $lesson->exists ? route('admin.video-lessons.update', $lesson) : route('admin.video-lessons.store') }}"
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
                                   class="input text-base @error('title') border-rose-400 @enderror" placeholder="{{ __('Sesi 1: Perkenalan') }}">
                            @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="label">{{ __('Modul') }} <span class="text-rose-500">*</span></label>
                            <select name="learning_module_id" x-model="moduleId" required class="input">
                                <option value="" disabled>{{ __('Pilih modul video…') }}</option>
                                @foreach ($modules as $module)
                                    <option value="{{ $module->id }}">{{ $module->name }}</option>
                                @endforeach
                            </select>
                            <p class="help">{{ __('Hanya modul dengan Tipe konten "video" yang tampil di sini.') }}</p>
                            @error('learning_module_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="summary" class="label">{{ __('Ringkasan') }}</label>
                            <textarea id="summary" name="summary" x-model="summary" rows="2" class="input"
                                      placeholder="{{ __('Apa yang dipelajari di video ini…') }}"></textarea>
                        </div>
                    </div>
                </div>

                {{-- ---------------------------------------------- Video --}}
                <div class="card p-5">
                    <h2 class="mb-1 font-semibold">
                        <x-icon name="play" class="inline size-4" /> {{ __('URL Video') }}
                    </h2>
                    <p class="help mb-3">{{ __('Link YouTube atau Vimeo.') }}</p>
                    <input name="video_url" x-model="videoUrl" type="url" class="input" placeholder="https://youtube.com/watch?v=…">
                    @error('video_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

                    <div class="mt-4">
                        <label for="cover_image" class="label text-xs">
                            <x-icon name="image" class="inline size-3.5" /> {{ __('Gambar sampul (opsional)') }}
                        </label>
                        <input id="cover_image" name="cover_image" type="file" accept="image/*" class="input py-1.5 text-sm">
                        @if ($lesson->cover_image)
                            <img src="{{ asset('storage/'.$lesson->cover_image) }}" class="mt-2 h-16 rounded-lg object-cover" alt="">
                        @endif
                    </div>
                </div>

                {{-- ---------------------------------------------- Bab Video --}}
                <div class="card p-5">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h2 class="font-semibold">{{ __('Bab Video') }}</h2>
                            <p class="help mt-0.5">{{ __('Peserta bisa klik bab untuk lompat ke waktu tertentu di video.') }}</p>
                        </div>
                        <button type="button" @click="addChapter()" class="btn-secondary text-sm">
                            <x-icon name="plus" class="size-4" /> {{ __('Tambah bab') }}
                        </button>
                    </div>

                    <div x-show="chapters.length === 0" x-cloak class="rounded-lg border border-dashed border-slate-300 p-6 text-center text-sm text-slate-400 dark:border-slate-700">
                        {{ __('Belum ada bab. Opsional — video tanpa bab tetap bisa diputar.') }}
                    </div>

                    <div class="space-y-2">
                        <template x-for="(chapter, index) in chapters" :key="chapter._uid">
                            <div class="flex items-center gap-2 rounded-lg border border-slate-200 p-3 dark:border-slate-700">
                                <input type="hidden" :name="`items[${index}][id]`" :value="chapter.id">
                                <input type="hidden" :name="`items[${index}][extra][type]`" value="video_chapter">

                                <input type="text" :name="`items[${index}][term]`" x-model="chapter.term"
                                       placeholder="{{ __('Judul bab') }}" required class="input flex-1 text-sm">

                                <input type="text" :name="`items[${index}][extra][timestamp]`" x-model="chapter.timestamp"
                                       placeholder="mm:ss" class="input w-24 text-sm font-mono">

                                <div class="flex shrink-0 gap-1">
                                    <button type="button" @click="moveChapter(index, -1)" :disabled="index === 0"
                                            class="btn-ghost px-2 py-1 disabled:opacity-30" :aria-label="'{{ __('Naikkan') }}'">
                                        <x-icon name="chevron-up" class="size-4" />
                                    </button>
                                    <button type="button" @click="moveChapter(index, 1)" :disabled="index === chapters.length - 1"
                                            class="btn-ghost px-2 py-1 disabled:opacity-30" :aria-label="'{{ __('Turunkan') }}'">
                                        <x-icon name="chevron-down" class="size-4" />
                                    </button>
                                    <button type="button" @click="removeChapter(index)" class="btn-ghost px-2 py-1 text-rose-600" :aria-label="'{{ __('Hapus') }}'">
                                        <x-icon name="x" class="size-4" />
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ---------------------------------------------------- Opsi Tambahan --}}
                <div class="card p-5" x-data="{ open: false }">
                    <button type="button" @click="open = !open" class="flex w-full items-center justify-between text-left font-semibold">
                        {{ __('Opsi Tambahan') }}
                        <span class="inline-block transition" :class="open ? 'rotate-180' : ''">
                            <x-icon name="chevron-down" class="size-4" />
                        </span>
                    </button>

                    <div x-show="open" x-collapse class="mt-4 space-y-4">
                        <div>
                            <label for="slug" class="label">{{ __('Slug') }}</label>
                            <input id="slug" name="slug" x-model="slug" class="input font-mono text-sm">
                            <p class="help">{{ __('Dipakai di URL pembelajar — otomatis dari judul kecuali diubah manual di sini.') }}</p>
                            @error('slug') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="level" class="label">{{ __('Level') }}</label>
                            <input id="level" name="level" x-model="level" class="input font-mono" placeholder="N5 / A2 / B1">
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
                        <a href="{{ route('admin.video-lessons.index') }}" class="btn-ghost text-center">{{ __('Batal') }}</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
