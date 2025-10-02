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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold">QR Code Generator</h1>
                        <p class="text-blue-100 text-sm md:text-base">Generate and export QR codes for your assets</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 md:gap-3">
                    <button id="previewBtn" class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-lg hover:bg-white/30 transition-all duration-200 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Preview PDF
                    </button>
                    <button id="exportBtn" class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition-all duration-200 flex items-center justify-center font-semibold">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Date Range Filter Section -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Date Range Filter</h3>
            <form method="GET" action="{{ route('qr.list') }}" class="flex flex-col sm:flex-row gap-4 items-end">
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
                @if(request('warranty'))
                    <input type="hidden" name="warranty" value="{{ request('warranty') }}">
                @endif
                
                <div class="flex-1">
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" 
                           id="date_from" 
                           name="date_from" 
                           value="{{ request('date_from') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                </div>
                
                <div class="flex-1">
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" 
                           id="date_to" 
                           name="date_to" 
                           value="{{ request('date_to') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" 
                            class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                        </svg>
                        Filter
                    </button>
                    
                    @if(request('date_from') || request('date_to'))
                        <a href="{{ route('qr.list') }}{{ request('search') ? '?search=' . request('search') : '' }}{{ request('status') ? (request('search') ? '&' : '?') . 'status=' . request('status') : '' }}{{ request('category') ? (request('search') || request('status') ? '&' : '?') . 'category=' . request('category') : '' }}{{ request('location') ? (request('search') || request('status') || request('category') ? '&' : '?') . 'location=' . request('location') : '' }}{{ request('warranty') ? (request('search') || request('status') || request('category') || request('location') ? '&' : '?') . 'warranty=' . request('warranty') : '' }}" 
                           class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Clear Date
                        </a>
                    @endif
                </div>
            </form>
            
            @if(request('date_from') || request('date_to'))
                <div class="mt-3 p-3 bg-red-50 rounded-md">
                    <p class="text-sm text-red-800">
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
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Additional Filters</h3>
            <form method="GET" action="{{ route('qr.list') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Status</option>
                        <option value="IN USE" {{ request('status') == 'IN USE' ? 'selected' : '' }}>IN USE</option>
                        <option value="UNDER REPAIR" {{ request('status') == 'UNDER REPAIR' ? 'selected' : '' }}>UNDER REPAIR</option>
                        <option value="DISPOSED" {{ request('status') == 'DISPOSED' ? 'selected' : '' }}>DISPOSED</option>
                        <option value="PULLED OUT" {{ request('status') == 'PULLED OUT' ? 'selected' : '' }}>PULLED OUT</option>
                        <option value="LOST" {{ request('status') == 'LOST' ? 'selected' : '' }}>LOST</option>
                    </select>
                </div>
                
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" id="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <select name="location" id="location" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Locations</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ request('location') == $location->id ? 'selected' : '' }}>
                                {{ $location->full_location }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="warranty" class="block text-sm font-medium text-gray-700 mb-1">Warranty</label>
                    <select name="warranty" id="warranty" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Warranties</option>
                        <option value="expiring_365" {{ request('warranty') == 'expiring_365' ? 'selected' : '' }}>Expiring within 365 days</option>
                        <option value="expiring_30" {{ request('warranty') == 'expiring_30' ? 'selected' : '' }}>Expiring within 30 days</option>
                        <option value="expired" {{ request('warranty') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                
                <div class="flex items-end gap-2">
                    <button type="submit" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                        </svg>
                        Apply Filters
                    </button>
                    
                    @if(request('status') || request('category') || request('location') || request('warranty'))
                        <a href="{{ route('qr.list') }}{{ request('search') ? '?search=' . request('search') : '' }}{{ request('date_from') ? (request('search') ? '&' : '?') . 'date_from=' . request('date_from') : '' }}{{ request('date_to') ? (request('search') || request('date_from') ? '&' : '?') . 'date_to=' . request('date_to') : '' }}" 
                           class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Clear Filters
                        </a>
                    @endif
                </div>
            </form>
            
            @if(request('status') || request('category') || request('location') || request('warranty'))
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
                        @if(request('warranty'))
                            @if(request('status') || request('category') || request('location')) | @endif
                            Warranty: {{ ucfirst(str_replace('_', ' ', request('warranty'))) }}
                        @endif
                        ({{ $assets->total() }} assets found)
                    </p>
                </div>
            @endif
        </div>

        <!-- Search Section -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Search Assets</h3>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <div class="relative w-full flex gap-2">
                    <input type="text" id="searchInput" placeholder="Search assets..." class="flex-1 min-w-0 sm:min-w-[400px] px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" value="{{ request('search') }}">
                    <button id="searchButton" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center whitespace-nowrap">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search
                    </button>
                </div>
                @if(request('search'))
                    <a href="{{ route('qr.list') }}{{ request('date_from') ? '?date_from=' . request('date_from') : '' }}{{ request('date_to') ? (request('date_from') ? '&' : '?') . 'date_to=' . request('date_to') : '' }}{{ request('status') ? (request('date_from') || request('date_to') ? '&' : '?') . 'status=' . request('status') : '' }}{{ request('category') ? (request('date_from') || request('date_to') || request('status') ? '&' : '?') . 'category=' . request('category') : '' }}{{ request('location') ? (request('date_from') || request('date_to') || request('status') || request('category') ? '&' : '?') . 'location=' . request('location') : '' }}{{ request('warranty') ? (request('date_from') || request('date_to') || request('status') || request('category') || request('location') ? '&' : '?') . 'warranty=' . request('warranty') : '' }}" 
                       class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear Search
                    </a>
                @endif
            </div>
            @if(request('search'))
                <div class="mt-3 p-3 bg-blue-50 rounded-md">
                    <p class="text-sm text-blue-800">
                        <strong>Searching for:</strong> "{{ request('search') }}" ({{ $assets->total() }} assets found)
                    </p>
                </div>
            @endif
        </div>

        <!-- Assets Table Section -->
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Select Assets for QR Generation</h3>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <label class="flex items-center">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Select All ({{ $assets->total() }} items)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="selectPage" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Select Page ({{ $assets->count() }} items)</span>
                        </label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500" id="selectedCount">0 selected</span>
                        <button id="clearSelection" class="text-sm text-red-600 hover:text-red-800 hidden">Clear All</button>
                    </div>
                </div>
            </div>

            @if($assets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="w-12 px-6 py-4 text-left">
                                    <input type="checkbox" id="selectAllHeader" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="w-20 px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QR Code</th>
                                <th class="w-20 px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($assets as $asset)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <input type="checkbox" name="selected_items[]" value="{{ $asset->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 asset-checkbox">
                                </td>
                                <td class="px-6 py-4">
                                    @if($asset->qr_code)
                                        <img src="{{ asset('storage/' . $asset->qr_code) }}" 
                                             alt="QR Code" 
                                             class="w-16 h-16 cursor-pointer hover:opacity-75 transition-opacity"
                                             onclick="openImageModal('{{ asset('storage/' . $asset->qr_code) }}')"
                                        >
                                    @else
                                        <div class="w-16 h-16 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center">
                                            <span class="text-gray-400 text-xs">No QR</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($asset->photo)
                                        <img src="{{ asset('storage/' . $asset->photo) }}" 
                                             alt="Asset Photo" 
                                             class="w-16 h-16 object-cover rounded cursor-pointer hover:opacity-75 transition-opacity"
                                             onclick="openImageModal('{{ asset('storage/' . $asset->photo) }}')"
                                        >
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                            <span class="text-gray-500 text-xs">No Photo</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $asset->name }}</div>
                                    @if($asset->category)
                                        <div class="text-sm text-gray-500">{{ $asset->category->name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-mono font-semibold text-gray-900">{{ $asset->serial_number }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($asset->purchase_date)->format('M d, Y') }}</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-6">
                    {{ $assets->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No assets found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request('date_from') || request('date_to'))
                            Try adjusting your date range filter.
                        @else
                            No assets are available for QR code generation.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <img id="enlargedImage" src="" alt="Enlarged Image" class="max-h-[80vh] max-w-full object-contain rounded-lg shadow-2xl">
        <button onclick="closeImageModal()" 
                class="absolute -top-4 -right-4 bg-white rounded-full p-3 shadow-lg hover:bg-gray-100 transition-colors duration-200">
            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<!-- Export and Preview Forms -->
<form id="exportForm" action="{{ route('qrcodes.export') }}" method="POST" class="hidden">
    @csrf
    @if(request('date_from'))
        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
    @endif
    @if(request('date_to'))
        <input type="hidden" name="date_to" value="{{ request('date_to') }}">
    @endif
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
    @if(request('warranty'))
        <input type="hidden" name="warranty" value="{{ request('warranty') }}">
    @endif
    <input type="hidden" name="selected_items" id="selectedItemsInput">
</form>

<form id="previewForm" action="{{ route('qrcodes.preview') }}" method="POST" target="_blank" class="hidden">
    @csrf
    @if(request('date_from'))
        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
    @endif
    @if(request('date_to'))
        <input type="hidden" name="date_to" value="{{ request('date_to') }}">
    @endif
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
    @if(request('warranty'))
        <input type="hidden" name="warranty" value="{{ request('warranty') }}">
    @endif
    <input type="hidden" name="selected_items" id="previewItemsInput">
</form>

<script>
    // Global selected items storage (persists across pagination)
    let globalSelectedItems = new Set();
    const totalItems = {{ $assets->total() }};
    let selectAllMode = false;

    // Initialize from localStorage if exists
    const storedSelection = localStorage.getItem('qr_selected_items');
    const storedSelectAllMode = localStorage.getItem('qr_select_all_mode');
    
    if (storedSelection) {
        globalSelectedItems = new Set(JSON.parse(storedSelection));
    }
    
    if (storedSelectAllMode === 'true') {
        selectAllMode = true;
    }

    // Select All functionality (works across all pages)
    document.getElementById('selectAll').addEventListener('change', function() {
        if (this.checked) {
            // Select all items across all pages
            selectAllMode = true;
            globalSelectedItems.clear();
            
            // Add all current page items
            const checkboxes = document.getElementsByName('selected_items[]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
                globalSelectedItems.add(checkbox.value);
            });
            
            localStorage.setItem('qr_select_all_mode', 'true');
        } else {
            // Deselect all
            selectAllMode = false;
            globalSelectedItems.clear();
            
            const checkboxes = document.getElementsByName('selected_items[]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            localStorage.setItem('qr_select_all_mode', 'false');
        }
        
        updateSelectedCount();
        updateSelectAllState();
        saveSelection();
    });

    document.getElementById('selectAllHeader').addEventListener('change', function() {
        document.getElementById('selectAll').checked = this.checked;
        document.getElementById('selectAll').dispatchEvent(new Event('change'));
    });

    // Select Page functionality (selects only current page items but persists through pagination)
    document.getElementById('selectPage').addEventListener('change', function() {
        const checkboxes = document.getElementsByName('selected_items[]');
        
        if (this.checked) {
            // Select all items on current page
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
                globalSelectedItems.add(checkbox.value);
            });
        } else {
            // Deselect all items on current page
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
                globalSelectedItems.delete(checkbox.value);
            });
        }
        
        // Exit select all mode if it was active
        if (selectAllMode) {
            selectAllMode = false;
            localStorage.setItem('qr_select_all_mode', 'false');
        }
        
        updateSelectedCount();
        updateSelectAllState();
        saveSelection();
    });

    // Clear selection button
    document.getElementById('clearSelection').addEventListener('click', function() {
        selectAllMode = false;
        globalSelectedItems.clear();
        
        const checkboxes = document.getElementsByName('selected_items[]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        document.getElementById('selectAll').checked = false;
        document.getElementById('selectAllHeader').checked = false;
        document.getElementById('selectPage').checked = false;
        
        updateSelectedCount();
        updateSelectAllState();
        saveSelection();
        localStorage.setItem('qr_select_all_mode', 'false');
    });

    // Individual checkbox change
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('asset-checkbox')) {
            const itemId = e.target.value;
            
            if (e.target.checked) {
                globalSelectedItems.add(itemId);
            } else {
                globalSelectedItems.delete(itemId);
                selectAllMode = false; // If user unchecks an item, we're no longer in "select all" mode
                localStorage.setItem('qr_select_all_mode', 'false');
            }
            
            updateSelectedCount();
            updateSelectAllState();
            saveSelection();
        }
    });

    function updateSelectedCount() {
        let count;
        if (selectAllMode) {
            count = totalItems;
        } else {
            count = globalSelectedItems.size;
        }
        
        document.getElementById('selectedCount').textContent = `${count} selected`;
        
        // Show/hide clear button
        const clearButton = document.getElementById('clearSelection');
        if (count > 0) {
            clearButton.classList.remove('hidden');
        } else {
            clearButton.classList.add('hidden');
        }
    }

    function updateSelectAllState() {
        const selectAll = document.getElementById('selectAll');
        const selectAllHeader = document.getElementById('selectAllHeader');
        const selectPage = document.getElementById('selectPage');
        const currentPageCheckboxes = document.getElementsByName('selected_items[]');
        const currentPageChecked = Array.from(currentPageCheckboxes).filter(cb => cb.checked);
        
        if (selectAllMode) {
            selectAll.checked = true;
            selectAllHeader.checked = true;
            selectPage.checked = true; // All pages selected means current page is also selected
        } else {
            // Update Select All checkbox
            if (currentPageChecked.length === 0) {
                selectAll.checked = false;
                selectAllHeader.checked = false;
            } else if (currentPageChecked.length === currentPageCheckboxes.length && globalSelectedItems.size === totalItems) {
                selectAll.checked = true;
                selectAllHeader.checked = true;
            } else {
                selectAll.checked = false;
                selectAllHeader.checked = false;
            }
            
            // Update Select Page checkbox
            if (currentPageChecked.length === currentPageCheckboxes.length && currentPageCheckboxes.length > 0) {
                selectPage.checked = true;
            } else {
                selectPage.checked = false;
            }
        }
    }

    function saveSelection() {
        localStorage.setItem('qr_selected_items', JSON.stringify([...globalSelectedItems]));
    }

    // Initialize page state
    function initializePageState() {
        const checkboxes = document.getElementsByName('selected_items[]');
        
        if (selectAllMode) {
            // If in select all mode, check all current page items
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
                globalSelectedItems.add(checkbox.value);
            });
        } else {
            // Check items that are in our global selection
            checkboxes.forEach(checkbox => {
                if (globalSelectedItems.has(checkbox.value)) {
                    checkbox.checked = true;
                }
            });
        }
        
        updateSelectedCount();
        updateSelectAllState();
    }

    // Preview functionality
    document.getElementById('previewBtn').addEventListener('click', function() {
        let selectedItems;
        
        if (selectAllMode) {
            // If in select all mode, we need to tell the backend to select all items matching current filters
            selectedItems = 'all';
        } else {
            selectedItems = [...globalSelectedItems];
        }

        if (selectedItems.length === 0 && selectedItems !== 'all') {
            showNotification('Please select items to preview', 'warning');
            return;
        }

        document.getElementById('previewItemsInput').value = JSON.stringify(selectedItems);
        document.getElementById('previewForm').submit();
    });

    // Export functionality
    document.getElementById('exportBtn').addEventListener('click', function() {
        let selectedItems;
        
        if (selectAllMode) {
            // If in select all mode, we need to tell the backend to select all items matching current filters
            selectedItems = 'all';
        } else {
            selectedItems = [...globalSelectedItems];
        }

        if (selectedItems.length === 0 && selectedItems !== 'all') {
            showNotification('Please select items to export', 'warning');
            return;
        }

        document.getElementById('selectedItemsInput').value = JSON.stringify(selectedItems);
        document.getElementById('exportForm').submit();
    });

    // Image modal functionality
    function openImageModal(imageSrc) {
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

    // Close modal when clicking outside the image
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });

    // Notification function
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
            type === 'warning' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 
            type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' : 
            'bg-blue-100 text-blue-800 border border-blue-200'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                ${message}
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Initialize page state
    initializePageState();
    
    // Search functionality
    document.getElementById('searchButton').addEventListener('click', function() {
        const searchValue = document.getElementById('searchInput').value;
        const currentUrl = new URL(window.location.href);
        
        if (searchValue.trim()) {
            currentUrl.searchParams.set('search', searchValue);
        } else {
            currentUrl.searchParams.delete('search');
        }
        
        window.location.href = currentUrl.toString();
    });
    
    // Allow search on Enter key
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('searchButton').click();
        }
    });
</script>
@endsection