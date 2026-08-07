@props(['title', 'description' => null, 'back' => null, 'backLabel' => null])

<div class="mb-6">
    @if ($back)
        <a href="{{ $back }}" class="mb-3 inline-flex items-center gap-1 text-sm font-medium text-slate-500 transition hover:text-brand-600">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            {{ $backLabel ?? __('Back') }}
        </a>
    @endif

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0">
            <h1 class="text-2xl font-bold tracking-tight">{{ $title }}</h1>
            @if ($description)
                <p class="mt-1.5 max-w-2xl text-sm text-slate-500 dark:text-slate-400">{{ $description }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="flex shrink-0 flex-wrap gap-2">{{ $actions }}</div>
        @endisset
    </div>
</div>
