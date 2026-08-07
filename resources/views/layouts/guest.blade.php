<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — {{ $appSettings()['app_name'] ?? config('app.name') }}</title>

    <script>
        (() => {
            const stored = localStorage.getItem('ui.theme');
            const prefers = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (stored === '"dark"' || ((!stored || stored === '"system"') && prefers)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-dvh bg-slate-50 antialiased dark:bg-slate-950">
    <div class="grid min-h-dvh lg:grid-cols-2">

        {{-- Brand panel --}}
        <aside class="relative hidden overflow-hidden bg-brand-700 p-12 text-white lg:flex lg:flex-col">
            <div aria-hidden class="absolute -right-24 -top-24 size-96 rounded-full bg-white/8 blur-2xl"></div>
            <div aria-hidden class="absolute -bottom-32 -left-16 size-80 rounded-full bg-amber-400/20 blur-3xl"></div>

            <a href="{{ route('home') }}" class="relative flex items-center gap-2.5">
                <span class="grid size-10 place-items-center rounded-lg bg-white/15">
                    <x-icon name="book-open" class="size-5" />
                </span>
                <span class="text-lg font-bold">{{ $appSettings()['app_name'] ?? config('app.name') }}</span>
            </a>

            <div class="relative mt-auto">
                <h2 class="text-3xl font-bold leading-tight">
                    {{ $appSettings()['app_tagline'] ?? __('Belajar bahasa dengan jalur yang jelas.') }}
                </h2>

                <ul class="mt-8 space-y-3">
                    @foreach ([
                        __('Modul Jepang: hiragana, katakana, kanji, JLPT'),
                        __('Modul Inggris: grammar, TOEFL, IELTS'),
                        __('Kuis, flashcard, dan pelacakan progres'),
                        __('Menu & hak akses sepenuhnya dari panel admin'),
                    ] as $point)
                        <li class="flex items-center gap-3 text-sm text-white/85">
                            <svg class="size-4.5 shrink-0 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $point }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        {{-- Form --}}
        <main class="grid place-items-center px-4 py-12">
            <div class="w-full max-w-sm">
                <a href="{{ route('home') }}" class="mb-8 flex items-center justify-center gap-2.5 lg:hidden">
                    <span class="grid size-9 place-items-center rounded-lg bg-brand-600 text-white">
                        <x-icon name="book-open" class="size-5" />
                    </span>
                    <span class="font-bold">{{ $appSettings()['app_name'] ?? config('app.name') }}</span>
                </a>

                @if (session('status'))
                    <x-alert type="success" class="mb-5">{{ session('status') }}</x-alert>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
