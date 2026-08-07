@extends('layouts.public')
@section('title', $appSettings()['meta_title'] ?? config('app.name'))

@section('content')
    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div aria-hidden class="absolute inset-x-0 -top-40 h-96 bg-gradient-to-b from-brand-100 to-transparent dark:from-brand-500/10"></div>

        <div class="relative mx-auto max-w-6xl px-4 py-20 lg:px-6 lg:py-28">
            <div class="max-w-2xl">
                <span class="badge bg-brand-100 text-brand-700 dark:bg-brand-500/15 dark:text-brand-300">
                    {{ __('Bahasa Jepang & Inggris dalam satu tempat') }}
                </span>

                <h1 class="mt-4 text-4xl font-bold leading-tight tracking-tight lg:text-5xl">
                    {{ $appSettings()['app_tagline'] ?? __('Belajar bahasa dengan jalur yang jelas.') }}
                </h1>

                <p class="mt-5 text-base text-slate-600 dark:text-slate-400">
                    {{ __('Dari hiragana sampai JLPT, dari grammar dasar sampai TOEFL — lengkap dengan kuis, flashcard spaced repetition, dan pelacakan progres harian.') }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('peserta.dashboard') }}" class="btn-primary px-6 py-3">{{ __('Lanjutkan belajar') }}</a>
                    @else
                        <a href="{{ route('register') }}" class="btn-primary px-6 py-3">{{ __('Mulai Belajar Gratis') }}</a>
                        <a href="{{ route('login') }}" class="btn-secondary px-6 py-3">{{ __('Sudah punya akun') }}</a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    {{-- Languages --}}
    <section class="border-t border-slate-200 bg-white py-20 dark:border-slate-800 dark:bg-slate-900">
        <div class="mx-auto max-w-6xl px-4 lg:px-6">
            <h2 class="text-3xl font-bold">{{ __('Dua bahasa, banyak modul') }}</h2>
            <p class="mt-3 max-w-2xl text-slate-500">
                {{ __('Setiap modul punya materi, item latihan, dan kuis penutupnya sendiri.') }}
            </p>

            <div class="mt-10 grid gap-6 md:grid-cols-2">
                @foreach ($languages as $language)
                    <div class="card p-6">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">{{ $language->flag }}</span>
                            <div>
                                <h3 class="text-lg font-bold">{{ $language->name }}</h3>
                                <p class="text-sm text-slate-500">{{ $language->modules_count }} {{ __('modul aktif') }}</p>
                            </div>
                        </div>

                        <p class="mt-4 text-sm text-slate-500">{{ $language->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Featured modules --}}
    @if ($featured->isNotEmpty())
        <section class="py-20">
            <div class="mx-auto max-w-6xl px-4 lg:px-6">
                <h2 class="text-3xl font-bold">{{ __('Modul unggulan') }}</h2>

                <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($featured as $module)
                        <div class="card p-5">
                            <div class="flex items-center gap-3">
                                <span class="grid size-11 place-items-center rounded-lg bg-brand-100 text-brand-700 dark:bg-brand-500/15 dark:text-brand-300">
                                    <x-icon :name="$module->icon" class="size-5" />
                                </span>
                                <div class="min-w-0">
                                    <h3 class="truncate font-bold">{{ $module->name }}</h3>
                                    <p class="truncate text-xs text-slate-500">{{ $module->language?->name }}</p>
                                </div>
                            </div>

                            <p class="mt-3 line-clamp-2 text-sm text-slate-500">{{ $module->description }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Leaderboard teaser --}}
    @if ($leaderboard->isNotEmpty())
        <section class="border-t border-slate-200 bg-white py-20 dark:border-slate-800 dark:bg-slate-900">
            <div class="mx-auto max-w-3xl px-4 lg:px-6">
                <h2 class="text-center text-3xl font-bold">{{ __('Peserta paling konsisten') }}</h2>
                <p class="mt-3 text-center text-slate-500">{{ __('XP dikumpulkan dari materi, kuis, dan flashcard.') }}</p>

                <ol class="card mt-10 divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($leaderboard as $index => $entry)
                        <li class="flex items-center gap-4 px-5 py-3.5">
                            <span class="w-6 text-center">
                                @if ($index < 3)
                                    <span class="text-lg">{{ ['🥇', '🥈', '🥉'][$index] }}</span>
                                @else
                                    <span class="font-mono text-sm text-slate-400">{{ $index + 1 }}</span>
                                @endif
                            </span>
                            <span class="min-w-0 flex-1 truncate font-medium">{{ $entry->name }}</span>
                            <span class="shrink-0 text-sm text-slate-500">Lv {{ $entry->level }}</span>
                            <span class="shrink-0 font-mono text-sm font-semibold">{{ number_format($entry->xp_total) }} XP</span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <section class="bg-brand-700 py-16 text-white">
        <div class="mx-auto max-w-3xl px-4 text-center lg:px-6">
            <h2 class="text-3xl font-bold">{{ __('Sepuluh menit hari ini mengalahkan tiga jam bulan depan.') }}</h2>
            @guest
                <a href="{{ route('register') }}" class="mt-7 inline-flex rounded-lg bg-white px-6 py-3 font-semibold text-brand-700 transition hover:bg-slate-100">
                    {{ __('Buat akun gratis') }}
                </a>
            @endguest
        </div>
    </section>
@endsection
