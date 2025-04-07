<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Maintenance;
use App\Models\User;
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
        ]);

        try {
            // Create separate maintenance records for each task
            foreach ($request->maintenance_tasks as $task) {
                Maintenance::create([
                    'lab_number' => $request->lab_number,
                    'maintenance_task' => $task,  // Changed from 'task' to 'maintenance_task'
                    'technician_id' => $request->technician_id,
                    'scheduled_date' => $request->scheduled_date,
                    'status' => 'scheduled'
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
        $maintenances = Maintenance::where('status', 'scheduled')
            ->orderBy('lab_number')
            ->orderBy('scheduled_date')
            ->get();
        return view('maintenance-upcoming', compact('maintenances'));
    }

    public function history()
    {
        $maintenances = Maintenance::whereIn('status', ['completed', 'cancelled'])
            ->orderBy('scheduled_date', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('maintenance-history', compact('maintenances'));
    }

    public function complete($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->update([
            'status' => 'completed',
            'action_by_id' => auth()->id(),
            'completed_at' => now()
        ]);
        return redirect()->route('maintenance.upcoming')->with('success', 'Maintenance marked as completed');
    }

    public function destroy($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->update([
            'status' => 'cancelled',
            'action_by_id' => auth()->id(),
            'completed_at' => null
        ]);
        return redirect()->route('maintenance.upcoming')->with('success', 'Maintenance cancelled successfully');
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

    // Remove the duplicate methods at the bottom and keep these improved versions
    public function completeAllByDate($lab, $date)
    {
        try {
            $maintenances = Maintenance::where('lab_number', $lab)
                ->whereDate('scheduled_date', $date)
                ->where('status', 'scheduled')
                ->get();

            foreach ($maintenances as $maintenance) {
                $maintenance->update([
                    'status' => 'completed',
                    'action_by_id' => auth()->id(),
                    'completed_at' => now()
                ]);
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
}
