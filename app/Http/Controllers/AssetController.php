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
    private function getLifeStatus($remainingLife, $totalLifespan)
    {
        // Prevent division by zero
        if ($totalLifespan <= 0) {
            return 'out of date'; // Changed from 'critical' to 'out of date'
        }
        
        // For very short lifespans (less than 3 months), always return warning or out of date
        if ($totalLifespan < 0.25) { // Less than 3 months
            return $remainingLife <= 0.08 ? 'out of date' : 'warning'; // Changed from 'critical' to 'out of date'
        }
        
        $percentage = ($remainingLife / $totalLifespan) * 100;
        
        if ($percentage <= 0) {
            return 'out of date'; // Changed from 'critical' to 'out of date'
        } elseif ($percentage <= 25) {
            return 'warning';
        } else {
            return 'good';
        }
    }

    public function index()
    {
        $assets = Asset::all()->map(function ($asset) {
            // Step 1: Calculate Warranty Years
            $purchaseDate = Carbon::parse($asset->purchase_date);
            $warrantyDate = Carbon::parse($asset->warranty_period);
            $warrantyYears = $purchaseDate->diffInDays($warrantyDate) / 365.25;
            
            // Calculate salvage value (10% of purchase price)
            $purchasePrice = $asset->purchase_price ?? 0;
            $salvageValue = $purchasePrice * 0.1;
            
            // Step 2: Calculate Annual Depreciation based on warranty period
            $calculationYears = max(0.01, $warrantyYears);
            $annualDepreciation = ($purchasePrice - $salvageValue) / $calculationYears;
            
            // Step 3: Calculate Useful Life (Lifespan) using depreciation formula
            // Useful Life = (Purchase Price - Salvage Value) / Annual Depreciation
            // This will give us the actual lifespan based on depreciation
            $usefulLife = $annualDepreciation > 0 
                ? ($purchasePrice - $salvageValue) / $annualDepreciation 
                : $calculationYears;
            
            // Extend the lifespan beyond warranty period (1.5 times the warranty period)
            $extendedLifespan = $warrantyYears * 1.5;
            
            // Use the maximum of calculated lifespan and extended warranty period
            $finalLifespan = max($usefulLife, $extendedLifespan);
            
            // Step 4: Calculate Age in Years
            $today = Carbon::now();
            $ageInYears = $purchaseDate->diffInDays($today) / 365.25;
            
            // Step 5: Calculate Remaining Life
            $remainingLife = max(0, $finalLifespan - $ageInYears); // Ensure remaining life is never negative
            
            // Calculate end of life date based on the final lifespan
            $endOfLifeDate = $purchaseDate->copy()->addDays($finalLifespan * 365.25);
            
            // Debug logging
            \Log::debug("Asset calculation debug:", [
                'id' => $asset->id,
                'purchase_date' => $purchaseDate->toDateString(),
                'warranty_date' => $warrantyDate->toDateString(),
                'warranty_years' => $warrantyYears,
                'extended_lifespan' => $extendedLifespan,
                'purchase_price' => $purchasePrice,
                'salvage_value' => $salvageValue,
                'annual_depreciation' => $annualDepreciation,
                'depreciation_lifespan' => $usefulLife,
                'final_lifespan' => $finalLifespan,
                'age_in_years' => $ageInYears,
                'remaining_life' => $remainingLife
            ]);
            
            // Add calculated fields to the asset
            $asset->calculated_lifespan = round($finalLifespan, 2);
            $asset->remaining_life = round($remainingLife, 2); // This will now never be negative
            $asset->end_of_life_date = $endOfLifeDate;
            
            // Add a status indicator for remaining life
            $asset->life_status = $this->getLifeStatus($remainingLife, $finalLifespan);
            
            return $asset;
        });
    
        return view('asset-list', compact('assets'));
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

            // Create QR code directory if it doesn't exist
            Storage::disk('public')->makeDirectory('qrcodes');

            // Generate QR code with proper data
            $qrCode = new QrCode(json_encode([
                'id' => $asset->id,
                'name' => $asset->name,
                'category' => $category->name, // Include category name
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
        $validated = $request->validate([
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
        ]);

        // Handle location field
        if ($request->location_select === 'others') {
            $validated['location'] = $request->location;
        } else {
            $validated['location'] = $request->location_select;
        }

        // Check for duplicate serial number, excluding the current asset
        if (Asset::where('serial_number', $request->serial_number)
            ->where('id', '!=', $asset->id)
            ->exists()
        ) {
            return back()
                ->withInput()
                ->withErrors(['serial_number' => 'This Serial Number is already registered in the system.']);
        }

        $oldValues = $asset->getAttributes();

        // Update the asset with validated data
        $asset->update($validated);

        // Record the changes in asset history
        foreach ($validated as $field => $newValue) {
            if (isset($oldValues[$field]) && $oldValues[$field] !== $newValue) {
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'change_type' => strtoupper($field),
                    'old_value' => $oldValues[$field],
                    'new_value' => $newValue,
                    'remarks' => "Updated $field",
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
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'serial_number' => $validated['serial_number']
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

        // Record changes in asset history
        foreach ($validated as $field => $value) {
            if ($oldValues[$field] != $value) {
                // Change purchase_price to PRICE for consistency
                $changeType = $field === 'purchase_price' ? 'PRICE' : strtoupper($field);

                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'change_type' => $changeType,
                    'old_value' => $oldValues[$field],
                    'new_value' => $value,
                    'changed_by' => auth()->id()
                ]);
            }
        }

        return redirect()->route('assets.index')
            ->with('success', 'Asset updated successfully');
    }

    public function fetch($serialNumber)
    {
        $asset = Asset::where('serial_number', $serialNumber)->first();

        if (!$asset) {
            return response()->json(['message' => 'Asset not found'], 404);
        }

        return response()->json([
            'name' => $asset->name,
            'location' => $asset->location,
            'category_id' => $asset->category_id
        ]);
    }
}
