@extends('layouts.borrowing-app')

@section('content')
<div class="flex-1 bg-gradient-to-br from-gray-50 via-gray-50 to-red-50 min-h-screen" id="mainContent">
    <div class="max-w-7xl mx-auto">
        <!-- Modern Header with Pattern -->
        <div class="mb-6 relative overflow-hidden rounded-2xl">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-gradient-to-r from-red-600 via-red-700 to-red-800">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                </div>
            </div>
            
            <!-- Header Content -->
            <div class="relative px-6 py-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <!-- Icon with Glow -->
                        <div class="relative">
                            <div class="absolute inset-0 bg-white/30 blur-xl rounded-full"></div>
                            <div class="relative bg-white/20 p-3 rounded-xl backdrop-blur-md border border-white/30 shadow-lg">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Title Section -->
                        <div>
                            <h1 class="text-2xl font-bold text-white tracking-tightr">Asset Borrowing System</h1>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></div>
                                <p class="text-red-100 text-sm">Manage and track borrowed IT equipment</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Back Button -->
                    <a href="{{ route('my.tasks') }}" class="group px-4 py-2.5 bg-white/10 hover:bg-white/20 rounded-xl text-white text-sm font-medium transition-all duration-300 backdrop-blur-md border border-white/20 hover:border-white/40 shadow-lg hover:shadow-xl">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span>Back to Main</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="px-6 pb-6">
            <!-- Enhanced Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <!-- Total Assets Card -->
                <div class="group relative bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-red-200">
                    <!-- Accent Border -->
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-red-500 via-red-600 to-red-500"></div>
                    
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex-1">
                                <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1.5">Total Assets</p>
                                <p class="text-3xl font-bold text-gray-900 tracking-tight">{{ $totalAssets }}</p>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-0 bg-red-500 opacity-20 blur-xl rounded-full group-hover:opacity-30 transition-opacity"></div>
                                <div class="relative bg-gradient-to-br from-red-500 to-red-600 p-3.5 rounded-xl shadow-lg group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs">
                            <div class="flex items-center gap-1 text-green-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                <span class="font-semibold">All devices</span>
                            </div>
                            <span class="text-gray-400">in inventory</span>
                        </div>
                    </div>
                </div>

                <!-- Available Assets Card -->
                <div class="group relative bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-green-200">
                    <!-- Accent Border -->
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-green-500 via-green-600 to-green-500"></div>
                    
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex-1">
                                <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1.5">Available</p>
                                <p class="text-3xl font-bold text-gray-900 tracking-tight">{{ $availableAssets }}</p>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-0 bg-green-500 opacity-20 blur-xl rounded-full group-hover:opacity-30 transition-opacity"></div>
                                <div class="relative bg-gradient-to-br from-green-500 to-green-600 p-3.5 rounded-xl shadow-lg group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs">
                            <div class="flex items-center gap-1 text-green-600">
                                <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="font-semibold">Ready to borrow</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Borrowed Assets Card -->
                <div class="group relative bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-orange-200">
                    <!-- Accent Border -->
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-orange-500 via-orange-600 to-orange-500"></div>
                    
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex-1">
                                <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1.5">Borrowed</p>
                                <p class="text-3xl font-bold text-gray-900 tracking-tight">{{ $borrowedAssets }}</p>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-0 bg-orange-500 opacity-20 blur-xl rounded-full group-hover:opacity-30 transition-opacity"></div>
                                <div class="relative bg-gradient-to-br from-orange-500 to-orange-600 p-3.5 rounded-xl shadow-lg group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs">
                            <div class="flex items-center gap-1 text-orange-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-semibold">Currently in use</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modern Quick Actions -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <!-- New Borrowing -->
                <a href="#" class="group relative bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-red-300 hover:-translate-y-1">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-red-500 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="p-4">
                        <div class="flex flex-col items-center text-center gap-3">
                            <div class="relative">
                                <div class="absolute inset-0 bg-red-500 opacity-0 group-hover:opacity-20 blur-xl rounded-full transition-opacity"></div>
                                <div class="relative bg-gradient-to-br from-red-50 to-red-100 p-3 rounded-xl group-hover:from-red-500 group-hover:to-red-600 transition-all duration-300 shadow-sm group-hover:shadow-md">
                                    <svg class="w-5 h-5 text-red-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm group-hover:text-red-600 transition-colors">New Borrowing</h3>
                                <p class="text-xs text-gray-500 mt-1">Create request</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Active Borrowings -->
                <a href="#" class="group relative bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-green-300 hover:-translate-y-1">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-green-500 to-green-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="p-4">
                        <div class="flex flex-col items-center text-center gap-3">
                            <div class="relative">
                                <div class="absolute inset-0 bg-green-500 opacity-0 group-hover:opacity-20 blur-xl rounded-full transition-opacity"></div>
                                <div class="relative bg-gradient-to-br from-green-50 to-green-100 p-3 rounded-xl group-hover:from-green-500 group-hover:to-green-600 transition-all duration-300 shadow-sm group-hover:shadow-md">
                                    <svg class="w-5 h-5 text-green-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm group-hover:text-green-600 transition-colors">Active</h3>
                                <p class="text-xs text-gray-500 mt-1">View ongoing</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- History -->
                <a href="#" class="group relative bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-purple-300 hover:-translate-y-1">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-500 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="p-4">
                        <div class="flex flex-col items-center text-center gap-3">
                            <div class="relative">
                                <div class="absolute inset-0 bg-purple-500 opacity-0 group-hover:opacity-20 blur-xl rounded-full transition-opacity"></div>
                                <div class="relative bg-gradient-to-br from-purple-50 to-purple-100 p-3 rounded-xl group-hover:from-purple-500 group-hover:to-purple-600 transition-all duration-300 shadow-sm group-hover:shadow-md">
                                    <svg class="w-5 h-5 text-purple-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm group-hover:text-purple-600 transition-colors">History</h3>
                                <p class="text-xs text-gray-500 mt-1">Past records</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Reports -->
                <a href="#" class="group relative bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-orange-300 hover:-translate-y-1">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-orange-500 to-orange-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="p-4">
                        <div class="flex flex-col items-center text-center gap-3">
                            <div class="relative">
                                <div class="absolute inset-0 bg-orange-500 opacity-0 group-hover:opacity-20 blur-xl rounded-full transition-opacity"></div>
                                <div class="relative bg-gradient-to-br from-orange-50 to-orange-100 p-3 rounded-xl group-hover:from-orange-500 group-hover:to-orange-600 transition-all duration-300 shadow-sm group-hover:shadow-md">
                                    <svg class="w-5 h-5 text-orange-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm group-hover:text-orange-600 transition-colors">Reports</h3>
                                <p class="text-xs text-gray-500 mt-1">Analytics</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Enhanced Available Assets Section -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                <div class="px-6 py-4 bg-gradient-to-r from-red-50 via-red-50 to-orange-50 border-b border-red-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <div class="absolute inset-0 bg-red-500 opacity-20 blur-lg rounded-lg"></div>
                                <div class="relative bg-gradient-to-br from-red-500 to-red-600 p-2.5 rounded-lg shadow-md">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Available Assets</h2>
                                <p class="text-xs text-gray-600 flex items-center gap-1.5 mt-0.5">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                    Ready for borrowing
                                </p>
                            </div>
                        </div>
                        <a href="#" class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-lg text-sm font-semibold transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                            View All
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    @if($borrowableAssets->isEmpty())
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl mb-4 shadow-inner">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <p class="text-gray-600 text-base font-semibold mb-1">No assets available for borrowing</p>
                            <p class="text-gray-400 text-sm">Check back later for available equipment</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($borrowableAssets as $asset)
                                <div class="group relative bg-white rounded-xl border-2 border-gray-200 hover:border-red-300 p-4 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                                    <!-- Status Badge -->
                                    <div class="absolute top-3 right-3">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gradient-to-r from-green-500 to-green-600 text-white text-[10px] font-bold rounded-full shadow-sm">
                                            <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                                            AVAILABLE
                                        </span>
                                    </div>

                                    <!-- Asset Info -->
                                    <div class="mb-3 pr-20">
                                        <h3 class="font-bold text-gray-900 text-sm mb-1.5 group-hover:text-red-600 transition-colors line-clamp-1">
                                            {{ $asset->name }}
                                        </h3>
                                        <div class="flex items-center gap-1.5 text-xs">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                            </svg>
                                            <p class="text-gray-500 font-mono truncate">{{ $asset->serial_number }}</p>
                                        </div>
                                    </div>

                                    <!-- Details -->
                                    <div class="space-y-2 mb-4 pb-4 border-b border-gray-100">
                                        <div class="flex items-start gap-2">
                                            <div class="mt-0.5 bg-gray-100 p-1.5 rounded-md group-hover:bg-red-50 transition-colors">
                                                <svg class="w-3 h-3 text-gray-500 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[11px] text-gray-500 font-medium uppercase tracking-wide">Category</p>
                                                <p class="text-xs text-gray-700 font-semibold truncate">{{ $asset->category->name ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <div class="mt-0.5 bg-gray-100 p-1.5 rounded-md group-hover:bg-red-50 transition-colors">
                                                <svg class="w-3 h-3 text-gray-500 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[11px] text-gray-500 font-medium uppercase tracking-wide">Location</p>
                                                <p class="text-xs text-gray-700 font-semibold truncate">{{ $asset->location->name ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Borrow Button -->
                                    <button class="w-full px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-bold rounded-lg transition-all shadow-md hover:shadow-lg group-hover:scale-[1.02] transform">
                                        <div class="flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                            </svg>
                                            <span>Borrow Now</span>
                                        </div>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
