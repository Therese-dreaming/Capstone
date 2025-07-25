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
use Illuminate\Support\Facades\Storage;
use App\Models\NonRegisteredAsset;
use Illuminate\Support\Facades\Auth;

class RepairRequestController extends Controller
{
    public function create()
    {
        // Check if user is a secretary (group_id 2)
        if (auth()->check() && auth()->user()->group_id === 2) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::all();
        $technicians = User::whereIn('group_id', [1, 2])
            ->where('status', 'active')
            ->get();
        return view('repair-request', compact('categories', 'technicians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_called' => 'required|date',
            'time_called' => 'required',
            'building' => 'required|string|max:255',
            'floor' => 'required|string|max:255',
            'room' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'equipment' => 'required|string',
            'serial_number' => 'nullable|string',
            'issue' => 'required|string',
            'status' => 'required|in:pending,urgent',
            'technician_id' => 'nullable|exists:users,id',
            'photo' => 'nullable|string|max:5242880' // 5MB base64 string
        ]);

        // If user is secretary, force technician_id to be their own ID
        if (auth()->user()->group_id === 2) {
            $request->merge(['technician_id' => auth()->id()]);
        }

        // If serial_number is empty string or null, set it to null
        $serialNumber = $request->serial_number ? trim($request->serial_number) : null;

        // Handle photo upload if present
        $photoPath = null;
        if ($request->has('photo') && $request->photo) {
            $photoPath = $this->savePhoto($request->photo);
        }

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
        $existingRequest = RepairRequest::where('building', $request->building)
            ->where('floor', $request->floor)
            ->where('room', $request->room)
            ->where('equipment', $request->equipment)
            ->whereNotIn('status', ['completed', 'disposed', 'cancelled'])
            ->first();

        if ($existingRequest) {
            // If the existing request is for a pulled out asset, check the asset's actual status
            if ($existingRequest->status === 'pulled_out' && $existingRequest->serial_number) {
                $asset = Asset::where('serial_number', $existingRequest->serial_number)->first();
                if ($asset && strtoupper($asset->status) === 'PULLED OUT') {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'This asset is currently pulled out. Ticket number: ' . $existingRequest->ticket_number);
                }
                // If asset is not PULLED OUT, allow new request (do not return here)
            } else {
                // For other statuses, still block
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'There is already an active repair request for this equipment in this location. Ticket number: ' . $existingRequest->ticket_number);
            }
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
            'building' => $request->building,
            'floor' => $request->floor,
            'room' => $request->room,
            'category_id' => $request->category_id,
            'equipment' => $request->equipment,
            'serial_number' => $serialNumber,
            'issue' => $request->issue,
            'photo' => $photoPath,
            'status' => $request->status,
            'technician_id' => $request->technician_id,
            'created_by' => auth()->id(),
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

        // Notify admins if request is created by a regular user
        if (auth()->user()->group_id === 3) {
            $admins = User::where('group_id', 1)->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'repair_request',
                    'message' => "New repair request from user: {$ticketNumber} - {$request->equipment}",
                    'is_read' => false,
                    'link' => url(route('repair.status', [], false))
                ]);
            }
        }

        return redirect()->back()->with('success', 'Repair request submitted successfully. Your ticket number is: ' . $ticketNumber);
    }

    /**
     * Save the base64 encoded photo to storage
     *
     * @param string $base64Image
     * @return string|null
     */
    private function savePhoto($base64Image)
    {
        try {
            // Remove the data URL prefix if present
            if (strpos($base64Image, 'data:image') === 0) {
                list(, $base64Image) = explode(',', $base64Image);
            }

            // Decode the base64 image
            $imageData = base64_decode($base64Image);
            if (!$imageData) {
                return null;
            }

            // Generate a unique filename
            $filename = 'repair_' . time() . '_' . uniqid() . '.jpg';
            
            // Save the image to storage
            $path = 'repair_photos/' . $filename;
            Storage::disk('public')->put($path, $imageData);

            return $path;
        } catch (\Exception $e) {
            \Log::error('Error saving repair photo: ' . $e->getMessage());
            return null;
        }
    }

    public function status(Request $request)
    {
        $technicians = User::whereIn('group_id', [1, 2])
            ->where('status', 'active')
            ->get();

        $urgentRepairs = RepairRequest::where('status', 'urgent')
            ->latest()
            ->get();

        // Build the query with search functionality
        $query = RepairRequest::whereNotIn('status', ['completed', 'cancelled', 'pulled_out']);

        // Add search condition if search term is provided
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('ticket_number', 'like', '%' . $searchTerm . '%')
                  ->orWhere('equipment', 'like', '%' . $searchTerm . '%')
                  ->orWhere('building', 'like', '%' . $searchTerm . '%')
                  ->orWhere('floor', 'like', '%' . $searchTerm . '%')
                  ->orWhere('room', 'like', '%' . $searchTerm . '%');
            });
        }

        $requests = $query->latest()->paginate(9)->withQueryString();

        // If it's an AJAX request, return JSON response
        if ($request->ajax()) {
            $view = view('repair-status', compact('requests'))->render();
            return response()->json([
                'html' => $view,
                'success' => true
            ]);
        }

        return view('repair-status', compact(
            'urgentRepairs',
            'requests',
            'technicians'
        ));
    }

    /**
     * Fetch data for a single repair request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRepairRequestData($id)
    {
        \Log::info('Fetching repair request data for ID: ' . $id);
        
        try {
            $repairRequest = RepairRequest::with('technician')->find($id);
            
            if (!$repairRequest) {
                \Log::warning('Repair request not found for ID: ' . $id);
                return response()->json(['message' => 'Repair request not found'], 404);
            }
            
            \Log::info('Repair request data:', ['data' => $repairRequest->toArray()]);
            return response()->json($repairRequest);
        } catch (\Exception $e) {
            \Log::error('Error fetching repair request data: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching repair request data: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        \Log::info('Starting repair request update', [
            'request_id' => $id,
            'request_data' => $request->all()
        ]);

        try {
            // Find the repair request
            $repairRequest = RepairRequest::findOrFail($id);
            \Log::info('Found repair request', ['repair_request' => $repairRequest->toArray()]);
            
            // Prepare update data
            $updateData = $request->only([
                'technician_id',
                'status',
                'issue',
                'remarks',
                'caller_name',
                'findings',
                'technician_signature',
                'caller_signature',
                'serial_number'
            ]);
            \Log::info('Initial update data', ['update_data' => $updateData]);

            // Handle photo upload if present
            if ($request->has('photo') && $request->photo) {
                // Delete old photo if exists
                if ($repairRequest->photo) {
                    Storage::disk('public')->delete($repairRequest->photo);
                }
                $updateData['photo'] = $this->savePhoto($request->photo);
            }

            // Handle time_started for in_progress status
            if ($request->status === 'in_progress' && $request->time_started) {
                $updateData['time_started'] = $request->time_started;
                \Log::info('Added time_started to update data', ['time_started' => $request->time_started]);
            }

            // Handle asset status updates for in_progress requests
            if ($request->status === 'in_progress' && $repairRequest->serial_number) {
                \Log::info('Processing in_progress status for asset', ['serial_number' => $repairRequest->serial_number]);
                
                $asset = Asset::where('serial_number', $repairRequest->serial_number)->first();
                if ($asset) {
                    $oldStatus = $asset->status;
                    \Log::info('Found asset', ['asset' => $asset->toArray(), 'old_status' => $oldStatus]);
                    
                    // Update asset status to UNDER REPAIR if not already
                    if ($asset->status !== 'UNDER REPAIR') {
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
                        \Log::info('Updated asset status to UNDER REPAIR');
                    }
                } else {
                    \Log::warning('Asset not found for serial number', ['serial_number' => $repairRequest->serial_number]);
                }
            }

            // Handle asset status updates for completed requests
            if ($request->status === 'completed') {
                // Get the serial number from the request or existing repair request
                $serialNumber = $request->serial_number ?? $repairRequest->serial_number;
                
                if ($serialNumber) {
                    $asset = Asset::where('serial_number', $serialNumber)->first();
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
                            'remarks' => "Ticket: {$repairRequest->ticket_number}\nIssue: {$repairRequest->issue}\nFindings: {$request->findings}\nRemarks: " . ($request->remarks ?? 'No remarks provided'),
                            'changed_by' => auth()->id()
                        ]);

                        // Create status change record
                        AssetHistory::create([
                            'asset_id' => $asset->id,
                            'change_type' => 'STATUS',
                            'old_value' => 'UNDER REPAIR',
                            'new_value' => 'IN USE',
                            'remarks' => "Asset repair completed. Findings: {$request->findings}",
                            'changed_by' => auth()->id()
                        ]);
                    }
                }
            }

            // Handle cancellation
            if ($request->status === 'cancelled' && $repairRequest->serial_number) {
                $asset = Asset::where('serial_number', $repairRequest->serial_number)->first();
                if ($asset) {
                    // Update asset status to IN USE
                    $asset->update([
                        'status' => 'IN USE'
                    ]);
                    
                    // Create status change record
                    AssetHistory::create([
                        'asset_id' => $asset->id,
                        'change_type' => 'STATUS',
                        'old_value' => 'UNDER REPAIR',
                        'new_value' => 'IN USE',
                        'remarks' => "Repair request cancelled. Ticket: {$repairRequest->ticket_number}\nRemarks: " . ($request->remarks ?? 'No remarks provided'),
                        'changed_by' => auth()->id()
                    ]);
                }
            }

            // Handle asset status updates for pulled out requests
            if ($request->status === 'pulled_out') {
                // Get the serial number from the request or existing repair request
                $serialNumber = $request->serial_number ?? $repairRequest->serial_number;
                
                if ($serialNumber) {
                    $asset = Asset::where('serial_number', $serialNumber)->first();
                    if ($asset) {
                        // Store the old status
                        $oldStatus = $asset->status;
                        
                        // Update asset status to PULLED OUT
                        $asset->update([
                            'status' => 'PULLED OUT'
                        ]);
                        
                        // Create asset history record for status change
                        AssetHistory::create([
                            'asset_id' => $asset->id,
                            'change_type' => 'STATUS',
                            'old_value' => $oldStatus,
                            'new_value' => 'PULLED OUT',
                            'remarks' => "Asset pulled out. Ticket: {$repairRequest->ticket_number}\nFindings: {$request->findings}\nRemarks: " . ($request->remarks ?? 'No remarks provided'),
                            'changed_by' => auth()->id()
                        ]);

                        // Create repair history record
                        AssetHistory::create([
                            'asset_id' => $asset->id,
                            'change_type' => 'REPAIR',
                            'old_value' => $repairRequest->issue ?? 'Not specified',
                            'new_value' => 'pulled_out',
                            'remarks' => "Ticket: {$repairRequest->ticket_number}\nIssue: {$repairRequest->issue}\nFindings: {$request->findings}\nRemarks: " . ($request->remarks ?? 'No remarks provided'),
                            'changed_by' => auth()->id()
                        ]);
                    }
                } else {
                    // Create non-registered asset record for unknown assets
                    NonRegisteredAsset::create([
                        'equipment_name' => $repairRequest->equipment,
                        'location' => $repairRequest->building . ' - ' . $repairRequest->floor . ' - ' . $repairRequest->room,
                        'category' => $repairRequest->category_id ? Category::find($repairRequest->category_id)->name : null,
                        'findings' => $request->findings,
                        'remarks' => $request->remarks,
                        'ticket_number' => $repairRequest->ticket_number,
                        'pulled_out_by' => Auth::user()->name,
                        'pulled_out_at' => now(),
                        'status' => 'PULLED OUT'
                    ]);
                }
            }

            // Set completion/cancellation datetime if applicable
            if (in_array($request->status, ['completed', 'cancelled', 'pulled_out'])) {
                $updateData['completed_at'] = now(); // Use current timestamp
                if ($request->status === 'completed') {
                    $updateData['technician_id'] = auth()->id();
                }
            }
        
            // Perform the update
            \Log::info('Attempting to update repair request', ['final_update_data' => $updateData]);
            $repairRequest->update($updateData);
            \Log::info('Successfully updated repair request');

            // Notify admins about the update if the current user is a secretary or admin
            if (in_array(auth()->user()->group_id, [1, 2])) {
                $admins = User::where('group_id', 1)
                    ->where('id', '!=', auth()->id()) // Don't notify the current admin
                    ->get();

                $action = match($request->status) {
                    'completed' => 'completed',
                    'cancelled' => 'cancelled',
                    'pulled_out' => 'pulled out',
                    'in_progress' => 'started working on',
                    default => 'updated'
                };

                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'type' => 'repair_update',
                        'message' => "Repair request {$repairRequest->ticket_number} has been {$action} by " . auth()->user()->name,
                        'is_read' => false,
                        'link' => url(route('repair.status', [], false))
                    ]);
                }
            }
            
            if ($request->ajax()) {
                \Log::info('Sending AJAX response');
                return response()->json([
                    'success' => true,
                    'message' => $request->status === 'cancelled' ? 'Request cancelled successfully' : 
                               ($request->status === 'in_progress' ? 'Repair started successfully' : 'Request updated successfully'),
                    'request' => $repairRequest
                ]);
            }
            
            $message = $request->status === 'cancelled' ? 'Request cancelled successfully' : 
                      ($request->status === 'in_progress' ? 'Repair started successfully' : 'Request updated successfully');
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Error updating repair request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update request: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Show the repair completion form.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function showCompletionForm($id)
    {
        $repairRequest = RepairRequest::with(['technician', 'asset', 'creator'])->findOrFail($id);
        
        // Check if the user has permission to complete this request
        if (auth()->user()->group_id == 2 && $repairRequest->technician_id != auth()->id()) {
            abort(403, 'You are not authorized to complete this repair request.');
        }

        return view('repair-completion-form', compact('repairRequest'));
    }

    // Add new method to handle asset disposal
    public function disposeAsset(Request $request, $serialNumber)
    {
        $asset = Asset::where('serial_number', $serialNumber)->firstOrFail();
        
        // Get the associated repair request
        $repairRequest = RepairRequest::where('serial_number', $serialNumber)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->latest()
            ->first();
        
        // Update asset status to DISPOSED
        $asset->update([
            'status' => 'DISPOSED'
        ]);
        
        // Create asset history record
        AssetHistory::create([
            'asset_id' => $asset->id,
            'change_type' => 'REPAIR',
            'old_value' => $request->issue ?? 'Not specified',
            'new_value' => $request->status,
            'remarks' => $repairRequest ? 
                "Ticket: {$repairRequest->ticket_number}\nIssue: {$request->issue}\nRemarks: {$request->remarks}" :
                "Issue: {$request->issue}\nRemarks: {$request->remarks}",
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

    public function completed(Request $request)
    {
        $query = RepairRequest::whereIn('status', ['completed', 'cancelled', 'pulled_out'])
            ->with(['technician', 'asset']);

        // Apply filters
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->location) {
            $query->where('location', $request->location);
        }

        // Request date filters
        if ($request->request_start_date) {
            $query->whereDate('created_at', '>=', $request->request_start_date);
        }
        if ($request->request_end_date) {
            $query->whereDate('created_at', '<=', $request->request_end_date);
        }

        // Completion date filters
        if ($request->completion_start_date) {
            $query->whereDate('completed_at', '>=', $request->completion_start_date);
        }
        if ($request->completion_end_date) {
            $query->whereDate('completed_at', '<=', $request->completion_end_date);
        }

        $completedRequests = $query->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Calculate duration for each request
        $completedRequests->getCollection()->transform(function ($request) {
            if ($request->time_started && $request->completed_at) {
                try {
                    $start = \Carbon\Carbon::parse($request->time_started);
                    $end = \Carbon\Carbon::parse($request->completed_at);
                    
                    // Calculate duration in minutes
                    $durationInMinutes = $start->diffInMinutes($end);
                    
                    if($durationInMinutes >= 1440) { // More than 24 hours
                        $days = floor($durationInMinutes / 1440);
                        $remainingMinutes = $durationInMinutes % 1440;
                        $hours = floor($remainingMinutes / 60);
                        $minutes = $remainingMinutes % 60;
                        $request->duration = $days . 'd' . 
                                           ($hours > 0 ? ' ' . $hours . 'hrs' : '') . 
                                           ($minutes > 0 ? ' ' . number_format($minutes, 2) . ' mins' : '');
                    } elseif($durationInMinutes >= 60) { // More than 1 hour
                        $hours = floor($durationInMinutes / 60);
                        $minutes = $durationInMinutes % 60;
                        $request->duration = $hours . 'hrs' . 
                                           ($minutes > 0 ? ' ' . number_format($minutes, 2) . ' mins' : '');
                    } else {
                        $request->duration = number_format($durationInMinutes, 2) . ' mins';
                    }
                } catch (\Exception $e) {
                    \Log::error('Error calculating duration', [
                        'error' => $e->getMessage(),
                        'request_id' => $request->id,
                        'ticket_number' => $request->ticket_number,
                        'time_started' => $request->time_started,
                        'completed_at' => $request->completed_at
                    ]);
                    $request->duration = 'N/A';
                }
            } else {
                $request->duration = 'N/A';
            }
            return $request;
        });

        return view('repair-completed', compact('completedRequests'));
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

    public function show($id)
    {
        return view('repair-details');
    }

    public function getData($id)
    {
        $request = RepairRequest::with(['asset', 'technician'])
            ->findOrFail($id);

        return response()->json($request);
    }

    public function details($id)
    {
        try {
            $repairRequest = RepairRequest::with(['technician', 'asset'])->findOrFail($id);
            
            return view('repair-details', compact('repairRequest'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error fetching repair request details: ' . $e->getMessage());
        }
    }

    /**
     * Show the logged-in user's repair requests and allow evaluation if completed or pulled out.
     */
    public function calls()
    {
        $user = auth()->user();
        $requests = RepairRequest::where('created_by', $user->id)
            ->orderByDesc('created_at')
            ->with(['technician', 'asset', 'evaluation'])
            ->get();

        return view('repair-requests.calls', compact('requests'));
    }

    /**
     * Handle evaluation form submission for a repair request.
     */
    public function evaluate(Request $request, $id)
    {
        $repairRequest = RepairRequest::where('id', $id)
            ->where('created_by', auth()->id())
            ->firstOrFail();

        // Check if the request was created by an admin or technician
        if (in_array($repairRequest->creator->group_id, [1, 2])) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Evaluation is not required for admin/technician-created requests.'
                ], 400);
            }
            return redirect()->back()->with('error', 'Evaluation is not required for admin/technician-created requests.');
        }

        if (!in_array($repairRequest->status, ['completed', 'pulled_out'])) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only evaluate after the repair is completed or pulled out.'
                ], 400);
            }
            return redirect()->back()->with('error', 'You can only evaluate after the repair is completed or pulled out.');
        }

        if ($repairRequest->evaluation) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already evaluated this technician.'
                ], 400);
            }
            return redirect()->back()->with('error', 'You have already evaluated this technician.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string',
            'is_anonymous' => 'nullable|boolean',
        ]);

        $repairRequest->evaluation()->create([
            'repair_request_id' => $repairRequest->id,
            'technician_id' => $repairRequest->technician_id,
            'evaluator_id' => auth()->id(),
            'rating' => $validated['rating'],
            'feedback' => $validated['feedback'] ?? null,
            'is_anonymous' => $request->has('is_anonymous'),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for evaluating your technician!'
            ]);
        }
        return redirect()->back()->with('success', 'Thank you for evaluating your technician!');
    }

    public function completeRepair(Request $request, $id)
    {
        $repairRequest = RepairRequest::findOrFail($id);
        
        // Validate the request
        $validated = $request->validate([
            'findings' => 'required|string',
            'remarks' => 'required|string',
            'technician_signature' => 'required|string',
            'caller_signature' => 'required_if:status,pulled_out',
            'status' => 'required|in:completed,pulled_out'
        ]);

        // Update the repair request
        $repairRequest->update([
            'findings' => $validated['findings'],
            'remarks' => $validated['remarks'],
            'technician_signature' => $validated['technician_signature'],
            'caller_signature' => $validated['caller_signature'] ?? null,
            'status' => $validated['status'],
            'completed_at' => now(),
            'technician_id' => auth()->id()
        ]);

        // If the asset is pulled out and has no serial number, create a non-registered asset record
        if ($validated['status'] === 'pulled_out' && empty($repairRequest->serial_number)) {
            NonRegisteredAsset::create([
                'equipment_name' => $repairRequest->equipment,
                'location' => $repairRequest->building . ' - ' . $repairRequest->floor . ' - ' . $repairRequest->room,
                'category' => $repairRequest->category_id ? Category::find($repairRequest->category_id)->name : null,
                'findings' => $validated['findings'],
                'remarks' => $validated['remarks'],
                'ticket_number' => $repairRequest->ticket_number,
                'pulled_out_by' => Auth::user()->name,
                'pulled_out_at' => now(),
                'status' => 'PULLED OUT'
            ]);
        }

        // If the asset has a serial number, update its status
        if (!empty($repairRequest->serial_number)) {
            $asset = Asset::where('serial_number', $repairRequest->serial_number)->first();
            if ($asset) {
                $asset->update([
                    'status' => $validated['status'] === 'pulled_out' ? 'PULLED OUT' : 'OPERATIONAL',
                    'last_updated_by' => Auth::user()->name,
                    'last_updated_at' => now()
                ]);
            }
        }

        return redirect()->route('repair-requests.show', $repairRequest->id)
            ->with('success', 'Repair request has been completed successfully.');
    }

    /**
     * Show the asset identification page for a repair request.
     */
    public function showIdentifyAssetForm($id)
    {
        $repairRequest = RepairRequest::findOrFail($id);
        if ($repairRequest->serial_number) {
            return redirect('/repair-status');
        }
        $serialNumber = $repairRequest->serial_number;
        return view('repair-identify', compact('repairRequest', 'serialNumber'));
    }

    /**
     * Save the serial number for a repair request and set asset status to UNDER REPAIR.
     */
    public function saveSerialNumber(Request $request, $id)
    {
        $request->validate([
            'serial_number' => 'required|string',
        ]);

        $repairRequest = RepairRequest::findOrFail($id);
        $serialNumber = trim($request->serial_number);
        $repairRequest->serial_number = $serialNumber;
        $repairRequest->save();

        $asset = Asset::where('serial_number', $serialNumber)->first();
        if ($asset && $asset->status !== 'UNDER REPAIR') {
            $oldStatus = $asset->status;
            $asset->update(['status' => 'UNDER REPAIR']);
            AssetHistory::create([
                'asset_id' => $asset->id,
                'change_type' => 'STATUS',
                'old_value' => $oldStatus,
                'new_value' => 'UNDER REPAIR',
                'remarks' => 'Asset status changed to UNDER REPAIR due to repair request asset identification',
                'changed_by' => auth()->id(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Serial number saved and asset status updated to UNDER REPAIR.'
        ]);
    }
}