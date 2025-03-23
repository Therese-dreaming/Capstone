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
        $technicians = User::whereNotIn('group_id', function($query) {
            $query->select('id')
                  ->from('groups')
                  ->where('name', 'Users');  // Changed from 'user' to 'Users'
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
        
        // Check for existing maintenance
        $existingMaintenance = Maintenance::where('asset_id', $request->asset_id)
            ->where('status', 'scheduled')
            ->first();

        if ($existingMaintenance && !$request->has('confirm_overwrite')) {
            return redirect()->back()
                ->with('warning', "This asset (Serial Number: {$asset->serial_number}) already has a scheduled maintenance. Do you want to overwrite it?")
                ->with('form_data', $request->only(['asset_id', 'maintenance_task', 'technician_id', 'scheduled_date']));
        }

        // Update existing maintenance or create new one
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
                      ->map(function($asset) {
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

    public function getAssetDetails(Asset $asset)
    {
        return response()->json([
            'id' => $asset->id,
            'name' => $asset->name,
            'serial_number' => $asset->serial_number,
            'status' => $asset->status,
            'photo_url' => $asset->photo ? asset('storage/' . $asset->photo) : null
        ]);
    }

    public function getSchedulesByDate($date)
        {
            $schedules = Maintenance::where('scheduled_date', $date)
                ->where('status', 'scheduled')
                ->with(['asset', 'technician'])
                ->get();
                
            return response()->json($schedules);
        }

    public function getCompletedMaintenance(Request $request)
    {
        $query = Maintenance::with(['asset', 'technician'])
            ->whereNotNull('completion_date')  // Changed from completed_date to completion_date
            ->orderBy('completion_date', 'desc');  // Changed here too

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('asset', function($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%");
            });
        }

        return response()->json($query->get());
    }
}