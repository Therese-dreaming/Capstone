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
            CategorySeeder::class,  // Make sure CategorySeeder runs before AssetSeeder
            AssetSeeder::class,
            MaintenanceTaskSeeder::class,
        ]);
    }
}
