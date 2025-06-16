<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\AssetHistory;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;  // Add this import

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::query();

        // Apply filters if they exist
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('serial_number', 'like', "%{$search}%");
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('location')) {
            $query->where('location', $request->location);
        }

        // Get the assets with pagination
        $assets = $query->paginate(10)->withQueryString();

        // Calculate lifespan and other metrics for each asset
        $assets->getCollection()->transform(function ($asset) {
            $today = Carbon::now();
            $purchaseDate = Carbon::parse($asset->purchase_date);
            $warrantyDate = Carbon::parse($asset->warranty_period);

            // Check if warranty has expired
            if ($warrantyDate->isPast()) {
                $asset->update([
                    'calculated_lifespan' => 0,
                    'remaining_life' => 0,
                    'end_of_life_date' => $warrantyDate,
                    'life_status' => 'critical'
                ]);
                return $asset;
            }

            // Step 1: Calculate Warranty Years
            $warrantyYears = $purchaseDate->diffInDays($warrantyDate) / 365.25;

            // Calculate salvage value (10% of purchase price)
            $purchasePrice = $asset->purchase_price ?? 0;
            $salvageValue = $purchasePrice * 0.1;

            // Step 2: Calculate Annual Depreciation based on warranty period
            $calculationYears = max(0.01, $warrantyYears);
            $annualDepreciation = ($purchasePrice - $salvageValue) / $calculationYears;

            // Step 3: Calculate Useful Life (Lifespan) using depreciation formula
            $usefulLife = $annualDepreciation > 0
                ? ($purchasePrice - $salvageValue) / $annualDepreciation
                : $calculationYears;

            // Extend the lifespan beyond warranty period (1.5 times the warranty period)
            $extendedLifespan = $warrantyYears * 1.5;

            // Use the maximum of calculated lifespan and extended warranty period
            $finalLifespan = max($usefulLife, $extendedLifespan);

            // Step 4: Calculate Age in Years
            $ageInYears = $purchaseDate->diffInDays($today) / 365.25;

            // Step 5: Calculate Remaining Life
            $remainingLife = max(0, $finalLifespan - $ageInYears);

            // Calculate end of life date based on the final lifespan
            $endOfLifeDate = $purchaseDate->copy()->addDays($finalLifespan * 365.25);
            
            // Determine life status
            $lifeStatus = $endOfLifeDate->isPast()
                ? 'critical'
                : $this->getLifeStatus($remainingLife, $finalLifespan);

            // Update the asset with calculated values
            $asset->update([
                'calculated_lifespan' => round($finalLifespan, 2),
                'remaining_life' => round($remainingLife, 2),
                'end_of_life_date' => $endOfLifeDate,
                'life_status' => $lifeStatus
            ]);
            
            return $asset;
        });
    
        return view('asset-list', compact('assets'));
    }

    private function getLifeStatus($remainingLife, $totalLifespan)
    {
        // Prevent division by zero and handle expired assets
        if ($totalLifespan <= 0 || $remainingLife <= 0) {
            return 'critical';
        }

        // Calculate percentage of remaining life
        $percentage = ($remainingLife / $totalLifespan) * 100;

        // Check absolute remaining life in years
        if ($remainingLife < 0.25) { // Less than 3 months
            return 'critical';
        } else if ($remainingLife < 0.5) { // Less than 6 months
            return 'warning';
        }

        // Also check percentage-based thresholds
        if ($percentage <= 10) {
            return 'critical';
        } else if ($percentage <= 25) {
            return 'warning';
        }

        return 'good';
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'location' => 'required|string|max:255',
                'status' => 'required|in:IN USE,UNDER REPAIR,UPGRADE,PULLED OUT',
                'model' => 'required|string|max:255',
                'serial_number' => 'required|string|max:255|unique:assets,serial_number',
                'specification' => 'required|string',
                'vendor' => 'required|string|max:255',
                'purchase_date' => 'required|date',
                'warranty_period' => 'required|date|after_or_equal:purchase_date',
                'purchase_price' => 'required|numeric|min:0',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Ensure the category exists
            $category = Category::findOrFail($validated['category_id']);

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('assets', 'public');
                $validated['photo'] = $photoPath;
            }

            // Create the asset first
            $asset = Asset::create($validated);

            // Create asset history record for the new asset
            AssetHistory::create([
                'asset_id' => $asset->id,
                'change_type' => 'CREATED',
                'old_value' => null,
                'new_value' => 'Asset created',
                'remarks' => 'New asset added to the system',
                'changed_by' => auth()->id()
            ]);

            // Create QR code directory if it doesn't exist
            Storage::disk('public')->makeDirectory('qrcodes');

            // Generate QR code with proper data
            $qrCode = new QrCode(json_encode([
                'id' => $asset->id,
                'serial_number' => $asset->serial_number
            ]));

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $qrPath = 'qrcodes/asset-' . $asset->id . '.png';

            // Ensure the QR code is stored properly
            if (Storage::disk('public')->put($qrPath, $result->getString())) {
                $asset->update(['qr_code' => $qrPath]);
            } else {
                throw new \Exception('Failed to store QR code');
            }

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Asset added successfully']);
            }

            return redirect()->route('assets.index')
                ->with('success', 'Asset has been added successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error: ' . json_encode($e->errors()));
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Asset creation error: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while adding the asset: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('add-asset', compact('categories'));
    }

    public function qrList()
    {
        $assets = Asset::all();
        return view('qr-list', compact('assets'));
    }

    public function previewQrCodes(Request $request)
    {
        $selectedIds = json_decode($request->selected_items);
        $assets = Asset::whereIn('id', $selectedIds)->get();

        $pdf = PDF::loadView('pdf.qr-codes', compact('assets'));
        return $pdf->stream('preview.pdf');
    }

    public function exportQrCodes(Request $request)
    {
        $selectedIds = json_decode($request->selected_items);
        $assets = Asset::whereIn('id', $selectedIds)->get();

        $pdf = PDF::loadView('pdf.qr-codes', compact('assets'));
        return $pdf->download('asset-qrcodes.pdf');
    }

    public function categoryReport(Request $request)
    {
        $query = Asset::query();

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        $categories = Category::all();

        // Prepare category statistics
        $categoryStats = Category::withCount('assets')
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'count' => $category->assets_count,
                    'total_value' => $category->assets->sum('purchase_price')
                ];
            });

        // Prepare detailed category information
        $categoryDetails = Category::all()->map(function ($category) {
            $assets = $category->assets;
            return [
                'name' => $category->name,
                'total' => $assets->count(),
                'in_use' => $assets->where('status', 'IN USE')->count(),
                'under_repair' => $assets->where('status', 'UNDER REPAIR')->count(),
                'disposed' => $assets->where('status', 'DISPOSED')->count(),
                'value' => $assets->sum('purchase_price')
            ];
        });

        return view('asset-category-report', compact('categories', 'categoryStats', 'categoryDetails'));
    }

    public function destroy(Asset $asset)
    {
        // Create a history record for deletion
        AssetHistory::create([
            'asset_id' => $asset->id,
            'change_type' => 'STATUS',
            'old_value' => $asset->status,
            'new_value' => 'DELETED',
            'remarks' => 'Asset was deleted from the system',
            'changed_by' => auth()->id()
        ]);

        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', 'Asset has been deleted successfully');
    }

    public function dispose(Request $request, Asset $asset)
    {
        $request->validate([
            'disposal_reason' => 'required|string|max:255'
        ]);

        // Update asset status and disposal information
        $asset->update([
            'status' => 'DISPOSED',
            'disposal_date' => now(),
            'disposal_reason' => $request->disposal_reason
        ]);

        // Create asset history record
        AssetHistory::create([
            'asset_id' => $asset->id,
            'change_type' => 'disposal',
            'old_value' => 'active',
            'new_value' => 'DISPOSED',
            'remarks' => $request->disposal_reason,
            'changed_by' => auth()->id()
        ]);

        return redirect($request->input('redirect', route('reports.disposal-history')))
            ->with('success', 'Asset has been marked as disposed.');
    }

    public function edit(Asset $asset)
    {
        $categories = Category::all();
        return view('assets.edit', compact('asset', 'categories'));
    }

    public function update(Request $request, Asset $asset)
    {
        // Only validate fields that are actually present and different
        $validationRules = [
            'name' => 'required',
            'serial_number' => 'required',
            'category_id' => 'required|exists:categories,id',
            'purchase_price' => 'required|numeric',
            'status' => 'required|in:IN USE,UNDER REPAIR,UPGRADE,PULLED OUT',
            'model' => 'required',
            'specification' => 'required',
            'vendor' => 'required',
            'purchase_date' => 'required|date',
            'warranty_period' => 'required|date',
        ];

        // Only validate fields that are present and different from current values
        $fieldsToValidate = [];
        $fieldsToUpdate = [];
        foreach ($validationRules as $field => $rules) {
            if ($request->has($field) && $request->get($field) != $asset->$field) {
                $fieldsToValidate[$field] = $rules;
                $fieldsToUpdate[] = $field;
            }
        }

        $validated = $request->validate($fieldsToValidate);

        // Handle location field if it's present and different
        if ($request->has('location_select') && $request->location_select != $asset->location) {
            if ($request->location_select === 'others') {
                $validated['location'] = $request->location;
            } else {
                $validated['location'] = $request->location_select;
            }
            $fieldsToUpdate[] = 'location';
        }

        // Check for duplicate serial number only if it's being updated
        if (isset($validated['serial_number']) && 
            Asset::where('serial_number', $validated['serial_number'])
                ->where('id', '!=', $asset->id)
                ->exists()
        ) {
            return back()
                ->withInput()
                ->withErrors(['serial_number' => 'This Serial Number is already registered in the system.']);
        }

        $oldValues = $asset->getAttributes();

        // Update only the validated fields
        $asset->update($validated);

        // Record the changes in asset history only for fields that actually changed
        foreach ($fieldsToUpdate as $field) {
            if (isset($validated[$field]) && $oldValues[$field] !== $validated[$field]) {
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'change_type' => strtoupper($field),
                    'old_value' => $oldValues[$field],
                    'new_value' => $validated[$field],
                    'remarks' => $field === 'status' ? 
                        "Changed from \"{$oldValues[$field]}\" to \"{$validated[$field]}\"" : 
                        "Updated $field",
                    'changed_by' => auth()->id()
                ]);
            }
        }

        // Check if any QR-relevant fields are being updated
        $qrRelevantFields = ['name', 'serial_number', 'category_id'];
        $needsQRUpdate = false;

        foreach ($qrRelevantFields as $field) {
            if (isset($validated[$field]) && $validated[$field] != $oldValues[$field]) {
                $needsQRUpdate = true;
                break;
            }
        }

        // If relevant fields changed, regenerate QR code
        if ($needsQRUpdate) {
            // Delete old QR code if it exists
            if ($asset->qr_code && Storage::exists('public/' . $asset->qr_code)) {
                Storage::delete('public/' . $asset->qr_code);
            }

            // Generate new QR code using Endroid - match the store method structure
            $qrCode = new QrCode(json_encode([
                'id' => $asset->id,
                'serial_number' => $asset->serial_number
            ]));

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Use the same naming convention as store method
            $qrPath = 'qrcodes/asset-' . $asset->id . '.png';
            Storage::disk('public')->put($qrPath, $result->getString());

            // Update the asset with new QR code path
            $validated['qr_code'] = $qrPath;
        }

        // Update the asset first to ensure all new values are saved
        $asset->update($validated);

        return redirect()->route('assets.index')
            ->with('success', 'Asset updated successfully');
    }

    public function fetch($serialNumber)
    {
        try {
            \Log::info('Fetching asset with serial number: ' . $serialNumber);
            
            // Validate serial number
            if (empty($serialNumber)) {
                return response()->json([
                    'message' => 'Serial number is required'
                ], 400);
            }
            
            $lab = request()->query('lab');
            
            // Log the query we're about to make
            \Log::info('Searching for asset with conditions:', [
                'serial_number' => $serialNumber,
                'lab' => $lab
            ]);
            
            $asset = Asset::where('serial_number', $serialNumber)
                ->when($lab, function($query) use ($lab) {
                    return $query->where('location', $lab);
                })
                ->with('category')
                ->first();
            
            // Log the query that was executed
            \Log::info('SQL Query:', [
                'sql' => Asset::where('serial_number', $serialNumber)->toSql(),
                'bindings' => ['serialNumber' => $serialNumber]
            ]);
            
            if (!$asset) {
                \Log::warning('Asset not found with serial number: ' . $serialNumber);
                
                // Check if the serial number exists in any format
                $similarAssets = Asset::where('serial_number', 'LIKE', '%' . $serialNumber . '%')
                    ->select('serial_number')
                    ->get();
                
                if ($similarAssets->isNotEmpty()) {
                    \Log::info('Similar serial numbers found:', ['similar' => $similarAssets->pluck('serial_number')]);
                    return response()->json([
                        'message' => 'Asset not found. Similar serial numbers exist: ' . 
                            $similarAssets->pluck('serial_number')->implode(', '),
                        'similar_serial_numbers' => $similarAssets->pluck('serial_number')
                    ], 404);
                }
                
                return response()->json([
                    'message' => $lab ? 'Asset not found in this lab' : 'Asset not found',
                    'serial_number' => $serialNumber
                ], 404);
            }
            
            \Log::info('Asset found:', ['asset' => $asset->toArray()]);
            
            return response()->json([
                'id' => $asset->id,
                'name' => $asset->name,
                'location' => $asset->location,
                'category_id' => $asset->category_id,
                'category' => $asset->category,
                'serial_number' => $asset->serial_number,
                'status' => $asset->status,
                'model' => $asset->model,
                'specification' => $asset->specification,
                'vendor' => $asset->vendor,
                'purchase_date' => $asset->purchase_date,
                'warranty_period' => $asset->warranty_period,
                'purchase_price' => $asset->purchase_price,
                'end_of_life_date' => $asset->end_of_life_date ? $asset->end_of_life_date->format('M d, Y') : null,
                'life_status' => $asset->life_status,
                'calculated_lifespan' => $asset->calculated_lifespan,
                'remaining_life' => $asset->remaining_life
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching asset:', [
                'serial_number' => $serialNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error fetching asset: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(\App\Models\Asset $asset)
    {
        return view('assets.show', compact('asset'));
    }
}
