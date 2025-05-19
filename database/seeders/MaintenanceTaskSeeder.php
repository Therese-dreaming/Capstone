<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaintenanceTaskSeeder extends Seeder
{
    public function run()
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
            'Disk Maintenance'
        ];

        foreach ($tasks as $task) {
            DB::table('maintenance_tasks')->insert([
                'name' => $task,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}