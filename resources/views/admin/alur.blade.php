@extends('layouts.app')
@section('title', __('Alur Aplikasi'))

@php
    /*
     * Navigation flows for the tasks admins actually perform.
     *
     * Each step names the exact sidebar entry or button it refers to, so the
     * page stays useful even for someone who has never opened the panel. The
     * `route` key is optional — steps that describe filling a form have none.
     *
     * Kinds: menu (sidebar navigation) · action (a button) · form (fill fields)
     *        · done (the outcome).
     */
    $flows = [
        [
            'id' => 'materi',
            'title' => __('Menambah materi baru'),
            'goal' => __('Membuat satu materi berisi kana, kanji, kosakata, atau poin grammar di dalam sebuah modul.'),
            'permission' => 'lessons.create',
            'featured' => true,
            'steps' => [
                ['kind' => 'menu', 'label' => __('Konten Belajar'), 'detail' => __('Buka grup menu di sidebar')],
                ['kind' => 'menu', 'label' => __('Materi'), 'detail' => __('Daftar seluruh materi'), 'route' => 'admin.lessons.index'],
                ['kind' => 'action', 'label' => __('Materi baru'), 'detail' => __('Tombol kanan atas'), 'route' => 'admin.lessons.create'],
                ['kind' => 'form', 'label' => __('Pilih modul'), 'detail' => __('Menentukan di mana materi ini muncul')],
                ['kind' => 'form', 'label' => __('Isi judul & level'), 'detail' => __('Judul wajib; level mis. N5 atau A2')],
                ['kind' => 'form', 'label' => __('Isi item materi'), 'detail' => __('Tambah baris satu per satu, atau impor CSV')],
                ['kind' => 'form', 'label' => __('Centang Terbitkan'), 'detail' => __('Tanpa ini materi tetap draf')],
                ['kind' => 'done', 'label' => __('Simpan'), 'detail' => __('Materi langsung terlihat peserta')],
            ],
            'notes' => [
                __('Materi draf hanya terlihat oleh pemegang izin lessons.update, jadi aman disiapkan lebih dulu.'),
                __('Urutan materi di dalam modul diatur lewat kolom Urutan pada form yang sama.'),
            ],
        ],

        [
            'id' => 'modul',
            'title' => __('Menambah modul pembelajaran baru'),
            'goal' => __('Menghadirkan modul baru — misalnya "Bahasa Korea — Hangul" — tanpa mengubah kode sama sekali.'),
            'permission' => 'modules.create',
            'featured' => true,
            'steps' => [
                ['kind' => 'menu', 'label' => __('Master Data'), 'detail' => __('Grup menu di sidebar')],
                ['kind' => 'menu', 'label' => __('Bahasa'), 'detail' => __('Lewati bila bahasanya sudah ada'), 'route' => 'admin.languages.index'],
                ['kind' => 'action', 'label' => __('Bahasa baru'), 'detail' => __('Isi nama, kode ISO, emoji bendera'), 'route' => 'admin.languages.create'],
                ['kind' => 'menu', 'label' => __('Modul Pembelajaran'), 'detail' => __('Kembali ke Master Data'), 'route' => 'admin.modules.index'],
                ['kind' => 'action', 'label' => __('Modul baru'), 'detail' => __('Tombol kanan atas'), 'route' => 'admin.modules.create'],
                ['kind' => 'form', 'label' => __('Biarkan "Buat permission otomatis" tercentang'), 'detail' => __('Ini yang membuat modul langsung punya hak akses')],
                ['kind' => 'menu', 'label' => __('Matriks Permission'), 'detail' => __('Pengguna & Akses → Matriks Permission'), 'route' => 'admin.roles.matrix'],
                ['kind' => 'form', 'label' => __('Centang {prefix}.view untuk Student'), 'detail' => __('Beri centang penuh untuk Teacher dan Admin')],
                ['kind' => 'menu', 'label' => __('Tambah Menu'), 'detail' => __('Menu Management → Tambah Menu'), 'route' => 'admin.menus.create'],
                ['kind' => 'form', 'label' => __('Route peserta.learning.module'), 'detail' => __('Parameter: module={slug-modul}, permission: {prefix}.view')],
                ['kind' => 'done', 'label' => __('Selesai'), 'detail' => __('Tanpa deploy, tanpa migrasi')],
            ],
            'notes' => [
                __('Cache menu dibersihkan otomatis, jadi peserta melihat menu barunya pada login berikutnya.'),
                __('Isi materinya menyusul lewat alur "Menambah materi baru" di atas.'),
            ],
        ],

        [
            'id' => 'kuis',
            'title' => __('Membuat kuis'),
            'goal' => __('Menyusun kuis penutup materi lengkap dengan soal, kunci jawaban, dan pembahasan.'),
            'permission' => 'quizzes.create',
            'steps' => [
                ['kind' => 'menu', 'label' => __('Konten Belajar'), 'detail' => __('Grup menu di sidebar')],
                ['kind' => 'menu', 'label' => __('Kuis'), 'detail' => __('Daftar kuis'), 'route' => 'admin.quizzes.index'],
                ['kind' => 'action', 'label' => __('Kuis baru'), 'detail' => __('Tombol kanan atas'), 'route' => 'admin.quizzes.create'],
                ['kind' => 'form', 'label' => __('Kaitkan modul & materi'), 'detail' => __('Keduanya opsional, tapi memudahkan peserta menemukannya')],
                ['kind' => 'form', 'label' => __('Tambah soal'), 'detail' => __('Pilih tipe, isi pilihan, tandai jawaban benar')],
                ['kind' => 'form', 'label' => __('Isi pembahasan'), 'detail' => __('Ditampilkan setelah kuis selesai')],
                ['kind' => 'form', 'label' => __('Atur skor lulus & batas waktu'), 'detail' => __('Kolom di panel kanan')],
                ['kind' => 'done', 'label' => __('Terbitkan'), 'detail' => __('Butuh minimal satu soal yang valid')],
            ],
            'notes' => [
                __('Setiap soal pilihan ganda wajib punya minimal dua pilihan dan tepat satu jawaban benar — form memvalidasinya langsung saat diketik.'),
                __('Penilaian dilakukan di server; kunci jawaban tidak pernah dikirim ke browser peserta.'),
            ],
        ],

        [
            'id' => 'impor',
            'title' => __('Mengimpor kosakata massal dari CSV'),
            'goal' => __('Memasukkan puluhan hingga ratusan item sekaligus tanpa mengetik satu per satu.'),
            'permission' => 'lessons.create',
            'steps' => [
                ['kind' => 'menu', 'label' => __('Konten Belajar → Materi'), 'detail' => __('Daftar materi'), 'route' => 'admin.lessons.index'],
                ['kind' => 'action', 'label' => __('Template CSV'), 'detail' => __('Unduh dulu supaya nama kolom pasti benar'), 'route' => 'admin.lessons.template'],
                ['kind' => 'form', 'label' => __('Isi berkas CSV'), 'detail' => __('Wajib: term. Opsional: reading, romaji, meaning, example')],
                ['kind' => 'action', 'label' => __('Buka materi tujuan'), 'detail' => __('Klik Edit pada materi yang ingin diisi')],
                ['kind' => 'form', 'label' => __('Panel "Impor item dari CSV"'), 'detail' => __('Ada di bagian atas halaman edit')],
                ['kind' => 'done', 'label' => __('Impor'), 'detail' => __('Item ditambahkan di bawah yang sudah ada')],
            ],
            'notes' => [
                __('Impor bersifat menambah, bukan mengganti — item lama tetap aman.'),
                __('Berkas disimpan sebagai UTF-8 dengan BOM supaya kana dan kanji terbaca benar di Excel.'),
            ],
        ],

        [
            'id' => 'pengguna',
            'title' => __('Menambah pengguna & memberi role'),
            'goal' => __('Membuat akun baru dan menentukan sejauh mana ia boleh mengakses panel.'),
            'permission' => 'users.create',
            'steps' => [
                ['kind' => 'menu', 'label' => __('Pengguna & Akses'), 'detail' => __('Grup menu di sidebar')],
                ['kind' => 'menu', 'label' => __('Pengguna'), 'detail' => __('Daftar seluruh akun'), 'route' => 'admin.users.index'],
                ['kind' => 'action', 'label' => __('Pengguna baru'), 'detail' => __('Tombol kanan atas'), 'route' => 'admin.users.create'],
                ['kind' => 'form', 'label' => __('Isi nama, email, kata sandi'), 'detail' => __('Kata sandi wajib untuk akun baru')],
                ['kind' => 'form', 'label' => __('Centang role'), 'detail' => __('Panel kanan — boleh lebih dari satu')],
                ['kind' => 'form', 'label' => __('Tandai email terverifikasi'), 'detail' => __('Melewati langkah verifikasi email')],
                ['kind' => 'done', 'label' => __('Buat pengguna'), 'detail' => __('Akun langsung bisa dipakai login')],
            ],
            'notes' => [
                __('Role bisa diubah belakangan dari halaman detail pengguna tanpa membuka form edit.'),
                __('Sistem menolak mencabut role Super Admin dari pemegang terakhirnya — supaya panel tidak pernah terkunci.'),
            ],
        ],

        [
            'id' => 'hak-akses',
            'title' => __('Mengatur hak akses sebuah role'),
            'goal' => __('Menentukan halaman dan tombol apa saja yang boleh disentuh sebuah role.'),
            'permission' => 'roles.update',
            'steps' => [
                ['kind' => 'menu', 'label' => __('Pengguna & Akses'), 'detail' => __('Grup menu di sidebar')],
                ['kind' => 'menu', 'label' => __('Matriks Permission'), 'detail' => __('Semua role & permission dalam satu grid'), 'route' => 'admin.roles.matrix'],
                ['kind' => 'form', 'label' => __('Cari modul'), 'detail' => __('Kotak filter di kiri atas toolbar')],
                ['kind' => 'form', 'label' => __('Centang per sel, baris, atau grup'), 'detail' => __('Tombol "grup" mencentang satu modul penuh')],
                ['kind' => 'done', 'label' => __('Simpan matriks'), 'detail' => __('Cache menu ikut dibersihkan otomatis')],
            ],
            'notes' => [
                __('Untuk satu role saja, jalur lebih pendek: Role → tombol Permission pada baris role tersebut.'),
                __('Baris Super Admin sengaja dikunci — ia memperoleh semua izin lewat Gate::before.'),
                __('Membatasi menu tertentu ke role tertentu dilakukan di Matriks Akses Menu, bukan di sini.'),
            ],
        ],

        [
            'id' => 'menu',
            'title' => __('Menambah menu di sidebar'),
            'goal' => __('Menampilkan halaman baru di navigasi, atau menata ulang urutan menu yang ada.'),
            'permission' => 'menus.create',
            'featured' => true,
            'steps' => [
                ['kind' => 'menu', 'label' => __('Menu Management'), 'detail' => __('Grup menu di sidebar')],
                ['kind' => 'menu', 'label' => __('Tambah Menu'), 'detail' => __('Form menu baru'), 'route' => 'admin.menus.create'],
                ['kind' => 'form', 'label' => __('Isi judul & ikon'), 'detail' => __('Ikon bisa nama, satu karakter, atau emoji')],
                ['kind' => 'form', 'label' => __('Pilih Route name'), 'detail' => __('Selalu utamakan route name daripada URL mentah')],
                ['kind' => 'form', 'label' => __('Tentukan permission'), 'detail' => __('Kosongkan bila boleh dilihat semua yang login')],
                ['kind' => 'form', 'label' => __('Pilih menu induk & posisi'), 'detail' => __('Panel kanan — sidebar, topbar, atau footer')],
                ['kind' => 'action', 'label' => __('Atur urutan'), 'detail' => __('Seret pegangan di Daftar Menu, lalu Simpan urutan'), 'route' => 'admin.menus.index'],
                ['kind' => 'done', 'label' => __('Muncul di sidebar'), 'detail' => __('Bagi yang punya permission-nya')],
            ],
            'notes' => [
                __('Menu induk tanpa route dan URL berfungsi sebagai grup saja; bila seluruh anaknya tersembunyi, induknya ikut hilang.'),
                __('Tipe divider dan header otomatis dibuang bila tidak ada menu yang mengikutinya.'),
            ],
        ],

        [
            'id' => 'backup',
            'title' => __('Membuat & mengunduh backup'),
            'goal' => __('Mengamankan basis data sebelum melakukan perubahan besar.'),
            'permission' => 'backups.create',
            'steps' => [
                ['kind' => 'menu', 'label' => __('Sistem'), 'detail' => __('Grup menu di sidebar')],
                ['kind' => 'menu', 'label' => __('Backup'), 'detail' => __('Daftar arsip yang tersedia'), 'route' => 'admin.backups.index'],
                ['kind' => 'action', 'label' => __('Buat backup sekarang'), 'detail' => __('Menjalankan mysqldump seketika')],
                ['kind' => 'action', 'label' => __('Unduh'), 'detail' => __('Simpan berkas .sql di luar server')],
                ['kind' => 'done', 'label' => __('Aman'), 'detail' => __('Sistem menyimpan 10 arsip terbaru')],
            ],
            'notes' => [
                __('Backup harian pukul 02:00 sudah berjalan otomatis lewat scheduler.'),
                __('Pemulihan sengaja hanya lewat terminal — perintahnya tercantum di halaman Backup.'),
            ],
        ],

        [
            'id' => 'laporan',
            'title' => __('Memeriksa progres peserta'),
            'goal' => __('Melihat siapa yang aktif, materi mana yang selesai, dan kuis mana yang paling sulit.'),
            'permission' => 'reports.view',
            'steps' => [
                ['kind' => 'menu', 'label' => __('Laporan'), 'detail' => __('Grup menu di sidebar')],
                ['kind' => 'menu', 'label' => __('Progres Peserta'), 'detail' => __('XP, level, streak, materi selesai'), 'route' => 'admin.reports.progress'],
                ['kind' => 'form', 'label' => __('Saring per modul'), 'detail' => __('Atau cari nama peserta')],
                ['kind' => 'action', 'label' => __('Export CSV'), 'detail' => __('Untuk diolah lebih lanjut di spreadsheet')],
                ['kind' => 'menu', 'label' => __('Performa Kuis'), 'detail' => __('Rata-rata skor dan jumlah percobaan'), 'route' => 'admin.reports.quiz'],
                ['kind' => 'done', 'label' => __('Tindak lanjut'), 'detail' => __('Kuis dengan rata-rata rendah biasanya butuh pembahasan lebih baik')],
            ],
            'notes' => [
                __('Aktivitas menampilkan grafik pendaftaran 12 bulan dan modul paling banyak diselesaikan.'),
            ],
        ],
    ];

    $kinds = [
        'menu' => ['label' => __('Menu'), 'ring' => 'border-brand-300', 'chip' => 'bg-brand-100 text-brand-700 dark:bg-brand-500/20 dark:text-brand-300'],
        'action' => ['label' => __('Tombol'), 'ring' => 'border-amber-300', 'chip' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300'],
        'form' => ['label' => __('Isi'), 'ring' => 'border-slate-300', 'chip' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300'],
        'done' => ['label' => __('Hasil'), 'ring' => 'border-emerald-300', 'chip' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300'],
    ];
@endphp

@section('content')
    <x-page-header
        :title="__('Alur Aplikasi')"
        :description="__('Urutan langkah untuk pekerjaan yang paling sering dilakukan — menu mana dulu, lalu ke mana. Setiap langkah bertuliskan nama menu dan tombol yang sebenarnya.')"
    >
        <x-slot:actions>
            <a href="{{ route('admin.panduan') }}" class="btn-secondary">{{ __('Panduan konsep') }}</a>
        </x-slot:actions>
    </x-page-header>

    {{-- Legend --}}
    <div class="card mb-6 flex flex-wrap items-center gap-x-6 gap-y-2 p-4 text-xs">
        <span class="font-semibold text-slate-500">{{ __('Keterangan') }}</span>
        @foreach ($kinds as $meta)
            <span class="flex items-center gap-2">
                <span class="badge {{ $meta['chip'] }}">{{ $meta['label'] }}</span>
            </span>
        @endforeach
        <span class="ml-auto text-slate-400">
            {{ __('Langkah bergaris bawah bisa langsung diklik.') }}
        </span>
    </div>

    {{-- Quick jump --}}
    <nav class="card mb-6 p-4" aria-label="{{ __('Daftar alur') }}">
        <p class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-400">{{ __('Lompat ke alur') }}</p>
        <div class="flex flex-wrap gap-2">
            @foreach ($flows as $index => $flow)
                <a href="#{{ $flow['id'] }}"
                   class="inline-flex items-center gap-2 rounded-full border border-slate-300 px-3 py-1.5 text-sm font-medium transition hover:border-brand-400 hover:text-brand-700 dark:border-slate-700 dark:hover:text-brand-300">
                    <span class="font-mono text-xs text-slate-400">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                    {{ $flow['title'] }}
                </a>
            @endforeach
        </div>
    </nav>

    {{-- Flows --}}
    <div class="space-y-6">
        @foreach ($flows as $index => $flow)
            <section id="{{ $flow['id'] }}" class="card scroll-mt-24 p-5 md:p-6">

                <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono text-xs text-slate-400">
                                {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <h2 class="text-lg font-bold">{{ $flow['title'] }}</h2>
                            @if ($flow['featured'] ?? false)
                                <span class="badge bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">
                                    {{ __('sering dipakai') }}
                                </span>
                            @endif
                        </div>
                        <p class="mt-1.5 max-w-2xl text-sm text-slate-500">{{ $flow['goal'] }}</p>
                    </div>

                    <span class="shrink-0 rounded-lg bg-slate-50 px-3 py-1.5 dark:bg-slate-800/60">
                        <span class="block text-[10px] font-bold uppercase tracking-wide text-slate-400">{{ __('Butuh izin') }}</span>
                        <code class="text-xs text-amber-600 dark:text-amber-400">{{ $flow['permission'] }}</code>
                    </span>
                </div>

                {{-- The chain. Horizontal on wide screens, vertical on mobile. --}}
                <ol class="flex flex-col gap-0 md:flex-row md:flex-wrap md:gap-y-4">
                    @foreach ($flow['steps'] as $stepIndex => $step)
                        @php $meta = $kinds[$step['kind']]; @endphp

                        <li class="relative flex items-stretch md:flex-none">
                            {{-- Step card --}}
                            <div class="w-full rounded-lg border {{ $meta['ring'] }} bg-white p-3 md:w-52 dark:bg-slate-900">
                                <div class="mb-1.5 flex items-center gap-2">
                                    <span class="grid size-5 shrink-0 place-items-center rounded-full bg-slate-100 font-mono text-[10px] font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                        {{ $stepIndex + 1 }}
                                    </span>
                                    <span class="badge {{ $meta['chip'] }} text-[10px]">{{ $meta['label'] }}</span>
                                </div>

                                @if (! empty($step['route']) && Route::has($step['route']))
                                    <a href="{{ route($step['route']) }}"
                                       class="block text-sm font-semibold underline decoration-brand-400 decoration-dotted underline-offset-4 hover:text-brand-600">
                                        {{ $step['label'] }}
                                    </a>
                                @else
                                    <p class="text-sm font-semibold">{{ $step['label'] }}</p>
                                @endif

                                <p class="mt-1 text-xs leading-relaxed text-slate-500">{{ $step['detail'] }}</p>
                            </div>

                            {{-- Connector --}}
                            @unless ($loop->last)
                                {{-- vertical on mobile --}}
                                <span aria-hidden="true"
                                      class="absolute left-6 top-full h-4 w-px bg-slate-300 md:hidden dark:bg-slate-700"></span>
                                {{-- horizontal on desktop --}}
                                <span aria-hidden="true"
                                      class="hidden items-center px-2 text-slate-300 md:flex dark:text-slate-600">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5-5 5M6 12h12" />
                                    </svg>
                                </span>
                            @endunless

                            {{-- spacer so mobile steps don't touch --}}
                            @unless ($loop->last)
                                <span class="block h-4 md:hidden"></span>
                            @endunless
                        </li>
                    @endforeach
                </ol>

                @if (! empty($flow['notes']))
                    <div class="mt-5 border-t border-slate-200 pt-4 dark:border-slate-800">
                        <p class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-400">{{ __('Perlu diketahui') }}</p>
                        <ul class="space-y-1.5">
                            @foreach ($flow['notes'] as $note)
                                <li class="flex gap-2.5 text-sm text-slate-500">
                                    <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-brand-400"></span>
                                    {{ $note }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </section>
        @endforeach
    </div>

    <div class="card mt-6 flex flex-wrap items-center gap-4 bg-brand-50 p-5 dark:bg-brand-500/10">
        <x-icon name="help" class="size-6 shrink-0 text-brand-600" />
        <div class="min-w-0 flex-1">
            <p class="font-semibold">{{ __('Mencari penjelasan konsepnya, bukan langkahnya?') }}</p>
            <p class="mt-0.5 text-sm text-slate-500">
                {{ __('Halaman Panduan menjelaskan cara kerja menu dinamis, RBAC, dan kenapa modul baru tidak butuh kode.') }}
            </p>
        </div>
        <a href="{{ route('admin.panduan') }}" class="btn-primary">{{ __('Buka Panduan') }}</a>
    </div>
@endsection
