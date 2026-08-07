<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('Admin')) — {{ $appSettings()['app_name'] ?? config('app.name') }}</title>

    <script>
        // Applied before first paint so a dark-mode reload never flashes white.
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

<body class="min-h-dvh bg-slate-50 text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-200">

    {{-- ==================== SIDEBAR ==================== --}}
    {{-- Every entry comes from the `menus` table via MenuBuilder. There is no
         hardcoded navigation anywhere in this file. --}}
    <aside
        x-data
        class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-slate-200 bg-white
               transition-transform duration-200 lg:translate-x-0 dark:border-slate-800 dark:bg-slate-900"
        :class="$store.ui.sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="flex h-16 shrink-0 items-center gap-2.5 border-b border-slate-200 px-4 dark:border-slate-800">
            <a href="{{ route('admin.home') }}" class="flex min-w-0 items-center gap-2.5">
                <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-brand-600 text-white">
                    <x-icon name="book-open" class="size-5" />
                </span>
                <span class="truncate font-semibold">{{ $appSettings()['app_name'] ?? config('app.name') }}</span>
            </a>

            <button type="button" @click="$store.ui.sidebarOpen = false"
                    class="ml-auto rounded-lg p-1.5 text-slate-500 hover:bg-slate-100 lg:hidden dark:hover:bg-slate-800"
                    aria-label="{{ __('Close menu') }}">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4" aria-label="{{ __('Main navigation') }}">
            <ul class="space-y-0.5">
                @forelse ($sidebarMenus as $item)
                    <x-navigation.sidebar-item :item="$item" />
                @empty
                    <li class="px-3 py-8 text-center text-sm text-slate-400">
                        {{ __('No menu is visible for your role yet.') }}
                        @can('menus.create')
                            <a href="{{ route('admin.menus.create') }}" class="mt-2 block font-medium text-brand-600 hover:underline">
                                {{ __('Create the first menu') }}
                            </a>
                        @endcan
                    </li>
                @endforelse
            </ul>
        </nav>

        <div class="border-t border-slate-200 p-3 dark:border-slate-800">
            <div class="flex items-center gap-3 rounded-lg px-2 py-2">
                <img src="{{ auth()->user()->avatar_url }}" alt=""
                     class="size-9 shrink-0 rounded-full object-cover">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-slate-500">{{ auth()->user()->getRoleNames()->implode(', ') ?: __('No role') }}</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- Mobile backdrop --}}
    <div x-data x-show="$store.ui.sidebarOpen" x-cloak x-transition.opacity
         @click="$store.ui.sidebarOpen = false"
         class="fixed inset-0 z-30 bg-slate-950/50 backdrop-blur-sm lg:hidden"></div>

    {{-- ==================== MAIN ==================== --}}
    <div class="lg:pl-64">

        {{-- Topbar --}}
        <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-slate-200 bg-white/85 px-4 backdrop-blur-md lg:px-6 dark:border-slate-800 dark:bg-slate-900/85">
            <button type="button" x-data @click="$store.ui.sidebarOpen = true"
                    class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden dark:text-slate-300 dark:hover:bg-slate-800"
                    aria-label="{{ __('Open menu') }}">
                <x-icon name="menu" class="size-5" />
            </button>

            @if (! empty($topbarMenus))
                <nav class="hidden items-center gap-1 md:flex" aria-label="{{ __('Quick links') }}">
                    @foreach ($topbarMenus as $item)
                        <a href="{{ $item['url'] }}"
                           @if ($item['target'] === '_blank') target="_blank" rel="noopener" @endif
                           class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">
                            {{ $item['title'] }}
                        </a>
                    @endforeach
                </nav>
            @endif

            <div class="ml-auto flex items-center gap-1.5">
                {{-- Theme toggle --}}
                <div x-data class="flex items-center">
                    <button type="button" @click="$store.ui.setTheme($store.ui.theme === 'dark' ? 'light' : 'dark')"
                            class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800"
                            aria-label="{{ __('Toggle theme') }}">
                        <svg class="size-5 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg class="hidden size-5 dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>
                </div>

                {{-- User menu --}}
                <div x-data="{ open: false }" class="relative">
                    <button type="button" @click="open = !open" :aria-expanded="open"
                            class="flex items-center gap-2 rounded-full p-0.5 transition hover:bg-slate-100 dark:hover:bg-slate-800">
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                             class="size-8 rounded-full object-cover">
                    </button>

                    <div x-show="open" x-cloak @click.outside="open = false" x-transition
                         class="absolute right-0 z-30 mt-2 w-56 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-lg dark:border-slate-800 dark:bg-slate-900">
                        <div class="border-b border-slate-200 px-4 py-3 dark:border-slate-800">
                            <p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                        </div>

                        <a href="{{ route('peserta.profile.edit') }}"
                           class="block px-4 py-2.5 text-sm transition hover:bg-slate-50 dark:hover:bg-slate-800">
                            {{ __('My profile') }}
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="block w-full border-t border-slate-200 px-4 py-2.5 text-left text-sm text-rose-600 transition hover:bg-rose-50 dark:border-slate-800 dark:hover:bg-rose-500/10">
                                {{ __('Sign out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page --}}
        <main class="mx-auto w-full max-w-7xl px-4 py-6 lg:px-6 lg:py-8">
            @if (session('success'))
                <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error" class="mb-5">{{ session('error') }}</x-alert>
            @endif

            @if ($errors->any())
                <x-alert type="error" class="mb-5">
                    <p class="font-semibold">{{ __('Please fix the following:') }}</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            @yield('content')
        </main>

        @if (! empty($footerMenus))
            <footer class="border-t border-slate-200 px-4 py-6 lg:px-6 dark:border-slate-800">
                <nav class="mx-auto flex max-w-7xl flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-500">
                    @foreach ($footerMenus as $item)
                        <a href="{{ $item['url'] }}"
                           @if ($item['target'] === '_blank') target="_blank" rel="noopener" @endif
                           class="hover:text-slate-900 dark:hover:text-slate-200">{{ $item['title'] }}</a>
                    @endforeach
                    <span class="ml-auto">&copy; {{ date('Y') }} {{ $appSettings()['app_name'] ?? config('app.name') }}</span>
                </nav>
            </footer>
        @endif
    </div>

    {{-- Toasts --}}
    <div x-data class="pointer-events-none fixed bottom-4 right-4 z-50 flex w-80 flex-col gap-2">
        <template x-for="toast in $store.toasts.items" :key="toast.id">
            <div x-transition
                 class="pointer-events-auto flex items-start gap-3 rounded-lg border bg-white px-4 py-3 shadow-lg dark:bg-slate-900"
                 :class="{
                    'border-emerald-300': toast.type === 'success',
                    'border-rose-300': toast.type === 'error',
                    'border-sky-300': toast.type === 'info',
                 }">
                <p class="flex-1 text-sm" x-text="toast.message"></p>
                <button type="button" @click="$store.toasts.remove(toast.id)"
                        class="text-slate-400 hover:text-slate-600" aria-label="{{ __('Dismiss') }}">&times;</button>
            </div>
        </template>
    </div>

    @stack('scripts')
</body>
</html>
