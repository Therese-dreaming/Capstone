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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
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

        <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border-l-4 border-purple-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center">
                <div class="bg-purple-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Completion Rate</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($overallStats['completion_rate'] ?? 0, 1) }}%</p>
                    <p class="text-sm text-gray-600">{{ $overallStats['total_tasks'] ?? 0 }} total tasks</p>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Rate</th>
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
                                        <div class="text-sm font-medium text-gray-900">{{ $employee->name }}</div>
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-20 bg-gray-200 rounded-full h-2 mr-3">
                                        @php
                                            $color = $employee->completion_rate >= 90 ? 'bg-green-500' : 
                                                    ($employee->completion_rate >= 75 ? 'bg-yellow-500' : 'bg-red-500');
                                        @endphp
                                        <div class="{{ $color }} h-2 rounded-full" style="width: {{ $employee->completion_rate }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ number_format($employee->completion_rate, 1) }}%</span>
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
                                    <div class="text-sm font-medium text-gray-900">{{ $employee->name }}</div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $employee->role }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Metrics Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-white rounded-lg border border-gray-200">
                                <div class="text-lg font-bold text-blue-600">{{ $employee->repairs_completed }}</div>
                                <div class="text-xs text-gray-500">Repairs</div>
                            </div>
                            <div class="text-center p-3 bg-white rounded-lg border border-gray-200">
                                <div class="text-lg font-bold text-green-600">{{ $employee->maintenance_tasks }}</div>
                                <div class="text-xs text-gray-500">Maintenance</div>
                            </div>
                            <div class="text-center p-3 bg-white rounded-lg border border-gray-200">
                                <div class="text-lg font-bold text-yellow-600">{{ $employee->avg_response_time }}</div>
                                <div class="text-xs text-gray-500">Hours</div>
                            </div>
                            <div class="text-center p-3 bg-white rounded-lg border border-gray-200">
                                <div class="text-lg font-bold text-purple-600">{{ number_format($employee->completion_rate, 1) }}%</div>
                                <div class="text-xs text-gray-500">Completion</div>
                            </div>
                        </div>

                        <!-- Completion Rate Bar -->
                        <div class="mt-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Completion Rate</span>
                                <span class="text-sm text-gray-500">{{ number_format($employee->completion_rate, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                @php
                                    $color = $employee->completion_rate >= 90 ? 'bg-green-500' : 
                                            ($employee->completion_rate >= 75 ? 'bg-yellow-500' : 'bg-red-500');
                                @endphp
                                <div class="{{ $color }} h-2 rounded-full transition-all duration-300" style="width: {{ $employee->completion_rate }}%"></div>
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