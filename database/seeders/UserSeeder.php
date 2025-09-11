<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            // Secretaries (Group ID 2) - Same as Technicians
            [
                'name' => 'Maria Santos',
                'username' => 'maria.santos',
                'password' => Hash::make('password123'),
                'group_id' => 2,
                'department' => 'Administrative Office',
                'position' => 'Secretary/Technician',
                'role' => 'secretary',
                'gender' => 'female',
            ],
            [
                'name' => 'Juan Dela Cruz',
                'username' => 'juan.delacruz',
                'password' => Hash::make('password123'),
                'group_id' => 2,
                'department' => 'Administrative Office',
                'position' => 'Secretary/Technician',
                'role' => 'secretary',
                'gender' => 'male',
            ],
            [
                'name' => 'Ana Rodriguez',
                'username' => 'ana.rodriguez',
                'password' => Hash::make('password123'),
                'group_id' => 2,
                'department' => 'Administrative Office',
                'position' => 'Secretary/Technician',
                'role' => 'secretary',
                'gender' => 'female',
            ],
            [
                'name' => 'Carlos Mendoza',
                'username' => 'carlos.mendoza',
                'password' => Hash::make('password123'),
                'group_id' => 2,
                'department' => 'Administrative Office',
                'position' => 'Secretary/Technician',
                'role' => 'secretary',
                'gender' => 'male',
            ],

            // Regular Users (Group ID 3)
            [
                'name' => 'Lisa Garcia',
                'username' => 'lisa.garcia',
                'password' => Hash::make('password123'),
                'group_id' => 3,
                'department' => 'Faculty',
                'position' => 'Professor',
                'role' => 'user',
                'gender' => 'female',
            ],
            [
                'name' => 'Roberto Silva',
                'username' => 'roberto.silva',
                'password' => Hash::make('password123'),
                'group_id' => 3,
                'department' => 'Faculty',
                'position' => 'Professor',
                'role' => 'user',
                'gender' => 'male',
            ],

            // Custodians (Group ID 4) - for asset management
            [
                'name' => 'Elena Torres',
                'username' => 'elena.torres',
                'password' => Hash::make('password123'),
                'group_id' => 4,
                'department' => 'Asset Management',
                'position' => 'Asset Custodian',
                'role' => 'custodian',
                'gender' => 'female',
            ],
            [
                'name' => 'David Kim',
                'username' => 'david.kim',
                'password' => Hash::make('password123'),
                'group_id' => 4,
                'department' => 'Asset Management',
                'position' => 'Asset Custodian',
                'role' => 'custodian',
                'gender' => 'male',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['username' => $userData['username']],
                $userData
            );
        }

        $this->command->info('Created ' . count($users) . ' users with various roles:');
        $this->command->info('- 4 Secretaries/Technicians (Group ID 2)');
        $this->command->info('- 2 Regular Users (Group ID 3)');
        $this->command->info('- 2 Custodians (Group ID 4)');
        $this->command->info('Note: Group ID 1 = Admin (handled by CreateDefaultUserSeeder)');
    }
}
