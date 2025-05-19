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
            ->select(DB::raw('ROUND(AVG(ABS(TIMESTAMPDIFF(HOUR, created_at, completed_at))), 1) as avg_hours'))
            ->value('avg_hours') ?? 0;

        // Convert to days if more than 24 hours
        $avgResponseDays = $avgResponseTime >= 24 ? round($avgResponseTime / 24, 1) : null;

        // Get urgent repairs
        $urgentRepairs = RepairRequest::where('status', 'urgent')
            ->latest()
            ->take(3)
            ->get();

        // Get assets with expiring warranties
        $warrantyExpiringAssets = Asset::select('*')
            ->selectRaw('DATEDIFF(warranty_period, CURDATE()) as days_until_warranty_expires')
            ->whereNotNull('warranty_period')
            ->where('status', '!=', 'DISPOSED')
            ->where(function($query) {
                $query->where('warranty_period', '<=', now()->addMonths(3))
                      ->orWhere('warranty_period', '<', now());
            })
            ->orderBy('warranty_period')
            ->limit(5)
            ->get();

        // Get critical and warning assets using stored values
        $criticalAndWarningAssets = Asset::where('status', '!=', 'DISPOSED')
            ->whereIn('life_status', ['critical', 'warning'])
            ->orderBy('remaining_life')
            ->limit(5)
            ->get()
            ->each(function ($asset) {
                $asset->days_left = $asset->end_of_life_date
                    ? now()->diffInDays($asset->end_of_life_date, false)
                    : 0;
            });

        // Keep the rest of the method unchanged
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
            DB::raw('ROUND(SUM(TIME_TO_SEC(TIMEDIFF(time_out, time_in))) / 3600, 1) as hours'),
            DB::raw('COUNT(DISTINCT DATE(time_in)) as days_used')
        )
            ->where('time_in', '>=', Carbon::now()->startOfMonth())
            ->where('time_in', '<=', Carbon::now()->endOfMonth())
            ->where('status', 'completed')
            ->whereNotNull('time_out')
            ->groupBy('laboratory')
            ->orderBy('hours', 'desc')
            ->get();

        // Calculate total lab hours
        $totalLabHours = $labUsageData->sum('hours');

        // Get most used lab with days count
        $mostUsedLab = $labUsageData->first() 
            ? sprintf("Lab %s (%d days this month)", 
                $labUsageData->first()->laboratory,
                $labUsageData->first()->days_used)
            : "N/A";

        // Calculate average daily usage more accurately
        $daysInMonth = Carbon::now()->daysInMonth;
        $avgDailyUsage = $totalLabHours > 0 ? round($totalLabHours / $daysInMonth, 1) : 0;

        // Calculate peak usage hour more accurately
        $peakUsageHours = LabLog::select(
            DB::raw('HOUR(time_in) as hour'),
            DB::raw('COUNT(*) as count')
        )
            ->where('time_in', '>=', Carbon::now()->startOfMonth())
            ->where('time_in', '<=', Carbon::now()->endOfMonth())
            ->where('status', 'completed')
            ->whereNotNull('time_out')
            ->groupBy(DB::raw('HOUR(time_in)'))
            ->orderBy('count', 'desc')
            ->first();

        $peakHour = $peakUsageHours ? $peakUsageHours->hour : null;

        // Department Usage Analysis with percentage calculation
        // Department Usage Analysis with user grouping instead of subject_course
        $deptUsageData = LabLog::select(
            'users.position as department', // Using user position as department
            DB::raw('SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) as hours'),
            DB::raw('(SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) / ' . ($totalLabHours ?: 1) . ' * 100) as usage_percentage')
        )
            ->join('users', 'lab_logs.user_id', '=', 'users.id')
            ->where('time_in', '>=', Carbon::now()->startOfMonth())
            ->where('time_in', '<=', Carbon::now()->endOfMonth())
            ->groupBy('users.position')
            ->orderBy('hours', 'desc')
            ->get();

        return view('dashboard', compact(
            'totalAssetValue',
            'disposedAssets',
            'monthlyData',
            'warrantyExpiringAssets',
            'totalOpen',
            'completedThisMonth',
            'avgResponseTime',
            'avgResponseDays',
            'urgentRepairs',
            'labUsageData',
            'deptUsageData',
            'totalLabHours',
            'mostUsedLab',
            'avgDailyUsage',
            'criticalAndWarningAssets',
            'peakHour' // Add this variable to the view
        ));
    }
}
