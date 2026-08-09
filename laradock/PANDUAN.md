# Panduan Laradock — Nihongo & English Academy

> File ini **bukan** terjemahan `README.md`/`README-ar.md`/`README-es.md`/`README-zh.md`
> di folder ini — file-file itu adalah dokumentasi umum proyek Laradock
> (upstream, jangan diubah supaya tidak bentrok saat Laradock di-update).
> `PANDUAN.md` ini khusus berisi **cara menjalankan Laradock untuk proyek
> Nihongo & English Academy**, berdasarkan langkah yang sudah benar-benar
> dicoba dan diverifikasi jalan — bukan asumsi.

## 🚀 Mulai cepat (untuk yang belum familiar Docker)

Cuma dua langkah, persis seperti menjalankan project pada umumnya:

```bash
# 1. Backend + database (jalan di Docker, sekali ini saja butuh Docker Desktop terbuka)
cd laradock
docker compose up -d          # tunggu sampai semua container "Running"/"Started"

# 2. Frontend (React) — di terminal terpisah, dari root proyek
cd frontend
npm install
npm run dev
```

Lalu buka:
- **http://localhost** — landing page & panel admin (login: lihat tabel "Akun
  hasil seeder" di bawah)
- **http://localhost:5173/app/** — aplikasi belajar (SPA React)

Itu saja. Tidak perlu menghafal nama service Docker satu per satu — file
`docker-compose.yml` di folder ini sudah dipangkas hanya berisi 8 service
yang dipakai proyek ini, jadi `docker compose up -d` tanpa embel-embel apa pun
sudah otomatis menyalakan semuanya (nginx, mysql, redis, phpmyadmin, mailpit,
workspace). Baru pertama kali? Ikuti "Setup pertama kali" di bawah dulu (isi
database masih kosong sampai migrate+seed dijalankan sekali).

Untuk mematikan semuanya lagi: `docker compose down` (dari dalam `laradock/`).

---

## ⚠️ Kondisi nyata vs `README.md` proyek (root)

`README.md` di root proyek (bagian "Catatan konfigurasi Laradock") menyebutkan
setup seperti ini aktif:

- vhost nginx khusus `laradock/nginx/sites/nihongo.conf`
- MySQL dipatok ke versi `8.0`
- Port kustom: nginx `8000`, phpMyAdmin `8080`, MySQL `33061`, Redis `63791`

**Status per checkout ini (sudah diverifikasi lewat `curl` sungguhan, bukan
asumsi):**

- ✅ **Vhost nginx sudah ada dan aktif** — `laradock/nginx/sites/nihongo.conf`
  dibuat, mengarah ke `/var/www/public`, dan sudah dicoba lewat
  `docker compose exec nginx nginx -t` + `nginx -s reload` + `curl` ke `/`,
  `/admin`, `/login`, dan `/api/login`, semuanya merespons benar. File ini
  **sengaja tidak masuk `.gitignore`** (ada pengecualian `!nihongo.conf` di
  `laradock/nginx/sites/.gitignore`) supaya ikut ter-clone dan otomatis aktif
  di komputer lain — tidak perlu dibuat ulang manual.
- ❌ **Port MASIH default Laradock, bukan port kustom di atas.**
  `laradock/.env` tidak berisi override port apa pun, jadi nginx tetap di
  port `80` (bukan `8000`), phpMyAdmin di `8081` (bukan `8080`), MySQL di
  `3306` (bukan `33061`), Redis di `6379` (bukan `63791`). Root README
  menyebutkan port ini sebagai *setup yang dituju* di lingkungan lain, tapi
  belum benar-benar di-override di `laradock/.env` proyek ini.
- ❔ Versi MySQL belum diverifikasi ulang di sesi ini.

Jadi: **aplikasi bisa langsung diakses lewat nginx tanpa langkah tambahan**
begitu `make up` selesai — cukup buka `http://localhost` (port `80`, bukan
`8000`). Tidak perlu lagi trik `artisan serve` manual (masih didokumentasikan
di bawah sebagai alternatif kalau suatu saat vhost-nya sengaja dimatikan).

---

## Prasyarat

Cuma butuh **Docker Desktop** berjalan. Tidak perlu install PHP, Composer,
atau Node di komputer — semuanya jalan di dalam container `workspace`.

---

## Setup pertama kali

Dari root proyek (bukan dari dalam folder `laradock/`):

```bash
# 1. Salin file environment aplikasi
cp .env.example .env

# 2. Nyalakan seluruh stack (nginx, mysql, redis, phpmyadmin, mailpit, workspace)
make up

# 3. Install dependency PHP di dalam container workspace
docker compose --project-directory laradock -f laradock/docker-compose.yml \
  exec -T workspace composer install --no-interaction

# 4. Generate APP_KEY dan symlink storage
docker compose --project-directory laradock -f laradock/docker-compose.yml \
  exec -T workspace php artisan key:generate --force
docker compose --project-directory laradock -f laradock/docker-compose.yml \
  exec -T workspace php artisan storage:link
```

> `make install` sebenarnya membungkus langkah 2–4 plus migrate/seed/build
> asset jadi satu perintah — tapi lihat catatan kredensial database di bawah
> **sebelum** menjalankan migrate, karena default Laradock kemungkinan besar
> tidak cocok dengan `.env` proyek ini.

### Kredensial database — bagian paling sering bikin gagal

`.env.example` proyek ini mengasumsikan database `nihongo_english` dengan
user `laravel`/`secret`:

```
DB_DATABASE=nihongo_english
DB_USERNAME=laravel
DB_PASSWORD=secret
```

Tapi **default Laradock** (di `laradock/mysql/defaults.env`, tidak
di-override oleh `laradock/.env` proyek ini) membuat database `default`
dengan user `default`/`secret` dan root `root`/`root`. Kalau langsung
`migrate`, akan muncul error:

```
SQLSTATE[HY000] [1045] Access denied for user 'laravel'@'...' (using password: YES)
```

**Solusi** — buat database & user yang benar secara manual, sekali saja,
lewat root MySQL:

```bash
docker compose --project-directory laradock -f laradock/docker-compose.yml \
  exec -T mysql mysql -uroot -proot -e "
CREATE DATABASE IF NOT EXISTS nihongo_english CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'laravel'@'%' IDENTIFIED BY 'secret';
GRANT ALL PRIVILEGES ON nihongo_english.* TO 'laravel'@'%';
FLUSH PRIVILEGES;
"
```

Baru setelah itu jalankan migrate & seed:

```bash
docker compose --project-directory laradock -f laradock/docker-compose.yml \
  exec -T workspace php artisan migrate --force
docker compose --project-directory laradock -f laradock/docker-compose.yml \
  exec -T workspace php artisan db:seed --force
```

### 🔴 PENTING: folder data MySQL bisa dipakai bersama proyek lain

Laradock menyimpan data MySQL di host, bukan di dalam container, lewat
`DATA_PATH_HOST` (default `~/.laradock/data`). **Kalau di komputermu ada
proyek Laravel/Laradock lain yang pernah dijalankan**, folder ini bisa saja
sudah berisi database proyek lain juga.

Sebelum menjalankan perintah yang mereset data (`docker compose down -v`,
menghapus isi `~/.laradock/data/mysql`, dsb.), **cek dulu** database apa saja
yang ada:

```bash
docker compose --project-directory laradock -f laradock/docker-compose.yml \
  exec -T mysql mysql -uroot -proot -e "SHOW DATABASES;"
```

Kalau ada database selain `nihongo_english`/`default`/`information_schema`/
`mysql`/`performance_schema`/`sys`, itu kemungkinan milik proyek lain —
**jangan dihapus.**

### Terakhir, build asset admin panel

```bash
docker compose --project-directory laradock -f laradock/docker-compose.yml \
  exec -T workspace npm install --no-audit --no-fund
docker compose --project-directory laradock -f laradock/docker-compose.yml \
  exec -T workspace npm run build
```

Atau lebih singkat: `make assets`.

### Akses FE setelah build selesai

`npm run build` di atas menghasilkan file statis di `public/build/`, dan
vhost nginx (`laradock/nginx/sites/nihongo.conf`, sudah tersedia di repo ini)
langsung mengarah ke `public/` — **tidak perlu langkah tambahan**, langsung
buka **http://localhost** (port `80`).

Kalau vhost-nya belum ter-load (mis. baru saja membuat/mengubah file
`.conf`), reload nginx sekali:

```bash
docker compose --project-directory laradock -f laradock/docker-compose.yml \
  exec -T nginx nginx -t && \
docker compose --project-directory laradock -f laradock/docker-compose.yml \
  exec -T nginx nginx -s reload
```

Detail lengkap & alternatif `artisan serve` (kalau vhost sengaja dimatikan)
ada di bagian "Mengakses aplikasi lewat browser" di bawah.

---

## Alamat service (default Laradock, belum di-custom)

Port ini didapat dari `make ps` — bukan dari tabel di root README, yang
menyebut port lain (lihat peringatan di atas).

| Layanan | Port host | Catatan |
|---|---|---|
| **nginx (app utama)** | **`80`** | **vhost `nihongo.conf` aktif → buka `http://localhost` langsung** |
| nginx (https, varnish) | `443`, `81` | tidak dikonfigurasi di proyek ini |
| phpMyAdmin | `8081` | login pakai `root` / `root`, atau `laravel` / `secret` |
| Mailpit (SMTP / UI) | `1125` / `8125` | UI web di port `8125` |
| MySQL (dari host) | `3306` | |
| Redis (dari host) | `6379` | |
| workspace → port 8000 container | `8001` | alternatif sementara, lihat bawah |
| SPA React (dev, Vite) | `5173` | jalankan `make spa-dev` dulu, lihat bawah |

### Mengakses backend/admin lewat browser

`laradock/nginx/sites/nihongo.conf` sudah ada dan aktif di repo ini — begitu
`make up`/`make install` selesai, admin panel & API langsung bisa diakses:

```
http://localhost/          → landing + panel admin
http://localhost/admin     → login admin
http://localhost/api/...   → API Sanctum untuk SPA
```

Tidak ada langkah tambahan. Kalau nginx sempat direstart dari scratch dan
vhost-nya entah kenapa tidak ter-load, cek dan reload manual:

```bash
docker compose --project-directory laradock -f laradock/docker-compose.yml \
  exec -T nginx nginx -t     # validasi syntax
docker compose --project-directory laradock -f laradock/docker-compose.yml \
  exec -T nginx nginx -s reload
```

**Alternatif sementara** (kalau vhost sengaja dimatikan/dihapus untuk
eksperimen) — jalankan server dev bawaan Laravel di dalam container:

```bash
docker compose --project-directory laradock -f laradock/docker-compose.yml \
  exec -d workspace php artisan serve --host=0.0.0.0 --port=8000
```

Lalu buka `http://localhost:8001`. Hentikan lagi dengan:

```bash
docker compose --project-directory laradock -f laradock/docker-compose.yml \
  exec -T workspace pkill -f "artisan serve"
```

### Mengakses SPA peserta (React) lewat browser

Beda dari backend — SPA punya dev server sendiri (Vite), tidak butuh vhost
nginx maupun `artisan serve`. Nyalakan dengan:

```bash
make spa-dev
```

atau langsung lewat docker compose:

```bash
docker compose --project-directory laradock -f laradock/docker-compose.yml \
  exec -d workspace bash -lc 'cd /var/www/frontend && npm install --no-audit --no-fund && npm run dev -- --host 0.0.0.0'
```

Lalu buka **http://localhost:5173/app/** (bukan port `5174` — itu salah
ketik lama yang sudah diperbaiki; port sebenarnya `5173`, sesuai
`WORKSPACE_VITE_PORT` default Laradock dan `server.port` di
`frontend/vite.config.ts`). Prefix `/app/` wajib — dikonfigurasi lewat
`base: '/app/'` di `vite.config.ts` supaya URL asetnya sama persis antara
dev dan produksi (`public/app/`). Contoh: `http://localhost:5173/app/learning`.

Root `/` tanpa prefix akan redirect (302) ke `/app/...`, jadi lebih baik
langsung pakai `/app/` dari awal.

> SPA ini **bisa jalan tanpa backend sama sekali** — defaultnya membaca data
> dummy dari `frontend/src/data/`. Lihat bagian "Frontend peserta (React)" di
> root `README.md` untuk cara menyambungkannya ke API Laravel sungguhan.

---

## Perintah harian (lewat `make`, dari root proyek)

| Perintah | Fungsi |
|---|---|
| `make up` | Nyalakan stack (nginx, mysql, redis, phpmyadmin, mailpit, workspace) |
| `make down` | Matikan stack |
| `make restart` | Restart semua service |
| `make ps` | Lihat status container |
| `make logs` | Tail log nginx & php-fpm |
| `make shell` | Masuk shell `bash` di container workspace |
| `make tinker` | Buka `php artisan tinker` |
| `make migrate` | Jalankan migrasi yang tertunda |
| `make seed` | Jalankan ulang seeder |
| `make fresh` | **Destruktif** — drop semua tabel, migrate ulang, seed ulang |
| `make backup` | Backup database (`php artisan backup:run`) |
| `make test` | Jalankan test suite |
| `make lint` | Rapikan kode PHP dengan Pint |
| `make assets` | Install & build asset admin panel (Blade/Alpine/Tailwind) |
| `make spa-dev` | Jalankan dev server React SPA (port host `5173`) |
| `make spa-build` | Build SPA React untuk produksi |
| `make deploy-spa` | Build SPA lalu publikasikan ke `public/app` |
| `make queue` | Nyalakan queue worker (`php-worker`) |
| `make schedule` | Jalankan Laravel scheduler di background |

Pengguna Windows PowerShell pakai `.\ld.ps1 <perintah-yang-sama>` — lihat
`ld.ps1` di root proyek, isinya identik dengan `Makefile` ini.

---

## Akun hasil seeder

| Email | Role |
|---|---|
| `superadmin@nihongo.test` | Super Admin |
| `admin@nihongo.test` | Admin |
| `teacher@nihongo.test` | Teacher |
| `student@nihongo.test` | Student |

Password semuanya: `password`.

---

## Troubleshooting

**"Access denied for user 'laravel'@..."**
→ Database/user belum dibuat. Lihat bagian "Kredensial database" di atas.

**Build pertama (`docker compose up`) lama sekali**
→ Normal. Laradock meng-compile image `workspace` & `php-fpm` dari sumber
(PHP + ekstensi + Node) saat pertama kali. Build berikutnya tinggal
beberapa detik karena sudah pakai image yang ter-cache.

**`make up` bentrok port ("port is already allocated")**
→ Ada service lain di komputer yang sudah pakai port itu (mis. MySQL lokal
lewat Homebrew, atau Laradock proyek lain yang jalan bersamaan). Set
override port di `laradock/.env` (lihat komentar `MYSQL_PORT` dsb. di file
itu), atau matikan service yang bentrok.

**Halaman blank / 404 saat buka lewat nginx**
→ Vhost `nihongo.conf` seharusnya sudah aktif secara default (lihat bagian
"Mengakses backend/admin lewat browser" di atas). Kalau tetap blank/404,
jalankan `docker compose ... exec -T nginx nginx -t` untuk cek syntax, lalu
`nginx -s reload`. Pastikan juga `laradock/nginx/sites/nihongo.conf` benar
ter-clone (bukan ke-strip oleh `.gitignore` versi lama — file ini punya
pengecualian eksplisit `!nihongo.conf`).

**(Sudah diperbaiki) `docker compose up -d` tanpa nama service dulu gagal
dengan puluhan error "context canceled" / "pull access denied for
mailu/clamav" dsb.** — `laradock/docker-compose.yml` sebelumnya adalah
katalog resmi **seluruh** 100+ service opsional Laradock (mailu, kafka,
neo4j, vllm, onedev, dst.), sehingga `docker compose up -d` tanpa nama service
mencoba menarik & menyalakan semuanya sekaligus, termasuk image yang sudah
tidak ada di registry. File ini sekarang **sudah dipangkas** hanya berisi 8
service yang benar-benar dipakai proyek ini (nginx, mysql, redis, phpmyadmin,
mailpit, workspace, php-fpm, docker-in-docker), jadi:

```bash
cd laradock
docker compose up -d
```

sekarang **cukup dan aman** — tidak perlu lagi menyebutkan nama service
manual. Katalog lengkap 100+ service yang asli masih ada di
`laradock/docker-compose.full.yml` kalau suatu saat butuh salah satunya
(pakai `docker compose -f docker-compose.full.yml up -d <nama-service>`).

**`Plugin 'mysql_native_password' is not loaded` — root/laravel tidak bisa
login ke MySQL sama sekali setelah container mysql di-recreate**
→ Akun yang sudah ada di data directory ini dibuat dengan plugin
`mysql_native_password` (default MySQL 8.0), tetapi `MYSQL_VERSION` di
`laradock/mysql/defaults.env` adalah `8.4`, dan MySQL 8.4 **menonaktifkan**
plugin itu secara default. Begitu container mysql di-recreate (mis. lewat
`docker compose up -d` tanpa nama service, atau `make fresh`), akun lama jadi
tidak bisa login sama sekali — bukan cuma proyek ini, root pun ikut terkunci.
Sudah diperbaiki secara permanen lewat `command: --mysql-native-password=ON`
di `laradock/mysql/compose.yml` (memuat ulang plugin-nya, tidak menyentuh
data). Kalau tetap muncul, pastikan `laradock/mysql/compose.yml` masih punya
baris `command` itu, lalu `docker compose up -d mysql` untuk recreate ulang.

**Butuh reset total (hati-hati)**
→ `docker compose --project-directory laradock -f laradock/docker-compose.yml down`
mematikan container tapi **tidak** menghapus data MySQL di host (data
disimpan di `~/.laradock/data`, lihat peringatan di atas soal folder yang
dipakai bersama). Jangan pernah menghapus folder itu tanpa mengecek dulu
apakah ada database proyek lain di dalamnya.
