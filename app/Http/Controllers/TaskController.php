<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;
use App\Models\RepairRequest;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Check if user has appropriate access
        if ($user->group_id > 2) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }
        
        // Get maintenance tasks assigned to the user with pagination
        $maintenanceTasks = Maintenance::where('technician_id', $user->id)
            ->where('status', 'scheduled')
            ->orderBy('scheduled_date')
            ->paginate(5, ['*'], 'maintenance_page');

        // Get repair requests assigned to the user with pagination
        $repairRequests = RepairRequest::where('technician_id', $user->id)
            ->whereNotIn('status', ['completed', 'cancelled', 'pulled_out'])
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'repair_page');

        return view('my-tasks', compact('maintenanceTasks', 'repairRequests'));
    }
}