<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\TestOngoingSessionsSeeder;

class CreateTestOngoingSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:ongoing-sessions {--clear : Clear existing ongoing sessions first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test ongoing sessions from yesterday for testing the improved login system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Creating Test Ongoing Sessions...');
        $this->info('===================================');
        
        $seeder = new TestOngoingSessionsSeeder();
        $seeder->setCommand($this);
        $seeder->run();
        
        $this->info('✅ Test data created successfully!');
        $this->info('');
        $this->info('🚀 Quick Test Commands:');
        $this->info('- View ongoing sessions: php artisan tinker -> \\App\\Models\\LabLog::where("status", "on-going")->with("user")->get()');
        $this->info('- Visit manual logout: http://your-domain/lab-schedule/manual-logout');
        $this->info('- Test login page: http://your-domain/lab-schedule/logging');
        
        return Command::SUCCESS;
    }
}
