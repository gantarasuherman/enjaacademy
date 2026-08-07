@extends('layouts.app')
@section('title', $quiz->title)

@section('content')
    <x-page-header
        :title="$quiz->title"
        :description="$quiz->description"
        :back="route('admin.quizzes.index')"
        :back-label="__('Kembali ke kuis')"
    >
        <x-slot:actions>
            @can('quizzes.update')
                <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn-primary">{{ __('Edit kuis') }}</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat icon="clipboard" tone="brand" :label="__('Jumlah soal')" :value="$quiz->questions->count()" />
        <x-stat icon="activity" tone="sky" :label="__('Percobaan')" :value="number_format($stats['attempts'])" />
        <x-stat icon="target" tone="amber" :label="__('Rata-rata skor')" :value="$stats['average'].'%'" />
        <x-stat icon="trophy" tone="emerald" :label="__('Tingkat kelulusan')" :value="$stats['pass_rate'].'%'" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="card">
                <div class="card-header"><h2 class="font-semibold">{{ __('Daftar soal & kunci jawaban') }}</h2></div>

                <ol class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($quiz->questions as $index => $question)
                        <li class="px-5 py-4">
                            <div class="flex items-start gap-3">
                                <span class="grid size-6 shrink-0 place-items-center rounded-full bg-slate-100 text-xs font-bold text-slate-500 dark:bg-slate-800">
                                    {{ $index + 1 }}
                                </span>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="badge bg-slate-100 text-slate-600 dark:bg-slate-800">{{ $question->type }}</span>
                                        <span class="text-xs text-slate-400">{{ $question->score }} {{ __('poin') }}</span>
                                    </div>

                                    <p class="mt-1.5 text-sm font-medium">{{ $question->question }}</p>

                                    @if ($question->type === 'fill_blank')
                                        <p class="mt-2 text-sm">
                                            <span class="text-slate-500">{{ __('Kunci:') }}</span>
                                            <span class="font-semibold text-emerald-600">{{ $question->correct_text }}</span>
                                        </p>
                                    @else
                                        <ul class="mt-2 space-y-1">
                                            @foreach ($question->options as $option)
                                                <li class="flex items-center gap-2 text-sm">
                                                    @if ($option->is_correct)
                                                        <span class="text-emerald-600">✓</span>
                                                        <span class="font-medium text-emerald-600">{{ $option->label }}</span>
                                                    @else
                                                        <span class="text-slate-300">○</span>
                                                        <span class="text-slate-500">{{ $option->label }}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    @if ($question->explanation)
                                        <p class="mt-2 rounded-lg bg-slate-50 p-2.5 text-xs text-slate-500 dark:bg-slate-800/60">
                                            💡 {{ $question->explanation }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-5 py-10 text-center text-sm text-slate-500">{{ __('Kuis ini belum punya soal.') }}</li>
                    @endforelse
                </ol>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Pengaturan') }}</h2>

                <dl class="space-y-3 text-sm">
                    @foreach ([
                        __('Modul') => $quiz->module?->name ?? '—',
                        __('Level') => $quiz->level ?? '—',
                        __('Tingkat') => $quiz->difficulty,
                        __('Skor lulus') => $quiz->pass_score.'%',
                        __('XP reward') => $quiz->xp_reward,
                        __('Batas waktu') => $quiz->time_limit_seconds ? intdiv($quiz->time_limit_seconds, 60).' '.__('menit') : __('tanpa batas'),
                        __('Maks percobaan') => $quiz->max_attempts ?? __('tak terbatas'),
                    ] as $label => $value)
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-500">{{ $label }}</dt>
                            <dd class="text-right font-medium">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div class="card">
                <div class="card-header"><h2 class="font-semibold">{{ __('Percobaan terakhir') }}</h2></div>

                <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($attempts as $attempt)
                        <li class="flex items-center gap-3 px-5 py-2.5">
                            <span class="min-w-0 flex-1 truncate text-sm">{{ $attempt->user?->name ?? '—' }}</span>
                            <span class="shrink-0 font-mono text-xs">{{ $attempt->score }}%</span>
                            @if ($attempt->passed)
                                <span class="badge shrink-0 bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15">✓</span>
                            @else
                                <span class="badge shrink-0 bg-rose-100 text-rose-700 dark:bg-rose-500/15">✕</span>
                            @endif
                        </li>
                    @empty
                        <li class="px-5 py-8 text-center text-sm text-slate-500">{{ __('Belum ada percobaan.') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
