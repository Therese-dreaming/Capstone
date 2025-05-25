@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 pt-6 md:pt-8">
    <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-4 md:mb-6">Asset Management Dashboard</h1>

    <!-- Toggle Button for Personal Statistics -->
    <button id="togglePersonalStats" class="mb-4 md:mb-6 bg-red-800 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
        <span class="show-text">Show My Statistics</span>
        <span class="hide-text hidden">Hide My Statistics</span>
    </button>

    <!-- Personal Statistics Section (Hidden by Default) -->
    <div id="personalStatsSection" class="hidden space-y-4 md:space-y-6">
        <!-- Maintenance and Repair Statistics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4 mt-4 md:mt-6">
            <div class="bg-blue-50 p-3 md:p-4 rounded-lg shadow">
                <p class="text-xs md:text-sm text-gray-600">Completed Repairs</p>
                <p class="text-xl md:text-2xl font-bold text-blue-600">{{ $personalStats['completed_repairs'] }}</p>
            </div>
            <div class="bg-green-50 p-3 md:p-4 rounded-lg shadow">
                <p class="text-xs md:text-sm text-gray-600">Completed Maintenance</p>
                <p class="text-xl md:text-2xl font-bold text-green-600">{{ $personalStats['completed_maintenance'] }}</p>
            </div>
        </div>

        <!-- Completed Repairs Table -->
        <div class="bg-white rounded-lg shadow p-3 md:p-6 mt-4 md:mt-6">
            <div class="flex justify-between items-center mb-3 md:mb-4">
                <h2 class="text-base md:text-lg font-semibold">Completed Repairs History</h2>
                <a href="{{ route('repairs.history') }}" class="text-blue-600 hover:text-blue-800 text-xs md:text-sm font-medium">View All</a>
            </div>
            <div class="overflow-x-auto">
                @if($personalStats['completed_repairs_history']->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($personalStats['completed_repairs_history']->take(5) as $repair)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <a href="{{ route('assets.index', ['search' => $repair->asset->serial_number]) }}" class="font-bold text-red-600 hover:underline">{{ $repair->asset->serial_number }}</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $repair->issue }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($repair->completed_at)->format('M j, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No completed repairs</h3>
                    <p class="mt-1 text-sm text-gray-500">There are no completed repairs to display at this time.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Completed Maintenance Table -->
        <div class="bg-white rounded-lg shadow p-3 md:p-6 mt-4 md:mt-6 mb-4 md:mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Completed Maintenance History</h2>
                <a href="{{ route('user.maintenance.history') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
            </div>
            <div class="overflow-x-auto">
                @if($personalStats['completed_maintenance_history']->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($personalStats['completed_maintenance_history']->take(5) as $maintenance)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $maintenance->lab_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ is_array($maintenance->maintenance_task) ? implode(', ', $maintenance->maintenance_task) : $maintenance->maintenance_task }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($maintenance->completed_at)->format('M j, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No completed maintenance</h3>
                    <p class="mt-1 text-sm text-gray-500">There are no completed maintenance tasks to display at this time.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Asset Procurement & Disposal -->
    <div class="bg-white p-3 md:p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-3 md:mb-4">
            <h2 class="text-base md:text-lg font-semibold">Asset Procurement & Disposal</h2>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4 mb-4 md:mb-6">
            <div class="sm:border-r sm:pr-4">
                <div class="text-xs md:text-sm text-gray-600">Total Asset Value</div>
                <div class="text-xl md:text-3xl font-bold">PHP {{ number_format($totalAssetValue, 2) }}</div>
            </div>
            <div class="mt-3 sm:mt-0 sm:pl-4">
                <div class="text-xs md:text-sm text-gray-600">Assets Disposed (30 days)</div>
                <div class="text-xl md:text-3xl font-bold">{{ $disposedAssets }}</div>
            </div>
        </div>

        <!-- Procurement Chart -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="h-64">
                <canvas id="procurementValueChart"></canvas>
            </div>
            <div class="h-64 mt-4 md:mt-0">
                <canvas id="assetCountChart"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const monthlyData = @json($monthlyData);

                // Procurement Value Chart
                new Chart(document.getElementById('procurementValueChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: monthlyData.map(item => item.month),
                        datasets: [{
                            label: 'Procurement Value (PHP)',
                            data: monthlyData.map(item => item.procurement_value),
                            backgroundColor: '#FEB35A',
                            borderColor: '#FEB35A',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'PHP ' + value.toLocaleString();
                                    }
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Monthly Procurement Value'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `PHP ${context.raw.toLocaleString()}`;
                                    }
                                }
                            }
                        }
                    }
                });

                // Asset Count Chart
                new Chart(document.getElementById('assetCountChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: monthlyData.map(item => item.month),
                        datasets: [{
                            label: 'Assets Procured',
                            data: monthlyData.map(item => item.procurement_count),
                            backgroundColor: '#4B5563',
                            borderColor: '#4B5563',
                            borderWidth: 1
                        }, {
                            label: 'Assets Disposed',
                            data: monthlyData.map(item => item.disposal_count),
                            backgroundColor: '#EF4444',
                            borderColor: '#EF4444',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Asset Count by Month'
                            }
                        }
                    }
                });
            });
        </script>

        <!-- Status Overview and Recent Urgent Repairs -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 md:mt-6">
            <!-- Status Overview -->
            <div class="bg-white rounded-lg shadow p-3 md:p-6">
                <h3 class="text-base md:text-lg font-semibold mb-3 md:mb-4">Repair Status Overview</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
                    <div class="bg-red-50 p-3 md:p-4 rounded-lg">
                        <p class="text-xs md:text-sm text-gray-600">Open Requests</p>
                        <p class="text-xl md:text-2xl font-bold text-red-600">{{ $totalOpen }}</p>
                    </div>
                    <div class="bg-green-50 p-3 md:p-4 rounded-lg">
                        <p class="text-xs md:text-sm text-gray-600">Completed This Month</p>
                        <p class="text-xl md:text-2xl font-bold text-green-600">{{ $completedThisMonth }}</p>
                    </div>
                    <div class="bg-blue-50 p-3 md:p-4 rounded-lg">
                        <p class="text-xs md:text-sm text-gray-600">Avg. Response Time</p>
                        <p class="text-xl md:text-2xl font-bold text-blue-600">
                            @if($avgResponseDays)
                            {{ $avgResponseDays }} days
                            @else
                            {{ $avgResponseTime }} hrs
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Recent Urgent Repairs -->
            <div class="bg-white rounded-lg shadow p-3 md:p-6 mt-4 md:mt-0">
                <h3 class="text-base md:text-lg font-semibold mb-3 md:mb-4">Recent Urgent Repairs</h3>
                @if($urgentRepairs->count() > 0)
                <div class="space-y-2">
                    @foreach($urgentRepairs->take(3) as $repair)
                    <div class="p-2 md:p-3 bg-red-50 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-xs md:text-sm">{{ $repair->ticket_number }}</span>
                            <span class="text-xs text-gray-600">{{ $repair->created_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-600 text-xs md:text-sm">No urgent repairs at the moment.</p>
                @endif
            </div>
        </div>

        <!-- Lab Utilization Analysis -->
        <div class="bg-white p-3 md:p-6 rounded-lg shadow mt-4 md:mt-6">
            <div class="flex justify-between items-center mb-3 md:mb-4">
                <h2 class="text-base md:text-lg font-semibold">Lab Utilization Analysis</h2>
            </div>

            <!-- Lab Usage Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-4 md:mb-6">
                <div class="bg-blue-50 p-3 md:p-4 rounded-lg">
                    <p class="text-xs md:text-sm text-gray-600">Total Lab Hours</p>
                    <p class="text-xl md:text-2xl font-bold text-blue-600">{{ $totalLabHours }} hrs</p>
                </div>
                <div class="bg-green-50 p-3 md:p-4 rounded-lg">
                    <p class="text-xs md:text-sm text-gray-600">Most Used Lab</p>
                    <p class="text-xl md:text-2xl font-bold text-green-600">{{ $mostUsedLab }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $labUsageData->first()?->days_used ?? 0 }} days this month</p>
                </div>
                <div class="bg-purple-50 p-3 md:p-4 rounded-lg">
                    <p class="text-xs md:text-sm text-gray-600">Average Daily Usage</p>
                    <p class="text-xl md:text-2xl font-bold text-purple-600">{{ $avgDailyUsage }} hrs</p>
                </div>
                <div class="bg-orange-50 p-3 md:p-4 rounded-lg">
                    <p class="text-xs md:text-sm text-gray-600">Peak Usage Time</p>
                    <p class="text-xl md:text-2xl font-bold text-orange-600">{{ $peakHour ? sprintf('%02d:00', $peakHour) : 'N/A' }}</p>
                </div>
            </div>

            <!-- Lab Usage Chart -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="h-64">
                    <canvas id="labUsageChart"></canvas>
                </div>
                <div class="h-64 mt-4 md:mt-0">
                    <canvas id="departmentUsageChart"></canvas>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Lab Usage Chart
                    new Chart(document.getElementById('labUsageChart').getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: @json($labUsageData->pluck('laboratory')),
                            datasets: [{
                                label: 'Hours Used',
                                data: @json($labUsageData->pluck('hours')),
                                backgroundColor: '#4F46E5',
                                borderColor: '#4F46E5',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Hours'
                                    }
                                }
                            },
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Laboratory Usage Distribution'
                                }
                            }
                        }
                    });

                    // Department Usage Chart
                    new Chart(document.getElementById('departmentUsageChart').getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: @json($deptUsageData->pluck('department')),
                            datasets: [{
                                data: @json($deptUsageData->pluck('usage_percentage')),
                                backgroundColor: [
                                    '#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Usage by Department (%)'
                                },
                                legend: {
                                    position: 'bottom'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ' + Math.round(context.raw) + '%';
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
        </div>

        <!-- Warranty Status Section -->
        <div class="bg-white p-3 md:p-6 rounded-lg shadow mt-4 md:mt-6">
            <div class="flex justify-between items-center mb-3 md:mb-4">
                <h2 class="text-base md:text-lg font-semibold">Warranty Status</h2>
            </div>

            <div class="overflow-x-auto">
                @if($warrantyExpiringAssets->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Serial Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warranty Ends</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Left</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($warrantyExpiringAssets as $asset)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <a href="{{ route('assets.index', ['search' => $asset->serial_number]) }}" class="font-bold text-red-600 hover:underline">{{ $asset->serial_number }}</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($asset->warranty_period)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $asset->days_until_warranty_expires <= 0 ? 'bg-red-100 text-red-800' : ($asset->days_until_warranty_expires <= 30 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    @if($asset->days_until_warranty_expires <= 0) Expired @else {{ $asset->days_until_warranty_expires }} days left @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->status }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No warranty alerts</h3>
                    <p class="mt-1 text-sm text-gray-500">There are no assets with expiring warranties at this time.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Asset Lifespan Status Section -->
        <div class="bg-white p-3 md:p-6 rounded-lg shadow mt-4 md:mt-6">
            <div class="flex justify-between items-center mb-3 md:mb-4">
                <h2 class="text-base md:text-lg font-semibold">Asset Lifespan Status</h2>
            </div>

            <div class="overflow-x-auto">
                @if($criticalAndWarningAssets->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End of Life Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining Life</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($criticalAndWarningAssets as $asset)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <a href="{{ route('assets.index', ['search' => $asset->serial_number]) }}" class="font-bold text-red-600 hover:underline">{{ $asset->serial_number }}</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $asset->end_of_life_date ? $asset->end_of_life_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $asset->life_status === 'critical' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ number_format($asset->remaining_life, 2) }} years
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $asset->life_status === 'critical' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($asset->life_status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No lifespan alerts</h3>
                    <p class="mt-1 text-sm text-gray-500">There are no assets with critical or warning lifespan status.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add this script at the end of your dashboard.blade.php file -->
<script>
    document.getElementById('togglePersonalStats').addEventListener('click', function() {
        const section = document.getElementById('personalStatsSection');
        const showText = this.querySelector('.show-text');
        const hideText = this.querySelector('.hide-text');

        section.classList.toggle('hidden');
        showText.classList.toggle('hidden');
        hideText.classList.toggle('hidden');
    });
</script>
@endsection