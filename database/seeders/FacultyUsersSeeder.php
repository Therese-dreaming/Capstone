<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class FacultyUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $facultyUsers = [
            [
                'name' => 'Dr. John Smith',
                'username' => 'john.smith',
                'password' => Hash::make('password123'),
                'position' => 'Faculty',
                'group_id' => 3, // Assuming group 3 is for faculty
                'status' => 'active',
                'rfid_number' => '1234567890',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Prof. Maria Garcia',
                'username' => 'maria.garcia',
                'password' => Hash::make('password123'),
                'position' => 'Teacher',
                'group_id' => 3,
                'status' => 'active',
                'rfid_number' => '2345678901',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Dr. Robert Johnson',
                'username' => 'robert.johnson',
                'password' => Hash::make('password123'),
                'position' => 'Faculty',
                'group_id' => 3,
                'status' => 'active',
                'rfid_number' => '3456789012',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Prof. Sarah Wilson',
                'username' => 'sarah.wilson',
                'password' => Hash::make('password123'),
                'position' => 'Teacher',
                'group_id' => 3,
                'status' => 'active',
                'rfid_number' => '4567890123',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Dr. Michael Brown',
                'username' => 'michael.brown',
                'password' => Hash::make('password123'),
                'position' => 'Faculty',
                'group_id' => 3,
                'status' => 'active',
                'rfid_number' => '5678901234',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($facultyUsers as $userData) {
            // Check if user already exists by username or RFID
            $existingUser = User::where('username', $userData['username'])
                ->orWhere('rfid_number', $userData['rfid_number'])
                ->first();

            if (!$existingUser) {
                User::create($userData);
                $this->command->info("✅ Created faculty user: {$userData['name']} (RFID: {$userData['rfid_number']})");
            } else {
                $this->command->info("⚠️  User already exists: {$userData['name']}");
            }
        }

        $this->command->info('');
        $this->command->info('🎓 Faculty Users Created:');
        $this->command->info('======================');
        
        $createdUsers = User::whereIn('position', ['Teacher', 'Faculty'])
            ->where('status', 'active')
            ->get();

        foreach ($createdUsers as $user) {
            $this->command->info("👤 {$user->name}");
            $this->command->info("   🏷️  RFID: {$user->rfid_number}");
            $this->command->info("   👔 Position: {$user->position}");
            $this->command->info('');
        }

        $this->command->info('💡 You can now run: php artisan test:ongoing-sessions');
    }
}
