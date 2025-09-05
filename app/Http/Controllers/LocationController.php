<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

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
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        // Use the final_room_number field instead
        $roomNumber = $request->final_room_number;
        
        // Validate that we have a room number
        if (empty($roomNumber)) {
            return back()->withErrors(['room_number' => 'Please select a room/office.'])->withInput();
        }
        
        $request->validate([
            'building' => 'required|string|max:255',
            'floor' => 'required|string|max:255',
        ]);

        $validated = [
            'building' => $request->building,
            'floor' => $request->floor,
            'room_number' => $roomNumber,
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
        // Use the final_room_number field instead
        $roomNumber = $request->final_room_number;
        
        // Validate that we have a room number
        if (empty($roomNumber)) {
            return back()->withErrors(['room_number' => 'Please select a room/office.'])->withInput();
        }
        
        $request->validate([
            'building' => 'required|string|max:255',
            'floor' => 'required|string|max:255',
        ]);

        $validated = [
            'building' => $request->building,
            'floor' => $request->floor,
            'room_number' => $roomNumber,
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

        $location->delete();

        return redirect()->route('locations.index')
            ->with('success', 'Location deleted successfully.');
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
}
