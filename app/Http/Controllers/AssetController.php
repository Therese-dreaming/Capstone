<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\AssetHistory;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::with(['category', 'location', 'vendor', 'creator']);

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
            $query->where('location_id', $request->location);
        }

        // Add date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->where('purchase_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('purchase_date', '<=', $request->date_to);
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
            // Special handling for non-registered assets
            $isFromNonRegistered = $request->has('from_non_registered') && $request->from_non_registered;
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'location_id' => 'required|exists:locations,id',
                'status' => 'required|in:IN USE,UNDER REPAIR,UPGRADE,PULLED OUT',
                'model' => 'required|string|max:255',
                'specification' => 'required|string',
                'vendor_id' => 'required|exists:vendors,id',
                'purchase_date' => 'required|date',
                'warranty_period' => 'required|date|after_or_equal:purchase_date',
                'purchase_price' => 'required|numeric|min:0',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // For non-registered assets, force status to PULLED OUT
            if ($isFromNonRegistered) {
                $validated['status'] = 'PULLED OUT';
            }

            // Ensure the category exists
            $category = Category::findOrFail($validated['category_id']);

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('assets', 'public');
                $validated['photo'] = $photoPath;
            }

            // Handle acquisition document upload
            if ($request->hasFile('acquisition_document')) {
                $acquisitionPath = $request->file('acquisition_document')->store('assets/acquisition_docs', 'public');
                $validated['acquisition_document'] = $acquisitionPath;
            }

            // Create the asset first
            $validated['created_by'] = auth()->id();
            $asset = Asset::create($validated);

            // Create asset history record for the new asset
            $historyRemarks = $isFromNonRegistered 
                ? 'Asset registered from non-registered pulled out status. Previously pulled out for repair.'
                : 'New asset added to the system';
                
            AssetHistory::create([
                'asset_id' => $asset->id,
                'change_type' => 'CREATED',
                'old_value' => null,
                'new_value' => $isFromNonRegistered ? 'Asset registered from non-registered status' : 'Asset created',
                'remarks' => $historyRemarks,
                'changed_by' => auth()->id()
            ]);

            // If this is a non-registered asset being registered, link the repair history
            if ($isFromNonRegistered) {
                $this->linkNonRegisteredAssetHistory($asset, $request);
            }

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
                $message = $isFromNonRegistered 
                    ? 'Asset registered successfully from non-registered status'
                    : 'Asset added successfully';
                return response()->json(['success' => true, 'message' => $message]);
            }

            $successMessage = $isFromNonRegistered 
                ? 'Asset has been registered successfully from non-registered pulled out status'
                : 'Asset has been added successfully';

            return redirect()->route($this->getRedirectRoute())
                ->with('success', $successMessage);
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

    public function create(Request $request)
    {
        $categories = \App\Models\Category::all();
        $vendors = \App\Models\Vendor::orderBy('name')->get();
        $locations = \App\Models\Location::orderBy('building')->orderBy('floor')->orderBy('room_number')->get();
        
        // Check if this is coming from a non-registered asset context
        $fromNonRegistered = $request->has('from_non_registered') && $request->from_non_registered;
        $status = $request->get('status', '');
        
        return view('add-asset', compact('categories', 'vendors', 'locations', 'fromNonRegistered', 'status'));
    }

    public function qrList(Request $request)
    {
        $query = Asset::with(['category', 'location', 'vendor']);

        // Apply date range filter if provided
        if ($request->has('date_from') && $request->date_from) {
            $query->where('purchase_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('purchase_date', '<=', $request->date_to);
        }

        // Apply other filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        $assets = $query->orderBy('purchase_date', 'desc')->get();
        
        // Get filter options for the form
        $categories = Category::all();
        
        return view('qr-list', compact('assets', 'categories'));
    }

    public function previewQrCodes(Request $request)
    {
        $query = Asset::with(['category', 'location', 'vendor']);

        // Apply date range filter if provided
        if ($request->has('date_from') && $request->date_from) {
            $query->where('purchase_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('purchase_date', '<=', $request->date_to);
        }

        // If specific items are selected, use those
        if ($request->has('selected_items') && $request->selected_items) {
            $selectedIds = json_decode($request->selected_items);
            $query->whereIn('id', $selectedIds);
        }

        $assets = $query->orderBy('purchase_date', 'desc')->get();

        $pdf = PDF::loadView('pdf.qr-codes', compact('assets'));
        return $pdf->stream('qr-codes-preview.pdf');
    }

    public function exportQrCodes(Request $request)
    {
        $query = Asset::with(['category', 'location', 'vendor']);

        // Apply date range filter if provided
        if ($request->has('date_from') && $request->date_from) {
            $query->where('purchase_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('purchase_date', '<=', $request->date_to);
        }

        // If specific items are selected, use those
        if ($request->has('selected_items') && $request->selected_items) {
            $selectedIds = json_decode($request->selected_items);
            $query->whereIn('id', $selectedIds);
        }

        $assets = $query->orderBy('purchase_date', 'desc')->get();

        $filename = 'asset-qrcodes-' . now()->format('Y-m-d') . '.pdf';
        $pdf = PDF::loadView('pdf.qr-codes', compact('assets'));
        return $pdf->download($filename);
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

        return redirect()->route($this->getRedirectRoute())
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

        $redirectRoute = $request->input('redirect');
        if (!$redirectRoute) {
            $redirectRoute = auth()->user()->group_id === 4 
                ? route('custodian.assets.index')
                : route('reports.disposal-history');
        }
        
        return redirect($redirectRoute)
            ->with('success', 'Asset has been marked as disposed.');
    }

    public function edit(Asset $asset)
    {
        $categories = Category::all();
        $vendors = \App\Models\Vendor::orderBy('name')->get();
        $locations = \App\Models\Location::orderBy('building')->orderBy('floor')->orderBy('room_number')->get();
        return view('assets.edit', compact('asset', 'categories', 'vendors', 'locations'));
    }

    public function update(Request $request, Asset $asset)
    {
        // Only validate fields that are actually present and different
        $validationRules = [
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'purchase_price' => 'required|numeric',
            'status' => 'required|in:IN USE,UNDER REPAIR,UPGRADE,PULLED OUT',
            'model' => 'required',
            'specification' => 'required',
            'vendor_id' => 'required|exists:vendors,id',
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

        // Handle acquisition document update
        if ($request->hasFile('acquisition_document')) {
            $acquisitionPath = $request->file('acquisition_document')->store('assets/acquisition_docs', 'public');
            $asset->acquisition_document = $acquisitionPath;
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

        return redirect()->route($this->getRedirectRoute())
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
                    return $query->whereHas('location', function($locationQuery) use ($lab) {
                        $locationQuery->where('room_number', $lab);
                    });
                })
                ->with(['category', 'location'])
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
                'location' => $asset->location ? $asset->location->full_location : 'N/A',
                'location_id' => $asset->location_id,
                'category_id' => $asset->category_id,
                'category' => $asset->category,
                'serial_number' => $asset->serial_number,
                'status' => $asset->status,
                'model' => $asset->model,
                'specification' => $asset->specification,
                'vendor_id' => $asset->vendor_id,
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

    /**
     * Link repair history from non-registered asset to the newly registered asset
     */
    private function linkNonRegisteredAssetHistory(Asset $asset, Request $request)
    {
        try {
            \Log::info('Starting to link non-registered asset history', [
                'asset_id' => $asset->id,
                'asset_name' => $asset->name,
                'asset_location' => $asset->location->building . ' - ' . $asset->location->floor . ' - ' . $asset->location->room_number,
                'search_criteria' => [
                    'equipment_name' => $asset->name,
                    'status' => 'PULLED OUT',
                    'has_ticket' => true,
                    'note' => 'Ignoring repair request location, using asset registration location'
                ]
            ]);

            // Find the non-registered asset that matches this equipment name
            // Ignore the repair request location since it might be informal/incorrect
            // Use the asset registration location instead
            // This ensures repair history is linked regardless of location mismatches
            $nonRegisteredAsset = \App\Models\NonRegisteredAsset::where('equipment_name', $asset->name)
                ->where('status', 'PULLED OUT')
                ->whereNotNull('ticket_number')
                ->latest('pulled_out_at')
                ->first();

            \Log::info('Non-registered asset search result', [
                'found' => $nonRegisteredAsset ? true : false,
                'non_registered_asset_id' => $nonRegisteredAsset ? $nonRegisteredAsset->id : null,
                'ticket_number' => $nonRegisteredAsset ? $nonRegisteredAsset->ticket_number : null
            ]);

            if ($nonRegisteredAsset && $nonRegisteredAsset->ticket_number) {
                // Find the repair request
                $repairRequest = \App\Models\RepairRequest::where('ticket_number', $nonRegisteredAsset->ticket_number)->first();
                
                if ($repairRequest) {
                    // Create repair history record for the new asset
                    $repairHistory = AssetHistory::create([
                        'asset_id' => $asset->id,
                        'change_type' => 'REPAIR',
                        'old_value' => $repairRequest->ticket_number, // Store ticket number in old_value for easy access
                        'new_value' => 'pulled_out',
                        'remarks' => $nonRegisteredAsset->remarks ?? 'Asset was previously non-registered and pulled out for repair',
                        'changed_by' => auth()->id()
                    ]);

                    \Log::info('Created repair history record', [
                        'history_id' => $repairHistory->id,
                        'asset_id' => $repairHistory->asset_id,
                        'change_type' => $repairHistory->change_type
                    ]);

                    // Create status change record showing the asset was pulled out
                    $statusHistory = AssetHistory::create([
                        'asset_id' => $asset->id,
                        'change_type' => 'STATUS',
                        'old_value' => 'IN USE',
                        'new_value' => 'PULLED OUT',
                        'remarks' => "Asset pulled out for repair on " . $nonRegisteredAsset->pulled_out_at->format('M d, Y') . ". Ticket: {$repairRequest->ticket_number}",
                        'changed_by' => auth()->id()
                    ]);

                    \Log::info('Created status history record', [
                        'history_id' => $statusHistory->id,
                        'asset_id' => $statusHistory->asset_id,
                        'change_type' => $statusHistory->change_type
                    ]);

                    // Update the non-registered asset to link it to the new asset
                    $nonRegisteredAsset->update([
                        'linked_asset_id' => $asset->id,
                        'linked_at' => now()
                    ]);

                    // Update the repair request to link it to the new asset
                    $repairRequest->update([
                        'serial_number' => $asset->serial_number,
                        'status' => 'pulled_out' // Ensure status reflects the asset is now registered but still pulled out
                    ]);

                    \Log::info('Successfully linked non-registered asset history to new asset', [
                        'asset_id' => $asset->id,
                        'non_registered_asset_id' => $nonRegisteredAsset->id,
                        'ticket_number' => $repairRequest->ticket_number,
                        'repair_request_updated' => true
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error linking non-registered asset history: ' . $e->getMessage(), [
                'asset_id' => $asset->id,
                'error' => $e->getMessage()
            ]);
            // Don't throw the exception - this is not critical for asset creation
        }
    }

    /**
     * Get the appropriate redirect route based on user role
     */
    private function getRedirectRoute()
    {
        if (auth()->user()->group_id === 4) {
            return 'custodian.assets.index';
        }
        return 'assets.index';
    }
}
