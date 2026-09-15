<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Makanan & Minuman',
                'slug' => 'makanan-minuman',
                'icon' => 'heroicon-o-shopping-bag',
                'color' => '#f59e0b',
            ],
            [
                'name' => 'Kerajinan & Souvenir',
                'slug' => 'kerajinan-souvenir',
                'icon' => 'heroicon-o-gift',
                'color' => '#8b5cf6',
            ],
            [
                'name' => 'Pertanian & Hasil Bumi',
                'slug' => 'pertanian-hasil-bumi',
                'icon' => 'heroicon-o-sun',
                'color' => '#10b981',
            ],
            [
                'name' => 'Jasa & Pelayanan',
                'slug' => 'jasa-pelayanan',
                'icon' => 'heroicon-o-wrench-screwdriver',
                'color' => '#3b82f6',
            ],
            [
                'name' => 'Fashion & Pakaian',
                'slug' => 'fashion-pakaian',
                'icon' => 'heroicon-o-sparkles',
                'color' => '#ec4899',
            ],
            [
                'name' => 'Pariwisata & Seni Budaya',
                'slug' => 'pariwisata-seni-budaya',
                'icon' => 'heroicon-o-map',
                'color' => '#06b6d4',
            ],
            [
                'name' => 'Perikanan & Peternakan',
                'slug' => 'perikanan-peternakan',
                'icon' => 'heroicon-o-lifebuoy',
                'color' => '#6366f1',
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
    }
}
