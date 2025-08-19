<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\AssetHistory;
use App\Models\Maintenance;
use App\Models\Vendor;
use App\Models\RepairRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use App\Models\MaintenanceRequest;
use Carbon\Carbon;
use App\Models\Location; // Added this import for the new methods
use Maatwebsite\Excel\Facades\Excel; // Added this import for the new methods
use App\Exports\PaascuInventoryExport;
use App\Exports\PaascuUtilizationExport;

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
        $assets = Asset::where('status', '!=', 'DISPOSED')->with('location')->get();
        
        // Calculate total summary
        $totalSummary = [
            'total_assets' => $assets->count(),
            'total_value' => $assets->sum('purchase_price')
        ];

        // Group assets by location
        $locationStats = $assets->groupBy('location_id')
            ->map(function ($locationAssets) {
                $location = $locationAssets->first()->location;
                return [
                    'location' => $location ? $location->full_location : 'N/A',
                    'count' => $locationAssets->count(),
                    'total_value' => $locationAssets->sum('purchase_price')
                ];
            })->values();

        // Convert to collection and add pagination using LengthAwarePaginator
        $perPage = 15;
        $currentPage = request()->get('page', 1);
        $total = $locationStats->count();
        $items = $locationStats->forPage($currentPage, $perPage);
        
        $locationStats = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('reports.location', compact('locationStats', 'totalSummary'));
    }

    public function locationDetails($location)
    {
        // Parse the location string (format: "Building - Floor X - Room Y")
        $parts = explode(' - ', $location);
        if (count($parts) === 3) {
            $building = $parts[0];
            $floor = str_replace('Floor ', '', $parts[1]);
            $room_number = str_replace('Room ', '', $parts[2]);
            
            // Find the location by building, floor, and room_number
            $locationModel = Location::where([
                'building' => $building,
                'floor' => $floor,
                'room_number' => $room_number
            ])->first();
            
            if ($locationModel) {
                return redirect()->route('locations.show', $locationModel->id);
            }
        }
        
        // If location not found, redirect back with error
        return redirect()->route('reports.location')->with('error', 'Location not found.');
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

        // Get maintenance history - use the asset's location_id
        $locationId = $asset->location_id;
        $assetMaintenances = collect();
        
        if ($locationId) {
            $assetMaintenances = \App\Models\Maintenance::where('location_id', $locationId)
                ->where('status', 'completed')
                ->where(function($query) use ($asset) {
                    $query->whereNull('excluded_assets')
                        ->orWhereRaw('NOT JSON_CONTAINS(excluded_assets, ?)', ['"' . $asset->id . '"']);
                })
                ->with(['location', 'technician'])
                ->orderBy('scheduled_date', 'desc')
                ->get();
        }

        // Debug the history records
        \Log::info('History records:', [
            'total_records' => $history->count(),
            'repair_records' => $history->where('change_type', 'REPAIR')->count(),
            'all_change_types' => $history->pluck('change_type')->unique()->toArray()
        ]);

        // Debug specific repair records
        $repairRecords = $history->where('change_type', 'REPAIR');
        \Log::info('Repair records details:', [
            'count' => $repairRecords->count(),
            'records' => $repairRecords->map(function($record) {
                return [
                    'id' => $record->id,
                    'asset_id' => $record->asset_id,
                    'change_type' => $record->change_type,
                    'remarks' => $record->remarks,
                    'created_at' => $record->created_at
                ];
            })->toArray()
        ]);

        $history = $history->groupBy('change_type');

        return view('reports.asset-history', compact('asset', 'history', 'assetMaintenances'));
    }

    public function procurementHistory(Request $request)
    {
        $query = Asset::with(['category', 'vendor'])->orderBy('purchase_date', 'desc');

        // Apply filters
        if ($request->filled('start_date')) {
            $query->where('purchase_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('purchase_date', '<=', $request->end_date);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        $assets = $query->paginate(10);

        // Get data for filter dropdowns
        $categories = Category::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();

        return view('reports.procurement-history', compact('assets', 'categories', 'vendors'));
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
            $query->where('disposal_date', '<=', $request->end_date . ' 23:59:59');
        }
    
        $disposedAssets = $query->paginate(10);
    
        return view('reports.disposal-history', compact('disposedAssets'));
    }

    public function vendorAnalysis(Request $request)
    {
        // Get all vendors with their assets and related data
        $vendors = Vendor::with(['assets' => function($query) {
            $query->with(['repairRequests' => function($repairQuery) {
                $repairQuery->where('status', 'completed');
            }]);
        }])->get();

        // Check if there are any vendors with assets
        $vendorsWithAssets = $vendors->filter(function($vendor) {
            return $vendor->assets->count() > 0;
        });

        if ($vendorsWithAssets->count() == 0) {
            // Return empty analysis with message
            $overallStats = [
                'total_vendors' => $vendors->count(),
                'total_assets' => 0,
                'total_value' => 0,
            ];

            return view('reports.vendor-analysis', compact('overallStats'))->with('noData', true);
        }

        $vendorAnalysis = $vendorsWithAssets->map(function ($vendor) {
            $assets = $vendor->assets;
            $totalAssets = $assets->count();
            $totalValue = $assets->sum('purchase_price');
            
            // Calculate repair statistics
            $totalRepairs = $assets->sum(function($asset) {
                return $asset->repairRequests->count();
            });
            
            $completedRepairs = $assets->sum(function($asset) {
                return $asset->repairRequests->where('status', 'completed')->count();
            });
            
            $disposedAssets = $assets->where('status', 'DISPOSED')->count();

            // Calculate completion rate
            $completionRate = $totalRepairs > 0 ? ($completedRepairs / $totalRepairs) * 100 : 0;

            // Calculate average age (years)
            $averageAge = null;
            if ($totalAssets > 0) {
                $totalYears = $assets->sum(function($asset) {
                    return \Carbon\Carbon::parse($asset->purchase_date)->diffInYears(now());
                });
                $averageAge = round($totalYears / $totalAssets, 1);
            }

            return [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'total_assets' => $totalAssets,
                'total_value' => $totalValue,
                'total_repairs' => $totalRepairs,
                'completed_repairs' => $completedRepairs,
                'completion_rate' => round($completionRate, 2),
                'disposed_count' => $disposedAssets,
                'average_age' => $averageAge,
            ];
        });

        // Calculate overall statistics
        $overallStats = [
            'total_vendors' => $vendors->count(),
            'total_assets' => $vendorsWithAssets->sum(function($vendor) { return $vendor->assets->count(); }),
            'total_value' => $vendorsWithAssets->sum(function($vendor) { return $vendor->assets->sum('purchase_price'); }),
        ];

        return view('reports.vendor-analysis', compact('vendorAnalysis', 'overallStats'));
    }

    public function vendorDetails(Vendor $vendor)
    {
        // Get vendor with assets and related data
        $vendor->load(['assets' => function($query) {
            $query->with(['category', 'repairRequests' => function($repairQuery) {
                $repairQuery->orderBy('created_at', 'desc');
            }]);
        }]);

        $assets = $vendor->assets;
        
        // Calculate detailed statistics
        $totalAssets = $assets->count();
        $totalValue = $assets->sum('purchase_price');
        $averageValue = $totalAssets > 0 ? $totalValue / $totalAssets : 0;
        
        // Asset status breakdown
        $statusBreakdown = [
            'in_use' => $assets->where('status', 'IN USE')->count(),
            'under_repair' => $assets->where('status', 'UNDER REPAIR')->count(),
            'pulled_out' => $assets->where('status', 'PULLED OUT')->count(),
            'disposed' => $assets->where('status', 'DISPOSED')->count()
        ];
        
        // Category breakdown
        $categoryBreakdown = $assets->groupBy('category.name')
            ->map(function($categoryAssets) {
                return [
                    'count' => $categoryAssets->count(),
                    'value' => $categoryAssets->sum('purchase_price'),
                    'repair_count' => $categoryAssets->sum(function($asset) {
                        return $asset->repairRequests->count();
                    })
                ];
            });
        
        // Repair analysis
        $totalRepairs = $assets->sum(function($asset) {
            return $asset->repairRequests->count();
        });
        
        $completedRepairs = $assets->sum(function($asset) {
            return $asset->repairRequests->where('status', 'completed')->count();
        });
        
        $disposedCount = $assets->where('status', 'DISPOSED')->count();
        
        // Recent repair requests (last 10)
        $recentRepairs = collect();
        foreach($assets as $asset) {
            foreach($asset->repairRequests as $repair) {
                $recentRepairs->push([
                    'asset_name' => $asset->name,
                    'serial_number' => $asset->serial_number,
                    'repair_id' => $repair->id,
                    'status' => $repair->status,
                    'created_at' => $repair->created_at,
                    'completed_at' => $repair->completed_at
                ]);
            }
        }
        $recentRepairs = $recentRepairs->sortByDesc('created_at')->take(10);
        
        // Age analysis
        $ageGroups = [
            '0-2 years' => $assets->filter(function($asset) {
                return Carbon::parse($asset->purchase_date)->diffInYears(now()) <= 2;
            })->count(),
            '3-5 years' => $assets->filter(function($asset) {
                $age = Carbon::parse($asset->purchase_date)->diffInYears(now());
                return $age > 2 && $age <= 5;
            })->count(),
            '6-10 years' => $assets->filter(function($asset) {
                $age = Carbon::parse($asset->purchase_date)->diffInYears(now());
                return $age > 5 && $age <= 10;
            })->count(),
            '10+ years' => $assets->filter(function($asset) {
                return Carbon::parse($asset->purchase_date)->diffInYears(now()) > 10;
            })->count()
        ];
        
        // Completion rate
        $completionRate = $totalRepairs > 0 ? ($completedRepairs / $totalRepairs) * 100 : 0;
        
        // Recommendations (simple example)
        $recommendations = [];
        if ($completionRate < 75) $recommendations[] = 'Improve repair completion rate for better reliability.';
        if ($disposedCount > 0) $recommendations[] = 'Investigate reasons for asset disposal and implement measures to reduce disposal rate.';
        if (empty($recommendations)) $recommendations[] = 'Vendor performance is within optimal range.';

        return view('reports.vendor-details', compact(
            'vendor',
            'totalAssets',
            'totalValue',
            'averageValue',
            'statusBreakdown',
            'categoryBreakdown',
            'totalRepairs',
            'completedRepairs',
            'completionRate',
            'disposedCount',
            'recentRepairs',
            'ageGroups',
            'recommendations'
        ));
    }

    public function labUsage(Request $request)
    {
        // Group by period based on filter
        $period = $request->get('period', 'day');
        $periodFormat = match($period) {
            'month' => 'DATE_FORMAT(time_in, "%Y-%m")',
            'year' => 'YEAR(time_in)',
            default => 'DATE(time_in)'
        };

        $query = DB::table('lab_logs')
            ->select(
                DB::raw("{$periodFormat} as period"),
                'users.department as department_name',
                'lab_logs.laboratory as lab_name',
                DB::raw('COUNT(*) as total_sessions'),
                DB::raw('SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) as total_hours'),
                DB::raw('AVG(TIMESTAMPDIFF(HOUR, time_in, time_out)) as avg_duration'),
                DB::raw('COUNT(DISTINCT user_id) as unique_users')
            )
            ->join('users', 'lab_logs.user_id', '=', 'users.id');

        // Apply filters
        if ($request->has('department_id')) {
            $query->where('users.department', $request->department_id);
        }

        if ($request->has('lab_id')) {
            $query->where('lab_logs.laboratory', $request->lab_id);
        }

        // Date range filter
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('time_in', [$request->start_date, $request->end_date . ' 23:59:59']);
        }

        // Group by period and names
        $query->groupBy('period', 'users.department', 'lab_logs.laboratory');

        // Get summary statistics
        $summary = DB::table('lab_logs')
            ->selectRaw('
                COUNT(*) as total_sessions,
                SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) as total_hours,
                AVG(TIMESTAMPDIFF(HOUR, time_in, time_out)) as avg_duration,
                COUNT(DISTINCT user_id) as unique_users,
                COUNT(DISTINCT laboratory) as labs_used
            ')
            ->when($request->has('department_id'), function($q) use ($request) {
                return $q->join('users', 'lab_logs.user_id', '=', 'users.id')
                    ->where('users.department', $request->department_id);
            })
            ->when($request->has('lab_id'), function($q) use ($request) {
                return $q->where('lab_logs.laboratory', $request->lab_id);
            })
            ->when($request->has('start_date') && $request->has('end_date'), function($q) use ($request) {
                return $q->whereBetween('time_in', [$request->start_date, $request->end_date . ' 23:59:59']);
            })
            ->when($request->has('lab_id'), function($q) use ($request) {
                return $q->where('lab_logs.laboratory', $request->lab_id);
            })
            ->first();

        // Get usage by department
        $departmentUsage = DB::table('lab_logs')
            ->join('users', 'lab_logs.user_id', '=', 'users.id')
            ->select(
                'users.department as department_name',
                DB::raw('COUNT(*) as total_sessions'),
                DB::raw('SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) as total_hours')
            )
            ->when($request->has('start_date') && $request->has('end_date'), function($q) use ($request) {
                return $q->whereBetween('time_in', [$request->start_date, $request->end_date . ' 23:59:59']);
            })
            ->when($request->has('lab_id'), function($q) use ($request) {
                return $q->where('lab_logs.laboratory', $request->lab_id);
            })
            ->groupBy('users.department')
            ->orderBy('total_hours', 'desc')
            ->paginate(5);

        // Get usage by lab
        $labUsage = DB::table('lab_logs')
            ->select(
                'laboratory as lab_name',
                DB::raw('COUNT(*) as total_sessions'),
                DB::raw('SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) as total_hours')
            )
            ->when($request->has('start_date') && $request->has('end_date'), function($q) use ($request) {
                return $q->whereBetween('time_in', [$request->start_date, $request->end_date . ' 23:59:59']);
            })
            ->when($request->has('department_id'), function($q) use ($request) {
                return $q->join('users', 'lab_logs.user_id', '=', 'users.id')
                    ->where('users.department', $request->department_id);
            })
            ->when($request->has('lab_id'), function($q) use ($request) {
                return $q->where('lab_logs.laboratory', $request->lab_id);
            })
            ->groupBy('laboratory')
            ->orderBy('total_hours', 'desc')
            ->paginate(5);

        // Get peak usage times
        $peakUsage = DB::table('lab_logs')
            ->select(
                DB::raw('HOUR(time_in) as hour'),
                DB::raw('COUNT(*) as total_sessions')
            )
            ->when($request->has('start_date') && $request->has('end_date'), function($q) use ($request) {
                return $q->whereBetween('time_in', [$request->start_date, $request->end_date . ' 23:59:59']);
            })
            ->when($request->has('department_id'), function($q) use ($request) {
                return $q->join('users', 'lab_logs.user_id', '=', 'users.id')
                    ->where('users.department', $request->department_id);
            })
            ->when($request->has('lab_id'), function($q) use ($request) {
                return $q->where('lab_logs.laboratory', $request->lab_id);
            })
            ->groupBy('hour')
            ->orderBy('total_sessions', 'desc')
            ->get();

        // Get data for the table
        $usageData = $query->paginate(10);

        // Get all departments and labs for filters
        $departments = DB::table('users')
            ->select('department as name')
            ->distinct()
            ->get()
            ->map(function($dept) {
                return (object)[
                    'id' => $dept->name,
                    'name' => $dept->name
                ];
            });
            
        $labs = DB::table('lab_logs')
            ->select('laboratory as name')
            ->distinct()
            ->get()
            ->map(function($lab) {
                return (object)[
                    'id' => $lab->name,
                    'name' => $lab->name
                ];
            });

        return view('reports.lab-usage', compact(
            'summary',
            'departmentUsage',
            'labUsage',
            'peakUsage',
            'usageData',
            'departments',
            'labs',
            'period'
        ));
    }

    public function exportLabUsageToPdf(Request $request)
    {
        // Get the same data as the main report
        $period = $request->get('period', 'day');
        $periodFormat = match($period) {
            'month' => 'DATE_FORMAT(time_in, "%Y-%m")',
            'year' => 'YEAR(time_in)',
            default => 'DATE(time_in)'
        };

        $query = DB::table('lab_logs')
            ->select(
                DB::raw("{$periodFormat} as period"),
                'users.department as department_name',
                'lab_logs.laboratory as lab_name',
                DB::raw('COUNT(*) as total_sessions'),
                DB::raw('SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) as total_hours'),
                DB::raw('AVG(TIMESTAMPDIFF(HOUR, time_in, time_out)) as avg_duration'),
                DB::raw('COUNT(DISTINCT user_id) as unique_users')
            )
            ->join('users', 'lab_logs.user_id', '=', 'users.id');

        // Apply filters
        if ($request->has('department_id')) {
            $query->where('users.department', $request->department_id);
        }

        if ($request->has('lab_id')) {
            $query->where('lab_logs.laboratory', $request->lab_id);
        }

        // Date range filter
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('time_in', [$request->start_date, $request->end_date . ' 23:59:59']);
        }

        // Group by period and names
        $query->groupBy('period', 'users.department', 'lab_logs.laboratory');

        // Get summary statistics
        $summary = DB::table('lab_logs')
            ->selectRaw('
                COUNT(*) as total_sessions,
                SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) as total_hours,
                AVG(TIMESTAMPDIFF(HOUR, time_in, time_out)) as avg_duration,
                COUNT(DISTINCT user_id) as unique_users,
                COUNT(DISTINCT laboratory) as labs_used
            ')
            ->when($request->has('department_id'), function($q) use ($request) {
                return $q->join('users', 'lab_logs.user_id', '=', 'users.id')
                    ->where('users.department', $request->department_id);
            })
            ->when($request->has('lab_id'), function($q) use ($request) {
                return $q->where('lab_logs.laboratory', $request->lab_id);
            })
            ->when($request->has('start_date') && $request->has('end_date'), function($q) use ($request) {
                return $q->whereBetween('time_in', [$request->start_date, $request->end_date . ' 23:59:59']);
            })
            ->when($request->has('lab_id'), function($q) use ($request) {
                return $q->where('lab_logs.laboratory', $request->lab_id);
            })
            ->first();

        // Get usage by department
        $departmentUsage = DB::table('lab_logs')
            ->join('users', 'lab_logs.user_id', '=', 'users.id')
            ->select(
                'users.department as department_name',
                DB::raw('COUNT(*) as total_sessions'),
                DB::raw('SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) as total_hours')
            )
            ->when($request->has('start_date') && $request->has('end_date'), function($q) use ($request) {
                return $q->whereBetween('time_in', [$request->start_date, $request->end_date . ' 23:59:59']);
            })
            ->when($request->has('lab_id'), function($q) use ($request) {
                return $q->where('lab_logs.laboratory', $request->lab_id);
            })
            ->groupBy('users.department')
            ->orderBy('total_hours', 'desc')
            ->paginate(5);

        // Get usage by lab
        $labUsage = DB::table('lab_logs')
            ->select(
                'laboratory as lab_name',
                DB::raw('COUNT(*) as total_sessions'),
                DB::raw('SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) as total_hours')
            )
            ->when($request->has('start_date') && $request->has('end_date'), function($q) use ($request) {
                return $q->whereBetween('time_in', [$request->start_date, $request->end_date . ' 23:59:59']);
            })
            ->when($request->has('department_id'), function($q) use ($request) {
                return $q->join('users', 'lab_logs.user_id', '=', 'users.id')
                    ->where('users.department', $request->department_id);
            })
            ->when($request->has('lab_id'), function($q) use ($request) {
                return $q->where('lab_logs.laboratory', $request->lab_id);
            })
            ->groupBy('laboratory')
            ->orderBy('total_hours', 'desc')
            ->paginate(5);

        // Get peak usage times
        $peakUsage = DB::table('lab_logs')
            ->select(
                DB::raw('HOUR(time_in) as hour'),
                DB::raw('COUNT(*) as total_sessions')
            )
            ->when($request->has('start_date') && $request->has('end_date'), function($q) use ($request) {
                return $q->whereBetween('time_in', [$request->start_date, $request->end_date . ' 23:59:59']);
            })
            ->when($request->has('department_id'), function($q) use ($request) {
                return $q->join('users', 'lab_logs.user_id', '=', 'users.id')
                    ->where('users.department', $request->department_id);
            })
            ->when($request->has('lab_id'), function($q) use ($request) {
                return $q->where('lab_logs.laboratory', $request->lab_id);
            })
            ->groupBy('hour')
            ->orderBy('total_sessions', 'desc')
            ->get();

        // Get data for the table
        $usageData = $query->paginate(10);

        // Generate PDF
        $pdf = PDF::loadView('reports.lab-usage-pdf', compact(
            'summary',
            'departmentUsage',
            'labUsage',
            'peakUsage',
            'usageData',
            'period'
        ));

        // Set PDF options
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);

        // Generate filename
        $filename = 'lab-usage-report-' . now()->format('Y-m-d') . '.pdf';

        // Return the PDF for download
        return $pdf->download($filename);
    }

    public function employeePerformance(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Get all employees with group_id 1 (admin) and group_id 2 (secretary)
        $employees = User::whereIn('group_id', [1, 2])->get();

        // Initialize arrays for statistics
        $employeeStats = collect();
        $totalRepairs = 0;
        $totalMaintenance = 0;
        $totalResponseTime = 0;
        $totalTasks = 0;
        $completedTasks = 0;

        foreach ($employees as $employee) {
            // Get completed repairs
            $repairsQuery = RepairRequest::where('technician_id', $employee->id)
                ->where('status', 'completed');

            $employeeRepairs = $repairsQuery->count();
            
            $repairsThisMonth = (clone $repairsQuery)
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)
                ->count();
            
            // Get maintenance tasks completed by this employee
            $maintenanceQuery = Maintenance::where('technician_id', $employee->id)
                ->where('status', 'completed');

            // Apply date filters if provided
            if ($startDate) {
                $repairsQuery->where('completed_at', '>=', $startDate);
                $maintenanceQuery->where('completed_at', '>=', $startDate);
            }
            if ($endDate) {
                $repairsQuery->where('completed_at', '<=', $endDate);
                $maintenanceQuery->where('completed_at', '<=', $endDate);
            }

            $maintenanceTasks = $maintenanceQuery->count();
            
            // Calculate average response time (only for repairs, using time_started to completed_at)
            $repairsWithTimeData = $repairsQuery->whereNotNull('time_started')
                ->whereNotNull('completed_at')
                ->where('time_started', '!=', '0000-00-00 00:00:00')
                ->where('completed_at', '!=', '0000-00-00 00:00:00')
                ->get();
            
            $avgResponseTime = 0;
            if ($repairsWithTimeData->count() > 0) {
                $totalMinutes = 0;
                foreach ($repairsWithTimeData as $repair) {
                    $start = Carbon::parse($repair->time_started);
                    $end = Carbon::parse($repair->completed_at);
                    $totalMinutes += $start->diffInMinutes($end);
                }
                $avgMinutes = $totalMinutes / $repairsWithTimeData->count();
                $avgResponseTime = round($avgMinutes / 60, 1); // Convert to hours
            }

            // Calculate completion rate
            $totalAssignedTasks = $repairsQuery->count() + $maintenanceQuery->count();
            $completionRate = $totalAssignedTasks > 0 ? 
                (($employeeRepairs + $maintenanceTasks) / $totalAssignedTasks) * 100 : 0;

            // Calculate performance score (weighted average of metrics)
            $performanceScore = 0;
            $weights = [
                'repairs' => 0.3,
                'maintenance' => 0.3,
                'response_time' => 0.2,
                'completion_rate' => 0.2
            ];

            // Normalize metrics to 0-100 scale
            $maxRepairs = $employees->max(function ($emp) use ($startDate, $endDate) {
                return RepairRequest::where('technician_id', $emp->id)
                    ->where('status', 'completed')
                    ->when($startDate, fn($q) => $q->where('completed_at', '>=', $startDate))
                    ->when($endDate, fn($q) => $q->where('completed_at', '<=', $endDate))
                    ->count();
            });
            $maxMaintenance = $employees->max(function ($emp) use ($startDate, $endDate) {
                return Maintenance::where('technician_id', $emp->id)
                    ->where('status', 'completed')
                    ->when($startDate, fn($q) => $q->where('completed_at', '>=', $startDate))
                    ->when($endDate, fn($q) => $q->where('completed_at', '<=', $endDate))
                    ->count();
            });

            $repairScore = $maxRepairs > 0 ? ($employeeRepairs / $maxRepairs) * 100 : 0;
            $maintenanceScore = $maxMaintenance > 0 ? ($maintenanceTasks / $maxMaintenance) * 100 : 0;
            // For response time score, lower time is better, so we invert the calculation
            // Cap at 24 hours (1440 minutes) as the worst case
            $maxResponseTime = 24; // hours
            $responseTimeScore = $avgResponseTime > 0 ? 
                max(0, (($maxResponseTime - $avgResponseTime) / $maxResponseTime) * 100) : 0;
            $responseTimeScore = min($responseTimeScore, 100); // Cap at 100

            $performanceScore = 
                ($repairScore * $weights['repairs']) +
                ($maintenanceScore * $weights['maintenance']) +
                ($responseTimeScore * $weights['response_time']) +
                ($completionRate * $weights['completion_rate']);

            // Add employee stats to collection
            $employeeStats->push((object)[
                'name' => $employee->name,
                'role' => $employee->group_id == 1 ? 'Admin' : 'Secretary',
                'repairs_completed' => $employeeRepairs,
                'repairs_this_month' => $repairsThisMonth,
                'maintenance_tasks' => $maintenanceTasks,
                'avg_response_time' => round($avgResponseTime, 1),
                'completion_rate' => round($completionRate, 1),
                'performance_score' => round($performanceScore, 1)
            ]);

            // Update overall statistics
            $totalRepairs += $employeeRepairs;
            $totalMaintenance += $maintenanceTasks;
            $totalResponseTime += $avgResponseTime;
            $totalTasks += $totalAssignedTasks;
            $completedTasks += ($employeeRepairs + $maintenanceTasks);
        }

        // Calculate overall statistics
        $employeeCount = $employees->count();
        $overallStats = [
            'total_repairs' => $totalRepairs,
            'total_maintenance' => $totalMaintenance,
            'avg_repairs_per_employee' => $employeeCount > 0 ? $totalRepairs / $employeeCount : 0,
            'avg_maintenance_per_employee' => $employeeCount > 0 ? $totalMaintenance / $employeeCount : 0,
            'avg_response_time' => $employeeCount > 0 ? round($totalResponseTime / $employeeCount, 1) : 0,
            'best_response_time' => $employeeStats->min('avg_response_time'),
            'completion_rate' => $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0,
            'total_tasks' => $totalTasks
        ];

        // Sort and rank employees by performance score
        $employeeStats = $employeeStats->sortByDesc('performance_score')->values();

        // Generate key findings
        $keyFindings = $this->generateKeyFindings($employeeStats, $overallStats);

        return view('employee-performance', compact('employeeStats', 'overallStats', 'keyFindings'));
    }

    private function generateKeyFindings($employeeStats, $overallStats)
    {
        $keyFindings = [];

        // Analyze performance distribution
        $avgPerformance = $employeeStats->avg('performance_score');
        $maxPerformance = $employeeStats->max('performance_score');
        $minPerformance = $employeeStats->min('performance_score');
        $performanceGap = $maxPerformance - $minPerformance;

        // Add key findings
        $keyFindings[] = "Average employee performance score is " . number_format($avgPerformance, 1) . "%";
        $keyFindings[] = "Performance gap between highest and lowest performers is " . number_format($performanceGap, 1) . "%";
        $keyFindings[] = "Overall task completion rate is " . number_format($overallStats['completion_rate'], 1) . "%";
        $keyFindings[] = "Average response time across all employees is " . $overallStats['avg_response_time'] . " hours";

        // Analyze role-specific performance
        $adminStats = $employeeStats->where('role', 'Admin');
        $secretaryStats = $employeeStats->where('role', 'Secretary');

        if ($adminStats->isNotEmpty()) {
            $adminAvg = $adminStats->avg('performance_score');
            $keyFindings[] = "Admins average performance score is " . number_format($adminAvg, 1) . "%";
        }

        if ($secretaryStats->isNotEmpty()) {
            $secretaryAvg = $secretaryStats->avg('performance_score');
            $keyFindings[] = "Secretaries average performance score is " . number_format($secretaryAvg, 1) . "%";
        }

        // Top performer analysis
        if ($employeeStats->isNotEmpty()) {
            $topPerformer = $employeeStats->first();
            $keyFindings[] = "Top performer: " . $topPerformer->name . " with " . number_format($topPerformer->performance_score, 1) . "% performance score";
        }

        return $keyFindings;
    }

    private function generateVendorRecommendations($vendor, $repairRate, $completionRate, $operationalRate, $statusBreakdown)
    {
        $recommendations = [];

        // Add general recommendations
        $recommendations[] = "Regularly review and update vendor performance metrics to ensure they align with organizational goals";
        $recommendations[] = "Implement a feedback system to gather input on vendor performance and improvement areas";

        // Add specific recommendations based on vendor's performance
        if ($repairRate < 20) {
            $recommendations[] = "Consider increasing the frequency of maintenance tasks to reduce repair rate";
        }
        if ($completionRate < 80) {
            $recommendations[] = "Investigate reasons for incomplete repairs and implement measures to improve completion rate";
        }
        if ($operationalRate < 60) {
            $recommendations[] = "Review the utilization of assets and consider optimizing their allocation";
        }
        if ($statusBreakdown['disposed'] > 0) {
            $recommendations[] = "Investigate reasons for asset disposal and implement measures to reduce disposal rate";
        }

        return $recommendations;
    }

    public function exportPaascuFromProcurement(Request $request)
    {
        // Get assets with relationships (same as procurement history)
        $query = Asset::with(['category', 'vendor', 'location']);

        // Apply all filters
        if ($request->has('start_date') && $request->start_date) {
            $query->where('purchase_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('purchase_date', '<=', $request->end_date);
        }

        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('vendor_id') && $request->vendor_id) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Get all assets (no pagination for export)
        $assets = $query->orderBy('location_id')
                       ->orderBy('category_id')
                       ->get();

        // Generate Excel file
        $filename = 'PAASCU_Computer_Lab_Inventory_' . now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new PaascuInventoryExport($assets), $filename);
    }

    public function exportLabUsageToPaascu(Request $request)
    {
        // Group by period based on filter
        $period = $request->get('period', 'day');
        $periodFormat = match($period) {
            'month' => 'DATE_FORMAT(time_in, "%Y-%m")',
            'year' => 'YEAR(time_in)',
            default => 'DATE(time_in)'
        };

        // Get the same data as lab usage report
        $query = DB::table('lab_logs')
            ->select(
                DB::raw("{$periodFormat} as period"),
                'users.department as department_name',
                'lab_logs.laboratory as lab_name',
                DB::raw('COUNT(*) as total_sessions'),
                DB::raw('SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) as total_hours'),
                DB::raw('AVG(TIMESTAMPDIFF(HOUR, time_in, time_out)) as avg_duration'),
                DB::raw('COUNT(DISTINCT user_id) as unique_users')
            )
            ->join('users', 'lab_logs.user_id', '=', 'users.id');

        // Apply filters
        if ($request->has('department_id') && $request->department_id) {
            $query->where('users.department', $request->department_id);
        }

        if ($request->has('lab_id') && $request->lab_id) {
            $query->where('lab_logs.laboratory', $request->lab_id);
        }

        // Date range filter
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('time_in', [$request->start_date, $request->end_date . ' 23:59:59']);
        }

        // Group by period and names
        $query->groupBy('period', 'users.department', 'lab_logs.laboratory');

        $usageData = $query->get();

        // Calculate summary statistics
        $summary = (object) [
            'total_sessions' => $usageData->sum('total_sessions'),
            'total_hours' => $usageData->sum('total_hours'),
            'avg_duration' => $usageData->avg('avg_duration'),
            'unique_users' => $usageData->sum('unique_users')
        ];

        // Generate Excel file
        $filename = 'PAASCU_Laboratory_Utilization_' . now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(
            new PaascuUtilizationExport($usageData, $summary), 
            $filename
        );
    }
}