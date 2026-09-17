# 📐 Diagram Arsitektur & Alur Bisnis Sistem - Sentra-Desa.id

Dokumen ini memuat dokumentasi visual dan teknis seluruh alur bisnis platform **Sentra-Desa.id**, mencakup hak akses 4 peran pengguna (*Superadmin, Village Admin, UMKM, User/Buyer*), alur autentikasi, siklus pengelolaan 11 modul desa, hingga sistem pembayaran *Payment Gateway (Escrow)*.

Seluruh diagram di bawah ini telah diselaraskan dan tervalidasi dengan implementasi kode pada repositori `sentra-desa-backend` (Laravel 12, Filament v3, dan Laravel Sanctum).

---

## 📑 Daftar Isi Diagram

1. [Diagram 1: Alur Akses Sistem & Autentikasi (4 Peran)](#1-diagram-1-alur-akses-sistem--autentikasi-4-peran)
2. [Diagram 2: Alur Onboarding & Verifikasi Merchant UMKM](#2-diagram-2-alur-onboarding--verifikasi-merchant-umkm)
3. [Diagram 3: Matriks & Siklus Pengelolaan 11 Modul Fitur Desa](#3-diagram-3-matriks--siklus-pengelolaan-11-modul-fitur-desa)
4. [Diagram 4: Alur Transaksi E-Commerce & Payment Gateway (Escrow)](#4-diagram-4-alur-transaksi-e-commerce--payment-gateway-escrow)
5. [Diagram 5: Alur Crowdfunding Wishlist Desa & CSR Perusahaan](#5-diagram-5-alur-crowdfunding-wishlist-desa--csr-perusahaan)
6. [Diagram 6: Arsitektur Geospatial & Cascading Wilayah](#6-diagram-6-arsitektur-geospatial--cascading-wilayah)

---

## 1. Diagram 1: Alur Akses Sistem & Autentikasi (4 Peran)

Sistem memisahkan akses secara tegas menjadi dua pintu masuk:
- **Pintu 1 - Web Backoffice (`/admin`)**: Filament Admin Panel berbasis Session Web khusus aparatur pemerintah.
- **Pintu 2 - Client Apps (Mobile/Web)**: Aplikasi Warga & Pelaku Usaha berbasis REST API dengan Laravel Sanctum Token.

```mermaid
flowchart TD
    Start([Akses Platform Sentra-Desa.id]) --> RoleCheck{Siapa Pengguna?}
    
    %% Alur Administrator (Backoffice)
    RoleCheck -->|Pemerintah Desa / Pusat| WebAdmin[Akses Web: /admin]
    WebAdmin --> FormLoginAdmin[Login Filament Admin]
    FormLoginAdmin --> CheckAdminLevel{Cek user_level}
    CheckAdminLevel -->|superadmin| SuperDashboard[Dashboard Nasional: Kelola Seluruh Wilayah & Konfigurasi]
    CheckAdminLevel -->|village_admin| VillageDashboard[Dashboard Desa: Kelola 1 Wilayah Desa Spesifik]
    CheckAdminLevel -->|Bukan Admin / Role Lain| DeniedAdmin[Akses Ditolak 403 Forbidden]
    
    %% Alur Masyarakat & UMKM (Client Apps)
    RoleCheck -->|Warga / Pembeli / UMKM| ClientApp[Akses Mobile App / Next.js Web]
    ClientApp --> HasAccount{Punya Akun?}
    HasAccount -->|Belum| RegisterUser[Daftar Akun: POST /v1/public/register]
    RegisterUser --> GenerateToken[Terbitkan Sanctum Bearer Token]
    HasAccount -->|Sudah| LoginUser[Login: POST /v1/public/login]
    LoginUser --> SingleSession[Single Session: Revoke Token di Perangkat Lain]
    SingleSession --> GenerateToken
    GenerateToken --> UserHome[Masuk Beranda: Akses Katalog & 11 Fitur Desa]
    
    %% Promosi Menjadi UMKM
    UserHome --> UpgradeUMKM{Mau Jual Produk Desa?}
    UpgradeUMKM -->|Ya| SubmitMerchant[Isi Form Merchant + Upload Legalitas/Logo]
    SubmitMerchant --> StatusPending[Status Toko: PENDING]
    StatusPending --> ReviewVillage[Village Admin Meninjau di /admin]
    ReviewVillage -->|Disetujui| ActiveUMKM[Role Bertambah 'umkm' & Status 'APPROVED']
    ActiveUMKM --> UMKMDashboard[Menu Khusus UMKM: Kelola Produk & Pesanan Masuk]
```

### 🔍 Validasi Teknis Backend:
| Titik Alur | Handler Backend | Logika Validasi |
| :--- | :--- | :--- |
| **Cek Hak Akses Admin** | `User::canAccessPanel(Panel $panel)` | Membatasi hanya `superadmin`, `province_admin`, `regency_admin`, dan `village_admin`. User biasa/UMKM memicu respon **403 Forbidden**. |
| **Isolasi Data Desa** | `getEloquentQuery()` di Resource Filament | Jika `user_level === 'village_admin'`, query otomatis difilter dengan `where('village_id', $user->village_id)`. |
| **Single-Session** | `AuthController@login` | `$user->tokens()->delete()` dijalankan sebelum menerbitkan token baru. Sesi di perangkat lain langsung terputus. |
| **Proteksi Produk UMKM** | `ProductController@store` | Memvalidasi `if (!$merchant || $merchant->status !== 'approved') return 403;`. |

---

## 2. Diagram 2: Alur Onboarding & Verifikasi Merchant UMKM

Alur transformasi dari akun warga biasa menjadi pedagang resmi terverifikasi:

```mermaid
sequenceDiagram
    autonumber
    actor Warga as Warga / Calon UMKM
    participant App as Frontend (Client App)
    participant API as Backend (Laravel API)
    participant DB as Database
    actor AdminDesa as Village Admin (/admin)

    Warga->>App: 1. Buka Menu "Daftar Sebagai Merchant"
    Warga->>App: 2. Lengkapi Form Toko, Upload Logo & Bukti Usaha
    App->>API: 3. POST /v1/umkm/merchant/register (Multipart Form-Data)
    
    API->>DB: 4. Cek apakah user sudah punya toko?
    alt User Sudah Terdaftar Toko
        API-->>App: 4a. Response HTTP 422 ("Anda sudah terdaftar sebagai merchant")
    else Pendaftaran Baru
        API->>DB: 4b. Simpan record Merchant (status: 'pending', logo & proof disimpan di disk public)
        API-->>App: 5. Response HTTP 201 ("Pendaftaran merchant berhasil dikirim, menunggu persetujuan admin")
    end

    Note over AdminDesa,API: Proses Moderasi oleh Pemerintah Desa
    AdminDesa->>API: 6. Login /admin > Buka Menu Data UMKM (Filter: Pending)
    AdminDesa->>AdminDesa: 7. Meninjau fisik usaha / dokumen pendukung warga desa
    
    alt Dokumen Sah & Terverifikasi
        AdminDesa->>API: 8a. POST /v1/village/merchants/{id}/approve
        API->>DB: 9a. Update status='approved', approved_at=now(), membership_expires_at=+1 tahun
        API-->>Warga: 10a. Toko Aktif! Fitur CRUD Tambah Produk & Toko Saya Terbuka
    else Dokumen Tidak Memenuhi Syarat
        AdminDesa->>API: 8b. POST /v1/village/merchants/{id}/reject
        API->>DB: 9b. Update status='rejected'
        API-->>Warga: 10b. Notifikasi Penolakan Toko diterima Warga
    end
```

---

## 3. Diagram 3: Matriks & Siklus Pengelolaan 11 Modul Fitur Desa

### Matriks Tanggung Jawab Data (RACI):
| No | Modul Fitur | Creator / Input | Validator / Approval | Konsumen Publik |
| :---: | :--- | :--- | :--- | :--- |
| **1** | **Profil Desa** | `village_admin` | `superadmin` | Warga, Publik, Wisatawan |
| **2** | **Potensi Desa** | `village_admin` | `superadmin` | Calon Investor, Industri |
| **3** | **Informasi / Layanan** | `village_admin` | Langsung Publish | Warga Desa Lokal |
| **4** | **Sentra Produk** | `umkm` (Merchant) | `village_admin` | Konsumen se-Indonesia |
| **5** | **Desa Ekspor** | `umkm` / Koperasi | `village_admin` / Pusat | Buyer Pasar Internasional |
| **6** | **Desa Wisata** | Pokdarwis / Desa | `village_admin` | Wisatawan Domestik & Mancanegara |
| **7** | **BUMDES** | Direktur BUMDes | `village_admin` | Mitra B2B, Pemda, Warga |
| **8** | **KDMP (Pangan)** | Admin Kecamatan/Kab | `superadmin` | Badan Pangan, Mitra Offtaker |
| **9** | **LKDD (Keuangan)** | Bendahara Desa | BPD / Superadmin | Warga (Transparansi Realisasi) |
| **10** | **Artikel Desa** | Perangkat / Kontributor | `village_admin` | Pembaca Umum & Warga |
| **11** | **Wishlist Desa** | Pemerintah Desa | `village_admin` | Donatur, Filantropi, CSR Perusahaan |

### Siklus Alur Data:
```mermaid
flowchart LR
    subgraph Tahap1 [1. Penginputan Data]
        VA[Village Admin via /admin] -->|Input| DataDesa[Profil, Potensi, Layanan, LKDD, Wisata, BUMDes]
        UMKM[Pelaku UMKM via Client App] -->|Unggah| DataProduk[Katalog Produk & Galeri Foto]
        KOP[Koperasi Desa Antardesa] -->|Input| DataKawasan[Kawasan KDMP & Produk Ekspor]
    end

    subgraph Tahap2 [2. Validasi & Kurasi]
        DataDesa & DataProduk & DataKawasan --> FilterData{Validasi Sistem & Hak Akses}
        FilterData -->|Lolos| StorageDB[(Database Sentra-Desa)]
    end

    subgraph Tahap3 [3. Publikasi Terintegrasi]
        StorageDB --> GatewayAPI[REST API: /v1/public/*]
        GatewayAPI --> Show1[Katalog Sentra Produk Desa]
        GatewayAPI --> Show2[Portal Transparansi LKDD]
        GatewayAPI --> Show3[Direktori Desa Wisata & Ekspor]
        GatewayAPI --> Show4[Layanan Surat Menyurat Warga]
    end
```

---

## 4. Diagram 4: Alur Transaksi E-Commerce & Payment Gateway (Escrow)

Arsitektur sistem pembayaran digital menggunakan rekening penampung aman (*Escrow*), terintegrasi dengan Payment Gateway (Midtrans / Xendit):

```mermaid
sequenceDiagram
    autonumber
    actor Buyer as Pembeli (User)
    participant App as Frontend (Next.js / Flutter)
    participant API as Backend (Laravel API)
    participant PG as Payment Gateway (Midtrans/Xendit)
    actor Merchant as Toko UMKM
    actor Kurir as Jasa Pengiriman (Ekspedisi)

    Buyer->>App: 1. Masukkan Produk ke Keranjang & Klik "Checkout"
    Buyer->>App: 2. Input Alamat Pengiriman & Pilih Kurir/Ongkir
    App->>API: 3. POST /v1/buyer/checkout (Cart Items, Address, Courier)
    
    API->>API: 4. Generate Order (INV-202609-XXXX) Status: UNPAID
    API->>PG: 5. Request Snap Token / Payment URL (Subtotal + Ongkir + Fee)
    PG-->>API: 6. Kembalikan Snap Token & Redirect URL
    API-->>App: 7. Kirimkan Snap Token ke Frontend Client
    
    App->>Buyer: 8. Tampilkan Dialog Bayar (QRIS, VA Bank, E-Wallet)
    Buyer->>PG: 9. Pembeli Melakukan Pelunasan (Scan QRIS / Transfer VA)
    
    PG-->>API: 10. Webhook HTTP Notification: Pembayaran Sukses (Settlement)
    API->>API: 11. Ubah Status Order menjadi "PAID"
    API->>Merchant: 12. Notifikasi Pesanan Masuk (Push Notif / WhatsApp Gateway)
    
    Merchant->>Kurir: 13. Kemas Pesanan & Serahkan ke Ekspedisi
    Merchant->>App: 14. Masukkan Nomor Resi Pengiriman
    App->>API: 15. POST /v1/umkm/orders/{id}/shipping (Nomor Resi)
    API-->>Buyer: 16. Notifikasi: "Pesanan Dikirim dengan Resi: XXX"
    
    Kurir-->>Buyer: 17. Paket Tiba di Alamat Pembeli
    Buyer->>App: 18. Klik "Konfirmasi Pesanan Diterima"
    App->>API: 19. POST /v1/buyer/orders/{id}/complete
    
    API->>API: 20. Eksekusi Pencairan Dana Escrow:
    Note over API: 1. Saldo Bersih -> Dompet Merchant UMKM<br/>2. Bagi Hasil BUMDes/PADes -> Rekening Desa<br/>3. Biaya Layanan -> Platform Sentra-Desa
```

---

## 5. Diagram 5: Alur Crowdfunding Wishlist Desa & CSR Perusahaan

Alur penggalangan dukungan dan pembiayaan sarana/prasarana fisik desa:

```mermaid
sequenceDiagram
    autonumber
    actor AdminDesa as Pemerintah Desa
    actor Donatur as Warga / Program CSR
    participant App as Web Sentra Desa
    participant API as Backend (Laravel)
    participant PG as Payment Gateway
    participant KasDesa as Rekening Pengadaan Desa

    AdminDesa->>App: 1. Ajukan Kebutuhan Sarana (Misal: "Mesin Pengering Gabah 5 Ton - Target Rp 120 Juta")
    App->>API: 2. POST /v1/village/wishlists (Data Usulan, RAB, Foto Lokasi)
    API-->>App: 3. Terbit di Etalase Publik Wishlist Desa (Status: Open)
    
    Donatur->>App: 4. Pilih Usulan Desa & Masukkan Nilai Donasi/Dukungan
    App->>API: 5. POST /v1/public/wishlists/{id}/donate (Nominal, Nama Donatur)
    API->>PG: 6. Request Tagihan Pembayaran
    PG-->>Donatur: 7. Donatur Melunasi Tagihan via QRIS / Transfer Perusahaan
    PG-->>API: 8. Callback Webhook: Pembayaran Donasi Terverifikasi
    
    API->>API: 9. Akumulasikan Dana Terkumpul pada Item Wishlist
    alt Target Tercapai (100%)
        API->>API: 10a. Update Status: "FUNDED" / "IN_PROGRESS"
        API->>KasDesa: 11a. Penyaluran Dana Otomatis ke Kas Pengadaan Desa
        AdminDesa->>App: 12a. Upload Bukti Pengadaan Barang & Laporan Realisasi
        API-->>Donatur: 13a. Laporan Pertanggungjawaban Otomatis Terkirim ke Donatur
    else Target Belum Tercapai
        API-->>App: 10b. Update Progress Bar Persentase Terkumpul di Halaman Publik
    end
```

---

## 6. Diagram 6: Arsitektur Geospatial & Cascading Wilayah

Struktur data wilayah nasional hierarkis 4 tingkat yang digunakan untuk memfilter seluruh katalog, desa wisata, potensi, hingga produk UMKM:

```mermaid
graph TD
    Prov[1. PROVINSI<br/>Tabel: provinces] -->|1 : N| Reg[2. KABUPATEN / KOTA<br/>Tabel: regencies]
    Reg -->|1 : N| Dis[3. KECAMATAN<br/>Tabel: districts]
    Dis -->|1 : N| Vil[4. DESA<br/>Tabel: villages]
    
    subgraph Data Terikat Wilayah Desa
        Vil --> P[Sentra Produk]
        Vil --> T[Desa Wisata]
        Vil --> B[BUMDes]
        Vil --> PO[Potensi Desa]
        Vil --> E[Desa Ekspor]
        Vil --> L[Layanan Desa]
        Vil --> LK[LKDD Dana Desa]
        Vil --> W[Wishlist Desa]
    end
```

### Format Query API Cascading:
```http
GET /api/v1/public/products?province_id=32&regency_id=3204&district_id=320405&village_id=3204052001
```
*Backend menjalankan kueri relasional `whereHas` bertingkat untuk menyaring data secara instan dan akurat.*
