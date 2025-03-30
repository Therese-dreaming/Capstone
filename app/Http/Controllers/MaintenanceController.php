<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use App\Models\Category;
use App\Models\Maintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function scheduleForm()
    {
        $categories = Category::all();
        $technicians = User::whereNotIn('group_id', function ($query) {
            $query->select('id')
                ->from('groups')
                ->where('name', 'Users');
        })->get();

        return view('maintenance-schedule', compact('categories', 'technicians'));
    }

    public function scheduleStore(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'maintenance_task' => 'required|string|max:255',
            'technician_id' => 'required|exists:users,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'confirm_completion' => 'required|accepted'
        ]);

        $asset = Asset::find($request->asset_id);

        $existingMaintenance = Maintenance::where('asset_id', $request->asset_id)
            ->where('status', 'scheduled')
            ->first();

        if ($existingMaintenance && !$request->has('confirm_overwrite')) {
            return redirect()->back()
                ->with('warning', "This asset (Serial Number: {$asset->serial_number}) already has a scheduled maintenance. Do you want to overwrite it?")
                ->with('form_data', $request->only(['asset_id', 'maintenance_task', 'technician_id', 'scheduled_date']));
        }

        if ($existingMaintenance) {
            $existingMaintenance->update([
                'task' => $request->maintenance_task,
                'technician_id' => $request->technician_id,
                'scheduled_date' => $request->scheduled_date,
                'serial_number' => $asset->serial_number
            ]);
        } else {
            Maintenance::create([
                'asset_id' => $request->asset_id,
                'task' => $request->maintenance_task,
                'technician_id' => $request->technician_id,
                'status' => 'scheduled',
                'scheduled_date' => $request->scheduled_date,
                'serial_number' => $asset->serial_number
            ]);
        }

        return redirect()->back()->with('success', 'Maintenance scheduled successfully');
    }

    public function getAssetsByCategory(Category $category)
    {
        $assets = Asset::where('category_id', $category->id)
            ->select('id', 'name', 'serial_number', 'status', 'photo')
            ->get()
            ->map(function ($asset) {
                return [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    'serial_number' => $asset->serial_number,
                    'status' => $asset->status,
                    'photo_url' => $asset->photo ? asset('storage/' . $asset->photo) : null
                ];
            });

        return response()->json($assets);
    }

    public function getSchedulesByDate($date)
    {
        $schedules = Maintenance::where('scheduled_date', $date)
            ->where('status', 'scheduled')
            ->with(['asset', 'technician'])
            ->get();

        return response()->json($schedules);
    }

    public function getSchedulesByDateRange($start, $end)
    {
        return Maintenance::whereBetween('scheduled_date', [$start, $end])
            ->where('status', 'scheduled')
            ->with(['asset', 'technician'])
            ->get();
    }

    public function getCompletedMaintenance(Request $request)
    {
        $query = Maintenance::with(['asset', 'technician'])
            ->where('status', 'completed')
            ->whereNotNull('completion_date')
            ->orderBy('completion_date', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('asset', function ($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%");
            });
        }

        return response()->json($query->get());
    }

    public function getUpcomingMaintenance(Request $request)
    {
        $query = Maintenance::with(['asset', 'technician'])
            ->where('status', 'scheduled')
            ->where('scheduled_date', '>=', now())
            ->orderBy('scheduled_date');

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('asset', function ($subQ) use ($request) {
                    $subQ->where('serial_number', 'like', '%' . $request->search . '%');
                });
            });
        }

        return response()->json($query->get());
    }

    public function upcomingView()
    {
        return view('scheduled-maintenance');
    }

    public function history()
    {
        return view('maintenance-history');
    }

    public function completeMaintenance($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->update([
            'status' => 'completed',
            'completion_date' => now()
        ]);

        return response()->json(['message' => 'Maintenance marked as completed']);
    }

    public function edit($id)
    {
        $maintenance = Maintenance::with(['asset', 'technician'])->findOrFail($id);
        $technicians = User::whereNotIn('group_id', function ($query) {
            $query->select('id')
                ->from('groups')
                ->where('name', 'Users');
        })->get();

        return view('maintenance-edit', compact('maintenance', 'technicians'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'asset_id' => 'required',
            'maintenance_task' => 'required',
            'technician_id' => 'required',
            'scheduled_date' => 'required|date',
            'confirm_completion' => 'required'
        ]);
    
        // Check for existing upcoming maintenance for this asset
        $existingMaintenance = Maintenance::where('asset_id', $request->asset_id)
            ->where('scheduled_date', '>=', now())
            ->where('status', '!=', 'Completed')
            ->first();
    
        // If there's an existing maintenance and no overwrite confirmation
        if ($existingMaintenance && !$request->has('confirm_overwrite')) {
            // Store form data in session for the confirmation form
            return redirect()->back()->with([
                'warning' => true,
                'form_data' => $request->all()
            ]);
        }
    
        // Create new maintenance schedule
        $maintenance = new Maintenance();
        $maintenance->asset_id = $request->asset_id;
        $maintenance->maintenance_task = $request->maintenance_task;
        $maintenance->technician_id = $request->technician_id;
        $maintenance->scheduled_date = $request->scheduled_date;
        $maintenance->status = 'Scheduled';
        $maintenance->save();
    
        return redirect()->route('maintenance.upcoming')
            ->with('success', 'Maintenance schedule created successfully');
    }

    // Add destroy method for delete functionality
    public function destroy($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->delete();
        
        return response()->json(['message' => 'Maintenance schedule deleted successfully']);
    }
}
