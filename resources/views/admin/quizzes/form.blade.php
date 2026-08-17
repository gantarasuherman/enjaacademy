@extends('layouts.app')
@section('title', $quiz->exists ? __('Edit kuis') : __('Kuis baru'))

@section('content')
    <x-page-header
        :title="$quiz->exists ? __('Edit kuis') : __('Kuis baru')"
        :back="route('admin.quizzes.index')"
        :back-label="__('Kembali ke kuis')"
    />

    <form method="POST"
          action="{{ $quiz->exists ? route('admin.quizzes.update', $quiz) : route('admin.quizzes.store') }}"
          class="grid gap-6 lg:grid-cols-3">
        @csrf
        @if ($quiz->exists) @method('PUT') @endif

        <div class="space-y-6 lg:col-span-2">
            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Informasi kuis') }}</h2>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="title" class="label">{{ __('Judul') }} <span class="text-rose-500">*</span></label>
                        <input id="title" name="title" value="{{ old('title', $quiz->title) }}" required
                               class="input @error('title') border-rose-400 @enderror">
                        @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="learning_module_id" class="label">{{ __('Modul') }}</label>
                        <select id="learning_module_id" name="learning_module_id" class="input">
                            <option value="">{{ __('— tanpa modul —') }}</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module->id }}" @selected((int) old('learning_module_id', $quiz->learning_module_id) === $module->id)>
                                    {{ $module->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="lesson_id" class="label">{{ __('Materi terkait') }}</label>
                        <select id="lesson_id" name="lesson_id" class="input">
                            <option value="">{{ __('— tanpa materi —') }}</option>
                            @foreach ($lessons as $lesson)
                                <option value="{{ $lesson->id }}" @selected((int) old('lesson_id', $quiz->lesson_id) === $lesson->id)>
                                    {{ $lesson->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="label">{{ __('Deskripsi') }}</label>
                        <textarea id="description" name="description" rows="2" class="input">{{ old('description', $quiz->description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Question builder --}}
            <div class="card p-5"
                 x-data="questionBuilder({ initial: @js($questions->map(fn ($q) => [
                     'id' => $q->id,
                     'question' => $q->question,
                     'type' => $q->type,
                     'explanation' => $q->explanation,
                     'correct_text' => $q->correct_text,
                     'score' => $q->score,
                     'options' => $q->options->map->only(['label', 'is_correct'])->values(),
                 ])->values()) })">

                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-semibold">{{ __('Soal') }}</h2>
                        <p class="text-xs text-slate-500">
                            <span x-text="questions.length"></span> {{ __('soal') }}
                            <template x-if="totalProblems > 0">
                                <span class="ml-1 text-amber-600" x-text="`· ${totalProblems} perlu diperbaiki`"></span>
                            </template>
                        </p>
                    </div>
                    <button type="button" @click="add()" class="btn-secondary text-sm">
                        <x-icon name="plus" class="size-4" /> {{ __('Tambah soal') }}
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(question, index) in questions" :key="index">
                        <div class="rounded-lg border border-slate-200 p-4 dark:border-slate-700">
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-sm font-semibold text-slate-400" x-text="`Soal ${index + 1}`"></span>

                                <div class="flex gap-1">
                                    <button type="button" @click="move(index, -1)" class="btn-ghost px-1.5 py-0.5 text-xs">↑</button>
                                    <button type="button" @click="move(index, 1)" class="btn-ghost px-1.5 py-0.5 text-xs">↓</button>
                                    <button type="button" @click="remove(index)" class="btn-ghost px-1.5 py-0.5 text-xs text-rose-600">{{ __('Hapus') }}</button>
                                </div>
                            </div>

                            <input type="hidden" :name="`questions[${index}][id]`" :value="question.id">

                            <div class="space-y-3">
                                <textarea data-question-text :name="`questions[${index}][question]`" x-model="question.question"
                                          rows="2" placeholder="{{ __('Tulis pertanyaan…') }}" class="input text-sm"></textarea>

                                <div class="grid gap-2 sm:grid-cols-3">
                                    <select :name="`questions[${index}][type]`" x-model="question.type"
                                            @change="onTypeChange(question)" class="input text-sm">
                                        <option value="multiple_choice">{{ __('Pilihan ganda') }}</option>
                                        <option value="true_false">{{ __('Benar / Salah') }}</option>
                                        <option value="fill_blank">{{ __('Isian') }}</option>
                                        <option value="matching">{{ __('Menjodohkan') }}</option>
                                        <option value="arrange">{{ __('Susun Kata') }}</option>
                                    </select>

                                    <input type="number" min="1" :name="`questions[${index}][score]`" x-model="question.score"
                                           placeholder="{{ __('Poin') }}" class="input text-sm">

                                    <template x-if="question.type === 'fill_blank'">
                                        <input :name="`questions[${index}][correct_text]`" x-model="question.correct_text"
                                               placeholder="{{ __('Kunci jawaban') }}" class="input text-sm">
                                    </template>
                                </div>

                                {{-- Options --}}
                                <template x-if="question.type !== 'fill_blank' && question.type !== 'arrange'">
                                    <div class="space-y-2">
                                        <template x-for="(option, optionIndex) in question.options" :key="optionIndex">
                                            <div class="flex items-center gap-2">
                                                <input type="radio" :name="`correct_${index}`"
                                                       :checked="option.is_correct"
                                                       @change="markCorrect(question, optionIndex)"
                                                       class="shrink-0 border-slate-300 text-emerald-600"
                                                       :aria-label="`{{ __('Tandai benar') }} ${optionIndex + 1}`">

                                                <input type="hidden" :name="`questions[${index}][options][${optionIndex}][is_correct]`"
                                                       :value="option.is_correct ? 1 : 0">

                                                <input :name="`questions[${index}][options][${optionIndex}][label]`"
                                                       x-model="option.label"
                                                       :placeholder="`{{ __('Pilihan') }} ${String.fromCharCode(65 + optionIndex)}`"
                                                       class="input text-sm">

                                                <button type="button" @click="removeOption(question, optionIndex)"
                                                        class="btn-ghost shrink-0 px-2 py-1 text-xs text-rose-600">&times;</button>
                                            </div>
                                        </template>

                                        <button type="button" @click="addOption(question)" class="btn-ghost text-xs">
                                            + {{ __('Tambah pilihan') }}
                                        </button>
                                    </div>
                                </template>

                                {{-- Susun Kata: kata dalam urutan yang benar — urutan barisnya sendiri adalah kunci jawaban, jadi diurutkan lewat ↑↓, bukan ditandai. --}}
                                <template x-if="question.type === 'arrange'">
                                    <div class="space-y-2">
                                        <p class="help mt-0">{{ __('Ketik kata-kata sesuai urutan kalimat yang benar — siswa akan melihatnya dalam urutan acak dan harus menyusunnya kembali.') }}</p>

                                        <template x-for="(option, optionIndex) in question.options" :key="optionIndex">
                                            <div class="flex items-center gap-2">
                                                <span class="w-5 shrink-0 text-center text-xs text-slate-400" x-text="optionIndex + 1"></span>

                                                <input :name="`questions[${index}][options][${optionIndex}][label]`"
                                                       x-model="option.label"
                                                       :placeholder="`{{ __('Kata') }} ${optionIndex + 1}`"
                                                       class="input text-sm">

                                                <div class="flex shrink-0 gap-1">
                                                    <button type="button" @click="moveOption(question, optionIndex, -1)" class="btn-ghost px-1.5 py-0.5 text-xs">↑</button>
                                                    <button type="button" @click="moveOption(question, optionIndex, 1)" class="btn-ghost px-1.5 py-0.5 text-xs">↓</button>
                                                    <button type="button" @click="removeOption(question, optionIndex)" class="btn-ghost px-2 py-1 text-xs text-rose-600">&times;</button>
                                                </div>
                                            </div>
                                        </template>

                                        <button type="button" @click="addOption(question)" class="btn-ghost text-xs">
                                            + {{ __('Tambah kata') }}
                                        </button>
                                    </div>
                                </template>

                                <textarea :name="`questions[${index}][explanation]`" x-model="question.explanation"
                                          rows="2" placeholder="{{ __('Pembahasan (ditampilkan setelah kuis selesai)') }}"
                                          class="input text-sm"></textarea>

                                {{-- Inline validation mirroring the server rules --}}
                                <template x-if="problems(question).length > 0">
                                    <ul class="rounded-lg bg-amber-50 p-2.5 text-xs text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">
                                        <template x-for="issue in problems(question)" :key="issue">
                                            <li x-text="`• ${issue}`"></li>
                                        </template>
                                    </ul>
                                </template>
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
                        <label for="category" class="label">{{ __('Jenis') }}</label>
                        <select id="category" name="category" class="input">
                            <option value="quiz" @selected(old('category', $quiz->category ?? 'quiz') === 'quiz')>{{ __('Kuis (latihan singkat)') }}</option>
                            <option value="test" @selected(old('category', $quiz->category ?? 'quiz') === 'test')>{{ __('Tes (ujian panjang berwaktu)') }}</option>
                        </select>
                        @error('category') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="difficulty" class="label">{{ __('Tingkat kesulitan') }}</label>
                        <select id="difficulty" name="difficulty" class="input">
                            @foreach (['easy' => __('Mudah'), 'medium' => __('Sedang'), 'hard' => __('Sulit')] as $value => $label)
                                <option value="{{ $value }}" @selected(old('difficulty', $quiz->difficulty ?? 'easy') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="level" class="label">{{ __('Level (Beginner..Advanced / N5..N1)') }}</label>
                        <input id="level" name="level" value="{{ old('level', $quiz->level) }}" class="input font-mono" placeholder="N5 / Intermediate">
                    </div>

                    <div>
                        <label for="time_limit_minutes" class="label">{{ __('Batas waktu (menit)') }}</label>
                        <input id="time_limit_minutes" name="time_limit_minutes" type="number" min="1"
                               value="{{ old('time_limit_minutes', $quiz->time_limit_seconds ? intdiv($quiz->time_limit_seconds, 60) : '') }}"
                               class="input" placeholder="{{ __('kosong = tanpa batas') }}">
                    </div>

                    <div>
                        <label for="pass_score" class="label">{{ __('Skor lulus (%)') }} <span class="text-rose-500">*</span></label>
                        <input id="pass_score" name="pass_score" type="number" min="0" max="100" required
                               value="{{ old('pass_score', $quiz->pass_score ?? 70) }}" class="input">
                    </div>

                    <div>
                        <label for="xp_reward" class="label">{{ __('XP reward') }}</label>
                        <input id="xp_reward" name="xp_reward" type="number" min="0"
                               value="{{ old('xp_reward', $quiz->xp_reward ?? 50) }}" class="input">
                    </div>

                    <div x-data="{ unlimited: {{ old('max_attempts', $quiz->max_attempts) ? 'false' : 'true' }} }">
                        <label class="label">{{ __('Pengaturan Percobaan') }}</label>
                        <p class="help mt-0 mb-2">{{ __('Berapa kali satu siswa boleh mengerjakan kuis ini.') }}</p>

                        <div class="flex flex-wrap gap-4">
                            <label class="inline-flex items-center gap-2 text-sm">
                                <input type="radio" value="true" x-model.boolean="unlimited" class="accent-brand-600">
                                {{ __('Tidak Terbatas') }}
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm">
                                <input type="radio" value="false" x-model.boolean="unlimited" class="accent-brand-600">
                                {{ __('Dibatasi') }}
                            </label>
                        </div>

                        <div x-show="!unlimited" x-collapse class="mt-3">
                            <label for="max_attempts" class="label">{{ __('Maksimal Percobaan') }}</label>
                            <input id="max_attempts" name="max_attempts" type="number" min="1"
                                   :required="!unlimited" :disabled="unlimited"
                                   value="{{ old('max_attempts', $quiz->max_attempts) }}" class="input">
                            <p class="help">{{ __('Harus berupa angka bulat, minimal 1.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Perilaku') }}</h2>

                <div class="space-y-3">
                    @foreach ([
                        'shuffle_questions' => __('Acak urutan soal'),
                        'shuffle_options' => __('Acak urutan pilihan'),
                        'show_explanation' => __('Tampilkan pembahasan'),
                        'is_published' => __('Terbitkan kuis'),
                    ] as $field => $label)
                        <label class="flex items-center gap-2.5 text-sm">
                            <input type="hidden" name="{{ $field }}" value="0">
                            <input type="checkbox" name="{{ $field }}" value="1"
                                   @checked(old($field, $quiz->{$field} ?? ($field !== 'is_published')))
                                   class="rounded border-slate-300 text-brand-600">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                @error('is_published') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1">
                    {{ $quiz->exists ? __('Simpan') : __('Buat kuis') }}
                </button>
                <a href="{{ route('admin.quizzes.index') }}" class="btn-secondary">{{ __('Batal') }}</a>
            </div>
        </div>
    </form>
@endsection
