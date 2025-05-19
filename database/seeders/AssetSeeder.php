<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetSeeder extends Seeder
{
    public function run()
    {
        DB::table('assets')->insert([
            'name' => 'Desktop Computer',
            'category_id' => 1,
            'location' => 'Office 101',
            'status' => 'IN USE',
            'model' => 'HP ProDesk 400 G7',
            'serial_number' => 'HP123456',
            'specification' => 'Intel i5, 8GB RAM, 256GB SSD',
            'vendor' => 'HP Philippines',
            'purchase_date' => '2023-01-15',
            'warranty_period' => '2026-01-15',
            'calculated_lifespan' => 5,  // Changed from 'lifespan' to 'calculated_lifespan'
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
