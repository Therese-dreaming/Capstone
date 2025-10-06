<div class="space-y-6">
    <!-- Vendor Header -->
    <div class="bg-red-800 text-white rounded-xl shadow-lg p-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center">
                <div class="bg-white/20 p-3 rounded-full mr-4 backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-white">{{ $vendor->name }}</h2>
                    <p class="text-red-100 text-sm md:text-base">Detailed Performance Analysis</p>
                </div>
            </div>
            <div class="text-center md:text-right">
                <div class="text-3xl md:text-4xl font-bold text-white">{{ $totalAssets }}</div>
                <div class="text-red-100 text-sm md:text-base font-medium">Total Assets</div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <!-- Total Value - Full Width Row -->
    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 shadow-lg border border-green-200 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center">
            <div class="bg-green-100 p-3 rounded-full mr-4">
                <svg class="w-6 h-6 text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-green-800">Total Value</p>
                <p class="text-2xl font-bold text-green-900 break-words">₱{{ number_format($totalValue, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Other Metrics - Two Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 shadow-lg border border-blue-200 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-full mr-4">
                    <svg class="w-6 h-6 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-blue-800">Total Repairs</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $totalRepairs }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-6 shadow-lg border border-emerald-200 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center">
                <div class="bg-emerald-100 p-3 rounded-full mr-4">
                    <svg class="w-6 h-6 text-emerald-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-emerald-800">Completed</p>
                    <p class="text-2xl font-bold text-emerald-900">{{ $completedRepairs }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-lg p-6 hover:shadow-xl transition-all duration-300">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Performance Metrics
            </h3>
            <div class="space-y-4">
                <div class="p-3 bg-gray-50 rounded-lg text-center">
                    <span class="block text-sm font-medium text-gray-700 mb-2">Total Repairs</span>
                    <span class="text-lg font-semibold text-gray-900">{{ $totalRepairs }}</span>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg text-center">
                    <span class="block text-sm font-medium text-gray-700 mb-2">Completion Rate</span>
                    <span class="text-lg font-semibold text-green-600">{{ number_format($completionRate, 1) }}%</span>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg text-center">
                    <span class="block text-sm font-medium text-gray-700 mb-2">Disposed Assets</span>
                    <span class="text-lg font-semibold text-red-600">{{ $disposedCount }}</span>
                </div>
            </div>
        </div>

        <!-- Asset Status Breakdown -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-lg p-6 hover:shadow-xl transition-all duration-300">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Asset Status
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                    <span class="text-sm text-gray-700 font-medium">In Use</span>
                    <span class="text-sm font-semibold text-green-600">{{ $statusBreakdown['in_use'] }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                    <span class="text-sm text-gray-700 font-medium">Under Repair</span>
                    <span class="text-sm font-semibold text-yellow-600">{{ $statusBreakdown['under_repair'] }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                    <span class="text-sm text-gray-700 font-medium">Pulled Out</span>
                    <span class="text-sm font-semibold text-red-600">{{ $statusBreakdown['pulled_out'] }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-700 font-medium">Disposed</span>
                    <span class="text-sm font-semibold text-gray-600">{{ $statusBreakdown['disposed'] }}</span>
                </div>
            </div>
        </div>

        <!-- Age Distribution -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-lg p-6 hover:shadow-xl transition-all duration-300">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Age Distribution
            </h3>
            <div class="space-y-3">
                @foreach($ageGroups as $ageGroup => $count)
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-700 font-medium">{{ $ageGroup }}</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div> 