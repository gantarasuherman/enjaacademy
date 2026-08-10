@extends('layouts.app')
@section('title', $level->exists ? __('Edit level') : __('Level baru'))

@section('content')
    <x-page-header
        :title="$level->exists ? __('Edit level') : __('Level baru')"
        :back="route('admin.grammar.levels.index')"
        :back-label="__('Kembali ke level')"
    />

    <form method="POST"
          action="{{ $level->exists ? route('admin.grammar.levels.update', $level) : route('admin.grammar.levels.store') }}"
          class="max-w-2xl space-y-6">
        @csrf
        @if ($level->exists) @method('PUT') @endif

        <div class="card p-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="name" class="label">{{ __('Nama level') }} <span class="text-rose-500">*</span></label>
                    <input id="name" name="name" value="{{ old('name', $level->name) }}" required
                           class="input @error('name') border-rose-400 @enderror" placeholder="N5">
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="slug" class="label">{{ __('Slug') }}</label>
                    <input id="slug" name="slug" value="{{ old('slug', $level->slug) }}" class="input" placeholder="n5">
                </div>

                <div>
                    <label for="language" class="label">{{ __('Bahasa') }} <span class="text-rose-500">*</span></label>
                    <select id="language" name="language" required class="input">
                        <option value="japanese" @selected(old('language', $level->language ?? 'japanese') === 'japanese')>{{ __('Bahasa Jepang') }}</option>
                        <option value="english" @selected(old('language', $level->language ?? 'japanese') === 'english')>{{ __('Bahasa Inggris') }}</option>
                    </select>
                    @error('language') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="track" class="label">{{ __('Jenis materi') }} <span class="text-rose-500">*</span></label>
                    <select id="track" name="track" required class="input">
                        <option value="grammar" @selected(old('track', $level->track ?? 'grammar') === 'grammar')>{{ __('Grammar') }}</option>
                        <option value="structure" @selected(old('track', $level->track ?? 'grammar') === 'structure')>{{ __('Struktur kalimat') }}</option>
                    </select>
                    @error('track') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="color" class="label">{{ __('Warna badge') }}</label>
                    <select id="color" name="color" class="input">
                        @foreach (config('admin.menu.badge_colors') as $color)
                            <option value="{{ $color }}" @selected(old('color', $level->color ?? 'indigo') === $color)>{{ $color }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="sort_order" class="label">{{ __('Urutan') }}</label>
                    <input id="sort_order" name="sort_order" type="number" min="0"
                           value="{{ old('sort_order', $level->sort_order ?? 0) }}" class="input">
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="label">{{ __('Deskripsi') }}</label>
                    <textarea id="description" name="description" rows="3" class="input">{{ old('description', $level->description) }}</textarea>
                </div>

                <div class="flex items-end pb-2">
                    <label class="flex items-center gap-2.5 text-sm">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                               @checked(old('is_active', $level->is_active ?? true))
                               class="rounded border-slate-300 text-brand-600">
                        {{ __('Aktif') }}
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.grammar.levels.index') }}" class="btn-secondary">{{ __('Batal') }}</a>
            <button type="submit" class="btn-primary">{{ $level->exists ? __('Simpan') : __('Buat level') }}</button>
        </div>
    </form>
@endsection
