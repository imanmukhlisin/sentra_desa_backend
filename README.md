# Sentra Desa - Backend & Admin Panel

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Filament](https://img.shields.io/badge/Filament-3.x-F59E0B?style=for-the-badge&logo=filament&logoColor=white)](https://filamentphp.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)

Backend API dan Content Management System (CMS) / Admin Panel untuk platform **Sentra Desa**, dibangun menggunakan **Laravel 12** dan **Filament PHP v3**. Platform ini memfasilitasi digitalisasi potensi desa, katalog produk UMKM desa, pariwisata, BUMDes, Koperasi Desa Merah Putih (KDMP), serta keterbukaan informasi dan layanan publik desa.

---

## 🚀 Fitur Utama

- **Hierarki Geospasial Wilayah**: Manajemen wilayah bertingkat (Provinsi &rarr; Kabupaten/Kota &rarr; Kecamatan &rarr; Desa/Kelurahan) dengan kode standar wilayah BPS/Kemendagri.
- **Filament Admin Panel (v3)**: Dashboard manajemen data modern, responsif, dan kaya fitur untuk admin dan pengelola konten.
- **Role & Permission Management**: Akses terstruktur menggunakan Spatie Laravel Permission & Filament Shield (`superadmin`, `village_admin`, `umkm`, `buyer`).
- **RESTful API v1**: Endpoint API untuk konsumsi aplikasi frontend (Web & Mobile) dengan autentikasi Laravel Sanctum.
- **Modul Sentra Desa**:
  - 🛒 **Sentra Produk (UMKM)**: Katalog produk unggulan desa, etalase merchant, dan verifikasi UMKM.
  - 🏞️ **Desa Wisata**: Destinasi wisata alam, budaya, tiket masuk, fasilitas, dan kontak.
  - 🌾 **Potensi Desa**: Pendataan potensi sektor pertanian, perikanan, pariwisata, dan industri kreatif.
  - 🏢 **BUMDes & KDMP**: Profil dan laporan unit usaha BUMDes serta Koperasi Desa Merah Putih.
  - 🚢 **Produk Ekspor**: Etalase komoditas desa siap ekspor lengkap dengan kode HS dan negara tujuan.
  - 🏛️ **Layanan Desa**: Informasi syarat dan prosedur pengurusan administrasi warga secara digital.

---

## 🛠️ Kebutuhan Sistem

Pastikan environment lokal memenuhi spesifikasi berikut:

- **PHP** >= 8.2 (dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `zip`)
- **Composer** >= 2.x
- **MySQL / MariaDB** >= 8.0 / 10.4
- **Node.js** >= 18.x & **NPM** (opsional, untuk aset build)

---

## 📦 Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek di komputer lokal:

### 1. Clone Repository
```bash
git clone https://github.com/imanmukhlisin/sentra_desa_backend.git
cd sentra_desa_backend
```

### 2. Install Dependensi PHP
```bash
composer install
```

### 3. Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi database pada file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db-sentra-desa
DB_USERNAME=root
DB_PASSWORD=
```
> Pastikan database dengan nama tersebut sudah dibuat di MySQL (misal via phpMyAdmin).

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Jalankan Migrasi & Database Seeder
Lakukan migrasi tabel dan pengisian data master (roles, geospatial, data dummy produk, wisata, dll.):
```bash
php artisan migrate:fresh --seed
```

### 6. Buat Storage Symlink
Buat tautan simbolik untuk akses file publik (gambar produk, cover desa, foto profil):
```bash
php artisan storage:link
```

### 7. Jalankan Server Lokal
```bash
php artisan serve
```
Aplikasi backend sekarang aktif di: **`http://localhost:8000`**

---

## 🔐 Akun Default (Hasil Seeder)

Setelah menjalankan `php artisan migrate:fresh --seed`, Anda dapat langsung login ke Dashboard Admin:

| Role | Email | Password | Panel URL |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@desa.com` | `P1csi8080#` | [http://localhost:8000/admin](http://localhost:8000/admin) |
| **Merchant / UMKM** | `berkah.tani@gmail.com` | `password` | API Login / Portal |
| **Sample Merchant** | `budi@gmail.com` | `password` | API Login / Portal |

---

## 📡 Dokumentasi Endpoint API Singkat

Prefix URL API: `http://localhost:8000/api/v1`

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/health` | Health check status server |
| `POST` | `/api/v1/public/login` | Login user / merchant |
| `POST` | `/api/v1/public/register` | Registrasi akun baru |
| `GET` | `/api/v1/public/provinces` | Master data provinsi |
| `GET` | `/api/v1/public/provinces/{id}/regencies` | Daftar kabupaten/kota per provinsi |
| `GET` | `/api/v1/public/regencies/{id}/districts` | Daftar kecamatan per kabupaten |
| `GET` | `/api/v1/public/districts/{id}/villages` | Daftar desa per kecamatan |
| `GET` | `/api/v1/public/products` | Katalog produk UMKM desa |
| `GET` | `/api/v1/public/tourisms` | Daftar destinasi desa wisata |
| `GET` | `/api/v1/public/villages` | Direktori desa & kelurahan |
| `GET` | `/api/v1/public/village-potentials` | Daftar potensi unggulan desa |

---

## 📂 Struktur Direktori Utama

```text
sentra-desa-backend/
├── app/
│   ├── Filament/          # Resource, Pages, dan Widgets Filament Admin
│   ├── Http/
│   │   ├── Controllers/   # Controller API & Web
│   │   └── Middleware/    # Middleware autentikasi & perizinan
│   └── Models/            # Eloquent ORM Models
├── config/                # Konfigurasi aplikasi & paket pihak ketiga
├── database/
│   ├── factories/         # Model factories untuk testing
│   ├── migrations/        # Skema migrasi database
│   └── seeders/           # Seeder data master & data awal
├── routes/
│   ├── api.php            # Rute RESTful API (v1)
│   ├── web.php            # Rute aplikasi web
│   └── console.php        # Artisan commands
└── storage/               # File upload, log, cache
```

---

## 🛠️ Perintah Berguna (Artisan)

- **Bersihkan Seluruh Cache:**
  ```bash
  php artisan optimize:clear
  ```
- **Jalankan Ulang Migrasi & Seeder:**
  ```bash
  php artisan migrate:fresh --seed
  ```
- **Generate Shield Permissions (Filament):**
  ```bash
  php artisan shield:generate
  ```

---

## 📄 Lisensi

Hak Cipta © 2026 **Sentra Desa**. Seluruh hak cipta dilindungi undang-undang.