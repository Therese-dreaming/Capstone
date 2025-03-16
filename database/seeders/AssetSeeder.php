<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;

class AssetSeeder extends Seeder
{
    public function run()
    {
        Asset::create([
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
            'lifespan' => 5
        ]);

        Asset::create([
            'name' => 'Laptop',
            'category_id' => 1,  // Changed from 'category' to 'category_id'
            'location' => 'Office 102',
            'status' => 'IN USE',
            'model' => 'Dell Latitude 5420',
            'serial_number' => 'DL789012',
            'specification' => 'Intel i7, 16GB RAM, 512GB SSD',
            'vendor' => 'Dell Philippines',
            'purchase_date' => '2023-02-20',
            'warranty_period' => '2025-02-20',
            'lifespan' => 4
        ]);

        Asset::create([
            'name' => 'Printer',
            'category_id' => 3,  // Changed from 'category' to 'category_id'
            'location' => 'Office 103',
            'status' => 'UNDER REPAIR',
            'model' => 'Epson L3110',
            'serial_number' => 'EP345678',
            'specification' => 'Color Inkjet, All-in-One',
            'vendor' => 'Epson Philippines',
            'purchase_date' => '2022-12-10',
            'warranty_period' => '2023-12-10',
            'lifespan' => 3
        ]);
    }
}
