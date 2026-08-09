{{--
    Custom pagination view, matching the admin panel's own design tokens
    (slate borders/text, brand-colored current page, class-based dark mode)
    instead of Laravel's stock gray/blue Tailwind defaults.
--}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500 dark:text-slate-400">
            {{ __('Menampilkan') }}
            @if ($paginator->firstItem())
                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $paginator->firstItem() }}</span>
                {{ __('–') }}
                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            {{ __('dari') }}
            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $paginator->total() }}</span>
        </p>

        <div class="flex flex-wrap items-center gap-1">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="inline-flex size-8 items-center justify-center rounded-lg border border-slate-200 text-slate-300 dark:border-slate-800 dark:text-slate-700">
                    <span aria-hidden="true">‹</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}"
                   class="inline-flex size-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">
                    <span aria-hidden="true">‹</span>
                </a>
            @endif

            {{-- Page numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span aria-disabled="true" class="inline-flex size-8 items-center justify-center text-sm text-slate-400 dark:text-slate-600">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex size-8 items-center justify-center rounded-lg bg-brand-600 text-sm font-semibold text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                               class="inline-flex size-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm text-slate-600 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}"
                   class="inline-flex size-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">
                    <span aria-hidden="true">›</span>
                </a>
            @else
                <span aria-disabled="true" class="inline-flex size-8 items-center justify-center rounded-lg border border-slate-200 text-slate-300 dark:border-slate-800 dark:text-slate-700">
                    <span aria-hidden="true">›</span>
                </span>
            @endif
        </div>
    </nav>
@endif
