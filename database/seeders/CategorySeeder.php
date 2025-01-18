<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Makanan',
            'user_id' => 1,
        ]);
        Category::create([
            'name' => 'Snack',
            'user_id' => 1,
        ]);
        Category::create([
            'name' => 'Minuman',
            'user_id' => 1,
        ]);
        Category::create([
            'name' => 'Paket Makanan',
            'user_id' => 1,
        ]);
        Category::create([
            'name' => 'Paket Snack',
            'user_id' => 1,
        ]);
        Category::create([
            'name' => 'Bahan',
            'user_id' => 1,
        ]);
    }
}
