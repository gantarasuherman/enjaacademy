@props(['item', 'depth' => 0])

{{--
    One node of the dynamic sidebar. Recurses into itself for children, so the
    nesting depth is whatever the `menus` table says — never hardcoded.
--}}

@php
    $type = $item['type'];
    $children = $item['children'] ?? [];
    $hasChildren = ! empty($children);
    $url = $item['url'];

    // A branch is "open" when the current URL is inside it, so deep links land
    // with their parent already expanded.
    $isActive = $url !== '#' && (request()->fullUrlIs($url) || request()->is(trim(parse_url($url, PHP_URL_PATH) ?? '', '/')));
    $childActive = collect($children)->contains(
        fn ($child) => $child['url'] !== '#' && request()->is(trim(parse_url($child['url'], PHP_URL_PATH) ?? '', '/').'*')
    );
@endphp

@if ($type === 'divider')
    <li role="separator" class="my-2 border-t border-slate-200 dark:border-slate-800"></li>

@elseif ($type === 'header')
    <li class="px-3 pb-1 pt-4 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500"
        x-show="!$store.ui.sidebarCollapsed" x-cloak>
        {{ $item['title'] }}
    </li>

@elseif ($hasChildren)
    <li x-data="{ open: @js($childActive) }">
        <button
            type="button"
            @click="$store.ui.sidebarCollapsed ? ($store.ui.sidebarCollapsed = false, open = true) : (open = !open)"
            :aria-expanded="open"
            title="{{ $item['title'] }}"
            class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition
                   text-slate-600 hover:bg-slate-100 hover:text-slate-900
                   dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white"
            :style="$store.ui.sidebarCollapsed ? '' : 'padding-left: {{ 0.75 + $depth * 0.75 }}rem'"
        >
            <x-icon :name="$item['icon']" class="size-4.5 shrink-0" />
            <span class="flex-1 truncate text-left" x-show="!$store.ui.sidebarCollapsed" x-cloak>{{ $item['title'] }}</span>

            @if ($item['badge'])
                <span class="rounded-full bg-{{ $item['badge_color'] ?? 'brand' }}-100 px-1.5 py-0.5 text-[10px] font-bold text-{{ $item['badge_color'] ?? 'brand' }}-700"
                      x-show="!$store.ui.sidebarCollapsed" x-cloak>
                    {{ $item['badge'] }}
                </span>
            @endif

            <svg class="size-4 shrink-0 transition-transform duration-200" :class="open && 'rotate-90'"
                 x-show="!$store.ui.sidebarCollapsed" x-cloak
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <ul x-show="open && !$store.ui.sidebarCollapsed" x-collapse x-cloak class="mt-0.5 space-y-0.5">
            @foreach ($children as $child)
                <x-navigation.sidebar-item :item="$child" :depth="$depth + 1" />
            @endforeach
        </ul>
    </li>

@else
    <li>
        <a
            href="{{ $url }}"
            @if ($item['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif
            @if ($isActive) aria-current="page" @endif
            class="relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition
                   {{ $isActive
                       ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/15 dark:text-brand-300'
                       : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}"
            :style="$store.ui.sidebarCollapsed ? '' : 'padding-left: {{ 0.75 + $depth * 0.75 }}rem'"
            title="{{ $item['description'] ?? $item['title'] }}"
        >
            @if ($isActive)
                <span class="absolute left-0 h-6 w-1 rounded-r-full bg-brand-600"></span>
            @endif

            <x-icon :name="$item['icon']" class="size-4.5 shrink-0" />
            <span class="flex-1 truncate" x-show="!$store.ui.sidebarCollapsed" x-cloak>{{ $item['title'] }}</span>

            @if ($item['badge'])
                <span class="rounded-full bg-{{ $item['badge_color'] ?? 'brand' }}-100 px-1.5 py-0.5 text-[10px] font-bold text-{{ $item['badge_color'] ?? 'brand' }}-700 dark:bg-{{ $item['badge_color'] ?? 'brand' }}-500/20 dark:text-{{ $item['badge_color'] ?? 'brand' }}-300"
                      x-show="!$store.ui.sidebarCollapsed" x-cloak>
                    {{ $item['badge'] }}
                </span>
            @endif

            @if ($item['target'] === '_blank')
                <svg class="size-3 shrink-0 opacity-50" x-show="!$store.ui.sidebarCollapsed" x-cloak
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            @endif
        </a>
    </li>
@endif
