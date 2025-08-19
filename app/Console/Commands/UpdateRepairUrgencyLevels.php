<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RepairRequest;
use Carbon\Carbon;

class UpdateRepairUrgencyLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repair:update-urgency-levels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update urgency levels for all pending repair requests based on ongoing activities and request age';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting urgency level update...');
        
        $pendingRequests = RepairRequest::whereNotIn('status', ['completed', 'cancelled', 'pulled_out'])->get();
        $updatedCount = 0;
        
        foreach ($pendingRequests as $request) {
            $oldUrgencyLevel = $request->urgency_level;
            $newUrgencyLevel = $this->calculateUrgencyLevel($request);
            
            if ($oldUrgencyLevel !== $newUrgencyLevel) {
                $request->update(['urgency_level' => $newUrgencyLevel]);
                $updatedCount++;
                
                $this->line("Updated request {$request->ticket_number}: Level {$oldUrgencyLevel} → Level {$newUrgencyLevel}");
            }
        }
        
        $this->info("Urgency level update completed. Updated {$updatedCount} requests out of {$pendingRequests->count()} total pending requests.");
        
        return 0;
    }

    /**
     * Calculate urgency level for a repair request
     */
    private function calculateUrgencyLevel($request)
    {
        // Check if there's an ongoing class/event (urgency level 1 - highest)
        if ($request->ongoing_activity === 'yes') {
            return 1;
        }
        
        // Check if request is over a week old (urgency level 2)
        $requestDate = Carbon::parse($request->date_called);
        $oneWeekAgo = Carbon::now()->subWeek();
        
        if ($requestDate->lt($oneWeekAgo)) {
            return 2;
        }
        
        // New request within the week (urgency level 3 - lowest)
        return 3;
    }
}
