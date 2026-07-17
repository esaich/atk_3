# ATK Laravel — Sistem Manajemen Inventaris & Pengadaan Barang

Aplikasi berbasis web untuk mengelola stok barang (ATK/inventaris), pencatatan barang masuk & keluar, supplier, pembayaran, pengadaan barang, serta alur permintaan barang antar-divisi dengan sistem persetujuan (approval). Dibangun dengan **Laravel 11**.

---

## Daftar Isi

1. [Fitur Utama](#1-fitur-utama)
2. [Teknologi yang Digunakan](#2-teknologi-yang-digunakan)
3. [Arsitektur & Cara Kerja Aplikasi](#3-arsitektur--cara-kerja-aplikasi)
4. [Struktur Basis Data](#4-struktur-basis-data)
5. [Instalasi](#5-instalasi)
6. [Konfigurasi Setelah Clone](#6-konfigurasi-setelah-clone)
7. [Menjalankan Aplikasi](#7-menjalankan-aplikasi)
8. [Akun Default (Seeder)](#8-akun-default-seeder)
9. [Panduan Penggunaan Aplikasi](#9-panduan-penggunaan-aplikasi)
10. [Struktur Folder Proyek](#10-struktur-folder-proyek)
11. [Troubleshooting Umum](#11-troubleshooting-umum)
12. [Metodologi Pengembangan (Waterfall vs SDLC)](#12-metodologi-pengembangan-waterfall-vs-sdlc)

---

## 1. Fitur Utama

**Untuk role Admin:**
- Dashboard admin
- Manajemen data barang (CRUD) + cetak daftar barang ke PDF
- Pencatatan barang masuk (dari supplier) + cetak PDF
- Pencatatan barang keluar (hasil approval permintaan divisi) + cetak PDF
- Manajemen data supplier (CRUD)
- Manajemen pembayaran ke supplier, termasuk cetak invoice PDF
- Pengadaan barang: pengajuan kebutuhan barang ke supplier, dikelompokkan per supplier & tanggal, cetak PDF per item maupun per kelompok
- Kelola permintaan barang dari seluruh divisi: **approve / reject**, dikelompokkan per tanggal
- Manajemen akun user divisi (CRUD)
- Pengaturan akun admin (ubah nama/password)

**Untuk role Divisi:**
- Dashboard divisi
- Mengajukan permintaan barang ke admin
- Melihat status permintaan (pending / disetujui / ditolak), dikelompokkan per tanggal
- Pengaturan akun sendiri (ubah nama/password)

**Autentikasi & Otorisasi:**
- Login dengan email & password
- Middleware berbasis role (`admin` dan `divisi`) — setiap grup route hanya bisa diakses oleh role yang sesuai, akses tanpa izin akan mendapat HTTP 403.

---

## 2. Teknologi yang Digunakan

| Komponen | Teknologi |
|---|---|
| Backend Framework | Laravel 11 (PHP ^8.2) |
| Database | MySQL (default XAMPP) — bisa juga SQLite |
| Frontend Build | Vite + Tailwind CSS |
| Template Engine | Blade |
| PDF Generator | `barryvdh/laravel-dompdf` |
| Rendering HTML→PDF alternatif | `spatie/browsershot` (butuh Node.js + Puppeteer/Chrome jika dipakai) |
| Monitoring Kesehatan App | `spatie/laravel-health` |
| Testing | PHPUnit |
| Code Quality | Laravel Pint, PHPStan (Larastan), Enlightn |

---

## 3. Arsitektur & Cara Kerja Aplikasi

Aplikasi ini menggunakan pola **MVC (Model-View-Controller)** standar Laravel:

- **Model** (`app/Models`) — representasi tabel database & relasi antar data (Eloquent ORM).
- **Controller** (`app/Http/Controllers`) — logika bisnis, menangani request dan mengembalikan view/response.
- **View** (`resources/views`) — tampilan Blade, dipisah per modul (`admin/`, `divisi/`, `barang/`, `payment/`, dst).

### Alur Request

```
Browser -> routes/web.php -> Middleware (auth + role) -> Controller -> Model (Eloquent/MySQL) -> View (Blade) -> Response
```

### Alur Kerja Bisnis (Business Flow)

**A. Alur Barang Masuk (Stok Bertambah)**
```
Admin input Barang Masuk (pilih Barang + Supplier + jumlah + harga)
   -> Stok pada tabel `barang` bertambah otomatis
   -> Bisa dicetak sebagai bukti/laporan PDF
```

**B. Alur Permintaan & Persetujuan Barang (Stok Berkurang)**
```
1. User Divisi membuat "Permintaan Barang" (pilih barang + jumlah + alasan)
      -> status default: pending

2. Admin membuka menu "Permintaan", meninjau per tanggal
      -> Admin klik Approve  -> status jadi "disetujui" + otomatis tercatat
         sebagai "Barang Keluar" (stok pada tabel `barang` berkurang)
      -> Admin klik Reject   -> status jadi "ditolak", stok tidak berubah

3. Divisi bisa memantau status permintaannya sendiri di dashboard
```

**C. Alur Pengadaan Barang (Perencanaan Belanja ke Supplier)**
```
Admin membuat pengajuan Pengadaan Barang (nama barang, jumlah, satuan, supplier tujuan)
   -> Data dikelompokkan per Supplier + tanggal pengajuan
   -> Bisa dicetak per item atau per kelompok (PDF) sebagai dokumen pengajuan ke supplier
```

**D. Alur Pembayaran**
```
Admin mencatat Payment ke Supplier (total harga, tanggal bayar, keterangan)
   -> Terhubung ke data Barang Masuk dari supplier terkait
   -> Bisa dicetak sebagai invoice PDF
```

### Role & Middleware

`app/Http/Middleware/RoleMiddleware.php` mengecek:
1. User sudah login (`Auth::check()`) — jika belum, redirect ke `/login`.
2. Role user (`admin` / `divisi`) sesuai dengan role yang diizinkan pada route — jika tidak sesuai, response **403 Forbidden**.

Didefinisikan di `routes/web.php` dengan pola:
```php
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () { ... });
Route::middleware(['auth', RoleMiddleware::class . ':divisi'])->group(function () { ... });
```

---

## 4. Struktur Basis Data

| Tabel | Fungsi | Relasi Utama |
|---|---|---|
| `users` | Data akun (admin & divisi), punya kolom `role` (enum: `admin`, `divisi`) | — |
| `supplier` | Data pemasok barang | hasMany `barang`, `payments`, `barang_masuk`, `pengadaan_barang` |
| `barang` | Master data barang + stok | hasMany `permintaan_barang`, `barang_masuk`, `barang_keluar` |
| `barang_masuk` | Transaksi barang masuk dari supplier | belongsTo `barang`, `supplier` |
| `barang_keluar` | Transaksi barang keluar (hasil approval) | belongsTo `permintaan_barang`, `barang` |
| `permintaan_barang` | Permintaan barang dari divisi, status: `pending`/`disetujui`/`ditolak` | belongsTo `users`, `barang`; hasOne `barang_keluar` |
| `pengadaan_barang` | Pengajuan pengadaan barang ke supplier | belongsTo `supplier` |
| `payments` | Pembayaran ke supplier | belongsTo `supplier`; hasMany `barang_masuk` |

### Diagram Relasi (ringkas)

```
users (1) ----< (N) permintaan_barang (N) >---- (1) barang
                        |  (1)
                        v
                  barang_keluar (N) >---- (1) barang

supplier (1) --< barang_masuk >-- (1) barang
supplier (1) --< payments
supplier (1) --< pengadaan_barang
```

---

## 5. Instalasi

### Prasyarat

- **XAMPP** (Apache + MySQL) — untuk kebutuhan database MySQL
- **PHP >= 8.2** (cek dengan `php -v`; kalau XAMPP masih PHP 8.0/8.1, upgrade dulu)
- **Composer** — https://getcomposer.org/download/
- **Node.js & npm** — https://nodejs.org/ (untuk build asset Vite/Tailwind)
- **Git** (opsional, kalau clone ulang)

### Langkah Instalasi

```bash
# 1. Clone repository (skip jika sudah punya foldernya)
git clone https://github.com/zah009/atk_laravel.git
cd atk_laravel

# 2. Install dependency PHP (Composer)
composer install

# 3. Install dependency frontend (npm)
npm install
```

> **Catatan Windows:** jika `composer install` gagal dengan error `Resource temporarily unavailable` saat menulis file di folder `vendor`, itu biasanya karena Windows Defender/Antivirus mengunci file. Tambahkan folder project ke exclusion list Windows Defender, lalu jalankan ulang `composer install`.

---

## 6. Konfigurasi Setelah Clone

### 6.1 Buat file `.env`

```bash
cp .env.example .env
```

### 6.2 Generate Application Key

```bash
php artisan key:generate
```

### 6.3 Buat Database di phpMyAdmin

Buka `http://localhost/phpmyadmin`, buat database baru, misal: `atk_laravel_db`.

### 6.4 Atur Koneksi Database di `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=atk_laravel_db
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan `DB_USERNAME`/`DB_PASSWORD` jika MySQL XAMPP kamu memakai kredensial berbeda dari default.

### 6.5 Atur `APP_URL`

- Jika menjalankan lewat `php artisan serve` -> `APP_URL=http://127.0.0.1:8000`
- Jika menjalankan lewat Apache XAMPP (folder di `htdocs`) -> `APP_URL=http://localhost/atk_laravel/public` (sesuaikan nama foldernya)

### 6.6 Jalankan Migration

```bash
php artisan migrate
```

Perintah ini akan membuat seluruh tabel (lihat [Struktur Basis Data](#4-struktur-basis-data)).

### 6.7 Jalankan Seeder (isi data awal)

```bash
php artisan db:seed
```

> `DatabaseSeeder.php` memanggil `DummyUsersSeeder` yang membuat 3 akun awal — lihat [Akun Default](#8-akun-default-seeder).

Atau sekaligus migrate + seed dari kosong:
```bash
php artisan migrate:fresh --seed
```
Peringatan: `migrate:fresh` akan **menghapus semua tabel** sebelum membuat ulang — pastikan tidak ada data penting yang hilang.

### 6.8 Build Asset Frontend

```bash
npm run build
```
Untuk mode development dengan auto-reload:
```bash
npm run dev
```

---

## 7. Menjalankan Aplikasi

**Opsi A — PHP built-in server (tidak perlu Apache aktif, cukup MySQL):**
```bash
php artisan serve
```
Buka `http://127.0.0.1:8000`

**Opsi B — Lewat Apache XAMPP:**
1. Pastikan folder project ada di `C:\xampp\htdocs\atk_laravel`
2. Nyalakan **Apache** dan **MySQL** di XAMPP Control Panel
3. Buka `http://localhost/atk_laravel/public`

> Document root harus mengarah ke folder `public/`, karena semua request Laravel masuk lewat `public/index.php`. Untuk menghindari `/public` di URL, bisa setup Virtual Host yang document root-nya langsung ke folder `public`.

---

## 8. Akun Default (Seeder)

Setelah `php artisan db:seed`, tersedia akun berikut:

| Nama | Role | Email | Password |
|---|---|---|---|
| Admin | `admin` | admin@gmail.com | admin123 |
| Divisi Keuangan | `divisi` | keuangan@gmail.com | admin123 |
| Divisi Perdagangan | `divisi` | perdagangan@gmail.com | admin123 |

Login admin akan diarahkan ke `/admin`, login divisi diarahkan ke `/divisi`.

---

## 9. Panduan Penggunaan Aplikasi

### Sebagai Admin

1. **Login** di `/login` dengan akun admin.
2. **Kelola Master Barang** (`/barang`): tambah/edit/hapus barang, lihat stok, unduh daftar barang PDF.
3. **Catat Barang Masuk** (`/barang-masuk`): input barang yang diterima dari supplier — stok otomatis bertambah.
4. **Kelola Supplier** (`/supplier`): tambah/edit/hapus data pemasok.
5. **Kelola Pengadaan Barang** (`/pengadaan`): buat pengajuan kebutuhan barang ke supplier tertentu, lihat rekap per supplier & tanggal, cetak PDF.
6. **Proses Permintaan Divisi** (`/admin/permintaan`): tinjau permintaan barang yang masuk (dikelompokkan per tanggal) -> klik **Setujui** (stok otomatis berkurang & tercatat sebagai barang keluar) atau **Tolak**.
7. **Lihat Riwayat Barang Keluar** (`/admin/barang-keluar`): rekap seluruh barang yang sudah dikeluarkan, bisa dicetak PDF.
8. **Kelola Pembayaran** (`/payment`): catat pembayaran ke supplier, unduh invoice PDF.
9. **Kelola Akun Divisi** (`/admin/divisi`): tambah/edit/hapus user divisi.
10. **Pengaturan Akun** (`/admin/settings`): ubah nama/password akun admin sendiri.

### Sebagai Divisi

1. **Login** di `/login` dengan akun divisi.
2. **Dashboard** (`/divisi`): ringkasan.
3. **Ajukan Permintaan Barang** (`/divisi/permintaan-barang`): pilih barang, jumlah, dan alasan permintaan.
4. **Pantau Status Permintaan**: lihat status `pending` / `disetujui` / `ditolak`, dikelompokkan per tanggal pengajuan.
5. **Pengaturan Akun** (`/divisi/settings`): ubah nama/password akun sendiri.

### Logout

Tersedia di semua role melalui tombol logout (route `POST /logout`), akan menghapus sesi dan redirect ke halaman login.

---

## 10. Struktur Folder Proyek

```
atk_laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Logika bisnis tiap modul
│   │   └── Middleware/      # RoleMiddleware untuk otorisasi role
│   └── Models/               # Eloquent model (Barang, Supplier, dst)
├── config/                   # File konfigurasi Laravel
├── database/
│   ├── migrations/           # Skema tabel database
│   └── seeders/               # Data awal (DummyUsersSeeder, dll)
├── public/                   # Document root (entry point index.php, asset build)
├── resources/
│   ├── views/                # Blade template, dipisah per modul & role
│   ├── css/, js/             # Sumber asset sebelum di-build Vite
├── routes/
│   └── web.php                # Definisi seluruh route aplikasi
├── .env.example               # Contoh konfigurasi environment
└── composer.json / package.json
```

---

## 11. Troubleshooting Umum

| Masalah | Penyebab | Solusi |
|---|---|---|
| `require(vendor/autoload.php): Failed to open stream` | Belum `composer install` | Jalankan `composer install` |
| `composer install` gagal `Resource temporarily unavailable` | File dikunci Antivirus/Windows Defender | Exclude folder project dari Windows Defender, ulangi command |
| Login gagal padahal password benar | User belum ada di database (seeder belum dijalankan/salah class) | Pastikan `DatabaseSeeder.php` memanggil `DummyUsersSeeder`, lalu `php artisan db:seed` |
| Error koneksi database | Kredensial `.env` salah, atau MySQL XAMPP belum aktif | Cek `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`; nyalakan MySQL di XAMPP |
| Asset CSS/JS tidak muncul | Belum `npm install` / `npm run build` | Jalankan `npm install && npm run build` |
| Error terkait `APP_KEY` kosong | Belum generate key | Jalankan `php artisan key:generate` |

---

## 12. Metodologi Pengembangan: Agile

Catatan konsep: **SDLC (*Software Development Life Cycle*)** adalah konsep payung tahapan siklus hidup pengembangan software (analisis -> desain -> implementasi -> pengujian -> deployment -> pemeliharaan). **Agile** adalah salah satu **model/pendekatan di dalam SDLC** — bedanya dengan Waterfall, tahapan-tahapan itu tidak dikerjakan berurutan sekali jalan, melainkan **diulang dalam siklus-siklus kecil (iterasi/sprint)**, satu iterasi untuk satu atau beberapa fitur, sehingga bisa cepat diuji dan direvisi.

### Kenapa Agile sesuai untuk proyek ini

Jejak pengembangan yang tercermin dari riwayat migration mendukung pola kerja iteratif, bukan linear satu arah:

| Bukti di Kode | Menunjukkan |
|---|---|
| `2025_06_02_..._change_default_stok_on_barang_table` | Revisi struktur data **setelah** fitur awal berjalan → siklus umpan balik & penyesuaian |
| `2025_06_25_..._change_barang_masuk_payment_id_to_supplier_id_table` | Perubahan relasi tabel di tengah jalan (dari `payment_id` ke `supplier_id`) → requirement berkembang saat development |
| `2025_07_14_..._remove_supplier_id_and_keterangan_from_barang_table` | Penyederhanaan skema setelah dipakai → hasil evaluasi/testing iterasi sebelumnya |
| `2025_07_22_..._create_pengadaan_barang_table` | Modul **Pengadaan Barang** ditambahkan belakangan, terpisah dari modul inti (Barang, Supplier, Permintaan) | → penambahan fitur bertahap (incremental) |

Pola "fitur inti dulu → dipakai → dievaluasi → direvisi/ditambah fitur baru" ini persis prinsip Agile: *"working software over comprehensive upfront design"* dan *"responding to change"*.

### Tahapan Agile (Scrum) yang Direkomendasikan untuk Didokumentasikan

1. **Product Backlog** — daftar seluruh kebutuhan fitur, contoh untuk proyek ini:
   - Autentikasi & manajemen role (admin/divisi)
   - Master data barang & supplier
   - Transaksi barang masuk
   - Alur permintaan & approval barang (barang keluar)
   - Modul pengadaan barang
   - Modul pembayaran & invoice PDF
2. **Sprint Planning** — backlog dipecah ke beberapa sprint (mis. 1 sprint = 1–2 minggu), diprioritaskan dari fitur inti ke fitur pendukung.
3. **Sprint Contoh Pemetaan ke Fitur Proyek Ini:**

   | Sprint | Fokus Fitur | Output |
   |---|---|---|
   | 1 | Autentikasi, role admin/divisi | Login, `RoleMiddleware` |
   | 2 | Master data | CRUD `Barang`, `Supplier` |
   | 3 | Transaksi masuk | `BarangMasuk` + update stok |
   | 4 | Alur permintaan | `PermintaanBarang` + approval -> `BarangKeluar` |
   | 5 | Pengadaan & Pembayaran | `PengadaanBarang`, `Payment` + cetak PDF |
   | 6 | Penyempurnaan | Perbaikan skema (migration revisi), pengujian, laporan |

4. **Daily Stand-up** *(jika tim > 1 orang)* — sinkronisasi progres harian singkat.
5. **Sprint Review** — demo fitur yang selesai di akhir sprint ke pembimbing/stakeholder.
6. **Sprint Retrospective** — evaluasi apa yang perlu diperbaiki di sprint berikutnya (tercermin dari migration-migration revisi di atas).
7. **Iterasi berulang** hingga seluruh backlog selesai, dengan kemungkinan requirement berubah di tengah jalan (misalnya penambahan modul Pengadaan Barang setelah modul inti berjalan).

