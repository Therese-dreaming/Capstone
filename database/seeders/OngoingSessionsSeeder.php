<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LabLog;
use App\Models\User;
use Carbon\Carbon;

class OngoingSessionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get yesterday's date
        $yesterday = Carbon::yesterday();
        
        // Find some faculty users to create ongoing sessions for
        $facultyUsers = User::whereIn('position', ['Teacher', 'Faculty'])
            ->where('status', 'active')
            ->limit(5)
            ->get();

        if ($facultyUsers->isEmpty()) {
            $this->command->warn('No faculty users found. Please seed users first.');
            return;
        }

        $purposes = ['lecture', 'examination', 'practical', 'research', 'training', 'other'];
        $laboratories = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        $ongoingSessions = [];

        foreach ($facultyUsers as $index => $user) {
            // Create different scenarios for testing
            switch ($index) {
                case 0:
                    // Morning session - forgot to logout after lecture
                    $timeIn = $yesterday->copy()->setHour(8)->setMinute(30);
                    $lab = 401;
                    $purpose = 'lecture';
                    break;
                    
                case 1:
                    // Afternoon session - forgot to logout after practical
                    $timeIn = $yesterday->copy()->setHour(14)->setMinute(15);
                    $lab = 402;
                    $purpose = 'practical';
                    break;
                    
                case 2:
                    // Evening session - forgot to logout after research
                    $timeIn = $yesterday->copy()->setHour(18)->setMinute(45);
                    $lab = 403;
                    $purpose = 'research';
                    break;
                    
                case 3:
                    // Late morning - forgot to logout after examination
                    $timeIn = $yesterday->copy()->setHour(10)->setMinute(0);
                    $lab = 404;
                    $purpose = 'examination';
                    break;
                    
                default:
                    // Random session
                    $timeIn = $yesterday->copy()->setHour(rand(9, 16))->setMinute(rand(0, 59));
                    $lab = $laboratories[array_rand($laboratories)];
                    $purpose = $purposes[array_rand($purposes)];
                    break;
            }

            $ongoingSessions[] = [
                'user_id' => $user->id,
                'laboratory' => $lab,
                'purpose' => $purpose,
                'time_in' => $timeIn,
                'time_out' => null,
                'status' => 'on-going',
                'created_at' => $timeIn,
                'updated_at' => $timeIn,
            ];
        }

        // Insert the ongoing sessions
        LabLog::insert($ongoingSessions);

        $this->command->info('Created ' . count($ongoingSessions) . ' ongoing sessions from yesterday:');
        
        foreach ($ongoingSessions as $session) {
            $user = User::find($session['user_id']);
            $this->command->line("- {$user->name} in Lab {$session['laboratory']} ({$session['purpose']}) since {$session['time_in']->format('Y-m-d H:i')}");
        }
        
        $this->command->info('');
        $this->command->info('🧪 Test Scenarios:');
        $this->command->info('1. Try logging into any lab with these users - should show warning');
        $this->command->info('2. Check manual logout page: /lab-schedule/manual-logout');
        $this->command->info('3. Test the warning modal and admin interface');
    }
}
