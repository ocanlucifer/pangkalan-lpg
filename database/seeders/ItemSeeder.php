<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Daftar bahan makanan dan minuman realistis
        $foodItems = [
            'Beras', 'Gula Pasir', 'Minyak Goreng', 'Tepung Terigu', 'Telur Ayam',
            'Daging Sapi', 'Daging Ayam', 'Ikan Tenggiri', 'Susu UHT', 'Kedelai',
            'Bawang Merah', 'Bawang Putih', 'Tomat', 'Cabai Merah', 'Kentang',
            'Wortel', 'Kol', 'Garam', 'Kecap Manis', 'Saus Tomat', 'Saos Sambal',
            'Susu Cair', 'Keju Cheddar', 'Roti Tawar', 'Yoghurt', 'Krimer',
            'Coklat Bubuk', 'Vanili', 'Kacang Tanah', 'Kacang Merah', 'Jagung Manis'
        ];

        $drinkItems = [
            'Air Mineral', 'Es Batu', 'Teh Celup', 'Kopi Bubuk', 'Jus Jeruk',
            'Soda', 'Susu Coklat', 'Jus Mangga', 'Jus Apel', 'Teh Hijau',
            'Minuman Energi', 'Minuman Lemon', 'Es Krim', 'Sirup Gula', 'Minuman Kopi'
        ];

        // Gabungkan kedua daftar (Bahan Makanan dan Minuman)
        $allItems = array_merge($foodItems, $drinkItems);
        $existingNames = [];

        $items = [];

        for ($i = 0; $i < 100; $i++) {
            // Pilih bahan dari daftar yang sudah disediakan
            $itemName = $faker->randomElement($allItems);

            // 70% chance of type_id = 2 (minuman), 30% chance of type_id = 1 (makanan)
            $type_id = ($i < 70) ? 2 : 1;

            // Set category_id ke 6 untuk bahan makanan/minuman
            $category_id = 6;

            // Cek apakah sudah ada item dengan nama yang sama dan active = true
            if (!in_array($itemName, $existingNames)) {
                // Tambahkan item baru ke array
                $items[] = [
                    'name' => $itemName,                          // Nama bahan
                    'category_id' => $category_id,                // ID kategori
                    'type_id' => $type_id,                        // ID tipe (minuman atau makanan)
                    'price' => $faker->randomFloat(2, 5000, 10000),    // Harga acak antara 5000 dan 10000
                    'stock' => 0,                                 // Set stok awal ke 0
                    'active' => true,                             // Pastikan status aktif
                    'user_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Simpan nama item yang sudah dimasukkan
                $existingNames[] = $itemName;
            }
        }

        // Insert data ke dalam tabel items jika ada data yang valid
        if (count($items) > 0) {
            DB::table('items')->insert($items);
        }
    }
}
