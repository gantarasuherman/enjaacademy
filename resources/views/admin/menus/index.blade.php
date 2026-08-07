@extends('layouts.app')
@section('title', __('Menu Management'))

@section('content')
    <x-page-header
        :title="__('Menu Management')"
        :description="__('Every entry in the sidebar, topbar and footer lives here. Drag to reorder, nest to create sub-menus — no code changes needed.')"
    >
        <x-slot:actions>
            @can('menus.update')
                <form method="POST" action="{{ route('admin.menus.clear-cache') }}">
                    @csrf
                    <button type="submit" class="btn-secondary">{{ __('Clear cache') }}</button>
                </form>
            @endcan

            @can('menus.view')
                <a href="{{ route('admin.menus.matrix') }}" class="btn-secondary">{{ __('Access matrix') }}</a>
            @endcan

            @can('menus.create')
                <a href="{{ route('admin.menus.create') }}" class="btn-primary">
                    <x-icon name="plus" class="size-4" /> {{ __('New menu') }}
                </a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    {{-- Position tabs --}}
    <div class="mb-5 flex flex-wrap gap-2">
        @foreach (['' => __('All positions')] + $positions as $key => $label)
            <a href="{{ route('admin.menus.index', array_filter(['position' => $key])) }}"
               class="rounded-full border px-3 py-1.5 text-sm font-medium transition
                      {{ $position === ($key ?: null)
                          ? 'border-brand-600 bg-brand-600 text-white'
                          : 'border-slate-300 bg-white text-slate-600 hover:border-brand-400 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if ($tree->isEmpty())
        <div class="card p-12 text-center">
            <p class="font-semibold">{{ __('No menus yet') }}</p>
            <p class="mt-1 text-sm text-slate-500">{{ __('Create the first menu, or run `php artisan db:seed --class=MenuSeeder`.') }}</p>
            @can('menus.create')
                <a href="{{ route('admin.menus.create') }}" class="btn-primary mt-4">{{ __('Create menu') }}</a>
            @endcan
        </div>
    @else
        <div
            x-data="menuTree({ reorderUrl: '{{ route('admin.menus.reorder') }}', maxDepth: {{ config('admin.menu.max_depth') }} })"
            class="card"
        >
            <div class="card-header">
                <div>
                    <h2 class="font-semibold">{{ __('Menu tree') }}</h2>
                    <p class="mt-0.5 text-xs text-slate-500">
                        {{ __('Drag the handle to reorder or nest. Changes are saved only when you press Save order.') }}
                    </p>
                </div>

                @can('menus.update')
                    <div class="flex gap-2" x-show="dirty" x-cloak>
                        <button type="button" @click="reset()" class="btn-secondary text-xs">{{ __('Discard') }}</button>
                        <button type="button" @click="save()" :disabled="saving" class="btn-primary text-xs">
                            <span x-show="!saving">{{ __('Save order') }}</span>
                            <span x-show="saving" x-cloak>{{ __('Saving…') }}</span>
                        </button>
                    </div>
                @endcan
            </div>

            <div class="p-4">
                <ul data-menu-list data-depth="0" class="space-y-1.5">
                    @foreach ($tree as $menu)
                        @include('admin.menus.partials.node', ['menu' => $menu, 'depth' => 0])
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Flat listing with search --}}
    <div class="card mt-6">
        <div class="card-header">
            <h2 class="font-semibold">{{ __('All menu entries') }}</h2>

            <form method="GET" class="flex gap-2">
                <input type="hidden" name="position" value="{{ request('position') }}">
                <input type="search" name="search" value="{{ request('search') }}"
                       placeholder="{{ __('Search title, route, permission…') }}"
                       class="input w-56 text-sm">
                <button type="submit" class="btn-secondary text-sm">{{ __('Search') }}</button>
            </form>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Target') }}</th>
                        <th>{{ __('Permission') }}</th>
                        <th>{{ __('Position') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($flat as $menu)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <x-icon :name="$menu->icon" class="size-4 shrink-0 text-slate-400" />
                                    <div class="min-w-0">
                                        <p class="truncate font-medium">{{ $menu->title }}</p>
                                        @if ($menu->parent)
                                            <p class="truncate text-xs text-slate-500">{{ __('under') }} {{ $menu->parent->title }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($menu->route_name)
                                    <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs dark:bg-slate-800">{{ $menu->route_name }}</code>
                                @elseif ($menu->url)
                                    <span class="text-xs text-slate-500">{{ Str::limit($menu->url, 40) }}</span>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($menu->permission_name)
                                    <code class="rounded bg-amber-50 px-1.5 py-0.5 text-xs text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">{{ $menu->permission_name }}</code>
                                @else
                                    <span class="text-xs text-slate-400">{{ __('everyone') }}</span>
                                @endif
                            </td>
                            <td><span class="badge bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $menu->position }}</span></td>
                            <td>
                                @if ($menu->is_active && $menu->is_visible)
                                    <span class="badge bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300">{{ __('Hidden') }}</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-sm text-slate-500">{{ __('No menus match your search.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($flat->hasPages())
            <div class="border-t border-slate-200 px-5 py-3 dark:border-slate-800">{{ $flat->links() }}</div>
        @endif
    </div>
@endsection
