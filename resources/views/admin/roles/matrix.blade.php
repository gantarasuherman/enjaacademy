@extends('layouts.app')
@section('title', __('Permission Matrix'))

@section('content')
    @php
        $superRole = $roles->firstWhere('name', $superRole);
        $allPermissionIds = $grouped->flatten(1)->pluck('id')->all();
        $roleIds = $roles->pluck('id')->all();
    @endphp

    <x-page-header
        :title="__('Permission Matrix')"
        :description="__('Grant or revoke every permission for every role in one grid. Changes apply only when you save.')"
        :back="route('admin.roles.index')"
        :back-label="__('Back to roles')"
    />

    <form method="POST" action="{{ route('admin.roles.update-matrix') }}"
          x-data="permissionMatrix({ initial: @js($assigned), superRoleId: {{ $superRole?->id ?? 'null' }} })">
        @csrf
        @method('PUT')

        {{-- Toolbar --}}
        <div class="card mb-4 flex flex-wrap items-center gap-3 p-4">
            <input type="search" x-model="search" placeholder="{{ __('Filter modules…') }}"
                   class="input w-56 text-sm">

            <button type="button" @click="toggleAll(@js($roleIds), @js($allPermissionIds))"
                    class="btn-secondary text-xs">{{ __('Toggle all') }}</button>

            <span class="text-xs text-slate-500">
                <span x-text="totalSelected"></span> {{ __('grants selected') }}
            </span>

            <div class="ml-auto flex items-center gap-3">
                <span x-show="changed" x-cloak class="text-xs font-medium text-amber-600">
                    {{ __('Unsaved changes') }}
                </span>
                <button type="submit" class="btn-primary text-sm">{{ __('Save matrix') }}</button>
            </div>
        </div>

        <x-alert type="info" class="mb-4">
            {{ __('The :role role is read-only here — it is granted everything by Gate::before, so it never falls behind when a new module adds permissions.', ['role' => config('admin.super_role')]) }}
        </x-alert>

        {{-- Matrix --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0 z-10 bg-slate-50 dark:bg-slate-800/80">
                        <tr>
                            <th class="sticky left-0 z-20 bg-slate-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-800">
                                {{ __('Permission') }}
                            </th>
                            @foreach ($roles as $role)
                                <th class="px-3 py-3 text-center">
                                    <div class="text-xs font-semibold">{{ $role->name }}</div>
                                    <div class="mt-0.5 text-[10px] font-normal text-slate-400">
                                        {{ $role->users_count ?? 0 }} {{ __('users') }}
                                    </div>
                                    @unless ($superRole && $role->id === $superRole->id)
                                        <button type="button"
                                                @click="toggleRow({{ $role->id }}, @js($allPermissionIds))"
                                                class="mt-1 text-[10px] text-brand-600 hover:underline">
                                            {{ __('all') }}
                                        </button>
                                    @endunless
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($grouped as $module => $permissions)
                            @php $moduleIds = $permissions->pluck('id')->all(); @endphp

                            {{-- Module group header --}}
                            <tr x-show="matches(@js($module))" class="bg-slate-50/60 dark:bg-slate-800/40">
                                <td class="sticky left-0 bg-slate-50 px-4 py-2 font-semibold dark:bg-slate-800/90">
                                    {{ $module }}
                                    <span class="ml-1 text-xs font-normal text-slate-400">({{ $permissions->count() }})</span>
                                </td>
                                @foreach ($roles as $role)
                                    <td class="px-3 py-2 text-center">
                                        @unless ($superRole && $role->id === $superRole->id)
                                            <button type="button"
                                                    @click="toggleRow({{ $role->id }}, @js($moduleIds))"
                                                    class="text-[10px] text-brand-600 hover:underline">
                                                {{ __('group') }}
                                            </button>
                                        @endunless
                                    </td>
                                @endforeach
                            </tr>

                            {{-- Individual permissions --}}
                            @foreach ($permissions as $permission)
                                <tr x-show="matches(@js($module.' '.$permission->name))"
                                    class="transition hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <td class="sticky left-0 z-10 bg-white px-4 py-2 dark:bg-slate-900">
                                        <code class="text-xs">{{ $permission->name }}</code>
                                    </td>

                                    @foreach ($roles as $role)
                                        @php $isSuper = $superRole && $role->id === $superRole->id; @endphp

                                        <td class="px-3 py-2 text-center">
                                            @if ($isSuper)
                                                <span class="text-emerald-500" title="{{ __('Granted by Gate::before') }}">✓</span>
                                            @else
                                                <input type="checkbox"
                                                       name="matrix[{{ $role->id }}][]"
                                                       value="{{ $permission->id }}"
                                                       :checked="isChecked({{ $role->id }}, {{ $permission->id }})"
                                                       @click.prevent="toggle({{ $role->id }}, {{ $permission->id }})"
                                                       class="size-4 cursor-pointer rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 flex justify-end gap-2">
            <a href="{{ route('admin.roles.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
            <button type="submit" class="btn-primary">{{ __('Save matrix') }}</button>
        </div>
    </form>
@endsection
