<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\RepairRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  // Add this line

class RepairRequestController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        return view('repair-request', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_called' => 'required|date',
            'time_called' => 'required',
            'department' => 'required|string',
            'office_room' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'equipment' => 'required|string',
            'issue' => 'required|string',
            'status' => 'required|in:pending,urgent'  // Add validation for status
        ]);

        RepairRequest::create([
            'date_called' => $request->date_called,
            'time_called' => $request->time_called,
            'department' => $request->department,
            'office_room' => $request->office_room,
            'category_id' => $request->category_id,
            'equipment' => $request->equipment,
            'issue' => $request->issue,
            'status' => $request->status  // Use the status from the form instead of hardcoding 'pending'
        ]);

        return redirect()->back()->with('success', 'Repair request submitted successfully');
    }

    public function status()
    {
        $urgentRepairs = RepairRequest::where('status', 'urgent')
            ->whereNull('technician_id')
            ->get();
            
        $requests = RepairRequest::orderBy('created_at', 'desc')
            ->with(['technician', 'category'])
            ->get();

        $totalOpen = RepairRequest::whereIn('status', ['pending', 'urgent', 'in_progress'])->count();
        $completedThisMonth = RepairRequest::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->count();
        
        // Calculate average response time in days
        $avgResponseTime = RepairRequest::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->avg(DB::raw('DATEDIFF(completed_at, created_at)'));

        return view('repair-status', compact(
            'urgentRepairs',
            'requests',
            'totalOpen',
            'completedThisMonth',
            'avgResponseTime'
        ));
    }
}