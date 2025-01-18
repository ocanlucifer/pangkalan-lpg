<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Create 10 vendor records
        for ($i = 0; $i < 23; $i++) {
            DB::table('vendors')->insert([
                'name' => $faker->company,               // Generate a fake company name
                'address' => $faker->address,            // Generate a fake address
                'contact' => $faker->phoneNumber,        // Generate a fake phone number
                'active' => $faker->boolean,             // Randomly assign active status (true or false)
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
