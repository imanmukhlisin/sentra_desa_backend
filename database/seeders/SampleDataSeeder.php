<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Village;
use App\Models\Tourism;
use App\Models\Product;
use App\Models\VillagePotential;
use App\Models\ExportProduct;
use App\Models\Bumdes;
use App\Models\Kdmp;
use App\Models\VillageService;
use App\Models\Merchant;
use App\Models\VillageContent;
use Illuminate\Support\Str;

/**
 * Seeder untuk data contoh seluruh modul Sentra Desa.
 * Menandai 6 desa sebagai verified, lalu mengisi setiap tabel modul
 * dengan 3-6 data sample agar admin panel dan Flutter frontend terisi.
 */
class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil 6 desa dari berbagai provinsi
        $villages = Village::with('district.regency.province')
            ->inRandomOrder()
            ->take(6)
            ->get();

        if ($villages->count() < 6) {
            $this->command->error('Kurang dari 6 desa di database. Import data wilayah dulu.');
            return;
        }

        // Verifikasi desa-desa terpilih agar muncul di public API
        foreach ($villages as $v) {
            $v->update([
                'is_verified' => true,
                'is_featured' => true,
                'description' => 'Desa ' . $v->name . ' merupakan desa yang memiliki potensi besar di bidang pertanian, pariwisata, dan UMKM. Masyarakat desa aktif mengembangkan produk unggulan daerah.',
                'head_name' => 'Kepala Desa ' . $v->name,
                'phone' => '0812' . rand(10000000, 99999999),
                'population' => rand(2000, 15000),
                'area_size' => rand(200, 5000) / 100,
            ]);
        }

        $userId = 1; // Super Admin

        // ========================
        // 1. DESA WISATA (Tourisms)
        // ========================
        $tourismData = [
            ['name' => 'Wisata Alam Bukit Pelangi', 'category' => 'alam', 'desc' => 'Destinasi wisata alam dengan pemandangan bukit dan hamparan hijau yang memukau. Cocok untuk hiking, camping, dan fotografi alam.', 'fee' => 15000, 'facilities' => 'Toilet, Warung, Area Parkir, Gazebo, Spot Foto'],
            ['name' => 'Desa Wisata Budaya Nusantara', 'category' => 'budaya', 'desc' => 'Desa wisata yang melestarikan tradisi budaya lokal dengan pertunjukan seni, kerajinan tangan, dan kuliner tradisional.', 'fee' => 25000, 'facilities' => 'Pendopo, Galeri Budaya, Workshop Area, Penginapan, Restoran'],
            ['name' => 'Taman Edukasi Pertanian', 'category' => 'edukasi', 'desc' => 'Wisata edukasi tentang pertanian organik, peternakan, dan pengolahan hasil bumi. Tersedia paket belajar untuk anak-anak dan keluarga.', 'fee' => 20000, 'facilities' => 'Lahan Praktik, Aula, Toilet, Parkir, Kantin'],
            ['name' => 'Air Terjun Tirta Mukti', 'category' => 'alam', 'desc' => 'Air terjun dengan ketinggian 30 meter yang dikelilingi hutan tropis. Airnya jernih dan segar, cocok untuk berenang.', 'fee' => 10000, 'facilities' => 'Jalan Setapak, Shelter, Toilet, Warung'],
            ['name' => 'Kampung Kerajinan Batik', 'category' => 'budaya', 'desc' => 'Sentra pembuatan batik tradisional dengan teknik tulis dan cap. Pengunjung dapat belajar membatik langsung dari pengrajin.', 'fee' => 35000, 'facilities' => 'Studio Batik, Galeri, Showroom, Toilet, Parkir'],
        ];

        foreach ($tourismData as $i => $t) {
            Tourism::create([
                'village_id' => $villages[$i % $villages->count()]->id,
                'created_by' => $userId,
                'name' => $t['name'],
                'slug' => Str::slug($t['name']),
                'description' => $t['desc'],
                'short_description' => Str::limit($t['desc'], 100),
                'category' => $t['category'],
                'entrance_fee' => $t['fee'],
                'opening_hours' => '08:00 - 17:00 WIB',
                'facilities' => $t['facilities'],
                'address' => 'Desa ' . $villages[$i % $villages->count()]->name,
                'latitude' => -6.5 + (rand(-200, 200) / 100),
                'longitude' => 106.8 + (rand(-500, 500) / 100),
                'is_active' => true,
                'is_featured' => $i < 3,
                'view_count' => rand(50, 500),
            ]);
        }
        $this->command->info('✅ 5 Tourism records created');

        // ========================
        // 2. MERCHANTS & PRODUCTS (Sentra Produk)
        // ========================
        $merchantData = [
            ['store' => 'Toko Oleh-Oleh Nusantara', 'type' => 'oleh_oleh', 'desc' => 'Menjual berbagai oleh-oleh khas desa, makanan ringan, dan kerajinan tangan.'],
            ['store' => 'Batik Ayu Collection', 'type' => 'fashion', 'desc' => 'Koleksi batik tulis dan cap berkualitas tinggi dengan motif khas daerah.'],
            ['store' => 'Tani Makmur Organik', 'type' => 'pertanian', 'desc' => 'Produk pertanian organik segar langsung dari ladang petani desa.'],
        ];

        $merchants = [];
        foreach ($merchantData as $i => $m) {
            $merchant = Merchant::create([
                'user_id' => $userId,
                'village_id' => $villages[$i]->id,
                'store_name' => $m['store'],
                'description' => $m['desc'],
                'phone' => '0813' . rand(10000000, 99999999),
                'address' => 'Desa ' . $villages[$i]->name,
                'business_type' => $m['type'],
                'established_year' => rand(2018, 2024),
                'status' => 'approved',
                'payment_proof' => 'seeder/payment-proof-placeholder.jpg',
                'approved_at' => now(),
                'approved_by' => $userId,
            ]);
            $merchants[] = $merchant;
        }
        $this->command->info('✅ 3 Merchant records created');

        $productData = [
            ['name' => 'Keripik Singkong Pedas Manis', 'cat' => 'makanan_minuman', 'price' => 25000, 'stock' => 100, 'desc' => 'Keripik singkong renyah dengan bumbu pedas manis khas desa. Dibuat dari singkong pilihan yang dipanen langsung dari ladang.'],
            ['name' => 'Batik Tulis Motif Mega Mendung', 'cat' => 'fashion', 'price' => 350000, 'stock' => 20, 'desc' => 'Batik tulis asli dengan motif mega mendung yang dikerjakan selama 2 minggu oleh pengrajin terampil.'],
            ['name' => 'Kopi Arabika Single Origin', 'cat' => 'makanan_minuman', 'price' => 75000, 'stock' => 50, 'desc' => 'Kopi arabika premium single origin dari dataran tinggi desa. Roasting medium dengan cita rasa cokelat dan buah.'],
            ['name' => 'Madu Hutan Asli', 'cat' => 'pertanian', 'price' => 120000, 'stock' => 30, 'desc' => 'Madu murni dari lebah hutan yang dipanen secara tradisional. Tanpa campuran, kaya antioksidan.'],
            ['name' => 'Gula Aren Organik', 'cat' => 'makanan_minuman', 'price' => 45000, 'stock' => 80, 'desc' => 'Gula aren murni tanpa bahan kimia, diproduksi dari nira segar pohon aren di sekitar desa.'],
            ['name' => 'Anyaman Bambu Tas Wanita', 'cat' => 'kerajinan', 'price' => 150000, 'stock' => 15, 'desc' => 'Tas wanita elegan dari anyaman bambu pilihan, dikerjakan oleh pengrajin lokal dengan desain modern.'],
        ];

        foreach ($productData as $i => $p) {
            Product::create([
                'merchant_id' => $merchants[$i % count($merchants)]->id,
                'village_id' => $villages[$i % $villages->count()]->id,
                'name' => $p['name'],
                'category' => $p['cat'],
                'price' => $p['price'],
                'discount_price' => $i % 2 === 0 ? $p['price'] * 0.85 : null,
                'stock' => $p['stock'],
                'unit' => 'pcs',
                'description' => $p['desc'],
                'is_available' => true,
                'is_featured' => $i < 3,
                'view_count' => rand(20, 300),
                'order_count' => rand(5, 50),
            ]);
        }
        $this->command->info('✅ 6 Product records created');

        // ========================
        // 3. POTENSI DESA (Village Potentials)
        // ========================
        $potentialData = [
            ['name' => 'Lahan Pertanian Padi Organik', 'cat' => 'pertanian', 'desc' => 'Lahan sawah subur seluas 150 hektar yang menghasilkan padi organik berkualitas tinggi. Sudah tersertifikasi organik nasional.', 'area' => 150, 'volume' => 500, 'value' => 2500000000],
            ['name' => 'Perkebunan Kopi Robusta', 'cat' => 'perkebunan', 'desc' => 'Perkebunan kopi robusta di dataran tinggi 800m dpl. Menghasilkan biji kopi dengan cita rasa khas dan diminati pasar ekspor.', 'area' => 80, 'volume' => 120, 'value' => 3600000000],
            ['name' => 'Sumber Mata Air Panas', 'cat' => 'pariwisata', 'desc' => 'Sumber mata air panas alami dengan suhu 42°C yang dipercaya memiliki khasiat kesehatan. Berpotensi dikembangkan menjadi destinasi wellness tourism.', 'area' => 5, 'volume' => 0, 'value' => 500000000],
            ['name' => 'Kawasan Hutan Bambu', 'cat' => 'kehutanan', 'desc' => 'Hutan bambu seluas 45 hektar yang menyediakan bahan baku untuk kerajinan, konstruksi, dan industri kreatif.', 'area' => 45, 'volume' => 200, 'value' => 800000000],
            ['name' => 'Tambak Udang Vaname', 'cat' => 'perikanan', 'desc' => 'Tambak udang vaname dengan teknologi bioflok. Produksi tinggi dan sudah memenuhi standar ekspor ke Jepang dan Korea.', 'area' => 25, 'volume' => 80, 'value' => 4800000000],
        ];

        foreach ($potentialData as $i => $p) {
            VillagePotential::create([
                'village_id' => $villages[$i % $villages->count()]->id,
                'created_by' => $userId,
                'category' => $p['cat'],
                'name' => $p['name'],
                'description' => $p['desc'],
                'total_area' => $p['area'],
                'production_volume' => $p['volume'],
                'economic_value' => $p['value'],
                'is_investment_ready' => $i % 2 === 0,
                'development_status' => ['teridentifikasi', 'dikembangkan', 'produktif', 'dikembangkan', 'produktif'][$i],
                'is_active' => true,
            ]);
        }
        $this->command->info('✅ 5 Village Potential records created');

        // ========================
        // 4. DESA EKSPOR (Export Products)
        // ========================
        $exportData = [
            ['name' => 'Kopi Premium Grade A', 'hs' => '090111', 'countries' => ['Jepang', 'Korea Selatan', 'Singapura'], 'value' => 150000, 'status' => 'sudah_ekspor', 'certs' => ['SNI', 'Organic', 'Fair Trade']],
            ['name' => 'Batik Tulis Premium', 'hs' => '621490', 'countries' => ['Malaysia', 'Jepang', 'Australia'], 'value' => 250000, 'status' => 'sudah_ekspor', 'certs' => ['SNI', 'Geographical Indication']],
            ['name' => 'Udang Vaname Beku', 'hs' => '030617', 'countries' => ['Jepang', 'USA', 'Uni Eropa'], 'value' => 500000, 'status' => 'proses_ekspor', 'certs' => ['HACCP', 'BAP', 'ASC']],
            ['name' => 'Minyak Kelapa VCO', 'hs' => '151311', 'countries' => ['Singapura', 'Hong Kong', 'Taiwan'], 'value' => 80000, 'status' => 'potensial', 'certs' => ['SNI', 'Organic']],
        ];

        foreach ($exportData as $i => $e) {
            ExportProduct::create([
                'village_id' => $villages[$i % $villages->count()]->id,
                'created_by' => $userId,
                'name' => $e['name'],
                'slug' => Str::slug($e['name']),
                'description' => 'Produk ekspor unggulan desa ' . $villages[$i % $villages->count()]->name . '. ' . $e['name'] . ' memiliki kualitas internasional dan diminati pasar global.',
                'hs_code' => $e['hs'],
                'destination_countries' => $e['countries'],
                'export_status' => $e['status'],
                'certifications' => $e['certs'],
                'has_export_license' => $i < 2,
                'export_value' => $e['value'],
                'unit' => 'kg',
                'contact_person' => 'Bapak Hadi ' . ($i + 1),
                'contact_phone' => '0821' . rand(10000000, 99999999),
                'is_active' => true,
                'is_featured' => $i < 2,
            ]);
        }
        $this->command->info('✅ 4 Export Product records created');

        // ========================
        // 5. BUMDES
        // ========================
        $bumdesData = [
            ['name' => 'BUMDes Makmur Jaya', 'units' => ['Unit Perdagangan', 'Unit Pertanian', 'Unit Simpan Pinjam'], 'cap' => 500000000, 'rev' => 1200000000, 'emp' => 25, 'perf' => 'maju'],
            ['name' => 'BUMDes Sejahtera Mandiri', 'units' => ['Unit Wisata', 'Unit Kerajinan', 'Unit Peternakan'], 'cap' => 300000000, 'rev' => 800000000, 'emp' => 18, 'perf' => 'berkembang'],
            ['name' => 'BUMDes Gotong Royong', 'units' => ['Unit Air Bersih', 'Unit Pengelolaan Sampah', 'Unit Pasar Desa'], 'cap' => 200000000, 'rev' => 450000000, 'emp' => 12, 'perf' => 'berkembang'],
            ['name' => 'BUMDes Sumber Rezeki', 'units' => ['Unit Pengolahan Hasil Tani', 'Unit Distribusi', 'Unit Digital'], 'cap' => 750000000, 'rev' => 2000000000, 'emp' => 35, 'perf' => 'maju'],
        ];

        foreach ($bumdesData as $i => $b) {
            Bumdes::create([
                'village_id' => $villages[$i % $villages->count()]->id,
                'created_by' => $userId,
                'name' => $b['name'],
                'slug' => Str::slug($b['name']),
                'description' => $b['name'] . ' adalah Badan Usaha Milik Desa yang bergerak di berbagai sektor usaha untuk meningkatkan perekonomian masyarakat desa.',
                'legal_number' => 'SK/' . rand(100, 999) . '/BUMDES/' . rand(2020, 2024),
                'established_date' => now()->subYears(rand(1, 5))->format('Y-m-d'),
                'initial_capital' => $b['cap'],
                'business_units' => $b['units'],
                'director_name' => 'Direktur ' . ['Ahmad', 'Budi', 'Cahyono', 'Dewi'][$i],
                'phone' => '0856' . rand(10000000, 99999999),
                'email' => strtolower(Str::slug($b['name'], '.')) . '@bumdes.id',
                'address' => 'Desa ' . $villages[$i % $villages->count()]->name,
                'annual_revenue' => $b['rev'],
                'employee_count' => $b['emp'],
                'is_active' => true,
                'performance_category' => $b['perf'],
            ]);
        }
        $this->command->info('✅ 4 Bumdes records created');

        // ========================
        // 6. KDMP (Kawasan Perdesaan)
        // ========================
        // Get 2 regency IDs from the selected villages
        $regencyIds = $villages->map(fn($v) => $v->district->regency_id)->unique()->take(2)->values();

        if ($regencyIds->count() >= 1) {
            $villageIdsForKdmp1 = $villages->take(3)->pluck('id')->toArray();
            Kdmp::create([
                'name' => 'Kawasan Agropolitan Lembah Subur',
                'code' => 'KDMP-' . rand(1000, 9999),
                'regency_id' => $regencyIds[0],
                'village_ids' => $villageIdsForKdmp1,
                'description' => 'Kawasan perdesaan agropolitan yang mengintegrasikan pertanian, pengolahan hasil tani, dan pemasaran. Terdiri dari beberapa desa yang saling mendukung rantai nilai komoditas unggulan.',
                'category' => 'kawasan_agropolitan',
                'total_area' => 1250.50,
                'total_population' => rand(15000, 50000),
                'village_count' => 3,
                'main_commodities' => 'Padi Organik, Kopi, Sayuran',
                'economic_potential' => 15000000000,
                'is_active' => true,
                'established_year' => 2022,
            ]);

            if ($regencyIds->count() >= 2) {
                $villageIdsForKdmp2 = $villages->skip(3)->take(3)->pluck('id')->toArray();
                Kdmp::create([
                    'name' => 'Kawasan Minapolitan Pesisir Timur',
                    'code' => 'KDMP-' . rand(1000, 9999),
                    'regency_id' => $regencyIds[1],
                    'village_ids' => $villageIdsForKdmp2,
                    'description' => 'Kawasan perdesaan minapolitan yang fokus pada perikanan budidaya dan hasil laut. Mengembangkan cold chain dan fasilitas pengolahan ikan modern.',
                    'category' => 'kawasan_minapolitan',
                    'total_area' => 850.00,
                    'total_population' => rand(10000, 30000),
                    'village_count' => 3,
                    'main_commodities' => 'Udang, Ikan Bandeng, Rumput Laut',
                    'economic_potential' => 8000000000,
                    'is_active' => true,
                    'established_year' => 2023,
                ]);
            }
        }
        $this->command->info('✅ KDMP records created');

        // ========================
        // 7. LAYANAN DESA (Village Services)
        // ========================
        $serviceData = [
            ['name' => 'Pembuatan Surat Keterangan Domisili', 'cat' => 'administrasi_kependudukan', 'desc' => 'Layanan pembuatan surat keterangan domisili untuk keperluan administrasi, pekerjaan, dan pendidikan.', 'req' => '["KTP asli dan fotokopi","Kartu Keluarga","Surat pengantar RT/RW","Pas foto 3x4 (2 lembar)"]', 'steps' => '["Ambil formulir di kantor desa","Isi formulir dan lengkapi persyaratan","Serahkan ke petugas","Tunggu proses verifikasi (1-2 hari)","Ambil surat yang sudah jadi"]', 'days' => 2, 'fee' => 0],
            ['name' => 'Pengurusan Surat Keterangan Usaha', 'cat' => 'ekonomi', 'desc' => 'Surat keterangan usaha untuk pelaku UMKM sebagai syarat perizinan dan akses permodalan.', 'req' => '["KTP pemilik usaha","Kartu Keluarga","Foto lokasi usaha","Bukti kepemilikan/sewa tempat"]', 'steps' => '["Ajukan permohonan di kantor desa","Lampirkan dokumen persyaratan","Verifikasi oleh perangkat desa","Penandatanganan oleh Kepala Desa","Pengambilan surat"]', 'days' => 3, 'fee' => 0],
            ['name' => 'Pelayanan Posyandu', 'cat' => 'kesehatan', 'desc' => 'Layanan kesehatan dasar untuk ibu hamil, bayi, dan balita meliputi imunisasi, pemeriksaan, dan pemberian makanan tambahan.', 'req' => '["Kartu Posyandu/KMS","Buku KIA","Kartu BPJS/KIS"]', 'steps' => '["Datang ke Posyandu sesuai jadwal","Registrasi dan penimbangan","Pemeriksaan kesehatan","Imunisasi (jika jadwal)","Konseling gizi"]', 'days' => 1, 'fee' => 0],
            ['name' => 'Pengajuan Bantuan Sosial', 'cat' => 'sosial', 'desc' => 'Layanan pendataan dan pengajuan bantuan sosial dari pemerintah pusat dan daerah untuk warga kurang mampu.', 'req' => '["KTP dan KK","SKTM dari RT/RW","Foto rumah","Bukti penghasilan"]', 'steps' => '["Registrasi di kantor desa","Verifikasi data oleh perangkat","Survey lapangan","Pengusulan ke kecamatan","Menunggu persetujuan"]', 'days' => 14, 'fee' => 0],
        ];

        foreach ($serviceData as $i => $s) {
            VillageService::create([
                'village_id' => $villages[$i % $villages->count()]->id,
                'name' => $s['name'],
                'slug' => Str::slug($s['name']),
                'description' => $s['desc'],
                'category' => $s['cat'],
                'requirements' => $s['req'],
                'process_steps' => $s['steps'],
                'estimated_days' => $s['days'],
                'fee' => $s['fee'],
                'contact_person' => 'Petugas Desa',
                'contact_phone' => '0857' . rand(10000000, 99999999),
                'office_hours' => 'Senin - Jumat, 08:00 - 15:00 WIB',
                'is_online_available' => $i < 2,
                'online_url' => $i < 2 ? 'https://sentradesa.id/layanan' : null,
                'is_active' => true,
            ]);
        }
        $this->command->info('✅ 4 Village Service records created');

        // ========================
        // 8. KONTEN DESA (Village Contents) — Artikel & Berita
        // ========================
        $contentData = [
            ['title' => 'Program Digitalisasi Desa 2026', 'cat' => 'informasi', 'content' => '<p>Desa kami telah memulai program digitalisasi layanan publik. Seluruh pengurusan surat menyurat kini dapat dilakukan secara online melalui platform Sentra Desa.</p><p>Program ini bertujuan untuk meningkatkan efisiensi pelayanan dan transparansi pengelolaan dana desa.</p>'],
            ['title' => 'Profil Desa Wisata Unggulan', 'cat' => 'profil', 'content' => '<p>Desa kami memiliki potensi wisata yang luar biasa dengan keindahan alam pegunungan, air terjun, dan keanekaragaman budaya lokal.</p><p>Setiap tahun ribuan wisatawan datang untuk menikmati atraksi budaya dan kuliner khas desa.</p>'],
            ['title' => 'Festival Panen Raya Desa', 'cat' => 'informasi', 'content' => '<p>Festival Panen Raya akan diselenggarakan pada bulan depan. Kegiatan meliputi perlombaan, pameran produk desa, dan pasar rakyat.</p><p>Seluruh warga dan pengunjung diajak berpartisipasi dalam merayakan hasil kerja keras petani desa.</p>'],
        ];

        foreach ($contentData as $i => $c) {
            VillageContent::create([
                'village_id' => $villages[$i % $villages->count()]->id,
                'title' => $c['title'],
                'slug' => Str::slug($c['title']),
                'category' => $c['cat'],
                'content' => $c['content'],
            ]);
        }
        $this->command->info('✅ 3 Village Content records created');

        $this->command->info('');
        $this->command->info('🎉 Semua data sample berhasil di-seed!');
        $this->command->info('   Villages verified: ' . $villages->count());
        $this->command->info('   Tourisms: 5');
        $this->command->info('   Merchants: 3, Products: 6');
        $this->command->info('   Village Potentials: 5');
        $this->command->info('   Export Products: 4');
        $this->command->info('   BUMDes: 4');
        $this->command->info('   KDMP: 2');
        $this->command->info('   Village Services: 4');
        $this->command->info('   Village Contents: 3');
    }
}
