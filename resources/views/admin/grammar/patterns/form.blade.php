@extends('layouts.app')
@section('title', $pattern->exists ? __('Edit pola') : __('Pola baru'))

@section('content')
    <x-page-header
        :title="$pattern->exists ? __('Edit pola') : __('Pola baru')"
        :back="route('admin.grammar.patterns.index')"
        :back-label="__('Kembali ke pola')"
    />

    <form method="POST"
          action="{{ $pattern->exists ? route('admin.grammar.patterns.update', $pattern) : route('admin.grammar.patterns.store') }}"
          class="grid gap-6 lg:grid-cols-3"
          x-data="{
              items: @js($items->map(fn ($item) => [
                  'id' => $item->id,
                  'type' => $item->type,
                  'sentence' => $item->sentence,
                  'romaji' => $item->romaji,
                  'translation' => $item->translation,
                  'correction' => $item->correction,
                  'correction_romaji' => $item->correction_romaji,
                  'note' => $item->note,
              ])->values()),
              addItem(type) {
                  this.items.push({ id: null, type, sentence: '', romaji: '', translation: '', correction: '', correction_romaji: '', note: '' });
              },
              removeItem(index) {
                  this.items.splice(index, 1);
              },
          }">
        @csrf
        @if ($pattern->exists) @method('PUT') @endif

        <div class="space-y-6 lg:col-span-2">
            <div class="card p-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="grammar_category_id" class="label">{{ __('Kategori') }} <span class="text-rose-500">*</span></label>
                        <select id="grammar_category_id" name="grammar_category_id" required class="input">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('grammar_category_id', $pattern->grammar_category_id) == $category->id)>
                                    {{ $category->level?->name }} — {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('grammar_category_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="slug" class="label">{{ __('Slug') }}</label>
                        <input id="slug" name="slug" value="{{ old('slug', $pattern->slug) }}" class="input font-mono text-sm">
                    </div>

                    <div>
                        <label for="title" class="label">{{ __('Judul pola') }} <span class="text-rose-500">*</span></label>
                        <input id="title" name="title" value="{{ old('title', $pattern->title) }}" required
                               class="input @error('title') border-rose-400 @enderror" placeholder="～ながら">
                        @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="title_romaji" class="label">{{ __('Romaji judul (untuk Jepang)') }}</label>
                        <input id="title_romaji" name="title_romaji" value="{{ old('title_romaji', $pattern->title_romaji) }}"
                               class="input" placeholder="~nagara">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="formula" class="label">{{ __('Rumus') }}</label>
                        <input id="formula" name="formula" value="{{ old('formula', $pattern->formula) }}" class="input font-mono text-sm" placeholder="V(ます形) + ながら">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="explanation" class="label">{{ __('Penjelasan') }} <span class="text-rose-500">*</span></label>
                        <textarea id="explanation" name="explanation" rows="5" required
                                  class="input @error('explanation') border-rose-400 @enderror">{{ old('explanation', $pattern->explanation) }}</textarea>
                        @error('explanation') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-semibold">{{ __('Contoh & Kesalahan Umum') }}</h2>
                    <div class="flex gap-2">
                        <button type="button" @click="addItem('example')" class="btn-secondary text-sm">
                            <x-icon name="plus" class="size-4" /> {{ __('Tambah Contoh') }}
                        </button>
                        <button type="button" @click="addItem('mistake')" class="btn-secondary text-sm">
                            <x-icon name="plus" class="size-4" /> {{ __('Tambah Kesalahan') }}
                        </button>
                    </div>
                </div>

                <p x-show="items.length === 0" x-cloak class="rounded-lg border border-dashed border-slate-300 p-8 text-center text-sm text-slate-400 dark:border-slate-700">
                    {{ __('Belum ada contoh atau kesalahan umum.') }}
                </p>

                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="rounded-lg border border-slate-200 p-3 dark:border-slate-700">
                            <div class="mb-2 flex items-center justify-between">
                                <span class="badge" :class="item.type === 'mistake' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/15' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15'"
                                      x-text="item.type === 'mistake' ? '{{ __('Kesalahan') }}' : '{{ __('Contoh') }}'"></span>
                                <button type="button" @click="removeItem(index)" class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Hapus') }}</button>
                            </div>

                            <input type="hidden" :name="`items[${index}][id]`" :value="item.id">
                            <input type="hidden" :name="`items[${index}][type]`" :value="item.type">

                            <div class="grid gap-2 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label class="label text-xs" x-text="item.type === 'mistake' ? '{{ __('Kalimat salah') }}' : '{{ __('Kalimat Jepang') }}'"></label>
                                    <input type="text" :name="`items[${index}][sentence]`" x-model="item.sentence" class="input text-sm">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="label text-xs">{{ __('Romaji (untuk Jepang)') }}</label>
                                    <input type="text" :name="`items[${index}][romaji]`" x-model="item.romaji" class="input text-sm">
                                </div>
                                <div>
                                    <label class="label text-xs">{{ __('Terjemahan') }}</label>
                                    <input type="text" :name="`items[${index}][translation]`" x-model="item.translation" class="input text-sm">
                                </div>
                                <div x-show="item.type === 'mistake'">
                                    <label class="label text-xs">{{ __('Kalimat benar') }}</label>
                                    <input type="text" :name="`items[${index}][correction]`" x-model="item.correction" class="input text-sm">
                                </div>
                                <div class="sm:col-span-2" x-show="item.type === 'mistake'">
                                    <label class="label text-xs">{{ __('Romaji kalimat benar') }}</label>
                                    <input type="text" :name="`items[${index}][correction_romaji]`" x-model="item.correction_romaji" class="input text-sm">
                                </div>
                                <div class="sm:col-span-2" x-show="item.type === 'mistake'">
                                    <label class="label text-xs">{{ __('Kenapa salah') }}</label>
                                    <input type="text" :name="`items[${index}][note]`" x-model="item.note" class="input text-sm">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <div class="space-y-3">
                    <div>
                        <label for="sort_order" class="label">{{ __('Urutan') }}</label>
                        <input id="sort_order" name="sort_order" type="number" min="0"
                               value="{{ old('sort_order', $pattern->sort_order ?? 0) }}" class="input">
                    </div>

                    <label class="flex items-center gap-2.5 text-sm">
                        <input type="hidden" name="is_published" value="0">
                        <input type="checkbox" name="is_published" value="1"
                               @checked(old('is_published', $pattern->is_published ?? false))
                               class="rounded border-slate-300 text-brand-600">
                        {{ __('Terbitkan') }}
                    </label>
                </div>

                <div class="mt-5 flex flex-col gap-2">
                    <button type="submit" class="btn-primary">{{ $pattern->exists ? __('Simpan') : __('Buat pola') }}</button>
                    <a href="{{ route('admin.grammar.patterns.index') }}" class="btn-secondary text-center">{{ __('Batal') }}</a>
                </div>
            </div>
        </div>
    </form>
@endsection
