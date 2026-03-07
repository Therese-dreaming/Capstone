@extends('layouts.borrowing-app')

@section('content')
<div class="flex-1 bg-gradient-to-br from-gray-50 via-gray-50 to-red-50 min-h-screen" id="mainContent">
    <div class="max-w-7xl mx-auto px-6 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Borrowing Reports by Asset</h1>
                    <p class="text-sm text-gray-600 mt-1">View borrowing statistics and history for each asset</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg text-sm font-semibold shadow-sm">
                        {{ $assets->total() }} Assets
                    </span>
                    <a href="{{ route('borrowing.reports.by-user') }}" class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg shadow-md transition-all border border-gray-300">
                        View by User
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-md p-5 mb-6 border border-gray-100">
            <form method="GET" action="{{ route('borrowing.reports.by-asset') }}" class="space-y-4 md:space-y-0 md:flex md:items-end md:gap-3">
                <!-- Search -->
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Search Assets</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by name, serial number, or model..." 
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                </div>

                <!-- Category Filter -->
                <div class="md:w-48">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Category</label>
                    <select name="category" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="md:w-40">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Status</label>
                    <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Available</option>
                        <option value="in_use" {{ request('status') == 'in_use' ? 'selected' : '' }}>In Use</option>
                        <option value="missing" {{ request('status') == 'missing' ? 'selected' : '' }}>Missing</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-semibold rounded-lg transition-all shadow-sm hover:shadow-md">
                        Filter
                    </button>
                    <a href="{{ route('borrowing.reports.by-asset') }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-all">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Assets List -->
        @if($assets->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mb-6">
                @foreach($assets as $asset)
                    <a href="{{ route('borrowing.reports.asset-detail', $asset->id) }}"
                       class="group bg-white rounded-xl shadow-md hover:shadow-xl border-2 border-gray-200 hover:border-red-300 transition-all duration-300 overflow-hidden hover:-translate-y-1 cursor-pointer">
                        <!-- Asset Image/Icon Header -->
                        <div class="relative h-40 bg-gradient-to-br from-blue-100 to-blue-200 overflow-hidden">
                            @if($asset->photo)
                                <img src="{{ asset('storage/' . $asset->photo) }}" alt="{{ $asset->name }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="flex items-center justify-center h-full">
                                    <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Status Badge -->
                            <div class="absolute top-3 right-3">
                                @if($asset->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-500 text-white shadow-lg">
                                        Available
                                    </span>
                                @elseif($asset->status === 'in_use')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-orange-500 text-white shadow-lg">
                                        In Use
                                    </span>
                                @elseif($asset->status === 'missing')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-500 text-white shadow-lg">
                                        Missing
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-500 text-white shadow-lg">
                                        {{ ucfirst($asset->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Asset Info -->
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 text-base mb-1 truncate" title="{{ $asset->name }}">
                                {{ $asset->name }}
                            </h3>
                            
                            <div class="text-xs text-gray-500 mb-3 font-mono truncate">
                                {{ $asset->serial_number }}
                            </div>

                            <!-- Details Grid -->
                            <div class="space-y-2 mb-4">
                                @if($asset->category)
                                    <div class="flex items-center gap-2 text-xs text-gray-600">
                                        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        <span class="truncate">{{ $asset->category->name }}</span>
                                    </div>
                                @endif
                                
                                @if($asset->model)
                                    <div class="flex items-center gap-2 text-xs text-gray-600">
                                        <svg class="w-4 h-4 text-purple-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="truncate">{{ $asset->model }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Statistics -->
                            <div class="grid grid-cols-3 gap-2 pt-3 border-t border-gray-200">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-blue-600">{{ $asset->borrowing_items_count }}</div>
                                    <div class="text-xs text-gray-500 font-semibold">Total</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-orange-600">{{ $asset->active_borrowings_count }}</div>
                                    <div class="text-xs text-gray-500 font-semibold">Active</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-green-600">{{ $asset->returned_borrowings_count }}</div>
                                    <div class="text-xs text-gray-500 font-semibold">Returned</div>
                                </div>
                            </div>

                            <!-- View Details Arrow -->
                            <div class="mt-3 pt-3 border-t border-gray-200 flex items-center justify-center text-xs text-red-600 font-semibold group-hover:text-red-700">
                                <span>View Details</span>
                                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($assets->hasPages())
                <div class="bg-white rounded-xl shadow-md px-6 py-4 border border-gray-100">
                    {{ $assets->appends(request()->except('page'))->links() }}
                </div>
            @endif
        @else
            <div class="bg-white rounded-xl shadow-md p-12 text-center border border-gray-100">
                <div class="mb-4">
                    <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-700 mb-2">No Assets Found</h3>
                <p class="text-sm text-gray-600 mb-6">
                    @if(request()->has('search') || request()->has('category') || request()->has('status'))
                        Try adjusting your filters to see more results.
                    @else
                        There are no assets with borrowing history.
                    @endif
                </p>
                @if(request()->has('search') || request()->has('category') || request()->has('status'))
                    <a href="{{ route('borrowing.reports.by-asset') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-semibold rounded-lg transition-all shadow-sm hover:shadow-md">
                        Clear Filters
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
