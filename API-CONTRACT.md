# 📑 API Contract - Sentra-Desa.id (v1)

Dokumentasi spesifikasi antarmuka pemrograman aplikasi (API Contract) resmi untuk platform **Sentra-Desa.id**. Dokumen ini menjadi acuan integrasi antara Backend (Laravel 12 / Sanctum) dan Client (Next.js / Flutter).

---

## 📌 Ringkasan Spesifikasi Protokol

- **Base URL Development:** `http://localhost:8000/api`
- **Base URL Production:** `https://api.sentra-desa.id/api`
- **Autentikasi:** Laravel Sanctum Token (`Authorization: Bearer <token>`)
- **Format Pertukaran Data:** JSON (`Content-Type: application/json`, `Accept: application/json`)
- **Upload Berkas:** `multipart/form-data`

---

## 📦 Standar Envelope Response

### 1. Response Berhasil (Single Object / Action)
```json
{
  "status": "success",
  "message": "Operasi berhasil dilakukan",
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
    "first_page_url": "https://api.sentra-desa.id/api/v1/public/products?page=1",
    "from": 1,
    "last_page": 5,
    "last_page_url": "https://api.sentra-desa.id/api/v1/public/products?page=5",
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "https://api.sentra-desa.id/api/v1/public/products?page=1",
        "label": "1",
        "active": true
      },
      {
        "url": "https://api.sentra-desa.id/api/v1/public/products?page=2",
        "label": "2",
        "active": false
      },
      {
        "url": "https://api.sentra-desa.id/api/v1/public/products?page=2",
        "label": "Next &raquo;",
        "active": false
      }
    ],
    "next_page_url": "https://api.sentra-desa.id/api/v1/public/products?page=2",
    "path": "https://api.sentra-desa.id/api/v1/public/products",
    "per_page": 12,
    "prev_page_url": null,
    "to": 12,
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
    "email": [
      "Format email tidak valid.",
      "Email sudah terdaftar."
    ],
    "password": [
      "Password minimal harus 8 karakter."
    ]
  }
}
```

---

## 📑 Daftar Modul & Fitur

1. [Sistem & Health Check](#1-sistem--health-check)
2. [Autentikasi & Profil Pengguna](#2-autentikasi--profil-pengguna)
3. [Geospatial & Filter Wilayah Bertingkat](#3-geospatial--filter-wilayah-bertingkat)
4. [Banner & Highlights Homepage](#4-banner--highlights-homepage)
5. [Sentra Produk (Katalog & E-Commerce Desa)](#5-sentra-produk-katalog--e-commerce-desa)
6. [Profil Desa & Desa Kita](#6-profil-desa--desa-kita)
7. [Potensi Desa](#7-potensi-desa)
8. [Desa Wisata](#8-desa-wisata)
9. [Desa Ekspor](#9-desa-ekspor)
10. [BUMDES (Badan Usaha Milik Desa)](#10-bumdes-badan-usaha-milik-desa)
11. [KDMP (Kawasan Perdesaan Mandiri Pangan)](#11-kdmp-kawasan-perdesaan-mandiri-pangan)
12. [Layanan & Informasi Desa](#12-layanan--informasi-desa)
13. [LKDD (Laporan Keuangan Dana Desa)](#13-lkdd-laporan-keuangan-dana-desa)
14. [Artikel & Warta Desa](#14-artikel--warta-desa)
15. [Wishlist Desa (Usulan & Kebutuhan Antardesa)](#15-wishlist-desa-usulan--kebutuhan-antardesa)
16. [Merchant UMKM & Approval Desa](#16-merchant-umkm--approval-desa)

---

### 1. Sistem & Health Check

#### 1.1 Health Check
- **Endpoint:** `GET /health`
- **Auth:** Publik
- **Response (200 OK):**
```json
{
  "status": "OK",
  "message": "SENTRA DESA API is running",
  "timestamp": "2026-09-16T13:35:00.000000Z",
  "version": "v1.0.0"
}
```

---

### 2. Autentikasi & Profil Pengguna

#### 2.1 Registrasi Warga / Pengguna Baru
- **Endpoint:** `POST /v1/public/register`
- **Auth:** Publik
- **Request Body:**
```json
{
  "name": "Budi Santoso",
  "email": "budi.santoso@example.com",
  "password": "password123"
}
```
- **Response (201 Created):**
```json
{
  "status": "success",
  "message": "Pendaftaran berhasil!",
  "access_token": "1|qWeRtYuIoP1234567890abcdef",
  "user": {
    "id": 12,
    "name": "Budi Santoso",
    "email": "budi.santoso@example.com",
    "roles": [],
    "merchant": null
  }
}
```

#### 2.2 Login Pengguna (Single Session Enforced)
- **Endpoint:** `POST /v1/public/login`
- **Auth:** Publik
- **Request Body:**
```json
{
  "email": "budi.santoso@example.com",
  "password": "password123"
}
```
- **Response (200 OK):**
```json
{
  "status": "success",
  "message": "Login berhasil!",
  "access_token": "2|zXcVbNm1234567890qwerty",
  "user": {
    "id": 12,
    "name": "Budi Santoso",
    "email": "budi.santoso@example.com",
    "roles": [
      {
        "id": 3,
        "name": "umkm"
      }
    ],
    "merchant": {
      "id": 5,
      "store_name": "Toko Madu Hutan Asli",
      "status": "active"
    }
  }
}
```

#### 2.3 Get Profil Saya
- **Endpoint:** `GET /v1/profile`
- **Auth:** `Bearer Token`
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "id": 12,
    "name": "Budi Santoso",
    "email": "budi.santoso@example.com",
    "roles": ["umkm"],
    "merchant": {
      "id": 5,
      "store_name": "Toko Madu Hutan Asli",
      "status": "active"
    }
  }
}
```

#### 2.4 Update Profil Saya
- **Endpoint:** `PUT /v1/profile`
- **Auth:** `Bearer Token`
- **Request Body:**
```json
{
  "name": "Budi Santoso Update",
  "email": "budi.santoso@example.com",
  "password": "newpassword123"
}
```
- **Response (200 OK):**
```json
{
  "status": "success",
  "message": "Profil berhasil diperbarui",
  "data": {
    "id": 12,
    "name": "Budi Santoso Update",
    "email": "budi.santoso@example.com"
  }
}
```

#### 2.5 Logout
- **Endpoint:** `POST /v1/logout`
- **Auth:** `Bearer Token`
- **Response (200 OK):**
```json
{
  "status": "success",
  "message": "Berhasil logout"
}
```

---

### 3. Geospatial & Filter Wilayah Bertingkat

#### 3.1 Daftar Provinsi
- **Endpoint:** `GET /v1/public/provinces`
- **Auth:** Publik
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 32,
      "name": "JAWA BARAT",
      "code": "32"
    }
  ]
}
```

#### 3.2 Daftar Kabupaten Berdasarkan Provinsi
- **Endpoint:** `GET /v1/public/provinces/{provinceId}/regencies`
- **Auth:** Publik
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 3204,
      "name": "KABUPATEN BANDUNG",
      "code": "32.04",
      "province_id": 32
    }
  ]
}
```

#### 3.3 Daftar Kecamatan Berdasarkan Kabupaten
- **Endpoint:** `GET /v1/public/regencies/{regencyId}/districts`
- **Auth:** Publik
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 320405,
      "name": "CIWIDEY",
      "code": "32.04.05",
      "regency_id": 3204
    }
  ]
}
```

#### 3.4 Daftar Desa Berdasarkan Kecamatan
- **Endpoint:** `GET /v1/public/districts/{districtId}/villages`
- **Auth:** Publik
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 3204052001,
      "name": "PANUNDAAN",
      "code": "32.04.05.2001",
      "district_id": 320405,
      "area_size": 14.5,
      "population": 6500
    }
  ]
}
```

#### 3.5 Detail Lengkap Wilayah Desa
- **Endpoint:** `GET /v1/public/villages/{villageId}`
- **Auth:** Publik
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "id": 3204052001,
    "name": "PANUNDAAN",
    "code": "32.04.05.2001",
    "district": {
      "id": 320405,
      "name": "CIWIDEY",
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
}
```

---

### 4. Banner & Highlights Homepage

#### 4.1 Daftar Banner Aktif
- **Endpoint:** `GET /v1/public/highlights`
- **Auth:** Publik
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "Festival Kopi & Hasil Bumi Desa 2026",
      "subtitle": "Dukung produk petani lokal langsung dari sumbernya",
      "image_url": "https://api.sentra-desa.id/storage/banners/banner-1.jpg",
      "target_url": "/sentra-produk?category=pertanian",
      "badge_text": "EVENT TERBARU",
      "sort_order": 1,
      "is_active": true
    }
  ]
}
```

---

### 5. Sentra Produk (Katalog & E-Commerce Desa)

#### 5.1 List Produk (Katalog Publik)
- **Endpoint:** `GET /v1/public/products`
- **Auth:** Publik
- **Query Params:**
  - `province_id`, `regency_id`, `district_id`, `village_id`: Filter wilayah
  - `category`: `pertanian`, `perikanan`, `peternakan`, `kerajinan`, `makanan`, `fashion`
  - `merchant_id`: Filter produk toko tertentu
  - `search`: Keyword pencarian produk / deskripsi
  - `limit`: Batasi jumlah item (tanpa pagination)
  - `per_page`: Jumlah item per halaman (default 12)
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 101,
        "name": "Madu Hutan Liar Murni 500ml",
        "slug": "madu-hutan-liar-murni-500ml",
        "description": "Madu murni alami tanpa pemanis buatan langsung dari lebah liar hutan desa.",
        "price": 85000,
        "stock": 45,
        "unit": "botol",
        "category": "makanan",
        "images": [
          "https://api.sentra-desa.id/storage/products/madu-1.jpg"
        ],
        "merchant": {
          "id": 5,
          "store_name": "Toko Madu Hutan Asli",
          "phone": "08123456789",
          "status": "active"
        },
        "village": {
          "id": 3204052001,
          "name": "Desa Panundaan",
          "district": {
            "name": "Ciwidey",
            "regency": {
              "name": "Kabupaten Bandung",
              "province": {
                "name": "Jawa Barat"
              }
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

#### 5.2 Detail Produk
- **Endpoint:** `GET /v1/public/products/{slug_or_id}`
- **Auth:** Publik
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "id": 101,
    "name": "Madu Hutan Liar Murni 500ml",
    "slug": "madu-hutan-liar-murni-500ml",
    "description": "Madu murni alami berkualitas tinggi...",
    "price": 85000,
    "stock": 45,
    "unit": "botol",
    "category": "makanan",
    "weight_grams": 600,
    "images": [
      "https://api.sentra-desa.id/storage/products/madu-1.jpg"
    ],
    "merchant": {
      "id": 5,
      "store_name": "Toko Madu Hutan Asli",
      "address": "Jl. Raya Desa No. 12",
      "phone": "08123456789"
    },
    "village": {
      "id": 3204052001,
      "name": "Desa Panundaan"
    }
  }
}
```

#### 5.3 Tambah Produk (Merchant UMKM)
- **Endpoint:** `POST /v1/umkm/products`
- **Auth:** `Bearer Token` (Role: `umkm`)
- **Request Body:**
```json
{
  "name": "Keripik Singkong Balado",
  "category": "makanan",
  "description": "Keripik singkong gurih khas desa",
  "price": 15000,
  "stock": 100,
  "unit": "bungkus",
  "weight_grams": 250
}
```
- **Response (201 Created):**
```json
{
  "status": "success",
  "message": "Produk berhasil ditambahkan",
  "data": {
    "id": 102,
    "name": "Keripik Singkong Balado",
    "slug": "keripik-singkong-balado",
    "price": 15000,
    "stock": 100
  }
}
```

---

### 6. Profil Desa & Desa Kita

#### 6.1 List Desa Terverifikasi
- **Endpoint:** `GET /v1/public/villages`
- **Auth:** Publik
- **Query Params:** `search`, `province_id`, `regency_id`, `district_id`, `is_featured`, `per_page`
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 3204052001,
        "name": "Desa Panundaan",
        "description": "Desa agrowisata dan sentra penghasil strawberry.",
        "logo": "https://api.sentra-desa.id/storage/villages/logo.png",
        "banner": "https://api.sentra-desa.id/storage/villages/banner.jpg",
        "population": 6500,
        "area_size": 14.5,
        "district": {
          "name": "Ciwidey",
          "regency": {
            "name": "Kabupaten Bandung"
          }
        }
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

#### 6.2 Detail Komprehensif Profil Desa
- **Endpoint:** `GET /v1/public/villages/{village_id}/profile`
- **Auth:** Publik
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "village": {
      "id": 3204052001,
      "name": "Desa Panundaan",
      "vision": "Mewujudkan Desa Mandiri Berbasis Digital dan Wisata 2029",
      "mission": "1. Meningkatkan UMKM Desa...",
      "mayor_name": "Ahmad Subagyo, S.P.",
      "contact_phone": "022-897654",
      "contact_email": "kontak@panundaan.desa.id",
      "population": 6500,
      "area_size": 14.5
    },
    "potentials_preview": [
      { "id": 1, "name": "Perkebunan Strawberry Organik", "category": "pertanian" }
    ],
    "products_preview": [
      { "id": 101, "name": "Madu Hutan Liar Murni 500ml", "price": 85000 }
    ],
    "tourisms_preview": [
      { "id": 1, "name": "Wisata Kebun Teh & Strawberry", "ticket_price": 25000 }
    ],
    "bumdes_preview": [
      { "id": 1, "name": "BUMDes Mitra Sejahtera" }
    ],
    "contents_preview": [
      { "id": 3, "title": "Jadwal Pelayanan KTP Digital Desa" }
    ]
  }
}
```

---

### 7. Potensi Desa

#### 7.1 List Potensi Desa
- **Endpoint:** `GET /v1/public/village-potentials`
- **Auth:** Publik
- **Query Params:** `category`, `village_id`, `search`, `per_page`
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "Sentra Budidaya Kopi Arabika Gunung Tilu",
        "category": "pertanian",
        "description": "Potensi lahan kopi 200 hektar dengan kapasitas panen 50 ton/tahun.",
        "estimated_value": "2.5 Miliar/Tahun",
        "images": [
          "https://api.sentra-desa.id/storage/potentials/kopi.jpg"
        ],
        "village": {
          "id": 3204052001,
          "name": "Desa Panundaan"
        }
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

---

### 8. Desa Wisata

#### 8.1 List Destinasi Desa Wisata
- **Endpoint:** `GET /v1/public/tourisms`
- **Auth:** Publik
- **Query Params:** `category`, `province_id`, `regency_id`, `search`, `per_page`
- **Response (200 OK):**
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
        "ticket_price": 20000,
        "location": "Blok Babakan Kiara RT 02/RW 04",
        "facilities": ["Area Parkir", "Spot Foto", "Gazebo", "Toilet Bersih"],
        "contact_phone": "081298765432",
        "images": [
          "https://api.sentra-desa.id/storage/tourisms/strawberry-1.jpg"
        ],
        "village": {
          "id": 3204052001,
          "name": "Desa Panundaan"
        }
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

#### 8.2 Detail Destinasi Wisata
- **Endpoint:** `GET /v1/public/tourisms/{slug_or_id}`
- **Auth:** Publik
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "Kampung Wisata Strawberry Ciwidey",
    "slug": "kampung-wisata-strawberry-ciwidey",
    "description": "Pengunjung dapat memetik buah strawberry segar langsung dari pohonnya...",
    "ticket_price": 20000,
    "map_url": "https://maps.google.com/?q=-7.1234,107.5678",
    "facilities": ["Parkir", "Toilet", "Resto Lokal"],
    "contact_phone": "081298765432"
  }
}
```

---

### 9. Desa Ekspor

#### 9.1 List Komoditas Ekspor
- **Endpoint:** `GET /v1/public/export-products`
- **Auth:** Publik
- **Response (200 OK):**
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
        "export_destinations": ["Jepang", "Belanda", "Singapura"],
        "monthly_capacity": "10 Ton",
        "moq": "500 Kg",
        "packaging": "Grainpro bag + Jute bag 60kg",
        "images": [
          "https://api.sentra-desa.id/storage/export/coffee-bean.jpg"
        ],
        "village": {
          "name": "Desa Panundaan"
        }
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

---

### 10. BUMDES (Badan Usaha Milik Desa)

#### 10.1 List BUMDes
- **Endpoint:** `GET /v1/public/bumdes`
- **Auth:** Publik
- **Response (200 OK):**
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
        "leader_name": "Drs. Hendra Gunawan",
        "phone": "081321456789",
        "legal_number": "AHU-00123.AH.01.33.TAHUN.2023",
        "business_units": [
          "Pengelolaan Air Bersih Desa",
          "Unit Pengeringan Gabah & Beras",
          "Pengelolaan Tiket Wisata"
        ],
        "village": {
          "name": "Desa Panundaan"
        }
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

---

### 11. KDMP (Kawasan Perdesaan Mandiri Pangan)

#### 11.1 List Kawasan Perdesaan
- **Endpoint:** `GET /v1/public/kdmp`
- **Auth:** Publik
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "code": "KDMP-BDG-01",
        "name": "Kawasan Perdesaan Hortikultura Gunung Tilu",
        "main_commodity": "Sayuran Dataran Tinggi & Kopi",
        "total_area_hectares": 1200,
        "villages_count": 5,
        "description": "Kerjasama antardesa 5 desa untuk integrasi rantai pasok hortikultura."
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

---

### 12. Layanan & Informasi Desa

#### 12.1 List Layanan Publik Desa
- **Endpoint:** `GET /v1/public/village-services`
- **Auth:** Publik
- **Query Params:** `village_id`
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "Surat Keterangan Usaha (SKU) untuk UMKM",
        "description": "Penerbitan surat izin dan keterangan operasional usaha bagi warga desa.",
        "requirements": [
          "Fotokopi KTP Pemohon",
          "Fotokopi Kartu Keluarga",
          "Surat Pengantar RT/RW"
        ],
        "estimated_time": "1 Hari Kerja",
        "cost": "Gratis (Rp 0)",
        "contact_person": "Bagian Pelayanan Umum (Pak Dadang - 0812334455)"
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

---

### 13. LKDD (Laporan Keuangan Dana Desa)

#### 13.1 Laporan Realisasi Anggaran Dana Desa
- **Endpoint:** `GET /v1/public/lkdd`
- **Auth:** Publik
- **Query Params:** `village_id`, `year`
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "year": 2025,
        "total_budget": 1450000000,
        "total_realization": 1425000000,
        "realization_percentage": 98.27,
        "sources": {
          "dana_desa_apbn": 980000000,
          "alokasi_dana_desa_add": 380000000,
          "pendapatan_asli_desa_pad": 90000000
        },
        "allocation_breakdown": [
          { "sector": "Pembangunan Infrastruktur", "budget": 650000000, "realization": 645000000 },
          { "sector": "Pemberdayaan Masyarakat & UMKM", "budget": 350000000, "realization": 340000000 }
        ],
        "village": {
          "name": "Desa Panundaan"
        }
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

---

### 14. Artikel & Warta Desa

#### 14.1 List Artikel Berita
- **Endpoint:** `GET /v1/public/articles`
- **Auth:** Publik
- **Query Params:** `search`, `category`, `per_page`
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "Pelatihan Digital Marketing bagi Pengrajin Anyaman Bambu Desa",
        "slug": "pelatihan-digital-marketing-pengrajin-bambu",
        "thumbnail": "https://api.sentra-desa.id/storage/articles/pelatihan.jpg",
        "category": "Pemberdayaan",
        "author_name": "Admin Desa",
        "published_at": "2026-09-15 08:30:00",
        "village": {
          "name": "Desa Panundaan"
        }
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

---

### 15. Wishlist Desa (Usulan & Kebutuhan Antardesa)

#### 15.1 List Wishlist Pembangunan Desa
- **Endpoint:** `GET /v1/public/wishlists`
- **Auth:** Publik
- **Query Params:** `category`, `status` (`open`, `funded`, `in_progress`, `completed`), `village_id`
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "Pengadaan Mesin Pengering Gabah (Bed Dryer) Kapasitas 5 Ton",
        "category": "sarana_pertanian",
        "target_amount": 120000000,
        "collected_amount": 45000000,
        "status": "open",
        "description": "Dibutuhkan untuk mencegah pembusukan gabah petani saat musim hujan.",
        "village": {
          "name": "Desa Panundaan"
        }
      }
    ],
    "per_page": 12,
    "total": 1
  }
}
```

---

### 16. Merchant UMKM & Approval Desa

#### 16.1 Pendaftaran Toko UMKM
- **Endpoint:** `POST /v1/umkm/merchant/register`
- **Auth:** `Bearer Token`
- **Content-Type:** `multipart/form-data`
- **Form Fields:**
  - `store_name` (string, required)
  - `village_id` (integer, required)
  - `address` (string, required)
  - `phone` (string, required)
  - `description` (string, optional)
  - `business_type` (string, optional)
  - `established_year` (string/digits:4, optional)
  - `logo` (file image, optional, max 2048KB)
  - `payment_proof` (file image, optional, max 2048KB)
- **Response (201 Created):**
```json
{
  "status": "success",
  "message": "Pendaftaran merchant berhasil dikirim, menunggu persetujuan admin.",
  "data": {
    "id": 6,
    "store_name": "Warung Keripik Bu Ani",
    "status": "pending",
    "village": {
      "name": "Desa Panundaan"
    }
  }
}
```

#### 16.2 Get Profil Toko Merchant Saya
- **Endpoint:** `GET /v1/umkm/merchant`
- **Auth:** `Bearer Token` (Role: `umkm`)
- **Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "id": 6,
    "store_name": "Warung Keripik Bu Ani",
    "phone": "081299887766",
    "address": "Dusun 2 RT 03",
    "status": "active",
    "village": {
      "id": 3204052001,
      "name": "Desa Panundaan"
    }
  }
}
```

#### 16.3 Approval Merchant oleh Admin Desa
- **Endpoint:** `POST /v1/village/merchants/{merchant_id}/approve`
- **Auth:** `Bearer Token` (Role: `village_admin` | `superadmin`)
- **Response (200 OK):**
```json
{
  "status": "success",
  "message": "Merchant berhasil disetujui dan aktif."
}
```

#### 16.4 Penolakan Merchant oleh Admin Desa
- **Endpoint:** `POST /v1/village/merchants/{merchant_id}/reject`
- **Auth:** `Bearer Token` (Role: `village_admin` | `superadmin`)
- **Request Body:**
```json
{
  "reason": "Dokumen pendukung / bukti usaha tidak terbaca jelas."
}
```
- **Response (200 OK):**
```json
{
  "status": "success",
  "message": "Pendaftaran merchant ditolak."
}
```
