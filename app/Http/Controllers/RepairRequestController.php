<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\RepairRequest;
use App\Models\User;  // Add this line
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'status' => 'required|in:pending,urgent'
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

    public function status(Request $request)
    {
        // Change to use group_id instead of role
        $technicians = User::whereIn('group_id', [1, 2])
            ->where('status', 'active')
            ->get();

        $urgentRepairs = RepairRequest::where('status', 'urgent')
            // Remove the whereNull('technician_id') condition
            ->latest()
            ->get();

        $requests = RepairRequest::latest()->get();

        // Calculate statistics
        $totalOpen = RepairRequest::whereIn('status', ['pending', 'urgent', 'in_progress'])->count();
        $completedThisMonth = RepairRequest::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->count();
        
        $avgResponseTime = RepairRequest::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(DAY, created_at, completed_at)) as avg_days'))
            ->first()
            ->avg_days ?? 0;

        return view('repair-status', compact(
            'urgentRepairs',
            'requests',
            'totalOpen',
            'completedThisMonth',
            'avgResponseTime',
            'technicians'  // Add this to the compact function
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date_finished' => 'required|date',
            'time_finished' => 'required',
            'technician_id' => 'required|exists:users,id',
            'remarks' => 'required|string',
            'status' => 'required|in:urgent,in_progress,completed'
        ]);

        $repairRequest = RepairRequest::findOrFail($id);
        
        $completedAt = $request->status === 'completed' 
            ? $request->date_finished . ' ' . $request->time_finished 
            : null;

        $repairRequest->update([
            'completed_at' => $completedAt,
            'technician_id' => $request->technician_id,
            'remarks' => $request->remarks,
            'status' => $request->status
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Repair request updated successfully to ' . ucfirst($request->status)
            ]);
        }

        return redirect()->back()->with('success', 'Repair request updated successfully');
    }

    public function destroy($id)
    {
        $request = RepairRequest::findOrFail($id);
        $request->delete();
        
        return redirect()->route('repair.status')
            ->with('success', 'Repair request deleted successfully.');
    }

    public function completed()
        {
            $completedRequests = RepairRequest::where('status', 'completed')
                ->with('technician')
                ->orderBy('updated_at', 'desc')
                ->get();
    
            return view('repair-completed', compact('completedRequests'));
        }
}
