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
        $totalOpen = RepairRequest::whereNotIn('status', ['completed', 'cancelled', 'pulled_out'])->count();
        $completedThisMonth = RepairRequest::where('status', 'completed')
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->count();
        
        // Add pulled out assets statistics
        $pulledOutThisMonth = RepairRequest::where('status', 'pulled_out')
            ->whereMonth('created_at', now()->month)
            ->count();

        // Calculate average response time for pulled out assets
        $pulledOutRepairs = RepairRequest::where('status', 'pulled_out')
            ->whereNotNull('time_started')
            ->whereNotNull('completed_at')
            ->where('time_started', '!=', '0000-00-00 00:00:00')
            ->where('completed_at', '!=', '0000-00-00 00:00:00')
            ->get();

        // Log the raw data for debugging
        \Log::info('Pulled Out Repairs Raw Data', [
            'repairs' => $pulledOutRepairs->map(function($repair) {
                return [
                    'id' => $repair->id,
                    'ticket_number' => $repair->ticket_number,
                    'time_started' => $repair->time_started,
                    'completed_at' => $repair->completed_at
                ];
            })
        ]);

        $pulledOutDurations = [];
        foreach ($pulledOutRepairs as $repair) {
            $start = \Carbon\Carbon::parse($repair->time_started);
            $end = \Carbon\Carbon::parse($repair->completed_at);
            $minutes = $start->diffInMinutes($end);
            $pulledOutDurations[] = $minutes;
        }

        // Calculate average manually to ensure accuracy
        $avgPulledOutMinutes = count($pulledOutDurations) > 0 ? array_sum($pulledOutDurations) / count($pulledOutDurations) : 0;

        // Log the calculation details
        \Log::info('Pulled Out Duration Calculation', [
            'durations' => $pulledOutDurations,
            'sum' => array_sum($pulledOutDurations),
            'count' => count($pulledOutDurations),
            'average' => $avgPulledOutMinutes
        ]);

        // Convert pulled out average to appropriate format
        if ($avgPulledOutMinutes >= 1440) { // More than 24 hours
            $avgPulledOutDays = round($avgPulledOutMinutes / 1440, 1);
            $avgPulledOutTime = null;
        } elseif ($avgPulledOutMinutes >= 60) { // More than 1 hour
            $hours = floor($avgPulledOutMinutes / 60);
            $remainingMinutes = round($avgPulledOutMinutes % 60, 1);
            $avgPulledOutTime = $hours . ($remainingMinutes > 0 ? 'hrs ' . $remainingMinutes . ' mins' : 'hrs');
            $avgPulledOutDays = null;
        } else {
            $avgPulledOutTime = round($avgPulledOutMinutes, 1) . ' mins';
            $avgPulledOutDays = null;
        }

        // Calculate average response time using time_started and completed_at
        $repairs = RepairRequest::where('status', 'completed')
            ->whereNotNull('time_started')
            ->whereNotNull('completed_at')
            ->where('time_started', '!=', '0000-00-00 00:00:00')
            ->where('completed_at', '!=', '0000-00-00 00:00:00')
            ->get();

        $durations = $repairs->map(function($repair) {
            $start = \Carbon\Carbon::parse($repair->time_started);
            $end = \Carbon\Carbon::parse($repair->completed_at);
            $minutes = $start->diffInMinutes($end);
            return [
                'id' => $repair->id,
                'ticket_number' => $repair->ticket_number,
                'time_started' => $repair->time_started,
                'completed_at' => $repair->completed_at,
                'duration_minutes' => $minutes
            ];
        });

        $avgResponseMinutes = $durations->avg('duration_minutes');

        // Convert to appropriate format
        if ($avgResponseMinutes >= 1440) { // More than 24 hours
            $avgResponseDays = round($avgResponseMinutes / 1440, 1);
            $avgResponseTime = null;
        } elseif ($avgResponseMinutes >= 60) { // More than 1 hour
            $hours = floor($avgResponseMinutes / 60);
            $remainingMinutes = round($avgResponseMinutes % 60, 1);
            $avgResponseTime = $hours . ($remainingMinutes > 0 ? 'hrs ' . $remainingMinutes . ' mins' : 'hrs');
            $avgResponseDays = null;
        } else {
            $avgResponseTime = round($avgResponseMinutes, 1) . ' mins';
            $avgResponseDays = null;
        }

        // Log the query and result for debugging
        \Log::info('Average Response Time Calculation', [
            'total_repairs' => $repairs->count(),
            'durations' => $durations->toArray(),
            'avg_minutes' => $avgResponseMinutes,
            'avg_hours' => $avgResponseTime,
            'avg_days' => $avgResponseDays,
            'calculation' => [
                'sum_minutes' => $durations->sum('duration_minutes'),
                'count' => $durations->count(),
                'average' => $durations->count() > 0 ? $durations->sum('duration_minutes') / $durations->count() : 0
            ]
        ]);

        // Get urgent repairs (urgency_level = 1)
        $urgentRepairs = RepairRequest::where('urgency_level', 1)
            ->whereNotIn('status', ['completed', 'cancelled', 'pulled_out'])
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
            ->paginate(5, ['*'], 'warranty_page');

        // Get critical and warning assets using stored values
        $criticalAndWarningAssets = Asset::where('status', '!=', 'DISPOSED')
            ->whereIn('life_status', ['critical', 'warning'])
            ->orderBy('remaining_life')
            ->paginate(5, ['*'], 'lifespan_page');

        $criticalAndWarningAssets->getCollection()->transform(function ($asset) {
            $asset->days_left = $asset->end_of_life_date
                ? max(0, now()->diffInDays($asset->end_of_life_date, false))
                : 0;
            return $asset;
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
            DB::raw('(SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) / NULLIF(' . ($totalLabHours ?: 1) . ', 0) * 100) as usage_percentage')
        )
            ->join('users', 'lab_logs.user_id', '=', 'users.id')
            ->where('time_in', '>=', Carbon::now()->startOfMonth())
            ->where('time_in', '<=', Carbon::now()->endOfMonth())
            ->groupBy('users.department')
            ->orderBy('hours', 'desc')
            ->get();

        // Add this before the return statement
        $user = auth()->user();
        
        // Calculate completion rate
        $totalTasks = RepairRequest::where('technician_id', $user->id)
            ->whereIn('status', ['pending', 'in_progress', 'completed'])
            ->count();
        $completedTasks = RepairRequest::where('technician_id', $user->id)
            ->where('status', 'completed')
            ->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
        
        // Calculate feedback score
        $avgRating = DB::table('technician_evaluations')
            ->where('technician_id', $user->id)
            ->whereNotNull('rating')
            ->avg('rating');
        $feedbackScore = $avgRating ? round($avgRating, 1) : 'N/A';
        
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
            'completed_maintenance_history' => Maintenance::with(['location', 'technician'])
                ->where('technician_id', $user->id)
                ->where('status', 'completed')
                ->whereNotNull('completed_at')
                ->orderBy('completed_at', 'desc')
                ->get(),
            'assigned_tasks' => RepairRequest::where('technician_id', $user->id)
                ->whereIn('status', ['pending', 'urgent', 'in_progress'])
                ->count(),
            'avg_response_time' => (function() use ($user) {
                $repairs = RepairRequest::where('technician_id', $user->id)
                    ->where('status', 'completed')
                    ->whereNotNull('time_started')
                    ->whereNotNull('completed_at')
                    ->where('time_started', '!=', '0000-00-00 00:00:00')
                    ->where('completed_at', '!=', '0000-00-00 00:00:00')
                    ->get();
                
                if ($repairs->count() > 0) {
                    $totalMinutes = 0;
                    foreach ($repairs as $repair) {
                        $start = \Carbon\Carbon::parse($repair->time_started);
                        $end = \Carbon\Carbon::parse($repair->completed_at);
                        $totalMinutes += $start->diffInMinutes($end);
                    }
                    $avgMinutes = $totalMinutes / $repairs->count();
                    
                    if ($avgMinutes >= 60) {
                        $hours = floor($avgMinutes / 60);
                        $remainingMinutes = round($avgMinutes % 60, 1);
                        return $hours . ($remainingMinutes > 0 ? 'hrs ' . $remainingMinutes . ' mins' : 'hrs');
                    } else {
                        return round($avgMinutes, 1) . ' mins';
                    }
                }
                return 'N/A';
            })(),
            'completion_rate' => $completionRate,
            'feedback_score' => $feedbackScore
        ];

        // Prepare monthly chart data
        $procurementChartData = [
            'labels' => collect($monthlyData)->pluck('month')->toArray(),
            'values' => collect($monthlyData)->pluck('procurement_value')->toArray()
        ];

        $assetCountChartData = [
            'labels' => collect($monthlyData)->pluck('month')->toArray(),
            'values' => collect($monthlyData)->pluck('procurement_count')->toArray()
        ];

        // Add yearly procurement data (last 5 years)
        $currentYear = Carbon::now()->year;
        $yearlyProcurements = Asset::select(
            DB::raw('YEAR(purchase_date) as year'),
            DB::raw('COUNT(*) as procurement_count'),
            DB::raw('SUM(purchase_price) as procurement_value')
        )
            ->whereYear('purchase_date', '>=', $currentYear - 4)
            ->whereNotNull('purchase_date')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        // Create yearly data array
        $yearlyData = collect(range($currentYear - 4, $currentYear))->map(function ($year) {
            return [
                'year' => $year,
                'procurement_count' => 0,
                'procurement_value' => 0
            ];
        })->toArray();

        // Merge actual data
        foreach ($yearlyProcurements as $proc) {
            $index = array_search($proc->year, array_column($yearlyData, 'year'));
            if ($index !== false) {
                $yearlyData[$index]['procurement_count'] = $proc->procurement_count;
                $yearlyData[$index]['procurement_value'] = $proc->procurement_value;
            }
        }

        $yearlyProcurementChartData = [
            'labels' => array_column($yearlyData, 'year'),
            'values' => array_column($yearlyData, 'procurement_value')
        ];

        // Get assets by category data (all active assets)
        $categoryData = Asset::select(
            'categories.name as category_name',
            DB::raw('COUNT(*) as asset_count'),
            DB::raw('SUM(purchase_price) as total_value')
        )
            ->join('categories', 'assets.category_id', '=', 'categories.id')
            ->where('assets.status', '!=', 'DISPOSED')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('asset_count', 'desc')
            ->get();

        $assetsByCategoryChartData = [
            'labels' => $categoryData->pluck('category_name')->toArray(),
            'values' => $categoryData->pluck('asset_count')->toArray()
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
            'personalStats',
            'pulledOutThisMonth',
            'avgPulledOutTime',
            'avgPulledOutDays',
            'procurementChartData',
            'assetCountChartData',
            'yearlyProcurementChartData',
            'assetsByCategoryChartData'
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
                ->concat(Maintenance::with(['location', 'technician'])
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
            // New: Average response time using only repairs (time_started to completed_at)
            'avg_response_time' => (function() use ($user) {
                $repairs = RepairRequest::where('technician_id', $user->id)
                    ->where('status', 'completed')
                    ->whereNotNull('time_started')
                    ->whereNotNull('completed_at')
                    ->where('time_started', '!=', '0000-00-00 00:00:00')
                    ->where('completed_at', '!=', '0000-00-00 00:00:00')
                    ->get();
                
                if ($repairs->count() > 0) {
                    $totalMinutes = 0;
                    foreach ($repairs as $repair) {
                        $start = \Carbon\Carbon::parse($repair->time_started);
                        $end = \Carbon\Carbon::parse($repair->completed_at);
                        $totalMinutes += $start->diffInMinutes($end);
                    }
                    $avgMinutes = $totalMinutes / $repairs->count();
                    
                    if ($avgMinutes >= 60) {
                        $hours = floor($avgMinutes / 60);
                        $remainingMinutes = round($avgMinutes % 60, 1);
                        return $hours . ($remainingMinutes > 0 ? 'hrs ' . $remainingMinutes . ' mins' : 'hrs');
                    } else {
                        return round($avgMinutes, 1) . ' mins';
                    }
                }
                return 'N/A';
            })(),
            'completed_repairs_history' => RepairRequest::with(['asset', 'category'])
                ->where('technician_id', $user->id)
                ->where('status', 'completed')
                ->whereNotNull('completed_at')
                ->orderBy('completed_at', 'desc')
                ->get(),
            'completed_maintenance_history' => Maintenance::with(['location', 'technician'])
                ->where('technician_id', $user->id)
                ->where('status', 'completed')
                ->whereNotNull('completed_at')
                ->orderBy('completed_at', 'desc')
                ->get(),
            'avg_rating' => DB::table('technician_evaluations')
                ->where('technician_id', $user->id)
                ->whereNotNull('rating')
                ->avg('rating') ?? 0
        ];

        // Calculate completion rate
        $personalStats['completion_rate'] = $personalStats['completion_rate']();

        return view('secretary-dashboard', compact('personalStats'));
    }

    public function userActionsHistory(Request $request)
    {
        $user = auth()->user();
        $perPage = 10; // Number of items per page
        
        // Get asset history actions
        $assetHistoryQuery = AssetHistory::with(['asset', 'user'])
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
            ->select('*', DB::raw("'asset_history' as action_source"), DB::raw('created_at as action_date'))
            ->orderBy('created_at', 'desc');
        
        // Get repair actions
        $repairQuery = RepairRequest::with(['asset', 'category'])
            ->where('technician_id', $user->id)
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->select('*', DB::raw("'repair' as action_source"), DB::raw('completed_at as action_date'))
            ->orderBy('completed_at', 'desc');
        
        // Get maintenance actions
        $maintenanceQuery = Maintenance::with(['location', 'technician'])
            ->where('technician_id', $user->id)
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->select('*', DB::raw("'maintenance' as action_source"), DB::raw('completed_at as action_date'))
            ->orderBy('completed_at', 'desc');
        
        // Apply filters
        $filter = $request->get('filter');
        if ($filter === 'asset_history') {
            $assetHistoryQuery = $assetHistoryQuery;
            $repairQuery = null;
            $maintenanceQuery = null;
        } elseif ($filter === 'repair') {
            $assetHistoryQuery = null;
            $repairQuery = $repairQuery;
            $maintenanceQuery = null;
        } elseif ($filter === 'maintenance') {
            $assetHistoryQuery = null;
            $repairQuery = null;
            $maintenanceQuery = $maintenanceQuery;
        }
        
        // Apply date filters
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        if ($startDate) {
            if ($assetHistoryQuery) {
                $assetHistoryQuery->whereDate('created_at', '>=', $startDate);
            }
            if ($repairQuery) {
                $repairQuery->whereDate('completed_at', '>=', $startDate);
            }
            if ($maintenanceQuery) {
                $maintenanceQuery->whereDate('completed_at', '>=', $startDate);
            }
        }
        
        if ($endDate) {
            if ($assetHistoryQuery) {
                $assetHistoryQuery->whereDate('created_at', '<=', $endDate);
            }
            if ($repairQuery) {
                $repairQuery->whereDate('completed_at', '<=', $endDate);
            }
            if ($maintenanceQuery) {
                $maintenanceQuery->whereDate('completed_at', '<=', $endDate);
            }
        }
        
        // Combine all queries using union
        $combinedQuery = collect();
        
        if ($assetHistoryQuery) {
            $combinedQuery = $combinedQuery->concat($assetHistoryQuery->get());
        }
        if ($repairQuery) {
            $combinedQuery = $combinedQuery->concat($repairQuery->get());
        }
        if ($maintenanceQuery) {
            $combinedQuery = $combinedQuery->concat($maintenanceQuery->get());
        }
        
        // Sort by action date
        $combinedQuery = $combinedQuery->sortByDesc('action_date');
        
        // Manual pagination
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedActions = $combinedQuery->slice($offset, $perPage);
        
        // Create paginator instance
        $actions = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedActions,
            $combinedQuery->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );
        
        // Add query parameters to pagination links
        $actions->appends($request->except('page'));
    
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
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('completed_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('completed_at', '<=', $request->end_date);
        }
        
        $repairs = $query->orderBy('completed_at', 'desc')->paginate(10);
        return view('repairs-history', compact('repairs'));
    }

    public function allMaintenanceHistory(Request $request)
    {
        $user = auth()->user();
        $query = \App\Models\Maintenance::with(['location', 'technician'])
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
        
        $maintenance = $query->orderBy('completed_at', 'desc')->paginate(10);
        
        return view('user-maintenance-history', compact('maintenance'));
    }
}
