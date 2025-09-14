@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 transition-all duration-300" id="mainContent">
    <div class="max-w-6xl mx-auto">
        <!-- Success and Error Messages -->
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-200 rounded-xl text-green-800 flex items-center">
            <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-200 rounded-xl text-red-800 flex items-center">
            <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
        @endif

        <!-- Main Container -->
        <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 sm:gap-0">
                <div class="flex items-center">
                    <div class="bg-red-100 p-3 rounded-full mr-4">
                        <svg class="w-8 h-8 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Asset Location Report</h1>
                        <p class="text-gray-600 text-sm md:text-base">Comprehensive overview of assets by location</p>
                    </div>
                </div>
                <button onclick="printReport()" id="printButton" class="inline-flex items-center px-6 py-3 bg-red-800 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-lg hover:shadow-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span id="printButtonText">Print Report</span>
                </button>
            </div>

                        <!-- Total Summary Card -->
                        <div class="mb-8">
                <div class="bg-red-800 text-white rounded-xl shadow-lg p-6 md:p-8">
                    <div class="flex items-center mb-4">
                        <div class="bg-white/20 p-3 rounded-full mr-4 backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold">Total Assets Summary</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
                            <div class="text-3xl md:text-4xl font-bold mb-2">{{ $totalSummary['total_assets'] }}</div>
                            <p class="text-red-100 text-sm md:text-base font-medium">Total Assets</p>
                        </div>
                        <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm sm:text-right">
                            <div class="text-3xl md:text-4xl font-bold mb-2">₱{{ number_format($totalSummary['total_value'], 2) }}</div>
                            <p class="text-red-100 text-sm md:text-base font-medium">Total Value</p>
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
                        Asset Distribution Charts
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Asset Count by Location Chart -->
                        <div class="bg-white rounded-xl shadow-md p-4">
                            <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Asset Count
                            </h4>
                            <div class="h-64">
                                <canvas id="assetCountChart"></canvas>
                            </div>
                        </div>

                        <!-- Asset Value by Location Chart -->
                        <div class="bg-white rounded-xl shadow-md p-4">
                            <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                Asset Value
                            </h4>
                            <div class="h-64">
                                <canvas id="assetValueChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Age Distribution Charts -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <!-- Asset Age Distribution Chart -->
                        <div class="bg-white rounded-xl shadow-md p-4">
                            <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Asset Age Distribution
                            </h4>
                            <div class="h-64">
                                <canvas id="assetAgeChart"></canvas>
                            </div>
                        </div>

                        <!-- Assets 5+ Years Old Chart -->
                        <div class="bg-white rounded-xl shadow-md p-4">
                            <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Assets 5+ Years Old
                            </h4>
                            <div class="h-64">
                                <canvas id="oldAssetsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Location Summary Cards (Mobile Only) -->
            <div class="grid grid-cols-1 gap-4 mb-8 md:hidden">
                @foreach($locationStats as $stat)
                <a href="{{ route('reports.location.details', $stat['location']) }}" class="group block bg-white rounded-xl shadow-md border border-gray-200 p-6 hover:shadow-lg hover:border-red-300 transition-all duration-300 no-underline text-gray-800 transform hover:-translate-y-1">
                    <div class="flex items-center mb-4">
                        <div class="bg-red-100 p-2 rounded-lg mr-3 group-hover:bg-red-200 transition-colors">
                            <svg class="w-5 h-5 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 group-hover:text-red-800 transition-colors">{{ $stat['location'] }}</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-3xl font-bold text-red-800">{{ $stat['count'] }}</span>
                            <div class="text-right">
                                <div class="text-sm text-gray-500 font-medium">Total Assets</div>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-gray-100">
                            <div class="text-sm text-gray-600">Total Value</div>
                            <div class="text-lg font-semibold text-gray-900">₱{{ number_format($stat['total_value'], 2) }}</div>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-red-600 text-sm font-medium group-hover:text-red-700 transition-colors">
                        <span>View Details</span>
                        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- Location Details Table (Desktop View) -->
            <div class="overflow-x-auto hidden md:block">
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2-2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Detailed Location Breakdown
                    </h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white rounded-lg">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Assets</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($locationStats as $stat)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-red-100 p-2 rounded-lg mr-3">
                                            <svg class="w-4 h-4 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $stat['location'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ $stat['count'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">₱{{ number_format($stat['total_value'], 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-800">
                                    <a href="{{ route('reports.location.details', $stat['location']) }}" class="inline-flex items-center px-3 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-colors font-medium">
                                        <span>View Details</span>
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100 rounded-lg">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">Total</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-800">
                                        {{ $totalSummary['total_assets'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">₱{{ number_format($totalSummary['total_value'], 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-8 print-hide">
                {{ $locationStats->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Chart data from PHP
    const chartData = {
        locations: @json($locationStats->pluck('location')),
        assetCounts: @json($locationStats->pluck('count')),
        assetValues: @json($locationStats->pluck('total_value')),
        ageDistribution: @json($ageDistribution),
        oldAssetsData: @json($oldAssetsData),
        colors: [
            '#EF4444', '#F59E0B', '#10B981', '#3B82F6', 
            '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16',
            '#F97316', '#6366F1', '#14B8A6', '#F43F5E'
        ]
    };

    // Asset Count by Location Chart (Bar Chart)
    const assetCountCtx = document.getElementById('assetCountChart').getContext('2d');
    new Chart(assetCountCtx, {
        type: 'bar',
        data: {
            labels: chartData.locations,
            datasets: [{
                label: 'Asset Count',
                data: chartData.assetCounts,
                backgroundColor: chartData.colors.slice(0, chartData.locations.length),
                borderColor: chartData.colors.slice(0, chartData.locations.length).map(color => color + '80'),
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

    // Asset Value by Location Chart (Horizontal Bar Chart)
    const assetValueCtx = document.getElementById('assetValueChart').getContext('2d');
    new Chart(assetValueCtx, {
        type: 'bar',
        data: {
            labels: chartData.locations,
            datasets: [{
                label: 'Asset Value (₱)',
                data: chartData.assetValues,
                backgroundColor: chartData.colors.slice(0, chartData.locations.length).map(color => color + '60'),
                borderColor: chartData.colors.slice(0, chartData.locations.length),
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

    // Asset Age Distribution Chart (Bar Chart)
    const assetAgeCtx = document.getElementById('assetAgeChart').getContext('2d');
    new Chart(assetAgeCtx, {
        type: 'bar',
        data: {
            labels: chartData.ageDistribution.labels,
            datasets: [{
                label: 'Asset Count',
                data: chartData.ageDistribution.data,
                backgroundColor: chartData.colors.slice(0, chartData.ageDistribution.labels.length).map(color => color + '80'),
                borderColor: chartData.colors.slice(0, chartData.ageDistribution.labels.length),
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

    // Assets 5+ Years Old Chart (Doughnut Chart)
    const oldAssetsCtx = document.getElementById('oldAssetsChart').getContext('2d');
    new Chart(oldAssetsCtx, {
        type: 'doughnut',
        data: {
            labels: chartData.oldAssetsData.labels,
            datasets: [{
                data: chartData.oldAssetsData.data,
                backgroundColor: ['#EF4444', '#10B981'],
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
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Auto-print when page loads in print mode
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === '1') {
            // Page loaded in print mode, update button and auto-print
            const printButton = document.getElementById('printButton');
            const printButtonText = document.getElementById('printButtonText');
            if (printButton && printButtonText) {
                printButtonText.textContent = 'Printing...';
                printButton.disabled = true;
            }
            
            // Auto-print after a short delay
            setTimeout(() => {
                window.print();
            }, 500);
        }
    });

    function printReport() {
        // Check if we're already in print mode
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === '1') {
            // Already in print mode, just print
            window.print();
        } else {
            // Add print parameter and reload
            const printButton = document.getElementById('printButton');
            const printButtonText = document.getElementById('printButtonText');
            if (printButton && printButtonText) {
                printButtonText.textContent = 'Loading...';
                printButton.disabled = true;
            }
            
            const url = new URL(window.location);
            url.searchParams.set('print', '1');
            window.location.href = url.toString();
        }
    }
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
        .flex.flex-col.sm\:flex-row.justify-between.items-start.sm\:items-center.mb-6.gap-4.sm\:gap-0 > .flex.flex-col.sm\:flex-row.space-y-3.sm\:space-y-0.sm\:space-x-3.w-full.sm\:w-auto { /* Hide the button container */
            display: none !important;
        }

        /* Hide location summary cards */
        .grid.grid-cols-1.gap-4.mb-8.md\:hidden {
            display: none !important;
        }

        /* Hide all charts section in print */
        .mb-8:has(.bg-gray-50.rounded-xl.p-6:has(h3:contains("Asset Distribution Charts"))) {
            display: none !important;
        }

        /* Hide chart containers specifically */
        .bg-gray-50.rounded-xl.p-6:has(h3:contains("Asset Distribution Charts")) {
            display: none !important;
        }

        /* Hide individual chart containers within the charts section */
        .bg-gray-50.rounded-xl.p-6 .bg-white.rounded-xl.shadow-md.p-4 {
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
        .max-w-6xl, .max-w-full {
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

        /* Ensure total summary card is visible and styled */
        .mb-8:has(.bg-red-800) { /* Target the parent div of the total summary card */
             display: block !important;
             margin-bottom: 20px !important;
        }

        .bg-red-800 {
            background-color: white !important;
            color: black !important;
            border: 1px solid #000;
            padding: 15px !important;
        }

        .bg-red-800 * {
            color: black !important;
        }

        /* Ensure the table is visible and styled for print */
        .overflow-x-auto.hidden.md\:block {
            display: block !important;
            overflow-x: visible !important; /* Ensure table is not scrollable in print */
        }

        .bg-gray-50.rounded-xl.p-6.mb-6 {
            background-color: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        table {
            width: 100% !important;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: auto !important; /* Allow columns to size naturally */
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

        /* Hide Actions column in print */
        table th:last-child,
        table td:last-child {
            display: none !important;
        }

        /* Hide pagination in print */
        .print-hide,
        .mt-8:has(.pagination),
        .pagination,
        .pagination-info,
        .pagination-links,
        nav[role="navigation"],
        .flex.items-center.justify-between,
        .flex-1.flex.justify-between.sm\:hidden,
        .hidden.sm\:flex-1.sm\:flex.sm\:items-center.sm\:justify-between {
            display: none !important;
        }

        /* Hide specific titles in print */
        h3:contains("Asset Distribution Charts"),
        h3:contains("Detailed Location Breakdown") {
            display: none !important;
        }

        /* Hide pagination info text */
        .text-sm.text-gray-700.leading-5,
        .text-sm.text-gray-500 {
            display: none !important;
        }

        /* Hide any remaining pagination elements */
        .flex.items-center.space-x-2 {
            display: none !important;
        }

        /* Ensure proper page breaks */
        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        tfoot {
            display: table-row-group !important;
        }

         /* Remove color-adjust for backgrounds/colors */
        thead th {
            -webkit-print-color-adjust: unset !important;
            print-color-adjust: unset !important;
        }
    }
</style>

@endsection
