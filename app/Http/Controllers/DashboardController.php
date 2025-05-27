<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\RepairRequest;
use App\Models\LabLog;
use App\Models\AssetHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Maintenance;
use Illuminate\Http\Request;

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
        // Department Usage Analysis with user grouping
        $deptUsageData = LabLog::select(
            'users.department as department', // Using actual department field
            DB::raw('SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) as hours'),
            DB::raw('(SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) / ' . ($totalLabHours ?: 1) . ' * 100) as usage_percentage')
        )
            ->join('users', 'lab_logs.user_id', '=', 'users.id')
            ->where('time_in', '>=', Carbon::now()->startOfMonth())
            ->where('time_in', '<=', Carbon::now()->endOfMonth())
            ->groupBy('users.department')
            ->orderBy('hours', 'desc')
            ->get();

        // Add this before the return statement
        $user = auth()->user();
        $personalStats = [
            'completed_repairs' => RepairRequest::where('technician_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'completed_maintenance' => Maintenance::where('technician_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'completed_repairs_history' => RepairRequest::with(['asset', 'category'])
                ->where('technician_id', $user->id)
                ->where('status', 'completed')
                ->whereNotNull('completed_at')
                ->orderBy('completed_at', 'desc')
                ->get(),
            'completed_maintenance_history' => Maintenance::with(['laboratory', 'maintenanceAssets'])
                ->where('technician_id', $user->id)
                ->where('status', 'completed')
                ->whereNotNull('completed_at')
                ->orderBy('completed_at', 'desc')
                ->get()
        ];

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
            'peakHour',
            'personalStats' // Add this to the compact list
        ));
    }

    public function secretaryDashboard()
    {
        $user = auth()->user();
        
        $personalStats = [
            'completed_repairs' => RepairRequest::where('technician_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'pending_repairs' => RepairRequest::where('technician_id', $user->id)
                ->whereIn('status', ['pending', 'urgent', 'in_progress'])
                ->get(),
            'completed_maintenance' => DB::table('maintenances')
                ->where('technician_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'upcoming_maintenance' => Maintenance::where('technician_id', $user->id)
                ->where('status', '!=', 'completed')
                ->where('scheduled_date', '>=', now())
                ->orderBy('scheduled_date')
                ->get(),
            // In the secretaryDashboard method, update the recent_actions part:
            'recent_actions' => collect()
                ->concat(AssetHistory::with(['asset', 'user'])
                    ->where('changed_by', $user->id)
                    // Exclude all REPAIR records and STATUS records related to repairs
                    ->where(function($query) {
                        $query->where('change_type', '!=', 'REPAIR')
                            ->where(function($q) {
                                $q->where('change_type', '!=', 'STATUS')
                                  ->orWhere(function($innerQ) {
                                      $innerQ->where('change_type', 'STATUS')
                                            ->where(function($deepQ) {
                                                $deepQ->where('old_value', '!=', 'UNDER REPAIR')
                                                      ->orWhere('new_value', '!=', 'IN USE');
                                            });
                                  });
                            });
                    })
                    ->select('*', DB::raw("'asset_history' as action_source"))
                    ->orderBy('created_at', 'desc')
                    ->get())
                ->concat(RepairRequest::with(['asset', 'category'])
                    ->where('technician_id', $user->id)
                    ->where('status', 'completed')
                    ->whereNotNull('completed_at')
                    ->select('*', DB::raw("'repair' as action_source"))
                    ->orderBy('completed_at', 'desc')
                    ->limit(5)
                    ->get())
                ->concat(Maintenance::with(['laboratory', 'maintenanceAssets'])
                    ->where('technician_id', $user->id)
                    ->where('status', 'completed')
                    ->whereNotNull('completed_at')
                    ->select('*', DB::raw("'maintenance' as action_source"))
                    ->orderBy('completed_at', 'desc')
                    ->limit(5)
                    ->get())
                ->sortByDesc(function($item) {
                    return $item->action_source === 'asset_history' ? $item->created_at : $item->completed_at;
                })
                ->take(5),
            'total_tasks' => RepairRequest::where('technician_id', $user->id)
                ->whereIn('status', ['pending', 'urgent', 'in_progress', 'completed'])
                ->count(),
            'completion_rate' => function() use ($user) {
                $total = RepairRequest::where('technician_id', $user->id)
                    ->whereIn('status', ['pending', 'urgent', 'in_progress', 'completed'])
                    ->count();
                $completed = RepairRequest::where('technician_id', $user->id)
                    ->where('status', 'completed')
                    ->count();
                return $total > 0 ? round(($completed / $total) * 100, 1) : 0;
            },
            'avg_completion_time' => RepairRequest::where('technician_id', $user->id)
                ->where('status', 'completed')
                ->whereNotNull('completed_at')
                ->select(DB::raw('ROUND(AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at)), 1) as avg_hours'))
                ->value('avg_hours') ?? 0,
            'completed_repairs_history' => RepairRequest::with(['asset', 'category'])
                ->where('technician_id', $user->id)
                ->where('status', 'completed')
                ->whereNotNull('completed_at')
                ->orderBy('completed_at', 'desc')
                ->get(),
            'completed_maintenance_history' => Maintenance::with(['laboratory', 'maintenanceAssets'])
                ->where('technician_id', $user->id)
                ->where('status', 'completed')
                ->whereNotNull('completed_at')
                ->orderBy('completed_at', 'desc')
                ->get()
        ];
    
        // Calculate completion rate
        $personalStats['completion_rate'] = $personalStats['completion_rate']();
    
        return view('secretary-dashboard', compact('personalStats'));
    }

    public function userActionsHistory()
    {
        $user = auth()->user();
        
        // Get repair requests completed by this user
        $completedRepairRequests = RepairRequest::where('technician_id', $user->id)
            ->where('status', 'completed')
            ->pluck('id');
        
        $actions = collect()
            ->concat(AssetHistory::with(['asset', 'user'])
                ->where('changed_by', $user->id)
                // Filter out STATUS records that are created when a repair is completed
                ->where(function($query) {
                    $query->where('change_type', '!=', 'STATUS')
                        ->orWhere(function($q) {
                            $q->where('change_type', 'STATUS')
                            ->where(function($innerQuery) {
                                $innerQuery->where('old_value', '!=', 'UNDER REPAIR')
                                          ->orWhere('new_value', '!=', 'IN USE');
                            });
                        });
                })
                ->select('*', DB::raw("'asset_history' as action_source"))
                ->orderBy('created_at', 'desc')
                ->get())
            ->concat(RepairRequest::with(['asset', 'category'])
                ->where('technician_id', $user->id)
                ->where('status', 'completed')
                ->whereNotNull('completed_at')
                ->select('*', DB::raw("'repair' as action_source"))
                ->orderBy('completed_at', 'desc')
                ->get())
            ->concat(Maintenance::with(['laboratory', 'maintenanceAssets'])
                ->where('technician_id', $user->id)
                ->where('status', 'completed')
                ->whereNotNull('completed_at')
                ->select('*', DB::raw("'maintenance' as action_source"))
                ->orderBy('completed_at', 'desc')
                ->get())
            ->sortByDesc(function($item) {
                return $item->action_source === 'asset_history' ? $item->created_at : $item->completed_at;
            });
    
        return view('actions-history', compact('actions'));
    }

    public function allRepairsHistory(Request $request)
    {
        $user = auth()->user();
        $query = \App\Models\RepairRequest::with(['asset', 'category'])
            ->where('technician_id', $user->id)
            ->where('status', 'completed')
            ->whereNotNull('completed_at');
            
        // Apply date range filters if provided
        if ($request->has('start_date')) {
            $query->whereDate('completed_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date')) {
            $query->whereDate('completed_at', '<=', $request->end_date);
        }
        
        $repairs = $query->orderBy('completed_at', 'desc')->get();
        return view('repairs-history', compact('repairs'));
    }

    public function allMaintenanceHistory(Request $request)
    {
        $user = auth()->user();
        $query = \App\Models\Maintenance::with(['laboratory', 'maintenanceAssets'])
            ->where('technician_id', $user->id)
            ->where('status', 'completed')
            ->whereNotNull('completed_at');
            
        // Apply date filters if provided
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('completed_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('completed_at', '<=', $request->end_date);
        }
        
        $maintenance = $query->orderBy('completed_at', 'desc')->get();
        
        return view('user-maintenance-history', compact('maintenance'));
    }
}
