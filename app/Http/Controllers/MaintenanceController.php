<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Maintenance;
use App\Models\User;
use App\Models\Asset;
use App\Models\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $technicians = User::whereHas('group', function ($query) {
            $query->whereNotIn('name', ['Users']);
        })->get();

        $labs = [
            '401' => 'Computer Laboratory 401',
            '402' => 'Computer Laboratory 402',
            '403' => 'Computer Laboratory 403',
            '404' => 'Computer Laboratory 404',
            '405' => 'Computer Laboratory 405',
            '406' => 'Computer Laboratory 406'
        ];

        // Fetch tasks from the database
        $maintenanceTasks = DB::table('maintenance_tasks')
            ->orderBy('created_at', 'desc')
            ->pluck('name')
            ->toArray();

        return view('maintenance-schedule', compact('technicians', 'labs', 'maintenanceTasks'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'lab_number' => 'required',
            'maintenance_tasks' => 'required|array|min:1',
            'technician_id' => 'required',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'excluded_assets' => 'nullable|string',  // For JSON string
        ]);

        try {
            // Decode the excluded assets JSON string
            $excludedAssets = $request->excluded_assets ? json_decode($request->excluded_assets, true) : [];

            // Create separate maintenance records for each task
            foreach ($request->maintenance_tasks as $task) {
                $maintenance = Maintenance::create([
                    'lab_number' => $request->lab_number,
                    'maintenance_task' => $task,
                    'technician_id' => $request->technician_id,
                    'scheduled_date' => $request->scheduled_date,
                    'status' => 'scheduled',
                    'excluded_assets' => $excludedAssets
                ]);

                // Create notification for assigned technician
                Notification::create([
                    'user_id' => $request->technician_id,
                    'type' => 'maintenance_assigned',
                    'message' => "You have been assigned maintenance task: {$task} in Lab {$request->lab_number}",
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

    public function upcoming()
    {
        $query = Maintenance::where('status', 'scheduled');

        // If user is secretary (group_id = 2), only show their assigned tasks
        if (auth()->user()->group_id === 2) {
            $query->where('technician_id', auth()->id());
        }

        $maintenances = $query->orderBy('lab_number')
            ->orderBy('scheduled_date')
            ->get();

        return view('maintenance-upcoming', compact('maintenances'));
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
            'status' => 'completed',
            'action_by_id' => auth()->id(),
            'completed_at' => now()
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
    
            return redirect()->route('maintenance.upcoming')->with('success', 'Maintenance marked as completed');
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

    public function history()
    {
        $maintenances = Maintenance::whereIn('status', ['completed', 'cancelled'])
            ->orderBy('scheduled_date', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('maintenance-history', compact('maintenances'));
    }

    public function edit($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $technicians = User::whereHas('group', function ($query) {
            $query->whereNotIn('name', ['Users']);
        })->get();

        $labs = [
            '401' => 'Computer Laboratory 401',
            '402' => 'Computer Laboratory 402',
            '403' => 'Computer Laboratory 403',
            '404' => 'Computer Laboratory 404',
        ];

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

    public function editByDate($lab, $date)
    {
        $maintenances = Maintenance::where('lab_number', $lab)
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
            'labNumber' => $lab,
            'date' => $date,
            'maintenances' => $maintenances,
            'technicians' => $technicians,
            'maintenanceTasks' => $maintenanceTasks
        ]);
    }

    public function updateByDate(Request $request, $lab, $date)
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

    public function addTask(Request $request, $lab, $date)
    {
        $maintenance = new Maintenance([
            'lab_number' => $lab,
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

    public function completeAllByDate($lab, $date)
    {
        try {
            $maintenances = Maintenance::where('lab_number', $lab)
                ->whereDate('scheduled_date', $date)
                ->where('status', 'scheduled')
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
                    'status' => 'completed',
                    'action_by_id' => auth()->id(),
                    'completed_at' => now()
                ];

                // Add asset issues data if it exists
                if (isset($assetIssues)) {
                    $updateData['asset_issues'] = $assetIssues;
                    $updateData['serial_number'] = json_encode($serialNumbers); // Store all serial numbers as JSON
                }

                $maintenance->update($updateData);
            }

            return redirect()->route('maintenance.upcoming')
                ->with('success', 'All maintenance tasks completed successfully');
        } catch (\Exception $e) {
            return redirect()->route('maintenance.upcoming')
                ->with('error', 'Failed to complete maintenance tasks');
        }
    }

    public function cancelAllByDate($lab, $date)
    {
        try {
            $maintenances = Maintenance::where('lab_number', $lab)
                ->whereDate('scheduled_date', $date)
                ->where('status', 'scheduled')
                ->get();

            foreach ($maintenances as $maintenance) {
                $maintenance->update([
                    'status' => 'cancelled',
                    'action_by_id' => auth()->id(),
                    'completed_at' => null
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
        $query = Maintenance::whereIn('status', ['completed', 'cancelled']);

        if ($request->lab) {
            $query->where('lab_number', $request->lab);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->start_date) {
            $query->whereDate('scheduled_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('scheduled_date', '<=', $request->end_date);
        }

        $maintenances = $query->orderBy('scheduled_date', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('exports.maintenance-history-pdf', compact('maintenances'));
    }

    public function exportPDF(Request $request)
    {
        $query = Maintenance::whereIn('status', ['completed', 'cancelled']);

        if ($request->lab) {
            $query->where('lab_number', $request->lab);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->start_date) {
            $query->whereDate('scheduled_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('scheduled_date', '<=', $request->end_date);
        }

        $maintenances = $query->orderBy('scheduled_date', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        $pdf = PDF::loadView('exports.maintenance-history-pdf', compact('maintenances'));
        return $pdf->download('maintenance-history.pdf');
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
        // Convert lab number to full laboratory name
        $labName = 'Computer Lab ' . $lab;

        $assets = Asset::where('location', $labName)
            ->where('status', '!=', 'DISPOSED')  // Changed from 'active' to match your asset status format
            ->get(['id', 'serial_number', 'name']);

        return response()->json($assets);
    }
    // Add this new method to get maintenance records for a specific asset
    public function getAssetMaintenances($assetId)
    {
        $asset = Asset::findOrFail($assetId);

        // Get all maintenances from the asset's laboratory that weren't excluded
        $maintenances = Maintenance::where('lab_number', $asset->laboratory)
            ->where(function ($query) use ($asset) {
                $query->whereNull('excluded_assets')
                    ->orWhereRaw('NOT JSON_CONTAINS(excluded_assets, ?)', ['"' . $asset->id . '"']);
            })
            ->where('status', 'completed')
            ->orderBy('scheduled_date', 'desc')
            ->get();

        return $maintenances;
    }
}
