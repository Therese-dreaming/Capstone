<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            GroupSeeder::class,
            CreateDefaultUserSeeder::class,
            UserSeeder::class,     // Add more users before other seeders
            CategorySeeder::class,  // Make sure CategorySeeder runs before AssetSeeder
            VendorSeeder::class,   // Ensure vendors are seeded before assets
            MaintenanceTaskSeeder::class,
            LaboratorySeeder::class,
            LocationSeeder::class,  // Locations are needed for assets
            AssetSeeder::class,    // Assets must be created before repair requests
            RepairRequestSeeder::class,  // Repair requests depend on assets
            MaintenanceSeeder::class,    // Maintenance tasks can be created independently
        ]);
    }
}
