<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VillageContent;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminActivationSeeder::class,
        ]);
        // 1. Buat Roles
        $adminRole = Role::firstOrCreate(['name' => 'superadmin']);
        Role::firstOrCreate(['name' => 'village_admin']);
        $merchantRole = Role::firstOrCreate(['name' => 'umkm']);
        Role::firstOrCreate(['name' => 'buyer']);

        // 2. Generate Permissions & Assign ke Superadmin (Filament Shield)
        $this->call([
            ShieldPermissionSeeder::class,
        ]);

        // 3. Buat Akun Superadmin
        $admin = User::firstOrCreate(
            ['email' => 'admin@desa.com'],
            [
                'name' => 'Super Admin Sentra Desa',
                'password' => bcrypt('P1csi8080#'),
            ]
        );

        $admin->update([
            'user_level' => 'superadmin',
            'is_active' => true,
        ]);

        if (!$admin->hasRole('superadmin')) {
            $admin->assignRole($adminRole);
        }

        // 3. Buat User Contoh (Calon Merchant)
        $sampleUsers = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@gmail.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti@gmail.com',
                'password' => bcrypt('password'),
            ],
        ];

        foreach ($sampleUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            // Berikan role umkm agar bisa didaftarkan di tabel Merchants
            if (!$user->hasRole('umkm')) {
                $user->assignRole($merchantRole);
            }
        }

        // 4. Buat Data Kategori Desa (Agar Dashboard Tidak Kosong)
        $categories = [
            'profil', 'potensi', 'wisata', 'bumdes', 
            'ekspor', 'informasi', 'kdmp', 'berkarya', 'belajar'
        ];

        foreach ($categories as $cat) {
            VillageContent::firstOrCreate(
                ['slug' => Str::slug("Informasi $cat Desa")],
                [
                    'title' => "Informasi " . ucfirst($cat) . " Desa",
                    'category' => $cat,
                    'content' => "Ini adalah konten awal untuk kategori $cat. Silakan edit melalui dashboard.",
                ]
            );
        }

        // 5. Seed Geospatial & Main Data
            $this->call([
                CategorySeeder::class,
                GeospatialSeeder::class,
                VillageProfileSeeder::class,
                ProductSeeder::class,
                TourismSeeder::class,
                BumdesSeeder::class,
                KdmpSeeder::class,
                ExportProductSeeder::class,
                VillageServiceSeeder::class,
                VillagePotentialSeeder::class,
            ]);
    }
}