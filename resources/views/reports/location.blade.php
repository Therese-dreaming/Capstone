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
                <button onclick="openSignatureModal()" id="printButton" class="inline-flex items-center px-6 py-3 bg-red-800 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span id="printButtonText">Preview PDF</span>
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
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Combined Asset Count and Value Chart -->
                        <div class="bg-white rounded-xl shadow-md p-4">
                            <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Asset Count & Value by Location
                            </h4>
                            <div class="h-80">
                                <canvas id="combinedAssetChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Age Distribution Charts -->
                    <div class="grid grid-cols-1 gap-6 mt-6">
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
                        <h3 class="text-lg font-bold text-gray-800 group-hover:text-red-800 transition-colors truncate" title="{{ $stat['location'] }}">{{ $stat['location'] }}</h3>
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
                                        <span class="text-sm font-medium text-gray-900 truncate" title="{{ $stat['location'] }}">{{ $stat['location'] }}</span>
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
    const rawLocations = @json($locationStats->pluck('location'));
    
    // Function to truncate long location names for charts
    function truncateLocation(location, maxLength = 25) {
        if (location.length <= maxLength) return location;
        return location.substring(0, maxLength) + '...';
    }
    
    const chartData = {
        locations: rawLocations.map(location => truncateLocation(location)),
        fullLocations: rawLocations, // Keep full names for tooltips
        assetCounts: @json($locationStats->pluck('count')),
        assetValues: @json($locationStats->pluck('total_value')),
        ageDistribution: @json($ageDistribution),
        colors: [
            '#EF4444', '#F59E0B', '#10B981', '#3B82F6', 
            '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16',
            '#F97316', '#6366F1', '#14B8A6', '#F43F5E'
        ]
    };

    // Combined Asset Count and Value Chart (Dual Y-Axis)
    const combinedAssetCtx = document.getElementById('combinedAssetChart').getContext('2d');
    new Chart(combinedAssetCtx, {
        type: 'bar',
        data: {
            labels: chartData.locations,
            datasets: [{
                label: 'Asset Count',
                data: chartData.assetCounts,
                backgroundColor: 'rgba(59, 130, 246, 0.8)', // Blue
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                yAxisID: 'y'
            }, {
                label: 'Asset Value (₱)',
                data: chartData.assetValues,
                backgroundColor: 'rgba(16, 185, 129, 0.8)', // Green
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'rect',
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
                        title: function(context) {
                            return chartData.fullLocations[context[0].dataIndex];
                        },
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return `Assets: ${context.parsed.y}`;
                            } else {
                                return `Value: ₱${context.parsed.y.toLocaleString()}`;
                            }
                        }
                    }
                }
            },
            scales: {
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
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#3B82F6',
                        font: {
                            size: 12
                        }
                    },
                    title: {
                        display: true,
                        text: 'Asset Count',
                        color: '#3B82F6',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        color: '#10B981',
                        font: {
                            size: 12
                        },
                        callback: function(value) {
                            return '₱' + (value / 1000).toFixed(0) + 'K';
                        }
                    },
                    title: {
                        display: true,
                        text: 'Asset Value (₱)',
                        color: '#10B981',
                        font: {
                            size: 14,
                            weight: 'bold'
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

    // Signature functionality
    let signatureCounter = 0;

    function openSignatureModal() {
        // Clear existing entries and add one default entry
        document.getElementById('signatureEntries').innerHTML = '';
        signatureCounter = 0;
        addSignatureEntry();
        
        // Show modal
        document.getElementById('signatureModal').classList.remove('hidden');
        document.getElementById('signatureModal').classList.add('flex');
    }

    function closeSignatureModal() {
        document.getElementById('signatureModal').classList.add('hidden');
        document.getElementById('signatureModal').classList.remove('flex');
    }

    function addSignatureEntry() {
        signatureCounter++;
        const entryId = `signature-${signatureCounter}`;
        
        const entryHtml = `
            <div class="border border-gray-200 rounded-lg p-4" id="${entryId}">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-medium text-gray-900">Signature ${signatureCounter}</h4>
                    <button onclick="removeSignatureEntry('${entryId}')" class="text-red-600 hover:text-red-800" title="Remove">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
                
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Label</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500" 
                           placeholder="e.g., Checked by, Supervised by, Approved by" 
                           id="${entryId}-label">
                </div>
                
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500" 
                           placeholder="Enter name" 
                           id="${entryId}-name">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Signature</label>
                    <div class="border border-gray-300 rounded-md">
                        <canvas id="${entryId}-canvas" width="300" height="120" class="block w-full cursor-crosshair"></canvas>
                    </div>
                    <div class="flex justify-end mt-2">
                        <button onclick="clearSignature('${entryId}-canvas')" class="text-sm text-gray-600 hover:text-gray-800">
                            Clear Signature
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('signatureEntries').insertAdjacentHTML('beforeend', entryHtml);
        initializeSignaturePad(`${entryId}-canvas`);
    }

    function removeSignatureEntry(entryId) {
        const entry = document.getElementById(entryId);
        if (entry) {
            entry.remove();
        }
        
        // If no entries left, add one default entry
        if (document.getElementById('signatureEntries').children.length === 0) {
            addSignatureEntry();
        }
    }

    function initializeSignaturePad(canvasId) {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;

        // Clear canvas to transparent (no white background)
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Set drawing styles
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        function getMousePos(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            
            return {
                x: (e.clientX - rect.left) * scaleX,
                y: (e.clientY - rect.top) * scaleY
            };
        }

        function getTouchPos(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            
            return {
                x: (e.touches[0].clientX - rect.left) * scaleX,
                y: (e.touches[0].clientY - rect.top) * scaleY
            };
        }

        // Mouse events
        canvas.addEventListener('mousedown', (e) => {
            isDrawing = true;
            const pos = getMousePos(e);
            lastX = pos.x;
            lastY = pos.y;
        });

        canvas.addEventListener('mousemove', (e) => {
            if (!isDrawing) return;
            const pos = getMousePos(e);
            
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            
            lastX = pos.x;
            lastY = pos.y;
        });

        canvas.addEventListener('mouseup', () => isDrawing = false);
        canvas.addEventListener('mouseout', () => isDrawing = false);

        // Touch events for mobile
        canvas.addEventListener('touchstart', (e) => {
            e.preventDefault();
            isDrawing = true;
            const pos = getTouchPos(e);
            lastX = pos.x;
            lastY = pos.y;
        });

        canvas.addEventListener('touchmove', (e) => {
            e.preventDefault();
            if (!isDrawing) return;
            const pos = getTouchPos(e);
            
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            
            lastX = pos.x;
            lastY = pos.y;
        });

        canvas.addEventListener('touchend', (e) => {
            e.preventDefault();
            isDrawing = false;
        });
    }

    function clearSignature(canvasId) {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext('2d');
        // Clear to transparent instead of white
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    function generatePDFWithSignatures() {
        // Collect all signature data
        const signatures = [];
        const entries = document.getElementById('signatureEntries').children;
        
        for (let i = 0; i < entries.length; i++) {
            const entry = entries[i];
            const entryId = entry.id;
            const label = document.getElementById(`${entryId}-label`).value.trim();
            const name = document.getElementById(`${entryId}-name`).value.trim();
            const canvas = document.getElementById(`${entryId}-canvas`);
            
            if (label && name) {
                signatures.push({
                    label: label,
                    name: name,
                    signature: canvas.toDataURL('image/png')
                });
            }
        }

        // Create a form to submit via POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('reports.location.exportPDF') }}';
        form.target = '_blank';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Add signatures as JSON
        if (signatures.length > 0) {
            const sigInput = document.createElement('input');
            sigInput.type = 'hidden';
            sigInput.name = 'signatures';
            sigInput.value = JSON.stringify(signatures);
            form.appendChild(sigInput);
        }
        
        // Submit form
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
        
        // Close modal
        closeSignatureModal();
    }
</script>

<!-- Signature Modal -->
<div id="signatureModal" 
     class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4"
     style="z-index: 70;">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add Signatures for PDF</h3>
                <button onclick="closeSignatureModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div id="signatureEntries" class="space-y-4">
                <!-- Signature entries will be added here dynamically -->
            </div>
            
            <div class="flex justify-between items-center mt-6">
                <button onclick="addSignatureEntry()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Signature
                </button>
                
                <div class="flex space-x-3">
                    <button onclick="closeSignatureModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Cancel
                    </button>
                    <button onclick="generatePDFWithSignatures()" class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Generate PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Canvas styling for signature pad */
    canvas {
        background-color: #f9fafb;
        border-radius: 0.375rem;
    }
    
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
