@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Page Header -->
    <div class="mb-6 md:mb-8">
        <div class="bg-gradient-to-r from-red-800 to-red-700 rounded-xl shadow-lg p-6 md:p-8 text-white">
            <div class="text-center">
                <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Employee Performance Analysis</h1>
                <p class="text-red-100 text-sm md:text-base">
                    Overall performance metrics for all employees
                </p>
            </div>
        </div>
    </div>



    <!-- Overall Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
        <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border-l-4 border-blue-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Repairs</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $overallStats['total_repairs'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Avg: {{ number_format($overallStats['avg_repairs_per_employee'] ?? 0, 1) }} per employee</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border-l-4 border-green-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Maintenance Tasks</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $overallStats['total_maintenance'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Avg: {{ number_format($overallStats['avg_maintenance_per_employee'] ?? 0, 1) }} per employee</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border-l-4 border-yellow-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center">
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Response Time</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $overallStats['avg_response_time'] ?? 0 }}h</p>
                    <p class="text-sm text-gray-600">Best: {{ $overallStats['best_response_time'] ?? 0 }}h</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Employee Performance Section -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Individual Employee Performance</h2>
            <p class="text-sm text-gray-600 mt-1">Detailed metrics for each employee during the selected period</p>
        </div>
        
        @if(isset($employeeStats) && count($employeeStats) > 0)
            <!-- Desktop Table View (hidden on mobile) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Repairs</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Maintenance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response Time</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($employeeStats as $employee)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-red-100 p-2 rounded-full">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <button type="button" class="text-left text-sm font-medium text-red-700 hover:underline employee-toggle" data-user-id="{{ $employee->id }}" data-user-name="{{ $employee->name }}">
                                            {{ $employee->name }}
                                        </button>
                                    </div>
                                </div>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $employee->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <span class="font-medium">{{ $employee->repairs_completed }}</span>
                                    <span class="text-gray-500 ml-1">completed</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <span class="font-medium">{{ $employee->maintenance_tasks }}</span>
                                    <span class="text-gray-500 ml-1">tasks</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <span class="font-medium">{{ $employee->avg_response_time }}</span>
                                    <span class="text-gray-500 ml-1">hours</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View (visible only on mobile) -->
            <div class="md:hidden p-4 md:p-6">
                <div class="space-y-4">
                    @foreach($employeeStats as $employee)
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 hover:shadow-md transition-shadow duration-200">
                        <!-- Employee Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="bg-red-100 p-2 rounded-full">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <button type="button" class="text-left text-sm font-medium text-red-700 hover:underline employee-toggle" data-user-id="{{ $employee->id }}" data-user-name="{{ $employee->name }}">
                                        {{ $employee->name }}
                                    </button>
                                    <span class="block mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $employee->role }}
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <div class="bg-gray-50 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Performance Data Available</h3>
                <p class="text-gray-500 mb-4">No employee performance data found for the selected period.</p>
                <div class="text-sm text-gray-400">
                    <p>• Try adjusting the date range</p>
                    <p>• Ensure employees have completed tasks during this period</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Selected Employee Charts Panel -->
    <div id="employeeChartsPanel" class="mt-6 md:mt-8 hidden">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-red-800 to-red-700 text-white flex items-center justify-between">
                <div>
                    <h3 id="panelTitle" class="text-lg md:text-xl font-semibold">Employee Insights</h3>
                    <p class="text-red-100 text-xs md:text-sm">Repair, Maintenance, and Rating breakdown</p>
                </div>
                <button id="closePanel" class="text-white/90 hover:text-white rounded-md border border-white/20 px-3 py-1 text-sm">Close</button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="rounded-xl border border-gray-100 p-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Repairs</h4>
                        <canvas id="globalRepairChart" height="160"></canvas>
                        <div class="mt-3 text-xs text-gray-600">
                            <span class="inline-flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>Completed</span>
                            <span class="ml-4 inline-flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full bg-amber-500"></span>Ongoing</span>
                            <span class="ml-4 inline-flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full bg-red-500"></span>Pulled Out <span id="pulledOutBreakdown" class="ml-1 text-[11px] text-gray-500"></span></span>
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-100 p-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Maintenance</h4>
                        <canvas id="globalMaintChart" height="160"></canvas>
                    </div>
                    <div class="rounded-xl border border-gray-100 p-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Average Rating</h4>
                        <div class="flex items-center gap-2">
                            <div id="globalRatingStars" class="flex items-center gap-1"></div>
                            <span id="globalRatingValue" class="text-sm text-gray-600"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="mt-6 md:mt-8 bg-white rounded-xl shadow-md p-6 md:p-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Summary</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-md font-medium text-gray-700 mb-3">Period Overview</h3>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Total Employees:</span>
                        <span class="font-medium">{{ count($employeeStats ?? []) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Tasks:</span>
                        <span class="font-medium">{{ ($overallStats['total_repairs'] ?? 0) + ($overallStats['total_maintenance'] ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Average Response Time:</span>
                        <span class="font-medium">{{ $overallStats['avg_response_time'] ?? 0 }} hours</span>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="text-md font-medium text-gray-700 mb-3">Performance Insights</h3>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                        <span>High performers: {{ $overallStats['completion_rate'] >= 90 ? 'Excellent' : ($overallStats['completion_rate'] >= 75 ? 'Good' : 'Needs improvement') }}</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                        <span>Team efficiency: {{ number_format($overallStats['avg_repairs_per_employee'] ?? 0, 1) }} repairs per person</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-purple-500 rounded-full mr-2"></div>
                        <span>Maintenance coverage: {{ number_format($overallStats['avg_maintenance_per_employee'] ?? 0, 1) }} tasks per person</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartInstances = {};
        const panel = document.getElementById('employeeChartsPanel');
        const panelTitle = document.getElementById('panelTitle');
        const closePanelBtn = document.getElementById('closePanel');
        let globalCharts = { repair: null, maint: null, rating: null };

        closePanelBtn.addEventListener('click', () => {
            panel.classList.add('hidden');
        });

        function makeDoughnut(ctx, labels, data, colors) {
            return new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }

        function renderStars(container, rating) {
            container.innerHTML = '';
            const fullStars = Math.floor(rating);
            const half = rating - fullStars >= 0.5;
            const emptyStars = 5 - fullStars - (half ? 1 : 0);
            const starSvg = (fill) => `
                <svg class="w-5 h-5 ${fill}" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.802-2.034a1 1 0 00-1.176 0l-2.802 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 00.95-.69l1.07-3.292z" />
                </svg>`;
            const halfStarSvg = `
                <svg class="w-5 h-5 text-yellow-400" viewBox="0 0 24 24" fill="currentColor">
                    <defs>
                        <linearGradient id="halfGrad" x1="0" x2="1" y1="0" y2="0">
                            <stop offset="50%" stop-color="#fbbf24" />
                            <stop offset="50%" stop-color="#e5e7eb" />
                        </linearGradient>
                    </defs>
                    <path fill="url(#halfGrad)" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                </svg>`;
            for (let i = 0; i < fullStars; i++) container.insertAdjacentHTML('beforeend', starSvg('text-yellow-400'));
            if (half) container.insertAdjacentHTML('beforeend', halfStarSvg);
            for (let i = 0; i < emptyStars; i++) container.insertAdjacentHTML('beforeend', starSvg('text-gray-300'));
        }

        async function loadMetrics(userId) {
            const res = await fetch(`/employee-performance/${userId}/metrics`);
            if (!res.ok) return null;
            return await res.json();
        }

        document.querySelectorAll('.employee-toggle').forEach(btn => {
            btn.addEventListener('click', async () => {
                const userId = btn.dataset.userId;
                const name = btn.dataset.userName;
                panelTitle.textContent = `${name} • Employee Insights`;

                const metrics = await loadMetrics(userId);
                if (!metrics) return;

                panel.classList.remove('hidden');

                const repairCtx = document.getElementById('globalRepairChart').getContext('2d');
                const maintCtx = document.getElementById('globalMaintChart').getContext('2d');
                // Destroy previous charts to avoid duplicates
                if (globalCharts.repair) { globalCharts.repair.destroy(); }
                if (globalCharts.maint) { globalCharts.maint.destroy(); }

                globalCharts.repair = makeDoughnut(
                    repairCtx,
                    ['Completed', 'Ongoing', 'Pulled Out'],
                    [metrics.repairs.completed, metrics.repairs.ongoing, metrics.repairs.pulled_out],
                    ['#10b981', '#f59e0b', '#ef4444']
                );

                globalCharts.maint = makeDoughnut(
                    maintCtx,
                    ['Completed', 'Ongoing', 'Cancelled'],
                    [metrics.maintenance.completed, metrics.maintenance.ongoing, metrics.maintenance.cancelled],
                    ['#10b981', '#f59e0b', '#6b7280']
                );

                // Update pulled out breakdown text
                const breakdownEl = document.getElementById('pulledOutBreakdown');
                if (breakdownEl && metrics.repairs.pulled_out_registered !== undefined) {
                    breakdownEl.textContent = `(Reg: ${metrics.repairs.pulled_out_registered}, Non-Reg: ${metrics.repairs.pulled_out_nonregistered})`;
                }

                // Render stars for rating
                const ratingStars = document.getElementById('globalRatingStars');
                const ratingValue = document.getElementById('globalRatingValue');
                renderStars(ratingStars, metrics.rating || 0);
                ratingValue.textContent = `${(metrics.rating || 0).toFixed(2)} / 5`;
            });
        });
    });
</script>
@endsection