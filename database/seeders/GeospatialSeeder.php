<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GeospatialSeeder extends Seeder
{
    public function run(): void
    {
        // Provinces
        $provinces = [
            ['id' => 11, 'code' => '11', 'name' => 'ACEH'],
            ['id' => 12, 'code' => '12', 'name' => 'SUMATERA UTARA'],
            ['id' => 32, 'code' => '32', 'name' => 'JAWA BARAT'],
            ['id' => 33, 'code' => '33', 'name' => 'JAWA TENGAH'],
            ['id' => 35, 'code' => '35', 'name' => 'JAWA TIMUR'],
            ['id' => 51, 'code' => '51', 'name' => 'BALI'],
        ];

        foreach ($provinces as $province) {
            DB::table('provinces')->insert($province);
        }

        // Regencies for JAWA BARAT (32)
        $regencies = [
            ['id' => 3201, 'code' => '3201', 'province_id' => 32, 'name' => 'KABUPATEN BOGOR'],
            ['id' => 3204, 'code' => '3204', 'province_id' => 32, 'name' => 'KABUPATEN BANDUNG'],
            ['id' => 3273, 'code' => '3273', 'province_id' => 32, 'name' => 'KOTA BANDUNG'],
            
            // JAWA TENGAH (33)
            ['id' => 3301, 'code' => '3301', 'province_id' => 33, 'name' => 'KABUPATEN CILACAP'],
            ['id' => 3371, 'code' => '3371', 'province_id' => 33, 'name' => 'KOTA SEMARANG'],
            
            // JAWA TIMUR (35)
            ['id' => 3501, 'code' => '3501', 'province_id' => 35, 'name' => 'KABUPATEN PACITAN'],
            ['id' => 3578, 'code' => '3578', 'province_id' => 35, 'name' => 'KOTA SURABAYA'],
            
            // BALI (51)
            ['id' => 5101, 'code' => '5101', 'province_id' => 51, 'name' => 'KABUPATEN JEMBRANA'],
            ['id' => 5108, 'code' => '5108', 'province_id' => 51, 'name' => 'KABUPATEN BADUNG'],
        ];

        foreach ($regencies as $regency) {
            DB::table('regencies')->insert($regency);
        }

        // Districts
        $districts = [
            // Kabupaten Bogor
            ['id' => 320101, 'code' => '320101', 'regency_id' => 3201, 'name' => 'Nanggung'],
            ['id' => 320102, 'code' => '320102', 'regency_id' => 3201, 'name' => 'Leuwiliang'],
            ['id' => 320103, 'code' => '320103', 'regency_id' => 3201, 'name' => 'Ciampea'],
            
            // Kabupaten Bandung
            ['id' => 320401, 'code' => '320401', 'regency_id' => 3204, 'name' => 'Ciwidey'],
            ['id' => 320402, 'code' => '320402', 'regency_id' => 3204, 'name' => 'Rancabali'],
            
            // Kota Bandung
            ['id' => 327301, 'code' => '327301', 'regency_id' => 3273, 'name' => 'Coblong'],
            ['id' => 327302, 'code' => '327302', 'regency_id' => 3273, 'name' => 'Cidadap'],
            
            // Kabupaten Badung
            ['id' => 510801, 'code' => '510801', 'regency_id' => 5108, 'name' => 'Kuta'],
            ['id' => 510802, 'code' => '510802', 'regency_id' => 5108, 'name' => 'Mengwi'],
            ['id' => 510803, 'code' => '510803', 'regency_id' => 5108, 'name' => 'Ubud'],
        ];

        foreach ($districts as $district) {
            DB::table('districts')->insert($district);
        }

        // Villages with detailed data
        $villages = [
            // Nanggung, Bogor
            [
                'id' => 32010101,
                'district_id' => 320101,
                'code' => '3201012001',
                'name' => 'Desa Nanggung',
                'cover_image' => 'https://picsum.photos/seed/nanggung/1200/600',
                'description' => 'Desa Nanggung merupakan desa yang terletak di kaki Gunung Halimun. Desa ini memiliki potensi pertanian yang sangat baik dengan komoditas unggulan padi organik dan sayuran hidroponik. Masyarakat desa sangat ramah dan masih menjaga tradisi gotong royong.',
                'population' => 5420,
                'area_size' => 450.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 32010102,
                'district_id' => 320101,
                'code' => '3201012002',
                'name' => 'Desa Malasari',
                'cover_image' => 'https://picsum.photos/seed/malasari/1200/600',
                'description' => 'Desa Malasari adalah gerbang menuju Taman Nasional Gunung Halimun Salak. Desa ini terkenal dengan ekowisata dan pertanian organik. Memiliki pemandangan alam yang indah dengan hutan pinus dan air terjun.',
                'population' => 3850,
                'area_size' => 780.3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Ciampea, Bogor
            [
                'id' => 32010301,
                'district_id' => 320103,
                'code' => '3201032001',
                'name' => 'Desa Ciampea',
                'cover_image' => 'https://picsum.photos/seed/ciampea/1200/600',
                'description' => 'Desa Ciampea adalah pusat produksi kerajinan bambu terbesar di Kabupaten Bogor. Hampir 60% penduduk bermata pencaharian sebagai pengrajin bambu dengan produk seperti furniture, anyaman, dan dekorasi.',
                'population' => 7200,
                'area_size' => 320.8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Ciwidey, Bandung
            [
                'id' => 32040101,
                'district_id' => 320401,
                'code' => '3204012001',
                'name' => 'Desa Alamendah',
                'cover_image' => 'https://picsum.photos/seed/alamendah/1200/600',
                'description' => 'Desa Alamendah terletak di kawasan wisata Ciwidey dengan udara sejuk dan pemandangan perkebunan teh yang menakjubkan. Desa ini dekat dengan Kawah Putih dan Situ Patenggang, menjadikannya destinasi wisata yang ramai.',
                'population' => 4680,
                'area_size' => 890.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Rancabali, Bandung
            [
                'id' => 32040201,
                'district_id' => 320402,
                'code' => '3204022001',
                'name' => 'Desa Alam Endah',
                'cover_image' => 'https://picsum.photos/seed/alamendah2/1200/600',
                'description' => 'Desa Alam Endah dikelilingi oleh perkebunan strawberry dan sayuran dataran tinggi. Wisata petik strawberry menjadi daya tarik utama desa ini. Suhu udara rata-rata 18-22 derajat Celsius.',
                'population' => 3920,
                'area_size' => 560.2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Kuta, Badung
            [
                'id' => 51080101,
                'district_id' => 510801,
                'code' => '5108012001',
                'name' => 'Desa Kuta',
                'cover_image' => 'https://picsum.photos/seed/kuta/1200/600',
                'description' => 'Desa Kuta adalah salah satu destinasi wisata paling terkenal di Bali. Pantai Kuta dengan pasir putihnya yang indah menarik wisatawan dari seluruh dunia. UMKM lokal berkembang pesat di bidang kuliner, kerajinan tangan, dan fashion.',
                'population' => 12500,
                'area_size' => 210.4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Ubud, Badung
            [
                'id' => 51080301,
                'district_id' => 510803,
                'code' => '5108032001',
                'name' => 'Desa Ubud',
                'cover_image' => 'https://picsum.photos/seed/ubud/1200/600',
                'description' => 'Desa Ubud adalah pusat seni dan budaya Bali. Terkenal dengan sawah terasering yang indah, galeri seni, dan musik tradisional. Ubud menjadi destinasi favorit wisatawan yang mencari pengalaman budaya autentik Bali.',
                'population' => 8900,
                'area_size' => 340.7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Mengwi, Badung
            [
                'id' => 51080201,
                'district_id' => 510802,
                'code' => '5108022001',
                'name' => 'Desa Mengwi',
                'cover_image' => 'https://picsum.photos/seed/mengwi/1200/600',
                'description' => 'Desa Mengwi terkenal dengan Pura Taman Ayun yang merupakan salah satu Situs Warisan Dunia UNESCO. Desa ini memiliki potensi wisata budaya dan pertanian sawah yang masih tradisional dengan sistem subak.',
                'population' => 6750,
                'area_size' => 420.3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($villages as $village) {
            DB::table('villages')->insert($village);
        }
    }
}
