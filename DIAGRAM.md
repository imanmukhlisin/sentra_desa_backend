# 📐 Diagram Arsitektur & Alur Bisnis Sistem - Sentra-Desa.id

Dokumen ini memuat dokumentasi visual dan teknis seluruh alur bisnis platform **Sentra-Desa.id**, mencakup hak akses 4 peran pengguna (*Superadmin, Village Admin, UMKM, User/Buyer*), alur autentikasi, siklus pengelolaan 11 modul desa, sistem pembayaran *Payment Gateway*, **siklus keanggotaan/subscription UMKM & Desa**, serta **pemetaan kebutuhan layar (*screen mapping*) untuk frontend**.

Seluruh diagram di bawah ini telah diselaraskan dengan implementasi kode backend (`sentra-desa-backend`) berbasis Laravel 12, Filament v3, dan Laravel Sanctum.

---

## 📑 Daftar Isi Diagram

1. [Diagram 1: Alur Akses Sistem & Autentikasi (4 Peran)](#1-diagram-1-alur-akses-sistem--autentikasi-4-peran)
2. [Diagram 2: Alur Onboarding & Verifikasi Merchant UMKM](#2-diagram-2-alur-onboarding--verifikasi-merchant-umkm)
3. [Diagram 3: Siklus Hidup Membership UMKM & Perpanjangan (Lifecycle & Renewal)](#3-diagram-3-siklus-hidup-membership-umkm--perpanjangan-lifecycle--renewal)
4. [Diagram 4: Rancangan Model Subscription B2G untuk Pemerintah Desa](#4-diagram-4-rancangan-model-subscription-b2g-untuk-pemerintah-desa)
5. [Diagram 5: Matriks & Siklus Pengelolaan 11 Modul Fitur Desa](#5-diagram-5-matriks--siklus-pengelolaan-11-modul-fitur-desa)
6. [Diagram 6: Alur Transaksi E-Commerce & Payment Gateway (Escrow)](#6-diagram-6-alur-transaksi-e-commerce--payment-gateway-escrow)
7. [Diagram 7: Alur Crowdfunding Wishlist Desa & CSR Perusahaan](#7-diagram-7-alur-crowdfunding-wishlist-desa--csr-perusahaan)
8. [Diagram 8: Arsitektur Geospatial & Cascading Wilayah](#8-diagram-8-arsitektur-geospatial--cascading-wilayah)
9. [Diagram 9: Blueprint Pemetaan Layar Frontend (Frontend Screen & State Blueprint)](#9-diagram-9-blueprint-pemetaan-layar-frontend-frontend-screen--state-blueprint)

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
    UpgradeUMKM -->|Ya| SubmitMerchant[Isi Form Merchant + Upload Legalitas/Logo/Bukti]
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

## 3. Diagram 3: Siklus Hidup Membership UMKM & Perpanjangan (Lifecycle & Renewal)

Diagram ini menggambarkan status masa aktif toko UMKM dari awal mendaftar, aktif 1 tahun, kedaluwarsa, hingga perpanjangan:

```mermaid
stateDiagram-v2
    [*] --> PENDING : Warga Submit Form & Bukti Daftar (POST /v1/umkm/merchant/register)
    
    PENDING --> REJECTED : Ditolak Admin Desa (POST /v1/village/merchants/{id}/reject)
    PENDING --> APPROVED : Disetujui Admin Desa (POST /v1/village/merchants/{id}/approve)
    
    state APPROVED {
        [*] --> ACTIVE : membership_expires_at = now() + 1 Tahun
        ACTIVE --> EXPIRING_SOON : Sisa Masa Aktif <= 30 Hari
        EXPIRING_SOON --> EXPIRED : membership_expires_at < now()
    }
    
    EXPIRED --> RENEWAL_PENDING : UMKM Upload Bukti Bayar Perpanjangan (POST /v1/umkm/merchant/renew)
    EXPIRING_SOON --> RENEWAL_PENDING : UMKM Perpanjang Dini
    
    RENEWAL_PENDING --> APPROVED : Admin Desa Setujui (membership_expires_at diperpanjang +1 Tahun)
    RENEWAL_PENDING --> EXPIRED : Admin Desa Tolak Bukti Bayar
```

### 📋 Perbandingan Model Membership UMKM:
```
+------------------------------------+------------------------------------+
| 1. MODEL SAAT INI (Semi-Manual)    | 2. MODEL TARGET (Payment Gateway)  |
+------------------------------------+------------------------------------+
| • UMKM transfer manual ke rekening | • UMKM klik tombol "Perpanjang"    |
|   desa / BUMDes                    | • Muncul QRIS / Virtual Account PG |
| • Upload foto struk bukti bayar    | • Begitu lunas, Webhook PG diterima|
| • Admin Desa manual cek mutasi     | • membership_expires_at bertambah  |
| • Admin klik tombol Approve        |   otomatis +1 tahun TANPA antre    |
+------------------------------------+------------------------------------+
```

---

## 4. Diagram 4: Rancangan Model Subscription B2G untuk Pemerintah Desa

Saat ini entitas Desa **BELUM** memiliki sistem subscription (statusnya hanya `is_verified` gratis dari Superadmin). Jika ke depan platform dimonetisasi sebagai **SaaS Pemerintah Desa (B2G)**, berikut adalah rancangan alur bisnisnya:

```mermaid
sequenceDiagram
    autonumber
    actor Kades as Kepala Desa / Sekdes
    participant App as Portal Desa
    participant API as Backend (Laravel)
    participant PG as Payment Gateway / BJB / Bank Daerah
    actor Superadmin as Superadmin Pusat

    Kades->>App: 1. Registrasi Akun Desa Baru
    App->>API: 2. Submit Data Desa, SK Kades, & Kontak Resmi
    API-->>Superadmin: 3. Verifikasi Legalitas Wilayah (is_verified = true)
    
    Note over Kades,App: Pemilihan Paket Langganan Desa Digital
    Kades->>App: 4. Pilih Paket Portal (Misal: Paket Digital Mandiri Rp 3 Juta/Tahun)
    App->>API: 5. POST /v1/village/subscription/checkout
    API->>PG: 6. Buat Invoice Tagihan Resmi (Virtual Account Pemda / QRIS APBDes)
    PG-->>Kades: 7. Nomor VA / Invoice Pemda Terbit
    
    Kades->>PG: 8. Bendahara Desa Melunasi Pembayaran via SP2D / Internet Banking Pemda
    PG-->>API: 9. Webhook: Pembayaran Langganan Desa Lunas
    
    API->>API: 10. Aktifkan Lisensi Desa:
    Note over API: village_tier = 'premium'<br/>license_expires_at = now() + 1 tahun<br/>Buka akses Modul Ekspor, LKDD, & Domain Desa
    API-->>App: 11. Seluruh Fitur Desa Premium Terbuka Penuh
```

---

## 5. Diagram 5: Matriks & Siklus Pengelolaan 11 Modul Fitur Desa

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

## 6. Diagram 6: Alur Transaksi E-Commerce & Payment Gateway (Escrow)

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

## 7. Diagram 7: Alur Crowdfunding Wishlist Desa & CSR Perusahaan

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

## 8. Diagram 8: Arsitektur Geospatial & Cascading Wilayah

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

---

## 9. Diagram 9: Blueprint Pemetaan Layar Frontend (Frontend Screen & State Blueprint)

Karena sisi frontend saat ini masih dalam proses pembangunan, berikut adalah **pemetaan struktur halaman (*routes*), komponen, dan kondisi tampilan (*states*)** yang harus disiapkan oleh pengembang Frontend (Next.js / Flutter):

```mermaid
graph TD
    subgraph Publik [Area Publik]
        Home["/ (Beranda)"] --> Cat["/sentra-produk (Katalog Produk)"]
        Home --> VList["/desa-kita (Direktori Desa)"]
        Home --> Fitur11["11 Modul (Wisata, Ekspor, BUMDes, LKDD, dll)"]
    end

    subgraph AuthArea [Area Autentikasi]
        Home --> Login["/login (Form Login Single-Session)"]
        Home --> Register["/register (Form Daftar Warga)"]
    end

    subgraph UserArea [Area Pengguna Warga]
        Login --> Profile["/profile (Profil Pengguna)"]
        Profile --> MerchantCTA["Banner: Buka Usaha Desa Anda"]
    end

    subgraph MerchantFlow [Alur Pendaftaran & Kelola UMKM]
        MerchantCTA --> MReg["/merchant/register (Form Toko + Upload Bukti)"]
        MReg --> MStatus{"Cek status Toko"}
        
        MStatus -->|status: pending| MPending["Layar: Menunggu Persetujuan Admin Desa"]
        MStatus -->|status: rejected| MReject["Layar: Pendaftaran Ditolak + Alasan"]
        MStatus -->|status: approved| MDash["/merchant/dashboard (Toko Saya)"]
        
        MDash --> MProd["/merchant/products (Katalog Produk Toko)"]
        MProd --> MAdd["/merchant/products/create (Form Tambah Produk)"]
        
        MDash --> MSub["/merchant/membership (Status Masa Aktif)"]
        MSub --> MRenew["Form Upload Bukti Perpanjang / Bayar QRIS"]
    end
```

### 📋 Daftar Layar yang Wajib Dibuat di Frontend:

| Rute Layar Frontend | Endpoint Backend Terkait | Komponen Utama | Kondisi State UI |
| :--- | :--- | :--- | :--- |
| **`/login`** | `POST /v1/public/login` | Input Email, Password, Tombol Masuk | Menampilkan error jika email/password salah. |
| **`/register`** | `POST /v1/public/register` | Input Nama, Email, Password | Simpan Sanctum Token ke LocalStorage/Cookie. |
| **`/merchant/register`** | `POST /v1/umkm/merchant/register` | Form Toko, Select Wilayah Desa, Upload Logo & Bukti Bayar | Jika user sudah punya toko ➡️ redirect ke dashboard/status. |
| **`/merchant/status`** | `GET /v1/umkm/merchant` | Kartu Info Status | - **Pending:** Banner oranye *"Menunggu verifikasi admin desa"*.<br>- **Rejected:** Banner merah *"Pendaftaran ditolak"*.<br>- **Approved:** Redirect ke dashboard. |
| **`/merchant/dashboard`** | `GET /v1/umkm/analytics` | Ringkasan Penjualan, Total Produk, Banner Sisa Masa Aktif | Menampilkan kartu countdown: *"Masa aktif toko sisa X hari lagi"*. |
| **`/merchant/membership`**| `POST /v1/umkm/merchant/renew` | Info Tanggal Kadaluarsa, Form Upload Bukti Perpanjangan | Jika `membership_expires_at` sudah lewat ➡️ Kunci tombol tambah produk & tampilkan peringatan perpanjang toko. |
| **`/merchant/products`** | `GET /v1/umkm/my-products` | List Tabel Produk Toko Sendiri, Tombol Edit/Hapus | Tombol **"Tambah Produk"** hanya bisa diklik jika toko `approved` & masa aktif masih berlaku. |
