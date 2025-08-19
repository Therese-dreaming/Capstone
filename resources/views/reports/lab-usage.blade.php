@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 transition-all duration-300" id="mainContent">
    <div class="max-w-7xl mx-auto">
        <!-- Main Container -->
        <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 md:gap-0">
                <div class="flex items-center">
                    <div class="bg-red-100 p-3 rounded-full mr-4">
                        <svg class="w-8 h-8 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Lab Usage Report</h1>
                        <p class="text-gray-600 text-sm md:text-base">Comprehensive laboratory usage analytics and insights</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button onclick="exportPDF()" class="inline-flex items-center px-6 py-3 bg-red-800 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Export PDF
                    </button>
                    <button onclick="exportPaascuUtilization()" class="inline-flex items-center px-6 py-3 bg-green-800 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export PAASCU Utilization
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="transform transition-all duration-300 hover:scale-[1.02] md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <div class="flex space-x-2">
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="h-10 flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="h-10 flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                    </div>
                </div>
                <div class="transform transition-all duration-300 hover:scale-[1.02]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <select name="department_id" class="w-full h-10 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="transform transition-all duration-300 hover:scale-[1.02]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lab</label>
                    <select name="lab_id" class="w-full h-10 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                        <option value="">All Labs</option>
                        @foreach($labs as $lab)
                            <option value="{{ $lab->id }}" {{ request('lab_id') == $lab->id ? 'selected' : '' }}>
                                {{ $lab->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="transform transition-all duration-300 hover:scale-[1.02]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Period</label>
                    <select name="period" class="w-full h-10 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                        <option value="day" {{ $period == 'day' ? 'selected' : '' }}>Daily</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Monthly</option>
                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 shadow-lg border border-red-200 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center">
                        <div class="bg-red-100 p-3 rounded-full mr-4">
                            <svg class="w-6 h-6 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-red-800">Total Sessions</p>
                            <p class="text-2xl font-bold text-red-900">{{ number_format($summary->total_sessions) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 shadow-lg border border-blue-200 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full mr-4">
                            <svg class="w-6 h-6 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-800">Total Hours</p>
                            <p class="text-2xl font-bold text-blue-900">{{ number_format($summary->total_hours, 1) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 shadow-lg border border-green-200 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full mr-4">
                            <svg class="w-6 h-6 text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-800">Average Duration</p>
                            <p class="text-2xl font-bold text-green-900">{{ number_format($summary->avg_duration, 1) }} hours</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 shadow-lg border border-purple-200 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full mr-4">
                            <svg class="w-6 h-6 text-purple-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-purple-800">Unique Users</p>
                            <p class="text-2xl font-bold text-purple-900">{{ number_format($summary->unique_users) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Usage by Department -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Usage by Department
                    </h3>
                    <div class="h-64">
                        <canvas id="departmentChart"></canvas>
                    </div>
                </div>

                <!-- Usage by Lab -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Usage by Laboratory
                    </h3>
                    <div class="h-64">
                        <canvas id="labChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Peak Usage Times -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300 p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Peak Usage Times
                </h3>
                <div class="h-64">
                    <canvas id="peakUsageChart"></canvas>
                </div>
            </div>

            <!-- Color Legend -->
            <div class="mb-8 bg-white rounded-xl border border-gray-200 shadow-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Department Colors -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Department Colors</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @php
                                $deptColors = [
                                    'Early Childhood Education (ECE)' => 'bg-red-100 text-red-800',
                                    'Grade School' => 'bg-blue-100 text-blue-800',
                                    'Junior High School' => 'bg-green-100 text-green-800',
                                    'Senior High Department' => 'bg-yellow-100 text-yellow-800',
                                    'College' => 'bg-purple-100 text-purple-800',
                                    'School of Graduate Studies' => 'bg-pink-100 text-pink-800'
                                ];
                            @endphp
                            @foreach($deptColors as $dept => $color)
                                <div class="flex items-center">
                                    <div class="w-4 h-4 {{ $color }} rounded mr-2"></div>
                                    <span class="text-sm text-gray-600">{{ $dept }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Laboratory Colors -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Laboratory Colors</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @php
                                $labColors = [
                                    '401' => 'bg-red-100 text-red-800',
                                    '402' => 'bg-green-100 text-green-800',
                                    '403' => 'bg-blue-100 text-blue-800',
                                    '404' => 'bg-yellow-100 text-yellow-800',
                                    '405' => 'bg-purple-100 text-purple-800',
                                    '406' => 'bg-pink-100 text-pink-800'
                                ];
                            @endphp
                            @foreach($labColors as $lab => $color)
                                <div class="flex items-center">
                                    <div class="w-4 h-4 {{ $color }} rounded mr-2"></div>
                                    <span class="text-sm text-gray-600">Lab {{ $lab }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage by Department Table -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300 mb-8">
                <div class="bg-gray-50 rounded-t-xl px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Usage by Department
                    </h3>
                </div>
                <!-- Mobile Cards View -->
                <div class="md:hidden divide-y divide-gray-200">
                    @if($departmentUsage->isEmpty())
                        <div class="p-8 text-center">
                            <div class="bg-gray-50 rounded-xl p-6 border-2 border-dashed border-gray-300">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Department Data</h3>
                                <p class="text-gray-500 mb-4">No department usage data found for the selected filters.</p>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                    <p class="text-sm text-blue-800">Try adjusting your date range, department, lab, or period filters to see results.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        @foreach($departmentUsage as $dept)
                        @php
                            $deptColor = $deptColors[$dept->department_name] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            <div class="{{ $deptColor }} rounded-lg p-3 mb-3">
                                <h4 class="text-base font-semibold mb-2">{{ $dept->department_name }}</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="bg-white rounded p-2 shadow-sm">
                                        <span class="text-xs text-gray-500 block">Total Sessions</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ number_format($dept->total_sessions) }}</span>
                                    </div>
                                    <div class="bg-white rounded p-2 shadow-sm">
                                        <span class="text-xs text-gray-500 block">Total Hours</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ number_format($dept->total_hours, 1) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sessions</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Hours</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if($departmentUsage->isEmpty())
                                <tr>
                                    <td colspan="3" class="px-6 py-12">
                                        <div class="text-center">
                                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Department Data</h3>
                                            <p class="text-gray-500 mb-4">No department usage data found for the selected filters.</p>
                                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 max-w-md mx-auto">
                                                <p class="text-sm text-blue-800">Try adjusting your date range, department, lab, or period filters to see results.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @foreach($departmentUsage as $dept)
                                @php
                                    $deptColor = $deptColors[$dept->department_name] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $deptColor }}">
                                            {{ $dept->department_name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($dept->total_sessions) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($dept->total_hours, 1) }}</td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    @if($departmentUsage->isNotEmpty())
                        <div class="px-6 py-4 bg-white border-t border-gray-200">
                            {{ $departmentUsage->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Usage by Lab Table -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300 mb-8">
                <div class="bg-gray-50 rounded-t-xl px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Usage by Laboratory
                    </h3>
                </div>
                <!-- Mobile Cards View -->
                <div class="md:hidden divide-y divide-gray-200">
                    @if($labUsage->isEmpty())
                        <div class="p-8 text-center">
                            <div class="bg-gray-50 rounded-xl p-6 border-2 border-dashed border-gray-300">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Laboratory Data</h3>
                                <p class="text-gray-500 mb-4">No laboratory usage data found for the selected filters.</p>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                    <p class="text-sm text-blue-800">Try adjusting your date range, department, lab, or period filters to see results.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        @foreach($labUsage as $lab)
                        @php
                            $labNumber = substr($lab->lab_name, -3);
                            $labColor = $labColors[$labNumber] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            <div class="{{ $labColor }} rounded-lg p-3 mb-3">
                                <h4 class="text-base font-semibold mb-2">{{ $lab->lab_name }}</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="bg-white rounded p-2 shadow-sm">
                                        <span class="text-xs text-gray-500 block">Total Sessions</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ number_format($lab->total_sessions) }}</span>
                                    </div>
                                    <div class="bg-white rounded p-2 shadow-sm">
                                        <span class="text-xs text-gray-500 block">Total Hours</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ number_format($lab->total_hours, 1) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sessions</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Hours</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if($labUsage->isEmpty())
                                <tr>
                                    <td colspan="3" class="px-6 py-12">
                                        <div class="text-center">
                                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Laboratory Data</h3>
                                            <p class="text-gray-500 mb-4">No laboratory usage data found for the selected filters.</p>
                                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 max-w-md mx-auto">
                                                <p class="text-sm text-blue-800">Try adjusting your date range, department, lab, or period filters to see results.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @foreach($labUsage as $lab)
                                @php
                                    $labNumber = substr($lab->lab_name, -3);
                                    $labColor = $labColors[$labNumber] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $labColor }}">
                                            {{ $lab->lab_name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($lab->total_sessions) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($lab->total_hours, 1) }}</td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    @if($labUsage->isNotEmpty())
                        <div class="px-6 py-4 bg-white border-t border-gray-200">
                            {{ $labUsage->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Detailed Usage Table -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="bg-gray-50 rounded-t-xl px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2-2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Detailed Usage
                    </h3>
                </div>
                <!-- Mobile Cards View -->
                <div class="md:hidden divide-y divide-gray-200">
                    @if($usageData->isEmpty())
                        <div class="p-8 text-center">
                            <div class="bg-gray-50 rounded-xl p-6 border-2 border-dashed border-gray-300">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2-2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Detailed Data</h3>
                                <p class="text-gray-500 mb-4">No detailed usage data found for the selected filters.</p>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                    <p class="text-sm text-blue-800">Try adjusting your date range, department, lab, or period filters to see results.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        @foreach($usageData as $data)
                        @php
                            $deptColor = $deptColors[$data->department_name] ?? 'bg-gray-100 text-gray-800';
                            $labNumber = substr($data->lab_name, -3);
                            $labColor = $labColors[$labNumber] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            <div class="bg-white rounded-lg p-3 mb-3">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-base font-semibold text-gray-800">{{ $data->period }}</h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $labColor }}">
                                        {{ $data->lab_name }}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $deptColor }}">
                                        {{ $data->department_name }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="bg-gray-50 rounded p-2">
                                        <span class="text-xs text-gray-500 block">Sessions</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ number_format($data->total_sessions) }}</span>
                                    </div>
                                    <div class="bg-gray-50 rounded p-2">
                                        <span class="text-xs text-gray-500 block">Total Hours</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ number_format($data->total_hours, 1) }}</span>
                                    </div>
                                    <div class="bg-gray-50 rounded p-2">
                                        <span class="text-xs text-gray-500 block">Avg. Duration</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ number_format($data->avg_duration, 1) }}h</span>
                                    </div>
                                    <div class="bg-gray-50 rounded p-2">
                                        <span class="text-xs text-gray-500 block">Unique Users</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ number_format($data->unique_users) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lab</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sessions</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Hours</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Duration</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unique Users</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if($usageData->isEmpty())
                                <tr>
                                    <td colspan="7" class="px-6 py-12">
                                        <div class="text-center">
                                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2-2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Detailed Data</h3>
                                            <p class="text-gray-500 mb-4">No detailed usage data found for the selected filters.</p>
                                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 max-w-md mx-auto">
                                                <p class="text-sm text-blue-800">Try adjusting your date range, department, lab, or period filters to see results.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @foreach($usageData as $data)
                                @php
                                    $deptColor = $deptColors[$data->department_name] ?? 'bg-gray-100 text-gray-800';
                                    $labNumber = substr($data->lab_name, -3);
                                    $labColor = $labColors[$labNumber] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $data->period }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $deptColor }}">
                                            {{ $data->department_name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $labColor }}">
                                            {{ $data->lab_name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($data->total_sessions) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($data->total_hours, 1) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($data->avg_duration, 1) }}h</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($data->unique_users) }}</td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    @if($usageData->isNotEmpty())
                        <div class="px-6 py-4 bg-white border-t border-gray-200">
                            {{ $usageData->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Department Usage Chart
    new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($departmentUsage->pluck('department_name')) !!},
            datasets: [{
                label: 'Total Hours',
                data: {!! json_encode($departmentUsage->pluck('total_hours')) !!},
                backgroundColor: 'rgba(220, 38, 38, 0.5)',
                borderColor: 'rgb(220, 38, 38)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        font: {
                            family: 'Poppins'
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            family: 'Poppins'
                        }
                    }
                },
                x: {
                    ticks: {
                        font: {
                            family: 'Poppins'
                        }
                    }
                }
            }
        }
    });

    // Lab Usage Chart
    new Chart(document.getElementById('labChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($labUsage->pluck('lab_name')) !!},
            datasets: [{
                label: 'Total Hours',
                data: {!! json_encode($labUsage->pluck('total_hours')) !!},
                backgroundColor: 'rgba(220, 38, 38, 0.5)',
                borderColor: 'rgb(220, 38, 38)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        font: {
                            family: 'Poppins'
                        }
                    }
                    }
                },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            family: 'Poppins'
                        }
                    }
                },
                x: {
                    ticks: {
                        font: {
                            family: 'Poppins'
                        }
                    }
                }
            }
        }
    });

    // Peak Usage Times Chart
    new Chart(document.getElementById('peakUsageChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($peakUsage->pluck('hour')->map(function($hour) { return sprintf('%02d:00', $hour); })) !!},
            datasets: [{
                label: 'Number of Sessions',
                data: {!! json_encode($peakUsage->pluck('total_sessions')) !!},
                fill: false,
                borderColor: 'rgb(220, 38, 38)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        font: {
                            family: 'Poppins'
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            family: 'Poppins'
                        }
                    }
                },
                x: {
                    ticks: {
                        font: {
                            family: 'Poppins'
                        }
                    }
                }
            }
        }
    });

    // Handle filter changes
    const filters = document.querySelectorAll('select, input[type="date"]');
    filters.forEach(filter => {
        filter.addEventListener('change', function() {
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = window.location.pathname;

            filters.forEach(f => {
                if (f.value) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = f.name;
                    input.value = f.value;
                    form.appendChild(input);
                }
            });

            document.body.appendChild(form);
            form.submit();
        });
    });
});

function exportPDF() {
    // Get current filter values
    const startDate = document.querySelector('input[name="start_date"]').value;
    const endDate = document.querySelector('input[name="end_date"]').value;
    const departmentId = document.querySelector('select[name="department_id"]').value;
    const labId = document.querySelector('select[name="lab_id"]').value;
    const period = document.querySelector('select[name="period"]').value;

    // Build query string
    const params = new URLSearchParams();
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);
    if (departmentId) params.append('department_id', departmentId);
    if (labId) params.append('lab_id', labId);
    if (period) params.append('period', period);

    // Redirect to PDF export route
    const url = `{{ route('reports.lab-usage.export') }}?${params.toString()}`;
    window.open(url, '_blank');
}

function exportPaascuUtilization() {
    // Get current filter values
    const startDate = document.querySelector('input[name="start_date"]').value;
    const endDate = document.querySelector('input[name="end_date"]').value;
    const departmentId = document.querySelector('select[name="department_id"]').value;
    const labId = document.querySelector('select[name="lab_id"]').value;
    const period = document.querySelector('select[name="period"]').value;

    // Build query string
    const params = new URLSearchParams();
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);
    if (departmentId) params.append('department_id', departmentId);
    if (labId) params.append('lab_id', labId);
    if (period) params.append('period', period);

    // Redirect to PAASCU utilization export route
    const url = `{{ route('reports.lab-usage.paascu-export') }}?${params.toString()}`;
    window.open(url, '_blank');
}

</script>


@endsection 