<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\RepairRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Keep existing statistics
        $totalAssetValue = Asset::where('status', '!=', 'DISPOSED')->sum('purchase_price');
        $disposedAssets = Asset::where('status', 'DISPOSED')
            ->where('disposal_date', '>=', Carbon::now()->subDays(30))
            ->count();

        // Add repair request statistics
        $totalOpen = RepairRequest::whereIn('status', ['pending', 'urgent', 'in_progress'])->count();
        $completedThisMonth = RepairRequest::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->count();
        $avgResponseTime = RepairRequest::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->select(DB::raw('ROUND(AVG(DATEDIFF(completed_at, created_at)), 1) as avg_days'))
            ->value('avg_days') ?? 0;
        
        // Get urgent repairs
        $urgentRepairs = RepairRequest::where('status', 'urgent')
            ->latest()
            ->take(3)
            ->get();

        // Keep existing warranty check
        $warrantyExpiringAssets = Asset::select('*')
            ->selectRaw('DATEDIFF(warranty_period, CURDATE()) as days_until_warranty_expires')
            ->whereNotNull('warranty_period')
            ->where('warranty_period', '>', now())
            ->where('warranty_period', '<=', now()->addMonths(3))
            ->where('status', '!=', 'DISPOSED')
            ->orderBy('warranty_period')
            ->limit(5)
            ->get();

        // Get all months of the current year
        $months = collect(range(1, 12))->map(function ($month) {
            return [
                'month_num' => $month,
                'month' => Carbon::create(null, $month, 1)->format('M'),
                'procurement_count' => 0,
                'procurement_value' => 0,
                'disposal_count' => 0
            ];
        })->toArray();

        // Get procurement data
        $procurements = Asset::select(
            DB::raw('MONTH(purchase_date) as month_num'),
            DB::raw('COUNT(*) as procurement_count'),
            DB::raw('SUM(purchase_price) as procurement_value')
        )
        ->whereYear('purchase_date', Carbon::now()->year)
        ->whereNotNull('purchase_date')
        ->groupBy('month_num')
        ->get();

        // Get disposal data
        $disposals = Asset::select(
            DB::raw('MONTH(disposal_date) as month_num'),
            DB::raw('COUNT(*) as disposal_count')
        )
        ->whereYear('disposal_date', Carbon::now()->year)
        ->where('status', 'DISPOSED')
        ->whereNotNull('disposal_date')
        ->groupBy('month_num')
        ->get();

        // Merge data into months array
        foreach ($procurements as $proc) {
            $index = $proc->month_num - 1;
            $months[$index]['procurement_count'] = $proc->procurement_count;
            $months[$index]['procurement_value'] = $proc->procurement_value;
        }

        foreach ($disposals as $disp) {
            $index = $disp->month_num - 1;
            $months[$index]['disposal_count'] = $disp->disposal_count;
        }

        $monthlyData = array_values($months);

        return view('dashboard', compact(
            'totalAssetValue',
            'disposedAssets',
            'monthlyData',
            'warrantyExpiringAssets',
            'totalOpen',
            'completedThisMonth',
            'avgResponseTime',
            'urgentRepairs'
        ));
    }
}