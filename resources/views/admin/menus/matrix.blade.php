@extends('layouts.app')
@section('title', __('Menu Access Matrix'))

@section('content')
    @php
        $superRoleModel = $roles->firstWhere('name', $superRole);
        $roleIds = $roles->pluck('id')->all();

        // Flatten the tree so the table can render one row per menu while
        // still showing the hierarchy through indentation.
        $flatten = function ($nodes, $depth = 0) use (&$flatten) {
            return collect($nodes)->flatMap(fn ($node) => collect([['menu' => $node, 'depth' => $depth]])
                ->merge($flatten($node->children, $depth + 1)));
        };

        $rows = $flatten($tree);
        $allMenuIds = $rows->pluck('menu.id')->all();
    @endphp

    <x-page-header
        :title="__('Menu Access Matrix')"
        :description="__('Restrict individual menus to specific roles. A menu with no role ticked falls back to its permission setting alone.')"
        :back="route('admin.menus.index')"
        :back-label="__('Back to menus')"
    />

    <form method="POST" action="{{ route('admin.menus.update-matrix') }}"
          x-data="permissionMatrix({ initial: @js($assigned), superRoleId: {{ $superRoleModel?->id ?? 'null' }} })">
        @csrf
        @method('PUT')

        <div class="card mb-4 flex flex-wrap items-center gap-3 p-4">
            <input type="search" x-model="search" placeholder="{{ __('Filter menus…') }}" class="input w-56 text-sm">

            <button type="button" @click="toggleAll(@js($roleIds), @js($allMenuIds))"
                    class="btn-secondary text-xs">{{ __('Toggle all') }}</button>

            <span class="text-xs text-slate-500">
                <span x-text="totalSelected"></span> {{ __('assignments') }}
            </span>

            <div class="ml-auto flex items-center gap-3">
                <span x-show="changed" x-cloak class="text-xs font-medium text-amber-600">{{ __('Unsaved changes') }}</span>
                <button type="submit" class="btn-primary text-sm">{{ __('Save matrix') }}</button>
            </div>
        </div>

        <x-alert type="info" class="mb-4">
            {{ __('The :role role sees every menu regardless of this grid.', ['role' => $superRole]) }}
        </x-alert>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0 z-10 bg-slate-50 dark:bg-slate-800/80">
                        <tr>
                            <th class="sticky left-0 z-20 bg-slate-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-800">
                                {{ __('Menu') }}
                            </th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                {{ __('Permission') }}
                            </th>
                            @foreach ($roles as $role)
                                <th class="px-3 py-3 text-center">
                                    <div class="text-xs font-semibold">{{ $role->name }}</div>
                                    @unless ($superRoleModel && $role->id === $superRoleModel->id)
                                        <button type="button" @click="toggleRow({{ $role->id }}, @js($allMenuIds))"
                                                class="mt-1 text-[10px] text-brand-600 hover:underline">{{ __('all') }}</button>
                                    @endunless
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($rows as $row)
                            @php
                                $menu = $row['menu'];
                                $depth = $row['depth'];
                            @endphp

                            <tr x-show="matches(@js($menu->title.' '.$menu->permission_name))"
                                class="transition hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td class="sticky left-0 z-10 bg-white px-4 py-2 dark:bg-slate-900">
                                    <div class="flex items-center gap-2" style="padding-left: {{ $depth * 1.25 }}rem">
                                        @if ($depth > 0)
                                            <span class="text-slate-300">└</span>
                                        @endif
                                        <x-icon :name="$menu->icon" class="size-4 shrink-0 text-slate-400" />
                                        <span class="truncate {{ $depth === 0 ? 'font-medium' : '' }}">{{ $menu->title }}</span>
                                        @if ($menu->type !== 'menu')
                                            <span class="badge bg-slate-100 text-slate-500 dark:bg-slate-800">{{ $menu->type }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-3 py-2">
                                    @if ($menu->permission_name)
                                        <code class="text-[11px] text-amber-600 dark:text-amber-400">{{ $menu->permission_name }}</code>
                                    @else
                                        <span class="text-[11px] text-slate-400">{{ __('everyone') }}</span>
                                    @endif
                                </td>

                                @foreach ($roles as $role)
                                    @php $isSuper = $superRoleModel && $role->id === $superRoleModel->id; @endphp

                                    <td class="px-3 py-2 text-center">
                                        @if ($isSuper)
                                            <span class="text-emerald-500" title="{{ __('Always visible') }}">✓</span>
                                        @else
                                            <input type="checkbox"
                                                   name="matrix[{{ $role->id }}][]"
                                                   value="{{ $menu->id }}"
                                                   :checked="isChecked({{ $role->id }}, {{ $menu->id }})"
                                                   @click.prevent="toggle({{ $role->id }}, {{ $menu->id }})"
                                                   class="size-4 cursor-pointer rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 flex justify-end gap-2">
            <a href="{{ route('admin.menus.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
            <button type="submit" class="btn-primary">{{ __('Save matrix') }}</button>
        </div>
    </form>
@endsection
