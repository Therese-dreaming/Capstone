<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            'Dell Technologies',
            'HP Inc.',
            'Lenovo Group Limited',
            'ASUS',
            'Acer Inc.',
            'Samsung Electronics',
            'Apple Inc.',
            'Microsoft Corporation',
            'Canon Inc.',
            'Epson'
        ];

        foreach ($vendors as $vendorName) {
            Vendor::create(['name' => $vendorName]);
        }
    }
}
