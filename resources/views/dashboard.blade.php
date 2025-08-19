@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Page Header with Background Design -->
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center">
                <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                    <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Asset Management Dashboard</h1>
                    <p class="text-red-100 text-sm md:text-lg">Monitor and manage your asset portfolio</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-xl shadow-md p-2 mb-4 md:mb-6">
        <ul class="flex flex-wrap text-sm font-medium text-center" id="dashboardTabs" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-block p-3 md:p-4 rounded-lg active transition-all duration-200" id="dashboard-tab" data-tabs-target="#dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="true">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-1 md:mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </button>
            </li>
            @if(auth()->user()->group_id == 1)
            <li class="mr-2" role="presentation">
                <a href="{{ route('employee-performance') }}" class="inline-block p-3 md:p-4 rounded-lg hover:bg-gray-100 transition-all duration-200">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-1 md:mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Employee Performance
                </a>
            </li>
            @endif
            <li class="mr-2" role="presentation">
                <button class="inline-block p-3 md:p-4 rounded-lg hover:bg-gray-100 transition-all duration-200" id="statistics-tab" data-tabs-target="#statistics" type="button" role="tab" aria-controls="statistics" aria-selected="false">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-1 md:mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    My Statistics
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab Content -->
    <div id="dashboardTabContent">
        <!-- Dashboard Tab -->
        <div class="block" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
            <!-- Asset Procurement & Disposal -->
            <div class="bg-white rounded-xl shadow-md p-4 md:p-6 mb-4 md:mb-6">
                <div class="flex justify-between items-center mb-4 md:mb-6">
                    <h2 class="text-lg md:text-xl font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                        Asset Procurement & Disposal
                    </h2>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 md:p-6 rounded-xl border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs md:text-sm font-medium text-blue-700 mb-1">Total Asset Value</div>
                                <div class="text-2xl md:text-3xl font-bold text-blue-900">PHP {{ number_format($totalAssetValue, 2) }}</div>
                            </div>
                            <div class="bg-blue-200 p-2 md:p-3 rounded-full">
                                <svg class="w-6 h-6 md:w-8 md:h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 md:p-6 rounded-xl border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs md:text-sm font-medium text-green-700 mb-1">Assets Disposed (30 days)</div>
                                <div class="text-2xl md:text-3xl font-bold text-green-900">{{ $disposedAssets }}</div>
                            </div>
                            <div class="bg-green-200 p-2 md:p-3 rounded-full">
                                <svg class="w-6 h-6 md:w-8 md:h-8 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Procurement Chart -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div class="bg-gray-50 p-3 md:p-4 rounded-xl">
                        <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-3 md:mb-4 text-center">Monthly Procurement Value</h3>
                        <div class="h-48 md:h-64">
                            <canvas id="procurementValueChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-3 md:p-4 rounded-xl">
                        <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-3 md:mb-4 text-center">Assets by Category</h3>
                        <div class="h-48 md:h-64">
                            <canvas id="assetCountChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Overview and Recent Urgent Repairs -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-4 md:mb-6">
                <!-- Status Overview -->
                <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
                    <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-4 md:mb-6 flex items-center">
                        <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Repair Status Overview
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
                        <div class="bg-gradient-to-r from-red-50 to-red-100 p-3 md:p-4 rounded-xl border border-red-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs md:text-sm font-medium text-red-700 mb-1">Open Requests</p>
                                    <p class="text-xl md:text-2xl font-bold text-red-800">{{ $totalOpen }}</p>
                                </div>
                                <div class="bg-red-200 p-2 rounded-full">
                                    <svg class="w-5 h-5 md:w-6 md:h-6 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-r from-green-50 to-green-100 p-3 md:p-4 rounded-xl border border-green-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs md:text-sm font-medium text-green-700 mb-1">Completed This Month</p>
                                    <p class="text-xl md:text-2xl font-bold text-green-800">{{ $completedThisMonth }}</p>
                                </div>
                                <div class="bg-green-200 p-2 rounded-full">
                                    <svg class="w-5 h-5 md:w-6 md:h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-3 md:p-4 rounded-xl border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs md:text-sm font-medium text-blue-700 mb-1">Avg. Response Time</p>
                                    <p class="text-xl md:text-2xl font-bold text-blue-800">
                                        @if($avgResponseDays)
                                        {{ $avgResponseDays }} days
                                        @else
                                        {{ $avgResponseTime }}
                                        @endif
                                    </p>
                                </div>
                                <div class="bg-blue-200 p-2 rounded-full">
                                    <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Urgent Repairs -->
                <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
                    <div class="flex justify-between items-center mb-4 md:mb-6">
                        <h3 class="text-lg md:text-xl font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Recent Urgent Repairs
                        </h3>
                        <a href="{{ route('repair.status') }}" class="text-xs md:text-sm text-red-600 hover:text-red-800 font-medium hover:underline">View All</a>
                    </div>
                    
                    @if(count($urgentRepairs) > 0)
                    <div class="space-y-3 md:space-y-4">
                        @foreach($urgentRepairs as $repair)
                        <div class="border-l-4 border-red-500 bg-red-50 p-3 md:p-4 rounded-r-xl hover:bg-red-100 transition-colors duration-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900 mb-1 truncate">
                                        @if($repair->asset && $repair->asset->name)
                                            {{ $repair->asset->name }}
                                        @else
                                            {{ $repair->equipment ?? 'Unknown Equipment' }}
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ $repair->issue }}</p>
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center text-xs text-gray-500 gap-1">
                                        <span>Reported: {{ $repair->created_at->format('M j, Y') }}</span>
                                        <a href="{{ route('repair.show', $repair->id) }}" class="text-red-600 hover:text-red-800 font-medium hover:underline">View Details</a>
                                    </div>
                                </div>
                                <span class="px-2 md:px-3 py-1 text-xs font-semibold rounded-full bg-red-200 text-red-800 ml-2 md:ml-3 flex-shrink-0">Urgent</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-6 md:py-8 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                        <div class="bg-gray-100 rounded-full p-3 md:p-4 w-12 h-12 md:w-16 md:h-16 mx-auto mb-3 md:mb-4 flex items-center justify-center">
                            <svg class="h-6 w-6 md:h-8 md:w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 mb-1">No urgent repairs</h3>
                        <p class="text-xs md:text-sm text-gray-500">There are no urgent repair requests at this time.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Asset Alerts Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-4 md:mb-6">
                <!-- Warranty Expiring Assets -->
                <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
                    <div class="flex justify-between items-center mb-4 md:mb-6">
                        <h3 class="text-lg md:text-xl font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Warranty Expiring Assets
                        </h3>
                        <a href="{{ route('assets.index') }}" class="text-xs md:text-sm text-yellow-600 hover:text-yellow-800 font-medium hover:underline">View All</a>
                    </div>
                    
                    @if($warrantyExpiringAssets->count() > 0)
                    <div class="space-y-3 md:space-y-4">
                        @foreach($warrantyExpiringAssets->take(5) as $asset)
                        <div class="border-l-4 border-yellow-500 bg-yellow-50 p-3 md:p-4 rounded-r-xl hover:bg-yellow-100 transition-colors duration-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900 mb-1 truncate">{{ $asset->name }}</p>
                                    <p class="text-sm text-gray-600 mb-2 truncate">{{ $asset->serial_number }}</p>
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center text-xs text-gray-500 gap-1">
                                        <span>Warranty: {{ \Carbon\Carbon::parse($asset->warranty_period)->format('M j, Y') }}</span>
                                    </div>
                                </div>
                                <span class="px-2 md:px-3 py-1 text-xs font-semibold rounded-full bg-yellow-200 text-yellow-800 ml-2 md:ml-3 flex-shrink-0">
                                    @if($asset->days_until_warranty_expires < 0)
                                        Expired
                                    @elseif($asset->days_until_warranty_expires <= 30)
                                        {{ $asset->days_until_warranty_expires }} days
                                    @else
                                        {{ $asset->days_until_warranty_expires }} days
                                    @endif
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-6 md:py-8 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                        <div class="bg-gray-100 rounded-full p-3 md:p-4 w-12 h-12 md:w-16 md:h-16 mx-auto mb-3 md:mb-4 flex items-center justify-center">
                            <svg class="h-6 w-6 md:h-8 md:w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 mb-1">No warranty alerts</h3>
                        <p class="text-xs md:text-sm text-gray-500">All assets have valid warranties.</p>
                    </div>
                    @endif
                </div>

                <!-- Critical & Warning Assets -->
                <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
                    <div class="flex justify-between items-center mb-4 md:mb-6">
                        <h3 class="text-lg md:text-xl font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Critical & Warning Assets
                        </h3>
                        <a href="{{ route('assets.index') }}" class="text-xs md:text-sm text-red-600 hover:text-red-800 font-medium hover:underline">View All</a>
                    </div>
                    
                    @if($criticalAndWarningAssets->count() > 0)
                    <div class="space-y-3 md:space-y-4">
                        @foreach($criticalAndWarningAssets->take(5) as $asset)
                        <div class="border-l-4 @if($asset->life_status === 'critical') border-red-500 bg-red-50 @else border-yellow-500 bg-yellow-50 @endif p-3 md:p-4 rounded-r-xl hover:bg-opacity-75 transition-colors duration-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900 mb-1 truncate">{{ $asset->name }}</p>
                                    <p class="text-sm text-gray-600 mb-2 truncate">{{ $asset->serial_number }}</p>
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center text-xs text-gray-500 gap-1">
                                        <span>Days Left: {{ $asset->days_left ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <span class="px-2 md:px-3 py-1 text-xs font-semibold rounded-full @if($asset->life_status === 'critical') bg-red-200 text-red-800 @else bg-yellow-200 text-yellow-800 @endif ml-2 md:ml-3 flex-shrink-0">
                                    @if($asset->life_status === 'critical')
                                        Critical
                                    @else
                                        Warning
                                    @endif
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-6 md:py-8 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                        <div class="bg-gray-100 rounded-full p-3 md:p-4 w-12 h-12 md:w-16 md:h-16 mx-auto mb-3 md:mb-4 flex items-center justify-center">
                            <svg class="h-6 w-6 md:h-8 md:w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 mb-1">No critical assets</h3>
                        <p class="text-xs md:text-sm text-gray-500">All assets are in good condition.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>



        <!-- My Statistics Tab -->
        <div class="hidden" id="statistics" role="tabpanel" aria-labelledby="statistics-tab">
            <div class="space-y-4 md:space-y-6">
                <!-- Maintenance and Repair Statistics -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 md:p-6 rounded-xl shadow-md border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs md:text-sm font-medium text-blue-700 mb-1">Completed Repairs</p>
                                <p class="text-2xl md:text-3xl font-bold text-blue-900">{{ $personalStats['completed_repairs'] }}</p>
                            </div>
                            <div class="bg-blue-200 p-2 md:p-3 rounded-full">
                                <svg class="w-6 h-6 md:w-8 md:h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 md:p-6 rounded-xl shadow-md border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs md:text-sm font-medium text-green-700 mb-1">Completed Maintenance</p>
                                <p class="text-2xl md:text-3xl font-bold text-green-900">{{ $personalStats['completed_maintenance'] }}</p>
                            </div>
                            <div class="bg-green-200 p-2 md:p-3 rounded-full">
                                <svg class="w-6 h-6 md:w-8 md:h-8 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Completed Repairs Table -->
                <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
                    <div class="flex justify-between items-center mb-4 md:mb-6">
                        <h2 class="text-lg md:text-xl font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Completed Repairs History
                        </h2>
                        <a href="{{ route('repairs.history') }}" class="text-blue-600 hover:text-blue-800 text-xs md:text-sm font-medium hover:underline">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        @if($personalStats['completed_repairs_history']->count() > 0)
                        <!-- Desktop Table View -->
                        <div class="hidden md:block">
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
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($repair->asset && $repair->asset->serial_number)
                                                <a href="{{ route('assets.index', ['search' => $repair->asset->serial_number]) }}" class="font-bold text-red-600 hover:underline">{{ $repair->asset->serial_number }}</a>
                                            @else
                                                <span class="text-gray-500">{{ $repair->equipment ?? 'Unknown Equipment' }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $repair->issue }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($repair->completed_at)->format('M j, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="md:hidden space-y-3 md:space-y-4">
                            @foreach($personalStats['completed_repairs_history']->take(5) as $repair)
                            <div class="bg-gray-50 rounded-lg p-3 md:p-4 hover:bg-gray-100 transition-colors duration-200">
                                <div class="flex justify-between items-start mb-2">
                                    @if($repair->asset && $repair->asset->serial_number)
                                        <a href="{{ route('assets.index', ['search' => $repair->asset->serial_number]) }}" class="font-bold text-red-600 hover:underline truncate">{{ $repair->asset->serial_number }}</a>
                                    @else
                                        <span class="text-gray-500 truncate">{{ $repair->equipment ?? 'Unknown Equipment' }}</span>
                                    @endif
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 flex-shrink-0">Completed</span>
                                </div>
                                <div class="text-sm text-gray-600 mb-1 line-clamp-2">{{ $repair->issue }}</div>
                                <div class="text-sm text-gray-500">Completed: {{ \Carbon\Carbon::parse($repair->completed_at)->format('M j, Y') }}</div>
                            </div>
                            @endforeach
                        </div>
                        @if($personalStats['completed_repairs_history']->count() > 5)
                        <div class="mt-4 md:mt-6 flex justify-center">
                            <a href="{{ route('repairs.history') }}" class="text-xs md:text-sm text-red-600 hover:text-red-800 font-medium hover:underline">View All Repairs</a>
                        </div>
                        @endif
                        @else
                        <div class="text-center py-8 md:py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                            <div class="bg-gray-100 rounded-full p-3 md:p-4 w-16 h-16 md:w-20 md:h-20 mx-auto mb-3 md:mb-4 flex items-center justify-center">
                                <svg class="h-8 w-8 md:h-10 md:w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">No completed repairs</h3>
                            <p class="text-xs md:text-sm text-gray-500">There are no completed repairs to display at this time.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Completed Maintenance Table -->
                <div class="bg-white rounded-xl shadow-md p-4 md:p-6 mb-4 md:mb-6">
                    <div class="flex justify-between items-center mb-4 md:mb-6">
                        <h2 class="text-lg md:text-xl font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Completed Maintenance History
                        </h2>
                        <a href="{{ route('user.maintenance.history') }}" class="text-blue-600 hover:text-blue-800 text-xs md:text-sm font-medium hover:underline">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        @if($personalStats['completed_maintenance_history']->count() > 0)
                        <!-- Desktop Table View -->
                        <div class="hidden md:block">
                        <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($personalStats['completed_maintenance_history']->take(5) as $maintenance)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $maintenance->maintenance_task ?? 'Unknown Task' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($maintenance->completed_at)->format('M j, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="md:hidden space-y-3 md:space-y-4">
                            @foreach($personalStats['completed_maintenance_history']->take(5) as $maintenance)
                            <div class="bg-gray-50 rounded-lg p-3 md:p-4 hover:bg-gray-100 transition-colors duration-200">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-gray-900 truncate">{{ $maintenance->maintenance_task ?? 'Unknown Task' }}</span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 flex-shrink-0">Completed</span>
                                </div>

                                <div class="text-sm text-gray-500">Completed: {{ \Carbon\Carbon::parse($maintenance->completed_at)->format('M j, Y') }}</div>
                            </div>
                            @endforeach
                        </div>
                        @if($personalStats['completed_maintenance_history']->count() > 5)
                        <div class="mt-4 md:mt-6 flex justify-center">
                            <a href="{{ route('user.maintenance.history') }}" class="text-xs md:text-sm text-red-600 hover:text-red-800 font-medium hover:underline">View All Maintenance</a>
                        </div>
                        @endif
                        @else
                        <div class="text-center py-8 md:py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                            <div class="bg-gray-100 rounded-full p-3 md:p-4 w-16 h-16 md:w-20 md:h-20 mx-auto mb-3 md:mb-4 flex items-center justify-center">
                                <svg class="h-8 w-8 md:h-10 md:w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">No completed maintenance</h3>
                            <p class="text-xs md:text-sm text-gray-500">There are no completed maintenance tasks to display at this time.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
                    <h2 class="text-lg md:text-xl font-semibold text-gray-900 mb-4 md:mb-6 flex items-center">
                        <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Your Performance Metrics
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 md:p-6 rounded-xl border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xs md:text-sm font-medium text-blue-700 mb-1">Average Response Time</h3>
                                    <p class="text-xl md:text-2xl font-bold text-blue-900">{{ $personalStats['avg_response_time'] ?? 'N/A' }}</p>
                                    <p class="text-xs text-blue-600 mt-1">Time to first response</p>
                                </div>
                                <div class="bg-blue-200 p-2 md:p-3 rounded-full">
                                    <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 md:p-6 rounded-xl border border-green-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xs md:text-sm font-medium text-green-700 mb-1">Completion Rate</h3>
                                    <p class="text-xl md:text-2xl font-bold text-green-900">{{ $personalStats['completion_rate'] ?? 'N/A' }}%</p>
                                    <p class="text-xs text-green-600 mt-1">Tasks completed at the time</p>
                                </div>
                                <div class="bg-green-200 p-2 md:p-3 rounded-full">
                                    <svg class="w-5 h-5 md:w-6 md:h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        

                        

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Tabs -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all tab buttons and content
        const tabs = document.querySelectorAll('[data-tabs-target]');
        const tabContents = document.querySelectorAll('[role="tabpanel"]');
        
        // Check for tab parameter in URL
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        
        // Function to activate a tab
        function activateTab(tabId) {
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active state from all tabs
            tabs.forEach(tab => {
                tab.classList.remove('text-red-800', 'border-red-800', 'bg-red-50');
                tab.classList.add('border-transparent');
                tab.setAttribute('aria-selected', 'false');
            });
            
            // Show the selected tab content
            const selectedContent = document.getElementById(tabId);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
                selectedContent.classList.add('block');
            }
            
            // Set active state on the selected tab
            const selectedTab = document.querySelector(`[data-tabs-target="#${tabId}"]`);
            if (selectedTab) {
                selectedTab.classList.remove('border-transparent');
                selectedTab.classList.add('text-red-800', 'border-red-800', 'bg-red-50');
                selectedTab.setAttribute('aria-selected', 'true');
            }
        }
        
        // Initialize tabs based on URL parameter or default to first tab
        if (tabParam && document.getElementById(tabParam)) {
            activateTab(tabParam);
        } else {
            // Default to first tab
            const firstTabId = tabs[0].getAttribute('data-tabs-target').substring(1);
            activateTab(firstTabId);
        }
        
        // Add click event listeners to tabs
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.getAttribute('data-tabs-target').substring(1);
                activateTab(target);
                
                // Update URL with the selected tab (without page reload)
                const url = new URL(window.location);
                url.searchParams.set('tab', target);
                window.history.pushState({}, '', url);
            });
        });
    });
</script>

<!-- Chart.js for Dashboard Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Procurement Value Chart
        const procurementCtx = document.getElementById('procurementValueChart');
        if (procurementCtx) {
            new Chart(procurementCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($procurementChartData['labels']) !!},
                    datasets: [{
                        label: 'Procurement Value (PHP)',
                        data: {!! json_encode($procurementChartData['values']) !!},
                        borderColor: '#991b1b',
                        backgroundColor: 'rgba(153, 27, 27, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        title: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'PHP ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Asset Count Chart
        const assetCountCtx = document.getElementById('assetCountChart');
        if (assetCountCtx) {
            new Chart(assetCountCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($assetCountChartData['labels']) !!},
                    datasets: [{
                        label: 'Asset Count',
                        data: {!! json_encode($assetCountChartData['values']) !!},
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(239, 68, 68, 0.7)',
                            'rgba(139, 92, 246, 0.7)',
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection