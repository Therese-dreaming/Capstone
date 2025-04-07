<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\RepairRequest;
use App\Models\User;  // Add this line
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;  // Change this line

class RepairRequestController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        return view('repair-request', compact('categories'));
    }

    public function store(Request $request)
    {
        $rules = [
            'date_called' => 'required|date',
            'time_called' => 'required',
            'office_room' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'equipment' => 'required|string',
            'issue' => 'required|string',
            'status' => 'required|in:pending,urgent'
        ];

        // Only require department if there's no ongoing activity
        if ($request->input('ongoing_activity') !== 'yes') {
            $rules['department'] = 'required|string';
        }

        $request->validate($rules);

        // Generate ticket number (format: REQ-YYYYMMDD-XXXX)
        $date = date('Ymd');
        $lastTicket = RepairRequest::where('ticket_number', 'like', "REQ-$date-%")
            ->orderBy('ticket_number', 'desc')
            ->first();
        
        $sequence = '0001';
        if ($lastTicket) {
            $lastSequence = substr($lastTicket->ticket_number, -4);
            $sequence = str_pad((int)$lastSequence + 1, 4, '0', STR_PAD_LEFT);
        }
        
        $ticketNumber = "REQ-$date-$sequence";

        // Combine date and time into a single datetime string
        $created_at = date('Y-m-d H:i:s', strtotime($request->date_called . ' ' . $request->time_called));

        RepairRequest::create([
            'ticket_number' => $ticketNumber,
            'date_called' => $request->date_called,
            'time_called' => $request->time_called,
            'department' => $request->input('ongoing_activity') === 'yes' ? null : $request->department,
            'office_room' => $request->office_room,
            'category_id' => $request->category_id,
            'equipment' => $request->equipment,
            'issue' => $request->issue,
            'status' => $request->status,
            'created_at' => $created_at,
            'updated_at' => $created_at
        ]);

        return redirect()->back()->with('success', 'Repair request submitted successfully. Your ticket number is: ' . $ticketNumber);
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

        $requests = RepairRequest::where('status', '!=', 'completed')
            ->latest()
            ->get();

        // Calculate statistics
        $totalOpen = RepairRequest::whereIn('status', ['pending', 'urgent', 'in_progress'])->count();
        $completedThisMonth = RepairRequest::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->count();

        // Fix the average response time calculation
        $avgResponseTime = RepairRequest::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->select(DB::raw('ROUND(AVG(DATEDIFF(completed_at, created_at)), 1) as avg_days'))
            ->value('avg_days') ?? 0;

        return view('repair-status', compact(
            'urgentRepairs',
            'requests',
            'totalOpen',
            'completedThisMonth',
            'avgResponseTime',
            'technicians'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'technician_id' => 'required|exists:users,id',
            'remarks' => 'required|string',
            'status' => 'required|in:urgent,in_progress,pulled_out,disposed,completed'
        ]);

        $repairRequest = RepairRequest::findOrFail($id);
        
        $updateData = [
            'technician_id' => $request->technician_id,
            'remarks' => $request->remarks,
            'status' => $request->status
        ];

        // Add completed_at if status is completed
        if ($request->status === 'completed') {
            $updateData['completed_at'] = now();
        }

        $repairRequest->update($updateData);

        // Reload the repair request with technician relationship
        $repairRequest = $repairRequest->fresh()->load(['technician' => function($query) {
            $query->select('id', 'name');
        }]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Request updated successfully',
                'request' => $repairRequest,
                'technician_name' => $repairRequest->technician->name
            ]);
        }

        return redirect()->back()->with('success', 'Request updated successfully.');
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

    public function urgent()
    {
        $urgentRepairs = RepairRequest::where('status', 'urgent')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalOpen = RepairRequest::whereNotIn('status', ['completed'])->count();
        $completedThisMonth = RepairRequest::where('status', 'completed')
            ->whereMonth('updated_at', now()->month)
            ->count();
        
        // Update this line to use completed_at instead of date_finished
        $avgResponseTime = RepairRequest::whereNotNull('completed_at')
            ->avg(DB::raw('DATEDIFF(completed_at, created_at)')) ?? 0;

        $technicians = User::where('role', 'technician')->get();

        return view('urgent-repairs', compact('urgentRepairs', 'totalOpen', 'completedThisMonth', 'avgResponseTime', 'technicians'));
    }

    public function markUrgent($id)
    {
        $request = RepairRequest::findOrFail($id);
        $request->status = 'urgent';
        $request->save();

        return response()->json([
            'success' => true,
            'message' => 'Request marked as urgent successfully'
        ]);
    }

    public function previewPDF(Request $request)
    {
        $query = RepairRequest::where('status', 'completed')
            ->with('technician');

        // Apply filters
        if ($request->department) {
            $query->where('department', $request->department);
        }
        if ($request->lab_room) {
            $query->where('office_room', $request->lab_room);
        }
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->date_filter_type === 'completion') {
            if ($request->start_date) {
                $query->whereDate('completed_at', '>=', $request->start_date);
            }
            if ($request->end_date) {
                $query->whereDate('completed_at', '<=', $request->end_date);
            }
        }

        $completedRequests = $query->orderBy('created_at', 'desc')->get();

        // Return HTML response for preview
        return view('pdf.repair-completed', compact('completedRequests'))->render();
    }

    public function exportPDF(Request $request)
    {
        $query = RepairRequest::where('status', 'completed')
            ->with('technician');

        // Apply same filters as preview
        if ($request->department) {
            $query->where('department', $request->department);
        }
        if ($request->lab_room) {
            $query->where('office_room', $request->lab_room);
        }
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->date_filter_type === 'completion') {
            if ($request->start_date) {
                $query->whereDate('completed_at', '>=', $request->start_date);
            }
            if ($request->end_date) {
                $query->whereDate('completed_at', '<=', $request->end_date);
            }
        }

        $completedRequests = $query->orderBy('created_at', 'desc')->get();

        $pdf = PDF::loadView('pdf.repair-completed', compact('completedRequests'));
        return $pdf->download('completed-repairs.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new CompletedRepairsExport($request->all()), 'completed-repairs.xlsx');
    }
}
