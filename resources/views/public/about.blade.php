@extends('layouts.public')
@section('title', __('Tentang Kami'))

@section('content')
    <section class="mx-auto max-w-3xl px-4 py-20 lg:px-6">
        <h1 class="text-4xl font-bold tracking-tight">{{ __('Tentang Kami') }}</h1>

        <div class="mt-8 space-y-6 text-slate-600 dark:text-slate-400">
            <p class="text-lg">
                {{ $appSettings()['app_tagline'] ?? __('Platform belajar bahasa Jepang dan Inggris untuk penutur Bahasa Indonesia.') }}
            </p>

            <p>
                {{ __('Kami membangun platform ini karena satu pengamatan sederhana: kebanyakan orang berhenti belajar bahasa bukan karena materinya sulit, tetapi karena tidak tahu harus mengerjakan apa berikutnya. Jalur belajar yang jelas mengalahkan tumpukan materi yang berserakan.') }}
            </p>

            <h2 class="pt-4 text-2xl font-bold text-slate-900 dark:text-white">{{ __('Cara kerja kami') }}</h2>

            <ul class="space-y-3">
                @foreach ([
                    __('Materi disusun bertingkat — satu modul selesai sebelum lanjut ke berikutnya.'),
                    __('Setiap materi ditutup kuis, dan setiap kuis punya pembahasan.'),
                    __('Flashcard memakai spaced repetition supaya kosakata benar-benar melekat.'),
                    __('Progres dicatat jujur: XP, level, dan streak harian.'),
                ] as $point)
                    <li class="flex gap-3">
                        <span class="mt-2 size-1.5 shrink-0 rounded-full bg-brand-600"></span>
                        {{ $point }}
                    </li>
                @endforeach
            </ul>

            <h2 class="pt-4 text-2xl font-bold text-slate-900 dark:text-white">{{ __('Teknologi') }}</h2>

            <p>
                {{ __('Aplikasi peserta dibangun dengan React 19 dan TypeScript, sedangkan panel admin serta API memakai Laravel 12. Seluruh menu, role, dan hak akses dikelola dari panel admin tanpa perlu mengubah kode.') }}
            </p>
        </div>

        @guest
            <a href="{{ route('register') }}" class="btn-primary mt-10 px-6 py-3">{{ __('Mulai belajar') }}</a>
        @endguest
    </section>
@endsection
