<?php

namespace Database\Seeders;

use App\Models\Kdmp;
use Illuminate\Database\Seeder;

class KdmpSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'village_id'       => 1,
                'name'             => 'Koperasi Desa Merah Putih Latiung',
                'code'             => 'KDMP-LATIUNG-001',
                'nomor_badan_hukum'=> '0001/BH/KDMP/2025',
                'description'      => 'Koperasi Desa Merah Putih Latiung merupakan koperasi multipurpose yang melayani kebutuhan simpan pinjam, perdagangan kebutuhan pokok, dan pengembangan usaha pertanian warga desa. Didirikan dalam rangka program nasional Koperasi Desa Merah Putih sesuai Perpres No. 9 Tahun 2025.',
                'status'           => 'aktif',
                'unit_usaha'       => ['simpan_pinjam', 'perdagangan', 'pertanian'],
                'ketua_name'       => 'Bapak Hasan Basri',
                'sekretaris_name'  => 'Ibu Siti Rahayu',
                'bendahara_name'   => 'Bapak Ahmad Fauzi',
                'total_members'    => 245,
                'modal_awal'       => 150000000,
                'total_assets'     => 420000000,
                'established_date' => '2025-03-15',
                'address'          => 'Jl. Merdeka No. 1, Desa Latiung',
                'phone'            => '081234567890',
                'is_active'        => true,
            ],
            [
                'village_id'       => 2,
                'name'             => 'Koperasi Desa Merah Putih Labuhan Bajau',
                'code'             => 'KDMP-LABBAJAU-001',
                'nomor_badan_hukum'=> '0002/BH/KDMP/2025',
                'description'      => 'Koperasi Desa Merah Putih Labuhan Bajau berfokus pada pengembangan sektor perikanan dan perdagangan hasil laut. Dengan lokasi strategis di pesisir, koperasi ini mendukung nelayan lokal untuk meningkatkan kesejahteraan melalui akses modal dan pemasaran.',
                'status'           => 'aktif',
                'unit_usaha'       => ['simpan_pinjam', 'perikanan', 'perdagangan'],
                'ketua_name'       => 'Bapak Ridwan Nainggolan',
                'sekretaris_name'  => 'Bapak Dodi Hartono',
                'bendahara_name'   => 'Ibu Nurhasanah',
                'total_members'    => 189,
                'modal_awal'       => 200000000,
                'total_assets'     => 580000000,
                'established_date' => '2025-04-01',
                'address'          => 'Jl. Bahari No. 7, Labuhan Bajau',
                'phone'            => '081345678901',
                'is_active'        => true,
            ],
            [
                'village_id'       => 3,
                'name'             => 'Koperasi Desa Merah Putih Suak Lamatan',
                'code'             => 'KDMP-SUAKLMT-001',
                'nomor_badan_hukum'=> null,
                'description'      => 'Koperasi Desa Merah Putih Suak Lamatan saat ini dalam tahap persiapan dan pembentukan kepengurusan. Fokus utama pada pengembangan unit usaha pertanian dan peternakan untuk mendukung ketahanan pangan desa.',
                'status'           => 'persiapan',
                'unit_usaha'       => ['pertanian', 'peternakan'],
                'ketua_name'       => 'Bapak Joko Supriyadi',
                'sekretaris_name'  => 'Ibu Dewi Sartika',
                'bendahara_name'   => null,
                'total_members'    => 67,
                'modal_awal'       => 50000000,
                'total_assets'     => 50000000,
                'established_date' => '2025-06-01',
                'address'          => 'Kantor Desa Suak Lamatan',
                'phone'            => '082156789012',
                'is_active'        => true,
            ],
            [
                'village_id'       => 4,
                'name'             => 'Koperasi Desa Merah Putih Ana Ao',
                'code'             => 'KDMP-ANAAO-001',
                'nomor_badan_hukum'=> '0004/BH/KDMP/2025',
                'description'      => 'KopDes Merah Putih Ana Ao aktif dalam 4 unit usaha: simpan pinjam, perdagangan, jasa umum, dan pariwisata desa. Koperasi ini berhasil meningkatkan PADes melalui pengelolaan wisata alam lokal yang dikelola secara kolektif oleh warga.',
                'status'           => 'aktif',
                'unit_usaha'       => ['simpan_pinjam', 'perdagangan', 'jasa', 'pariwisata'],
                'ketua_name'       => 'Bapak Surya Dharma',
                'sekretaris_name'  => 'Ibu Fatimah Zulfa',
                'bendahara_name'   => 'Bapak Wahyu Prasetyo',
                'total_members'    => 312,
                'modal_awal'       => 300000000,
                'total_assets'     => 875000000,
                'established_date' => '2025-02-20',
                'address'          => 'Jl. Suka Maju No. 12, Ana Ao',
                'phone'            => '085678901234',
                'is_active'        => true,
            ],
            [
                'village_id'       => 5,
                'name'             => 'Koperasi Desa Merah Putih Lataling',
                'code'             => 'KDMP-LATALING-001',
                'nomor_badan_hukum'=> '0005/BH/KDMP/2025',
                'description'      => 'KopDes Merah Putih Lataling mengelola unit usaha perdagangan hasil pertanian dan simpan pinjam. Program unggulan koperasi ini adalah kredit usaha mikro tanpa bunga bagi anggota untuk modal usaha kecil dan menengah.',
                'status'           => 'aktif',
                'unit_usaha'       => ['simpan_pinjam', 'perdagangan', 'pertanian'],
                'ketua_name'       => 'Ibu Sri Mulyani',
                'sekretaris_name'  => 'Bapak Eko Santoso',
                'bendahara_name'   => 'Bapak Agus Wibowo',
                'total_members'    => 156,
                'modal_awal'       => 100000000,
                'total_assets'     => 290000000,
                'established_date' => '2025-05-10',
                'address'          => 'Jl. Desa Lataling No. 3',
                'phone'            => '081298765432',
                'is_active'        => true,
            ],
            [
                'village_id'       => 6,
                'name'             => 'Koperasi Desa Merah Putih Pulau Bengkalak',
                'code'             => 'KDMP-BENGKLK-001',
                'nomor_badan_hukum'=> null,
                'description'      => 'Koperasi Desa Merah Putih Pulau Bengkalak dalam proses pembentukan. Rencananya akan fokus pada unit usaha perikanan dan pariwisata bahari mengingat potensi laut yang besar di desa kepulauan ini.',
                'status'           => 'persiapan',
                'unit_usaha'       => ['perikanan', 'pariwisata'],
                'ketua_name'       => 'Bapak Supriadi Nalu',
                'sekretaris_name'  => null,
                'bendahara_name'   => null,
                'total_members'    => 42,
                'modal_awal'       => 25000000,
                'total_assets'     => 25000000,
                'established_date' => null,
                'address'          => 'Kantor Desa Pulau Bengkalak',
                'phone'            => '082198765432',
                'is_active'        => true,
            ],
        ];

        $villageIds = \Illuminate\Support\Facades\DB::table('villages')->pluck('id')->toArray();

        foreach ($data as $index => $row) {
            if (!empty($villageIds)) {
                $row['village_id'] = $villageIds[$index % count($villageIds)];
            }
            Kdmp::updateOrCreate(['code' => $row['code']], $row);
        }
    }
}
