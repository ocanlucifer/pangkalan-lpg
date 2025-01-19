<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Type;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Type::create([
            'name' => 'Pengecer',
            'discount'  => 35,
            'user_id' => 1,
        ]);
        Type::create([
            'name' => 'Usaha Mikro',
            'discount'  => 0,
            'user_id' => 1,
        ]);
        Type::create([
            'name' => 'Rumah Tangga',
            'discount'  => 0,
            'user_id' => 1,
        ]);

    }
}
