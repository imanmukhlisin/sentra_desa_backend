<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BumdesSeeder extends Seeder
{
    public function run(): void
    {
        $bumdes = [
            [
                'village_id' => 32010101,
                'name' => 'BUMDes Nanggung Jaya',
                'slug' => 'bumdes-nanggung-jaya',
                'description' => 'BUMDes yang fokus pada pengembangan pertanian organik dan pengolahan hasil pertanian.',
                'director_name' => 'Bapak Suparman',
                'established_date' => '2019-03-15',
                'initial_capital' => 500000000,
                'phone' => '081234550001',
                'address' => 'Kantor Desa Nanggung',
                'logo' => 'https://picsum.photos/seed/bumdes1/800/600',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'village_id' => 32010102,
                'name' => 'BUMDes Malasari Mandiri',
                'slug' => 'bumdes-malasari-mandiri',
                'description' => 'BUMDes yang mengelola ekowisata dan homestay di kawasan Gunung Halimun.',
                'director_name' => 'Bapak Agus Setiawan',
                'established_date' => '2020-06-22',
                'initial_capital' => 350000000,
                'phone' => '081234550002',
                'address' => 'Kampung Malasari RT 01/01',
                'logo' => 'https://picsum.photos/seed/bumdes2/800/600',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'village_id' => 32010301,
                'name' => 'BUMDes Ciampea Kreatif',
                'slug' => 'bumdes-ciampea-kreatif',
                'description' => 'BUMDes yang mengembangkan industri kreatif kerajinan bambu.',
                'director_name' => 'Ibu Siti Nurjanah',
                'established_date' => '2018-08-10',
                'initial_capital' => 750000000,
                'phone' => '081234550003',
                'address' => 'Jl. Raya Ciampea KM 5',
                'logo' => 'https://picsum.photos/seed/bumdes3/800/600',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'village_id' => 32040201,
                'name' => 'BUMDes Rancabali Agro',
                'slug' => 'bumdes-rancabali-agro',
                'description' => 'BUMDes yang fokus pada pengembangan agrowisata strawberry.',
                'director_name' => 'Ibu Dewi Lestari',
                'established_date' => '2019-04-20',
                'initial_capital' => 850000000,
                'phone' => '081234550005',
                'address' => 'Jl. Rancabali KM 10',
                'logo' => 'https://picsum.photos/seed/bumdes5/800/600',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'village_id' => 51080101,
                'name' => 'BUMDes Kuta Maju',
                'slug' => 'bumdes-kuta-maju',
                'description' => 'BUMDes yang mengelola pasar seni dan pusat kuliner tradisional.',
                'director_name' => 'Bapak I Made Wirawan',
                'established_date' => '2016-01-15',
                'initial_capital' => 2500000000,
                'phone' => '081234550006',
                'address' => 'Jl. Pantai Kuta No. 123',
                'logo' => 'https://picsum.photos/seed/bumdes6/800/600',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'village_id' => 51080301,
                'name' => 'BUMDes Ubud Lestari',
                'slug' => 'bumdes-ubud-lestari',
                'description' => 'BUMDes yang mengembangkan wisata budaya dan seni.',
                'director_name' => 'Bapak Nyoman Sudarsana',
                'established_date' => '2015-09-08',
                'initial_capital' => 1800000000,
                'phone' => '081234550007',
                'address' => 'Jl. Raya Ubud No. 45',
                'logo' => 'https://picsum.photos/seed/bumdes7/800/600',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($bumdes as $item) {
            DB::table('bumdes')->insert($item);
        }
    }
}
