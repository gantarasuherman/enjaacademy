# Nihongo & English Academy

Platform belajar bahasa Jepang & Inggris dengan **panel admin yang sepenuhnya dinamis** — menu, role, permission, dan modul pembelajaran dikelola dari UI tanpa mengubah kode.

| Bagian | Stack | Lokasi |
|---|---|---|
| **Frontend peserta** | React 19 · TypeScript · Vite · Zustand · Tailwind v4 | `frontend/` |
| **Backend + Admin Panel** | Laravel 12 · PHP 8.4 · Blade · Alpine.js · Tailwind v4 | root |
| **Deployment** | **Laradock** · Nginx · MySQL 8 · Redis · Mailpit · phpMyAdmin | `laradock/` |

---

## 1. Menjalankan

### Prasyarat
Docker Desktop. Tidak perlu PHP, Composer, atau Node di host — semuanya
dijalankan di dalam container `workspace` milik Laradock.

### Struktur

```
d:\AI\
├── app/  routes/  database/  resources/   ← Laravel 12
├── frontend/                              ← React 19 SPA
├── public/app/                            ← hasil build SPA (dibuat make deploy-spa)
└── laradock/                              ← Laradock resmi
    ├── .env                               ← SEMUA port & versi service diatur di sini
    ├── docker-compose.yml
    └── nginx/sites/nihongo.conf           ← site config proyek ini
```

Laradock mount root proyek ke `/var/www` di dalam container.

### Sekali jalan

**Windows (PowerShell)** — tidak butuh `make`:

```powershell
Copy-Item .env.example .env
.\ld.ps1 install        # up + composer + key + migrate + seed + build assets
```

**Linux / macOS / WSL:**

```bash
cp .env.example .env
make install
```

Keduanya adalah pembungkus tipis yang identik. Tanpa keduanya, langsung lewat
Laradock:

```bash
cp .env.example .env
cd laradock
docker compose up -d nginx mysql redis phpmyadmin mailpit workspace

docker compose exec workspace composer install
docker compose exec workspace php artisan key:generate
docker compose exec workspace php artisan storage:link
docker compose exec workspace php artisan migrate --seed
docker compose exec workspace npm install && docker compose exec workspace npm run build
```

> **Build pertama memakan waktu.** Laradock meng-compile image `workspace` dan
> `php-fpm` dari sumber (PHP 8.4 + ekstensi + Node 22). Sekali jadi, `make up`
> berikutnya hanya butuh beberapa detik.

### Alamat

| Layanan | URL | Diatur di |
|---|---|---|
| Landing & panel admin | http://localhost:8000 | `NGINX_HOST_HTTP_PORT` |
| Panel admin langsung | http://localhost:8000/admin | — |
| SPA peserta (dev) | http://localhost:5174 | `WORKSPACE_VITE_PORT` |
| phpMyAdmin | http://localhost:8080 | `PMA_PORT` |
| Mailpit | http://localhost:8025 | `MAILPIT_HTTP_PORT` |
| MySQL (dari host) | 127.0.0.1:33061 | `MYSQL_PORT` |
| Redis (dari host) | 127.0.0.1:63791 | `REDIS_PORT` |

Semua nilai di kolom kanan ada di `laradock/.env` — satu tempat, tidak
terduplikasi di `.env` aplikasi.

### Catatan konfigurasi Laradock

- `APP_CODE_PATH_HOST=../` — Laradock mengambil kode dari direktori induknya.
- `nginx/sites/default.conf` dinonaktifkan (`.disabled`), digantikan
  `nihongo.conf` yang mengarah ke `/var/www/public` dan menambahkan aturan
  cache untuk aset SPA.
- Redis Laradock **berpassword** (`secret_redis`); `.env` aplikasi sudah
  disetel sesuai, jangan dikosongkan.
- MySQL dipatok ke `8.0` (default Laradock `8.4`) karena itu versi yang dipakai
  saat verifikasi.
- File instruksi agen bawaan Laradock (`CLAUDE.md`, `AGENTS.md`, `.cursorrules`,
  dst.) sudah dihapus dari salinan ini supaya tidak tertukar dengan instruksi
  proyekmu.

### Akun hasil seeder

| Email | Role | Akses |
|---|---|---|
| superadmin@nihongo.test | Super Admin | semua (lewat `Gate::before`) |
| admin@nihongo.test | Admin | seluruh panel kecuali hapus role |
| teacher@nihongo.test | Teacher | materi, kuis, laporan |
| student@nihongo.test | Student | hanya area belajar |

Kata sandi semuanya: `password`

---

## 2. Frontend peserta (React)

SPA berdiri sendiri dan **bisa jalan tanpa backend** — defaultnya membaca JSON di `frontend/src/data/`.

```bash
make spa-dev                # http://localhost:5174
# atau
cd frontend && npm install && npm run dev
```

### Beralih dari data dummy ke API Laravel

Satu baris di `frontend/.env`:

```env
VITE_DATA_SOURCE=api        # sebelumnya: mock
```

Seluruh service di `frontend/src/services/api/` sudah punya kedua implementasi
di balik signature yang sama, jadi tidak ada komponen yang perlu diubah:

```ts
export const learningService = {
    listModules: () => source(
        () => delay(modulesFromJson),                      // mock
        () => http.get('/learning/modules').then(unwrap),  // api
    ),
};
```

### Publikasikan SPA ke Laravel (produksi)

```bash
make deploy-spa             # build + salin frontend/dist → public/app
```

Setelah itu route peserta (`/dashboard`, `/belajar/...`, `/kuis/...`) dilayani
Laravel dari `public/app`. Selama `public/app` belum ada, route tersebut
mengarahkan ke Vite dev server.

### Isi aplikasi peserta

7 modul skill (Vocabulary, Grammar, Listening, Speaking, Reading, Writing,
Conversation), quiz engine dengan **7 tipe soal** (pilihan ganda, benar-salah,
ketik, isian, cocokkan, susun kata, menyimak), flashcard **spaced repetition
SM-2**, progress/XP/level/streak, achievement, leaderboard, bookmark,
certificate, PWA offline.

Web Speech API dipakai untuk skor pelafalan (Speaking & Conversation) dan
text-to-speech kosakata.

---

## 3. Panel admin

### Menu dinamis (inti sistem)

Tidak ada satu pun menu yang di-hardcode di Blade. Sidebar dirender dari tabel
`menus` secara rekursif:

```
User login
  → MenuBuilder ambil menu aktif untuk posisi (sidebar/topbar/footer)
  → filter: permission_name yang dimiliki user  AND  matriks menu_role
  → susun pohon parent-child (unlimited nesting)
  → buang cabang kosong, divider ganda, header menggantung
  → cache per (posisi × role signature)
```

Cache otomatis dibersihkan lewat observer setiap kali menu, role, atau
permission berubah — memakai **versioned key**, bukan cache tags, supaya bekerja
di semua cache driver.

Fitur: drag & drop reorder, icon picker, badge, permission-based visibility,
route name atau URL, target `_self`/`_blank`, tipe menu/divider/header/external.

### RBAC

Permission memakai format `{module}.{action}`. `Gate::before` memberi role
Super Admin akses penuh, sehingga ia tidak pernah tertinggal saat modul baru
menambah permission.

Dua matriks disediakan: **Role × Permission** dan **Role × Menu**.

### Tambah modul pembelajaran tanpa coding

Ini bukan klaim kosong — modul adalah *baris tabel*, bukan kelas PHP:

1. **Master Data → Bahasa** — buat bahasa (kalau belum ada)
2. **Master Data → Modul** — buat modul, biarkan *"Buat permission otomatis"* tercentang
   → sistem membuat `{prefix}.view/.create/.update/.delete`
3. **Matriks Permission** — centang `{prefix}.view` untuk role Student
4. **Konten Belajar → Materi** — isi materi (manual atau impor CSV)
5. **Menu Management** — buat menu ke route `peserta.learning.module` dengan
   parameter `module=slug-modul`, permission `{prefix}.view`

Selesai. Tanpa deploy, tanpa migrasi. Panduan lengkap tersedia di dalam aplikasi:
`/admin/panduan`.

---

## 4. Arsitektur

```
app/
├── DataTransferObjects/      DTO — memindahkan input tervalidasi ke service
├── Http/
│   ├── Controllers/
│   │   ├── Admin/            panel admin (Blade)
│   │   ├── Api/              Sanctum API untuk SPA
│   │   ├── Auth/             login, register, reset, verifikasi
│   │   └── SpaController     menyajikan build React
│   ├── Middleware/           SetLocale, TrackUserActivity, EnsureUserCanAccessAdmin
│   ├── Requests/             Form Request (validasi + otorisasi)
│   └── Resources/            API Resource
├── Models/                   Eloquent + Concerns (Auditable, HasSlug)
├── Observers/                MenuObserver (flush cache), AuditableObserver
├── Policies/                 BasePolicy memetakan CRUD → {prefix}.{action}
├── Repositories/
│   ├── Contracts/            interface
│   └── Eloquent/             implementasi + BaseRepository
└── Services/
    ├── Menu/                 MenuBuilder, MenuCache, MenuService
    ├── Rbac/                 RoleService, PermissionService
    ├── Learning/             LearningService, QuizService, FlashcardService
    ├── Gamification/         ProgressService, AchievementService
    ├── Audit/                AuditLogger
    ├── Setting/              SettingService
    └── System/               BackupService, ImportExportService
```

Alur: **Controller → Form Request → DTO → Service → Repository → Model**.
Controller tidak pernah menyentuh query, service tidak pernah menyentuh Request.

### Struktur route

```
routes/
├── web.php          orchestrator
├── auth.php         guest + verifikasi email
├── public.php       landing, tentang, kontak
├── peserta.php      route peserta → SPA React
├── api.php          Sanctum
└── modules/
    ├── master.php     bahasa, modul, achievement
    ├── learning.php   materi, kuis
    ├── report.php     progres, kuis, leaderboard, aktivitas
    └── system.php     audit log, backup
```

Setiap grup memakai pola `$prefix` yang konsisten:

```php
$prefix = 'menus';
Route::controller(MenuController::class)
    ->prefix($prefix)->name("$prefix.")
    ->group(function () use ($prefix) {
        Route::get('/', 'index')->name('index')->middleware("permission:$prefix.view");
        // ...
    });
```

---

## 5. Perintah

Windows pakai `.\ld.ps1 <task>`, Linux/macOS pakai `make <task>` — nama task
sama persis.

| Task | Fungsi |
|---|---|
| `help` | daftar semua task |
| `up` / `down` | start / stop stack Laradock |
| `build` | rebuild image workspace & php-fpm |
| `ps` / `logs` | status container / tail log nginx+php-fpm |
| `shell` / `tinker` | bash di workspace / tinker |
| `install` | setup awal lengkap |
| `fresh` | `migrate:fresh --seed` (destruktif) |
| `assets` | build asset panel admin |
| `spa-build` | build SPA React |
| `spa-dev` | dev server SPA (http://localhost:5174) |
| `deploy-spa` | build SPA → `public/app` |
| `cache-clear` | bersihkan config/route/view + cache menu |
| `backup` | backup database manual |
| `queue` / `schedule` | queue worker / scheduler |
| `test` / `lint` | test suite / Pint |

Semua task hanyalah pembungkus tipis; setara dengan menjalankan manual dari
dalam `laradock/`:

```bash
cd laradock
docker compose exec workspace php artisan menu:clear   # invalidasi cache navigasi
docker compose exec workspace php artisan backup:run   # backup mysqldump
```

### Queue & scheduler

Antrean Redis diproses oleh container `php-worker` milik Laradock:

```bash
cd laradock
docker compose up -d php-worker
```

Versi Laradock ini tidak menyertakan container scheduler khusus. Jadwal Laravel
(backup harian 02:00, pembersihan audit log mingguan) dijalankan dengan salah
satu cara berikut:

```bash
# Cara paling sederhana — biarkan berjalan di workspace
cd laradock && docker compose exec -d workspace php artisan schedule:work

# Atau tambahkan satu baris ke crontab host
* * * * * cd /path/ke/laradock && docker compose exec -T workspace php artisan schedule:run
```

---

## 6. Status verifikasi

Dijalankan sungguhan di **stack Laradock** (nginx → php-fpm 8.4 → MySQL 8 →
Redis), bukan sekadar asumsi:

| Item | Status |
|---|---|
| Stack Laradock | ✅ 8 container up: nginx, php-fpm, mysql, redis, workspace, phpmyadmin, mailpit, dind |
| PHP di container | ✅ 8.4.24 + pdo_mysql, redis, intl, bcmath, gd, zip, mbstring, exif, pcntl |
| Node di workspace | ✅ v22.23.2 |
| Koneksi MySQL & Redis | ✅ keduanya `connected` dari dalam container |
| 12 migrasi | ✅ jalan bersih |
| 7 seeder | ✅ 62 menu bersarang, 4 akun, 15 modul, konten JP/EN |
| Menu dinamis per role | ✅ Super Admin 54 · Teacher 41 · Student 25 item |
| Semua `route_name` di tabel menus | ✅ 62/62 resolve ke route nyata |
| **39 pemeriksaan HTTP lewat nginx** | ✅ **39/39 lulus** |
| — 29 halaman admin | ✅ 200 |
| — RBAC | ✅ Teacher 403 di roles/backups · Student 403 di seluruh `/admin` |
| — 5 deep link SPA | ✅ 200, bundle React asli terkirim |
| Halaman publik & auth | ✅ 200 |
| Endpoint reorder drag & drop | ✅ `{"message":"Menu order saved."}` |
| Sanctum API login | ✅ token terbit |
| Asset SPA lewat nginx | ✅ 200 + `Cache-Control: max-age=31536000` |
| phpMyAdmin & Mailpit | ✅ 200 |
| Pengiriman email → Mailpit | ✅ pesan diterima |
| Blade (40+ view) | ✅ `view:cache` sukses |
| Build asset panel admin | ✅ di dalam workspace |
| React `tsc -b` + build produksi | ✅ di dalam workspace, PWA precache 1,3 MB |
| Error di log selama pengujian | ✅ 0 |

Lima bug nyata ditemukan dan diperbaiki selama verifikasi:

1. **Slug menu bentrok** — dua menu berjudul "Pencapaian" (peserta & admin)
   menabrak unique index. Seeder sekarang men-dedupe slug.
2. **Student bisa membuka `/admin`** — `canAccessAdminPanel()` meloloskan siapa
   pun yang punya permission apa pun, padahal siswa memegang `hiragana.view`
   dan kawan-kawan. Kini hanya permission dari modul panel yang memberi akses.
3. **Deep link SPA hilang path-nya** dan path Laravel tidak cocok dengan React
   Router — produksi akan 404. URL kedua router kini identik (`/app/*`).
4. **`Route::controller` + method `locale`** — dengan ext-intl aktif (yang
   dipasang Laradock, tapi tidak ada di PHP host saya), string `'locale'`
   di-resolve ke kelas bawaan PHP `Locale`, dan seluruh aplikasi gagal boot.
   Method diganti `switchLocale`. Bug ini hanya muncul setelah pindah ke
   Laradock.
5. **`npm run build` SPA gagal** — `tsc -b` ikut mem-build `tsconfig.node.json`
   yang butuh `@types/node` untuk `vite.config.ts`. Sebelumnya saya hanya
   menjalankan `tsc -p tsconfig.app.json`, jadi celah ini lolos.

---

## 7. Catatan jujur tentang cakupan

- **API belum lengkap.** Yang sudah ada: auth (Sanctum), menu, dan alur
  learning inti. Endpoint vocabulary, grammar, flashcard, dan leaderboard baru
  ada di sisi klien. SPA berjalan penuh dengan `VITE_DATA_SOURCE=mock`
  (default), jadi ini tidak memblokir apa pun — tetapi `VITE_DATA_SOURCE=api`
  belum bisa dipakai untuk seluruh modul.
- **Audio listening masih placeholder.** Pemutar sudah menangani berkas yang
  belum ada dan menawarkan text-to-speech sebagai pengganti.
- **Belum ada automated test.** Verifikasi di atas dilakukan lewat smoke test
  HTTP manual, bukan PHPUnit/Vitest.
- **Modul Jepang ada di backend, bukan di SPA.** Seeder mengisi Hiragana,
  Katakana, Kanji, JLPT, dll., dan panel admin mengelolanya sepenuhnya. Aplikasi
  React yang dibangun pada tahap ini fokus ke Bahasa Inggris sesuai spesifikasi
  Tahap 1; layar peserta untuk modul Jepang belum dibuat.
