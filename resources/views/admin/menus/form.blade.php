@extends('layouts.app')
@section('title', $menu->exists ? __('Edit menu') : __('New menu'))

@section('content')
    <x-page-header
        :title="$menu->exists ? __('Edit menu') : __('New menu')"
        :description="__('A menu points at a named route (preferred) or a raw URL, and is shown only to users holding its permission.')"
        :back="route('admin.menus.index')"
        :back-label="__('Back to menus')"
    />

    <form method="POST"
          action="{{ $menu->exists ? route('admin.menus.update', $menu) : route('admin.menus.store') }}"
          class="grid gap-6 lg:grid-cols-3">
        @csrf
        @if ($menu->exists) @method('PUT') @endif

        <div class="space-y-6 lg:col-span-2">
            {{-- Identity --}}
            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Identity') }}</h2>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="title" class="label">{{ __('Title') }} <span class="text-rose-500">*</span></label>
                        <input id="title" name="title" value="{{ old('title', $menu->title) }}" required
                               class="input @error('title') border-rose-400 @enderror">
                        @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="slug" class="label">{{ __('Slug') }}</label>
                        <input id="slug" name="slug" value="{{ old('slug', $menu->slug) }}"
                               placeholder="{{ __('auto from title') }}" class="input">
                        <p class="help">{{ __('Must be unique. Leave empty to generate it.') }}</p>
                    </div>

                    {{-- Icon picker --}}
                    <div x-data="iconPicker({ initial: @js(old('icon', $menu->icon)) })">
                        <label class="label">{{ __('Icon') }}</label>

                        <div class="relative">
                            <input type="hidden" name="icon" :value="value">

                            <button type="button" @click="open = !open"
                                    class="input flex items-center gap-2 text-left">
                                <template x-if="value">
                                    <span class="font-mono text-xs" x-text="value"></span>
                                </template>
                                <template x-if="!value">
                                    <span class="text-slate-400">{{ __('Choose an icon') }}</span>
                                </template>
                                <span class="ml-auto text-slate-400">▾</span>
                            </button>

                            <div x-show="open" x-cloak @click.outside="open = false"
                                 class="absolute z-20 mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 shadow-lg dark:border-slate-700 dark:bg-slate-900">
                                <input type="search" x-model="search" placeholder="{{ __('Filter…') }}"
                                       class="input mb-2 text-sm">

                                <p class="mb-1 text-[11px] font-bold uppercase tracking-wide text-slate-400">{{ __('Icons') }}</p>
                                <div class="grid max-h-40 grid-cols-6 gap-1 overflow-y-auto">
                                    <template x-for="icon in filtered" :key="icon">
                                        <button type="button" @click="select(icon)"
                                                class="grid aspect-square place-items-center rounded border border-slate-200 text-[9px] hover:border-brand-400 dark:border-slate-700"
                                                :class="value === icon && 'border-brand-600 bg-brand-50 dark:bg-brand-500/15'"
                                                :title="icon"
                                                x-text="icon.slice(0, 4)"></button>
                                    </template>
                                </div>

                                <p class="mb-1 mt-3 text-[11px] font-bold uppercase tracking-wide text-slate-400">{{ __('Characters & emoji') }}</p>
                                <div class="grid grid-cols-7 gap-1">
                                    <template x-for="char in emoji" :key="char">
                                        <button type="button" @click="select(char)"
                                                class="grid aspect-square place-items-center rounded border border-slate-200 text-base hover:border-brand-400 dark:border-slate-700"
                                                :class="value === char && 'border-brand-600 bg-brand-50 dark:bg-brand-500/15'"
                                                x-text="char"></button>
                                    </template>
                                </div>

                                <button type="button" @click="clear(); open = false"
                                        class="mt-3 w-full rounded border border-slate-200 py-1.5 text-xs text-slate-500 hover:bg-slate-50 dark:border-slate-700">
                                    {{ __('No icon') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="label">{{ __('Description') }}</label>
                        <input id="description" name="description" value="{{ old('description', $menu->description) }}"
                               class="input" placeholder="{{ __('Shown as a tooltip in the sidebar') }}">
                    </div>
                </div>
            </div>

            {{-- Destination --}}
            <div class="card p-5">
                <h2 class="mb-1 font-semibold">{{ __('Destination') }}</h2>
                <p class="mb-4 text-xs text-slate-500">
                    {{ __('Prefer a route name — it survives URL changes. Leave both empty to make this a parent-only branch.') }}
                </p>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="route_name" class="label">{{ __('Route name') }}</label>
                        <input id="route_name" name="route_name" list="route-names"
                               value="{{ old('route_name', $menu->route_name) }}"
                               class="input font-mono text-sm @error('route_name') border-rose-400 @enderror"
                               placeholder="admin.users.index">
                        <datalist id="route-names">
                            @foreach ($routeNames as $name)
                                <option value="{{ $name }}"></option>
                            @endforeach
                        </datalist>
                        @error('route_name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        <p class="help">{{ __(':count routes available.', ['count' => count($routeNames)]) }}</p>
                    </div>

                    <div>
                        <label for="route_params" class="label">{{ __('Route parameters') }}</label>
                        <input id="route_params" name="route_params" value="{{ old('route_params', $menu->route_params) }}"
                               class="input font-mono text-sm" placeholder='module=hiragana'>
                        <p class="help">{{ __('key=value, comma separated — or JSON.') }}</p>
                    </div>

                    <div>
                        <label for="url" class="label">{{ __('URL') }}</label>
                        <input id="url" name="url" value="{{ old('url', $menu->url) }}"
                               class="input @error('url') border-rose-400 @enderror" placeholder="https://…">
                        @error('url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="type" class="label">{{ __('Type') }}</label>
                        <select id="type" name="type" class="input">
                            @foreach ($types as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', $menu->type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="target" class="label">{{ __('Open in') }}</label>
                        <select id="target" name="target" class="input">
                            @foreach ($targets as $value => $label)
                                <option value="{{ $value }}" @selected(old('target', $menu->target) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Access --}}
            <div class="card p-5">
                <h2 class="mb-1 font-semibold">{{ __('Who can see this') }}</h2>
                <p class="mb-4 text-xs text-slate-500">
                    {{ __('Permission and roles are combined with AND. Leave both empty and every signed-in user sees it.') }}
                </p>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="permission_name" class="label">{{ __('Required permission') }}</label>
                        <select id="permission_name" name="permission_name" class="input">
                            <option value="">{{ __('— everyone —') }}</option>
                            @foreach ($permissions as $name)
                                <option value="{{ $name }}" @selected(old('permission_name', $menu->permission_name) === $name)>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('permission_name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="role_default" class="label">{{ __('Default role hint') }}</label>
                        <select id="role_default" name="role_default" class="input">
                            <option value="">{{ __('— none —') }}</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" @selected(old('role_default', $menu->role_default) === $role->name)>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <span class="label">{{ __('Restrict to roles (menu access matrix)') }}</span>
                        <div class="grid gap-2 rounded-lg border border-slate-200 p-3 sm:grid-cols-3 dark:border-slate-700">
                            @foreach ($roles as $role)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                           @checked(in_array($role->id, old('roles', $selectedRoles), true))
                                           class="rounded border-slate-300 text-brand-600">
                                    {{ $role->name }}
                                </label>
                            @endforeach
                        </div>
                        <p class="help">{{ __('Leave all unticked to let the permission alone decide.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar column --}}
        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Placement') }}</h2>

                <div class="space-y-4">
                    <div>
                        <label for="parent_id" class="label">{{ __('Parent menu') }}</label>
                        <select id="parent_id" name="parent_id" class="input">
                            <option value="">{{ __('— top level —') }}</option>
                            @foreach ($parents as $parent)
                                <option value="{{ $parent->id }}" @selected((int) old('parent_id', $menu->parent_id) === $parent->id)>
                                    {{ $parent->indented_title }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="position" class="label">{{ __('Position') }}</label>
                        <select id="position" name="position" class="input">
                            @foreach ($positions as $value => $label)
                                <option value="{{ $value }}" @selected(old('position', $menu->position) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="sort_order" class="label">{{ __('Sort order') }}</label>
                        <input id="sort_order" name="sort_order" type="number" min="0"
                               value="{{ old('sort_order', $menu->sort_order) }}" class="input">
                        <p class="help">{{ __('Leave empty to append at the end. Drag & drop also updates this.') }}</p>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Badge') }}</h2>

                <div class="space-y-4">
                    <div>
                        <label for="badge" class="label">{{ __('Badge text') }}</label>
                        <input id="badge" name="badge" value="{{ old('badge', $menu->badge) }}"
                               class="input" placeholder="{{ __('New') }}">
                    </div>

                    <div>
                        <label for="badge_color" class="label">{{ __('Badge colour') }}</label>
                        <select id="badge_color" name="badge_color" class="input">
                            <option value="">{{ __('— default —') }}</option>
                            @foreach ($badgeColors as $color)
                                <option value="{{ $color }}" @selected(old('badge_color', $menu->badge_color) === $color)>{{ $color }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Visibility') }}</h2>

                <div class="space-y-3">
                    @foreach ([
                        'is_active' => __('Active'),
                        'is_visible' => __('Visible in navigation'),
                        'is_sidebar' => __('Show in sidebar'),
                        'is_topbar' => __('Show in topbar'),
                        'is_footer' => __('Show in footer'),
                    ] as $field => $label)
                        <label class="flex items-center gap-2.5 text-sm">
                            <input type="hidden" name="{{ $field }}" value="0">
                            <input type="checkbox" name="{{ $field }}" value="1"
                                   @checked(old($field, $menu->{$field} ?? false))
                                   class="rounded border-slate-300 text-brand-600">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1">
                    {{ $menu->exists ? __('Save changes') : __('Create menu') }}
                </button>
                <a href="{{ route('admin.menus.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
            </div>
        </div>
    </form>
@endsection
