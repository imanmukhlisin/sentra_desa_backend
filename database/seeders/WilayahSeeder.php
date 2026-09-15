<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        // Seed dummy user for merchant
        DB::table('users')->insert([
            [
                'id' => 101,
                'name' => 'Budi (UD Berkah Tani)',
                'email' => 'berkah.tani@gmail.com',
                'password' => bcrypt('dummy123'),
            ],
        ]);
        // Seed dummy village for merchant
        DB::table('villages')->insert([
            [
                'district_id' => 320101,
                'code' => '32010101',
                'name' => 'Desa Dummy Merchant',
            ],
        ]);
        // Seed provinces first
        DB::table('provinces')->insert([
            ['id' => 32, 'code' => '32', 'name' => 'JAWA BARAT'],
        ]);
        // Sample batch insert for kabupaten, kecamatan, kelurahan
        DB::table('regencies')->insert([
            ['id' => 3201, 'province_id' => 32, 'code' => '3201', 'name' => 'KABUPATEN BOGOR', 'type' => 'kabupaten'],
            ['id' => 3273, 'province_id' => 32, 'code' => '3273', 'name' => 'KOTA BANDUNG', 'type' => 'kota'],
        ]);
        DB::table('districts')->insert([
            ['id' => 320101, 'regency_id' => 3201, 'code' => '320101', 'name' => 'NANGGUNG'],
            ['id' => 320102, 'regency_id' => 3201, 'code' => '320102', 'name' => 'LEUWILIANG'],
            ['id' => 320103, 'regency_id' => 3201, 'code' => '320103', 'name' => 'CIAMPEA'],
            ['id' => 320401, 'regency_id' => 3201, 'code' => '320401', 'name' => 'CIWIDEY'],
            ['id' => 320402, 'regency_id' => 3201, 'code' => '320402', 'name' => 'RANCABALI'],
            ['id' => 327301, 'regency_id' => 3273, 'code' => '327301', 'name' => 'COBLONG'],
            ['id' => 327302, 'regency_id' => 3273, 'code' => '327302', 'name' => 'CIDADAP'],
            ['id' => 510801, 'regency_id' => 3201, 'code' => '510801', 'name' => 'KUTA'],
            ['id' => 510802, 'regency_id' => 3201, 'code' => '510802', 'name' => 'MENGWI'],
            ['id' => 510803, 'regency_id' => 3201, 'code' => '510803', 'name' => 'UBUD'],
        ]);
        DB::table('villages')->insert([
            ['district_id' => 320101, 'code' => '3201012003', 'name' => 'Desa Hambaro'],
            ['district_id' => 327301, 'code' => '3273011001', 'name' => 'Kelurahan Dago'],
        ]);
    }
}
