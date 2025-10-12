<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Maintenance;
use App\Models\User;
use App\Models\Asset;
use App\Models\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\RepairRequest;

class MaintenanceController extends Controller
{
    public function addNewTask(Request $request)
    {
        $request->validate([
            'task' => 'required|string|max:255|unique:maintenance_tasks,name'
        ]);

        try {
            // Create a new maintenance task
            DB::table('maintenance_tasks')->insert([
                'name' => $request->task,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task added successfully',
                'task' => $request->task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Modify the schedule method to fetch tasks from the database
    public function schedule()
    {
        $technicians = User::whereIn('group_id', [1, 2])
            ->where('status', 'active')
            ->get();

        // Get all available locations
        $locations = \App\Models\Location::all()->mapWithKeys(function ($location) {
            return [$location->id => $location->building . ' - Floor ' . $location->floor . ' - Room ' . $location->room_number];
        });

        // Fetch tasks from the database
        $maintenanceTasks = DB::table('maintenance_tasks')
            ->orderBy('created_at', 'desc')
            ->pluck('name')
            ->toArray();

        // Compute ongoing counts per technician (repairs: not completed/cancelled/pulled_out; maintenance: scheduled)
        $maintenanceOngoing = Maintenance::select('technician_id', DB::raw('COUNT(*) as count'))
            ->where('status', 'scheduled')
            ->whereNotNull('technician_id')
            ->groupBy('technician_id')
            ->pluck('count', 'technician_id');

        $repairOngoing = RepairRequest::select('technician_id', DB::raw('COUNT(*) as count'))
            ->whereNotIn('status', ['completed', 'cancelled', 'pulled_out'])
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

        return view('maintenance-schedule', compact(
            'technicians',
            'locations',
            'maintenanceTasks',
            'technicianOngoingCounts',
            'technicianRepairCounts',
            'technicianMaintenanceCounts'
        ));
    }
    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'maintenance_tasks' => 'required|array|min:1',
            'technician_id' => 'required',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'target_date' => 'required|date|after_or_equal:scheduled_date',
            'excluded_assets' => 'nullable|string',  // For JSON string
        ]);

        try {
            // Get location for notification message
            $location = \App\Models\Location::findOrFail($request->location_id);
            $locationName = $location->building . ' - Floor ' . $location->floor . ' - Room ' . $location->room_number;
            
            // Decode the excluded assets JSON string
            $excludedAssets = $request->excluded_assets ? json_decode($request->excluded_assets, true) : [];

            // Create separate maintenance records for each task
            foreach ($request->maintenance_tasks as $task) {
                $maintenance = Maintenance::create([
                    'location_id' => $request->location_id,
                    'maintenance_task' => $task,
                    'technician_id' => $request->technician_id,
                    'scheduled_date' => $request->scheduled_date,
                    'target_date' => $request->target_date,
                    'status' => 'scheduled',
                    'excluded_assets' => $excludedAssets
                ]);

                // Create notification for assigned technician
                Notification::create([
                    'user_id' => $request->technician_id,
                    'type' => 'maintenance_assigned',
                    'message' => "You have been assigned maintenance task: {$task} in {$locationName}",
                    'is_read' => false,
                    'link' => '/maintenance/upcoming'
                ]);
            }

            return redirect()->route('maintenance.upcoming')
                ->with('success', 'Maintenance tasks scheduled successfully');
        } catch (\Exception $e) {
            return redirect()->route('maintenance.schedule')
                ->withErrors(['error' => 'Failed to schedule maintenance tasks: ' . $e->getMessage()]);
        }
    }

    public function upcoming(Request $request)
    {
        $query = Maintenance::whereIn('status', ['scheduled', 'needs_rework']);

        // If user is secretary (group_id = 2), only show their assigned tasks
        if (auth()->user()->group_id === 2) {
            $query->where('technician_id', auth()->id());
        }

        $maintenances = $query->with(['location', 'technician'])
            ->orderBy('location_id')
            ->orderBy('scheduled_date')
            ->get();

        // Load excluded assets for each maintenance record
        foreach ($maintenances as $maintenance) {
            if (!empty($maintenance->excluded_assets)) {
                $maintenance->excludedAssetModels = Asset::whereIn('id', $maintenance->excluded_assets)
                    ->select('id', 'serial_number', 'name')
                    ->get();
            } else {
                $maintenance->excludedAssetModels = collect();
            }
        }

        // Get the maintenance_id from query parameter if provided
        $highlightMaintenanceId = $request->get('maintenance_id');

        return view('maintenance-upcoming', compact('maintenances', 'highlightMaintenanceId'));
    }

    public function complete($id)
    {
        $maintenance = Maintenance::findOrFail($id);

        // Check if user is secretary and is assigned to this task
        if (auth()->user()->group_id === 2 && $maintenance->technician_id !== auth()->id()) {
            return redirect()->route('maintenance.upcoming')
                ->with('error', 'You are not authorized to complete this maintenance task');
        }
    
        // Debug incoming request data
        \Log::info('Complete maintenance request data:', request()->all());
    
        $updateData = [
            'status' => 'pending_approval',
            'approval_status' => 'pending_approval',
            'action_by_id' => auth()->id(),
            'completed_at' => now(),
            'notes' => request('notes'),
            // Reset approval fields when resubmitting (for rework cases)
            'approved_by_id' => null,
            'approved_at' => null,
            'admin_signature' => null,
            'admin_notes' => null,
            'quality_issues' => [],
            'requires_rework' => false,
            'rework_instructions' => null
        ];
    
        // Handle asset issues if they exist in the request
        if (request()->has('has_issues') && request()->has_issues == 1) {
            $assetIssues = [];
            $serialNumbers = [];
            
            // Handle main issue
            if (request()->has('issue_description')) {
                $mainSerialNumber = request()->input('serial_number');
                $serialNumbers[] = $mainSerialNumber;
                $assetIssues[] = [
                    'issue_description' => request()->input('issue_description')
                ];
            }
            
            // Handle additional issues
            $additionalSerialNumbers = request()->input('additional_serial_number', []);
            $additionalIssueDescriptions = request()->input('additional_issue_description', []);
            
            foreach ($additionalSerialNumbers as $key => $serialNumber) {
                if (isset($additionalIssueDescriptions[$key])) {
                    $serialNumbers[] = $serialNumber;
                    $assetIssues[] = [
                        'issue_description' => $additionalIssueDescriptions[$key]
                    ];
                }
            }
            
            $updateData['asset_issues'] = $assetIssues;
            $updateData['serial_number'] = json_encode($serialNumbers); // Store all serial numbers as JSON
            } else {
            // If no issues, set empty arrays for asset_issues and serial_number
            $updateData['asset_issues'] = [];
            $updateData['serial_number'] = json_encode([]);
        }
    
        try {
            $maintenance->update($updateData);
            \Log::info('Maintenance updated successfully:', ['maintenance_id' => $id]);
    
            return redirect()->route('maintenance.upcoming')->with('success', 'Maintenance submitted for approval');
        } catch (\Exception $e) {
            \Log::error('Error updating maintenance:', ['error' => $e->getMessage()]);
            return redirect()->route('maintenance.upcoming')->with('error', 'Failed to update maintenance: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $maintenance = Maintenance::findOrFail($id);

            // Check if user is secretary and is assigned to this task
            if (auth()->user()->group_id === 2 && $maintenance->technician_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to delete this maintenance task'
                ], 403);
            }

            // Delete the record from the database
            $maintenance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Maintenance deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting maintenance: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history(Request $request)
    {
        $query = Maintenance::with(['location', 'technician', 'actionBy', 'approvedBy'])
            ->whereIn('status', ['completed', 'cancelled'])
            ->orderBy('scheduled_date', 'desc');

        // Apply filters
        if ($request->filled('lab')) {
            $query->where('lab_number', $request->lab);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->where('scheduled_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('scheduled_date', '<=', $request->end_date);
        }

        if ($request->filled('issue')) {
            if ($request->issue === 'with_issues') {
                $query->whereNotNull('asset_issues')->where('asset_issues', '!=', '[]');
            } elseif ($request->issue === 'no_issues') {
                $query->where(function($q) {
                    $q->whereNull('asset_issues')->orWhere('asset_issues', '[]');
                });
            }
        }

        $maintenances = $query->paginate(15);

        return view('maintenance-history', compact('maintenances'));
    }

    public function show($id)
    {
        $maintenance = Maintenance::with(['location', 'technician', 'actionBy'])
            ->findOrFail($id);

        return view('maintenance.show', compact('maintenance'));
    }

    public function edit($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $technicians = User::whereHas('group', function ($query) {
            $query->whereNotIn('name', ['Users']);
        })->get();

        // Use Laboratory model instead of hardcoded data
        $labs = \App\Models\Laboratory::orderBy('number')->pluck('name', 'number')->toArray();

        $maintenanceTasks = [
            'Format and Software Installation',
            'Physical Checking',
            'Windows Update',
            'General Cleaning',
            'Antivirus Update',
            'Scan for Virus',
            'Disk Cleanup',
            'Cleaning',
            'Disk Maintenance'
        ];

        return view('maintenance-edit', compact('maintenance', 'technicians', 'labs', 'maintenanceTasks'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lab_number' => 'required',
            'maintenance_task' => 'required',
            'technician_id' => 'required',
            'scheduled_date' => 'required|date|after_or_equal:today',
        ]);

        $maintenance = Maintenance::findOrFail($id);
        $maintenance->update([
            'lab_number' => $request->lab_number,
            'maintenance_task' => $request->maintenance_task,
            'technician_id' => $request->technician_id,
            'scheduled_date' => $request->scheduled_date
        ]);

        return redirect()->route('maintenance.upcoming')->with('success', 'Maintenance updated successfully');
    }

    public function editByDate($locationId, $date)
    {
        $maintenances = Maintenance::where('location_id', $locationId)
            ->whereDate('scheduled_date', $date)
            ->get();

        // Get users who are not in the 'Users' group
        $technicians = User::whereHas('group', function ($query) {
            $query->where('name', '!=', 'Users');
        })->get();

        // Use the same maintenance tasks as in schedule method
        $maintenanceTasks = [
            'Format and Software Installation',
            'Physical Checking',
            'Windows Update',
            'General Cleaning',
            'Antivirus Update',
            'Scan for Virus',
            'Disk Cleanup',
            'Cleaning',
            'Disk Maintenance'
        ];

        return view('maintenance-edit-lab', [
            'locationId' => $locationId,
            'date' => $date,
            'maintenances' => $maintenances,
            'technicians' => $technicians,
            'maintenanceTasks' => $maintenanceTasks
        ]);
    }

    public function updateByDate(Request $request, $locationId, $date)
    {
        foreach ($request->maintenances as $maintenanceData) {
            Maintenance::find($maintenanceData['id'])->update([
                'maintenance_task' => $maintenanceData['maintenance_task'],
                'technician_id' => $maintenanceData['technician_id']
            ]);
        }

        return redirect()->route('maintenance.upcoming')
            ->with('success', 'Maintenance schedules updated successfully');
    }

    public function editLab($labNumber)
    {
        $maintenances = Maintenance::where('lab_number', $labNumber)
            ->where('status', 'scheduled')
            ->orderBy('scheduled_date')
            ->get();

        $technicians = User::whereHas('group', function ($query) {
            $query->whereNotIn('name', ['Users']);
        })->get();

        $maintenanceTasks = [
            'Format and Software Installation',
            'Physical Checking',
            'Windows Update',
            'General Cleaning',
            'Antivirus Update',
            'Scan for Virus',
            'Disk Cleanup',
            'Cleaning',
            'Disk Maintenance'
        ];

        return view('maintenance-edit-lab', compact('maintenances', 'technicians', 'maintenanceTasks', 'labNumber'));
    }

    public function updateLab(Request $request, $labNumber)
    {
        $request->validate([
            'maintenances' => 'required|array',
            'maintenances.*.id' => 'required|exists:maintenances,id',
            'maintenances.*.maintenance_task' => 'required',
            'maintenances.*.technician_id' => 'required',
            'maintenances.*.scheduled_date' => 'required|date|after_or_equal:today',
        ]);

        foreach ($request->maintenances as $data) {
            $maintenance = Maintenance::findOrFail($data['id']);
            $maintenance->update([
                'maintenance_task' => $data['maintenance_task'],
                'technician_id' => $data['technician_id'],
                'scheduled_date' => $data['scheduled_date']
            ]);
        }

        return redirect()->route('maintenance.upcoming')->with('success', 'Laboratory maintenance schedule updated successfully');
    }

    public function addTask(Request $request, $locationId, $date)
    {
        $maintenance = new Maintenance([
            'location_id' => $locationId,
            'scheduled_date' => $date,
            'maintenance_task' => $request->maintenance_task,
            'technician_id' => $request->technician_id,
            'status' => 'scheduled'  // Changed from 'pending' to 'scheduled'
        ]);

        $maintenance->save();

        return redirect()->back()->with('success', 'Maintenance task added successfully');
    }

    public function deleteTask(Maintenance $maintenance)
    {
        $maintenance->delete();
        return redirect()->back()->with('success', 'Maintenance task deleted successfully');
    }

    public function completeAllByDate($locationId, $date)
    {
        try {
            $maintenances = Maintenance::where('location_id', $locationId)
                ->whereDate('scheduled_date', $date)
                ->whereIn('status', ['scheduled', 'needs_rework'])
                ->get();

            // Handle asset issues if they exist in the request
            if (request()->has('has_issues') && request()->has_issues == 1) {
                $assetIssues = [];
                $serialNumbers = [];
                
                // Handle main issue
                if (request()->has('issue_description')) {
                    $mainSerialNumber = request()->input('serial_number');
                    $serialNumbers[] = $mainSerialNumber;
                    $assetIssues[] = [
                        'issue_description' => request()->input('issue_description')
                    ];
                }
                
                // Handle additional issues
                $additionalSerialNumbers = request()->input('additional_serial_number', []);
                $additionalIssueDescriptions = request()->input('additional_issue_description', []);
                
                foreach ($additionalSerialNumbers as $key => $serialNumber) {
                    if (isset($additionalIssueDescriptions[$key])) {
                        $serialNumbers[] = $serialNumber;
                        $assetIssues[] = [
                            'issue_description' => $additionalIssueDescriptions[$key]
                        ];
                    }
                }
            }

            foreach ($maintenances as $maintenance) {
                $updateData = [
                    'status' => 'pending_approval',
                    'approval_status' => 'pending_approval',
                    'action_by_id' => auth()->id(),
                    'completed_at' => now(),
                    // Reset approval fields when resubmitting (for rework cases)
                    'approved_by_id' => null,
                    'approved_at' => null,
                    'admin_signature' => null,
                    'admin_notes' => null,
                    'quality_issues' => [],
                    'requires_rework' => false,
                    'rework_instructions' => null
                ];

                // Add asset issues data if it exists
                if (isset($assetIssues)) {
                    $updateData['asset_issues'] = $assetIssues;
                    $updateData['serial_number'] = json_encode($serialNumbers); // Store all serial numbers as JSON
                }

                $maintenance->update($updateData);
            }

            return redirect()->route('maintenance.upcoming')
                ->with('success', 'All maintenance tasks submitted for approval');
        } catch (\Exception $e) {
            return redirect()->route('maintenance.upcoming')
                ->with('error', 'Failed to complete maintenance tasks');
        }
    }

    public function cancelAllByDate($locationId, $date)
    {
        try {
            $maintenances = Maintenance::where('location_id', $locationId)
                ->whereDate('scheduled_date', $date)
                ->where('status', 'scheduled')
                ->get();

            foreach ($maintenances as $maintenance) {
                $maintenance->update([
                    'status' => 'cancelled',
                    'action_by_id' => auth()->id(),
                    'completed_at' => null,
                    'notes' => request('notes')
                ]);
            }

            return redirect()->route('maintenance.upcoming')
                ->with('success', 'All maintenance tasks cancelled successfully');
        } catch (\Exception $e) {
            return redirect()->route('maintenance.upcoming')
                ->with('error', 'Failed to cancel maintenance tasks');
        }
    }

    public function previewPDF(Request $request)
    {
        try {
            // Get the same filtered data as the history page
            $query = Maintenance::with(['technician', 'actionBy', 'location'])
                ->whereIn('status', ['completed', 'cancelled'])
                ->orderBy('scheduled_date', 'desc');

            // Apply filters if they exist
            if ($request->has('start_date') && $request->start_date) {
                $query->whereDate('scheduled_date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date) {
                $query->whereDate('scheduled_date', '<=', $request->end_date);
            }

            if ($request->filled('lab')) {
                $query->whereHas('location', function($q) use ($request) {
                    $q->where('room_number', $request->lab);
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('issue')) {
                if ($request->issue === 'with_issues') {
                    $query->whereNotNull('asset_issues')->where('asset_issues', '!=', '[]');
                } elseif ($request->issue === 'no_issues') {
                    $query->where(function($q) {
                        $q->whereNull('asset_issues')->orWhere('asset_issues', '[]');
                    });
                }
            }

            $maintenances = $query->get();

            // Transform data for PDF template
            $maintenances = $maintenances->map(function($maintenance) {
                $maintenance->lab_number = $maintenance->location ? $maintenance->location->room_number : 'N/A';
                // The maintenance_task field is already correct, no transformation needed
                return $maintenance;
            });

            // Generate PDF for preview (stream instead of download)
            $pdf = Pdf::loadView('exports.maintenance-history-pdf', compact('maintenances'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->stream('maintenance-history-preview.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to preview PDF: ' . $e->getMessage());
        }
    }


    public function destroyMultiple(Request $request)
    {
        try {
            $ids = $request->ids;
            Maintenance::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' maintenance records deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting maintenance records: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getLabAssets($lab)
    {
        // Get assets by room_number from the location relationship, excluding DISPOSED, LOST, and PULLED OUT assets
        $assets = Asset::whereHas('location', function($query) use ($lab) {
                $query->where('room_number', $lab);
            })
            ->whereNotIn('status', ['DISPOSED', 'LOST', 'PULLED OUT'])
            ->with('location')
            ->get(['id', 'serial_number', 'name', 'location_id', 'status']);

        return response()->json($assets);
    }
    
    public function getLocationAssets($locationId)
    {
        // Get assets by location_id, excluding DISPOSED, LOST, and PULLED OUT assets
        $assets = Asset::where('location_id', $locationId)
            ->whereNotIn('status', ['DISPOSED', 'LOST', 'PULLED OUT'])
            ->with('location')
            ->get(['id', 'serial_number', 'name', 'location_id', 'status']);

        return response()->json($assets);
    }
    // Add this new method to get maintenance records for a specific asset
    public function getAssetMaintenances($assetId)
    {
        $asset = Asset::findOrFail($assetId);

        // Get all maintenances from the asset's location that weren't excluded
        $maintenances = Maintenance::where('location_id', $asset->location_id)
            ->where(function ($query) use ($asset) {
                $query->whereNull('excluded_assets')
                    ->orWhere(function($q) use ($asset) {
                        $q->whereJsonDoesntContain('excluded_assets', $asset->id);
                    });
            })
            ->where('status', 'completed')
            ->orderBy('scheduled_date', 'desc')
            ->get();

        return $maintenances;
    }

    public function cancel($id)
    {
        try {
            $maintenance = Maintenance::findOrFail($id);

            if (auth()->user()->group_id === 2 && $maintenance->technician_id !== auth()->id()) {
                return redirect()->route('maintenance.upcoming')
                    ->with('error', 'You are not authorized to cancel this maintenance task');
            }

            $maintenance->update([
                'status' => 'cancelled',
                'action_by_id' => auth()->id(),
                'completed_at' => null,
                'notes' => request('notes')
            ]);

            return redirect()->route('maintenance.upcoming')->with('success', 'Maintenance cancelled');
        } catch (\Exception $e) {
            return redirect()->route('maintenance.upcoming')->with('error', 'Failed to cancel maintenance');
        }
    }

    public function exportHistoryPDF(Request $request)
    {
        try {
            // Get the same filtered data as the history page
            $query = Maintenance::with(['technician', 'actionBy', 'location'])
                ->whereIn('status', ['completed', 'cancelled'])
                ->orderBy('scheduled_date', 'desc');

            // Apply filters if they exist
            if ($request->has('start_date') && $request->start_date) {
                $query->whereDate('scheduled_date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date) {
                $query->whereDate('scheduled_date', '<=', $request->end_date);
            }

            if ($request->filled('lab')) {
                $query->whereHas('location', function($q) use ($request) {
                    $q->where('room_number', $request->lab);
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('issue')) {
                if ($request->issue === 'with_issues') {
                    $query->whereNotNull('asset_issues')->where('asset_issues', '!=', '[]');
                } elseif ($request->issue === 'no_issues') {
                    $query->where(function($q) {
                        $q->whereNull('asset_issues')->orWhere('asset_issues', '[]');
                    });
                }
            }

            $maintenances = $query->get();

            // Transform data for PDF template
            $maintenances = $maintenances->map(function($maintenance) {
                $maintenance->lab_number = $maintenance->location ? $maintenance->location->room_number : 'N/A';
                // The maintenance_task field is already correct, no transformation needed
                return $maintenance;
            });

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

            // Generate PDF
            $pdf = Pdf::loadView('exports.maintenance-history-pdf', compact('maintenances', 'signatures'));
            $pdf->setPaper('A4', 'landscape');

            // Generate filename with current date
            $filename = 'maintenance-history-' . date('Y-m-d') . '.pdf';

            // Stream PDF for preview in browser (users can download from browser if needed)
            return $pdf->stream($filename);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to export PDF: ' . $e->getMessage());
        }
    }

    // New methods for approval workflow
    public function pendingApproval(Request $request)
    {
        $query = Maintenance::with(['location', 'technician', 'actionBy'])
            ->where('status', 'pending_approval')
            ->orderBy('completed_at', 'desc');

        // Apply filters
        if ($request->filled('lab')) {
            $query->whereHas('location', function($q) use ($request) {
                $q->where('room_number', $request->lab);
            });
        }

        if ($request->filled('technician')) {
            $query->where('technician_id', $request->technician);
        }

        if ($request->filled('start_date')) {
            $query->where('completed_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('completed_at', '<=', $request->end_date);
        }

        $maintenances = $query->paginate(15);
        $technicians = User::whereIn('group_id', [1, 2])->where('status', 'active')->get();

        return view('maintenance-pending-approval', compact('maintenances', 'technicians'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_signature' => 'required|string',
            'admin_notes' => 'nullable|string|max:1000',
            'quality_issues' => 'nullable|array',
            'requires_rework' => 'nullable|boolean',
            'rework_instructions' => 'nullable|string|max:1000'
        ]);

        try {
            $maintenance = Maintenance::findOrFail($id);

            $approvalStatus = $request->requires_rework ? 'needs_rework' : 'approved';
            $mainStatus = $approvalStatus === 'approved' ? 'completed' : 'pending_approval';

            $maintenance->update([
                'status' => $mainStatus,
                'approval_status' => $approvalStatus,
                'approved_by_id' => auth()->id(),
                'approved_at' => now(),
                'admin_signature' => $request->admin_signature,
                'admin_notes' => $request->admin_notes,
                'quality_issues' => $request->quality_issues ?? [],
                'requires_rework' => $request->requires_rework ?? false,
                'rework_instructions' => $request->rework_instructions
            ]);

            // Create notification for technician
            if ($approvalStatus === 'needs_rework') {
                Notification::create([
                    'user_id' => $maintenance->technician_id,
                    'type' => 'maintenance_rework_required',
                    'message' => "Maintenance task requires rework: {$maintenance->maintenance_task}",
                    'is_read' => false,
                    'link' => '/maintenance/upcoming'
                ]);
            } else {
                Notification::create([
                    'user_id' => $maintenance->technician_id,
                    'type' => 'maintenance_approved',
                    'message' => "Maintenance task approved: {$maintenance->maintenance_task}",
                    'is_read' => false,
                    'link' => '/maintenance/history'
                ]);
            }

            $message = $approvalStatus === 'approved' ? 'Maintenance approved successfully' : 'Maintenance marked for rework';
            return redirect()->route('maintenance.pending-approval')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('maintenance.pending-approval')
                ->with('error', 'Failed to process approval: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
            'quality_issues' => 'nullable|array'
        ]);

        try {
            $maintenance = Maintenance::findOrFail($id);

            $maintenance->update([
                'status' => 'needs_rework', // Send back to upcoming for rework
                'approval_status' => 'rejected',
                'approved_by_id' => auth()->id(),
                'approved_at' => now(),
                'admin_notes' => $request->admin_notes,
                'quality_issues' => $request->quality_issues ?? [],
                'requires_rework' => $request->has('requires_rework') ? true : false,
                'rework_count' => $maintenance->rework_count + 1, // Increment rework counter
                'rework_instructions' => $request->rework_instructions ?? null
            ]);

            // Create notification for technician
            Notification::create([
                'user_id' => $maintenance->technician_id,
                'type' => 'maintenance_rejected',
                'message' => "Maintenance task rejected: {$maintenance->maintenance_task}",
                'is_read' => false,
                'link' => '/maintenance/upcoming'
            ]);

            return redirect()->route('maintenance.pending-approval')
                ->with('success', 'Maintenance rejected successfully');

        } catch (\Exception $e) {
            return redirect()->route('maintenance.pending-approval')
                ->with('error', 'Failed to reject maintenance: ' . $e->getMessage());
        }
    }

}
