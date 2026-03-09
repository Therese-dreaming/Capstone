@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50">

    <!-- Page Header with Background Design -->
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                        <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Asset Management</h1>
                        <p class="text-red-100 text-sm md:text-lg">Manage and track all institutional assets</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="p-4 md:p-6">
        <!-- Add success message section here -->
        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
            {{ session('error') }}
        </div>
        @endif
        <!-- Main Container -->
        <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-3">
                <h1 class="text-xl md:text-2xl font-bold">ALL ASSETS</h1>
                <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                        @if(auth()->user()->group_id === 4)
                            <a href="{{ route('custodian.assets.create') }}" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 text-center">
                                Add New Asset
                            </a>
                        @else
                            <a href="{{ route('assets.create') }}" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 text-center">
                                Add New Asset
                            </a>
                            <a href="{{ route('qr.list') }}{{ request('date_from') || request('date_to') ? '?' . http_build_query(request()->only(['date_from', 'date_to'])) : '' }}" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 text-center flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                QR List
                            </a>
                            @if(auth()->user()->group_id != 2)
                            <a href="{{ route('reports.procurement-history') }}" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 text-center">
                                Procurement History
                            </a>
                            <a href="{{ route('reports.disposal-history') }}" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 text-center">
                                Disposal History
                            </a>
                            @endif
                        @endif
                        <button onclick="openFullList()" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Date Range Filter Section -->
            <div class="mt-4 mb-4 p-2 bg-gray-50 rounded-lg border">
                <div class="flex items-center justify-between cursor-pointer" onclick="toggleFilter('dateFilter')">
                    <h3 class="text-sm font-semibold text-gray-700">Date Range Filter</h3>
                    <svg id="dateFilterIcon" class="w-4 h-4 text-gray-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
                <form id="dateFilter" method="GET" action="{{ request()->url() }}" class="hidden flex-col sm:flex-row gap-2 items-end mt-2">
                    <!-- Preserve existing search parameters -->
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('location'))
                        <input type="hidden" name="location" value="{{ request('location') }}">
                    @endif
                    
                    <div class="flex-1">
                        <label for="date_from" class="block text-xs font-medium text-gray-600 mb-0.5">From Date</label>
                        <input type="date" 
                               id="date_from" 
                               name="date_from" 
                               value="{{ request('date_from') }}"
                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-red-500">
                    </div>
                    
                    <div class="flex-1">
                        <label for="date_to" class="block text-xs font-medium text-gray-600 mb-0.5">To Date</label>
                        <input type="date" 
                               id="date_to" 
                               name="date_to" 
                               value="{{ request('date_to') }}"
                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-red-500">
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" 
                                class="bg-red-800 text-white px-3 py-1.5 text-sm rounded hover:bg-red-700 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                            </svg>
                            Filter
                        </button>
                        
                        @if(request('date_from') || request('date_to'))
                            @php
                                $dateParams = [];
                                if (request('search')) $dateParams['search'] = request('search');
                                if (request('status')) $dateParams['status'] = request('status');
                                if (request('category')) $dateParams['category'] = request('category');
                                if (request('location')) $dateParams['location'] = request('location');

                                if (request('warranty')) $dateParams['warranty'] = request('warranty');
                                if (request('technician')) $dateParams['technician'] = request('technician');
                                $dateUrl = request()->url() . (!empty($dateParams) ? '?' . http_build_query($dateParams) : '');
                            @endphp
                            <a href="{{ $dateUrl }}" 
                               class="bg-gray-500 text-white px-3 py-1.5 text-sm rounded hover:bg-gray-600 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear Date
                            </a>
                        @endif
                    </div>
                </form>
                
                @if(request('date_from') || request('date_to'))
                    <div class="mt-2 p-2 bg-red-50 rounded">
                        <p class="text-xs text-red-800">
                            <strong>Filtered by:</strong> 
                            @if(request('date_from'))
                                From {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}
                            @endif
                            @if(request('date_from') && request('date_to'))
                                to
                            @endif
                            @if(request('date_to'))
                                {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                            @endif
                            ({{ $assets->total() }} assets found)
                        </p>
                    </div>
                @endif
            </div>

            <!-- Additional Filters Section -->
            <div class="mb-4 p-2 bg-gray-50 rounded-lg border">
                <div class="flex items-center justify-between cursor-pointer" onclick="toggleFilter('additionalFilters')">
                    <h3 class="text-sm font-semibold text-gray-700">Additional Filters</h3>
                    <svg id="additionalFiltersIcon" class="w-4 h-4 text-gray-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
                <div id="additionalFilters" class="hidden space-y-2 mt-2">
                    <form method="GET" action="{{ request()->url() }}" class="space-y-2">
                    <!-- Preserve existing search and date parameters -->
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('date_from'))
                        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    @endif
                    @if(request('date_to'))
                        <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    @endif
                    @if(request('warranty'))
                        <input type="hidden" name="warranty" value="{{ request('warranty') }}">
                    @endif
                    @if(request('technician'))
                        <input type="hidden" name="technician" value="{{ request('technician') }}">
                    @endif
                    
                    <!-- First Row: 4 filters -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                        <div>
                            <label for="status" class="block text-xs font-medium text-gray-600 mb-0.5">Status</label>
                            <select name="status" id="status" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-red-500">
                                <option value="">All Status</option>
                                <option value="IN USE" {{ request('status') == 'IN USE' ? 'selected' : '' }}>IN USE</option>
                                <option value="UNDER REPAIR" {{ request('status') == 'UNDER REPAIR' ? 'selected' : '' }}>UNDER REPAIR</option>
                                <option value="DISPOSED" {{ request('status') == 'DISPOSED' ? 'selected' : '' }}>DISPOSED</option>
                                <option value="PULLED OUT" {{ request('status') == 'PULLED OUT' ? 'selected' : '' }}>PULLED OUT</option>
                                <option value="LOST" {{ request('status') == 'LOST' ? 'selected' : '' }}>LOST</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="category" class="block text-xs font-medium text-gray-600 mb-0.5">Category</label>
                            <select name="category" id="category" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-red-500">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="location" class="block text-xs font-medium text-gray-600 mb-0.5">Location</label>
                            <select name="location" id="location" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-red-500">
                                <option value="">All Locations</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ request('location') == $location->id ? 'selected' : '' }}>
                                        {{ $location->full_location }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                    </div>
                    
                    <!-- Second Row: 2 filters + buttons -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 items-end">
                        <div>
                            <label for="warranty" class="block text-xs font-medium text-gray-600 mb-0.5">Warranty</label>
                            <select name="warranty" id="warranty" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-red-500">
                                <option value="">All Warranties</option>
                                <option value="expiring_365" {{ request('warranty') == 'expiring_365' ? 'selected' : '' }}>Expiring within 365 days</option>
                                <option value="expiring_30" {{ request('warranty') == 'expiring_30' ? 'selected' : '' }}>Expiring within 30 days</option>
                                <option value="expired" {{ request('warranty') == 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="technician" class="block text-xs font-medium text-gray-600 mb-0.5">Technician</label>
                            <select name="technician" id="technician" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-red-500">
                                <option value="">All Technicians</option>
                                @if(isset($technicians))
                                    @foreach($technicians as $technician)
                                        <option value="{{ $technician->id }}" {{ request('technician') == $technician->id ? 'selected' : '' }}>
                                            {{ $technician->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        <div class="flex gap-2">
                            <button type="submit" class="bg-red-800 text-white px-3 py-1.5 text-sm rounded hover:bg-red-700 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                                </svg>
                                Apply Filters
                            </button>
                            
                            @if(request('status') || request('category') || request('location') || request('warranty') || request('technician'))
                                @php
                                    $clearParams = [];
                                    if (request('search')) $clearParams['search'] = request('search');
                                    if (request('date_from')) $clearParams['date_from'] = request('date_from');
                                    if (request('date_to')) $clearParams['date_to'] = request('date_to');
                                    $clearUrl = request()->url() . (!empty($clearParams) ? '?' . http_build_query($clearParams) : '');
                                @endphp
                                <a href="{{ $clearUrl }}" 
                                   class="bg-gray-500 text-white px-3 py-1.5 text-sm rounded hover:bg-gray-600 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Clear Filters
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
                
                @if(request('status') || request('category') || request('location') || request('warranty') || request('technician') || request('parent'))
                    <div class="mt-3 p-3 bg-red-50 rounded-md">
                        <p class="text-sm text-red-800">
                            <strong>Filtered by:</strong> 
                            @if(request('status'))
                                Status: {{ request('status') }}
                            @endif
                            @if(request('category'))
                                @php
                                    $selectedCategory = $categories->firstWhere('id', request('category'));
                                @endphp
                                @if($selectedCategory)
                                    @if(request('status')) | @endif
                                    Category: {{ $selectedCategory->name }}
                                @endif
                            @endif
                            @if(request('location'))
                                @php
                                    $selectedLocation = $locations->firstWhere('id', request('location'));
                                @endphp
                                @if($selectedLocation)
                                    @if(request('status') || request('category')) | @endif
                                    Location: {{ $selectedLocation->full_location }}
                                @endif
                            @endif
                            @if(request('parent'))
                                @php
                                    $parentFilterLabel = '';
                                    if (request('parent') == 'has_parent') {
                                        $parentFilterLabel = 'Has Parent (Components)';
                                    } elseif (request('parent') == 'no_parent') {
                                        $parentFilterLabel = 'No Parent (Standalone)';
                                    } elseif (request('parent') == 'is_parent') {
                                        $parentFilterLabel = 'Is Parent (Has Components)';
                                    } else {
                                        $selectedParent = isset($parentAssets) ? $parentAssets->firstWhere('id', request('parent')) : null;
                                        if ($selectedParent) {
                                            $parentFilterLabel = 'Parent: ' . $selectedParent->name . ' (' . $selectedParent->serial_number . ')';
                                        } else {
                                            $parentFilterLabel = 'Parent: ' . request('parent');
                                        }
                                    }
                                @endphp
                                @if(request('status') || request('category') || request('location')) | @endif
                                {{ $parentFilterLabel }}
                            @endif
                            @if(request('warranty'))
                                @if(request('status') || request('category') || request('location') || request('parent')) | @endif
                                Warranty: {{ ucfirst(str_replace('_', ' ', request('warranty'))) }}
                            @endif
                            @if(request('technician'))
                                @php
                                    $selectedTechnician = isset($technicians) ? $technicians->firstWhere('id', request('technician')) : null;
                                @endphp
                                @if($selectedTechnician)
                                    @if(request('status') || request('category') || request('location') || request('warranty') || request('parent')) | @endif
                                    Technician: {{ $selectedTechnician->name }}
                                @endif
                            @endif
                            ({{ $assets->total() }} assets found)
                        </p>
                    </div>
                @endif
                </div>
            </div>

            <!-- Search Section -->
            <div class="mb-4 p-2 bg-gray-50 rounded-lg border">
                <div class="flex items-center justify-between cursor-pointer" onclick="toggleFilter('searchSection')">
                    <h3 class="text-sm font-semibold text-gray-700">Search Assets</h3>
                    <svg id="searchSectionIcon" class="w-4 h-4 text-gray-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
                <div id="searchSection" class="hidden flex-col sm:flex-row items-start sm:items-center gap-2 mt-2">
                    <div class="relative w-full flex gap-2">
                        <input type="text" id="searchInput" placeholder="Search assets..." class="flex-1 min-w-0 sm:min-w-[350px] px-2 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-red-500" value="{{ request('search') }}">
                        <button id="searchButton" class="bg-red-800 text-white px-3 py-1.5 text-sm rounded hover:bg-red-700 flex items-center whitespace-nowrap">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Search
                        </button>
                    </div>
                    @if(request('search'))
                        @php
                            $searchParams = [];
                            if (request('date_from')) $searchParams['date_from'] = request('date_from');
                            if (request('date_to')) $searchParams['date_to'] = request('date_to');
                            if (request('status')) $searchParams['status'] = request('status');
                            if (request('category')) $searchParams['category'] = request('category');
                            if (request('location')) $searchParams['location'] = request('location');
                            if (request('parent')) $searchParams['parent'] = request('parent');
                            if (request('warranty')) $searchParams['warranty'] = request('warranty');
                            if (request('technician')) $searchParams['technician'] = request('technician');
                            $searchUrl = request()->url() . (!empty($searchParams) ? '?' . http_build_query($searchParams) : '');
                        @endphp
                        <a href="{{ $searchUrl }}" 
                           class="bg-gray-500 text-white px-3 py-1.5 text-sm rounded hover:bg-gray-600 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Clear Search
                        </a>
                    @endif
                </div>
                @if(request('search'))
                    <div class="mt-2 p-2 bg-blue-50 rounded">
                        <p class="text-xs text-blue-800">
                            <strong>Searching for:</strong> "{{ request('search') }}" ({{ $assets->total() }} assets found)
                        </p>
                    </div>
                @endif
            </div>

            <!-- Divider Line -->
            <div class="border-b-2 border-red-800 mb-4 md:mb-6"></div>


            <!-- Mobile View Cards -->
            <div class="md:hidden space-y-2 mb-4">
                @foreach($assets as $asset)
                <div class="border rounded-lg p-3 bg-white shadow-sm">
                    <div class="mb-2">
                        <h3 class="font-bold text-gray-900 text-sm">{{ $asset->name ?? '' }}</h3>
                        <p class="text-xs text-gray-600">System SN: {{ $asset->serial_number ?? 'N/A' }}</p>
                        @if($asset->manufacturer_serial_number)
                            <p class="text-xs text-gray-600">Manufacturer SN: <span class="font-medium">{{ $asset->manufacturer_serial_number }}</span></p>
                        @endif
                    </div>
                    
                    <div class="flex gap-2 mb-2">
                        @if($asset->photo)
                        <img src="{{ asset('storage/' . $asset->photo) }}" alt="Asset Photo" class="w-20 h-20 object-cover rounded cursor-pointer hover:opacity-75 transition-opacity" onclick="openImageModal('{{ asset('storage/' . $asset->photo) }}')">
                        @else
                        <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                            <span class="text-gray-500">No Photo</span>
                        </div>
                        @endif
                        
                        @if($asset->qr_code)
                        <img src="{{ asset('storage/' . $asset->qr_code) }}" alt="QR Code" class="w-20 h-20 cursor-pointer hover:opacity-75 transition-opacity" onclick="openImageModal('{{ asset('storage/' . $asset->qr_code) }}')">
                        @endif
                    </div>
                    
                                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs mb-2">
                            <div>
                                <p class="text-gray-500">Status:</p>
                                <p class="font-medium">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @switch($asset->status)
                                                @case('UNDER REPAIR')
                                                    bg-yellow-100 text-yellow-800
                                                    @break
                                                @case('IN USE')
                                                    bg-green-100 text-green-800
                                                    @break
                                                @case('DISPOSED')
                                                    bg-red-100 text-red-800
                                                    @break
                                                @case('PULLED OUT')
                                                    bg-orange-100 text-orange-800
                                                    @break
                                                @case('LOST')
                                                    bg-red-200 text-red-900
                                                    @break
                                                @default
                                                    bg-gray-100 text-gray-800
                                            @endswitch">
                                        {{ $asset->status ?? 'N/A' }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500">Category:</p>
                                <p class="font-medium">{{ $asset->category->name ?? '' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Location:</p>
                                <p class="font-medium">{{ $asset->location ? $asset->location->full_location : 'N/A' }}</p>
                            </div>
                            @if($asset->parent_id && $asset->parent)
                            <div>
                                <p class="text-gray-500">Parent Asset:</p>
                                <p class="font-medium text-purple-700">{{ $asset->parent->name }}</p>
                                <p class="text-xs text-gray-500">{{ $asset->parent->serial_number }}</p>
                            </div>
                            @elseif($asset->components && $asset->components->count() > 0)
                            <div>
                                <p class="text-gray-500">Components:</p>
                                <p class="font-medium text-blue-700">{{ $asset->components->count() }} component(s)</p>
                            </div>
                            @endif
                            <div>
                                <p class="text-gray-500">Created By:</p>
                                <p class="font-medium">{{ $asset->creator ? $asset->creator->name : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Price:</p>
                                <p class="font-medium">₱{{ number_format($asset->purchase_price ?? 0, 2) }}</p>
                            </div>
                        </div>
                    
                    <div class="flex justify-end space-x-2">
                        @if(auth()->user()->group_id === 4)
                            <!-- Custodians only see Edit button -->
                            <a href="{{ route('custodian.assets.edit', $asset->id) }}" class="bg-yellow-600 text-white p-1.5 rounded hover:bg-yellow-700 tooltip" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                        @else
                            <!-- Non-custodians see all actions -->
                            <a href="{{ route('reports.asset-history', $asset->id) }}" class="bg-blue-600 text-white p-1.5 rounded hover:bg-blue-700 tooltip" title="History">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </a>
                            <a href="{{ route('assets.edit', $asset->id) }}" class="bg-yellow-600 text-white p-1.5 rounded hover:bg-yellow-700 tooltip" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            @if(auth()->user()->group_id === 1)
                            <button onclick="confirmAssetDelete({{ $asset->id }})" class="bg-red-600 text-white p-1.5 rounded hover:bg-red-700 tooltip" title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                            @endif
                            <button onclick="confirmAssetDispose({{ $asset->id }})" class="bg-gray-600 text-white p-1.5 rounded hover:bg-gray-700 tooltip" title="Dispose">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                            </button>
                            @if($asset->status === 'LOST')
                                <button onclick="confirmAssetMarkAsFound({{ $asset->id }})" class="bg-green-500 text-white p-1.5 rounded hover:bg-green-600 tooltip" title="Mark as Found">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                            @else
                                <button onclick="confirmAssetMarkAsLost({{ $asset->id }})" class="bg-red-500 text-white p-1.5 rounded hover:bg-red-600 tooltip" title="Mark as Lost">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Desktop Asset List Preview -->
            <div class="hidden md:block relative overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase w-24">Photo</th>
                            <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase w-24">QR Code</th>
                            <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase">Serial Number</th>
                            <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase">Manufacturer SN</th>
                            <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase">Asset Name</th>
                            <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase">Created By</th>
                            <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase sticky right-16 bg-gray-50 z-10">Category</th>
                            <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase sticky right-0 bg-gray-50 z-10">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assets as $asset)
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-500">{{ $loop->iteration + ($assets->currentPage() - 1) * $assets->perPage() }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">
                                @if($asset->photo)
                                <img src="{{ asset('storage/' . $asset->photo) }}" alt="Asset Photo" class="w-16 h-16 object-cover rounded cursor-pointer hover:opacity-75 transition-opacity" onclick="openImageModal('{{ asset('storage/' . $asset->photo) }}')">
                                @else
                                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                    <span class="text-xs text-gray-500">No Photo</span>
                                </div>
                                @endif
                            </td>
                            <!-- Rest of the desktop table remains the same -->
                            <td class="px-2 py-2 whitespace-nowrap">
                                @if($asset->qr_code)
                                <img src="{{ asset('storage/' . $asset->qr_code) }}" alt="QR Code" class="w-16 h-16 cursor-pointer hover:opacity-75 transition-opacity" onclick="openImageModal('{{ asset('storage/' . $asset->qr_code) }}')">
                                @endif
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 font-bold">{{ $asset->serial_number ?? 'N/A' }}</td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">
                                {{ $asset->manufacturer_serial_number ?? 'N/A' }}
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">
                                {{ $asset->name ?? '' }}
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full inline-flex items-center justify-center min-w-[80px]
                                            @switch($asset->status)
                                                @case('UNDER REPAIR')
                                                    bg-yellow-100 text-yellow-800
                                                    @break
                                                @case('IN USE')
                                                    bg-green-100 text-green-800
                                                    @break
                                                @case('DISPOSED')
                                                    bg-red-100 text-red-800
                                                    @break
                                                @case('PULLED OUT')
                                                    bg-orange-100 text-orange-800
                                                    @break
                                                @case('LOST')
                                                    bg-red-200 text-red-900
                                                    @break
                                                @default
                                                    bg-gray-100 text-gray-800
                                            @endswitch">
                                    {{ $asset->status ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">{{ $asset->creator ? $asset->creator->name : 'N/A' }}</td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">₱{{ number_format($asset->purchase_price ?? 0, 2) }}</td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 sticky right-16 bg-white z-10">{{ $asset->category->name ?? '' }}</td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 sticky right-0 bg-white z-10">
                                <div class="flex space-x-2">
                                    @if(auth()->user()->group_id === 4)
                                        <!-- Custodians only see Edit button -->
                                        <a href="{{ route('custodian.assets.edit', $asset->id) }}" class="bg-yellow-600 text-white p-1.5 rounded hover:bg-yellow-700 tooltip" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    @else
                                        <!-- Non-custodians see all actions -->
                                        <a href="{{ route('reports.asset-history', $asset->id) }}" class="bg-blue-600 text-white p-1.5 rounded hover:bg-blue-700 tooltip" title="History">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('assets.edit', $asset->id) }}" class="bg-yellow-600 text-white p-1.5 rounded hover:bg-yellow-700 tooltip" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        @if(auth()->user()->group_id === 1)
                                        <button onclick="confirmAssetDelete({{ $asset->id }})" class="bg-red-600 text-white p-1.5 rounded hover:bg-red-700 tooltip" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        @endif
                                        <button onclick="confirmAssetDispose({{ $asset->id }})" class="bg-gray-600 text-white p-1.5 rounded hover:bg-gray-700 tooltip" title="Dispose">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                            </svg>
                                        </button>
                                        @if($asset->status === 'LOST')
                                            <button onclick="confirmAssetMarkAsFound({{ $asset->id }})" class="bg-green-500 text-white p-1.5 rounded hover:bg-green-600 tooltip" title="Mark as Found">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        @else
                                            <button onclick="confirmAssetMarkAsLost({{ $asset->id }})" class="bg-red-500 text-white p-1.5 rounded hover:bg-red-600 tooltip" title="Mark as Lost">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add pagination links -->
        <div class="mt-6">
            {{ $assets->links() }}
        </div>
    </div>
</div>

<!-- Move imageModal outside and adjust z-index -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center">
    <div class="relative" onclick="event.stopPropagation();">
        <img id="enlargedImage" src="" alt="Enlarged Image" class="max-h-[80vh] max-w-[80vw] object-contain">
        <button onclick="closeImageModal()" class="absolute -top-4 -right-4 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 transition-colors">
            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <button onclick="downloadImage()" class="absolute -bottom-4 left-1/2 transform -translate-x-1/2 bg-red-600 text-white rounded-full px-4 py-2 shadow-lg hover:bg-red-700 transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <span class="font-medium">Download</span>
        </button>
    </div>
</div>

<!-- Update fullListModal z-index -->
<div id="fullListModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-6 border w-11/12 shadow-xl rounded-lg bg-white">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Complete Asset List</h2>
                <p class="text-sm text-gray-600 mt-1">Comprehensive view of all asset details</p>
            </div>
            <button onclick="closeFullList()" class="text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Search Bar -->
        <div class="mb-4">
            <div class="flex gap-2">
                <input type="text" id="modalSearchInput" placeholder="Search assets..." class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" value="{{ request('search') }}">
                <button id="modalSearchButton" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center whitespace-nowrap">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Search
                </button>
            </div>
        </div>

        <!-- Filter -->
        <div class="flex justify-between items-center mb-4">
            <div class="relative">
                <button id="columnFilterBtn" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                    Show/Hide Columns
                </button>
                <!-- Update the column filter menu -->
                <div id="columnFilterMenu" class="hidden absolute left-0 mt-2 w-64 bg-white rounded-md shadow-lg z-50 p-4 border border-gray-200">
                    <div class="space-y-2 max-h-96 overflow-y-auto">
                        <!-- Add Select All option -->
                        <label class="flex items-center space-x-2 pb-2 border-b border-gray-200 mb-2">
                            <input type="checkbox" checked id="selectAll" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="font-medium">Select All</span>
                        </label>

                        <!-- Update existing checkboxes to match styling -->
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="0" class="column-toggle rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span>#</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="1" class="column-toggle">
                            <span>Photo</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="2" class="column-toggle">
                            <span>QR Code</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="3" class="column-toggle">
                            <span>Asset Name</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="4" class="column-toggle">
                            <span>Serial Number</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="5" class="column-toggle">
                            <span>Manufacturer SN</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="6" class="column-toggle">
                            <span>Purchase Price</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="7" class="column-toggle">
                            <span>Category</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="8" class="column-toggle">
                            <span>Location</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="9" class="column-toggle">
                            <span>Status</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="10" class="column-toggle">
                            <span>Model</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="11" class="column-toggle">
                            <span>Specification</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="12" class="column-toggle">
                            <span>Vendor</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="13" class="column-toggle">
                            <span>Purchase Date</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="14" class="column-toggle">
                            <span>Warranty Period</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="15" class="column-toggle">
                            <span>Lifespan (Yrs)</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="16" class="column-toggle">
                            <span>End of Lifespan Date</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="17" class="column-toggle">
                            <span>Acquisition Doc</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-y-auto max-h-[70vh] rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 full-list-table">
                <!-- Add to full list table header -->
                <thead class="bg-gray-50 sticky top-0 shadow-sm z-10">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">Photo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">QR Code</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Asset Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Serial Number</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Manufacturer SN</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Purchase Price</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Location</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Model</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Specification</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Vendor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Purchase Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Warranty Period</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Lifespan (Yrs)</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">End of Lifespan Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Acquisition Doc</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($assets as $asset)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration + ($assets->currentPage() - 1) * $assets->perPage() }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($asset->photo)
                            <img src="{{ asset('storage/' . $asset->photo) }}" alt="Asset Photo" class="w-16 h-16 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity shadow-sm" onclick="openImageModal('{{ asset('storage/' . $asset->photo) }}')">
                            @else
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($asset->qr_code)
                            <img src="{{ asset('storage/' . $asset->qr_code) }}" alt="QR Code" class="w-16 h-16 cursor-pointer hover:opacity-75 transition-opacity shadow-sm rounded-lg" onclick="openImageModal('{{ asset('storage/' . $asset->qr_code) }}')">
                            @else
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                            </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            {{ $asset->name ?? '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap font-bold">{{ $asset->serial_number ?? '' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            @if($asset->manufacturer_serial_number)
                                <span class="font-medium">{{ $asset->manufacturer_serial_number }}</span>
                            @else
                                <span class="text-gray-400 text-xs">N/A</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($asset->purchase_price ?? 0, 2) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $asset->category->name ?? '' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $asset->location ? $asset->location->full_location : 'N/A' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-3 py-1.5 text-xs font-medium rounded-full inline-flex items-center justify-center min-w-[90px]
                                    @switch($asset->status)
                                        @case('UNDER REPAIR')
                                            bg-yellow-100 text-yellow-800
                                            @break
                                        @case('IN USE')
                                            bg-green-100 text-green-800
                                            @break
                                        @case('DISPOSED')
                                            bg-red-100 text-red-800
                                            @break
                                        @case('PULLED OUT')
                                            bg-orange-100 text-orange-800
                                            @break
                                        @case('LOST')
                                            bg-red-200 text-red-900
                                            @break
                                        @default
                                            bg-gray-100 text-gray-800
                                    @endswitch">
                                {{ $asset->status ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $asset->model ?? '' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $asset->specification ?? '' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $asset->vendor->name ?? '' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($asset->purchase_date)->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($asset->warranty_period)->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                            @switch($asset->life_status)
                                @case('critical')
                                    bg-red-100 text-red-800
                                    @break
                                @case('warning')
                                    bg-yellow-100 text-yellow-800
                                    @break
                                @default
                                    bg-green-100 text-green-800
                        @endswitch">
                                {{ $asset->calculated_lifespan }} year(s)
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                            @switch($asset->life_status)
                                @case('critical')
                                    bg-red-100 text-red-800
                                    @break
                                @case('warning')
                                    bg-yellow-100 text-yellow-800
                                    @break
                                @default
                                    bg-green-100 text-green-800
                            @endswitch">
                                {{ \Carbon\Carbon::parse($asset->end_of_life_date)->format('M d, Y') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($asset->acquisition_document)
                                <a href="{{ asset('storage/' . $asset->acquisition_document) }}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                                    <svg class="w-5 h-5 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    View
                                </a>
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="assetDeleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" style="z-index: 60;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Delete Asset</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">Are you sure you want to delete this asset? This action cannot be undone.</p>
                <div class="text-left">
                    <label for="deletePassword" class="block text-sm font-medium text-gray-700 mb-2">Confirm your password</label>
                    <input id="deletePassword" type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500" placeholder="Enter your password" autocomplete="current-password">
                    <div id="deleteError" class="hidden mt-2 p-2 bg-red-100 border border-red-400 text-red-700 rounded text-sm"></div>
                </div>
            </div>
            <div class="flex justify-center gap-4 mt-4">
                <button id="assetDeleteConfirm" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                    Delete
                </button>
                <button onclick="closeAssetDeleteModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Dispose Confirmation Modal -->
<div id="assetDisposeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" style="z-index: 60;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Dispose Asset</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">Are you sure you want to mark this asset as disposed?</p>
                <div class="text-left">
                    <label for="disposeReason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Disposal</label>
                    <textarea id="disposeReason" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500" rows="3" placeholder="Enter reason for disposal"></textarea>
                    <div id="disposeError" class="hidden mt-2 p-2 bg-red-100 border border-red-400 text-red-700 rounded text-sm"></div>
                </div>
            </div>
            <div class="flex justify-center gap-4 mt-4">
                <button id="assetDisposeConfirm" class="px-4 py-2 bg-yellow-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-300">
                    Dispose
                </button>
                <button onclick="closeAssetDisposeModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Mark as Lost Confirmation Modal -->
<div id="assetMarkAsLostModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" style="z-index: 60;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Mark Asset as Lost</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">Are you sure you want to mark this asset as lost?</p>
                <div class="text-left">
                    <label for="lostReason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Loss</label>
                    <textarea id="lostReason" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500" rows="3" placeholder="Enter reason for loss"></textarea>
                    <div id="lostError" class="hidden mt-2 p-2 bg-red-100 border border-red-400 text-red-700 rounded text-sm"></div>
                </div>
            </div>
            <div class="flex justify-center gap-4 mt-4">
                <button id="assetMarkAsLostConfirm" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                    Mark as Lost
                </button>
                <button onclick="closeAssetMarkAsLostModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Mark as Found Confirmation Modal -->
<div id="assetMarkAsFoundModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" style="z-index: 60;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Mark Asset as Found</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">Where was this asset found? It will be marked as "In Use" at the selected location.</p>
                <div class="text-left">
                    <label for="foundLocation" class="block text-sm font-medium text-gray-700 mb-2">Found Location</label>
                    <select id="foundLocation" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="">Select location where asset was found</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" data-location="{{ $location->full_location }}">
                                {{ $location->full_location }}
                            </option>
                        @endforeach
                        <option value="new_location">+ Add New Location</option>
                    </select>
                    
                    <!-- New Location Form (Hidden by default) -->
                    <div id="newLocationForm" class="hidden mt-4 p-4 bg-gray-50 rounded-lg border">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Add New Location</h4>
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label for="newBuilding" class="block text-xs font-medium text-gray-600 mb-1">Building</label>
                                <input type="text" id="newBuilding" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="e.g., Main Building">
                            </div>
                            <div>
                                <label for="newFloor" class="block text-xs font-medium text-gray-600 mb-1">Floor</label>
                                <input type="text" id="newFloor" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="e.g., 2nd Floor">
                            </div>
                            <div>
                                <label for="newRoom" class="block text-xs font-medium text-gray-600 mb-1">Room Number</label>
                                <input type="text" id="newRoom" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="e.g., Room 201">
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button type="button" id="saveNewLocation" class="px-3 py-1.5 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                Save Location
                            </button>
                            <button type="button" id="cancelNewLocation" class="px-3 py-1.5 bg-gray-500 text-white text-xs rounded hover:bg-gray-600">
                                Cancel
                            </button>
                        </div>
                    </div>
                    
                    <div id="foundError" class="hidden mt-2 p-2 bg-red-100 border border-red-400 text-red-700 rounded text-sm"></div>
                </div>
            </div>
            <div class="flex justify-center gap-4 mt-4">
                <button id="assetMarkAsFoundConfirm" class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-300">
                    Mark as Found
                </button>
                <button onclick="closeAssetMarkAsFoundModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Update the JavaScript for dispose confirmation -->
<script>
    function openFullList() {
        document.getElementById('fullListModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeFullList() {
        document.getElementById('fullListModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openImageModal(imageSrc) {
        event.stopPropagation();
        const modal = document.getElementById('imageModal');
        const enlargedImage = document.getElementById('enlargedImage');
        enlargedImage.src = imageSrc;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function downloadImage() {
        const enlargedImage = document.getElementById('enlargedImage');
        const imageSrc = enlargedImage.src;
        
        // Extract filename from URL
        const urlParts = imageSrc.split('/');
        const filename = urlParts[urlParts.length - 1] || 'qr-code.png';
        
        // Use fetch to get the image as a blob to avoid CORS issues
        fetch(imageSrc)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.blob();
            })
            .then(blob => {
                // Create a temporary URL for the blob
                const blobUrl = window.URL.createObjectURL(blob);
                
                // Create a temporary anchor element and trigger download
                const link = document.createElement('a');
                link.href = blobUrl;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                
                // Clean up
                document.body.removeChild(link);
                window.URL.revokeObjectURL(blobUrl);
            })
            .catch(error => {
                console.error('Error downloading image:', error);
                
                // Fallback: try direct download
                const link = document.createElement('a');
                link.href = imageSrc;
                link.download = filename;
                link.target = '_blank';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
    }

    // Toggle filter visibility
    function toggleFilter(filterId) {
        const filterSection = document.getElementById(filterId);
        const icon = document.getElementById(filterId + 'Icon');
        
        if (filterSection.classList.contains('hidden')) {
            filterSection.classList.remove('hidden');
            filterSection.classList.add('flex');
            icon.classList.add('rotate-180');
        } else {
            filterSection.classList.add('hidden');
            filterSection.classList.remove('flex');
            icon.classList.remove('rotate-180');
        }
    }

    // Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        document.getElementById('fullListModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFullList();
            }
        });

        const searchInput = document.getElementById('searchInput');
        const searchButton = document.getElementById('searchButton');
        const modalSearchInput = document.getElementById('modalSearchInput');
        const modalSearchButton = document.getElementById('modalSearchButton');

        // Function to perform search
        function performSearch(searchValue) {
            const currentUrl = new URL(window.location.href);
            
            if (searchValue && searchValue.trim()) {
                currentUrl.searchParams.set('search', searchValue.trim());
            } else {
                currentUrl.searchParams.delete('search');
            }
            
            window.location.href = currentUrl.toString();
        }

        // Main search button event listener
        if (searchButton) {
            searchButton.addEventListener('click', function() {
                const searchValue = searchInput ? searchInput.value : '';
                performSearch(searchValue);
            });
        }

        // Main search input event listener (Enter key)
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performSearch(this.value);
                }
            });
        }

        // Modal search button event listener
        if (modalSearchButton) {
            modalSearchButton.addEventListener('click', function() {
                const searchValue = modalSearchInput ? modalSearchInput.value : '';
                performSearch(searchValue);
            });
        }

        // Modal search input event listener (Enter key)
        if (modalSearchInput) {
            modalSearchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performSearch(this.value);
                }
            });
        }
    });

    let currentAssetId = null;
    
    // Debug function to track currentAssetId changes
    function debugCurrentAssetId(action) {
        console.log(`currentAssetId ${action}:`, currentAssetId, 'type:', typeof currentAssetId);
    }

    // Helper functions for error display
    function showError(errorElementId, message) {
        const errorElement = document.getElementById(errorElementId);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
        }
    }

    function hideError(errorElementId) {
        const errorElement = document.getElementById(errorElementId);
        if (errorElement) {
            errorElement.classList.add('hidden');
        }
    }

    function confirmAssetDelete(assetId) {
        console.log('confirmAssetDelete called with assetId:', assetId, 'type:', typeof assetId);
        if (assetId && assetId !== null && assetId !== undefined && !isNaN(assetId)) {
            currentAssetId = assetId;
            debugCurrentAssetId('set in confirmAssetDelete');
            const deleteModal = document.getElementById('assetDeleteModal');
            if (deleteModal) {
                deleteModal.classList.remove('hidden');
            }
        } else {
            console.error('Invalid asset ID provided to confirmAssetDelete:', assetId);
        }
    }

    function confirmAssetDispose(assetId) {
        console.log('confirmAssetDispose called with assetId:', assetId, 'type:', typeof assetId);
        if (assetId && assetId !== null && assetId !== undefined && !isNaN(assetId)) {
            currentAssetId = assetId;
            debugCurrentAssetId('set in confirmAssetDispose');
            const disposeModal = document.getElementById('assetDisposeModal');
            if (disposeModal) {
                // Clear any previous errors and reset the form
                hideError('disposeError');
                const disposeReasonElement = document.getElementById('disposeReason');
                if (disposeReasonElement) {
                    disposeReasonElement.value = '';
                }
                
                disposeModal.classList.remove('hidden');
                // Ensure the textarea is accessible
                console.log('Dispose reason element after modal open:', !!disposeReasonElement);
                if (disposeReasonElement) {
                    disposeReasonElement.focus();
                    // Add real-time validation
                    disposeReasonElement.addEventListener('input', function() {
                        if (this.value.trim()) {
                            hideError('disposeError');
                        }
                    });
                }
            }
        } else {
            console.error('Invalid asset ID provided to confirmAssetDispose:', assetId);
        }
    }

    function confirmAssetMarkAsLost(assetId) {
        console.log('confirmAssetMarkAsLost called with assetId:', assetId, 'type:', typeof assetId);
        if (assetId && assetId !== null && assetId !== undefined && !isNaN(assetId)) {
            currentAssetId = assetId;
            debugCurrentAssetId('set in confirmAssetMarkAsLost');
            const markAsLostModal = document.getElementById('assetMarkAsLostModal');
            if (markAsLostModal) {
                // Clear any previous errors and reset the form
                hideError('lostError');
                const lostReasonElement = document.getElementById('lostReason');
                if (lostReasonElement) {
                    lostReasonElement.value = '';
                }
                
                markAsLostModal.classList.remove('hidden');
                // Ensure the textarea is accessible
                console.log('Lost reason element after modal open:', !!lostReasonElement);
                if (lostReasonElement) {
                    lostReasonElement.focus();
                    // Add real-time validation
                    lostReasonElement.addEventListener('input', function() {
                        if (this.value.trim()) {
                            hideError('lostError');
                        }
                    });
                }
            }
        } else {
            console.error('Invalid asset ID provided to confirmAssetMarkAsLost:', assetId);
        }
    }

    function confirmAssetMarkAsFound(assetId) {
        console.log('confirmAssetMarkAsFound called with assetId:', assetId, 'type:', typeof assetId);
        if (assetId && assetId !== null && assetId !== undefined && !isNaN(assetId)) {
            currentAssetId = assetId;
            debugCurrentAssetId('set in confirmAssetMarkAsFound');
            const markAsFoundModal = document.getElementById('assetMarkAsFoundModal');
            if (markAsFoundModal) {
                // Clear any previous errors and reset the form
                hideError('foundError');
                const foundLocationElement = document.getElementById('foundLocation');
                const newLocationForm = document.getElementById('newLocationForm');
                if (foundLocationElement) {
                    foundLocationElement.value = '';
                }
                if (newLocationForm) {
                    newLocationForm.classList.add('hidden');
                }
                
                markAsFoundModal.classList.remove('hidden');
                // Ensure the dropdown is accessible
                console.log('Found location element after modal open:', !!foundLocationElement);
                if (foundLocationElement) {
                    foundLocationElement.focus();
                    // Add real-time validation
                    foundLocationElement.addEventListener('change', function() {
                        if (this.value === 'new_location') {
                            // Show new location form
                            if (newLocationForm) {
                                newLocationForm.classList.remove('hidden');
                            }
                        } else {
                            // Hide new location form
                            if (newLocationForm) {
                                newLocationForm.classList.add('hidden');
                            }
                        }
                        if (this.value && this.value !== 'new_location') {
                            hideError('foundError');
                        }
                    });
                }
            }
        } else {
            console.error('Invalid asset ID provided to confirmAssetMarkAsFound:', assetId);
        }
    }

    function closeAssetDeleteModal() {
        debugCurrentAssetId('before closeAssetDeleteModal');
        const deleteModal = document.getElementById('assetDeleteModal');
        if (deleteModal) {
            deleteModal.classList.add('hidden');
        }
        currentAssetId = null;
        debugCurrentAssetId('after closeAssetDeleteModal');
    }

    function closeAssetDisposeModal() {
        debugCurrentAssetId('before closeAssetDisposeModal');
        const disposeModal = document.getElementById('assetDisposeModal');
        if (disposeModal) {
            disposeModal.classList.add('hidden');
        }
        const disposeReasonElement = document.getElementById('disposeReason');
        if (disposeReasonElement) {
            disposeReasonElement.value = ''; // Clear the reason field
        }
        document.body.style.overflow = 'auto'; // Restore page scrolling
        currentAssetId = null;
        debugCurrentAssetId('after closeAssetDisposeModal');
    }

    function closeAssetMarkAsLostModal() {
        debugCurrentAssetId('before closeAssetMarkAsLostModal');
        const markAsLostModal = document.getElementById('assetMarkAsLostModal');
        if (markAsLostModal) {
            markAsLostModal.classList.add('hidden');
        }
        const lostReasonElement = document.getElementById('lostReason');
        if (lostReasonElement) {
            lostReasonElement.value = ''; // Clear the reason field
        }
        document.body.style.overflow = 'auto'; // Restore page scrolling
        currentAssetId = null;
        debugCurrentAssetId('after closeAssetMarkAsLostModal');
    }

    function closeAssetMarkAsFoundModal() {
        debugCurrentAssetId('before closeAssetMarkAsFoundModal');
        const markAsFoundModal = document.getElementById('assetMarkAsFoundModal');
        if (markAsFoundModal) {
            markAsFoundModal.classList.add('hidden');
        }
        const foundLocationElement = document.getElementById('foundLocation');
        if (foundLocationElement) {
            foundLocationElement.value = ''; // Clear the location field
        }
        document.body.style.overflow = 'auto'; // Restore page scrolling
        currentAssetId = null;
        debugCurrentAssetId('after closeAssetMarkAsFoundModal');
    }


    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const columnFilterBtn = document.getElementById('columnFilterBtn');
        const columnFilterMenu = document.getElementById('columnFilterMenu');
        const fullListTable = document.querySelector('.full-list-table');
        const toggles = document.querySelectorAll('.column-toggle');

        // Add event listeners for modal buttons
        const deleteConfirmBtn = document.getElementById('assetDeleteConfirm');
        if (deleteConfirmBtn) {
            deleteConfirmBtn.addEventListener('click', function() {
                console.log('=== DELETE CONFIRM CLICKED ===');
                console.log('currentAssetId:', currentAssetId, 'type:', typeof currentAssetId);
                debugCurrentAssetId('in delete confirm click');
                
                if (currentAssetId && currentAssetId !== null && currentAssetId !== undefined) {
                    const passwordInput = document.getElementById('deletePassword');
                    const errorBox = document.getElementById('deleteError');
                    const password = passwordInput ? passwordInput.value : '';

                    // Require password
                    if (!password) {
                        if (errorBox) {
                            errorBox.textContent = 'Password is required to confirm deletion.';
                            errorBox.classList.remove('hidden');
                        }
                        return;
                    } else if (errorBox) {
                        errorBox.classList.add('hidden');
                        errorBox.textContent = '';
                    }
                    const form = document.createElement('form');
                    form.method = 'POST';
                    @if(auth()->user()->group_id === 4)
                        form.action = `/custodian/assets/${currentAssetId}`;
                    @else
                        form.action = `/assets/${currentAssetId}`;
                    @endif
                    
                    console.log('Delete form action:', form.action);
                    console.log('Form method:', form.method);

                    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
                    const csrfToken = csrfTokenElement ? csrfTokenElement.content : '';
                    console.log('CSRF Token:', csrfToken);
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';

                    const passwordField = document.createElement('input');
                    passwordField.type = 'hidden';
                    passwordField.name = 'password';
                    passwordField.value = password;

                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    form.appendChild(passwordField);
                    
                    console.log('Form data before submit:', {
                        action: form.action,
                        method: form.method,
                        csrfToken: csrfToken,
                        methodOverride: 'DELETE',
                        assetId: currentAssetId
                    });
                    
                    document.body.appendChild(form);
                    console.log('Submitting delete form...');
                    form.submit();
                } else {
                    console.error('Invalid currentAssetId for delete:', currentAssetId);
                }
            });
        }

        const disposeConfirmBtn = document.getElementById('assetDisposeConfirm');
        if (disposeConfirmBtn) {
            disposeConfirmBtn.addEventListener('click', function() {
                console.log('=== DISPOSE CONFIRM CLICKED ===');
                console.log('currentAssetId:', currentAssetId, 'type:', typeof currentAssetId);
                debugCurrentAssetId('in dispose confirm click');
                
                if (currentAssetId && currentAssetId !== null && currentAssetId !== undefined) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    @if(auth()->user()->group_id === 4)
                        form.action = `/custodian/assets/${currentAssetId}/dispose`;
                    @else
                        form.action = `/assets/${currentAssetId}/dispose`;
                    @endif

                    console.log('Dispose form action:', form.action);
                    console.log('Form method:', form.method);

                    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
                    const csrfToken = csrfTokenElement ? csrfTokenElement.content : '';
                    console.log('CSRF Token:', csrfToken);
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;

                    const redirectInput = document.createElement('input');
                    redirectInput.type = 'hidden';
                    redirectInput.name = 'redirect';
                    @if(auth()->user()->group_id === 4)
                        redirectInput.value = '{{ route("custodian.assets.index") }}';
                    @else
                        redirectInput.value = '{{ route("reports.disposal-history") }}';
                    @endif

                    // Add disposal reason input
                    const reasonInput = document.createElement('input');
                    reasonInput.type = 'hidden';
                    reasonInput.name = 'disposal_reason';
                    
                    // Wait a moment for the modal to be fully rendered
                    setTimeout(() => {
                        const disposeReasonElement = document.getElementById('disposeReason');
                        const disposalReason = disposeReasonElement ? disposeReasonElement.value.trim() : '';
                        reasonInput.value = disposalReason;
                        console.log('Disposal reason element found:', !!disposeReasonElement);
                        console.log('Disposal reason value:', disposalReason);
                        
                        // Validate that we have a reason
                        if (!disposalReason) {
                            console.error('No disposal reason provided!');
                            showError('disposeError', 'Please enter a reason for disposal.');
                            return;
                        }
                        
                        // Continue with form submission
                        form.appendChild(csrfInput);
                        form.appendChild(reasonInput);
                        form.appendChild(redirectInput);
                        
                        console.log('Form data before submit:', {
                            action: form.action,
                            method: form.method,
                            csrfToken: csrfToken,
                            disposalReason: disposalReason,
                            redirect: redirectInput.value,
                            assetId: currentAssetId
                        });
                        
                        document.body.appendChild(form);
                        console.log('Submitting dispose form...');
                        form.submit();
                    }, 100);
                } else {
                    console.error('Invalid currentAssetId for dispose:', currentAssetId);
                }
            });
        }

        const markAsLostConfirmBtn = document.getElementById('assetMarkAsLostConfirm');
        if (markAsLostConfirmBtn) {
            markAsLostConfirmBtn.addEventListener('click', function() {
                console.log('=== MARK AS LOST CONFIRM CLICKED ===');
                console.log('currentAssetId:', currentAssetId, 'type:', typeof currentAssetId);
                debugCurrentAssetId('in mark as lost confirm click');
                
                if (currentAssetId && currentAssetId !== null && currentAssetId !== undefined) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    @if(auth()->user()->group_id === 4)
                        form.action = `/custodian/assets/${currentAssetId}/mark-as-lost`;
                    @else
                        form.action = `/assets/${currentAssetId}/mark-as-lost`;
                    @endif

                    console.log('Mark as Lost form action:', form.action);
                    console.log('Form method:', form.method);

                    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
                    const csrfToken = csrfTokenElement ? csrfTokenElement.content : '';
                    console.log('CSRF Token:', csrfToken);
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;

                    const redirectInput = document.createElement('input');
                    redirectInput.type = 'hidden';
                    redirectInput.name = 'redirect';
                    @if(auth()->user()->group_id === 4)
                        redirectInput.value = '{{ route("custodian.assets.index") }}';
                    @else
                        redirectInput.value = '{{ route("assets.index") }}';
                    @endif

                    // Add lost reason input
                    const reasonInput = document.createElement('input');
                    reasonInput.type = 'hidden';
                    reasonInput.name = 'lost_reason';
                    
                    // Wait a moment for the modal to be fully rendered
                    setTimeout(() => {
                        const lostReasonElement = document.getElementById('lostReason');
                        const lostReason = lostReasonElement ? lostReasonElement.value.trim() : '';
                        reasonInput.value = lostReason;
                        console.log('Lost reason element found:', !!lostReasonElement);
                        console.log('Lost reason value:', lostReason);
                        
                        // Validate that we have a reason
                        if (!lostReason) {
                            console.error('No lost reason provided!');
                            showError('lostError', 'Please enter a reason for loss.');
                            return;
                        }
                        
                        // Continue with form submission
                        form.appendChild(csrfInput);
                        form.appendChild(reasonInput);
                        form.appendChild(redirectInput);
                        
                        console.log('Form data before submit:', {
                            action: form.action,
                            method: form.method,
                            csrfToken: csrfToken,
                            lostReason: lostReason,
                            redirect: redirectInput.value,
                            assetId: currentAssetId
                        });
                        
                        document.body.appendChild(form);
                        console.log('Submitting mark as lost form...');
                        form.submit();
                    }, 100);
                } else {
                    console.error('Invalid currentAssetId for mark as lost:', currentAssetId);
                }
            });
        }

        const markAsFoundConfirmBtn = document.getElementById('assetMarkAsFoundConfirm');
        if (markAsFoundConfirmBtn) {
            markAsFoundConfirmBtn.addEventListener('click', function() {
                console.log('=== MARK AS FOUND CONFIRM CLICKED ===');
                console.log('currentAssetId:', currentAssetId, 'type:', typeof currentAssetId);
                debugCurrentAssetId('in mark as found confirm click');
                
                if (currentAssetId && currentAssetId !== null && currentAssetId !== undefined) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    @if(auth()->user()->group_id === 4)
                        form.action = `/custodian/assets/${currentAssetId}/mark-as-found`;
                    @else
                        form.action = `/assets/${currentAssetId}/mark-as-found`;
                    @endif

                    console.log('Mark as Found form action:', form.action);
                    console.log('Form method:', form.method);

                    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
                    const csrfToken = csrfTokenElement ? csrfTokenElement.content : '';
                    console.log('CSRF Token:', csrfToken);
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;

                    const redirectInput = document.createElement('input');
                    redirectInput.type = 'hidden';
                    redirectInput.name = 'redirect';
                    @if(auth()->user()->group_id === 4)
                        redirectInput.value = '{{ route("custodian.assets.index") }}';
                    @else
                        redirectInput.value = '{{ route("assets.index") }}';
                    @endif

                    // Wait a moment for the modal to be fully rendered
                    setTimeout(() => {
                        const foundLocationElement = document.getElementById('foundLocation');
                        const foundLocationId = foundLocationElement ? foundLocationElement.value : '';
                        console.log('Found location element found:', !!foundLocationElement);
                        console.log('Found location value:', foundLocationId);
                        
                        // Check if user selected "Add New Location"
                        if (foundLocationId === 'new_location') {
                            // Validate new location form
                            const newBuilding = document.getElementById('newBuilding').value.trim();
                            const newFloor = document.getElementById('newFloor').value.trim();
                            const newRoom = document.getElementById('newRoom').value.trim();
                            
                            if (!newBuilding || !newFloor || !newRoom) {
                                showError('foundError', 'Please fill in all location fields (Building, Floor, Room).');
                                return;
                            }
                            
                            // Add new location data to form
                            const buildingInput = document.createElement('input');
                            buildingInput.type = 'hidden';
                            buildingInput.name = 'new_building';
                            buildingInput.value = newBuilding;
                            
                            const floorInput = document.createElement('input');
                            floorInput.type = 'hidden';
                            floorInput.name = 'new_floor';
                            floorInput.value = newFloor;
                            
                            const roomInput = document.createElement('input');
                            roomInput.type = 'hidden';
                            roomInput.name = 'new_room';
                            roomInput.value = newRoom;
                            
                            form.appendChild(csrfInput);
                            form.appendChild(buildingInput);
                            form.appendChild(floorInput);
                            form.appendChild(roomInput);
                            form.appendChild(redirectInput);
                            
                            console.log('Form data before submit (new location):', {
                                action: form.action,
                                method: form.method,
                                csrfToken: csrfToken,
                                newBuilding: newBuilding,
                                newFloor: newFloor,
                                newRoom: newRoom,
                                redirect: redirectInput.value,
                                assetId: currentAssetId
                            });
                        } else {
                            // Validate that we have a location
                            if (!foundLocationId) {
                                console.error('No found location provided!');
                                showError('foundError', 'Please select a location where the asset was found.');
                                return;
                            }
                            
                            // Add found location input
                            const locationInput = document.createElement('input');
                            locationInput.type = 'hidden';
                            locationInput.name = 'found_location_id';
                            locationInput.value = foundLocationId;
                            
                            form.appendChild(csrfInput);
                            form.appendChild(locationInput);
                            form.appendChild(redirectInput);
                            
                            console.log('Form data before submit (existing location):', {
                                action: form.action,
                                method: form.method,
                                csrfToken: csrfToken,
                                foundLocationId: foundLocationId,
                                redirect: redirectInput.value,
                                assetId: currentAssetId
                            });
                        }
                        
                        document.body.appendChild(form);
                        console.log('Submitting mark as found form...');
                        form.submit();
                    }, 100);
                } else {
                    console.error('Invalid currentAssetId for mark as found:', currentAssetId);
                }
            });
        }

        // Add event listeners for new location form
        const saveNewLocationBtn = document.getElementById('saveNewLocation');
        const cancelNewLocationBtn = document.getElementById('cancelNewLocation');
        
        if (saveNewLocationBtn) {
            saveNewLocationBtn.addEventListener('click', function() {
                // Validate new location form
                const newBuilding = document.getElementById('newBuilding').value.trim();
                const newFloor = document.getElementById('newFloor').value.trim();
                const newRoom = document.getElementById('newRoom').value.trim();
                
                if (!newBuilding || !newFloor || !newRoom) {
                    showError('foundError', 'Please fill in all location fields (Building, Floor, Room).');
                    return;
                }
                
                // Hide error if validation passes
                hideError('foundError');
                
                // You could add AJAX call here to save the location and get the ID
                // For now, we'll let the form submission handle it
                console.log('New location validated:', { newBuilding, newFloor, newRoom });
            });
        }
        
        if (cancelNewLocationBtn) {
            cancelNewLocationBtn.addEventListener('click', function() {
                // Hide new location form and reset dropdown
                const newLocationForm = document.getElementById('newLocationForm');
                const foundLocationElement = document.getElementById('foundLocation');
                
                if (newLocationForm) {
                    newLocationForm.classList.add('hidden');
                }
                if (foundLocationElement) {
                    foundLocationElement.value = '';
                }
                
                // Clear form fields
                document.getElementById('newBuilding').value = '';
                document.getElementById('newFloor').value = '';
                document.getElementById('newRoom').value = '';
                
                hideError('foundError');
            });
        }

        // Toggle menu
        columnFilterBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            columnFilterMenu.classList.toggle('hidden');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!columnFilterMenu.contains(e.target) && !columnFilterBtn.contains(e.target)) {
                columnFilterMenu.classList.add('hidden');
            }
        });

        // Select All functionality
        selectAll.addEventListener('change', function() {
            const isChecked = this.checked;
            toggles.forEach(toggle => {
                toggle.checked = isChecked;
                updateColumnVisibility(toggle);
            });
        });

        // Individual toggle functionality
        toggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                updateColumnVisibility(this);
                updateSelectAllState();
            });
        });

        function updateColumnVisibility(toggle) {
            const columnIndex = toggle.dataset.column;
            const isVisible = toggle.checked;

            // Toggle header
            const headers = fullListTable.querySelectorAll('th');
            if (headers[columnIndex]) {
                headers[columnIndex].style.display = isVisible ? '' : 'none';
            }

            // Toggle cells
            const cells = fullListTable.querySelectorAll(`td:nth-child(${parseInt(columnIndex) + 1})`);
            cells.forEach(cell => {
                cell.style.display = isVisible ? '' : 'none';
            });
        }

        function updateSelectAllState() {
            selectAll.checked = Array.from(toggles).every(toggle => toggle.checked);
        }
    });

</script>
@endsection