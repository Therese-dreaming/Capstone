@extends('layouts.app')

@section('title', 'Vendor Analysis')

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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Vendor Analysis Report</h1>
                        <p class="text-gray-600 text-sm md:text-base">Comprehensive vendor performance and reliability analysis</p>
                    </div>
                </div>
                <button type="button" onclick="previewPDF()" class="print:hidden inline-flex items-center px-6 py-3 bg-red-800 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Preview PDF
                </button>
            </div>

            <!-- Overall Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 shadow-lg border border-red-200 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center">
                        <div class="bg-red-100 p-3 rounded-full mr-4">
                            <svg class="w-6 h-6 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-red-800">Total Vendors</p>
                            <p class="text-2xl md:text-3xl font-bold text-red-900">{{ $overallStats['total_vendors'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 shadow-lg border border-blue-200 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full mr-4">
                            <svg class="w-6 h-6 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-800">Total Assets</p>
                            <p class="text-2xl md:text-3xl font-bold text-blue-900">{{ number_format($overallStats['total_assets']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 shadow-lg border border-green-200 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full mr-4">
                            <svg class="w-6 h-6 text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-green-800">Total Value</p>
                            <p class="text-2xl md:text-3xl font-bold text-green-900 break-words">₱{{ number_format($overallStats['total_value'], 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 shadow-lg border border-purple-200 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full mr-4">
                            <svg class="w-6 h-6 text-purple-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-purple-800">Analysis Ready</p>
                            <p class="text-2xl md:text-3xl font-bold text-purple-900">{{ isset($noData) ? '0' : count($vendorAnalysis) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="mb-8">
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Vendor Performance Charts
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Vendor Asset Count Chart - Full Width -->
                        <div class="bg-white rounded-xl shadow-md p-4">
                            <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Asset Count by Vendor
                            </h4>
                            <div class="h-80">
                                <canvas id="vendorAssetCountChart"></canvas>
                            </div>
                        </div>

                        <!-- Vendor Asset Value and Performance Charts - Side by Side -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Vendor Asset Value Chart -->
                            <div class="bg-white rounded-xl shadow-md p-4">
                                <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                    Asset Value by Vendor
                                </h4>
                                <div class="h-80">
                                    <canvas id="vendorAssetValueChart"></canvas>
                                </div>
                            </div>

                            <!-- Vendor Performance Distribution Chart -->
                            <div class="bg-white rounded-xl shadow-md p-4">
                                <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                                    </svg>
                                    Performance Distribution
                                </h4>
                                <div class="h-80">
                                    <canvas id="vendorPerformanceChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vendor Performance Table -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-lg">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center p-6 border-b border-gray-200 gap-4">
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Vendor Performance Analysis
                    </h2>
                    @if(!isset($noData))
                    <div class="relative w-full md:w-auto">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="vendorSearch" placeholder="Search vendors..." 
                               class="w-full md:w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors print:hidden">
                    </div>
                    @endif
                </div>

                @if(isset($noData))
                <div class="p-12 text-center">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900 mb-3">No Vendor Data Available</h3>
                    <p class="text-gray-600 mb-6">There are no assets assigned to vendors yet. To see vendor analysis:</p>
                    <div class="space-y-3 text-sm text-gray-600 max-w-md mx-auto">
                        <div class="flex items-center">
                            <span class="bg-red-100 text-red-800 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mr-3">1</span>
                            <p>Go to <a href="{{ route('assets.create') }}" class="text-red-600 hover:text-red-700 font-medium">Add Asset</a> and assign vendors to your assets</p>
                        </div>
                        <div class="flex items-center">
                            <span class="bg-red-100 text-red-800 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mr-3">2</span>
                            <p>Or update existing assets to include vendor information</p>
                        </div>
                        <div class="flex items-center">
                            <span class="bg-red-100 text-red-800 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mr-3">3</span>
                            <p>Once assets are assigned to vendors, this analysis will show detailed performance metrics</p>
                        </div>
                    </div>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="vendorTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor Name</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assets</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Repairs</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Rate</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disposed Assets</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Age (Years)</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($vendorAnalysis as $vendor)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-red-100 p-2 rounded-lg mr-3">
                                            <svg class="w-4 h-4 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">{{ $vendor['name'] }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $vendor['total_assets'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 break-words">
                                    <span class="block">₱{{ number_format($vendor['total_value'], 2) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $vendor['total_repairs'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $vendor['completion_rate'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium">{{ number_format($vendor['completion_rate'], 1) }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-bold">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ $vendor['disposed_count'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $vendor['average_age'] ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" 
                                            onclick="showVendorDetails({{ $vendor['id'] }}, '{{ addslashes($vendor['name']) }}')"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-xs font-medium rounded-lg text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Details
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Vendor Details Modal -->
<div id="vendorDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-6 border w-11/12 md:w-3/4 lg:w-1/2 shadow-xl rounded-xl bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Vendor Details</h3>
                <button onclick="closeVendorDetails()" class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="vendorDetailsContent" class="space-y-4">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Chart data from PHP
    const chartData = {
        vendors: @json(isset($vendorAnalysis) ? collect($vendorAnalysis)->pluck('name') : []),
        assetCounts: @json(isset($vendorAnalysis) ? collect($vendorAnalysis)->pluck('total_assets') : []),
        assetValues: @json(isset($vendorAnalysis) ? collect($vendorAnalysis)->pluck('total_value') : []),
        completionRates: @json(isset($vendorAnalysis) ? collect($vendorAnalysis)->pluck('completion_rate') : []),
        colors: [
            '#EF4444', '#F59E0B', '#10B981', '#3B82F6', 
            '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16',
            '#F97316', '#6366F1', '#14B8A6', '#F43F5E'
        ]
    };

    // Wait for DOM to be loaded before creating charts
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, chart data:', chartData); // Debug log
        
        // Only create charts if there's vendor data
        if (chartData.vendors && chartData.vendors.length > 0) {
            console.log('Creating charts with', chartData.vendors.length, 'vendors'); // Debug log
            
            // Vendor Asset Count Chart (Bar Chart)
            const vendorAssetCountCtx = document.getElementById('vendorAssetCountChart');
            if (vendorAssetCountCtx) {
                console.log('Creating asset count chart'); // Debug log
                new Chart(vendorAssetCountCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: chartData.vendors,
                        datasets: [{
                            label: 'Asset Count',
                            data: chartData.assetCounts,
                            backgroundColor: chartData.colors.slice(0, chartData.vendors.length),
                            borderColor: chartData.colors.slice(0, chartData.vendors.length).map(color => color + '80'),
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: 'rgba(255, 255, 255, 0.2)',
                                borderWidth: 1,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return `Assets: ${context.parsed.y}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)',
                                    drawBorder: false
                                },
                                ticks: {
                                    color: '#6B7280',
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#6B7280',
                                    font: {
                                        size: 11
                                    },
                                    maxRotation: 45
                                }
                            }
                        }
                    }
                });
            } else {
                console.error('Asset count chart canvas not found'); // Debug log
            }

            // Vendor Asset Value Chart (Horizontal Bar Chart)
            const vendorAssetValueCtx = document.getElementById('vendorAssetValueChart');
            if (vendorAssetValueCtx) {
                console.log('Creating asset value chart'); // Debug log
                new Chart(vendorAssetValueCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: chartData.vendors,
                        datasets: [{
                            label: 'Asset Value (₱)',
                            data: chartData.assetValues,
                            backgroundColor: chartData.colors.slice(0, chartData.vendors.length).map(color => color + '60'),
                            borderColor: chartData.colors.slice(0, chartData.vendors.length),
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: 'rgba(255, 255, 255, 0.2)',
                                borderWidth: 1,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return `Value: ₱${context.parsed.x.toLocaleString()}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)',
                                    drawBorder: false
                                },
                                ticks: {
                                    color: '#6B7280',
                                    font: {
                                        size: 12
                                    },
                                    callback: function(value) {
                                        return '₱' + (value / 1000).toFixed(0) + 'K';
                                    }
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#6B7280',
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                console.error('Asset value chart canvas not found'); // Debug log
            }

            // Vendor Performance Distribution Chart (Doughnut Chart)
            const vendorPerformanceCtx = document.getElementById('vendorPerformanceChart');
            if (vendorPerformanceCtx) {
                console.log('Creating performance chart'); // Debug log
                new Chart(vendorPerformanceCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: chartData.vendors,
                        datasets: [{
                            data: chartData.completionRates,
                            backgroundColor: chartData.colors.slice(0, chartData.vendors.length),
                            borderColor: 'white',
                            borderWidth: 3,
                            hoverOffset: 15,
                            cutout: '60%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: {
                                        size: 12
                                    },
                                    color: '#374151'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: 'rgba(255, 255, 255, 0.2)',
                                borderWidth: 1,
                                cornerRadius: 8,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        return `${context.label}: ${context.parsed}%`;
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                console.error('Performance chart canvas not found'); // Debug log
            }
        } else {
            console.log('No vendor data available for charts'); // Debug log
        }
    });

    function previewPDF() {
        // Open PDF preview in new tab
        window.open('{{ route("reports.vendor-analysis.previewPDF") }}', '_blank');
    }
</script>

<script>
// Pass the correct vendor details base URL from Laravel to JS
const vendorDetailsBaseUrl = "{{ url('reports/vendor-details') }}";

document.addEventListener('DOMContentLoaded', function() {
    // Vendor search functionality using vanilla JavaScript
    const vendorSearch = document.getElementById('vendorSearch');
    if (vendorSearch) {
        vendorSearch.addEventListener('keyup', function() {
            const value = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#vendorTable tbody tr');
            
            tableRows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                if (text.indexOf(value) > -1) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});

window.showVendorDetails = function(vendorId, vendorName) {
    console.log('Showing details for vendor:', vendorId, vendorName); // Debug log
    
    // Show loading state
    document.getElementById('modalTitle').textContent = vendorName + ' - Analysis';
    document.getElementById('vendorDetailsContent').innerHTML = `
        <div class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
            <span class="ml-2 text-gray-600">Loading vendor details...</span>
        </div>
    `;
    
    // Show modal
    document.getElementById('vendorDetailsModal').classList.remove('hidden');
    
    // Load vendor details via AJAX (use correct base URL)
    fetch(vendorDetailsBaseUrl + '/' + vendorId)
        .then(response => response.text())
        .then(data => {
            document.getElementById('vendorDetailsContent').innerHTML = data;
        })
        .catch(error => {
            console.log('AJAX failed:', error); // Debug log
            // Fallback content if AJAX fails
            document.getElementById('vendorDetailsContent').innerHTML = `
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Detailed Analysis Coming Soon</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Detailed analysis for <strong>${vendorName}</strong> will include:</p>
                                <ul class="list-disc list-inside mt-2 space-y-1">
                                    <li>Asset breakdown by category</li>
                                    <li>Repair history timeline</li>
                                    <li>Cost analysis and trends</li>
                                    <li>Performance recommendations</li>
                                    <li>Comparative analysis with other vendors</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
};

function closeVendorDetails() {
    document.getElementById('vendorDetailsModal').classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('vendorDetailsModal');
    if (e.target === modal) {
        closeVendorDetails();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('vendorDetailsModal');
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
        closeVendorDetails();
    }
});
</script>

<style>
@media print {
    /* Hide navigation elements and buttons */
    aside.fixed,
    nav.bg-white,
    .sidebar-nav,
    header,
    .header,
    #header,
    [x-data],
    button,
    .print-hide,
    .flex.flex-col.md\:flex-row.justify-between.items-start.md\:items-center.mb-8.gap-4.md\:gap-0 > button { /* Hide the print button */
        display: none !important;
    }

    /* Hide statistics cards */
    .grid.grid-cols-1.md\:grid-cols-2.lg\:grid-cols-4.gap-6.mb-8.print\:hidden,
    .grid.grid-cols-1.md\:grid-cols-2.lg\:grid-cols-4.gap-6.mb-8 {
        display: none !important;
    }

    /* Hide charts section in print */
    .mb-8 .bg-gray-50.rounded-xl.p-6 {
        display: none !important;
    }

    /* Hide search functionality */
    .flex.flex-col.md\:flex-row.justify-between.items-start.md\:items-center.p-6.border-b.border-gray-200.gap-4 > div:last-child,
    .relative.w-full.md\:w-auto.print\:hidden,
    #vendorSearch {
        display: none !important;
    }

    /* Ensure main content area is visible and uses full width */
    .flex-1.p-4,
    .flex-1.p-8 {
         padding: 0 !important;
    }

    /* Remove left margin from main content added for sidebar */
    .md\:ml-80 {
        margin-left: 0 !important;
    }

    /* Ensure container is visible and uses full width */
    .max-w-7xl, .max-w-full {
        max-width: 100% !important;
        width: 100% !important;
    }

    /* Reset layout */
    body, html {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        background: white !important;
    }

    /* Style the title */
    h1 {
        text-align: center !important;
        margin-bottom: 20px;
        font-size: 18pt;
        width: 100% !important;
        display: block !important;
    }

    /* Style the subtitle */
    .text-gray-600.text-sm.md\:text-base {
        text-align: center !important;
        margin-bottom: 20px;
        font-size: 10pt;
        display: block !important;
    }

    /* Ensure the table is visible and styled for print */
    .overflow-x-auto {
        display: block !important;
        overflow-x: visible !important; /* Ensure table is not scrollable in print */
    }

    .bg-white.rounded-xl.border.border-gray-200.shadow-lg {
        background-color: white !important;
        padding: 0 !important;
        margin: 0 !important;
        box-shadow: none !important;
        border: none !important;
    }

    table {
        width: 100% !important;
        table-layout: auto !important; /* Allow columns to size naturally */
        border-collapse: collapse;
        margin-top: 20px;
    }

    th {
        background-color: #f3f4f6;
        color: #6b7280;
        font-size: 9pt !important;
        font-weight: 600;
        text-transform: uppercase;
        padding: 8px !important;
        text-align: left;
        white-space: normal !important;
    }

    td {
        padding: 8px !important;
        font-size: 9pt !important;
        border-bottom: 1px solid #e5e7eb;
        white-space: normal !important;
    }

    /* Ensure proper page breaks */
    thead {
        display: table-header-group;
    }

    tr {
        page-break-inside: avoid;
    }

    /* Remove color-adjust for backgrounds/colors */
    thead th,
    .bg-blue-100,
    .bg-green-100,
    .bg-red-100 {
        -webkit-print-color-adjust: unset !important;
        print-color-adjust: unset !important;
    }

    /* Hide search input */
    .relative.w-full.md\:w-auto.print\:hidden {
        display: none !important;
    }

    /* Remove gradients and use solid colors for print */
    .bg-gradient-to-br {
        background: white !important;
        border: 1px solid #e5e7eb !important;
    }

    /* Remove shadows and borders that don't print well */
    .shadow-lg,
    .shadow-xl,
    .rounded-xl,
    .rounded-lg {
        box-shadow: none !important;
        border-radius: 0 !important;
    }

    /* Hide interactive elements */
    .hover\\:bg-gray-50 {
        background-color: white !important;
    }

    /* Ensure text is readable */
    * {
        color: black !important;
        background-color: white !important;
    }

    /* Hide Actions column in print */
    table th:last-child,
    table td:last-child {
        display: none !important;
    }
}
</style>
@endsection