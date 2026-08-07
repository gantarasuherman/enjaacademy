@extends('layouts.app')
@section('title', $language->exists ? __('Edit bahasa') : __('Bahasa baru'))

@section('content')
    <x-page-header
        :title="$language->exists ? __('Edit bahasa') : __('Bahasa baru')"
        :back="route('admin.languages.index')"
        :back-label="__('Kembali ke bahasa')"
    />

    <form method="POST"
          action="{{ $language->exists ? route('admin.languages.update', $language) : route('admin.languages.store') }}"
          class="max-w-2xl space-y-6">
        @csrf
        @if ($language->exists) @method('PUT') @endif

        <div class="card p-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="label">{{ __('Nama') }} <span class="text-rose-500">*</span></label>
                    <input id="name" name="name" value="{{ old('name', $language->name) }}" required
                           class="input @error('name') border-rose-400 @enderror" placeholder="Bahasa Jepang">
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="slug" class="label">{{ __('Slug') }}</label>
                    <input id="slug" name="slug" value="{{ old('slug', $language->slug) }}" class="input" placeholder="japanese">
                </div>

                <div>
                    <label for="code" class="label">{{ __('Kode ISO') }} <span class="text-rose-500">*</span></label>
                    <input id="code" name="code" value="{{ old('code', $language->code) }}" required
                           class="input font-mono" placeholder="ja" maxlength="10">
                    @error('code') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="flag" class="label">{{ __('Emoji bendera') }}</label>
                    <input id="flag" name="flag" value="{{ old('flag', $language->flag) }}" class="input" placeholder="🇯🇵" maxlength="10">
                </div>

                <div>
                    <label for="color" class="label">{{ __('Warna') }}</label>
                    <select id="color" name="color" class="input">
                        @foreach (config('admin.menu.badge_colors') as $color)
                            <option value="{{ $color }}" @selected(old('color', $language->color ?? 'indigo') === $color)>{{ $color }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="label">{{ __('Deskripsi') }}</label>
                    <textarea id="description" name="description" rows="3" class="input">{{ old('description', $language->description) }}</textarea>
                </div>

                <div>
                    <label for="sort_order" class="label">{{ __('Urutan') }}</label>
                    <input id="sort_order" name="sort_order" type="number" min="0"
                           value="{{ old('sort_order', $language->sort_order ?? 0) }}" class="input">
                </div>

                <div class="flex items-end pb-2">
                    <label class="flex items-center gap-2.5 text-sm">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                               @checked(old('is_active', $language->is_active ?? true))
                               class="rounded border-slate-300 text-brand-600">
                        {{ __('Aktif') }}
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.languages.index') }}" class="btn-secondary">{{ __('Batal') }}</a>
            <button type="submit" class="btn-primary">{{ $language->exists ? __('Simpan') : __('Buat bahasa') }}</button>
        </div>
    </form>
@endsection
