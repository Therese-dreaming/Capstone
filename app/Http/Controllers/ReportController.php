<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\AssetHistory;
use App\Models\Maintenance;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function categoryReport()
    {
        $categories = Category::with(['assets' => function($query) {
            $query->where('status', '!=', 'DISPOSED');
        }])->get();
        
        // Calculate total summary
        $totalSummary = [
            'total_assets' => Asset::where('status', '!=', 'DISPOSED')->count(),
            'total_value' => Asset::where('status', '!=', 'DISPOSED')->sum('purchase_price')
        ];

        $categoryStats = $categories->map(function ($category) {
            $activeAssets = $category->assets->where('status', '!=', 'DISPOSED');
            return [
                'name' => $category->name,
                'count' => $activeAssets->count(),
                'total_value' => $activeAssets->sum('purchase_price')
            ];
        });

        return view('reports.category', compact('categories', 'categoryStats', 'totalSummary'));
    }

    public function locationReport()
    {
        $assets = Asset::where('status', '!=', 'DISPOSED')->get();
        
        // Calculate total summary
        $totalSummary = [
            'total_assets' => $assets->count(),
            'total_value' => $assets->sum('purchase_price')
        ];

        // Group assets by location
        $locationStats = $assets->groupBy('location')
            ->map(function ($locationAssets) {
                return [
                    'location' => $locationAssets->first()->location,
                    'count' => $locationAssets->count(),
                    'total_value' => $locationAssets->sum('purchase_price')
                ];
            })->values();

        return view('reports.location', compact('locationStats', 'totalSummary'));
    }

    public function locationDetails($location)
    {
        $assets = Asset::where('location', $location)->with('category')->get();
        return view('reports.location-details', compact('location', 'assets'));
    }

    public function assetCategoryReport()
    {
        $categories = Category::with('assets')->get();

        $categoryStats = $categories->map(function ($category) {
            return [
                'name' => $category->name,
                'count' => $category->assets->count(),
                'total_value' => $category->assets->sum('purchase_price')
            ];
        });

        return view('reports.category', compact('categories', 'categoryStats'));
    }

    public function categoryDetails(Category $category)
    {
        $assets = $category->assets()->with('category')->get();
        return view('reports.category-details', compact('category', 'assets'));
    }

    public function assetHistory(Asset $asset)
    {
        // Add debug logging
        \Log::info('Fetching history for asset: ' . $asset->id);

        // Get general history with eager loading
        $history = AssetHistory::with(['asset', 'user'])
            ->where('asset_id', $asset->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get maintenance history - use the correct model namespace
        $assetMaintenances = \App\Models\Maintenance::where('lab_number', substr($asset->location, -3))
            ->where('status', 'completed')
            ->where(function($query) use ($asset) {
                $query->whereNull('excluded_assets')
                    ->orWhereRaw('NOT JSON_CONTAINS(excluded_assets, ?)', ['"' . $asset->id . '"']);
            })
            ->orderBy('scheduled_date', 'desc')
            ->get();

        // Debug the history records
        \Log::info('History records:', [
            'total_records' => $history->count(),
            'repair_records' => $history->where('change_type', 'REPAIR')->count(),
            'all_change_types' => $history->pluck('change_type')->unique()->toArray()
        ]);

        $history = $history->groupBy('change_type');

        return view('reports.asset-history', compact('asset', 'history', 'assetMaintenances'));
    }

    public function procurementHistory(Request $request)
    {
        $query = Asset::with('category')->orderBy('purchase_date', 'desc');

        if ($request->filled('start_date')) {
            $query->where('purchase_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('purchase_date', '<=', $request->end_date);
        }

        $assets = $query->get();

        return view('reports.procurement-history', compact('assets'));
    }

    public function disposalHistory(Request $request)
    {
        $query = Asset::with('category')
            ->where('status', 'DISPOSED')
            ->orderBy('disposal_date', 'desc');
    
        if ($request->filled('start_date')) {
            $query->where('disposal_date', '>=', $request->start_date);
        }
    
        if ($request->filled('end_date')) {
            $query->where('disposal_date', '<=', $request->end_date);
        }
    
        $disposedAssets = $query->get();
    
        return view('reports.disposal-history', compact('disposedAssets'));
    }
}
