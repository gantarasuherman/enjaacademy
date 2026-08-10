@extends('layouts.app')
@section('title', $category->exists ? __('Edit kategori') : __('Kategori baru'))

@section('content')
    <x-page-header
        :title="$category->exists ? __('Edit kategori') : __('Kategori baru')"
        :back="route('admin.grammar.categories.index')"
        :back-label="__('Kembali ke kategori')"
    />

    <form method="POST"
          action="{{ $category->exists ? route('admin.grammar.categories.update', $category) : route('admin.grammar.categories.store') }}"
          class="max-w-2xl space-y-6">
        @csrf
        @if ($category->exists) @method('PUT') @endif

        <div class="card p-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="grammar_level_id" class="label">{{ __('Level') }} <span class="text-rose-500">*</span></label>
                    <select id="grammar_level_id" name="grammar_level_id" required class="input">
                        @foreach ($levels as $level)
                            <option value="{{ $level->id }}" @selected(old('grammar_level_id', $category->grammar_level_id) == $level->id)>
                                {{ $level->name }} — {{ $level->language === 'english' ? __('Inggris') : __('Jepang') }} · {{ $level->track === 'structure' ? __('Struktur') : __('Grammar') }}
                            </option>
                        @endforeach
                    </select>
                    @error('grammar_level_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="parent_id" class="label">{{ __('Induk (opsional — kosongkan untuk kategori utama)') }}</label>
                    <select id="parent_id" name="parent_id" class="input">
                        <option value="">{{ __('— Tidak ada, ini kategori utama —') }}</option>
                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id) == $parent->id)>{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="name" class="label">{{ __('Nama kategori') }} <span class="text-rose-500">*</span></label>
                    <input id="name" name="name" value="{{ old('name', $category->name) }}" required
                           class="input @error('name') border-rose-400 @enderror" placeholder="Partikel Dasar">
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="slug" class="label">{{ __('Slug') }}</label>
                    <input id="slug" name="slug" value="{{ old('slug', $category->slug) }}" class="input" placeholder="partikel-dasar">
                </div>

                <div>
                    <label for="sort_order" class="label">{{ __('Urutan') }}</label>
                    <input id="sort_order" name="sort_order" type="number" min="0"
                           value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="input">
                </div>

                <div class="flex items-end pb-2">
                    <label class="flex items-center gap-2.5 text-sm">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                               @checked(old('is_active', $category->is_active ?? true))
                               class="rounded border-slate-300 text-brand-600">
                        {{ __('Aktif') }}
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.grammar.categories.index') }}" class="btn-secondary">{{ __('Batal') }}</a>
            <button type="submit" class="btn-primary">{{ $category->exists ? __('Simpan') : __('Buat kategori') }}</button>
        </div>
    </form>
@endsection
