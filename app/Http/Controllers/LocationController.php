<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Building;
use App\Models\Floor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $query = Location::withCount('assets');
        
        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('building', 'like', "%{$search}%")
                  ->orWhere('floor', 'like', "%{$search}%")
                  ->orWhere('room_number', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(building, ' - ', floor, ' - ', room_number) LIKE ?", ["%{$search}%"]);
            });
        }
        
        $locations = $query->orderBy('building')->orderBy('floor')->orderBy('room_number')->paginate(15)->withQueryString();
        $buildings = Building::with('floors')->orderBy('name')->get();
        return view('locations.index', compact('locations', 'buildings'));
    }

    public function create()
    {
        $buildings = Building::with('floors')->where('is_active', true)->orderBy('name')->get();
        return view('locations.create', compact('buildings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'building' => 'required|string|max:255',
            'floor' => 'required|string|max:255',
            'room_number' => 'required|string|max:255',
        ]);

        $validated = [
            'building' => $request->building,
            'floor' => $request->floor,
            'room_number' => $request->room_number,
        ];

        // Check if location already exists
        $existingLocation = Location::where([
            'building' => $validated['building'],
            'floor' => $validated['floor'],
            'room_number' => $validated['room_number'],
        ])->first();

        if ($existingLocation) {
            return back()->withErrors(['error' => 'This location already exists.'])->withInput();
        }

        Location::create($validated);

        return redirect()->route('locations.index')
            ->with('success', 'Location created successfully.');
    }

    public function show(Location $location)
    {
        $location->load(['assets' => function($query) {
            $query->with(['category', 'vendor']);
        }]);
        
        return view('locations.show', compact('location'));
    }

    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'building' => 'required|string|max:255',
            'floor' => 'required|string|max:255',
            'room_number' => 'required|string|max:255',
        ]);

        $validated = [
            'building' => $request->building,
            'floor' => $request->floor,
            'room_number' => $request->room_number,
        ];

        // Check if another location already exists with these details
        $existingLocation = Location::where([
            'building' => $validated['building'],
            'floor' => $validated['floor'],
            'room_number' => $validated['room_number'],
        ])->where('id', '!=', $location->id)->first();

        if ($existingLocation) {
            return back()->withErrors(['error' => 'A location with these details already exists.'])->withInput();
        }

        $location->update($validated);

        return redirect()->route('locations.index')
            ->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        // Check if location has assets
        if ($location->assets()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete location that has assets assigned to it.']);
        }

        // Check if location has maintenance records
        if ($location->maintenances()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete location that has maintenance records associated with it. Please remove or reassign the maintenance records first.']);
        }

        try {
            $location->delete();
            return redirect()->route('locations.index')
                ->with('success', 'Location deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle any other foreign key constraint violations
            if ($e->getCode() == 23000) {
                return back()->withErrors(['error' => 'Cannot delete location because it is referenced by other records in the system.']);
            }
            
            // Re-throw if it's a different error
            throw $e;
        }
    }

    public function checkRelations(Location $location)
    {
        $assetsCount = $location->assets()->count();
        $maintenancesCount = $location->maintenances()->count();
        
        return response()->json([
            'hasRelations' => $assetsCount > 0 || $maintenancesCount > 0,
            'assetsCount' => $assetsCount,
            'maintenancesCount' => $maintenancesCount,
        ]);
    }

    public function getAll()
    {
        $locations = Location::all()->map(function($location) {
            return [
                'id' => $location->id,
                'full_location' => $location->full_location,
                'building' => $location->building,
                'floor' => $location->floor,
                'room_number' => $location->room_number,
            ];
        });

        return response()->json($locations);
    }

    public function showBuilding(Building $building)
    {
        $building->load('floors');
        return view('buildings.show', compact('building'));
    }

    // Building management methods
    public function storeBuilding(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:buildings,name',
            'description' => 'nullable|string',
        ]);

        Building::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('locations.index')
            ->with('success', 'Building created successfully.');
    }

    public function updateBuilding(Request $request, Building $building)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:buildings,name,' . $building->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $building->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('locations.index')
            ->with('success', 'Building updated successfully.');
    }

    public function destroyBuilding(Building $building)
    {
        DB::transaction(function () use ($building) {
            // Delete all floors first (due to foreign key constraint)
            $building->floors()->delete();
            // Then delete the building
            $building->delete();
        });

        return redirect()->route('locations.index')
            ->with('success', 'Building and all its floors have been deleted successfully.');
    }

    public function storeFloor(Request $request, Building $building)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'floor_number' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $building->floors()->create([
            'name' => $request->name,
            'floor_number' => $request->floor_number,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('locations.index')
            ->with('success', 'Floor added successfully.');
    }

    public function bulkStoreFloors(Request $request, Building $building)
    {
        $request->validate([
            'floors' => 'required|array|min:1',
            'floors.*.name' => 'required|string|max:255',
        ]);

        $createdFloors = [];
        $duplicateFloors = [];
        $existingFloorNames = $building->floors()->pluck('name')->toArray();

        foreach ($request->floors as $floorData) {
            $floorName = trim($floorData['name']);
            
            // Skip if floor already exists
            if (in_array($floorName, $existingFloorNames)) {
                $duplicateFloors[] = $floorName;
                continue;
            }

            // Create the floor
            $building->floors()->create([
                'name' => $floorName,
                'floor_number' => null, // Can be set later if needed
                'description' => null,
                'is_active' => true,
            ]);

            $createdFloors[] = $floorName;
        }

        $message = '';
        if (count($createdFloors) > 0) {
            $message = count($createdFloors) . ' floor(s) added successfully.';
        }
        
        if (count($duplicateFloors) > 0) {
            if ($message) $message .= ' ';
            $message .= count($duplicateFloors) . ' floor(s) were skipped (already exist).';
        }

        return redirect()->back()->with('success', $message);
    }

    public function updateFloor(Request $request, Building $building, Floor $floor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'floor_number' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $floor->update([
            'name' => $request->name,
            'floor_number' => $request->floor_number,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('locations.index')
            ->with('success', 'Floor updated successfully.');
    }

    public function destroyFloor(Building $building, Floor $floor)
    {
        $floor->delete();

        return redirect()->route('locations.index')
            ->with('success', 'Floor deleted successfully.');
    }
}
