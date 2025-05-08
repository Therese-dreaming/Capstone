<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\RepairRequest;
use App\Models\User;
use App\Models\AssetHistory;  // Add this import
use App\Models\Asset;  // Add this import
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;  // Change this line

class RepairRequestController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        $technicians = User::whereIn('group_id', [1, 2])
            ->where('status', 'active')
            ->get();
        return view('repair-request', compact('categories', 'technicians'));
    }

    public function store(Request $request)
    {
        $rules = [
            'date_called' => 'required|date',
            'time_called' => 'required',
            'location' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'equipment' => 'required|string',
            'serial_number' => 'nullable|string',
            'issue' => 'required|string',
            'status' => 'required|in:pending,urgent',
            'technician_id' => 'nullable|exists:users,id'  // Add this line
        ];

        $request->validate($rules);

        // If serial_number is empty string or null, set it to null
        $serialNumber = $request->serial_number ? trim($request->serial_number) : null;

        // If serial number is provided, verify it exists in assets
        if ($serialNumber) {
            $asset = Asset::where('serial_number', $serialNumber)->first();
            if (!$asset) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Error! No asset found with this serial number. Please check and try again.');
            }
            // Use the exact serial number from the database
            $serialNumber = $asset->serial_number;
            
            // Store the current status before updating
            $oldStatus = $asset->status;
            
            // Update asset status to UNDER REPAIR
            $asset->update([
                'status' => 'UNDER REPAIR'
            ]);
            
            // Create asset history record
            AssetHistory::create([
                'asset_id' => $asset->id,
                'change_type' => 'STATUS',
                'old_value' => $oldStatus,
                'new_value' => 'UNDER REPAIR',
                'remarks' => "Asset status changed to UNDER REPAIR due to repair request",
                'changed_by' => auth()->id()
            ]);
        }

        // Check for existing active repair request
        $existingRequest = RepairRequest::where('location', $request->location)
            ->where('equipment', $request->equipment)
            ->whereNotIn('status', ['completed', 'disposed', 'cancelled'])
            ->first();

        if ($existingRequest) {
            // Check if the existing request is for a pulled out asset
            if ($existingRequest->status === 'pulled_out') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This asset is currently pulled out. Ticket number: ' . $existingRequest->ticket_number);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'There is already an active repair request for this equipment in this location. Ticket number: ' . $existingRequest->ticket_number);
        }

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

        $repairRequest = RepairRequest::create([
            'ticket_number' => $ticketNumber,
            'date_called' => $request->date_called,
            'time_called' => $request->time_called,
            'location' => $request->location,
            'category_id' => $request->category_id,
            'equipment' => $request->equipment,
            'serial_number' => $serialNumber,
            'issue' => $request->issue,
            'status' => $request->status,
            'technician_id' => $request->technician_id,  // Add this line
            'created_at' => $created_at,
            'updated_at' => $created_at
        ]);

        // Create notification for technician if assigned
        if ($request->technician_id) {
            Notification::create([
                'user_id' => $request->technician_id,
                'type' => 'repair_assigned',
                'message' => "New repair request assigned: {$ticketNumber} - {$request->equipment}",
                'is_read' => false,
                'link' => url(route('repair.status', [], false))
            ]);
        }

        // Create notification for all technicians if urgent
        if ($request->status === 'urgent') {
            $technicians = User::whereIn('group_id', [1, 2])->get();
            foreach ($technicians as $technician) {
                Notification::create([
                    'user_id' => $technician->id,
                    'type' => 'urgent_repair',
                    'message' => "Urgent repair request: {$ticketNumber} - {$request->equipment}",
                    'is_read' => false,
                    'link' => url(route('repair.status', [], false))
                ]);
            }
        }

        return redirect()->back()->with('success', 'Repair request submitted successfully. Your ticket number is: ' . $ticketNumber);
    }

    public function status(Request $request)
    {
        $technicians = User::whereIn('group_id', [1, 2])
            ->where('status', 'active')
            ->get();

        $urgentRepairs = RepairRequest::where('status', 'urgent')
            ->latest()
            ->get();

        // Update this line to exclude pulled_out requests as well
        $requests = RepairRequest::whereNotIn('status', ['completed', 'cancelled', 'pulled_out'])
            ->latest()
            ->get();

        return view('repair-status', compact(
            'urgentRepairs',
            'requests',
            'technicians'
        ));
    }

    public function update(Request $request, $id)
    {
        // Find the repair request
        $repairRequest = RepairRequest::findOrFail($id);
    
        // Validate request
        $request->validate([
            'status' => 'required|in:pending,urgent,in_progress,completed,cancelled,pulled_out,disposed',
            'remarks' => 'required_if:status,completed,cancelled,pulled_out|string|nullable',
            'technician_id' => 'nullable|exists:users,id',
            'date_finished' => 'required_if:status,completed,cancelled,pulled_out|date|nullable',
            'time_finished' => 'required_if:status,completed,cancelled,pulled_out|nullable',
        ]);
    
        // Update repair request with all fields
        $updateData = [
            'status' => $request->status,
            'remarks' => $request->remarks,
            'technician_id' => $request->technician_id,
            'updated_at' => now()
        ];
    
        // Handle asset status updates for pulled out requests
        if ($request->status === 'pulled_out' && $repairRequest->serial_number) {
            // Preserve the existing technician_id if not provided in the request
            if (!$request->technician_id) {
                $updateData['technician_id'] = $repairRequest->technician_id;
            }
            
            $asset = Asset::where('serial_number', $repairRequest->serial_number)->first();
            if ($asset) {
                $oldStatus = $asset->status;
                // Update asset status to PULLED OUT
                $asset->update([
                    'status' => 'PULLED OUT'
                ]);
                
                // Create repair history record
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'change_type' => 'REPAIR',
                    'old_value' => $request->issue ?? 'Not specified',
                    'new_value' => $request->status,
                    'remarks' => "Ticket: {$repairRequest->ticket_number}\nIssue: {$request->issue}\nRemarks: {$request->remarks}",
                    'changed_by' => auth()->id()
                ]);

                // Add status change record
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'change_type' => 'STATUS',
                    'old_value' => $oldStatus,
                    'new_value' => 'PULLED OUT',
                    'remarks' => "Asset status changed to PULLED OUT due to repair request",
                    'changed_by' => auth()->id()
                ]);
            }
        } elseif ($request->status === 'in_progress' && $repairRequest->serial_number) {
            // If the user clicks 'No' in pull out modal, set status back to IN USE
            $asset = Asset::where('serial_number', $repairRequest->serial_number)->first();
            if ($asset) {
                // Update asset status back to IN USE
                $asset->update([
                    'status' => 'IN USE'
                ]);
                
                // Create asset history record
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'change_type' => 'REPAIR',
                    'old_value' => $request->issue ?? 'Not specified',
                    'new_value' => $request->status,
                    'remarks' => "Ticket: {$this->ticket_number}\nIssue: {$request->issue}\nRemarks: {$request->remarks}",
                    'changed_by' => auth()->id()
                ]);
            }
        }

        // Handle asset status updates for completed requests
        if ($request->status === 'completed' && $repairRequest->serial_number) {
            $asset = Asset::where('serial_number', $repairRequest->serial_number)->first();
            if ($asset) {
                // Update asset status to IN USE
                $asset->update([
                    'status' => 'IN USE'
                ]);
                
                // Create repair history record
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'change_type' => 'REPAIR',
                    'old_value' => $repairRequest->issue ?? 'Not specified',
                    'new_value' => 'completed',
                    'remarks' => "Ticket: {$repairRequest->ticket_number}\nIssue: {$repairRequest->issue}\nRemarks: " . ($request->remarks ?? 'No remarks provided'),
                    'changed_by' => auth()->id()
                ]);

                // Create status change record
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'change_type' => 'STATUS',
                    'old_value' => 'UNDER REPAIR',
                    'new_value' => 'IN USE',
                    'remarks' => "Asset repair completed: " . ($request->remarks ?? 'No remarks provided'),
                    'changed_by' => auth()->id()
                ]);
            }
        }

        // Handle asset status updates for cancelled requests
        if ($request->status === 'cancelled' && $repairRequest->serial_number) {
            $asset = Asset::where('serial_number', $repairRequest->serial_number)->first();
            if ($asset) {
                // Update asset status back to IN USE
                $asset->update([
                    'status' => 'IN USE'
                ]);
                
                // Create repair history record
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'change_type' => 'REPAIR',
                    'old_value' => $repairRequest->issue ?? 'Not specified',
                    'new_value' => 'cancelled',
                    'remarks' => "Ticket: {$repairRequest->ticket_number}\nIssue: {$repairRequest->issue}\nRemarks: " . ($request->remarks ?? 'No remarks provided'),
                    'changed_by' => auth()->id()
                ]);

                // Create status change record
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'change_type' => 'STATUS',
                    'old_value' => 'UNDER REPAIR',
                    'new_value' => 'IN USE',
                    'remarks' => "Asset repair cancelled: " . ($request->remarks ?? 'No remarks provided'),
                    'changed_by' => auth()->id()
                ]);
            }
        }

        // Set completion/cancellation datetime if applicable
        if (in_array($request->status, ['completed', 'cancelled', 'pulled_out']) && $request->date_finished && $request->time_finished) {
            $updateData['completed_at'] = date('Y-m-d H:i:s', strtotime($request->date_finished . ' ' . $request->time_finished));
        }
    
        // Perform the update
        try {
            $repairRequest->update($updateData);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $request->status === 'cancelled' ? 'Request cancelled successfully' : 'Request updated successfully',
                    'request' => $repairRequest
                ]);
            }
            
            $message = $request->status === 'cancelled' ? 'Request cancelled successfully' : 'Request updated successfully';
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Add new method to handle asset disposal
    public function disposeAsset(Request $request, $serialNumber)
    {
        $asset = Asset::where('serial_number', $serialNumber)->firstOrFail();
        
        // Update asset status to DISPOSED
        $asset->update([
            'status' => 'DISPOSED'
        ]);
        
        // Create asset history record
        AssetHistory::create([
            'asset_id' => $asset->id,
            'change_type' => 'REPAIR',  // Change this from 'STATUS' to 'REPAIR'
            'old_value' => $request->issue ?? 'Not specified',  // Store the issue instead of status
            'new_value' => $request->status,
            'remarks' => "Ticket: {$this->ticket_number}\nIssue: {$request->issue}\nRemarks: {$request->remarks}",
            'changed_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asset marked for disposal successfully'
        ]);
    }

    public function destroy($id)
    {
        $request = RepairRequest::findOrFail($id);
        $request->delete();

        return redirect()->route('repair.status')
            ->with('success', 'Repair request deleted successfully.');
    }

    public function destroyMultiple(Request $request)
    {
        try {
            RepairRequest::whereIn('id', $request->ids)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function completed()
    {
        // Update this line to include both completed and cancelled requests
        $completedRequests = RepairRequest::whereIn('status', ['completed', 'cancelled', 'pulled_out'])
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
        if ($request->location) {
            $query->where('location', $request->location);
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

        return view('pdf.repair-completed', compact('completedRequests'))->render();
    }

    public function exportPDF(Request $request)
    {
        $query = RepairRequest::where('status', 'completed')
            ->with('technician');

        // Apply same filters as preview
        if ($request->location) {
            $query->where('location', $request->location);
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
