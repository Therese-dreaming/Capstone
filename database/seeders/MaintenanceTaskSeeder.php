<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaintenanceTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            'Format and Software Installation',
            'Physical Checking',
            'Windows Update',
            'General Cleaning',
            'Antivirus Update',
            'Scan for Virus',
            'Disk Cleanup',
            'Cleaning',
            'Disk Maintenance',
        ];

        foreach ($tasks as $task) {
            DB::table('maintenance_tasks')->updateOrInsert(
                ['name' => $task],
                ['name' => $task, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        if (isset($this->command)) {
            $this->command->info('Seeded ' . count($tasks) . ' maintenance tasks.');
        }
    }
}
