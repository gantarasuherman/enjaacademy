@props(['type' => 'info'])

@php
    $styles = [
        'success' => 'bg-emerald-50 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-300',
        'error' => 'bg-rose-50 text-rose-800 dark:bg-rose-500/10 dark:text-rose-300',
        'warning' => 'bg-amber-50 text-amber-800 dark:bg-amber-500/10 dark:text-amber-300',
        'info' => 'bg-sky-50 text-sky-800 dark:bg-sky-500/10 dark:text-sky-300',
    ];
@endphp

<div role="alert" {{ $attributes->merge(['class' => 'rounded-lg px-4 py-3 text-sm '.($styles[$type] ?? $styles['info'])]) }}>
    {{ $slot }}
</div>
