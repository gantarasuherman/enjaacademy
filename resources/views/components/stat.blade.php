@props(['label', 'value', 'hint' => null, 'icon' => null, 'tone' => 'brand'])

@php
    $tones = [
        'brand' => 'bg-brand-100 text-brand-700 dark:bg-brand-500/20 dark:text-brand-300',
        'emerald' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
        'amber' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
        'rose' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300',
        'sky' => 'bg-sky-100 text-sky-700 dark:bg-sky-500/20 dark:text-sky-300',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'card flex items-center gap-4 p-5']) }}>
    @if ($icon)
        <span class="grid size-11 shrink-0 place-items-center rounded-lg {{ $tones[$tone] ?? $tones['brand'] }}">
            <x-icon :name="$icon" class="size-5" />
        </span>
    @endif

    <div class="min-w-0">
        <p class="truncate text-xs font-medium text-slate-500">{{ $label }}</p>
        <p class="text-xl font-bold leading-tight">{{ $value }}</p>
        @if ($hint)
            <p class="truncate text-[11px] text-slate-400">{{ $hint }}</p>
        @endif
    </div>
</div>
