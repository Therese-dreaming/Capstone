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
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('specification', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('location') && $request->location) {
            $query->where('location_id', $request->location);
        }

        // Add warranty filter
        if ($request->has('warranty') && $request->warranty) {
            $today = Carbon::now();
            
            switch ($request->warranty) {
                case 'expiring_365':
                    $query->whereNotNull('warranty_period')
                          ->where('warranty_period', '>', $today->format('Y-m-d'))
                          ->where('warranty_period', '<=', $today->copy()->addDays(365)->format('Y-m-d'));
                    break;
                case 'expiring_30':
                    $query->whereNotNull('warranty_period')
                          ->where('warranty_period', '>', $today->format('Y-m-d'))
                          ->where('warranty_period', '<=', $today->copy()->addDays(30)->format('Y-m-d'));
                    break;
                case 'expired':
                    $query->whereNotNull('warranty_period')
                          ->where('warranty_period', '<', $today->format('Y-m-d'));
                    break;
            }
        }

        // Add date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->where('purchase_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('purchase_date', '<=', $request->date_to);
        }

        // Order by created_at descending (recently added first)
        $query->orderBy('created_at', 'desc');

        // Get the assets with pagination
        $assets = $query->paginate(10)->withQueryString();

        // Get filter data
        $categories = Category::all();
        $locations = Location::orderBy('building')->orderBy('floor')->orderBy('room_number')->get();

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
    
        // Get technicians for filter
        $technicians = \App\Models\User::whereIn('group_id', [1, 2])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('asset-list', compact('assets', 'categories', 'locations', 'technicians'));
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
                'status' => 'required|in:IN USE,UNDER REPAIR,PULLED OUT,LOST,DISPOSED',
                'model' => 'required|string|max:255',
                'specification' => 'required|string',
                'vendor_id' => 'required|exists:vendors,id',
                'purchase_date' => 'required|date',
                'warranty_period' => 'required|date|after_or_equal:purchase_date',
                'purchase_price' => 'required|numeric|min:0',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'acquisition_document' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:10240',
                'quantity' => 'nullable|integer|min:1|max:100',
            ]);

            // For non-registered assets, use the dynamic status passed from repair request
            // Don't override the status - it should already be set correctly based on repair status

            // Ensure the category exists
            $category = Category::findOrFail($validated['category_id']);

            // Upload once; reuse for bulk
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('assets', 'public');
                $validated['photo'] = $photoPath;
            }

            // Handle acquisition document upload
            if ($request->hasFile('acquisition_document')) {
                $acquisitionPath = $request->file('acquisition_document')->store('assets/acquisition_docs', 'public');
                $validated['acquisition_document'] = $acquisitionPath;
            }

            // Determine quantity (default 1). Lock to 1 when from non-registered flow
            $quantity = (int) ($validated['quantity'] ?? $request->input('quantity', 1));
            if ($isFromNonRegistered) {
                $quantity = 1;
            }
            if ($quantity < 1) { $quantity = 1; }
            if ($quantity > 100) { $quantity = 100; }

            $validated['created_by'] = auth()->id();

            $createdAssets = [];
            for ($i = 0; $i < $quantity; $i++) {
                // Create each asset
                $asset = Asset::create($validated);

                // Create asset history record for the new asset
                $historyRemarks = $isFromNonRegistered 
                    ? 'Asset registered from repair request with status: ' . $validated['status'] . '. Automatically linked to repair request.'
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

                $createdAssets[] = $asset->id;
            }

            if ($request->wantsJson()) {
                $message = $isFromNonRegistered 
                    ? 'Asset registered successfully with status: ' . $validated['status'] . ' and linked to repair request'
                    : (count($createdAssets) > 1 ? (count($createdAssets) . ' assets added successfully') : 'Asset added successfully');
                return response()->json(['success' => true, 'message' => $message, 'created_ids' => $createdAssets]);
            }

            $successMessage = $isFromNonRegistered 
                ? 'Asset has been registered successfully with status: ' . $validated['status'] . ' and automatically linked to the repair request'
                : (count($createdAssets) > 1 ? (count($createdAssets) . ' assets have been added successfully') : 'Asset has been added successfully');

            // If this is from a repair request, redirect back to the repair details page
            if ($isFromNonRegistered && $request->has('repair_request_id') && $request->repair_request_id) {
                return redirect()->route('repair.show', $request->repair_request_id)
                    ->with('success', $successMessage);
            }

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
        $repairRequestId = $request->get('repair_request_id', '');
        
        return view('add-asset', compact('categories', 'vendors', 'locations', 'fromNonRegistered', 'status', 'repairRequestId'));
    }

    public function qrList(Request $request)
    {
        $query = Asset::with(['category', 'location', 'vendor', 'creator']);

        // Apply filters if they exist
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('specification', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('location') && $request->location) {
            $query->where('location_id', $request->location);
        }

        // Add warranty filter
        if ($request->has('warranty') && $request->warranty) {
            $today = Carbon::now();
            
            switch ($request->warranty) {
                case 'expiring_365':
                    $query->whereNotNull('warranty_period')
                          ->where('warranty_period', '>', $today->format('Y-m-d'))
                          ->where('warranty_period', '<=', $today->copy()->addDays(365)->format('Y-m-d'));
                    break;
                case 'expiring_30':
                    $query->whereNotNull('warranty_period')
                          ->where('warranty_period', '>', $today->format('Y-m-d'))
                          ->where('warranty_period', '<=', $today->copy()->addDays(30)->format('Y-m-d'));
                    break;
                case 'expired':
                    $query->whereNotNull('warranty_period')
                          ->where('warranty_period', '<', $today->format('Y-m-d'));
                    break;
            }
        }

        // Add date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->where('purchase_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('purchase_date', '<=', $request->date_to);
        }

        // Order by created_at descending (recently added first)
        $query->orderBy('created_at', 'desc');

        // Get the assets with pagination
        $assets = $query->paginate(10)->withQueryString();

        // Get filter data
        $categories = Category::all();
        $locations = Location::orderBy('building')->orderBy('floor')->orderBy('room_number')->get();
        
        return view('qr-list', compact('assets', 'categories', 'locations'));
    }

    public function previewQrCodes(Request $request)
    {
        $query = Asset::with(['category', 'location', 'vendor', 'creator']);

        // Apply filters if they exist
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('specification', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('location') && $request->location) {
            $query->where('location_id', $request->location);
        }

        // Add warranty filter
        if ($request->has('warranty') && $request->warranty) {
            $today = Carbon::now();
            
            switch ($request->warranty) {
                case 'expiring_365':
                    $query->whereNotNull('warranty_period')
                          ->where('warranty_period', '>', $today->format('Y-m-d'))
                          ->where('warranty_period', '<=', $today->copy()->addDays(365)->format('Y-m-d'));
                    break;
                case 'expiring_30':
                    $query->whereNotNull('warranty_period')
                          ->where('warranty_period', '>', $today->format('Y-m-d'))
                          ->where('warranty_period', '<=', $today->copy()->addDays(30)->format('Y-m-d'));
                    break;
                case 'expired':
                    $query->whereNotNull('warranty_period')
                          ->where('warranty_period', '<', $today->format('Y-m-d'));
                    break;
            }
        }

        // Apply date range filter if provided
        if ($request->has('date_from') && $request->date_from) {
            $query->where('purchase_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('purchase_date', '<=', $request->date_to);
        }

        // If specific items are selected, use those
        if ($request->has('selected_items') && $request->selected_items) {
            $selectedItems = json_decode($request->selected_items);
            
            // If "all" is selected, don't add any additional where clause (use all filtered items)
            if ($selectedItems !== 'all') {
                $query->whereIn('id', $selectedItems);
            }
            // If $selectedItems === 'all', we use all items that match the current filters
        }

        $assets = $query->orderBy('created_at', 'desc')->get();

        $pdf = PDF::loadView('pdf.qr-codes', compact('assets'));
        return $pdf->stream('qr-codes-preview.pdf');
    }

    public function exportQrCodes(Request $request)
    {
        $query = Asset::with(['category', 'location', 'vendor', 'creator']);

        // Apply filters if they exist
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('specification', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('location') && $request->location) {
            $query->where('location_id', $request->location);
        }

        // Add warranty filter
        if ($request->has('warranty') && $request->warranty) {
            $today = Carbon::now();
            
            switch ($request->warranty) {
                case 'expiring_365':
                    $query->whereNotNull('warranty_period')
                          ->where('warranty_period', '>', $today->format('Y-m-d'))
                          ->where('warranty_period', '<=', $today->copy()->addDays(365)->format('Y-m-d'));
                    break;
                case 'expiring_30':
                    $query->whereNotNull('warranty_period')
                          ->where('warranty_period', '>', $today->format('Y-m-d'))
                          ->where('warranty_period', '<=', $today->copy()->addDays(30)->format('Y-m-d'));
                    break;
                case 'expired':
                    $query->whereNotNull('warranty_period')
                          ->where('warranty_period', '<', $today->format('Y-m-d'));
                    break;
            }
        }

        // Apply date range filter if provided
        if ($request->has('date_from') && $request->date_from) {
            $query->where('purchase_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('purchase_date', '<=', $request->date_to);
        }

        // If specific items are selected, use those
        if ($request->has('selected_items') && $request->selected_items) {
            $selectedItems = json_decode($request->selected_items);
            
            // If "all" is selected, don't add any additional where clause (use all filtered items)
            if ($selectedItems !== 'all') {
                $query->whereIn('id', $selectedItems);
            }
            // If $selectedItems === 'all', we use all items that match the current filters
        }

        $assets = $query->orderBy('created_at', 'desc')->get();

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
        // Only Admins (group_id = 1) can delete assets
        if (!auth()->check() || auth()->user()->group_id !== 1) {
            return redirect()->back()->withErrors(['error' => 'Unauthorized: Only admins can delete assets.']);
        }

        // Verify password confirmation
        request()->validate([
            'password' => 'required|string'
        ]);

        if (!\Hash::check(request('password'), auth()->user()->password)) {
            return redirect()->back()->withErrors(['error' => 'Password confirmation failed.']);
        }
        \Log::info('=== DELETE METHOD CALLED ===');
        \Log::info('Asset ID:', ['asset_id' => $asset->id]);
        \Log::info('Asset current status:', ['status' => $asset->status]);
        \Log::info('User ID:', ['user_id' => auth()->id()]);

        try {
            // Create a history record for deletion
            $historyData = [
                'asset_id' => $asset->id,
                'change_type' => 'STATUS',
                'old_value' => $asset->status,
                'new_value' => 'DELETED',
                'remarks' => 'Asset was deleted from the system',
                'changed_by' => auth()->id()
            ];

            \Log::info('Creating asset history with data:', $historyData);
            
            AssetHistory::create($historyData);

            \Log::info('Asset history created successfully');

            \Log::info('Deleting asset...');
            $asset->delete();

            \Log::info('Asset deleted successfully');

            $redirectRoute = $this->getRedirectRoute();
            \Log::info('Redirecting to:', ['route' => $redirectRoute]);

            return redirect()->route($redirectRoute)
                ->with('success', 'Asset has been deleted successfully');
                
        } catch (\Exception $e) {
            \Log::error('Error in destroy method:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $asset->id
            ]);
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while deleting the asset: ' . $e->getMessage()]);
        }
    }

    public function dispose(Request $request, Asset $asset)
    {
        \Log::info('=== DISPOSE METHOD CALLED ===');
        \Log::info('Request data:', $request->all());
        \Log::info('Asset ID:', ['asset_id' => $asset->id]);
        \Log::info('Asset current status:', ['status' => $asset->status]);
        \Log::info('User ID:', ['user_id' => auth()->id()]);

        try {
            $request->validate([
                'disposal_reason' => 'required|string|max:255'
            ]);

            \Log::info('Validation passed for disposal');

            // Store old status for history
            $oldStatus = $asset->status;

            // Update asset status and disposal information
            $updateData = [
                'status' => 'DISPOSED',
                'disposal_date' => now(),
                'disposal_reason' => $request->disposal_reason
            ];

            \Log::info('Updating asset with data:', $updateData);
            
            $asset->update($updateData);

            \Log::info('Asset updated successfully');

            // Create asset history record
            $historyData = [
                'asset_id' => $asset->id,
                'change_type' => 'disposal',
                'old_value' => $oldStatus,
                'new_value' => 'DISPOSED',
                'remarks' => $request->disposal_reason,
                'changed_by' => auth()->id()
            ];

            \Log::info('Creating asset history with data:', $historyData);
            
            AssetHistory::create($historyData);

            \Log::info('Asset history created successfully');

            $redirectRoute = $request->input('redirect');
            if (!$redirectRoute) {
                $redirectRoute = auth()->user()->group_id === 4 
                    ? route('custodian.assets.index')
                    : route('reports.disposal-history');
            }

            \Log::info('Redirecting to:', ['route' => $redirectRoute]);
            
            return redirect($redirectRoute)
                ->with('success', 'Asset has been marked as disposed.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in dispose method:', [
                'errors' => $e->errors(),
                'asset_id' => $asset->id
            ]);
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error in dispose method:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $asset->id
            ]);
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while disposing the asset: ' . $e->getMessage()]);
        }
    }

    public function markAsLost(Request $request, Asset $asset)
    {
        \Log::info('=== MARK AS LOST METHOD CALLED ===');
        \Log::info('Request data:', $request->all());
        \Log::info('Asset ID:', ['asset_id' => $asset->id]);
        \Log::info('Asset current status:', ['status' => $asset->status]);
        \Log::info('User ID:', ['user_id' => auth()->id()]);

        try {
            $request->validate([
                'lost_reason' => 'required|string|max:255'
            ]);

            \Log::info('Validation passed for mark as lost');

            // Store the old status
            $oldStatus = $asset->status;

            // Update asset status to LOST
            $updateData = [
                'status' => 'LOST',
                'lost_date' => now(),
                'lost_reason' => $request->lost_reason
            ];

            \Log::info('Updating asset with data:', $updateData);
            
            $asset->update($updateData);

            \Log::info('Asset updated successfully');

            // Create asset history record
            $historyData = [
                'asset_id' => $asset->id,
                'change_type' => 'STATUS',
                'old_value' => $oldStatus,
                'new_value' => 'LOST',
                'remarks' => $request->lost_reason,
                'changed_by' => auth()->id()
            ];

            \Log::info('Creating asset history with data:', $historyData);
            
            AssetHistory::create($historyData);

            \Log::info('Asset history created successfully');

            $redirectRoute = $request->input('redirect');
            if (!$redirectRoute) {
                $redirectRoute = auth()->user()->group_id === 4 
                    ? route('custodian.assets.index')
                    : route('assets.index');
            }

            \Log::info('Redirecting to:', ['route' => $redirectRoute]);
            
            return redirect($redirectRoute)
                ->with('success', 'Asset has been marked as lost.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in markAsLost method:', [
                'errors' => $e->errors(),
                'asset_id' => $asset->id
            ]);
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error in markAsLost method:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $asset->id
            ]);
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while marking the asset as lost: ' . $e->getMessage()]);
        }
    }

    public function markAsFound(Request $request, Asset $asset)
    {
        \Log::info('=== MARK AS FOUND METHOD CALLED ===');
        \Log::info('Request data:', $request->all());
        \Log::info('Asset ID:', ['asset_id' => $asset->id]);
        \Log::info('Asset current status:', ['status' => $asset->status]);
        \Log::info('User ID:', ['user_id' => auth()->id()]);

        try {
            // Check if user is creating a new location
            if ($request->has('new_building') && $request->has('new_floor') && $request->has('new_room')) {
                // Validate new location data
                $request->validate([
                    'new_building' => 'required|string|max:255',
                    'new_floor' => 'required|string|max:255',
                    'new_room' => 'required|string|max:255'
                ]);

                \Log::info('Creating new location for found asset');

                // Create new location
                $newLocation = \App\Models\Location::create([
                    'building' => $request->new_building,
                    'floor' => $request->new_floor,
                    'room_number' => $request->new_room
                ]);

                \Log::info('New location created:', ['location_id' => $newLocation->id, 'location' => $newLocation->full_location]);

                $foundLocationId = $newLocation->id;
            } else {
                // Validate existing location
                $request->validate([
                    'found_location_id' => 'required|exists:locations,id'
                ]);

                $foundLocationId = $request->found_location_id;
            }

            \Log::info('Validation passed for mark as found');

            // Store the old status and location
            $oldStatus = $asset->status;
            $oldLocation = $asset->location_id;

            // Get the new location
            $newLocation = \App\Models\Location::findOrFail($foundLocationId);

            // Update asset status to IN USE and set new location
            $updateData = [
                'status' => 'IN USE',
                'location_id' => $foundLocationId,
                'found_date' => now(),
                'lost_date' => null, // Clear lost date
                'lost_reason' => null // Clear lost reason
            ];

            \Log::info('Updating asset with data:', $updateData);
            
            $asset->update($updateData);

            \Log::info('Asset updated successfully');

            // Create asset history record for status change
            $statusHistoryData = [
                'asset_id' => $asset->id,
                'change_type' => 'STATUS',
                'old_value' => $oldStatus,
                'new_value' => 'IN USE',
                'remarks' => "Asset found and marked as in use at {$newLocation->full_location}",
                'changed_by' => auth()->id()
            ];

            \Log::info('Creating status history with data:', $statusHistoryData);
            AssetHistory::create($statusHistoryData);

            // Create asset history record for location change if location changed
            if ($oldLocation != $foundLocationId) {
                $locationHistoryData = [
                    'asset_id' => $asset->id,
                    'change_type' => 'LOCATION',
                    'old_value' => $oldLocation,
                    'new_value' => $foundLocationId,
                    'remarks' => "Asset location updated to {$newLocation->full_location} when found",
                    'changed_by' => auth()->id()
                ];

                \Log::info('Creating location history with data:', $locationHistoryData);
                AssetHistory::create($locationHistoryData);
            }

            \Log::info('Asset history created successfully');

            $redirectRoute = $request->input('redirect');
            if (!$redirectRoute) {
                $redirectRoute = auth()->user()->group_id === 4 
                    ? route('custodian.assets.index')
                    : route('assets.index');
            }

            \Log::info('Redirecting to:', ['route' => $redirectRoute]);
            
            $successMessage = $request->has('new_building') 
                ? "Asset has been marked as found and is now in use at the new location: {$newLocation->full_location}"
                : 'Asset has been marked as found and is now in use.';
            
            return redirect($redirectRoute)
                ->with('success', $successMessage);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in markAsFound method:', [
                'errors' => $e->errors(),
                'asset_id' => $asset->id
            ]);
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error in markAsFound method:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $asset->id
            ]);
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while marking the asset as found: ' . $e->getMessage()]);
        }
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
            'status' => 'required|in:IN USE,UNDER REPAIR,PULLED OUT,LOST,DISPOSED',
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
                ->whereNotIn('status', ['LOST', 'DISPOSED'])
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
                
                // Check if the serial number exists in any format (excluding LOST and DISPOSED)
                $similarAssets = Asset::where('serial_number', 'LIKE', '%' . $serialNumber . '%')
                    ->whereNotIn('status', ['LOST', 'DISPOSED'])
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
            // Direct approach: Use the repair_request_id passed from the frontend
            if ($request->has('repair_request_id') && $request->repair_request_id) {
                $repairRequest = \App\Models\RepairRequest::find($request->repair_request_id);
                
                if ($repairRequest) {
                    // Create repair history record for the new asset
                    $repairHistory = AssetHistory::create([
                        'asset_id' => $asset->id,
                        'change_type' => 'REPAIR',
                        'old_value' => $repairRequest->ticket_number, // Store ticket number in old_value for easy access
                        'new_value' => 'linked_to_repair',
                        'remarks' => 'Asset registered and linked to repair request: ' . $repairRequest->ticket_number . '. Equipment: ' . $repairRequest->equipment,
                        'changed_by' => auth()->id()
                    ]);

                    // Update the repair request to link it to the new asset
                    $repairRequest->update([
                        'serial_number' => $asset->serial_number
                        // Don't change the repair status - keep it as is (completed, in_progress, etc.)
                    ]);

                    // Find and update any related non-registered asset record if it exists
                    $nonRegisteredAsset = \App\Models\NonRegisteredAsset::where('ticket_number', $repairRequest->ticket_number)
                        ->where('equipment_name', $asset->name)
                        ->first();
                        
                    if ($nonRegisteredAsset) {
                        $nonRegisteredAsset->update([
                            'linked_asset_id' => $asset->id,
                            'linked_at' => now()
                        ]);
                    }
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
