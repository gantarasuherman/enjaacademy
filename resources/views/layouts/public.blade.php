<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php $settings = $appSettings(); @endphp

    <title>@yield('title', $settings['meta_title'] ?? config('app.name'))</title>
    <meta name="description" content="{{ $settings['meta_description'] ?? '' }}">
    <meta name="keywords" content="{{ $settings['meta_keywords'] ?? '' }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $settings['meta_title'] ?? config('app.name') }}">
    <meta property="og:description" content="{{ $settings['meta_description'] ?? '' }}">
    @if (! empty($settings['og_image']))
        <meta property="og:image" content="{{ Storage::disk('public')->url($settings['og_image']) }}">
    @endif

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

<body class="flex min-h-dvh flex-col bg-slate-50 antialiased dark:bg-slate-950">

    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/85 backdrop-blur-md dark:border-slate-800 dark:bg-slate-900/85">
        <div class="mx-auto flex h-16 max-w-6xl items-center gap-4 px-4 lg:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="grid size-9 place-items-center rounded-lg bg-brand-600 text-white">
                    <x-icon name="book-open" class="size-5" />
                </span>
                <span class="font-bold">{{ $settings['app_name'] ?? config('app.name') }}</span>
            </a>

            <nav class="ml-6 hidden items-center gap-1 md:flex">
                <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">{{ __('Beranda') }}</a>
                <a href="{{ route('about') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">{{ __('Tentang') }}</a>
                <a href="{{ route('contact') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">{{ __('Kontak') }}</a>
            </nav>

            <div class="ml-auto flex items-center gap-2">
                @auth
                    <a href="{{ auth()->user()->canAccessAdminPanel() ? route('admin.home') : route('peserta.dashboard') }}"
                       class="btn-primary text-sm">{{ __('Masuk aplikasi') }}</a>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost hidden text-sm sm:inline-flex">{{ __('Masuk') }}</a>
                    @if ($settings['registration_open'] ?? true)
                        <a href="{{ route('register') }}" class="btn-primary text-sm">{{ __('Daftar Gratis') }}</a>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-12 md:grid-cols-3 lg:px-6">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="grid size-8 place-items-center rounded-lg bg-brand-600 text-white">
                        <x-icon name="book-open" class="size-4" />
                    </span>
                    <span class="font-bold">{{ $settings['app_name'] ?? config('app.name') }}</span>
                </div>
                <p class="mt-3 max-w-sm text-sm text-slate-500">{{ $settings['app_tagline'] ?? '' }}</p>
            </div>

            @if (! empty($footerMenus))
                <div>
                    <p class="mb-3 text-sm font-bold">{{ __('Tautan') }}</p>
                    <ul class="space-y-2 text-sm text-slate-500">
                        @foreach ($footerMenus as $item)
                            <li>
                                <a href="{{ $item['url'] }}"
                                   @if ($item['target'] === '_blank') target="_blank" rel="noopener" @endif
                                   class="hover:text-slate-900 dark:hover:text-white">{{ $item['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <p class="mb-3 text-sm font-bold">{{ __('Kontak') }}</p>
                <ul class="space-y-2 text-sm text-slate-500">
                    @if (! empty($settings['contact_email']))
                        <li>{{ $settings['contact_email'] }}</li>
                    @endif
                    @if (! empty($settings['contact_phone']))
                        <li>{{ $settings['contact_phone'] }}</li>
                    @endif
                    @if (! empty($settings['address']))
                        <li>{{ $settings['address'] }}</li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="border-t border-slate-200 px-4 py-5 text-center text-xs text-slate-400 dark:border-slate-800">
            &copy; {{ date('Y') }} {{ $settings['app_name'] ?? config('app.name') }}.
            {{ __('Dibangun dengan Laravel 12 & React 19.') }}
        </div>
    </footer>
</body>
</html>
