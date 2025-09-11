<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ComprehensiveSeeder extends Seeder
{
    /**
     * Run a comprehensive seeder that creates all related data
     * This seeder can be run independently for testing purposes
     */
    public function run()
    {
        $this->command->info('Starting comprehensive seeding process...');
        
        // Run all seeders in the correct order
        $this->call([
            GroupSeeder::class,
            CreateDefaultUserSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            VendorSeeder::class,
            MaintenanceTaskSeeder::class,
            LaboratorySeeder::class,
            LocationSeeder::class,
        ]);
        
        $this->command->info('Basic data seeded successfully.');
        
        // Seed assets
        $this->command->info('Seeding assets...');
        $this->call(AssetSeeder::class);
        
        // Seed repair requests
        $this->command->info('Seeding repair requests...');
        $this->call(RepairRequestSeeder::class);
        
        // Seed maintenance tasks
        $this->command->info('Seeding maintenance tasks...');
        $this->call(MaintenanceSeeder::class);
        
        $this->command->info('Comprehensive seeding completed successfully!');
        $this->command->info('Created:');
        $this->command->info('- 20 assets with various statuses');
        $this->command->info('- 15 repair requests with different urgency levels');
        $this->command->info('- 12 maintenance tasks with various schedules');
    }
}
