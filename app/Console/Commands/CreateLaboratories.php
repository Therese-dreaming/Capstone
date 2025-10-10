<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\LaboratorySeeder;

class CreateLaboratories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:laboratories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create laboratory records for the lab system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏢 Creating Laboratory Records...');
        $this->info('===============================');
        
        $seeder = new LaboratorySeeder();
        $seeder->setCommand($this);
        $seeder->run();
        
        $this->info('✅ Laboratory records setup complete!');
        $this->info('');
        $this->info('🚀 Available Labs:');
        
        $labs = \App\Models\Laboratory::orderBy('number')->get();
        foreach ($labs as $lab) {
            $this->info("🔬 Lab {$lab->number} - {$lab->name}");
        }
        
        $this->info('');
        $this->info('💡 You can now run: php artisan test:ongoing-sessions');
        
        return Command::SUCCESS;
    }
}
