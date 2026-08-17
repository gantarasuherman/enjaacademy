@extends('layouts.app')
@section('title', __('Pengaturan Aplikasi'))

@section('content')
    <x-page-header :title="__('Pengaturan Aplikasi')" :description="__('Identitas aplikasi, kontak, pendaftaran, dan meta SEO.')" />

    <div x-data="{ tab: 'general' }" class="space-y-6">
        <div class="flex gap-1 border-b border-slate-200 dark:border-slate-800">
            @foreach (['general' => __('Umum'), 'meta' => __('Meta / SEO'), 'integrations' => __('Integrasi')] as $key => $label)
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

        {{-- Integrations --}}
        <form x-show="tab === 'integrations'" x-cloak method="POST" action="{{ route('admin.management-admin.update-integrations') }}"
              class="space-y-6">
            @csrf

            <div class="card p-5">
                <h2 class="mb-1 font-semibold">{{ __('Provider AI Aktif') }}</h2>
                <p class="help mt-0 mb-4">{{ __('Dipakai semua fitur "Buat dengan AI" (kosakata & materi lesson) — cuma satu yang aktif sekaligus.') }}</p>

                <div class="flex flex-col gap-2">
                    <label class="flex items-center gap-2.5 text-sm">
                        <input type="radio" name="ai_provider" value="gemini"
                               @checked(old('ai_provider', $integrations['ai_provider']) === 'gemini')
                               class="border-slate-300 text-brand-600">
                        <span>Gemini (Google) — <span class="text-slate-500">{{ __('gratis') }}</span></span>
                    </label>
                    <label class="flex items-center gap-2.5 text-sm">
                        <input type="radio" name="ai_provider" value="grok"
                               @checked(old('ai_provider', $integrations['ai_provider']) === 'grok')
                               class="border-slate-300 text-brand-600">
                        <span>Grok (xAI) — <span class="text-slate-500">{{ __('berbayar per token') }}</span></span>
                    </label>
                    <label class="flex items-center gap-2.5 text-sm">
                        <input type="radio" name="ai_provider" value="groq"
                               @checked(old('ai_provider', $integrations['ai_provider']) === 'groq')
                               class="border-slate-300 text-brand-600">
                        <span>Groq — <span class="text-slate-500">{{ __('gratis, hanya dibatasi rate limit') }}</span></span>
                    </label>
                </div>
                @error('ai_provider') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="card p-5" x-data="{ showKey: false, clear: false }">
                <h2 class="mb-1 font-semibold">{{ __('AI Content Generator (Gemini)') }}</h2>
                <p class="help mt-0 mb-4">
                    {{ __('Dipakai fitur "Buat dengan AI" di halaman materi. Dapatkan API key gratis di') }}
                    <a href="https://aistudio.google.com/apikey" target="_blank" rel="noopener" class="text-brand-600 hover:underline">aistudio.google.com/apikey</a>
                    {{ __('— tanpa kartu kredit.') }}
                </p>

                <div class="space-y-4">
                    <div>
                        <label for="gemini_api_key" class="label">{{ __('API Key') }}</label>

                        <div class="relative">
                            <input :type="showKey ? 'text' : 'password'" id="gemini_api_key" name="gemini_api_key"
                                   x-bind:disabled="clear" autocomplete="off" class="input pr-10 font-mono"
                                   placeholder="{{ $integrations['gemini_api_key_set'] ? '•••••••••••••••• ('.__('tersimpan').')' : __('Belum diatur') }}">
                            <button type="button" @click="showKey = !showKey"
                                    class="absolute inset-y-0 right-0 grid w-10 place-items-center text-slate-400 hover:text-slate-600">
                                <x-icon name="eye" class="size-4" />
                            </button>
                        </div>

                        <p class="help">
                            {{ __('Kosongkan untuk mempertahankan key yang sudah tersimpan.') }}
                            @if ($integrations['gemini_api_key_set'])
                                <span class="text-emerald-600 dark:text-emerald-400">{{ __('Key sudah diatur.') }}</span>
                            @else
                                <span class="text-amber-600 dark:text-amber-400">{{ __('Belum ada key — fitur AI akan menampilkan "belum diaktifkan".') }}</span>
                            @endif
                        </p>

                        @if ($integrations['gemini_api_key_set'])
                            <label class="mt-2 flex items-center gap-2 text-sm text-rose-600">
                                <input type="hidden" name="clear_gemini_api_key" value="0">
                                <input type="checkbox" name="clear_gemini_api_key" value="1" x-model="clear"
                                       class="rounded border-slate-300 text-rose-600">
                                {{ __('Hapus API key dari Pengaturan (kembali memakai .env jika ada)') }}
                            </label>
                        @endif

                        @error('gemini_api_key') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="max-w-xs">
                        <label for="gemini_model" class="label">{{ __('Model') }}</label>
                        <input id="gemini_model" name="gemini_model"
                               value="{{ old('gemini_model', $integrations['gemini_model']) }}"
                               class="input font-mono text-sm" placeholder="gemini-flash-latest">
                        <p class="help">{{ __('Kosongkan untuk memakai default (gemini-flash-latest).') }}</p>
                    </div>
                </div>
            </div>

            <div class="card p-5" x-data="{ showKey: false, clear: false }">
                <h2 class="mb-1 font-semibold">{{ __('AI Content Generator (Grok)') }}</h2>
                <p class="help mt-0 mb-4">
                    {{ __('Alternatif Gemini — berbayar per token, tidak ada tingkat gratis. Ambil API key di') }}
                    <a href="https://console.x.ai" target="_blank" rel="noopener" class="text-brand-600 hover:underline">console.x.ai</a>.
                </p>

                <div class="space-y-4">
                    <div>
                        <label for="grok_api_key" class="label">{{ __('API Key') }}</label>

                        <div class="relative">
                            <input :type="showKey ? 'text' : 'password'" id="grok_api_key" name="grok_api_key"
                                   x-bind:disabled="clear" autocomplete="off" class="input pr-10 font-mono"
                                   placeholder="{{ $integrations['grok_api_key_set'] ? '•••••••••••••••• ('.__('tersimpan').')' : __('Belum diatur') }}">
                            <button type="button" @click="showKey = !showKey"
                                    class="absolute inset-y-0 right-0 grid w-10 place-items-center text-slate-400 hover:text-slate-600">
                                <x-icon name="eye" class="size-4" />
                            </button>
                        </div>

                        <p class="help">
                            {{ __('Kosongkan untuk mempertahankan key yang sudah tersimpan.') }}
                            @if ($integrations['grok_api_key_set'])
                                <span class="text-emerald-600 dark:text-emerald-400">{{ __('Key sudah diatur.') }}</span>
                            @else
                                <span class="text-amber-600 dark:text-amber-400">{{ __('Belum ada key — kalau Grok dipilih sebagai provider aktif, fitur AI akan menampilkan "belum diaktifkan".') }}</span>
                            @endif
                        </p>

                        @if ($integrations['grok_api_key_set'])
                            <label class="mt-2 flex items-center gap-2 text-sm text-rose-600">
                                <input type="hidden" name="clear_grok_api_key" value="0">
                                <input type="checkbox" name="clear_grok_api_key" value="1" x-model="clear"
                                       class="rounded border-slate-300 text-rose-600">
                                {{ __('Hapus API key dari Pengaturan (kembali memakai .env jika ada)') }}
                            </label>
                        @endif

                        @error('grok_api_key') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="max-w-xs">
                        <label for="grok_model" class="label">{{ __('Model') }}</label>
                        <input id="grok_model" name="grok_model"
                               value="{{ old('grok_model', $integrations['grok_model']) }}"
                               class="input font-mono text-sm" placeholder="grok-4.6">
                        <p class="help">{{ __('Kosongkan untuk memakai default (grok-4.6).') }}</p>
                    </div>
                </div>
            </div>

            <div class="card p-5" x-data="{ showKey: false, clear: false }">
                <h2 class="mb-1 font-semibold">{{ __('AI Content Generator (Groq)') }}</h2>
                <p class="help mt-0 mb-4">
                    {{ __('Alternatif gratis lain — tanpa kartu kredit, hanya dibatasi rate limit (bukan kuota harian seperti Gemini). Ambil API key di') }}
                    <a href="https://console.groq.com/keys" target="_blank" rel="noopener" class="text-brand-600 hover:underline">console.groq.com/keys</a>.
                </p>

                <div class="space-y-4">
                    <div>
                        <label for="groq_api_key" class="label">{{ __('API Key') }}</label>

                        <div class="relative">
                            <input :type="showKey ? 'text' : 'password'" id="groq_api_key" name="groq_api_key"
                                   x-bind:disabled="clear" autocomplete="off" class="input pr-10 font-mono"
                                   placeholder="{{ $integrations['groq_api_key_set'] ? '•••••••••••••••• ('.__('tersimpan').')' : __('Belum diatur') }}">
                            <button type="button" @click="showKey = !showKey"
                                    class="absolute inset-y-0 right-0 grid w-10 place-items-center text-slate-400 hover:text-slate-600">
                                <x-icon name="eye" class="size-4" />
                            </button>
                        </div>

                        <p class="help">
                            {{ __('Kosongkan untuk mempertahankan key yang sudah tersimpan.') }}
                            @if ($integrations['groq_api_key_set'])
                                <span class="text-emerald-600 dark:text-emerald-400">{{ __('Key sudah diatur.') }}</span>
                            @else
                                <span class="text-amber-600 dark:text-amber-400">{{ __('Belum ada key — kalau Groq dipilih sebagai provider aktif, fitur AI akan menampilkan "belum diaktifkan".') }}</span>
                            @endif
                        </p>

                        @if ($integrations['groq_api_key_set'])
                            <label class="mt-2 flex items-center gap-2 text-sm text-rose-600">
                                <input type="hidden" name="clear_groq_api_key" value="0">
                                <input type="checkbox" name="clear_groq_api_key" value="1" x-model="clear"
                                       class="rounded border-slate-300 text-rose-600">
                                {{ __('Hapus API key dari Pengaturan (kembali memakai .env jika ada)') }}
                            </label>
                        @endif

                        @error('groq_api_key') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="max-w-xs">
                        <label for="groq_model" class="label">{{ __('Model') }}</label>
                        <input id="groq_model" name="groq_model"
                               value="{{ old('groq_model', $integrations['groq_model']) }}"
                               class="input font-mono text-sm" placeholder="openai/gpt-oss-120b">
                        <p class="help">{{ __('Kosongkan untuk memakai default (openai/gpt-oss-120b).') }}</p>
                    </div>
                </div>
            </div>

            <div class="card p-5" x-data="{ showApi: false, showPrivate: false, clearApi: false, clearPrivate: false }">
                <h2 class="mb-1 font-semibold">{{ __('Pembayaran QRIS (Tripay)') }}</h2>
                <p class="help mt-0 mb-4">
                    {{ __('Dipakai untuk checkout kursus berbayar. Daftar sandbox gratis (tanpa badan usaha) di') }}
                    <a href="https://tripay.co.id" target="_blank" rel="noopener" class="text-brand-600 hover:underline">tripay.co.id</a>
                    {{ __(', lalu ambil Merchant Code/API Key/Private Key dari menu API & Integrasi → Simulator → Merchant → Detail. Kosongkan semua untuk memakai mode simulasi (tanpa QRIS asli).') }}
                </p>

                <div class="space-y-4">
                    <div class="max-w-sm">
                        <label for="tripay_merchant_code" class="label">{{ __('Merchant Code') }}</label>
                        <input id="tripay_merchant_code" name="tripay_merchant_code"
                               value="{{ old('tripay_merchant_code', $integrations['tripay_merchant_code']) }}"
                               class="input font-mono text-sm" placeholder="T0001">
                    </div>

                    <div>
                        <label for="tripay_api_key" class="label">{{ __('API Key') }}</label>
                        <div class="relative">
                            <input :type="showApi ? 'text' : 'password'" id="tripay_api_key" name="tripay_api_key"
                                   x-bind:disabled="clearApi" autocomplete="off" class="input pr-10 font-mono"
                                   placeholder="{{ $integrations['tripay_api_key_set'] ? '•••••••••••••••• ('.__('tersimpan').')' : __('Belum diatur') }}">
                            <button type="button" @click="showApi = !showApi"
                                    class="absolute inset-y-0 right-0 grid w-10 place-items-center text-slate-400 hover:text-slate-600">
                                <x-icon name="eye" class="size-4" />
                            </button>
                        </div>
                        @if ($integrations['tripay_api_key_set'])
                            <label class="mt-2 flex items-center gap-2 text-sm text-rose-600">
                                <input type="hidden" name="clear_tripay_api_key" value="0">
                                <input type="checkbox" name="clear_tripay_api_key" value="1" x-model="clearApi"
                                       class="rounded border-slate-300 text-rose-600">
                                {{ __('Hapus API key dari Pengaturan') }}
                            </label>
                        @endif
                        @error('tripay_api_key') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="tripay_private_key" class="label">{{ __('Private Key') }}</label>
                        <div class="relative">
                            <input :type="showPrivate ? 'text' : 'password'" id="tripay_private_key" name="tripay_private_key"
                                   x-bind:disabled="clearPrivate" autocomplete="off" class="input pr-10 font-mono"
                                   placeholder="{{ $integrations['tripay_private_key_set'] ? '•••••••••••••••• ('.__('tersimpan').')' : __('Belum diatur') }}">
                            <button type="button" @click="showPrivate = !showPrivate"
                                    class="absolute inset-y-0 right-0 grid w-10 place-items-center text-slate-400 hover:text-slate-600">
                                <x-icon name="eye" class="size-4" />
                            </button>
                        </div>
                        <p class="help">{{ __('Dipakai untuk tanda tangan transaksi & verifikasi webhook — jaga kerahasiaannya.') }}</p>
                        @if ($integrations['tripay_private_key_set'])
                            <label class="mt-2 flex items-center gap-2 text-sm text-rose-600">
                                <input type="hidden" name="clear_tripay_private_key" value="0">
                                <input type="checkbox" name="clear_tripay_private_key" value="1" x-model="clearPrivate"
                                       class="rounded border-slate-300 text-rose-600">
                                {{ __('Hapus Private key dari Pengaturan') }}
                            </label>
                        @endif
                        @error('tripay_private_key') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <p class="text-xs">
                        @if ($integrations['tripay_api_key_set'] && $integrations['tripay_private_key_set'] && $integrations['tripay_merchant_code'])
                            <span class="text-emerald-600 dark:text-emerald-400">{{ __('Tripay aktif — checkout kursus berbayar akan pakai QRIS asli.') }}</span>
                        @else
                            <span class="text-amber-600 dark:text-amber-400">{{ __('Belum lengkap — checkout kursus berbayar masih pakai mode simulasi.') }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">{{ __('Simpan integrasi') }}</button>
            </div>
        </form>
    </div>
@endsection
