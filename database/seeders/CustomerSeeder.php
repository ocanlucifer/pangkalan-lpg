<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Membuat instance Faker
        $faker = Faker::create();

        // Menambahkan beberapa data pelanggan secara acak
        for ($i = 0; $i < 10; $i++) { // Menambahkan 10 pelanggan
            DB::table('customers')->insert([
                'nik' => $faker->unique()->numerify('##############'), // Membuat NIK unik
                'name' => $faker->name,
                'address' => $faker->address,
                'contact' => $faker->phoneNumber,
                'active' => $faker->boolean, // Nilai acak true atau false
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
