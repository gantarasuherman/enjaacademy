@extends('layouts.app')
@section('title', __('Panduan'))

@section('content')
    <x-page-header
        :title="__('Panduan Penggunaan')"
        :description="__('Cara mengelola sistem tanpa menyentuh kode — termasuk menambah modul pembelajaran baru dari nol.')"
    />

    <div x-data="{ tab: 'module' }" class="grid gap-6 lg:grid-cols-[16rem_1fr]">

        {{-- Nav --}}
        <aside class="lg:sticky lg:top-24 lg:self-start">
            <nav class="card space-y-1 p-3">
                @foreach ([
                    'module' => __('Tambah modul tanpa coding'),
                    'menu' => __('Mengelola menu'),
                    'rbac' => __('Role & permission'),
                    'content' => __('Materi & kuis'),
                    'ops' => __('Backup & audit'),
                ] as $key => $label)
                    <button type="button" @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}'
                                ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/15 dark:text-brand-300'
                                : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'"
                            class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium transition">
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </aside>

        <div>
            {{-- ============ ADD A MODULE WITHOUT CODING ============ --}}
            <div x-show="tab === 'module'" class="space-y-6">
                <div class="card p-6">
                    <h2 class="text-xl font-bold">{{ __('Menambah modul pembelajaran baru — tanpa satu baris kode') }}</h2>
                    <p class="mt-2 text-sm text-slate-500">
                        {{ __('Contoh: menambahkan modul "Bahasa Korea — Hangul". Seluruh langkah dikerjakan dari panel ini.') }}
                    </p>

                    <ol class="mt-6 space-y-6">
                        @foreach ([
                            [
                                'title' => __('Buat bahasa (jika belum ada)'),
                                'body' => __('Master Data → Bahasa → Bahasa baru. Isi nama, kode ISO, dan emoji bendera.'),
                                'route' => 'admin.languages.create',
                                'cta' => __('Buka form bahasa'),
                                'can' => 'languages.create',
                            ],
                            [
                                'title' => __('Buat modul dan generate permission-nya'),
                                'body' => __('Master Data → Modul Pembelajaran → Modul baru. Pilih bahasa, tipe konten, lalu biarkan "Buat permission otomatis" tetap tercentang. Sistem akan membuat {prefix}.view, .create, .update, dan .delete.'),
                                'route' => 'admin.modules.create',
                                'cta' => __('Buka form modul'),
                                'can' => 'modules.create',
                            ],
                            [
                                'title' => __('Berikan permission ke role'),
                                'body' => __('Pengguna & Akses → Matriks Permission. Cari modul barumu, centang {prefix}.view untuk role Student, dan centang penuh untuk Teacher/Admin.'),
                                'route' => 'admin.roles.matrix',
                                'cta' => __('Buka matriks permission'),
                                'can' => 'roles.view',
                            ],
                            [
                                'title' => __('Isi materi'),
                                'body' => __('Konten Belajar → Materi → Materi baru. Pilih modul barumu, isi item satu per satu, atau impor sekaligus lewat CSV.'),
                                'route' => 'admin.lessons.create',
                                'cta' => __('Buka form materi'),
                                'can' => 'lessons.create',
                            ],
                            [
                                'title' => __('Buat menu sidebar'),
                                'body' => __('Menu Management → Tambah Menu. Pilih route peserta.learning.module, isi Route parameters dengan module=slug-modul-mu, dan set Required permission ke {prefix}.view.'),
                                'route' => 'admin.menus.create',
                                'cta' => __('Buka form menu'),
                                'can' => 'menus.create',
                            ],
                        ] as $index => $step)
                            <li class="flex gap-4">
                                <span class="grid size-8 shrink-0 place-items-center rounded-full bg-brand-600 text-sm font-bold text-white">
                                    {{ $index + 1 }}
                                </span>

                                <div class="min-w-0 flex-1">
                                    <h3 class="font-semibold">{{ $step['title'] }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">{{ $step['body'] }}</p>

                                    @can($step['can'])
                                        <a href="{{ route($step['route']) }}" class="btn-secondary mt-3 text-sm">
                                            {{ $step['cta'] }}
                                        </a>
                                    @endcan
                                </div>
                            </li>
                        @endforeach
                    </ol>

                    <x-alert type="success" class="mt-6">
                        <p class="font-semibold">{{ __('Selesai — tidak ada deploy, tidak ada migrasi.') }}</p>
                        <p class="mt-1">
                            {{ __('Peserta dengan role yang tepat akan langsung melihat menu barunya setelah login berikutnya. Cache menu dibersihkan otomatis setiap kali menu, role, atau permission berubah.') }}
                        </p>
                    </x-alert>
                </div>

                <div class="card p-6">
                    <h3 class="font-semibold">{{ __('Kenapa ini bisa bekerja?') }}</h3>
                    <ul class="mt-3 space-y-2 text-sm text-slate-500">
                        <li class="flex gap-2.5">
                            <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-brand-600"></span>
                            {{ __('Modul adalah baris di tabel learning_modules, bukan kelas PHP. Satu set controller generic melayani semua modul.') }}
                        </li>
                        <li class="flex gap-2.5">
                            <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-brand-600"></span>
                            {{ __('Setiap modul menentukan permission-nya sendiri lewat permission_prefix, jadi tidak ada Policy baru yang perlu ditulis.') }}
                        </li>
                        <li class="flex gap-2.5">
                            <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-brand-600"></span>
                            {{ __('Sidebar dirender dari tabel menus secara rekursif — tidak ada menu yang di-hardcode di Blade.') }}
                        </li>
                    </ul>
                </div>
            </div>

            {{-- ============ MENU ============ --}}
            <div x-show="tab === 'menu'" x-cloak class="space-y-6">
                <div class="card p-6">
                    <h2 class="text-xl font-bold">{{ __('Mengelola menu') }}</h2>

                    <dl class="mt-5 space-y-5 text-sm">
                        @foreach ([
                            __('Route name vs URL') => __('Selalu utamakan route name — URL bisa berubah, nama route tidak. Isi URL hanya untuk tautan eksternal.'),
                            __('Menu induk tanpa tujuan') => __('Kosongkan route dan URL untuk membuat menu yang hanya berfungsi sebagai grup. Menu induk yang seluruh anaknya tersembunyi akan ikut disembunyikan otomatis.'),
                            __('Drag & drop') => __('Seret pegangan di kiri untuk mengubah urutan atau memindahkan menu ke dalam menu lain. Perubahan baru tersimpan setelah menekan "Simpan urutan".'),
                            __('Permission vs matriks role') => __('Keduanya digabung dengan AND. Menu tanpa permission dan tanpa role terlihat oleh semua pengguna yang login.'),
                            __('Divider & header') => __('Tipe "divider" membuat garis pemisah, "header" membuat judul bagian. Keduanya otomatis hilang jika tidak ada menu yang mengikutinya.'),
                        ] as $term => $definition)
                            <div>
                                <dt class="font-semibold">{{ $term }}</dt>
                                <dd class="mt-1 text-slate-500">{{ $definition }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    @can('menus.view')
                        <a href="{{ route('admin.menus.index') }}" class="btn-primary mt-6">{{ __('Buka Menu Management') }}</a>
                    @endcan
                </div>
            </div>

            {{-- ============ RBAC ============ --}}
            <div x-show="tab === 'rbac'" x-cloak class="space-y-6">
                <div class="card p-6">
                    <h2 class="text-xl font-bold">{{ __('Role & Permission') }}</h2>

                    <dl class="mt-5 space-y-5 text-sm">
                        @foreach ([
                            __('Format permission') => __('Selalu {module}.{action}, misalnya kanji.view. Format ini dipakai oleh middleware, Policy, dan pengelompokan matriks.'),
                            __('Role :super', ['super' => config('admin.super_role')]) => __('Mendapat semua izin lewat Gate::before, jadi tidak pernah tertinggal ketika modul baru menambah permission. Namanya tidak bisa diubah dan tidak bisa dihapus.'),
                            __('Matriks permission') => __('Cara tercepat mengatur banyak role sekaligus. Gunakan tombol "grup" untuk mencentang satu modul penuh pada satu role.'),
                            __('Matriks akses menu') => __('Membatasi menu tertentu ke role tertentu. Berguna untuk memisahkan menu Teacher dan Student meski permission-nya mirip.'),
                            __('Pengaman') => __('Sistem menolak menghapus role yang masih dipakai, menghapus permission yang masih dirujuk menu, dan mencabut role :super dari pemegang terakhirnya.', ['super' => config('admin.super_role')]),
                        ] as $term => $definition)
                            <div>
                                <dt class="font-semibold">{{ $term }}</dt>
                                <dd class="mt-1 text-slate-500">{{ $definition }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="mt-6 flex flex-wrap gap-2">
                        @can('roles.view')
                            <a href="{{ route('admin.roles.matrix') }}" class="btn-primary">{{ __('Matriks permission') }}</a>
                        @endcan
                        @can('menus.view')
                            <a href="{{ route('admin.menus.matrix') }}" class="btn-secondary">{{ __('Matriks akses menu') }}</a>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- ============ CONTENT ============ --}}
            <div x-show="tab === 'content'" x-cloak class="space-y-6">
                <div class="card p-6">
                    <h2 class="text-xl font-bold">{{ __('Materi & Kuis') }}</h2>

                    <dl class="mt-5 space-y-5 text-sm">
                        @foreach ([
                            __('Item materi') => __('Satu baris item = satu kana, kanji, kosakata, atau poin grammar. Tabel item bersifat generic sehingga dipakai semua tipe modul.'),
                            __('Impor CSV') => __('Kolom wajib hanya "term". Kolom opsional: reading, romaji, meaning, example, example_meaning. Unduh template dari halaman materi.'),
                            __('Draf vs terbit') => __('Materi draf hanya terlihat oleh pengguna dengan izin lessons.update, sehingga bisa disiapkan tanpa terlihat peserta.'),
                            __('Kuis') => __('Sebuah kuis wajib punya minimal satu soal sebelum bisa diterbitkan. Setiap soal pilihan ganda butuh minimal dua pilihan dan tepat satu jawaban benar.'),
                            __('Penilaian') => __('Penilaian dilakukan di server. Jawaban ketik/isian dibandingkan setelah normalisasi huruf besar-kecil, tanda baca, dan bentuk singkatan (didn\'t = did not).'),
                        ] as $term => $definition)
                            <div>
                                <dt class="font-semibold">{{ $term }}</dt>
                                <dd class="mt-1 text-slate-500">{{ $definition }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="mt-6 flex flex-wrap gap-2">
                        @can('lessons.view')
                            <a href="{{ route('admin.lessons.index') }}" class="btn-primary">{{ __('Kelola materi') }}</a>
                        @endcan
                        @can('quizzes.view')
                            <a href="{{ route('admin.quizzes.index') }}" class="btn-secondary">{{ __('Kelola kuis') }}</a>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- ============ OPS ============ --}}
            <div x-show="tab === 'ops'" x-cloak class="space-y-6">
                <div class="card p-6">
                    <h2 class="text-xl font-bold">{{ __('Backup & Audit') }}</h2>

                    <dl class="mt-5 space-y-5 text-sm">
                        @foreach ([
                            __('Backup otomatis') => __('Scheduler membuat backup setiap hari pukul 02:00 dan hanya menyimpan :n arsip terbaru.', ['n' => config('admin.backup.keep')]),
                            __('Backup manual') => __('Tombol "Buat backup sekarang" menjalankan mysqldump seketika. Gunakan sebelum melakukan perubahan besar.'),
                            __('Audit log') => __('Mencatat setiap create, update, dan delete pada pengguna, role, permission, dan menu — lengkap dengan nilai sebelum dan sesudah.'),
                            __('Pembersihan log') => __('Log lebih lama dari :n hari dibersihkan otomatis setiap minggu, atau manual dari halaman audit log.', ['n' => config('admin.audit.prune_days')]),
                            __('Cache menu') => __('Dibersihkan otomatis setiap kali menu, role, atau permission berubah. Tombol "Clear cache" hanya diperlukan bila kamu mengubah data langsung di database.'),
                        ] as $term => $definition)
                            <div>
                                <dt class="font-semibold">{{ $term }}</dt>
                                <dd class="mt-1 text-slate-500">{{ $definition }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="mt-6 flex flex-wrap gap-2">
                        @can('backups.view')
                            <a href="{{ route('admin.backups.index') }}" class="btn-primary">{{ __('Backup') }}</a>
                        @endcan
                        @can('audit-logs.view')
                            <a href="{{ route('admin.audit-logs.index') }}" class="btn-secondary">{{ __('Audit log') }}</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
