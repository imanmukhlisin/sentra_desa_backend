<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder ini mengisi data desa/kelurahan dari data master wilayah Indonesia.
 * Hanya nama dan relasi ke kecamatan (district_id) yang diisi.
 * Data profil lainnya dikosongkan, nanti diupdate oleh user role desa.
 */
class VillageProfileSeeder extends Seeder
{
    public function run(): void
    {
        // Data master kelurahan/desa berdasarkan kecamatan yang sudah ada di database
        $villages = [
            // ── Kecamatan Nanggung (320101) - Kabupaten Bogor ──
            ['district_id' => 320101, 'code' => '3201012003', 'name' => 'Desa Hambaro'],
            ['district_id' => 320101, 'code' => '3201012004', 'name' => 'Desa Pangkal Jaya'],
            ['district_id' => 320101, 'code' => '3201012005', 'name' => 'Desa Cisarua'],
            ['district_id' => 320101, 'code' => '3201012006', 'name' => 'Desa Curug Bitung'],
            ['district_id' => 320101, 'code' => '3201012007', 'name' => 'Desa Bantar Karet'],
            ['district_id' => 320101, 'code' => '3201012008', 'name' => 'Desa Parakanmuncang'],
            ['district_id' => 320101, 'code' => '3201012009', 'name' => 'Desa Sukaluyu'],

            // ── Kecamatan Leuwiliang (320102) - Kabupaten Bogor ──
            ['district_id' => 320102, 'code' => '3201022001', 'name' => 'Desa Leuwiliang'],
            ['district_id' => 320102, 'code' => '3201022002', 'name' => 'Desa Leuwimekar'],
            ['district_id' => 320102, 'code' => '3201022003', 'name' => 'Desa Cibeber I'],
            ['district_id' => 320102, 'code' => '3201022004', 'name' => 'Desa Cibeber II'],
            ['district_id' => 320102, 'code' => '3201022005', 'name' => 'Desa Barengkok'],
            ['district_id' => 320102, 'code' => '3201022006', 'name' => 'Desa Karacak'],
            ['district_id' => 320102, 'code' => '3201022007', 'name' => 'Desa Purasari'],
            ['district_id' => 320102, 'code' => '3201022008', 'name' => 'Desa Puraseda'],

            // ── Kecamatan Ciampea (320103) - Kabupaten Bogor ──
            ['district_id' => 320103, 'code' => '3201032002', 'name' => 'Desa Cibadak'],
            ['district_id' => 320103, 'code' => '3201032003', 'name' => 'Desa Cibanteng'],
            ['district_id' => 320103, 'code' => '3201032004', 'name' => 'Desa Tegalwaru'],
            ['district_id' => 320103, 'code' => '3201032005', 'name' => 'Desa Cinangka'],
            ['district_id' => 320103, 'code' => '3201032006', 'name' => 'Desa Benteng'],
            ['district_id' => 320103, 'code' => '3201032007', 'name' => 'Desa Bojong Rangkas'],

            // ── Kecamatan Ciwidey (320401) - Kabupaten Bandung ──
            ['district_id' => 320401, 'code' => '3204012002', 'name' => 'Desa Ciwidey'],
            ['district_id' => 320401, 'code' => '3204012003', 'name' => 'Desa Lebakmuncang'],
            ['district_id' => 320401, 'code' => '3204012004', 'name' => 'Desa Panundaan'],
            ['district_id' => 320401, 'code' => '3204012005', 'name' => 'Desa Nengkelan'],
            ['district_id' => 320401, 'code' => '3204012006', 'name' => 'Desa Rawabogo'],
            ['district_id' => 320401, 'code' => '3204012007', 'name' => 'Desa Sukawening'],

            // ── Kecamatan Rancabali (320402) - Kabupaten Bandung ──
            ['district_id' => 320402, 'code' => '3204022002', 'name' => 'Desa Cipelah'],
            ['district_id' => 320402, 'code' => '3204022003', 'name' => 'Desa Indragiri'],
            ['district_id' => 320402, 'code' => '3204022004', 'name' => 'Desa Patengan'],
            ['district_id' => 320402, 'code' => '3204022005', 'name' => 'Desa Sugihmukti'],

            // ── Kecamatan Coblong (327301) - Kota Bandung ──
            ['district_id' => 327301, 'code' => '3273011001', 'name' => 'Kelurahan Dago'],
            ['district_id' => 327301, 'code' => '3273011002', 'name' => 'Kelurahan Lebak Siliwangi'],
            ['district_id' => 327301, 'code' => '3273011003', 'name' => 'Kelurahan Lebak Gede'],
            ['district_id' => 327301, 'code' => '3273011004', 'name' => 'Kelurahan Sadang Serang'],
            ['district_id' => 327301, 'code' => '3273011005', 'name' => 'Kelurahan Cipaganti'],
            ['district_id' => 327301, 'code' => '3273011006', 'name' => 'Kelurahan Sekeloa'],

            // ── Kecamatan Cidadap (327302) - Kota Bandung ──
            ['district_id' => 327302, 'code' => '3273021001', 'name' => 'Kelurahan Ciumbuleuit'],
            ['district_id' => 327302, 'code' => '3273021002', 'name' => 'Kelurahan Hegarmanah'],
            ['district_id' => 327302, 'code' => '3273021003', 'name' => 'Kelurahan Ledeng'],

            // ── Kecamatan Kuta (510801) - Kabupaten Badung ──
            ['district_id' => 510801, 'code' => '5108012002', 'name' => 'Desa Kedonganan'],
            ['district_id' => 510801, 'code' => '5108012003', 'name' => 'Desa Tuban'],
            ['district_id' => 510801, 'code' => '5108012004', 'name' => 'Desa Legian'],
            ['district_id' => 510801, 'code' => '5108012005', 'name' => 'Desa Seminyak'],

            // ── Kecamatan Mengwi (510802) - Kabupaten Badung ──
            ['district_id' => 510802, 'code' => '5108022002', 'name' => 'Desa Kapal'],
            ['district_id' => 510802, 'code' => '5108022003', 'name' => 'Desa Lukluk'],
            ['district_id' => 510802, 'code' => '5108022004', 'name' => 'Desa Sempidi'],
            ['district_id' => 510802, 'code' => '5108022005', 'name' => 'Desa Sading'],
            ['district_id' => 510802, 'code' => '5108022006', 'name' => 'Desa Abianbase'],
            ['district_id' => 510802, 'code' => '5108022007', 'name' => 'Desa Baha'],

            // ── Kecamatan Ubud (510803) - Kabupaten Badung ──
            ['district_id' => 510803, 'code' => '5108032002', 'name' => 'Desa Peliatan'],
            ['district_id' => 510803, 'code' => '5108032003', 'name' => 'Desa Mas'],
            ['district_id' => 510803, 'code' => '5108032004', 'name' => 'Desa Lodtunduh'],
            ['district_id' => 510803, 'code' => '5108032005', 'name' => 'Desa Singakerta'],
            ['district_id' => 510803, 'code' => '5108032006', 'name' => 'Desa Kedewatan'],
            ['district_id' => 510803, 'code' => '5108032007', 'name' => 'Desa Sayan'],
        ];

        $now = now();

        foreach ($villages as $village) {
            // Skip jika village dengan code ini sudah ada
            $exists = DB::table('villages')->where('code', $village['code'])->exists();
            if ($exists) {
                continue;
            }

            DB::table('villages')->insert([
                'district_id' => $village['district_id'],
                'code' => $village['code'],
                'name' => $village['name'],
                // Profil dikosongkan - akan diupdate oleh user role desa
                'postal_code' => null,
                'description' => null,
                'vision' => null,
                'mission' => null,
                'history' => null,
                'head_name' => null,
                'logo' => null,
                'cover_image' => null,
                'website' => null,
                'phone' => null,
                'email' => null,
                'latitude' => null,
                'longitude' => null,
                'population' => 0,
                'area_size' => null,
                'dusun_count' => 0,
                'is_verified' => false,
                'is_featured' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
