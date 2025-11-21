<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\RepairRequest;
use App\Models\User;
use App\Models\AssetHistory;  // Add this import
use App\Models\Asset;  // Add this import
use App\Models\Notification;
use App\Models\Building;
use App\Models\Floor;
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
        $buildings = Building::where('is_active', true)->orderBy('name')->get();
        $floors = Floor::with('building')->where('is_active', true)->orderBy('building_id')->orderBy('floor_number')->orderBy('name')->get();
        return view('repair-request', compact('categories', 'technicians', 'buildings', 'floors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_called' => 'required|date',
            'time_called' => 'required',
            'building' => 'required|string|max:255',
            'floor' => 'required|string|max:255',
            'room' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'equipment' => 'required|string',
            'serial_number' => 'nullable|string',
            'issue' => 'required|string',
            'status' => 'required|in:pending,urgent',
            'urgency_level' => 'nullable|integer|min:1|max:3',
            'ongoing_activity' => 'nullable|in:yes,no',
            'technician_id' => 'nullable|exists:users,id',
            'photo' => 'nullable|string|max:14680064' // 10MB base64 string (~10MB * 1.37)
        ]);

        // If user is secretary, force technician_id to be their own ID
        if (auth()->user()->group_id === 2) {
            $request->merge(['technician_id' => auth()->id()]);
        }

        // Determine urgency level automatically if not provided
        $urgencyLevel = $request->urgency_level;
        if (!$urgencyLevel) {
            // Check if there's an ongoing class/event (urgency level 1 - highest)
            if ($request->ongoing_activity === 'yes') {
                $urgencyLevel = 1;
            } else {
                // Check if request is over a week old (urgency level 2)
                $requestDate = \Carbon\Carbon::parse($request->date_called);
                $oneWeekAgo = \Carbon\Carbon::now()->subWeek();
                
                if ($requestDate->lt($oneWeekAgo)) {
                    $urgencyLevel = 2;
                } else {
                    // New request within the week (urgency level 3 - lowest)
                    $urgencyLevel = 3;
                }
            }
        }

        // Set ongoing_activity if not provided
        $ongoingActivity = $request->ongoing_activity ?? 'no';

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
            
            // Check if asset is in a state that prevents repair linking
            if (in_array($asset->status, ['DISPOSED', 'LOST', 'PULLED OUT'])) {
                $statusMessage = match($asset->status) {
                    'DISPOSED' => 'disposed',
                    'LOST' => 'lost',
                    'PULLED OUT' => 'already pulled out for repair',
                    default => 'in an invalid state'
                };
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Error! This asset has been {$statusMessage} and cannot be linked to a repair request.");
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

        // Ensure category_id: default to 'Uncategorized' if not provided
        $categoryId = $request->category_id;
        if (!$categoryId) {
            $uncat = Category::firstOrCreate(['name' => 'Uncategorized']);
            $categoryId = $uncat->id;
        }

        $repairRequest = RepairRequest::create([
            'ticket_number' => $ticketNumber,
            'date_called' => $request->date_called,
            'time_called' => $request->time_called,
            'building' => $request->building,
            'floor' => $request->floor,
            'room' => $request->room,
            'category_id' => $categoryId,
            'equipment' => $request->equipment,
            'serial_number' => $serialNumber,
            'issue' => $request->issue,
            'photo' => $photoPath,
            'status' => $request->status,
            'urgency_level' => $urgencyLevel,
            'ongoing_activity' => $ongoingActivity,
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

        // Auto-update urgency levels for admins (once per session)
        if (auth()->user()->group_id == 1 && !session('urgency_levels_updated')) {
            $this->updateUrgencyLevelsSilently();
            session(['urgency_levels_updated' => true]);
        }

        // Build the query with search functionality
        // Exclude 'in_review' as well since it should appear in completed view
        $query = RepairRequest::whereNotIn('status', ['completed', 'cancelled', 'pulled_out', 'in_review']);

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

        $requests = $query->orderBy('urgency_level', 'asc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(9)->withQueryString();

        // Compute ongoing counts per technician (repairs: not completed/cancelled/pulled_out; maintenance: scheduled)
        $maintenanceOngoing = \App\Models\Maintenance::select('technician_id', DB::raw('COUNT(*) as count'))
            ->where('status', 'scheduled')
            ->whereNotNull('technician_id')
            ->groupBy('technician_id')
            ->pluck('count', 'technician_id');

        $repairOngoing = RepairRequest::select('technician_id', DB::raw('COUNT(*) as count'))
            ->whereNotIn('status', ['completed', 'cancelled', 'pulled_out', 'in_review'])
            ->whereNotNull('technician_id')
            ->groupBy('technician_id')
            ->pluck('count', 'technician_id');

        $technicianOngoingCounts = [];
        $technicianRepairCounts = [];
        $technicianMaintenanceCounts = [];
        foreach ($technicians as $tech) {
            $maintCount = (int) ($maintenanceOngoing[$tech->id] ?? 0);
            $repairCount = (int) ($repairOngoing[$tech->id] ?? 0);
            $technicianOngoingCounts[$tech->id] = $maintCount + $repairCount;
            $technicianRepairCounts[$tech->id] = $repairCount;
            $technicianMaintenanceCounts[$tech->id] = $maintCount;
        }

        // If it's an AJAX request, return JSON response
        if ($request->ajax()) {
            $view = view('repair-status', compact('requests', 'technicians', 'technicianOngoingCounts', 'technicianRepairCounts', 'technicianMaintenanceCounts'))->render();
            return response()->json([
                'html' => $view,
                'success' => true
            ]);
        }

        return view('repair-status', compact(
            'urgentRepairs',
            'requests',
            'technicians',
            'technicianOngoingCounts',
            'technicianRepairCounts',
            'technicianMaintenanceCounts'
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
            
            // Validate photo uploads if present
            if ($request->hasFile('before_photos') || $request->hasFile('after_photos')) {
                $request->validate([
                    'before_photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                    'after_photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                ]);
            }
            
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
                'serial_number',
                'urgency_level',
                'ongoing_activity'
            ]);
            \Log::info('Initial update data', ['update_data' => $updateData]);

            // Handle before/after photo uploads for completion
            if ($request->status === 'completed') {
                $beforePhotos = [];
                $afterPhotos = [];
                
                if ($request->hasFile('before_photos')) {
                    foreach ($request->file('before_photos') as $photo) {
                        $path = $photo->store('repairs/before', 'public');
                        $beforePhotos[] = $path;
                    }
                }
                
                if ($request->hasFile('after_photos')) {
                    foreach ($request->file('after_photos') as $photo) {
                        $path = $photo->store('repairs/after', 'public');
                        $afterPhotos[] = $path;
                    }
                }
                
                $updateData['before_photos'] = $beforePhotos;
                $updateData['after_photos'] = $afterPhotos;
                
                // Handle signature type and delegation
                $callerPresent = $request->input('caller_present') === 'yes';
                
                if ($callerPresent) {
                    // Caller is present - immediate signature
                    if ($request->has('caller_signature')) {
                        $updateData['signature_type'] = 'caller';
                        $updateData['caller_signature'] = $request->caller_signature;
                        $updateData['caller_signed_at'] = now();
                        $updateData['verification_status'] = 'verified';
                    }
                } else {
                    // Caller not present - check for delegate
                    if ($request->has('delegate_name') && $request->filled('delegate_name')) {
                        // Delegate will sign
                        if ($request->has('delegate_signature')) {
                            $updateData['signature_type'] = 'delegate';
                            $updateData['delegate_name'] = $request->delegate_name; // Store delegate name as text
                            $updateData['caller_signature'] = $request->delegate_signature;
                            $updateData['caller_signed_at'] = now();
                            $updateData['verification_status'] = 'verified';
                        }
                    } else {
                        // Deferred signature - caller will sign within 48 hours
                        $updateData['signature_type'] = 'deferred';
                        $updateData['signature_deadline'] = now()->addHours(48);
                        $updateData['verification_status'] = 'pending';
                    }
                }
            }

            // Handle photo upload if present (for issue reporting)
            if ($request->has('photo') && $request->photo) {
                // Delete old photo if exists
                if ($repairRequest->photo) {
                    Storage::disk('public')->delete($repairRequest->photo);
                }
                $updateData['photo'] = $this->savePhoto($request->photo);
            }

            // If urgency_level is explicitly provided, mark as manually overridden
            if ($request->filled('urgency_level')) {
                $updateData['urgency_overridden'] = true;
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

            // Handle asset status updates for completed/pulled_out requests
            if ($request->status === 'completed' || $request->status === 'pulled_out') {
                // Store the original intended status
                $originalStatus = $request->status;
                
                // Check if there's no caller signature - if so, set status to 'in_review'
                if (empty($updateData['caller_signature'])) {
                    $updateData['status'] = 'in_review';
                    // Store original status in remarks so we can restore it later
                    $updateData['remarks'] = ($updateData['remarks'] ?? '') . "\n\n[System Note]: Original completion type: " . $originalStatus;
                    \Log::info('No caller signature provided, changing status to in_review', ['original_status' => $originalStatus]);
                }
                
                // Save current completion attempt to repair_histories before updating
                \App\Models\RepairHistory::create([
                    'repair_request_id' => $repairRequest->id,
                    'technician_id' => auth()->id(),
                    'attempt_number' => ($repairRequest->rework_count ?? 0) + 1,
                    'findings' => $updateData['findings'] ?? null,
                    'remarks' => $updateData['remarks'] ?? null,
                    'before_photos' => $updateData['before_photos'] ?? null,
                    'after_photos' => $updateData['after_photos'] ?? null,
                    'technician_signature' => $updateData['technician_signature'] ?? null,
                    'caller_signature' => $updateData['caller_signature'] ?? null,
                    'time_started' => $repairRequest->time_started,
                    'completed_at' => now(),
                    'caller_signed_at' => $updateData['caller_signed_at'] ?? null,
                    'verification_status' => null, // Will be set when caller signs
                ]);
                
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
                            'old_value' => $repairRequest->ticket_number, // Store ticket number for easy access
                            'new_value' => 'completed',
                            'remarks' => $request->remarks ?? 'Repair completed',
                            'changed_by' => auth()->id()
                        ]);

                        // Create status change record
                        AssetHistory::create([
                            'asset_id' => $asset->id,
                            'change_type' => 'STATUS',
                            'old_value' => 'UNDER REPAIR',
                            'new_value' => 'IN USE',
                            'remarks' => "Asset repair completed",
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
                        'remarks' => "Repair request cancelled",
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
                            'remarks' => "Asset pulled out for repair",
                            'changed_by' => auth()->id()
                        ]);

                        // Create repair history record
                        AssetHistory::create([
                            'asset_id' => $asset->id,
                            'change_type' => 'REPAIR',
                            'old_value' => $repairRequest->ticket_number, // Store ticket number for easy access
                            'new_value' => 'pulled_out',
                            'remarks' => $request->remarks ?? 'Asset pulled out for repair',
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
            $oldTechnicianId = $repairRequest->technician_id;
            $repairRequest->update($updateData);
            \Log::info('Successfully updated repair request');

            // Notify newly assigned technician
            $newTechnicianId = $repairRequest->technician_id;
            if (!empty($newTechnicianId) && $newTechnicianId != $oldTechnicianId) {
                try {
                    Notification::create([
                        'user_id' => $newTechnicianId,
                        'type' => 'repair_assigned',
                        'message' => "New repair request assigned: {$repairRequest->ticket_number} - {$repairRequest->equipment}",
                        'is_read' => false,
                        'link' => url(route('repair.status', [], false))
                    ]);
                    \Log::info('Notified assigned technician', ['technician_id' => $newTechnicianId]);
                } catch (\Exception $e) {
                    \Log::error('Failed to notify assigned technician', ['error' => $e->getMessage()]);
                }
            }

            // Handle notifications for signature delegation (completion)
            if ($request->status === 'completed' && isset($updateData['signature_type'])) {
                try {
                    if ($updateData['signature_type'] === 'deferred') {
                        // Notify caller to sign within 48 hours
                        Notification::create([
                            'user_id' => $repairRequest->created_by,
                            'type' => 'signature_required',
                            'message' => "Repair completed for {$repairRequest->ticket_number}. Please review and sign within 48 hours.",
                            'is_read' => false,
                            'link' => '/repair-requests/pending-signature'
                        ]);
                        \Log::info('Notified caller for deferred signature', ['caller_id' => $repairRequest->created_by]);
                    } elseif (in_array($updateData['signature_type'], ['caller', 'delegate'])) {
                        // Notify caller that repair is completed and signed
                        Notification::create([
                            'user_id' => $repairRequest->created_by,
                            'type' => 'repair_completed',
                            'message' => "Repair for {$repairRequest->ticket_number} has been completed and signed.",
                            'is_read' => false,
                            'link' => "/repair-requests/{$repairRequest->id}"
                        ]);
                        \Log::info('Notified caller of completed repair', ['caller_id' => $repairRequest->created_by]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to create signature notification', ['error' => $e->getMessage()]);
                }
            }

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
        // Calculate statistics from ALL repair requests (not just completed ones)
        $totalRepairs = RepairRequest::count();
        $completedRepairs = RepairRequest::where('status', 'completed')->count();
        $unregisteredItems = RepairRequest::whereIn('status', ['completed', 'cancelled', 'pulled_out'])
            ->where(function($query) {
                $query->whereNull('serial_number')
                      ->orWhere('serial_number', '');
            })
            ->count();

        $query = RepairRequest::whereIn('status', ['completed', 'cancelled', 'pulled_out', 'in_review'])
            ->with(['technician', 'asset']);

        // Apply filters
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->location) {
            // Parse location string (format: "Building-Floor-Room")
            $locationParts = explode('-', $request->location, 3);
            if (count($locationParts) === 3) {
                $building = trim($locationParts[0]);
                $floor = trim($locationParts[1]);
                $room = trim($locationParts[2]);
                
                $query->where('building', $building)
                      ->where('floor', $floor)
                      ->where('room', $room);
            }
        }
        
        // Registration status filter
        if ($request->registration) {
            if ($request->registration === 'registered') {
                $query->where(function($q) {
                    $q->whereNotNull('serial_number')
                      ->where('serial_number', '!=', '');
                });
            } elseif ($request->registration === 'unregistered') {
                $query->where(function($q) {
                    $q->whereNull('serial_number')
                      ->orWhere('serial_number', '');
                });
            }
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

        return view('repair-completed', compact('completedRequests', 'totalRepairs', 'completedRepairs', 'unregisteredItems'));
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

    /**
     * Update urgency levels for all pending repair requests
     * This method can be called via cron job or manually
     */
    public function updateUrgencyLevels()
    {
        $pendingRequests = RepairRequest::whereNotIn('status', ['completed', 'cancelled', 'pulled_out', 'in_review'])->get();
        
        foreach ($pendingRequests as $request) {
            // Skip if urgency was manually overridden
            if ($request->urgency_overridden) {
                continue;
            }
            $urgencyLevel = $this->calculateUrgencyLevel($request);
            
            if ($request->urgency_level !== $urgencyLevel) {
                $request->update(['urgency_level' => $urgencyLevel]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Urgency levels updated successfully'
        ]);
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
        $requestDate = \Carbon\Carbon::parse($request->date_called);
        $oneWeekAgo = \Carbon\Carbon::now()->subWeek();
        
        if ($requestDate->lt($oneWeekAgo)) {
            return 2;
        }
        
        // New request within the week (urgency level 3 - lowest)
        return 3;
    }

    /**
     * Silently update urgency levels without user notification
     */
    private function updateUrgencyLevelsSilently()
    {
        $pendingRequests = RepairRequest::whereNotIn('status', ['completed', 'cancelled', 'pulled_out', 'in_review'])->get();
        
        foreach ($pendingRequests as $request) {
            // Skip if urgency was manually overridden
            if ($request->urgency_overridden) {
                continue;
            }
            $urgencyLevel = $this->calculateUrgencyLevel($request);
            
            if ($request->urgency_level !== $urgencyLevel) {
                $request->update(['urgency_level' => $urgencyLevel]);
            }
        }
    }

    /**
     * Reset the urgency levels session flag to allow re-updating
     */
    public function resetUrgencySession()
    {
        session()->forget('urgency_levels_updated');
        return response()->json(['success' => true, 'message' => 'Session reset successfully']);
    }

    public function previewPDF(Request $request)
    {
        try {
            $query = RepairRequest::whereIn('status', ['completed', 'cancelled', 'pulled_out'])
                ->with(['technician', 'asset']);

            // Apply status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Apply location filter
            if ($request->filled('location')) {
                // Parse location string (format: "Building-Floor-Room")
                $locationParts = explode('-', $request->location, 3);
                if (count($locationParts) === 3) {
                    $building = trim($locationParts[0]);
                    $floor = trim($locationParts[1]);
                    $room = trim($locationParts[2]);
                    
                    $query->where('building', $building)
                          ->where('floor', $floor)
                          ->where('room', $room);
                }
            }

            // Apply request date filters
            if ($request->filled('request_start_date')) {
                $query->whereDate('created_at', '>=', $request->request_start_date);
            }
            if ($request->filled('request_end_date')) {
                $query->whereDate('created_at', '<=', $request->request_end_date);
            }

            // Apply completion date filters
            if ($request->filled('completion_start_date')) {
                $query->whereDate('completed_at', '>=', $request->completion_start_date);
            }
            if ($request->filled('completion_end_date')) {
                $query->whereDate('completed_at', '<=', $request->completion_end_date);
            }

            $completedRequests = $query->orderBy('created_at', 'desc')->get();

            // Generate PDF and stream it (for preview)
            $pdf = PDF::loadView('pdf.repair-completed', compact('completedRequests'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->stream('repair-history-preview.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to preview PDF: ' . $e->getMessage());
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            $query = RepairRequest::whereIn('status', ['completed', 'cancelled', 'pulled_out'])
                ->with(['technician', 'asset']);

            // Apply status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Apply location filter
            if ($request->filled('location')) {
                // Parse location string (format: "Building-Floor-Room")
                $locationParts = explode('-', $request->location, 3);
                if (count($locationParts) === 3) {
                    $building = trim($locationParts[0]);
                    $floor = trim($locationParts[1]);
                    $room = trim($locationParts[2]);
                    
                    $query->where('building', $building)
                          ->where('floor', $floor)
                          ->where('room', $room);
                }
            }

            // Apply request date filters
            if ($request->filled('request_start_date')) {
                $query->whereDate('created_at', '>=', $request->request_start_date);
            }
            if ($request->filled('request_end_date')) {
                $query->whereDate('created_at', '<=', $request->request_end_date);
            }

            // Apply completion date filters
            if ($request->filled('completion_start_date')) {
                $query->whereDate('completed_at', '>=', $request->completion_start_date);
            }
            if ($request->filled('completion_end_date')) {
                $query->whereDate('completed_at', '<=', $request->completion_end_date);
            }

            $completedRequests = $query->orderBy('created_at', 'desc')->get();

            // Process signatures if provided
            $signatures = [];
            if ($request->has('signatures')) {
                $signaturesData = json_decode($request->signatures, true);
                if (is_array($signaturesData)) {
                    foreach ($signaturesData as $signature) {
                        if (isset($signature['label'], $signature['name'], $signature['signature'])) {
                            $signatures[] = [
                                'label' => $signature['label'],
                                'name' => $signature['name'],
                                'signature_base64' => $signature['signature']
                            ];
                        }
                    }
                }
            }

            // Generate PDF and download it
            $pdf = PDF::loadView('pdf.repair-completed', compact('completedRequests', 'signatures'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->stream('repair-history-report.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to export PDF: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new CompletedRepairsExport($request->all()), 'completed-repairs.xlsx');
    }

    public function show($id)
    {
        $repairRequest = RepairRequest::findOrFail($id);
        
        // Access control: Secretaries/Technicians (group_id=2) can only view requests assigned to them
        if (auth()->user()->group_id == 2 && $repairRequest->technician_id != auth()->id()) {
            abort(403, 'You can only view repair requests assigned to you.');
        }
        
        return view('repair-details');
    }

    public function getData($id)
    {
        $request = RepairRequest::with(['asset.location', 'technician', 'creator', 'evaluation', 'histories.technician'])
            ->findOrFail($id);
            
        // Access control: Secretaries/Technicians (group_id=2) can only view requests assigned to them
        if (auth()->user()->group_id == 2 && $request->technician_id != auth()->id()) {
            return response()->json(['error' => 'You can only view repair requests assigned to you.'], 403);
        }

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

        // Require caller signature verification before evaluation
        if ($repairRequest->verification_status !== 'verified') {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please submit and verify the caller signature before submitting an evaluation.'
                ], 400);
            }
            return redirect()->back()->with('error', 'Please submit and verify the caller signature before submitting an evaluation.');
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
        ]);

        $repairRequest->evaluation()->create([
            'repair_request_id' => $repairRequest->id,
            'technician_id' => $repairRequest->technician_id,
            'evaluator_id' => auth()->id(),
            'rating' => $validated['rating'],
            'feedback' => $validated['feedback'] ?? null,
            'is_anonymous' => false,
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

        // Determine the final status based on caller signature
        $finalStatus = $validated['status'];
        if ($validated['status'] === 'completed' && empty($validated['caller_signature'])) {
            // If no caller signature, set status to 'in_review' instead of 'completed'
            $finalStatus = 'in_review';
        }

        // Update the repair request
        $repairRequest->update([
            'findings' => $validated['findings'],
            'remarks' => $validated['remarks'],
            'technician_signature' => $validated['technician_signature'],
            'caller_signature' => $validated['caller_signature'] ?? null,
            'status' => $finalStatus,
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
            return redirect('/repair-status')->with('error', 'Asset has already been identified for this repair request. Serial Number: ' . $repairRequest->serial_number);
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

    // Show pending signatures for caller
    public function pendingSignatures()
    {
        $user = auth()->user();
        // Defensive: only requesters (group_id = 3) may access this page; return 403 to avoid redirect loops
        if (!$user || (int)$user->group_id !== 3) {
            abort(403, 'Unauthorized');
        }
        
        // Get repair requests where user is the creator and signature is pending
        // This includes 'in_review' status AND legacy 'pulled_out'/'completed' without signature
        $repairRequests = RepairRequest::where('created_by', $user->id)
            ->where(function($query) {
                $query->where('status', 'in_review')
                      ->orWhere(function($q) {
                          $q->whereIn('status', ['completed', 'pulled_out'])
                            ->whereNull('caller_signature')
                            ->whereNotNull('completed_at');
                      });
            })
            ->whereNull('caller_signature')
            ->whereNotNull('completed_at')
            ->with(['technician', 'asset'])
            ->orderBy('completed_at', 'asc')
            ->get();

        return view('repair-pending-signature', compact('repairRequests'));
    }

    // Submit deferred signature
    public function submitSignature(Request $request, $id)
    {
        $request->validate([
            'caller_signature' => 'required|string',
            'action' => 'required|in:approve,rework'
        ]);

        try {
            $repairRequest = RepairRequest::findOrFail($id);
            
            // Verify user is authorized (creator)
            if ($repairRequest->created_by !== auth()->id()) {
                return redirect()->route('repair.pending-signature')
                    ->with('error', 'You are not authorized to sign this repair request');
            }

            if ($request->action === 'approve') {
                // Determine the final status based on original completion type
                $finalStatus = 'completed'; // default
                if (strpos($repairRequest->remarks, 'Original completion type: pulled_out') !== false) {
                    $finalStatus = 'pulled_out';
                }
                
                // Caller approves the work
                $repairRequest->update([
                    'caller_signature' => $request->caller_signature,
                    'caller_signed_at' => now(),
                    'verification_status' => 'verified',
                    'status' => $finalStatus // Restore original status (completed or pulled_out)
                ]);

                // Update the latest repair history record with approval
                $latestHistory = \App\Models\RepairHistory::where('repair_request_id', $repairRequest->id)
                    ->orderBy('attempt_number', 'desc')
                    ->first();
                    
                if ($latestHistory) {
                    $latestHistory->update([
                        'caller_signature' => $request->caller_signature,
                        'caller_signed_at' => now(),
                        'verification_status' => 'approved'
                    ]);
                }

                // Notify technician
                Notification::create([
                    'user_id' => $repairRequest->technician_id,
                    'type' => 'signature_approved',
                    'message' => "Your repair work for {$repairRequest->ticket_number} has been approved by the caller.",
                    'is_read' => false,
                    'link' => "/repair-requests/{$repairRequest->id}"
                ]);

                return redirect()->route('repair.pending-signature')
                    ->with('success', 'Signature submitted successfully. Work has been verified.');
            } else {
                // Caller requests rework - mark as disputed and return to in_progress
                $repairRequest->rework_count = (int)($repairRequest->rework_count ?? 0) + 1;
                $repairRequest->caller_signature = $request->caller_signature;
                $repairRequest->caller_signed_at = now();
                $repairRequest->verification_status = 'disputed';
                $repairRequest->status = 'in_progress';
                $repairRequest->completed_at = null; // clear completion since rework is requested
                $repairRequest->remarks = ($repairRequest->remarks ?? '') . "\n\n[Caller Feedback]: " . ($request->rework_notes ?? 'Caller requested rework after review.');
                
                // Update the latest repair history record with dispute
                $latestHistory = \App\Models\RepairHistory::where('repair_request_id', $repairRequest->id)
                    ->orderBy('attempt_number', 'desc')
                    ->first();
                    
                if ($latestHistory) {
                    $latestHistory->update([
                        'caller_signature' => $request->caller_signature,
                        'caller_signed_at' => now(),
                        'verification_status' => 'disputed',
                        'caller_feedback' => $request->rework_notes ?? 'Caller requested rework after review.'
                    ]);
                }
                $repairRequest->save();

                // Notify technician
                Notification::create([
                    'user_id' => $repairRequest->technician_id,
                    'type' => 'rework_requested',
                    'message' => "Rework requested for repair {$repairRequest->ticket_number}. Please review caller feedback.",
                    'is_read' => false,
                    'link' => "/repair-requests/{$repairRequest->id}"
                ]);

                return redirect()->route('repair.pending-signature')
                    ->with('success', 'Rework request submitted. Technician has been notified.');
            }

        } catch (\Exception $e) {
            return redirect()->route('repair.pending-signature')
                ->with('error', 'Failed to submit signature: ' . $e->getMessage());
        }
    }

    /**
     * Send reminder to user for pending signature
     */
    public function sendReminder($id)
    {
        try {
            $request = RepairRequest::findOrFail($id);

            // Check if request is in_review and has no signature
            if ($request->status !== 'in_review' || $request->caller_signature) {
                return response()->json([
                    'success' => false,
                    'message' => 'This request does not need a reminder'
                ], 400);
            }

            // Check if reminder has already been sent
            if ($request->reminder_sent_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'A reminder has already been sent for this request on ' . 
                                $request->reminder_sent_at->format('M j, Y g:i A')
                ], 400);
            }

            // Update reminder sent timestamp
            $request->update([
                'reminder_sent_at' => now()
            ]);

            // Create notification for the requester
            Notification::create([
                'user_id' => $request->created_by,
                'type' => 'signature_reminder',
                'message' => "Reminder: Please sign the completed repair request {$request->ticket_number}. It has been pending for over 48 hours.",
                'is_read' => false,
                'link' => "/repair-requests/pending-signature"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reminder sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending reminder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auto-approve a repair request after 72 hours
     */
    public function autoApprove($id)
    {
        try {
            $request = RepairRequest::findOrFail($id);

            // Check if request is in_review and has no signature
            if ($request->status !== 'in_review' || $request->caller_signature) {
                return response()->json([
                    'success' => false,
                    'message' => 'This request cannot be auto-approved'
                ], 400);
            }

            // Check if it's been at least 72 hours
            $completedAt = \Carbon\Carbon::parse($request->completed_at);
            $hoursSinceCompletion = $completedAt->diffInHours(now());

            if ($hoursSinceCompletion < 72) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request can only be auto-approved after 72 hours'
                ], 400);
            }

            // Determine the final status based on original completion type
            $finalStatus = 'completed'; // default
            if (strpos($request->remarks, 'Original completion type: pulled_out') !== false) {
                $finalStatus = 'pulled_out';
            }
            
            // Update verification status and change status to original completion type
            $request->update([
                'verification_status' => 'verified',
                'status' => $finalStatus, // Restore original status (completed or pulled_out)
                'remarks' => ($request->remarks ?? '') . "\n\n[Auto-Approved]: No response after 72 hours. Auto-approved by system on " . now()->format('M j, Y g:i A')
            ]);

            // Notify technician
            Notification::create([
                'user_id' => $request->technician_id,
                'type' => 'auto_approved',
                'message' => "Your repair work for {$request->ticket_number} has been auto-approved after 72 hours of no response.",
                'is_read' => false,
                'link' => "/repair-requests/{$request->id}"
            ]);

            // Notify requester
            Notification::create([
                'user_id' => $request->created_by,
                'type' => 'auto_approved',
                'message' => "Repair request {$request->ticket_number} has been auto-approved due to no response within 72 hours.",
                'is_read' => false,
                'link' => "/repair-requests/{$request->id}"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request auto-approved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error auto-approving request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send reminder to all eligible requests (48+ hours, no reminder sent yet)
     */
    public function sendReminderToAll()
    {
        try {
            // Find all in_review requests without signature, 48+ hours old, no reminder sent
            $eligibleRequests = RepairRequest::where('status', 'in_review')
                ->whereNull('caller_signature')
                ->whereNull('reminder_sent_at')
                ->whereNotNull('completed_at')
                ->where('completed_at', '<=', now()->subHours(48))
                ->get();

            if ($eligibleRequests->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No eligible requests found (must be 48+ hours old without reminder sent)'
                ], 400);
            }

            $count = 0;
            foreach ($eligibleRequests as $request) {
                // Update reminder sent timestamp
                $request->update(['reminder_sent_at' => now()]);

                // Create notification
                Notification::create([
                    'user_id' => $request->created_by,
                    'type' => 'signature_reminder',
                    'message' => "Reminder: Please sign the completed repair request {$request->ticket_number}. It has been pending for over 48 hours.",
                    'is_read' => false,
                    'link' => "/repair-requests/pending-signature"
                ]);

                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully sent reminders to {$count} user(s)"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending reminders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auto-approve all eligible requests (72+ hours old)
     */
    public function autoApproveAll()
    {
        try {
            // Find all in_review requests without signature, 72+ hours old
            $eligibleRequests = RepairRequest::where('status', 'in_review')
                ->whereNull('caller_signature')
                ->whereNotNull('completed_at')
                ->where('completed_at', '<=', now()->subHours(72))
                ->get();

            if ($eligibleRequests->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No eligible requests found (must be 72+ hours old without signature)'
                ], 400);
            }

            $count = 0;
            foreach ($eligibleRequests as $request) {
                // Determine the final status based on original completion type
                $finalStatus = 'completed'; // default
                if (strpos($request->remarks, 'Original completion type: pulled_out') !== false) {
                    $finalStatus = 'pulled_out';
                }
                
                // Update verification status and change status to original completion type
                $request->update([
                    'verification_status' => 'verified',
                    'status' => $finalStatus, // Restore original status (completed or pulled_out)
                    'remarks' => ($request->remarks ?? '') . "\n\n[Auto-Approved]: No response after 72 hours. Auto-approved by admin on " . now()->format('M j, Y g:i A')
                ]);

                // Notify technician
                Notification::create([
                    'user_id' => $request->technician_id,
                    'type' => 'auto_approved',
                    'message' => "Your repair work for {$request->ticket_number} has been auto-approved after 72 hours of no response.",
                    'is_read' => false,
                    'link' => "/repair-requests/{$request->id}"
                ]);

                // Notify requester
                Notification::create([
                    'user_id' => $request->created_by,
                    'type' => 'auto_approved',
                    'message' => "Repair request {$request->ticket_number} has been auto-approved due to no response within 72 hours.",
                    'is_read' => false,
                    'link' => "/repair-requests/{$request->id}"
                ]);

                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully auto-approved {$count} request(s)"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error auto-approving requests: ' . $e->getMessage()
            ], 500);
        }
    }
}