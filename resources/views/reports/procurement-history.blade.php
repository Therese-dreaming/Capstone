@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 transition-all duration-300" id="mainContent">
    <div class="max-w-7xl mx-auto">
        <!-- Main Container -->
        <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex items-center mb-6">
                    <div class="bg-red-100 p-3 rounded-full mr-4">
                        <svg class="w-8 h-8 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Procurement History Report</h1>
                        <p class="text-gray-600 text-sm md:text-base">Total Assets Procured: {{ $assets->count() }}</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 w-full sm:w-auto items-start sm:items-center">
                    <button onclick="printReport()" class="bg-red-800 text-white px-6 py-3 rounded-lg hover:bg-red-700 flex items-center justify-center sm:justify-start w-full sm:w-auto text-sm font-medium transition-colors duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Report
                    </button>
                    <button onclick="exportPaascu()" class="bg-green-800 text-white px-6 py-3 rounded-lg hover:bg-green-700 flex items-center justify-center sm:justify-start w-full sm:w-auto text-sm font-medium transition-colors duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Generate PAASCU Report
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <form action="{{ route('reports.procurement-history') }}" method="GET" class="mb-8">
                <!-- First Row: Date Range and Category -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="transform transition-all duration-300 hover:scale-[1.02] md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                        <div class="flex space-x-2">
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="h-10 flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="h-10 flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                        </div>
                    </div>
                    <div class="transform transition-all duration-300 hover:scale-[1.02]">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category_id" class="w-full h-10 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Second Row: Status and Vendor -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="transform transition-all duration-300 hover:scale-[1.02]">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full h-10 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            <option value="">All Status</option>
                            <option value="IN USE" {{ request('status') == 'IN USE' ? 'selected' : '' }}>IN USE</option>
                            <option value="UNDER REPAIR" {{ request('status') == 'UNDER REPAIR' ? 'selected' : '' }}>UNDER REPAIR</option>
                            <option value="DISPOSED" {{ request('status') == 'DISPOSED' ? 'selected' : '' }}>DISPOSED</option>
                            <option value="PULLED OUT" {{ request('status') == 'PULLED OUT' ? 'selected' : '' }}>PULLED OUT</option>
                            <option value="LOST" {{ request('status') == 'LOST' ? 'selected' : '' }}>LOST</option>
                        </select>
                    </div>
                    <div class="transform transition-all duration-300 hover:scale-[1.02]">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vendor</label>
                        <select name="vendor_id" class="w-full h-10 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            <option value="">All Vendors</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="transform transition-all duration-300 hover:scale-[1.02] flex items-end">
                        <button type="submit" class="w-full h-10 bg-red-800 text-white px-4 py-2 rounded-lg hover:bg-red-700 flex items-center justify-center text-sm font-medium transition-colors duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>

            <!-- Reset Filters -->
            @if(request('start_date') || request('end_date') || request('category_id') || request('status') || request('vendor_id'))
            <div class="mb-6 flex justify-center">
                <a href="{{ route('reports.procurement-history') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 flex items-center text-sm transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset All Filters
                </a>
            </div>
            @endif

            <!-- Date Range Info (if filtered) -->
            @if(request('start_date') || request('end_date'))
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl text-blue-800 text-center md:text-left">
                <div class="flex items-center justify-center md:justify-start">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">
                        Showing results from
                        {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') : 'the beginning' }}
                        to
                        {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') : 'present' }}
                    </span>
                </div>
            </div>
            @endif

            <!-- Charts Section -->
            <div class="mb-8">
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Procurement Analytics Charts
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Procurement by Category and Asset Status Charts - Side by Side -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Procurement by Category Chart -->
                            <div class="bg-white rounded-xl shadow-md p-4">
                                <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    Procurement by Category
                                </h4>
                                <div class="h-80">
                                    <canvas id="procurementCategoryChart"></canvas>
                                </div>
                            </div>

                            <!-- Asset Status Chart -->
                            <div class="bg-white rounded-xl shadow-md p-4">
                                <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Asset Status Distribution
                                </h4>
                                <div class="h-80">
                                    <canvas id="assetStatusChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Procurement by Vendor and Timeline Charts - Side by Side -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Procurement by Vendor Chart -->
                            <div class="bg-white rounded-xl shadow-md p-4">
                                <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Procurement by Vendor
                                </h4>
                                <div class="h-80">
                                    <canvas id="procurementVendorChart"></canvas>
                                </div>
                            </div>

                            <!-- Procurement Timeline Chart -->
                            <div class="bg-white rounded-xl shadow-md p-4">
                                <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Procurement Timeline
                                </h4>
                                <div class="h-80">
                                    <canvas id="procurementTimelineChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="mb-8">
                <div class="bg-red-800 text-white rounded-xl shadow-lg p-6 md:p-8">
                    <div class="flex items-center mb-4">
                        <div class="bg-white/20 p-3 rounded-full mr-4 backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold">Procurement Summary</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
                            <div class="text-3xl md:text-4xl font-bold mb-2">{{ $assets->count() }}</div>
                            <p class="text-red-100 text-sm md:text-base font-medium">Total Assets Procured</p>
                        </div>
                        <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm sm:text-right">
                            <div class="text-3xl md:text-4xl font-bold mb-2">₱{{ number_format($assets->sum('purchase_price'), 2) }}</div>
                            <p class="text-red-100 text-sm md:text-base font-medium">Total Investment</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assets Table (Desktop View) -->
            <div class="overflow-x-auto hidden md:block">
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2-2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Procurement Details
                    </h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white rounded-lg">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Name</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Date</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Price</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($assets as $asset)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $asset->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <a href="{{ route('assets.index', ['search' => $asset->serial_number]) }}" class="font-bold text-red-600 hover:underline">{{ $asset->serial_number }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->category->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($asset->purchase_date)->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">₱{{ number_format($asset->purchase_price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->vendor->name ?? $asset->vendor }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($asset->status == 'IN USE') bg-green-100 text-green-800
                                        @elseif($asset->status == 'UNDER REPAIR') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ $asset->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination for Desktop -->
            <div class="mt-6 hidden md:block">
                {{ $assets->appends(request()->query())->links() }}
            </div>

            <!-- Assets List (Mobile View) -->
            <div class="grid grid-cols-1 gap-4 md:hidden">
                @foreach($assets as $asset)
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-800 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-start justify-between mb-3">
                        <div class="font-bold text-lg text-gray-900">{{ $asset->name }}</div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">₱{{ number_format($asset->purchase_price, 2) }}</div>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <span class="text-gray-600"><strong>Category:</strong> {{ $asset->category->name }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <span class="text-gray-600"><strong>Serial Number:</strong> <a href="{{ route('assets.index', ['search' => $asset->serial_number]) }}" class="font-bold text-red-600 hover:underline">{{ $asset->serial_number }}</a></span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-gray-600"><strong>Purchase Date:</strong> {{ \Carbon\Carbon::parse($asset->purchase_date)->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="text-gray-600"><strong>Vendor:</strong> {{ $asset->vendor->name ?? $asset->vendor }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-gray-600"><strong>Status:</strong></span>
                            <span class="px-2 py-1 ml-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($asset->status == 'IN USE') bg-green-100 text-green-800
                                @elseif($asset->status == 'UNDER REPAIR') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $asset->status }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Total Purchase Price Summary for Mobile -->
                <div class="bg-gray-50 rounded-xl shadow-lg p-6 flex justify-between items-center mt-2">
                    <div class="text-lg font-bold text-gray-900">Total Procurement Value</div>
                    <div class="text-lg font-bold text-red-800">₱{{ number_format($assets->sum('purchase_price'), 2) }}</div>
                </div>
            </div>

            <!-- Pagination for Mobile -->
            <div class="mt-6 md:hidden">
                {{ $assets->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Chart data from PHP
    const chartData = {
        categories: @json($categories->pluck('name')),
        vendors: @json($vendors->pluck('name')),
        assets: @json($assets->items()),
        colors: [
            '#EF4444', '#F59E0B', '#10B981', '#3B82F6', 
            '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16',
            '#F97316', '#6366F1', '#14B8A6', '#F43F5E'
        ]
    };

    // Wait for DOM to be loaded before creating charts
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, chart data:', chartData); // Debug log
        
        // Only create charts if there's data
        if (chartData.assets && chartData.assets.length > 0) {
            console.log('Creating charts with', chartData.assets.length, 'assets'); // Debug log
            
            // Prepare data for charts
            const categoryData = prepareCategoryData();
            const vendorData = prepareVendorData();
            const timelineData = prepareTimelineData();
            
            console.log('Prepared data:', { categoryData, vendorData, timelineData }); // Debug log
            
            // Procurement by Category Chart (Bar Chart)
            const procurementCategoryCtx = document.getElementById('procurementCategoryChart');
            if (procurementCategoryCtx) {
                console.log('Creating category chart'); // Debug log
                new Chart(procurementCategoryCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: categoryData.labels,
                        datasets: [{
                            label: 'Asset Count',
                            data: categoryData.counts,
                            backgroundColor: chartData.colors.slice(0, categoryData.labels.length),
                            borderColor: chartData.colors.slice(0, categoryData.labels.length).map(color => color + '80'),
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
                console.error('Category chart canvas not found'); // Debug log
            }

            // Asset Status Chart (Doughnut Chart)
            const assetStatusCtx = document.getElementById('assetStatusChart');
            if (assetStatusCtx) {
                console.log('Creating asset status chart'); // Debug log
                const statusData = prepareStatusData();
                new Chart(assetStatusCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: statusData.labels,
                        datasets: [{
                            data: statusData.counts,
                            backgroundColor: [
                                '#10B981', // Green for IN USE
                                '#F59E0B', // Yellow for UNDER REPAIR
                                '#EF4444', // Red for DISPOSED
                                '#8B5CF6', // Purple for PULLED OUT
                                '#DC2626'  // Dark red for LOST
                            ],
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
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return `${context.label}: ${context.parsed} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                console.error('Asset status chart canvas not found'); // Debug log
            }

            // Procurement by Vendor Chart (Horizontal Bar Chart)
            const procurementVendorCtx = document.getElementById('procurementVendorChart');
            if (procurementVendorCtx) {
                console.log('Creating vendor chart'); // Debug log
                new Chart(procurementVendorCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: vendorData.labels,
                        datasets: [{
                            label: 'Asset Count',
                            data: vendorData.counts,
                            backgroundColor: chartData.colors.slice(0, vendorData.labels.length).map(color => color + '60'),
                            borderColor: chartData.colors.slice(0, vendorData.labels.length),
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
                                        return `Assets: ${context.parsed.x}`;
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
                console.error('Vendor chart canvas not found'); // Debug log
            }

            // Procurement Timeline Chart (Line Chart)
            const procurementTimelineCtx = document.getElementById('procurementTimelineChart');
            if (procurementTimelineCtx) {
                console.log('Creating timeline chart'); // Debug log
                new Chart(procurementTimelineCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: timelineData.labels,
                        datasets: [{
                            label: 'Assets Procured',
                            data: timelineData.counts,
                            borderColor: '#8B5CF6',
                            backgroundColor: '#8B5CF6' + '20',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#8B5CF6',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 6,
                            pointHoverRadius: 8
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
                                    color: 'rgba(0, 0, 0, 0.1)',
                                    drawBorder: false
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
                console.error('Timeline chart canvas not found'); // Debug log
            }
        } else {
            console.log('No procurement data available for charts'); // Debug log
        }
    });

    // Helper functions to prepare chart data
    function prepareCategoryData() {
        const categoryCounts = {};
        chartData.assets.forEach(asset => {
            const categoryName = asset.category && asset.category.name ? asset.category.name : 'Unknown';
            categoryCounts[categoryName] = (categoryCounts[categoryName] || 0) + 1;
        });
        
        return {
            labels: Object.keys(categoryCounts),
            counts: Object.values(categoryCounts)
        };
    }

    function prepareVendorData() {
        const vendorCounts = {};
        chartData.assets.forEach(asset => {
            const vendorName = asset.vendor && asset.vendor.name ? asset.vendor.name : 'Unknown';
            vendorCounts[vendorName] = (vendorCounts[vendorName] || 0) + 1;
        });
        
        return {
            labels: Object.keys(vendorCounts),
            counts: Object.values(vendorCounts)
        };
    }

    function prepareTimelineData() {
        const timelineCounts = {};
        chartData.assets.forEach(asset => {
            if (asset.purchase_date) {
                const date = new Date(asset.purchase_date);
                const monthYear = date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                timelineCounts[monthYear] = (timelineCounts[monthYear] || 0) + 1;
            }
        });
        
        // Sort by date
        const sortedEntries = Object.entries(timelineCounts).sort((a, b) => {
            return new Date(a[0]) - new Date(b[0]);
        });
        
        return {
            labels: sortedEntries.map(entry => entry[0]),
            counts: sortedEntries.map(entry => entry[1])
        };
    }

    function prepareStatusData() {
        const statusCounts = {
            'IN USE': 0,
            'UNDER REPAIR': 0,
            'DISPOSED': 0,
            'PULLED OUT': 0,
            'LOST': 0
        };

        chartData.assets.forEach(asset => {
            const status = asset.status || 'Unknown';
            if (statusCounts.hasOwnProperty(status)) {
                statusCounts[status]++;
            }
        });

        return {
            labels: Object.keys(statusCounts),
            counts: Object.values(statusCounts)
        };
    }
</script>

<script>
    function printReport() {
        window.print();
    }

    function exportPaascu() {
        // Get current filter values
        const startDate = document.querySelector('input[name="start_date"]').value;
        const endDate = document.querySelector('input[name="end_date"]').value;
        const categoryId = document.querySelector('select[name="category_id"]').value;
        const status = document.querySelector('select[name="status"]').value;
        const vendorId = document.querySelector('select[name="vendor_id"]').value;

        // Build query string
        const params = new URLSearchParams();
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (categoryId) params.append('category_id', categoryId);
        if (status) params.append('status', status);
        if (vendorId) params.append('vendor_id', vendorId);

        // Redirect to PAASCU export route
        const url = `{{ route('reports.procurement-history.paascu-export') }}?${params.toString()}`;
        window.open(url, '_blank');
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
        .flex.flex-col.md\:flex-row.justify-between.items-start.md\:items-center.mb-8.gap-4.md\:gap-0 > div:last-child, /* Hide button group container */
        .flex.flex-col.md\:flex-row.justify-between.items-start.md\:items-center.mb-8.gap-4.md\:gap-0, /* Hide header section */
        .grid.grid-cols-1.gap-4.md\:hidden, /* Hide mobile cards */
        form { /* Hide filter form */
            display: none !important;
        }

        /* Hide summary card */
        .mb-8:has(.bg-red-800) {
            display: none !important;
        }

        /* Hide charts section in print */
        .mb-8 .bg-gray-50.rounded-xl.p-6 {
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

        /* Show total assets count in print */
        .text-gray-600.text-sm.md\:text-base {
            text-align: center !important;
            margin-bottom: 20px;
            font-size: 10pt;
            display: block !important;
        }

        /* Show date range in print if filtered */
        .mb-6.p-4.bg-blue-50.border.border-blue-200.rounded-xl.text-blue-800.text-center.md\:text-left {
            display: block !important;
            text-align: center !important;
            margin-bottom: 15px;
            background-color: white !important;
            border: 1px solid #000 !important;
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

        /* Ensure proper page breaks */
        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        /* Remove color-adjust for backgrounds/colors */
        thead th {
            -webkit-print-color-adjust: unset !important;
            print-color-adjust: unset !important;
        }

        /* Remove status colors in print */
        .px-3.inline-flex {
            background-color: transparent !important;
            color: black !important;
        }
    }
</style>
@endsection
