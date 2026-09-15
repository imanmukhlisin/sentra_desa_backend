<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TourismSeeder extends Seeder
{
    public function run(): void
    {
        $tourisms = [
            [
                'village_id' => 32010102,
                'name' => 'Ekowisata Gunung Halimun',
                'slug' => 'ekowisata-gunung-halimun',
                'description' => 'Wisata alam dengan trekking menuju air terjun tersembunyi dan hutan pinus. Udara sejuk dan pemandangan yang menakjubkan.',
                'category' => 'alam',
                'address' => 'Kampung Malasari RT 03/02',
                'phone' => '081234560001',
                'opening_hours' => '07:00-17:00',
                'entrance_fee' => 25000,
                'facilities' => 'Parkir, Toilet, Mushola, Warung, Spot Foto',
                'cover_image' => 'https://picsum.photos/seed/tourism1/1200/800',
                'latitude' => -6.7321,
                'longitude' => 106.5234,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'village_id' => 32040101,
                'name' => 'Kawah Putih Ciwidey',
                'slug' => 'kawah-putih-ciwidey',
                'description' => 'Danau kawah vulkanik dengan air berwarna putih kehijauan yang eksotis.',
                'category' => 'alam',
                'address' => 'Jl. Raya Soreang - Ciwidey',
                'phone' => '081234560003',
                'opening_hours' => '07:00-17:00',
                'entrance_fee' => 50000,
                'facilities' => 'Park ir Luas, Toilet, Mushola, Food Court, Souvenir Shop',
                'cover_image' => 'https://picsum.photos/seed/tourism3/1200/800',
                'latitude' => -7.1661,
                'longitude' => 107.4024,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'village_id' => 51080101,
                'name' => 'Pantai Kuta',
                'slug' => 'pantai-kuta',
                'description' => 'Pantai legendaris dengan pasir putih dan sunset yang memukau.',
                'category' => 'alam',
                'address' => 'Jl. Pantai Kuta',
                'phone' => '081234560005',
                'opening_hours' => '24 Jam',
                'entrance_fee' => 0,
                'facilities' => 'Parkir, Toilet, Shower, Penyewaan Surfboard, Restoran',
                'cover_image' => 'https://picsum.photos/seed/tourism5/1200/800',
                'latitude' => -8.7184,
                'longitude' => 115.1686,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'village_id' => 51080301,
                'name' => 'Tegalalang Rice Terrace',
                'slug' => 'tegalalang-rice-terrace',
                'description' => 'Sawah terasering terindah di Bali dengan sistem irigasi subak tradisional.',
                'category' => 'alam',
                'address' => 'Jl. Raya Tegalalang',
                'phone' => '081234560007',
                'opening_hours' => '08:00-18:00',
                'entrance_fee' => 20000,
                'facilities' => 'Parkir, Toilet, Café, Swing, Spot Foto',
                'cover_image' => 'https://picsum.photos/seed/tourism7/1200/800',
                'latitude' => -8.4353,
                'longitude' => 115.2823,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'village_id' => 51080301,
                'name' => 'Monkey Forest Ubud',
                'slug' => 'monkey-forest-ubud',
                'description' => 'Hutan lindung dengan populasi lebih dari 700 ekor monyet ekor panjang Bali.',
                'category' => 'alam',
                'address' => 'Jl. Monkey Forest Ubud',
                'phone' => '081234560008',
                'opening_hours' => '08:30-18:00',
                'entrance_fee' => 80000,
                'facilities' => 'Parkir, Toilet, Information Center',
                'cover_image' => 'https://picsum.photos/seed/tourism8/1200/800',
                'latitude' => -8.5193,
                'longitude' => 115.2587,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'village_id' => 51080201,
                'name' => 'Pura Taman Ayun',
                'slug' => 'pura-taman-ayun',
                'description' => 'Pura kerajaan Mengwi yang merupakan Situs Warisan Dunia UNESCO.',
                'category' => 'budaya',
                'address' => 'Jl. Ayodya No.10 Mengwi',
                'phone' => '081234560010',
                'opening_hours' => '08:00-18:00',
                'entrance_fee' => 30000,
                'facilities' => 'Parkir, Toilet, Guide, Museum, Taman',
                'cover_image' => 'https://picsum.photos/seed/tourism10/1200/800',
                'latitude' => -8.5506,
                'longitude' => 115.1719,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($tourisms as $tourism) {
            DB::table('tourisms')->insert($tourism);
        }
    }
}
