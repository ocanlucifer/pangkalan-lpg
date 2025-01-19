<?php

namespace Database\Seeders;

use App\Models\Item;
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
        Item::create([
            'name'              => 'LPG 3Kg',
            'buy_price'         => 18000,
            'sell_price'        => 25000,
            'stock'             => 0,
            'user_id' => 1,
        ]);
    }
}
