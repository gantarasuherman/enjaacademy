@extends('layouts.app')
@section('title', $module->exists ? __('Edit modul') : __('Modul baru'))

@section('content')
    <x-page-header
        :title="$module->exists ? __('Edit modul') : __('Modul baru')"
        :description="__('Modul menentukan sendiri permission-nya lewat prefix. Centang "generate permission" dan modul langsung siap dipakai tanpa menyentuh kode.')"
        :back="route('admin.modules.index')"
        :back-label="__('Kembali ke modul')"
    />

    <form method="POST"
          action="{{ $module->exists ? route('admin.modules.update', $module) : route('admin.modules.store') }}"
          class="grid gap-6 lg:grid-cols-3">
        @csrf
        @if ($module->exists) @method('PUT') @endif

        <div class="space-y-6 lg:col-span-2">
            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Identitas modul') }}</h2>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="label">{{ __('Nama') }} <span class="text-rose-500">*</span></label>
                        <input id="name" name="name" value="{{ old('name', $module->name) }}" required
                               class="input @error('name') border-rose-400 @enderror" placeholder="Kanji">
                        @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="slug" class="label">{{ __('Slug') }}</label>
                        <input id="slug" name="slug" value="{{ old('slug', $module->slug) }}" class="input">
                    </div>

                    <div>
                        <label for="language_id" class="label">{{ __('Bahasa') }} <span class="text-rose-500">*</span></label>
                        <select id="language_id" name="language_id" required class="input">
                            @foreach ($languages as $language)
                                <option value="{{ $language->id }}" @selected((int) old('language_id', $module->language_id) === $language->id)>
                                    {{ $language->flag }} {{ $language->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('language_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="content_type" class="label">{{ __('Tipe konten') }} <span class="text-rose-500">*</span></label>
                        <select id="content_type" name="content_type" required class="input">
                            @foreach (['kana', 'kanji', 'vocabulary', 'grammar', 'conversation', 'listening', 'speaking', 'reading', 'writing', 'exam', 'video'] as $type)
                                <option value="{{ $type }}" @selected(old('content_type', $module->content_type) === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                        <p class="help">{{ __('Menentukan tampilan materi di aplikasi peserta.') }}</p>
                    </div>

                    <div>
                        <label for="icon" class="label">{{ __('Ikon') }}</label>
                        <input id="icon" name="icon" value="{{ old('icon', $module->icon) }}" class="input" placeholder="漢 / book / 🇯🇵">
                        <p class="help">{{ __('Nama ikon, satu karakter, atau emoji.') }}</p>
                    </div>

                    <div>
                        <label for="color" class="label">{{ __('Warna') }}</label>
                        <select id="color" name="color" class="input">
                            @foreach (config('admin.menu.badge_colors') as $color)
                                <option value="{{ $color }}" @selected(old('color', $module->color ?? 'indigo') === $color)>{{ $color }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="label">{{ __('Deskripsi') }}</label>
                        <textarea id="description" name="description" rows="3" class="input">{{ old('description', $module->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <h2 class="mb-1 font-semibold">{{ __('Hak akses') }}</h2>
                <p class="mb-4 text-xs text-slate-500">
                    {{ __('Modul ini akan dijaga oleh {prefix}.view / .create / .update / .delete.') }}
                </p>

                <div class="space-y-4">
                    <div>
                        <label for="permission_prefix" class="label">{{ __('Prefix permission') }}</label>
                        <input id="permission_prefix" name="permission_prefix" pattern="[a-z0-9_\-]+"
                               value="{{ old('permission_prefix', $module->permission_prefix) }}"
                               class="input font-mono @error('permission_prefix') border-rose-400 @enderror"
                               placeholder="{{ __('otomatis dari slug') }}">
                        @error('permission_prefix') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex items-start gap-2.5 rounded-lg bg-brand-50 p-3 text-sm dark:bg-brand-500/10">
                        <input type="hidden" name="generate_permissions" value="0">
                        <input type="checkbox" name="generate_permissions" value="1" checked
                               class="mt-0.5 rounded border-slate-300 text-brand-600">
                        <span>
                            <span class="font-medium">{{ __('Buat permission otomatis saat menyimpan') }}</span>
                            <span class="mt-0.5 block text-xs text-slate-500">
                                {{ __('Ini yang membuat modul baru langsung bisa dipakai — tinggal centang di matriks permission dan buat menunya.') }}
                            </span>
                        </span>
                    </label>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Tampilan') }}</h2>

                <div class="space-y-4">
                    <div>
                        <label for="sort_order" class="label">{{ __('Urutan') }}</label>
                        <input id="sort_order" name="sort_order" type="number" min="0"
                               value="{{ old('sort_order', $module->sort_order ?? 0) }}" class="input">
                    </div>

                    <label class="flex items-center gap-2.5 text-sm">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                               @checked(old('is_active', $module->is_active ?? true))
                               class="rounded border-slate-300 text-brand-600">
                        {{ __('Modul aktif') }}
                    </label>

                    <label class="flex items-center gap-2.5 text-sm">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" value="1"
                               @checked(old('is_featured', $module->is_featured ?? false))
                               class="rounded border-slate-300 text-brand-600">
                        {{ __('Tampilkan di beranda') }}
                    </label>
                </div>
            </div>

            <div class="card p-5" x-data="{ isPaid: {{ old('is_paid', $module->is_paid ?? false) ? 'true' : 'false' }} }">
                <h2 class="mb-4 font-semibold">{{ __('Harga') }}</h2>

                <div class="space-y-4">
                    <label class="flex items-center gap-2.5 text-sm">
                        <input type="hidden" name="is_paid" value="0">
                        <input type="checkbox" name="is_paid" value="1" x-model="isPaid"
                               class="rounded border-slate-300 text-brand-600">
                        {{ __('Kursus berbayar') }}
                    </label>

                    <div x-show="isPaid" x-cloak>
                        <label for="price" class="label">{{ __('Harga (Rp)') }}</label>
                        <input id="price" name="price" type="number" min="0" step="1000"
                               value="{{ old('price', $module->price ?? 0) }}"
                               class="input @error('price') border-rose-400 @enderror" placeholder="50000">
                        @error('price') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        <p class="help">{{ __('Peserta harus menyelesaikan transaksi (simulasi) sebelum kursus ini masuk ke "Kursus Saya".') }}</p>
                    </div>
                </div>
            </div>

            @if ($module->exists)
                <div class="card p-5">
                    <h2 class="mb-3 font-semibold">{{ __('Langkah berikutnya') }}</h2>
                    <ol class="space-y-2 text-sm text-slate-500">
                        <li>1. {{ __('Tambahkan materi di menu Konten Belajar.') }}</li>
                        <li>2. {{ __('Centang permission modul di matriks.') }}</li>
                        <li>3. {{ __('Buat menu sidebar yang mengarah ke modul ini.') }}</li>
                    </ol>
                    <a href="{{ route('admin.menus.create') }}" class="btn-secondary mt-4 w-full">{{ __('Buat menu untuk modul ini') }}</a>
                </div>
            @endif

            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1">
                    {{ $module->exists ? __('Simpan') : __('Buat modul') }}
                </button>
                <a href="{{ route('admin.modules.index') }}" class="btn-secondary">{{ __('Batal') }}</a>
            </div>
        </div>
    </form>
@endsection
