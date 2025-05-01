@extends('layouts.app')

@section('content')

<div class="flex-1 ml-72 p-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Asset Management Dashboard</h1>

    <!-- Asset Procurement & Disposal -->
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Asset Procurement & Disposal</h2>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="border-r pr-4">
                <div class="text-sm text-gray-600">Total Asset Value</div>
                <div class="text-3xl font-bold">PHP {{ number_format($totalAssetValue, 2) }}</div>
            </div>
            <div class="pl-4">
                <div class="text-sm text-gray-600">Assets Disposed (30 days)</div>
                <div class="text-3xl font-bold">{{ $disposedAssets }}</div>
            </div>
        </div>

        <!-- Procurement Chart -->
        <!-- Replace the single chart div with two charts side by side -->
        <div class="grid grid-cols-2 gap-4">
            <div class="h-64">
                <canvas id="procurementValueChart"></canvas>
            </div>
            <div class="h-64">
                <canvas id="assetCountChart"></canvas>
            </div>
        </div>



        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const monthlyData = @json($monthlyData);

                // Procurement Value Chart
                new Chart(document.getElementById('procurementValueChart').getContext('2d'), {
                    type: 'bar'
                    , data: {
                        labels: monthlyData.map(item => item.month)
                        , datasets: [{
                            label: 'Procurement Value (PHP)'
                            , data: monthlyData.map(item => item.procurement_value)
                            , backgroundColor: '#FEB35A'
                            , borderColor: '#FEB35A'
                            , borderWidth: 1
                        }]
                    }
                    , options: {
                        responsive: true
                        , maintainAspectRatio: false
                        , scales: {
                            y: {
                                beginAtZero: true
                                , ticks: {
                                    callback: function(value) {
                                        return 'PHP ' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                        , plugins: {
                            title: {
                                display: true
                                , text: 'Monthly Procurement Value'
                            }
                            , tooltip: {
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
                    type: 'bar'
                    , data: {
                        labels: monthlyData.map(item => item.month)
                        , datasets: [{
                            label: 'Assets Procured'
                            , data: monthlyData.map(item => item.procurement_count)
                            , backgroundColor: '#4B5563'
                            , borderColor: '#4B5563'
                            , borderWidth: 1
                        }, {
                            label: 'Assets Disposed'
                            , data: monthlyData.map(item => item.disposal_count)
                            , backgroundColor: '#EF4444'
                            , borderColor: '#EF4444'
                            , borderWidth: 1
                        }]
                    }
                    , options: {
                        responsive: true
                        , maintainAspectRatio: false
                        , scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                        , plugins: {
                            title: {
                                display: true
                                , text: 'Asset Count by Month'
                            }
                        }
                    }
                });
            });

        </script>

        <!-- Add this after the Procurement Chart section -->
        <div class="grid grid-cols-2 gap-4 mt-6">
            <!-- Status Overview -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Repair Status Overview</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-red-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Open Requests</p>
                        <p class="text-2xl font-bold text-red-600">{{ $totalOpen }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Completed This Month</p>
                        <p class="text-2xl font-bold text-green-600">{{ $completedThisMonth }}</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Avg. Response Time</p>
                        <p class="text-2xl font-bold text-blue-600">{{ round($avgResponseTime) }} days</p>
                    </div>
                </div>
            </div>

            <!-- Keep the existing Warranty Status section in the other column -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Recent Urgent Repairs</h3>
                @if($urgentRepairs->count() > 0)
                <div class="space-y-2">
                    @foreach($urgentRepairs->take(3) as $repair)
                    <div class="p-3 bg-red-50 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-medium">{{ $repair->ticket_number }}</span>
                            <span class="text-sm text-gray-600">{{ $repair->created_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-600">No urgent repairs at the moment.</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mt-6">
            <!-- Status Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Status Distribution</h3>
                <div class="flex justify-between items-center">
                    <div class="h-64">
                        <canvas id="statusDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warranty Status Section -->
        <div class="bg-white p-6 rounded-lg shadow mt-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Warranty Status</h2>
            </div>

            <div class="overflow-x-auto">
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">{{ $asset->serial_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($asset->warranty_period)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $asset->days_until_warranty_expires <= 30 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $asset->days_until_warranty_expires }} days
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->status }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
