<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LabLog;
use App\Models\User;
use Carbon\Carbon;

class TestOngoingSessionsSeeder extends Seeder
{
    /**
     * Run the database seeds for testing ongoing sessions.
     * This creates realistic test data for the improved login system.
     */
    public function run(): void
    {
        // Clear any existing ongoing sessions first (optional)
        $existingOngoing = LabLog::where('status', 'on-going')->count();
        if ($existingOngoing > 0) {
            $this->command->warn("Found {$existingOngoing} existing ongoing sessions.");
            if ($this->command->confirm('Clear existing ongoing sessions first?', true)) {
                LabLog::where('status', 'on-going')->delete();
                $this->command->info('Cleared existing ongoing sessions.');
            }
        }

        // Get yesterday's date for realistic test data
        $yesterday = Carbon::yesterday();
        
        // Check if laboratories exist, if not create them
        $labCount = \App\Models\Laboratory::count();
        if ($labCount === 0) {
            $this->command->info('🏢 No laboratories found. Creating laboratory records...');
            
            $labSeeder = new \Database\Seeders\LaboratorySeeder();
            $labSeeder->setCommand($this->command);
            $labSeeder->run();
            
            $this->command->info('✅ Laboratory records created successfully!');
            $this->command->info('');
        }
        
        // Find faculty users
        $facultyUsers = User::whereIn('position', ['Teacher', 'Faculty'])
            ->where('status', 'active')
            ->get();

        if ($facultyUsers->isEmpty()) {
            $this->command->error('❌ No faculty users found!');
            $this->command->info('🔧 Creating faculty users automatically...');
            
            // Run the faculty users seeder
            $facultySeeder = new \Database\Seeders\FacultyUsersSeeder();
            $facultySeeder->setCommand($this->command);
            $facultySeeder->run();
            
            // Re-fetch faculty users
            $facultyUsers = User::whereIn('position', ['Teacher', 'Faculty'])
                ->where('status', 'active')
                ->get();
                
            if ($facultyUsers->isEmpty()) {
                $this->command->error('❌ Failed to create faculty users!');
                return;
            }
            
            $this->command->info('✅ Faculty users created successfully!');
            $this->command->info('');
        }

        // Take up to 4 users for testing
        $testUsers = $facultyUsers->take(4);
        
        $testScenarios = [
            [
                'name' => 'Morning Lecture Forgotten',
                'time_in' => $yesterday->copy()->setHour(8)->setMinute(30),
                'laboratory' => '401',
                'purpose' => 'lecture',
                'description' => 'Faculty started morning lecture, forgot to tap out'
            ],
            [
                'name' => 'Afternoon Practical Session',
                'time_in' => $yesterday->copy()->setHour(14)->setMinute(15),
                'laboratory' => '402',
                'purpose' => 'practical',
                'description' => 'Practical session in the afternoon, no logout'
            ],
            [
                'name' => 'Evening Research Work',
                'time_in' => $yesterday->copy()->setHour(18)->setMinute(45),
                'laboratory' => '403',
                'purpose' => 'research',
                'description' => 'Late research session, forgot to logout'
            ],
            [
                'name' => 'Examination Session',
                'time_in' => $yesterday->copy()->setHour(10)->setMinute(0),
                'laboratory' => '404',
                'purpose' => 'examination',
                'description' => 'Exam proctoring session, no logout recorded'
            ]
        ];

        $createdSessions = [];

        foreach ($testUsers as $index => $user) {
            if (isset($testScenarios[$index])) {
                $scenario = $testScenarios[$index];
                
                $session = LabLog::create([
                    'user_id' => $user->id,
                    'laboratory' => $scenario['laboratory'],
                    'purpose' => $scenario['purpose'],
                    'time_in' => $scenario['time_in'],
                    'time_out' => null,
                    'status' => 'on-going',
                    'created_at' => $scenario['time_in'],
                    'updated_at' => $scenario['time_in'],
                ]);

                $createdSessions[] = [
                    'user' => $user,
                    'session' => $session,
                    'scenario' => $scenario
                ];
            }
        }

        // Display results
        $this->command->info('');
        $this->command->info('🎯 Created Test Ongoing Sessions:');
        $this->command->info('=====================================');
        
        foreach ($createdSessions as $data) {
            $user = $data['user'];
            $session = $data['session'];
            $scenario = $data['scenario'];
            
            $this->command->info("👤 {$user->name} (ID: {$user->id})");
            $this->command->info("   📍 Laboratory {$session->laboratory}");
            $this->command->info("   🎯 Purpose: {$session->purpose}");
            $this->command->info("   ⏰ Time In: {$session->time_in->format('Y-m-d H:i A')}");
            $this->command->info("   📝 {$scenario['description']}");
            if ($user->rfid_number) {
                $this->command->info("   🏷️  RFID: {$user->rfid_number}");
            }
            $this->command->info('');
        }

        $this->command->info('🧪 Testing Instructions:');
        $this->command->info('========================');
        $this->command->info('1. 🌐 Visit: /lab-schedule/logging');
        $this->command->info('2. 🏷️  Use any of the RFID numbers above to tap into a DIFFERENT lab');
        $this->command->info('3. ⚠️  You should see the ongoing session warning modal');
        $this->command->info('4. 🛠️  Check manual logout: /lab-schedule/manual-logout');
        $this->command->info('5. 👨‍💼 Admin can manually set logout times for forgotten sessions');
        $this->command->info('');
        $this->command->info('💡 Expected Behavior:');
        $this->command->info('- ✅ New login allowed despite ongoing sessions');
        $this->command->info('- ⚠️  Warning modal shows with session details');
        $this->command->info('- 📋 Manual logout page lists all ongoing sessions');
        $this->command->info('- 📊 No automatic 11:59 PM logout times');
    }
}
