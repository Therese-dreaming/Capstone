<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\RepairRequest;
use App\Models\LabLog;
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

        // Lab Utilization Analysis
        $labUsageData = LabLog::select(
            'laboratory',
            DB::raw('SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) as hours')
        )
            ->whereMonth('date', Carbon::now()->month)
            ->groupBy('laboratory')
            ->orderBy('hours', 'desc')
            ->get();

        // Calculate total lab hours
        $totalLabHours = $labUsageData->sum('hours');

        // Get most used lab
        $mostUsedLab = $labUsageData->first() ? "Lab " . $labUsageData->first()->laboratory : "N/A";

        // Calculate average daily usage
        $daysInMonth = Carbon::now()->daysInMonth;
        $avgDailyUsage = $totalLabHours > 0 ? round($totalLabHours / $daysInMonth, 1) : 0;

        // Department Usage Analysis
        $deptUsageData = LabLog::select(
            'subject_course as department',
            DB::raw('SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) as hours')
        )
            ->whereMonth('date', Carbon::now()->month)
            ->groupBy('subject_course')
            ->orderBy('hours', 'desc')
            ->get();

        $assets = Asset::where('status', '!=', 'DISPOSED')->get();

        $criticalAndWarningAssets = $assets->map(function ($asset) {
            // Calculate useful life using straight-line depreciation
            $purchasePrice = $asset->purchase_price;
            $salvageValue = $purchasePrice * 0.1; // 10% salvage value

            $purchaseDate = Carbon::parse($asset->purchase_date);
            $warrantyDate = Carbon::parse($asset->warranty_period);
            $warrantyYears = $purchaseDate->diffInYears($warrantyDate);

            if ($warrantyYears <= 0) {
                $warrantyYears = 1;
            }

            $annualDepreciation = ($purchasePrice - $salvageValue) / $warrantyYears;

            if ($annualDepreciation <= 0) {
                $usefulLife = $warrantyYears;
            } else {
                $usefulLife = round(($purchasePrice - $salvageValue) / $annualDepreciation, 2);
            }

            // Calculate end of life date and days remaining
            $endOfLife = $purchaseDate->copy()->addYears($usefulLife);
            $daysLeft = now()->diffInDays($endOfLife, false);

            // Update status calculation to match AssetController logic
            $percentage = ($daysLeft / ($usefulLife * 365)) * 100;
            
            if ($percentage <= 10) {
                $asset->life_status = 'critical';
            } elseif ($percentage <= 25) {
                $asset->life_status = 'warning';
            } else {
                $asset->life_status = 'good';
            }

            $asset->end_of_life_date = $endOfLife;
            $asset->days_left = $daysLeft;

            return $asset;
        })
        ->filter(function ($asset) {
            // Only show assets with warning (≤25%) or critical (≤10%) status
            return $asset->life_status === 'critical' || $asset->life_status === 'warning';
        })
        ->sortBy('days_left')
        ->take(5);

        return view('dashboard', compact(
            'totalAssetValue',
            'disposedAssets',
            'monthlyData',
            'warrantyExpiringAssets',
            'totalOpen',
            'completedThisMonth',
            'avgResponseTime',
            'urgentRepairs',
            'labUsageData',
            'deptUsageData',
            'totalLabHours',
            'mostUsedLab',
            'avgDailyUsage',
            'criticalAndWarningAssets'
        ));
    }
}
