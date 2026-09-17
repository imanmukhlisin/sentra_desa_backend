# 📑 API Contract - Sentra-Desa.id (v1)

Dokumentasi spesifikasi antarmuka pemrograman aplikasi (API Contract) resmi untuk platform **Sentra-Desa.id**. Dokumen ini menjadi acuan utama integrasi antara **Backend (Laravel 12 / Sanctum)** dan **Frontend (Next.js / Flutter)**, serta diselaraskan langsung dengan **Notion Project Tracker**.

---

## 📌 Ringkasan Spesifikasi Protokol

- **Base URL Development:** `http://localhost:8000/api`
- **Base URL Production:** `https://api.sentra-desa.id/api`
- **Autentikasi:** Laravel Sanctum Token (`Authorization: Bearer <token>`)
- **Format Header:**
  ```http
  Accept: application/json
  Content-Type: application/json
  ```
- **Upload File:** `multipart/form-data`

---

## 📦 Standar Response Envelope

### 1. Response Berhasil (Single Object / Action)
```json
{
  "status": "success",
  "message": "Operasi berhasil",
  "data": { ... }
}
```

### 2. Response Berhasil dengan Paginasi (List Collection)
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [ ... ],
    "first_page_url": "http://localhost:8000/api/v1/public/products?page=1",
    "from": 1,
    "last_page": 5,
    "last_page_url": "http://localhost:8000/api/v1/public/products?page=5",
    "per_page": 12,
    "total": 60
  }
}
```

### 3. Response Gagal / Error Umum (401, 403, 404, 500)
```json
{
  "status": "error",
  "message": "Resource atau endpoint tidak ditemukan"
}
```

### 4. Response Validasi Input Gagal (422 Unprocessable Entity)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["Format email tidak valid.", "Email sudah terdaftar."],
    "password": ["Password minimal harus 8 karakter."]
  }
}
```

---

## 📋 TABEL MASTER TASK NOTION (PROJECT TRACKER MAPPING)

Tabel di bawah ini diselaraskan **100% persis** dengan properti dan opsi dropdown `Module / Feature` di Notion:

| Task Name | Module / Feature | Priority | Related API Endpoint | Sprint / Phase |
| :--- | :--- | :---: | :--- | :--- |
| **Integrasi List Desa Terverifikasi & Filter Wilayah** | `Profile Desa` | P0 | `GET /v1/public/villages` | Sprint 1 |
| **Integrasi Detail Profil Desa Lengkap (Visi Misi & Preview Modul)** | `Profile Desa` | P1 | `GET /v1/public/villages/{id}/profile` | Sprint 1 |
| **Integrasi List & Filter Potensi Desa** | `Potensi Desa` | P1 | `GET /v1/public/village-potentials` | Sprint 1 |
| **Integrasi Detail Potensi & Status Investasi Desa** | `Potensi Desa` | P2 | `GET /v1/public/village-potentials/{id}` | Sprint 1 |
| **Integrasi Direktori Layanan Surat & Administrasi Warga** | `Informasi Desa` | P1 | `GET /v1/public/village-services` | Sprint 1 |
| **Integrasi Pengumuman Warta & Informasi Desa** | `Informasi Desa` | P2 | `GET /v1/public/contents` | Sprint 1 |
| **Integrasi Katalog Sentra Produk (Kategori, Search, Filter Wilayah)** | `Sentra Produk` | P0 | `GET /v1/public/products` | Sprint 1 |
| **Integrasi Halaman Detail Produk & Kontak WhatsApp Toko** | `Sentra Produk` | P0 | `GET /v1/public/products/{slug}` | Sprint 1 |
| **Integrasi Form Tambah Produk Baru UMKM (Multi-Image)** | `Sentra Produk` | P1 | `POST /v1/umkm/products` | Sprint 2 |
| **Integrasi Katalog Komoditas Siap Ekspor & HS Code** | `Desa Ekspor` | P1 | `GET /v1/public/export-products` | Sprint 1 |
| **Integrasi Detail Komoditas Ekspor & Info Kontak Koperasi** | `Desa Ekspor` | P2 | `GET /v1/public/export-products/{slug}` | Sprint 1 |
| **Integrasi Direktori Desa Wisata & Filter Kategori** | `Desa Wisata` | P1 | `GET /v1/public/tourisms` | Sprint 1 |
| **Integrasi Detail Destinasi Wisata, Tiket, Fasilitas & Peta Maps** | `Desa Wisata` | P1 | `GET /v1/public/tourisms/{slug}` | Sprint 1 |
| **Integrasi Profil BUMDes & Unit Usaha Aktif** | `BUMDES` | P1 | `GET /v1/public/bumdes` | Sprint 1 |
| **Integrasi Detail BUMDes, Legalitas AHU & Performa Usaha** | `BUMDES` | P2 | `GET /v1/public/bumdes/{slug}` | Sprint 1 |
| **Integrasi Direktori Koperasi Desa Merah Putih (KDMP)** | `KDMP` | P1 | `GET /v1/public/kdmp` | Sprint 1 |
| **Integrasi Detail KDMP, Legalitas & Unit Usaha Koperasi** | `KDMP` | P2 | `GET /v1/public/kdmp/{code}` | Sprint 1 |
| **Integrasi Transparansi Dana Desa & Infografis APBDes (LKDD)** | `LKDD` | P1 | `GET /v1/public/lkdd` | Sprint 1 |
| **Integrasi Detail Realisasi Anggaran Belanja & SilPA LKDD** | `LKDD` | P2 | `GET /v1/public/lkdd/{id}` | Sprint 1 |
| **Integrasi Berita Warta Desa & Kategori Artikel** | `Artikel` | P2 | `GET /v1/public/articles` | Sprint 1 |
| **Integrasi Halaman Baca Artikel Desa** | `Artikel` | P2 | `GET /v1/public/articles/{slug}` | Sprint 1 |
| **Integrasi Usulan Kebutuhan Sarana Wishlist Desa** | `Wishlist Desa` | P1 | `GET /v1/public/wishlists` | Sprint 1 |
| **Integrasi Detail Wishlist Desa & Form Donasi Sarana** | `Wishlist Desa` | P2 | `GET /v1/public/wishlists/{id}` | Sprint 2 |
| **Integrasi Login User Warga (Single-Session Token)** | `Login User` | P0 | `POST /v1/public/login` | Sprint 1 |
| **Integrasi Registrasi Member Warga Baru** | `Login User` | P0 | `POST /v1/public/register` | Sprint 1 |
| **Integrasi Form Pendaftaran Merchant UMKM + Upload Bukti** | `Login UMKM` | P0 | `POST /v1/umkm/merchant/register` | Sprint 1 |
| **Integrasi Cek Status Toko & Kartu Sisa Hari Membership UMKM** | `Login UMKM` | P1 | `GET /v1/umkm/merchant` | Sprint 2 |
| **Integrasi Form Perpanjangan Toko / Renewal Membership UMKM** | `Login UMKM` | P1 | `POST /v1/umkm/merchant/renew` | Sprint 2 |
| **Integrasi Form Pengajuan Pendaftaran Admin Desa Baru** | `Login Desa` | P1 | `POST /v1/public/register-village` | Sprint 2 |
| **Integrasi Verifikasi Toko UMKM oleh Admin Desa** | `Login Desa` | P1 | `POST /v1/village/merchants/{id}/approve` | Sprint 2 |

---

## 🧭 RINCIAN SPESIFIKASI API PER MODUL NOTION

---

### MODUL: Profile Desa
> **Label Notion:** `Profile Desa`  
> **Kegunaan:** Direktori desa terverifikasi se-Indonesia dan etalase agregasi profil desa lengkap (visi/misi, demografi, aparatur, serta preview komoditas/produk).

#### 1. List Desa Terverifikasi
- **Method & URL:** `GET /v1/public/villages`
- **Query Parameters:**
  | Parameter | Tipe | Keterangan |
  | :--- | :--- | :--- |
  | `province_id` | integer | Filter provinsi (opsional) |
  | `regency_id` | integer | Filter kabupaten (opsional) |
  | `district_id` | integer | Filter kecamatan (opsional) |
  | `search` | string | Pencarian nama atau deskripsi desa |
  | `is_featured` | boolean | Tampilkan hanya desa unggulan (`1` / `true`) |
  | `per_page` | integer | Jumlah data per halaman (default: `12`) |
- **Response JSON (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 3204052001,
        "name": "Panundaan",
        "code": "32.04.05.2001",
        "description": "Desa agrowisata dan sentra penghasil strawberry.",
        "logo": "https://api.sentra-desa.id/storage/villages/logo.png",
        "cover_image": "https://api.sentra-desa.id/storage/villages/cover.jpg",
        "population": 6500,
        "area_size": "14.50",
        "is_verified": true,
        "is_featured": true,
        "district": {
          "id": 320405,
          "name": "Ciwidey",
          "regency": {
            "id": 3204,
            "name": "KABUPATEN BANDUNG",
            "province": {
              "id": 32,
              "name": "JAWA BARAT"
            }
          }
        }
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

#### 2. Detail Agregasi Profil Desa
- **Method & URL:** `GET /v1/public/villages/{village_id}/profile`
- **Response JSON (200 OK):**
```json
{
  "status": "success",
  "data": {
    "village": {
      "id": 3204052001,
      "name": "Panundaan",
      "vision": "Mewujudkan Desa Mandiri Berbasis Wisata & Digital 2029",
      "mission": "1. Peningkatan UMKM Desa\n2. Digitalisasi Layanan Publik",
      "mayor_name": "Ahmad Subagyo, S.P.",
      "contact_phone": "022-897654",
      "contact_email": "kontak@panundaan.desa.id",
      "address": "Jl. Raya Ciwidey No. 45",
      "population": 6500,
      "area_size": "14.50"
    },
    "potentials_preview": [
      { "id": 1, "name": "Kebun Strawberry Organik", "category": "pertanian" }
    ],
    "products_preview": [
      { "id": 101, "name": "Madu Hutan Liar 500ml", "price": "85000.00" }
    ],
    "tourisms_preview": [
      { "id": 1, "name": "Wisata Alam Kebun Teh", "entrance_fee": "25000.00" }
    ],
    "bumdes_preview": [
      { "id": 1, "name": "BUMDes Sejahtera Abadi" }
    ],
    "export_products_preview": [
      { "id": 1, "name": "Green Bean Kopi Arabika", "hs_code": "0901.11.00" }
    ],
    "contents_preview": [
      { "id": 5, "title": "Jadwal Pelayanan KTP Digital Desa" }
    ]
  }
}
```

---

### MODUL: Potensi Desa
> **Label Notion:** `Potensi Desa`  
> **Kegunaan:** Menampilkan potensi komoditas, lahan, nilai estimasi ekonomi tahunan, dan status kesiapan investasi bagi calon investor.

#### 1. List Potensi Desa
- **Method & URL:** `GET /v1/public/village-potentials`
- **Query Parameters:** `category`, `village_id`, `search`, `per_page`
- **Response JSON (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Sentra Budidaya Kopi Arabika Gunung Tilu",
        "category": "pertanian",
        "description": "Potensi lahan kopi 200 hektar dengan kapasitas panen 50 ton/tahun.",
        "image": "https://api.sentra-desa.id/storage/potentials/kopi.jpg",
        "gallery": [
          "https://api.sentra-desa.id/storage/potentials/kopi-1.jpg"
        ],
        "total_area": "200.00",
        "production_volume": "50 Ton/Tahun",
        "economic_value": "2500000000.00",
        "is_investment_ready": true,
        "investment_needs": "Mesin Pengolah Biji Kopi (Huller & Roaster)",
        "development_status": "berkembang",
        "village": {
          "id": 3204052001,
          "name": "Panundaan"
        }
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

#### 2. Detail Potensi Desa
- **Method & URL:** `GET /v1/public/village-potentials/{id}`
- **Response JSON (200 OK):** Mengembalikan objek data tunggal potensi desa.

---

### MODUL: Informasi Desa
> **Label Notion:** `Informasi Desa`  
> **Kegunaan:** Direktori persyaratan surat menyurat warga (SKU, SKCK, Domisili), estimasi waktu proses, dan pengumuman warta desa.

#### 1. List Layanan Administrasi Publik
- **Method & URL:** `GET /v1/public/village-services`
- **Query Parameters:** `village_id`, `category`, `search`
- **Response JSON (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Surat Keterangan Usaha (SKU) untuk UMKM",
        "slug": "surat-keterangan-usaha-sku",
        "category": "administrasi",
        "description": "Penerbitan surat izin operasional usaha bagi warga pelaku UMKM.",
        "requirements": [
          "Fotokopi KTP Pemohon",
          "Fotokopi Kartu Keluarga",
          "Surat Pengantar RT/RW",
          "Foto Tempat Usaha"
        ],
        "process_steps": "1. Serahkan berkas\n2. Verifikasi petugas\n3. Penandatanganan Kades\n4. Surat terbit",
        "estimated_days": "1 Hari Kerja",
        "fee": "0.00",
        "contact_person": "Bagian Pelayanan Umum (Pak Dadang)",
        "contact_phone": "081233445566",
        "office_hours": "Senin - Jumat: 08.00 - 15.00 WIB",
        "is_online_available": false
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

#### 2. List Warta & Informasi Desa
- **Method & URL:** `GET /v1/public/contents`
- **Query Parameters:** `village_id`, `category` (default: `informasi`), `search`
- **Response JSON (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 5,
      "title": "Jadwal Pelayanan KTP Digital & Pajak Bumi Bangunan",
      "slug": "jadwal-pelayanan-ktp-digital-pbb",
      "content": "Diberitahukan kepada seluruh warga...",
      "image": "https://api.sentra-desa.id/storage/contents/layanan.jpg",
      "published_at": "2026-09-15 09:00:00"
    }
  ]
}
```

---

### MODUL: Sentra Produk
> **Label Notion:** `Sentra Produk`  
> **Kegunaan:** Katalog e-commerce produk hasil karya UMKM desa dengan filter kategori, wilayah bertingkat, stok, dan kontak pedagang.

#### 1. List Produk Publik
- **Method & URL:** `GET /v1/public/products`
- **Query Parameters:**
  - `category`: `makanan_minuman`, `kerajinan`, `fashion`, `pertanian`, `perikanan`, `peternakan`, `jasa`, `lainnya`
  - `province_id`, `regency_id`, `district_id`, `village_id`
  - `merchant_id`: Filter toko tertentu
  - `search`: Keyword pencarian nama / deskripsi
  - `limit`: Batas item tanpa pagination (opsional)
  - `per_page`: Jumlah item per halaman (default: `12`)
- **Response JSON (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 101,
        "name": "Madu Hutan Liar Murni 500ml",
        "slug": "madu-hutan-liar-murni-500ml-ab12c",
        "price": "85000.00",
        "discount_price": "75000.00",
        "description": "Madu murni alami tanpa campuran pemanis buatan langsung dari lebah liar hutan desa.",
        "category": "makanan_minuman",
        "stock": 45,
        "unit": "botol",
        "weight": "600.00",
        "image": "https://api.sentra-desa.id/storage/products/madu-utama.jpg",
        "gallery": [
          "https://api.sentra-desa.id/storage/products/gallery/madu-1.jpg"
        ],
        "is_available": true,
        "merchant": {
          "id": 5,
          "store_name": "Toko Madu Hutan Asli",
          "phone": "08123456789",
          "address": "Dusun Babakan RT 02/RW 03",
          "status": "approved"
        },
        "village": {
          "id": 3204052001,
          "name": "Panundaan"
        }
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

#### 2. Detail Produk
- **Method & URL:** `GET /v1/public/products/{slug_or_id}`
- **Response JSON (200 OK):** Mengembalikan objek produk tunggal lengkap.

#### 3. Tambah Produk Baru (Role: UMKM)
- **Method & URL:** `POST /v1/umkm/products`
- **Auth:** `Bearer Token` (Role: `umkm`, Status Merchant: `approved`)
- **Request Body (Multipart Form-Data):**
  - `name` (string, required)
  - `price` (numeric, required)
  - `description` (string, required)
  - `category` (enum, required)
  - `stock` (integer, optional)
  - `unit` (string, optional)
  - `weight` (numeric dalam gram, optional)
  - `image` (file image, max 2048KB)
  - `gallery[]` (array file image, max 4 file)
- **Response JSON (201 Created):**
```json
{
  "status": "success",
  "message": "Produk berhasil ditambahkan",
  "data": {
    "id": 102,
    "name": "Keripik Singkong Balado",
    "slug": "keripik-singkong-balado-x9z1a",
    "price": "15000.00",
    "stock": 100
  }
}
```

---

### MODUL: Desa Ekspor
> **Label Notion:** `Desa Ekspor`  
> **Kegunaan:** Katalog komoditas desa siap ekspor lengkap dengan standar sertifikasi, HS Code, kapasitas volume bulanan, dan Minimal Order Quantity (MOQ).

#### 1. List Komoditas Ekspor
- **Method & URL:** `GET /v1/public/export-products`
- **Query Parameters:** `village_id`, `search`, `per_page`
- **Response JSON (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Green Bean Kopi Arabika Java Preanger Grade 1",
        "slug": "green-bean-kopi-arabika-grade-1",
        "hs_code": "0901.11.00",
        "description": "Biji kopi petik merah olahan proses washed dan honey...",
        "image": "https://api.sentra-desa.id/storage/export/coffee.jpg",
        "destination_countries": ["Jepang", "Belanda", "Singapura"],
        "export_status": "active",
        "certifications": ["Organik Indonesia", "Fair Trade", "Halal"],
        "has_export_license": true,
        "export_volume": "10",
        "unit": "Ton/Bulan",
        "export_value": "1500000000.00",
        "contact_person": "Koperasi Ekspor Tani Makmur",
        "contact_phone": "081298765432",
        "contact_email": "export@panundaan.desa.id",
        "village": {
          "id": 3204052001,
          "name": "Panundaan"
        }
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

#### 2. Detail Produk Ekspor
- **Method & URL:** `GET /v1/public/export-products/{slug_or_id}`
- **Response JSON (200 OK):** Mengembalikan data komoditas ekspor tunggal.

---

### MODUL: Desa Wisata
> **Label Notion:** `Desa Wisata`  
> **Kegunaan:** Direktori destinasi desa wisata, agrowisata, wisata budaya, harga tiket masuk (HTM), jam buka, koordinat Google Maps, dan fasilitas.

#### 1. List Destinasi Wisata
- **Method & URL:** `GET /v1/public/tourisms`
- **Query Parameters:** `category`, `province_id`, `regency_id`, `search`, `per_page`
- **Response JSON (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Kampung Wisata Strawberry Ciwidey",
        "slug": "kampung-wisata-strawberry-ciwidey",
        "category": "agrowisata",
        "short_description": "Wisata petik buah strawberry segar langsung dari pohonnya.",
        "cover_image": "https://api.sentra-desa.id/storage/tourisms/strawberry.jpg",
        "gallery": [
          "https://api.sentra-desa.id/storage/tourisms/strawberry-1.jpg"
        ],
        "entrance_fee": "20000.00",
        "opening_hours": "Setiap Hari: 07.00 - 17.00 WIB",
        "facilities": ["Area Parkir", "Spot Foto", "Gazebo", "Toilet", "Mushola"],
        "address": "Blok Babakan Kiara RT 02/RW 04",
        "latitude": "-7.12345678",
        "longitude": "107.56789012",
        "phone": "081298765432",
        "website": "https://wisata.panundaan.desa.id",
        "village": {
          "id": 3204052001,
          "name": "Panundaan"
        }
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

#### 2. Detail Destinasi Wisata
- **Method & URL:** `GET /v1/public/tourisms/{slug_or_id}`
- **Response JSON (200 OK):** Mengembalikan objek wisata tunggal lengkap.

---

### MODUL: BUMDES
> **Label Notion:** `BUMDES`  
> **Kegunaan:** Profil legalitas Badan Usaha Milik Desa (AHU Kemenkumham), jajaran pengurus, modal awal, unit usaha aktif, dan performa keuangan.

#### 1. List BUMDes
- **Method & URL:** `GET /v1/public/bumdes`
- **Query Parameters:** `village_id`, `search`, `per_page`
- **Response JSON (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "BUMDes Sejahtera Abadi",
        "slug": "bumdes-sejahtera-abadi",
        "description": "Lembaga usaha desa penggerak ekonomi warga bidang air bersih dan wisata.",
        "logo": "https://api.sentra-desa.id/storage/bumdes/logo.png",
        "legal_number": "AHU-00123.AH.01.33.TAHUN.2023",
        "established_date": "2020-08-17",
        "initial_capital": "150000000.00",
        "annual_revenue": "450000000.00",
        "employee_count": 18,
        "business_units": [
          "Pengelolaan Air Bersih Desa (HIPPAM)",
          "Unit Pengeringan Gabah & Beras",
          "Pengelolaan Tiket Wisata Alam"
        ],
        "director_name": "Drs. Hendra Gunawan",
        "phone": "081321456789",
        "email": "bumdes@panundaan.desa.id",
        "address": "Kantor BUMDes Jl. Desa No. 10",
        "performance_category": "maju",
        "village": {
          "id": 3204052001,
          "name": "Panundaan"
        }
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

#### 2. Detail BUMDes
- **Method & URL:** `GET /v1/public/bumdes/{slug_or_id}`
- **Response JSON (200 OK):** Mengembalikan objek data BUMDes tunggal.

---

### MODUL: KDMP
> **Label Notion:** `KDMP`  
> **Kegunaan:** Menampilkan profil Koperasi Desa Merah Putih, nomor legalitas badan hukum, kepengurusan, total aset, dan unit usaha koperasi.

#### 1. List Koperasi Desa
- **Method & URL:** `GET /v1/public/kdmp`
- **Query Parameters:** `village_id`, `status` (`aktif`, `dalam_pembinaan`), `search`, `per_page`
- **Response JSON (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "code": "KDMP-320405-001",
        "name": "Koperasi Desa Merah Putih Tani Makmur",
        "nomor_badan_hukum": "AHU-0004567.AH.01.26.TAHUN.2024",
        "description": "Koperasi simpan pinjam dan pengadaan sarana produksi pertanian.",
        "cover_image": "https://api.sentra-desa.id/storage/kdmp/koperasi.jpg",
        "status": "aktif",
        "unit_usaha": ["pertanian", "simpan_pinjam", "perdagangan"],
        "ketua_name": "Ir. Budi Suharsono",
        "sekretaris_name": "Siti Aminah, S.E.",
        "bendahara_name": "Rahmat Hidayat",
        "total_members": 240,
        "modal_awal": "50000000.00",
        "total_assets": "850000000.00",
        "established_date": "2024-01-15",
        "phone": "08122334455",
        "address": "Kompleks Sentra Tani RT 01/RW 02",
        "village": {
          "id": 3204052001,
          "name": "Panundaan"
        }
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

#### 2. Detail Koperasi Desa
- **Method & URL:** `GET /v1/public/kdmp/{code_or_id}`
- **Response JSON (200 OK):** Mengembalikan data koperasi tunggal.

---

### MODUL: LKDD
> **Label Notion:** `LKDD`  
> **Kegunaan:** Portal transparansi APBDes dan realisasi Dana Desa (APBN, PADes, ADD) per bidang belanja (pemerintahan, pembangunan, pembinaan, kebencanaan).

#### 1. List Laporan Keuangan Desa
- **Method & URL:** `GET /v1/public/lkdd`
- **Query Parameters:** `fiscal_year` (tahun anggaran), `province_id`, `regency_id`, `district_id`, `village_id`, `search`, `per_page`
- **Response JSON (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "fiscal_year": 2025,
        "period": "tahunan",
        "head_name": "Ahmad Subagyo, S.P.",
        "head_title": "Kepala Desa Panundaan",
        "pendapatan_asli_desa": "90000000.00",
        "dana_desa": "980000000.00",
        "alokasi_dana_desa": "380000000.00",
        "bagi_hasil_pajak": "45000000.00",
        "bantuan_keuangan_kab": "30000000.00",
        "bantuan_keuangan_prov": "50000000.00",
        "pendapatan_lainnya": "10000000.00",
        "total_pendapatan": "1585000000.00",
        "belanja_pemerintahan": "350000000.00",
        "belanja_pembangunan": "720000000.00",
        "belanja_pembinaan": "180000000.00",
        "belanja_pemberdayaan": "250000000.00",
        "belanja_bencana": "65000000.00",
        "total_belanja": "1565000000.00",
        "silpa": "20000000.00",
        "infographic_image": "https://api.sentra-desa.id/storage/lkdd/infografis-2025.jpg",
        "notes": "Laporan telah disahkan dalam Musdes Pertanggungjawaban APBDes TA 2025.",
        "village": {
          "id": 3204052001,
          "name": "Panundaan"
        }
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

#### 2. Detail Laporan Keuangan Desa
- **Method & URL:** `GET /v1/public/lkdd/{id}`
- **Response JSON (200 OK):** Mengembalikan data laporan keuangan tunggal.

---

### MODUL: Artikel
> **Label Notion:** `Artikel`  
> **Kegunaan:** Publikasi berita kegiatan gotong royong, edukasi pertanian UMKM, dan siaran pers dari pemerintah desa.

#### 1. List Artikel Berita Desa
- **Method & URL:** `GET /v1/public/articles`
- **Query Parameters:** `category`, `village_id`, `featured` (`1`), `search`, `per_page`
- **Response JSON (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "Pelatihan Digital Marketing bagi Pengrajin Anyaman Bambu Desa",
        "slug": "pelatihan-digital-marketing-pengrajin-bambu-x1y2z",
        "excerpt": "Sebanyak 40 pengrajin anyaman bambu mengikuti pelatihan pemasaran digital...",
        "content": "<p>Panundaan - Pemerintah Desa Panundaan bekerja sama dengan...</p>",
        "thumbnail": "https://api.sentra-desa.id/storage/articles/pelatihan.jpg",
        "category": "pemberdayaan",
        "author_name": "Admin Desa",
        "is_featured": true,
        "views_count": 342,
        "published_at": "2026-09-15 08:30:00",
        "village": {
          "id": 3204052001,
          "name": "Panundaan"
        }
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

#### 2. Detail Artikel Berita
- **Method & URL:** `GET /v1/public/articles/{slug}`
- **Response JSON (200 OK):** Mengembalikan data artikel tunggal lengkap.

---

### MODUL: Wishlist Desa
> **Label Notion:** `Wishlist Desa`  
> **Kegunaan:** Sarana publikasi usulan kebutuhan mendesak desa (alat tani, mesin pengering, jembatan, sarana kesehatan) yang membuka peluang dukungan dari donatur atau program CSR perusahaan.

#### 1. List Wishlist Kebutuhan Desa
- **Method & URL:** `GET /v1/public/wishlists`
- **Query Parameters:** `category`, `status` (`open`, `fulfilled`, `closed`), `village_id`, `search`, `per_page`
- **Response JSON (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "Pengadaan Mesin Pengering Gabah (Bed Dryer) Kapasitas 5 Ton",
        "category": "pertanian",
        "description": "Dibutuhkan untuk mencegah pembusukan gabah padi petani saat musim hujan tiba.",
        "quantity": "1.00",
        "unit": "Unit Mesin",
        "needed_by": "2026-11-30",
        "image": "https://api.sentra-desa.id/storage/wishlists/dryer.jpg",
        "status": "open",
        "village": {
          "id": 3204052001,
          "name": "Panundaan",
          "district": {
            "name": "Ciwidey",
            "regency": {
              "name": "KABUPATEN BANDUNG"
            }
          }
        }
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

#### 2. Detail Wishlist Desa
- **Method & URL:** `GET /v1/public/wishlists/{id}`
- **Response JSON (200 OK):** Mengembalikan data wishlist tunggal lengkap.

---

### MODUL: Login User
> **Label Notion:** `Login User`  
> **Kegunaan:** Autentikasi dan pendaftaran mandiri member warga/pembeli umum berbasis Single-Session Sanctum Token.

#### 1. Registrasi Warga Baru
- **Method & URL:** `POST /v1/public/register`
- **Request Body:**
```json
{
  "name": "Budi Santoso",
  "email": "budi@example.com",
  "password": "password123"
}
```
- **Response JSON (201 Created):**
```json
{
  "status": "success",
  "message": "Pendaftaran berhasil!",
  "access_token": "1|qWeRtYuIoP1234567890abcdef",
  "user": {
    "id": 12,
    "name": "Budi Santoso",
    "email": "budi@example.com"
  }
}
```

#### 2. Login User (Single-Session)
- **Method & URL:** `POST /v1/public/login`
- **Request Body:**
```json
{
  "email": "budi@example.com",
  "password": "password123"
}
```
- **Response JSON (200 OK):** Mengembalikan `access_token` baru dan mencabut token di perangkat lain.

---

### MODUL: Login UMKM
> **Label Notion:** `Login UMKM`  
> **Kegunaan:** Pendaftaran member pelaku usaha desa, upload bukti legalitas/pembayaran, dan pengelolaan masa aktif keanggotaan toko (membership).

#### 1. Pendaftaran Merchant UMKM Baru
- **Method & URL:** `POST /v1/umkm/merchant/register`
- **Auth:** `Bearer Token`
- **Request Body (Multipart Form-Data):**
  - `store_name` (string, required)
  - `village_id` (integer, required)
  - `address` (string, required)
  - `phone` (string, required)
  - `description` (string, optional)
  - `business_type` (string, optional)
  - `established_year` (string, optional)
  - `logo` (file image, optional, max 2048KB)
  - `payment_proof` (file image, optional, max 2048KB)
- **Response JSON (201 Created):**
```json
{
  "status": "success",
  "message": "Pendaftaran merchant berhasil dikirim, menunggu persetujuan admin.",
  "data": {
    "id": 6,
    "store_name": "Warung Keripik Bu Ani",
    "status": "pending",
    "village": {
      "name": "Panundaan"
    }
  }
}
```

#### 2. Cek Status Toko & Sisa Masa Aktif Membership
- **Method & URL:** `GET /v1/umkm/merchant`
- **Auth:** `Bearer Token` (Role: `umkm`)
- **Response JSON (200 OK):**
```json
{
  "status": "success",
  "data": {
    "id": 6,
    "store_name": "Warung Keripik Bu Ani",
    "status": "approved",
    "approved_at": "2026-09-17 10:00:00",
    "membership_expires_at": "2027-09-17 10:00:00",
    "renewal_status": "none"
  }
}
```

#### 3. Permintaan Perpanjangan Toko (Renewal)
- **Method & URL:** `POST /v1/umkm/merchant/renew`
- **Auth:** `Bearer Token`
- **Request Body (Multipart Form-Data):** `renewal_proof` (file image, max 2048KB)
- **Response JSON (200 OK):**
```json
{
  "status": "success",
  "message": "Permintaan perpanjangan berhasil dikirim.",
  "data": {
    "renewal_status": "pending"
  }
}
```

---

### MODUL: Login Desa
> **Label Notion:** `Login Desa`  
> **Kegunaan:** Pengajuan pendaftaran aparatur desa dan verifikasi toko UMKM oleh pemerintah desa.

#### 1. Verifikasi / Approval Toko UMKM oleh Admin Desa
- **Method & URL:** `POST /v1/village/merchants/{id}/approve`
- **Auth:** `Bearer Token` (Role: `village_admin`)
- **Response JSON (200 OK):**
```json
{
  "status": "success",
  "message": "Merchant approved, membership aktif 1 tahun"
}
```

#### 2. Tolak Pendaftaran Toko UMKM
- **Method & URL:** `POST /v1/village/merchants/{id}/reject`
- **Auth:** `Bearer Token` (Role: `village_admin`)
- **Response JSON (200 OK):**
```json
{
  "status": "success",
  "message": "Merchant rejected"
}
```
