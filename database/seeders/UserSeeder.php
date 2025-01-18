<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin', // Role default
            'is_active' => true, // Flag aktif
        ]);

        User::create([
            'name' => 'Owner Cafe',
            'username' => 'owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('owner123'),
            'role' => 'owner',
            'is_active' => false, // Flag aktif
        ]);

        User::create([
            'name' => 'Cashier Cafe',
            'username' => 'kasir',
            'email' => 'kasir@example.com',
            'password' => Hash::make('kasir123'),
            'role' => 'cashier',
            'is_active' => false, // Flag aktif
        ]);

        User::create([
            'name' => 'Chef Cafe',
            'username' => 'chef',
            'email' => 'chef@example.com',
            'password' => Hash::make('chef123'),
            'role' => 'user',
            'is_active' => false, // Flag aktif
        ]);

        User::create([
            'name' => 'Baristha Cafe',
            'username' => 'barishta',
            'email' => 'baristha@example.com',
            'password' => Hash::make('baristha123'),
            'role' => 'user',
            'is_active' => false, // Flag aktif
        ]);

        User::create([
            'name' => 'Pelayan Cafe',
            'username' => 'pelayan',
            'email' => 'pelayan@example.com',
            'password' => Hash::make('pelayan123'),
            'role' => 'pelayan',
            'is_active' => false, // Flag aktif
        ]);
    }
}
