<div class="space-y-6">
    <!-- Vendor Header -->
    <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">{{ $vendor->name }}</h2>
                <p class="text-red-100">Detailed Performance Analysis</p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold">{{ $totalAssets }}</div>
                <div class="text-red-100">Total Assets</div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-dollar-sign text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Value</p>
                    <p class="text-lg font-semibold text-gray-900">₱{{ number_format($totalValue, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-tools text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Repairs</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $totalRepairs }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Completed</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $completedRepairs }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $pendingRepairs }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Metrics</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Repair Rate</span>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($repairRate, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-{{ $repairRate <= 10 ? 'green' : ($repairRate <= 25 ? 'yellow' : 'red') }}-600 h-2 rounded-full" style="width: {{ min($repairRate, 100) }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Completion Rate</span>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($completionRate, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-{{ $completionRate >= 90 ? 'green' : ($completionRate >= 75 ? 'yellow' : 'red') }}-600 h-2 rounded-full" style="width: {{ $completionRate }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Operational Rate</span>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($operationalRate, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-{{ $operationalRate >= 80 ? 'green' : ($operationalRate >= 60 ? 'yellow' : 'red') }}-600 h-2 rounded-full" style="width: {{ $operationalRate }}%"></div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    {{ $reliabilityRating == 'High' ? 'bg-green-100 text-green-800' : 
                       ($reliabilityRating == 'Medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    <i class="fas fa-{{ $reliabilityRating == 'High' ? 'star' : ($reliabilityRating == 'Medium' ? 'star-half-alt' : 'exclamation-triangle') }} mr-1"></i>
                    {{ $reliabilityRating }} Reliability
                </span>
            </div>
        </div>

        <!-- Asset Status Breakdown -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Asset Status</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">In Use</span>
                    <span class="text-sm font-semibold text-green-600">{{ $statusBreakdown['in_use'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Under Repair</span>
                    <span class="text-sm font-semibold text-yellow-600">{{ $statusBreakdown['under_repair'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Pulled Out</span>
                    <span class="text-sm font-semibold text-red-600">{{ $statusBreakdown['pulled_out'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Disposed</span>
                    <span class="text-sm font-semibold text-gray-600">{{ $statusBreakdown['disposed'] }}</span>
                </div>
            </div>
        </div>

        <!-- Age Distribution -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Age Distribution</h3>
            <div class="space-y-3">
                @foreach($ageGroups as $ageGroup => $count)
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">{{ $ageGroup }}</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Category Breakdown -->
    @if($categoryBreakdown->count() > 0)
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Assets by Category</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Repairs</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($categoryBreakdown as $categoryName => $data)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $categoryName }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $data['count'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($data['value'], 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $data['repair_count'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Recent Repairs -->
    @if($recentRepairs->count() > 0)
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Repair Requests</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentRepairs as $repair)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $repair['asset_name'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $repair['serial_number'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $repair['status'] == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($repair['status']) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $repair['created_at']->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $repair['completed_at'] ? $repair['completed_at']->format('M d, Y') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Recommendations -->
    @if(count($recommendations) > 0)
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recommendations</h3>
        <div class="space-y-3">
            @foreach($recommendations as $recommendation)
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-lightbulb text-yellow-500 mt-1"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-700">{{ $recommendation }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div> 