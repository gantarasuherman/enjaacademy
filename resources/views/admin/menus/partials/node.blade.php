{{-- One draggable node. Recurses for children, so nesting is data-driven. --}}

<li data-menu-id="{{ $menu->id }}" class="menu-node">
    <div class="menu-node-row flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2.5 dark:border-slate-800 dark:bg-slate-900">
        @can('menus.update')
            <button type="button" data-menu-handle
                    class="cursor-grab text-slate-400 transition hover:text-slate-600 active:cursor-grabbing"
                    aria-label="{{ __('Drag to reorder') }}">
                <svg class="size-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M7 4a1 1 0 100 2 1 1 0 000-2zm0 5a1 1 0 100 2 1 1 0 000-2zm0 5a1 1 0 100 2 1 1 0 000-2zm6-10a1 1 0 100 2 1 1 0 000-2zm0 5a1 1 0 100 2 1 1 0 000-2zm0 5a1 1 0 100 2 1 1 0 000-2z" />
                </svg>
            </button>
        @endcan

        <x-icon :name="$menu->icon" class="size-4 shrink-0 text-slate-400" />

        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <span class="truncate text-sm font-medium">{{ $menu->title }}</span>

                @if ($menu->type !== 'menu')
                    <span class="badge bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $menu->type }}</span>
                @endif

                @if ($menu->badge)
                    <span class="badge bg-{{ $menu->badge_color ?? 'brand' }}-100 text-{{ $menu->badge_color ?? 'brand' }}-700">{{ $menu->badge }}</span>
                @endif

                @unless ($menu->is_active && $menu->is_visible)
                    <span class="badge bg-slate-200 text-slate-600 dark:bg-slate-700">{{ __('hidden') }}</span>
                @endunless
            </div>

            <div class="mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-slate-500">
                @if ($menu->route_name)
                    <span class="font-mono">{{ $menu->route_name }}</span>
                @elseif ($menu->url)
                    <span class="font-mono">{{ Str::limit($menu->url, 36) }}</span>
                @endif

                @if ($menu->permission_name)
                    <span class="text-amber-600 dark:text-amber-400">🔒 {{ $menu->permission_name }}</span>
                @endif

                @if ($menu->roles->isNotEmpty())
                    <span>{{ __('roles') }}: {{ $menu->roles->pluck('name')->implode(', ') }}</span>
                @endif
            </div>
        </div>

        <div class="flex shrink-0 gap-1">
            @can('menus.create')
                <form method="POST" action="{{ route('admin.menus.duplicate', $menu) }}">
                    @csrf
                    <button type="submit" class="btn-ghost px-2 py-1 text-xs" title="{{ __('Duplicate this branch') }}">
                        {{ __('Copy') }}
                    </button>
                </form>
            @endcan

            @can('menus.update')
                <a href="{{ route('admin.menus.edit', $menu) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Edit') }}</a>
            @endcan

            @can('menus.delete')
                <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}"
                      onsubmit="return confirm('{{ __('Delete this menu and all of its children?') }}')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Delete') }}</button>
                </form>
            @endcan
        </div>
    </div>

    {{-- Children list is always rendered so an empty branch is still a drop target. --}}
    <ul data-menu-list data-depth="{{ $depth + 1 }}"
        class="ml-6 mt-1.5 space-y-1.5 border-l-2 border-dashed border-slate-200 pl-4 dark:border-slate-800
               {{ $menu->children->isEmpty() ? 'min-h-3' : '' }}">
        @foreach ($menu->children as $child)
            @include('admin.menus.partials.node', ['menu' => $child, 'depth' => $depth + 1])
        @endforeach
    </ul>
</li>
