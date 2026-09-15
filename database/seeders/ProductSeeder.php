<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // First, create users for merchants
        $merchantUsers = [
            [
                'id' => 101,
                'name' => 'Budi (UD Berkah Tani)',
                'email' => 'berkah.tani@gmail.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 102,
                'name' => 'Ahmad (Kerajinan Bambu)',
                'email' => 'bambu.sejahtera@gmail.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 103,
                'name' => 'Dewi (Strawberry Fresh)',
                'email' => 'strawberry.ciwidey@gmail.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 104,
                'name' => 'Made (Kuta Handicraft)',
                'email' => 'kuta.handicraft@gmail.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 105,
                'name' => 'Nyoman (Ubud Art)',
                'email' => 'ubud.art@gmail.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($merchantUsers as $user) {
            DB::table('users')->insert($user);
        }

        // Then create merchants
        $merchants = [
            [
                'id' => 1,
                'user_id' => 101,
                'village_id' => 32010101, // Desa Nanggung
                'store_name' => 'UD Berkah Tani',
                'description' => 'Produsen beras organik premium',
                'address' => 'Jl. Raya Nanggung No. 45',
                'phone' => '081234567890',
                'payment_proof' => 'dummy.jpg',
                'status' => 'approved',
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'user_id' => 102,
                'village_id' => 32010301, // Desa Ciampea
                'store_name' => 'Kerajinan Bambu Sejahtera',
                'description' => 'Produsen furniture dan kerajinan bambu',
                'address' => 'Kampung Babakan RT 02/05',
                'phone' => '081234567891',
                'payment_proof' => 'dummy.jpg',
                'status' => 'approved',
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'user_id' => 103,
                'village_id' => 32040201, // Desa Alam Endah
                'store_name' => 'Strawberry Fresh Ciwidey',
                'description' => 'Petani strawberry dan produsen olahan strawberry',
                'address' => 'Jl. Rancabali KM 12',
                'phone' => '081234567892',
                'payment_proof' => 'dummy.jpg',
                'status' => 'approved',
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'user_id' => 104,
                'village_id' => 51080101, // Desa Kuta
                'store_name' => 'Kuta Handicraft',
                'description' => 'Toko kerajinan tangan khas Bali',
                'address' => 'Jl. Pantai Kuta No. 88',
                'phone' => '081234567893',
                'payment_proof' => 'dummy.jpg',
                'status' => 'approved',
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'user_id' => 105,
                'village_id' => 51080301, // Desa Ubud
                'store_name' => 'Ubud Art Gallery',
                'description' => 'Galeri seni dan kerajinan tradisional Bali',
                'address' => 'Jl. Raya Ubud No. 12',
                'phone' => '081234567894',
                'payment_proof' => 'dummy.jpg',
                'status' => 'approved',
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($merchants as $merchant) {
            DB::table('merchants')->insert($merchant);
        }

        // Products
        $products = [
            // Beras Organik
            [
                'merchant_id' => 1,
                'village_id' => 32010101,
                'name' => 'Beras Organik Premium Nanggung',
                'description' => 'Beras organik berkualitas tinggi dari Desa Nanggung. Ditanam tanpa pestisida kimia dengan sistem irigasi alami dari mata air gunung. Tekstur pulen dan aroma wangi khas beras premium.',
                'category' => 'makanan_minuman',
                'price' => 45000,
                'stock' => 500,
                'unit' => 'kg',
                'image' => 'https://picsum.photos/seed/beras1/800/800',
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'merchant_id' => 1,
                'village_id' => 32010101,
                'name' => 'Beras Merah Organik',
                'description' => 'Beras merah organik kaya akan serat dan nutrisi. Cocok untuk diet sehat dan penderita diabetes.',
                'category' => 'makanan_minuman',
                'price' => 55000,
                'stock' => 300,
                'unit' => 'kg',
                'image' => 'https://picsum.photos/seed/beras2/800/800',
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Kerajinan Bambu
            [
                'merchant_id' => 2,
                'village_id' => 32010301,
                'name' => 'Kursi Santai Bambu',
                'description' => 'Kursi santai dari bambu pilihan dengan finishing natural. Kuat, nyaman, dan ramah lingkungan. Cocok untuk teras atau ruang tamu bergaya natural.',
                'category' => 'kerajinan',
                'price' => 850000,
                'stock' => 25,
                'unit' => 'unit',
                'image' => 'https://picsum.photos/seed/bambu1/800/800',
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'merchant_id' => 2,
                'village_id' => 32010301,
                'name' => 'Anyaman Bambu Dekorasi',
                'description' => 'Anyaman bambu dengan motif tradisional untuk dekorasi dinding. Dapat custom ukuran dan motif.',
                'category' => 'kerajinan',
                'price' => 250000,
                'stock' => 50,
                'unit' => 'unit',
                'image' => 'https://picsum.photos/seed/bambu2/800/800',
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'merchant_id' => 2,
                'village_id' => 32010301,
                'name' => 'Meja Makan Bambu Set',
                'description' => 'Set meja makan dari bambu untuk 6 orang. Termasuk 6 kursi dengan desain minimalis modern.',
                'category' => 'kerajinan',
                'price' => 4500000,
                'stock' => 10,
                'unit' => 'set',
                'image' => 'https://picsum.photos/seed/bambu3/800/800',
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Strawberry Products
            [
                'merchant_id' => 3,
                'village_id' => 32040201,
                'name' => 'Strawberry Segar Grade A',
                'description' => 'Strawberry segar pilihan langsung dari kebun. Manis, segar, dan berkualitas tinggi. Dipetik pagi hari untuk kesegaran maksimal.',
                'category' => 'makanan_minuman',
                'price' => 60000,
                'stock' => 200,
                'unit' => 'kg',
                'image' => 'https://picsum.photos/seed/strawberry1/800/800',
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'merchant_id' => 3,
                'village_id' => 32040201,
                'name' => 'Selai Strawberry Homemade',
                'description' => 'Selai strawberry buatan rumahan tanpa pengawet. Terbuat dari 100% buah strawberry segar pilihan.',
                'category' => 'makanan_minuman',
                'price' => 45000,
                'stock' => 150,
                'unit' => 'botol',
                'image' => 'https://picsum.photos/seed/strawberry2/800/800',
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'merchant_id' => 3,
                'village_id' => 32040201,
                'name' => 'Paket Wisata Petik Strawberry',
                'description' => 'Paket wisata petik strawberry untuk keluarga. Termasuk 1kg strawberry, welcome drink, dan foto di kebun.',
                'category' => 'jasa',
                'price' => 100000,
                'stock' => 999,
                'unit' => 'paket',
                'image' => 'https://picsum.photos/seed/strawberry3/800/800',
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Bali Handicraft
            [
                'merchant_id' => 4,
                'village_id' => 51080101,
                'name' => 'Ukiran Kayu Garuda',
                'description' => 'Ukiran kayu berbentuk Garuda dengan detail halus. Terbuat dari kayu jati pilihan oleh pengrajin berpengalaman.',
                'category' => 'kerajinan',
                'price' => 1250000,
                'stock' => 15,
                'unit' => 'unit',
                'image' => 'https://picsum.photos/seed/bali1/800/800',
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'merchant_id' => 4,
                'village_id' => 51080101,
                'name' => 'Batik Bali Modern',
                'description' => 'Kain batik dengan motif khas Bali dalam desain modern. Cocok untuk fashion atau dekorasi interior.',
                'category' => 'fashion',
                'price' => 350000,
                'stock' => 80,
                'unit' => 'potong',
                'image' => 'https://picsum.photos/seed/bali2/800/800',
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'merchant_id' => 4,
                'village_id' => 51080101,
                'name' => 'Tas Anyaman Pandan Bali',
                'description' => 'Tas anyaman dari daun pandan dengan sentuhan modern. Ringan, kuat, dan fashionable.',
                'category' => 'fashion',
                'price' => 185000,
                'stock' => 100,
                'unit' => 'unit',
                'image' => 'https://picsum.photos/seed/bali3/800/800',
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Ubud Art
            [
                'merchant_id' => 5,
                'village_id' => 51080301,
                'name' => 'Lukisan Sawah Terasering',
                'description' => 'Lukisan pemandangan sawah terasering khas Ubud dengan teknik akrilik di kanvas. Ukuran 60x80 cm.',
                'category' => 'kerajinan',
                'price' => 2500000,
                'stock' => 8,
                'unit' => 'unit',
                'image' => 'https://picsum.photos/seed/ubud1/800/800',
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'merchant_id' => 5,
                'village_id' => 51080301,
                'name' => 'Patung Kayu Buddha',
                'description' => 'Patung Buddha dari kayu suar dengan ukiran detail. Tinggi 40cm, cocok untuk dekorasi rumah atau villa.',
                'category' => 'kerajinan',
                'price' => 950000,
                'stock' => 20,
                'unit' => 'unit',
                'image' => 'https://picsum.photos/seed/ubud2/800/800',
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'merchant_id' => 5,
                'village_id' => 51080301,
                'name' => 'Dreamcatcher Bali Style',
                'description' => 'Dreamcatcher dengan sentuhan Bali menggunakan manik-manik kayu dan bulu alami. Diameter 30cm.',
                'category' => 'kerajinan',
                'price' => 125000,
                'stock' => 50,
                'unit' => 'unit',
                'image' => 'https://picsum.photos/seed/ubud3/800/800',
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert($product);
        }
    }
}
