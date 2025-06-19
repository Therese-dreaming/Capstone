<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors.
     */
    public function index()
    {
        $vendors = Vendor::withCount('assets')->withSum('assets', 'purchase_price')->get();
        return view('vendors.index', compact('vendors'));
    }

    /**
     * Add a new vendor via AJAX (similar to maintenance tasks).
     */
    public function addNewVendor(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:vendors'
        ]);

        try {
            $vendor = Vendor::create([
                'name' => $request->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vendor added successfully',
                'vendor' => [
                    'id' => $vendor->id,
                    'name' => $vendor->name
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add vendor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all vendors for dropdown.
     */
    public function getAllVendors(Request $request): JsonResponse
    {
        $query = Vendor::query();
        
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $vendors = $query->orderBy('name')->get(['id', 'name']);
        return response()->json($vendors);
    }

    /**
     * Remove the specified vendor from storage.
     */
    public function destroy(Vendor $vendor)
    {
        // Check if vendor has assets
        if ($vendor->assets()->count() > 0) {
            return redirect()->route('vendors.index')
                ->with('error', 'Cannot delete vendor with existing assets. Please reassign or remove the assets first.');
        }

        $vendorName = $vendor->name;
        $vendor->delete();

        return redirect()->route('vendors.index')
            ->with('success', "Vendor '{$vendorName}' has been deleted successfully.");
    }

    /**
     * AJAX: Show detailed vendor analysis for modal.
     */
    public function vendorDetails($vendorId)
    {
        $vendor = \App\Models\Vendor::findOrFail($vendorId);
        $assets = $vendor->assets()->with('category')->get();
        $totalAssets = $assets->count();
        $totalValue = $assets->sum('purchase_price');

        // Repairs
        $repairRequests = \App\Models\RepairRequest::whereIn('serial_number', $assets->pluck('serial_number'))->get();
        $totalRepairs = $repairRequests->count();
        $completedRepairs = $repairRequests->where('status', 'completed')->count();
        $pendingRepairs = $repairRequests->where('status', '!=', 'completed')->count();

        // Rates
        $repairRate = $totalAssets > 0 ? ($totalRepairs / $totalAssets) * 100 : 0;
        $completionRate = $totalRepairs > 0 ? ($completedRepairs / $totalRepairs) * 100 : 0;
        $operationalRate = $totalAssets > 0 ? (($totalAssets - $assets->where('status', 'under_repair')->count()) / $totalAssets) * 100 : 0;

        // Reliability
        if ($repairRate <= 10 && $completionRate >= 90 && $operationalRate >= 80) {
            $reliabilityRating = 'High';
        } elseif ($repairRate <= 25 && $completionRate >= 75 && $operationalRate >= 60) {
            $reliabilityRating = 'Medium';
        } else {
            $reliabilityRating = 'Low';
        }

        // Status breakdown
        $statusBreakdown = [
            'in_use' => $assets->where('status', 'in_use')->count(),
            'under_repair' => $assets->where('status', 'under_repair')->count(),
            'pulled_out' => $assets->where('status', 'pulled_out')->count(),
            'disposed' => $assets->where('status', 'disposed')->count(),
        ];

        // Age distribution
        $now = now();
        $ageGroups = [
            '0-1 years' => 0,
            '1-3 years' => 0,
            '3-5 years' => 0,
            '5+ years' => 0,
        ];
        foreach ($assets as $asset) {
            if (!$asset->purchase_date) continue;
            $years = $now->diffInYears(\Carbon\Carbon::parse($asset->purchase_date));
            if ($years <= 1) $ageGroups['0-1 years']++;
            elseif ($years <= 3) $ageGroups['1-3 years']++;
            elseif ($years <= 5) $ageGroups['3-5 years']++;
            else $ageGroups['5+ years']++;
        }

        // Category breakdown
        $categoryBreakdown = collect();
        foreach ($assets->groupBy('category.name') as $categoryName => $group) {
            $categoryBreakdown[$categoryName] = [
                'count' => $group->count(),
                'value' => $group->sum('purchase_price'),
                'repair_count' => $repairRequests->whereIn('serial_number', $group->pluck('serial_number'))->count(),
            ];
        }

        // Recent repairs
        $recentRepairs = $repairRequests->sortByDesc('created_at')->take(5)->map(function($repair) {
            return [
                'asset_name' => optional($repair->asset)->name,
                'serial_number' => $repair->serial_number,
                'status' => $repair->status,
                'created_at' => $repair->created_at,
                'completed_at' => $repair->completed_at,
            ];
        });

        // Recommendations (simple example)
        $recommendations = [];
        if ($repairRate > 25) $recommendations[] = 'Consider reviewing asset quality or maintenance schedules.';
        if ($completionRate < 75) $recommendations[] = 'Improve repair completion rate for better reliability.';
        if ($operationalRate < 60) $recommendations[] = 'Increase operational assets to improve uptime.';
        if (empty($recommendations)) $recommendations[] = 'Vendor performance is within optimal range.';

        return view('reports.vendor-details', compact(
            'vendor',
            'totalAssets',
            'totalValue',
            'totalRepairs',
            'completedRepairs',
            'pendingRepairs',
            'repairRate',
            'completionRate',
            'operationalRate',
            'reliabilityRating',
            'statusBreakdown',
            'ageGroups',
            'categoryBreakdown',
            'recentRepairs',
            'recommendations'
        ));
    }
}
