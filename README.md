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
12. [Metodologi Pengembangan: SDLC dengan Pendekatan Agile](#12-metodologi-pengembangan-sdlc-dengan-pendekatan-agile)

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
- **Lupa password via OTP email** — user meminta kode OTP 6 digit yang dikirim ke email terdaftar, verifikasi kode, lalu set password baru (lihat [Panduan Penggunaan](#lupa-password-otp))
- Middleware berbasis role (`admin` dan `divisi`) — setiap grup route hanya bisa diakses oleh role yang sesuai, akses tanpa izin akan mendapat HTTP 403

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
| Pengiriman Email (OTP) | Laravel Mail (SMTP, contoh: Mailtrap untuk testing) |
| Testing | PHPUnit |
| Code Quality | Laravel Pint, PHPStan (Larastan), Enlightn |

---

## 3. Arsitektur & Cara Kerja Aplikasi

Aplikasi ini menggunakan pola **MVC (Model-View-Controller)** standar Laravel:

- **Model** (`app/Models`) — representasi tabel database & relasi antar data (Eloquent ORM).
- **Controller** (`app/Http/Controllers`) — logika bisnis, menangani request dan mengembalikan view/response.
- **View** (`resources/views`) — tampilan Blade, dipisah per modul (`admin/`, `divisi/`, `barang/`, `payment/`, `login/`, `emails/`, dst).

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

**E. Alur Lupa Password (OTP Email)**
```
1. User klik "Lupa password?" di halaman login, input email
      -> Sistem generate OTP 6 digit, simpan ke tabel `password_otps` (expired 10 menit)
      -> OTP dikirim ke email lewat Mail::send() (ForgotPasswordController::sendOtp)

2. User input OTP yang diterima
      -> Sistem cek kecocokan OTP + belum expired (ForgotPasswordController::verifyOtp)
      -> Jika valid, session ditandai "otp_verified"

3. User input password baru (min. 8 karakter, harus konfirmasi)
      -> Password di-hash & disimpan, OTP dihapus dari DB
      -> Redirect ke halaman login
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

Rute lupa password **tidak** memakai middleware `auth` (karena user belum login saat mengaksesnya), tapi dibatasi `throttle:6,1` (maksimal 6 request/menit per IP) untuk mencegah spam permintaan OTP maupun percobaan brute-force menebak kode OTP.

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
| `password_otps` | Kode OTP reset password (email, otp, expires_at) — tabel terpisah dari `password_reset_tokens` bawaan Laravel | — |

### Diagram Relasi (ringkas)

```
users (1) ----< (N) permintaan_barang (N) >---- (1) barang
                        |  (1)
                        v
                  barang_keluar (N) >---- (1) barang

supplier (1) --< barang_masuk >-- (1) barang
supplier (1) --< payments
supplier (1) --< pengadaan_barang
supplier (1) --< barang
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

### 6.6 Atur Mailer (untuk fitur OTP)

Fitur lupa password butuh mailer aktif. Untuk testing lokal, disarankan pakai [Mailtrap](https://mailtrap.io) (email sandbox, tidak terkirim ke inbox asli):

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=xxxxxxxxxxxxxx
MAIL_PASSWORD=xxxxxxxxxxxxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@atkapp.test"
MAIL_FROM_NAME="ATK App"
```

Ganti `MAIL_USERNAME`/`MAIL_PASSWORD` dengan kredensial dari dashboard Mailtrap masing-masing (Email Testing → Inbox → SMTP Settings). Untuk produksi, ganti ke SMTP provider asli (Gmail, SES, dsb) — **jangan pernah commit kredensial SMTP ke Git**, pastikan `.env` ada di `.gitignore`.

### 6.7 Jalankan Migration

```bash
php artisan migrate
```

Perintah ini akan membuat seluruh tabel (lihat [Struktur Basis Data](#4-struktur-basis-data)), termasuk tabel `password_otps`.

### 6.8 Jalankan Seeder (isi data awal)

```bash
php artisan db:seed
```

> `DatabaseSeeder.php` memanggil `DummyUsersSeeder` yang membuat 3 akun awal — lihat [Akun Default](#8-akun-default-seeder).

Atau sekaligus migrate + seed dari kosong:
```bash
php artisan migrate:fresh --seed
```
Peringatan: `migrate:fresh` akan **menghapus semua tabel** sebelum membuat ulang — pastikan tidak ada data penting yang hilang.

### 6.9 Build Asset Frontend

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

### <a id="lupa-password-otp"></a>Lupa Password (OTP Email)

1. Di halaman `/login`, klik link **"Lupa password?"**.
2. Masukkan email akun yang terdaftar di tabel `users`, klik **Kirim Kode OTP**.
3. Cek inbox email tersebut (atau dashboard Mailtrap → Email Testing → Inboxes saat masih di mode testing) — kode OTP 6 digit berlaku **10 menit**.
4. Masukkan kode OTP di halaman verifikasi.
5. Setelah valid, masukkan password baru (minimal 8 karakter) beserta konfirmasinya.
6. Sistem redirect ke `/login` — login dengan password baru.

Rute terkait: `/forgot-password`, `/forgot-password/otp`, `/forgot-password/reset` (lihat `ForgotPasswordController`).

---

## 10. Struktur Folder Proyek

```
atk_laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Logika bisnis tiap modul (termasuk ForgotPasswordController)
│   │   └── Middleware/        # RoleMiddleware untuk otorisasi role
│   ├── Mail/                  # Mailable class (OtpMail)
│   └── Models/                 # Eloquent model (Barang, Supplier, dst)
├── config/                     # File konfigurasi Laravel
├── database/
│   ├── migrations/             # Skema tabel database
│   └── seeders/                 # Data awal (DummyUsersSeeder, dll)
├── public/                     # Document root (entry point index.php, asset build)
├── resources/
│   ├── views/
│   │   ├── login/               # Login, forgot-password, verify-otp, reset-password
│   │   ├── emails/               # Template email OTP
│   │   └── ...                   # View modul lain (admin/, divisi/, barang/, payment/, dst)
│   ├── css/, js/                 # Sumber asset sebelum di-build Vite
├── routes/
│   └── web.php                    # Definisi seluruh route aplikasi
├── tests/
│   ├── Feature/                   # Test level fitur (PHPUnit)
│   └── Unit/                       # Test level unit (PHPUnit)
├── .env.example                    # Contoh konfigurasi environment
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
| Email OTP tidak terkirim / tidak muncul di Mailtrap | Kredensial `MAIL_*` salah, email tujuan tidak ada di tabel `users`, atau config cache lama | Cek `.env` bagian `MAIL_*`, pastikan email yang diinput ada di tabel `users`, jalankan `php artisan config:clear` |
| `Undefined type 'App\Mail\OtpMail'` di editor | File `app/Mail/OtpMail.php` belum dibuat | Buat file sesuai kode di `ForgotPasswordController` yang memanggil `new OtpMail($otp)` |

---

## 12. Metodologi Pengembangan: SDLC dengan Pendekatan Agile

### 12.1 Posisi Agile dalam SDLC

**SDLC (*Software Development Life Cycle*)** adalah payung konsep tahapan hidup sebuah software: **Perencanaan → Analisis Kebutuhan → Perancangan (Desain) → Implementasi → Pengujian → Deployment → Pemeliharaan**. Tahapan ini berlaku di semua model pengembangan — bedanya di setiap model adalah *bagaimana* tahapan-tahapan itu dijalankan.

**Waterfall** menjalankan tahapan itu satu arah, linear, satu kali jalan: desain harus 100% final sebelum coding dimulai, dan requirement dianggap tidak berubah. **Agile** menjalankan seluruh tahapan itu juga — tapi dalam **siklus berulang berskala kecil (iterasi/sprint)**, satu sprint biasanya fokus ke satu atau beberapa fitur, sehingga tiap fitur bisa langsung diuji dan direvisi sebelum lanjut ke fitur berikutnya, dan requirement boleh berubah di tengah jalan berdasarkan hasil evaluasi.

Proyek ini dikembangkan dengan pendekatan **Agile**, dan itu bukan klaim tanpa bukti — jejaknya terlihat langsung di riwayat migration dan struktur kode, dijelaskan di bawah.

### 12.2 Tahap 1 — Perencanaan & Analisis Kebutuhan (Product Backlog)

Sebelum coding, kebutuhan sistem dipecah jadi **Product Backlog** berdasarkan dua peran pengguna (aktor) yang teridentifikasi dari proses bisnis manajemen ATK:

| Aktor | Kebutuhan Utama |
|---|---|
| **Admin** | Kelola master data (barang, supplier), catat transaksi masuk, proses approval permintaan divisi, kelola pengadaan & pembayaran, cetak laporan PDF |
| **Divisi** | Ajukan permintaan barang, pantau status pengajuan sendiri |

Dari sini disusun *user story* tingkat tinggi, misalnya:
- *"Sebagai admin, saya ingin menyetujui/menolak permintaan barang dari divisi, agar stok gudang tercatat akurat."*
- *"Sebagai divisi, saya ingin mengajukan permintaan barang dan melihat statusnya, tanpa perlu akses ke data divisi lain."*
- *"Sebagai pengguna yang lupa password, saya ingin mereset password sendiri lewat email, tanpa perlu minta admin reset manual."* (backlog tambahan yang muncul belakangan — lihat Sprint 7)

Kebutuhan non-fungsional juga ditetapkan di tahap ini: pemisahan akses berbasis role (admin vs divisi), kebutuhan dokumen tercetak (PDF) untuk setiap transaksi penting, dan keamanan proses autentikasi — hal-hal ini langsung memengaruhi keputusan arsitektur di tahap perancangan.

### 12.3 Tahap 2 — Perancangan (Design)

#### 12.3.1 Perancangan Basis Data (ERD)

Desain skema database dituangkan sebagai **migration Laravel**, bukan dirancang sekali jadi sempurna — pola pertumbuhannya bertahap sesuai kebutuhan tiap fitur:

```
users             → akun & role (admin/divisi)
supplier          → master pemasok
barang            → master barang + stok
barang_masuk      → transaksi in dari supplier   (belongsTo barang, supplier)
permintaan_barang → pengajuan dari divisi         (belongsTo user, barang)
barang_keluar     → hasil approval permintaan     (belongsTo permintaan_barang, barang)
pengadaan_barang  → rencana belanja ke supplier   (belongsTo supplier)
payments          → pembayaran ke supplier        (belongsTo supplier, hasMany barang_masuk)
password_otps     → kode OTP reset password       (tabel independen, tidak terikat model lain)
```

Relasi antar tabel (dari `app/Models/*.php`):

```
users (1) ──< permintaan_barang >── (1) barang
                    │ hasOne
                    ▼
              barang_keluar >── (1) barang

supplier (1) ──< barang_masuk
supplier (1) ──< payments
supplier (1) ──< pengadaan_barang
supplier (1) ──< barang
```

Struktur ini **direvisi beberapa kali setelah dipakai** — bukti nyata proses desain iteratif, bukan sekali gambar ERD lalu final:

| Migration | Perubahan Desain |
|---|---|
| `2025_06_02_change_default_stok_on_barang_table` | Ubah nilai default kolom `stok` setelah modul barang berjalan |
| `2025_06_25_change_barang_masuk_payment_id_to_supplier_id_table` | Relasi `barang_masuk` awalnya ke `payments`, direvisi jadi langsung ke `supplier` — model bisnis "barang masuk dibayar belakangan, terpisah dari siapa suppliernya" ternyata kurang cocok dengan alur nyata |
| `2025_07_14_remove_supplier_id_and_keterangan_from_barang_table` | Kolom `supplier_id` & `keterangan` di tabel `barang` dihapus — sebelumnya redundan karena relasi supplier sudah ada lewat `barang_masuk` |
| `2025_07_22_create_pengadaan_barang_table` | Modul baru ditambahkan **setelah** modul inti stabil, bukan direncanakan sejak awal |
| `2026_07_24_create_password_otps_table` | Tabel baru untuk fitur lupa password, sengaja dipisah dari `password_reset_tokens` bawaan Laravel (yang primary key-nya `email`, kurang cocok untuk histori OTP berulang) |

#### 12.3.2 Perancangan Arsitektur

Pola **MVC** standar Laravel:

```
Browser → routes/web.php → Middleware (auth + RoleMiddleware) → Controller → Model (Eloquent) → View (Blade) → Response
```

`RoleMiddleware` (`app/Http/Middleware/RoleMiddleware.php`) jadi keputusan desain kunci: satu middleware generik yang menerima parameter role (`:admin` atau `:divisi`), dipasang per grup route di `routes/web.php`, bukan dicek manual di tiap controller — supaya aturan akses terpusat di satu tempat.

Untuk fitur lupa password, desainnya sengaja **tidak** memakai middleware `auth` (user belum login), melainkan mengandalkan **state machine berbasis session** (`otp_email`, `otp_verified`) di `ForgotPasswordController` supaya user tidak bisa melompat langsung ke halaman reset password tanpa melalui verifikasi OTP.

### 12.4 Tahap 3 — Implementasi per Sprint

Memetakan urutan migration & modul ke sprint (asumsi 1 sprint ≈ 1–2 minggu, fokus fitur inti dulu baru fitur pendukung):

| Sprint | Fokus | Output di Kode |
|---|---|---|
| 1 | Autentikasi & Role | `users` table, `SesiController`, `RoleMiddleware` |
| 2 | Master Data | `Barang`, `Supplier`, CRUD via `BarangController`, `SupplierController` |
| 3 | Transaksi Masuk | `barang_masuk` table, `BarangMasukController`, update stok otomatis |
| 4 | Alur Permintaan | `permintaan_barang`, `barang_keluar`, `PermintaanBarangController`, `PermintaanAdminController` (approve/reject) |
| 5 | Pengadaan & Pembayaran | `pengadaan_barang`, `payments`, `PengadaanBarangController`, `PaymentController`, cetak PDF (`barryvdh/laravel-dompdf`) |
| 6 | Penyempurnaan Skema | Revisi 3 migration (default stok, relasi supplier, pembersihan kolom redundan) |
| 7 | Keamanan Akun | Fitur lupa password via OTP email (`password_otps`, `ForgotPasswordController`, `OtpMail`) |

Setiap sprint menghasilkan modul yang **langsung bisa dipakai** (working software), bukan menunggu semua modul selesai baru bisa dites — ciri khas Agile dibanding Waterfall.

### 12.5 Tahap 4 — Pengujian (Testing)

Pengujian pada proyek ini dilakukan dengan dua cara, dan penting untuk jujur soal statusnya masing-masing:

**a. Pengujian manual per fitur (dilakukan)** — setiap fitur diuji langsung lewat browser setelah sprint-nya selesai, mengikuti alur bisnis: input data → cek stok berubah sesuai ekspektasi → cek PDF ter-generate benar → cek akses ditolak (403) kalau role salah. Untuk fitur OTP, pengujian manual mencakup: kirim OTP ke email sandbox (Mailtrap), verifikasi kode benar/salah/kedaluwarsa, dan reset password lalu login ulang. Pola "revisi migration setelah dipakai" adalah bukti tidak langsung bahwa pengujian manual ini benar-benar terjadi — bug/ketidaksesuaian baru ketahuan setelah fitur dicoba.

**b. Pengujian otomatis (PHPUnit)** — selain `ExampleTest.php` bawaan Laravel, proyek ini punya Feature test untuk tiga jalur kritis, semuanya pakai `RefreshDatabase` (jalan di SQLite in-memory, tidak menyentuh database MySQL asli):

| File Test | Yang Diuji |
|---|---|
| `tests/Feature/Auth/LoginTest.php` | Login admin/divisi diarahkan ke dashboard yang benar, password salah ditolak, `RoleMiddleware` menolak akses lintas-role dengan 403, user belum login diarahkan ke `/login` |
| `tests/Feature/Permintaan/ApprovalTest.php` | Approve mengurangi stok & mencatat `barang_keluar`, approve ditolak kalau stok tidak cukup (stok tidak berubah), reject mengubah status tanpa mengubah stok, divisi tidak bisa akses endpoint approve admin |
| `tests/Feature/Auth/ForgotPasswordOtpTest.php` | Request OTP mengirim mail (pakai `Mail::fake()`) & menyimpan record, email tidak terdaftar tidak mengirim mail, verifikasi OTP benar/salah/expired, reset password mengubah hash & menghapus OTP, akses reset form tanpa verifikasi OTP ditolak |

Jalankan semuanya dengan:
```bash
php artisan test
```
atau spesifik satu file:
```bash
php artisan test --filter=ApprovalTest
```

Test ini butuh ekstensi PHP `pdo_sqlite` aktif (biasanya sudah default di XAMPP — cek di `php.ini`, pastikan baris `extension=pdo_sqlite` tidak diberi tanda `;` di depan). Test masih perlu diperluas seiring modul lain berkembang (pengadaan barang, payment) — tiga file ini menutup jalur yang paling berisiko kalau salah (kehilangan stok/uang atau kebocoran akses lintas-role).

### 12.6 Tahap 5 — Deployment & Sprint Review

Setiap sprint didemokan (Sprint Review) sebelum lanjut ke sprint berikutnya — dalam konteks proyek individu/kelompok kecil, ini setara dengan menunjukkan progres ke pembimbing di setiap milestone. Evaluasi hasil demo (Sprint Retrospective) itulah yang mendorong perubahan skema di Tahap 3 — siklus **Design → Implement → Test → Review → Redesign** berulang, bukan satu arah.

### 12.7 Ringkasan Alur Ujung ke Ujung

```
Product Backlog (kebutuhan admin, divisi, & keamanan akun)
      ↓
Perancangan ERD & Arsitektur MVC + RoleMiddleware
      ↓
Sprint 1..7 (implementasi bertahap, tiap sprint = working feature)
      ↓
Pengujian manual tiap fitur selesai  →  ditemukan bug/desain kurang pas
      ↓
Revisi skema (migration perbaikan)  →  kembali ke Design untuk fitur terkait
      ↓
Sprint Review per milestone
      ↓
Deployment lokal (XAMPP/artisan serve) → siap untuk pengujian akhir & evaluasi
```