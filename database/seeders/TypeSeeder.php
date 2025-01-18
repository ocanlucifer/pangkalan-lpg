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
            'name' => 'Vegetarian',
            'user_id' => 1,
        ]);
        Type::create([
            'name' => 'Non-Vegetarian',
            'user_id' => 1,
        ]);

    }
}
