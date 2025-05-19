<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateDefaultUserSeeder extends Seeder
{
    public function run()
    {
        // Check if admin user already exists
        if (!DB::table('users')->where('username', 'admin')->exists()) {
            DB::table('users')->insert([
                'name' => 'Admin User',
                'username' => 'admin',
                'password' => Hash::make('password'),  // You might want to change this password
                'group_id' => 1,
                'status' => 'Active',
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
