<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Dummy category IDs and type IDs
        $categories = [
            1 => 'Makanan',
            2 => 'Snack',
            3 => 'Minuman',
            4 => 'Paket Makanan',
            5 => 'Paket Snack'
        ]; // Map ID ke kategori
        $types = [1 => 'Vegetarian', 2 => 'Non-Vegetarian'];

        foreach (range(1, 50) as $index) {
            $category_id = $faker->randomElement(array_keys($categories));

            // Nama menu sesuai kategori
            $name = match ($category_id) {
                1 => $faker->randomElement([
                    'Nasi Goreng Ayam', 'Mie Ayam Spesial', 'Rendang Sapi',
                    'Sate Ayam Madura', 'Sup Ayam Kampung', 'Ayam Geprek Keju',
                    'Ikan Bakar Rica', 'Nasi Liwet Komplit', 'Pecel Lele',
                    'Tongseng Kambing', 'Gudeg Jogja', 'Soto Lamongan',
                ]),
                2 => $faker->randomElement([
                    'Keripik Kentang', 'Brownies Coklat', 'Donat Mini',
                    'Keripik Pisang', 'Cheese Stick', 'Pastel Isi Ayam',
                    'Roti Sobek Coklat', 'Bakwan Jagung', 'Lumpia Semarang',
                    'Pempek Palembang', 'Tahu Bulat', 'Martabak Mini',
                ]),
                3 => $faker->randomElement([
                    'Es Teh Manis', 'Es Jeruk Nipis', 'Kopi Latte',
                    'Milkshake Strawberry', 'Smoothie Alpukat', 'Teh Hijau',
                    'Cappuccino', 'Jus Mangga', 'Jus Buah Naga',
                    'Es Campur', 'Air Kelapa Muda', 'Susu Coklat Hangat',
                ]),
                4 => $faker->randomElement([
                    'Paket Nasi Ayam + Es Teh', 'Paket Burger + Kentang Goreng + Soda',
                    'Paket Sushi + Miso Soup', 'Paket Rendang + Jus Jeruk',
                    'Paket Ramen + Ocha', 'Paket Steak + Es Lemon Tea',
                    'Paket Pecel + Es Cendol', 'Paket Pizza + Milkshake Vanilla',
                ]),
                5 => $faker->randomElement([
                    'Keripik Kentang + Brownies Coklat + Donat Mini',
                    'Keripik Pisang + Cheese Stick + Pastel Isi Ayam',
                    'Roti Sobek Coklat + Bakwan Jagung + Lumpia Semarang',
                    'Pempek Palembang + Tahu Bulat + Martabak Mini',
                ]),
                default => 'Menu Tidak Diketahui',
            };

            // Cek apakah ada item dengan nama yang sama dan active = true
            $existingItem = MenuItem::where('name', $name)
                ->where('active', true)
                ->first();

            // Jika belum ada item dengan nama yang sama dan active = true, insert data baru
            if (!$existingItem) {
                MenuItem::create([
                    'name' => $name,
                    'category_id' => $category_id, // ID kategori sesuai dengan kategori
                    'type_id' => $faker->randomElement(array_keys($types)), // Random type ID
                    'active' => $faker->boolean, // True/False
                    'price' => $faker->randomFloat(15000, 20000, 30000), // Harga acak antara 10.00 - 200.00
                    'user_id' => 1,
                ]);
            }
        }
    }
}
