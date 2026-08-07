@extends('layouts.app')
@section('title', __('Pengaturan Aplikasi'))

@section('content')
    <x-page-header :title="__('Pengaturan Aplikasi')" :description="__('Identitas aplikasi, kontak, pendaftaran, dan meta SEO.')" />

    <div x-data="{ tab: 'general' }" class="space-y-6">
        <div class="flex gap-1 border-b border-slate-200 dark:border-slate-800">
            @foreach (['general' => __('Umum'), 'meta' => __('Meta / SEO')] as $key => $label)
                <button type="button" @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}'
                            ? 'border-brand-600 text-brand-600'
                            : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                        class="border-b-2 px-4 py-2.5 text-sm font-medium transition">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- General --}}
        <form x-show="tab === 'general'" method="POST" action="{{ route('admin.management-admin.update-general') }}"
              enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Identitas') }}</h2>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="app_name" class="label">{{ __('Nama aplikasi') }}</label>
                        <input id="app_name" name="app_name" value="{{ old('app_name', $general['app_name'] ?? '') }}" class="input">
                    </div>

                    <div>
                        <label for="app_tagline" class="label">{{ __('Tagline') }}</label>
                        <input id="app_tagline" name="app_tagline" value="{{ old('app_tagline', $general['app_tagline'] ?? '') }}" class="input">
                    </div>

                    <div>
                        <label for="logo" class="label">{{ __('Logo') }}</label>
                        <input id="logo" name="logo" type="file" accept="image/*" class="input py-1.5">
                        @if (! empty($general['logo']))
                            <img src="{{ Storage::disk('public')->url($general['logo']) }}" alt="" class="mt-2 h-10">
                        @endif
                    </div>

                    <div>
                        <label for="favicon" class="label">{{ __('Favicon') }}</label>
                        <input id="favicon" name="favicon" type="file" accept="image/*" class="input py-1.5">
                        @if (! empty($general['favicon']))
                            <img src="{{ Storage::disk('public')->url($general['favicon']) }}" alt="" class="mt-2 size-8">
                        @endif
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Kontak') }}</h2>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="contact_email" class="label">{{ __('Email') }}</label>
                        <input id="contact_email" name="contact_email" type="email"
                               value="{{ old('contact_email', $general['contact_email'] ?? '') }}" class="input">
                    </div>

                    <div>
                        <label for="contact_phone" class="label">{{ __('Telepon') }}</label>
                        <input id="contact_phone" name="contact_phone"
                               value="{{ old('contact_phone', $general['contact_phone'] ?? '') }}" class="input">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="address" class="label">{{ __('Alamat') }}</label>
                        <textarea id="address" name="address" rows="2" class="input">{{ old('address', $general['address'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Pendaftaran') }}</h2>

                <div class="space-y-4">
                    <label class="flex items-center gap-2.5 text-sm">
                        <input type="hidden" name="registration_open" value="0">
                        <input type="checkbox" name="registration_open" value="1"
                               @checked(old('registration_open', $general['registration_open'] ?? true))
                               class="rounded border-slate-300 text-brand-600">
                        {{ __('Pendaftaran publik dibuka') }}
                    </label>

                    <div class="max-w-xs">
                        <label for="default_role" class="label">{{ __('Role untuk pendaftar baru') }}</label>
                        <select id="default_role" name="default_role" class="input">
                            @foreach ($roles as $value => $label)
                                <option value="{{ $value }}" @selected(($general['default_role'] ?? 'Student') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="maintenance_notice" class="label">{{ __('Pengumuman / banner') }}</label>
                        <textarea id="maintenance_notice" name="maintenance_notice" rows="2" class="input"
                                  placeholder="{{ __('Kosongkan jika tidak ada pengumuman.') }}">{{ old('maintenance_notice', $general['maintenance_notice'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">{{ __('Simpan pengaturan umum') }}</button>
            </div>
        </form>

        {{-- Meta --}}
        <form x-show="tab === 'meta'" x-cloak method="POST" action="{{ route('admin.management-admin.update-meta') }}"
              enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Meta SEO') }}</h2>

                <div class="space-y-4">
                    <div>
                        <label for="meta_title" class="label">{{ __('Meta title') }}</label>
                        <input id="meta_title" name="meta_title" value="{{ old('meta_title', $meta['meta_title'] ?? '') }}" class="input">
                        <p class="help">{{ __('Idealnya di bawah 60 karakter.') }}</p>
                    </div>

                    <div>
                        <label for="meta_description" class="label">{{ __('Meta description') }}</label>
                        <textarea id="meta_description" name="meta_description" rows="3" class="input">{{ old('meta_description', $meta['meta_description'] ?? '') }}</textarea>
                        <p class="help">{{ __('Idealnya 150–160 karakter.') }}</p>
                    </div>

                    <div>
                        <label for="meta_keywords" class="label">{{ __('Meta keywords') }}</label>
                        <input id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $meta['meta_keywords'] ?? '') }}" class="input">
                    </div>

                    <div>
                        <label for="og_image" class="label">{{ __('Gambar Open Graph') }}</label>
                        <input id="og_image" name="og_image" type="file" accept="image/*" class="input py-1.5">
                        @if (! empty($meta['og_image']))
                            <img src="{{ Storage::disk('public')->url($meta['og_image']) }}" alt="" class="mt-2 h-24 rounded-lg">
                        @endif
                    </div>

                    <div>
                        <label for="analytics_id" class="label">{{ __('Analytics ID') }}</label>
                        <input id="analytics_id" name="analytics_id" value="{{ old('analytics_id', $meta['analytics_id'] ?? '') }}"
                               class="input font-mono" placeholder="G-XXXXXXXXXX">
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">{{ __('Simpan meta') }}</button>
            </div>
        </form>
    </div>
@endsection
