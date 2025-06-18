@extends('layouts.app')

@section('title', 'Vendor Analysis')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg">
            <!-- Header -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Vendor Analysis Report</h1>
                        <p class="text-sm text-gray-600">Comprehensive vendor performance and reliability analysis</p>
                    </div>
                </div>
                <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                    <i class="fas fa-print mr-2"></i>
                    Print Report
                </button>
            </div>

            <div class="p-6">
                <!-- Overall Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-red-50 rounded-lg p-6 shadow-lg border border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-building text-3xl text-red-800"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-800">Total Vendors</p>
                                <p class="text-2xl font-bold text-red-900">{{ $overallStats['total_vendors'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-red-50 rounded-lg p-6 shadow-lg border border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-desktop text-3xl text-red-800"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-800">Total Assets</p>
                                <p class="text-2xl font-bold text-red-900">{{ number_format($overallStats['total_assets']) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-red-50 rounded-lg p-6 shadow-lg border border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-dollar-sign text-3xl text-red-800"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-800">Total Value</p>
                                <p class="text-2xl font-bold text-red-900">₱{{ number_format($overallStats['total_value'], 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-red-50 rounded-lg p-6 shadow-lg border border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-tools text-3xl text-red-800"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-800">Avg Repair Rate</p>
                                <p class="text-2xl font-bold text-red-900">{{ number_format($overallStats['average_repair_rate'], 1) }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reliability Distribution -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-red-50 rounded-lg p-6 relative overflow-hidden shadow-lg border border-gray-200">
                        <div class="relative z-10">
                            <h3 class="text-3xl font-bold text-red-900">{{ $overallStats['high_reliability_vendors'] }}</h3>
                            <p class="text-red-800">High Reliability Vendors</p>
                        </div>
                        <div class="absolute right-4 top-4 text-red-200 opacity-20">
                            <i class="fas fa-star text-6xl"></i>
                        </div>
                    </div>

                    <div class="bg-red-50 rounded-lg p-6 relative overflow-hidden shadow-lg border border-gray-200">
                        <div class="relative z-10">
                            <h3 class="text-3xl font-bold text-red-900">{{ $overallStats['medium_reliability_vendors'] }}</h3>
                            <p class="text-red-800">Medium Reliability Vendors</p>
                        </div>
                        <div class="absolute right-4 top-4 text-red-200 opacity-20">
                            <i class="fas fa-star-half-alt text-6xl"></i>
                        </div>
                    </div>

                    <div class="bg-red-50 rounded-lg p-6 relative overflow-hidden shadow-lg border border-gray-200">
                        <div class="relative z-10">
                            <h3 class="text-3xl font-bold text-red-900">{{ $overallStats['low_reliability_vendors'] }}</h3>
                            <p class="text-red-800">Low Reliability Vendors</p>
                        </div>
                        <div class="absolute right-4 top-4 text-red-200 opacity-20">
                            <i class="fas fa-exclamation-triangle text-6xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Vendor Performance Table -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-lg">
                    <div class="flex justify-between items-center p-6 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Vendor Performance Analysis</h2>
                        @if(!isset($noData))
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="vendorSearch" placeholder="Search vendors..." 
                                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        </div>
                        @endif
                    </div>

                    @if(isset($noData))
                    <div class="p-12 text-center">
                        <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-chart-bar text-3xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Vendor Data Available</h3>
                        <p class="text-gray-600 mb-6">There are no assets assigned to vendors yet. To see vendor analysis:</p>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p>1. Go to <a href="{{ route('assets.create') }}" class="text-red-600 hover:text-red-700 font-medium">Add Asset</a> and assign vendors to your assets</p>
                            <p>2. Or update existing assets to include vendor information</p>
                            <p>3. Once assets are assigned to vendors, this analysis will show detailed performance metrics</p>
                        </div>
                    </div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="vendorTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assets</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Repair Rate</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Rate</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operational Rate</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reliability Rating</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Age (Years)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($vendorAnalysis as $vendor)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $vendor['name'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $vendor['total_assets'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₱{{ number_format($vendor['total_value'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($vendor['repair_rate'] <= 10)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $vendor['repair_rate'] }}%
                                            </span>
                                        @elseif($vendor['repair_rate'] <= 25)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                {{ $vendor['repair_rate'] }}%
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ $vendor['repair_rate'] }}%
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($vendor['completion_rate'] >= 90)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $vendor['completion_rate'] }}%
                                            </span>
                                        @elseif($vendor['completion_rate'] >= 75)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                {{ $vendor['completion_rate'] }}%
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ $vendor['completion_rate'] }}%
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($vendor['operational_rate'] >= 80)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $vendor['operational_rate'] }}%
                                            </span>
                                        @elseif($vendor['operational_rate'] >= 60)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                {{ $vendor['operational_rate'] }}%
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ $vendor['operational_rate'] }}%
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($vendor['reliability_rating'] == 'High')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-star mr-1"></i>High
                                            </span>
                                        @elseif($vendor['reliability_rating'] == 'Medium')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-star-half-alt mr-1"></i>Medium
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Low
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $vendor['average_age'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button type="button" 
                                                onclick="showVendorDetails({{ $vendor['id'] }}, '{{ addslashes($vendor['name']) }}')"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                            <i class="fas fa-eye mr-1"></i>
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
</div>

<!-- Vendor Details Modal -->
<div id="vendorDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Vendor Details</h3>
                <button onclick="closeVendorDetails()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="vendorDetailsContent" class="space-y-4">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
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

function showVendorDetails(vendorId, vendorName) {
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
    
    // Load vendor details via AJAX
    fetch('/reports/vendor-details/' + vendorId)
        .then(response => response.text())
        .then(data => {
            document.getElementById('vendorDetailsContent').innerHTML = data;
        })
        .catch(error => {
            console.log('AJAX failed:', error); // Debug log
            // Fallback content if AJAX fails
            document.getElementById('vendorDetailsContent').innerHTML = `
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
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
}

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

// Print styles
@media print {
    .print\\:hidden {
        display: none !important;
    }
    
    .print\\:block {
        display: block !important;
    }
    
    .print\\:text-black {
        color: black !important;
    }
    
    .print\\:bg-white {
        background-color: white !important;
    }
    
    .print\\:shadow-none {
        box-shadow: none !important;
    }
    
    .print\\:border {
        border: 1px solid #e5e7eb !important;
    }
}
</script>
@endsection 