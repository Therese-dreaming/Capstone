<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\FacultyUsersSeeder;

class CreateFacultyUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:faculty-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create sample faculty users for testing the lab system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('👨‍🏫 Creating Faculty Users...');
        $this->info('===========================');
        
        $seeder = new FacultyUsersSeeder();
        $seeder->setCommand($this);
        $seeder->run();
        
        $this->info('✅ Faculty users setup complete!');
        $this->info('');
        $this->info('🚀 Next Steps:');
        $this->info('- Run: php artisan test:ongoing-sessions');
        $this->info('- Test the improved login system');
        
        return Command::SUCCESS;
    }
}
