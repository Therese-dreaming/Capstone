@extends('layouts.app')

@section('content')
<div class="flex-1">
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
                <!-- Add search input -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:space-x-4 w-full md:w-auto">
                    <div class="relative w-full sm:w-auto">
                        <input type="text" id="searchInput" placeholder="Search by Serial Number" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                    </div>
                    <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                        <a href="{{ route('assets.create') }}" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 text-center">
                            Add New Asset
                        </a>
                        <a href="{{ route('reports.procurement-history') }}" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 text-center">
                            Procurement History
                        </a>
                        <a href="{{ route('reports.disposal-history') }}" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 text-center">
                            Disposal History
                        </a>
                        <button onclick="openFullList()" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Divider Line -->
            <div class="border-b-2 border-red-800 mb-4 md:mb-6"></div>

            <!-- Mobile View Cards -->
            <div class="md:hidden space-y-4 mb-4">
                @foreach($assets as $asset)
                <div class="border rounded-lg p-4 bg-white shadow-sm">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-bold text-gray-900">{{ $asset->name ?? '' }}</h3>
                            <p class="text-sm text-gray-600">{{ $asset->serial_number ?? 'N/A' }}</p>
                        </div>
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
                                    @case('UPGRADE')
                                        bg-blue-100 text-blue-800
                                        @break
                                    @case('PENDING DEPLOYMENT')
                                        bg-purple-100 text-purple-800
                                        @break
                                    @case('PULLED OUT')
                                        bg-orange-100 text-orange-800
                                        @break
                                    @default
                                        bg-gray-100 text-gray-800
                                @endswitch">
                            {{ $asset->status ?? 'N/A' }}
                        </span>
                    </div>
                    
                    <div class="flex gap-3 mb-3">
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
                    
                    <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                        <div>
                            <p class="text-gray-500">Category:</p>
                            <p class="font-medium">{{ $asset->category->name ?? '' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Price:</p>
                            <p class="font-medium">₱{{ number_format($asset->purchase_price ?? 0, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Location:</p>
                            <p class="font-medium">{{ $asset->location ?? '' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-2">
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
                        <button onclick="confirmDelete({{ $asset->id }})" class="bg-red-600 text-white p-1.5 rounded hover:bg-red-700 tooltip" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                        <button onclick="confirmDispose({{ $asset->id }})" class="bg-gray-600 text-white p-1.5 rounded hover:bg-gray-700 tooltip" title="Dispose">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Desktop Asset List Preview -->
            <div class="hidden md:block relative overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Photo</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">QR Code</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serial Number</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assets as $asset)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($asset->photo)
                                <img src="{{ asset('storage/' . $asset->photo) }}" alt="Asset Photo" class="w-20 h-20 object-cover rounded cursor-pointer hover:opacity-75 transition-opacity" onclick="openImageModal('{{ asset('storage/' . $asset->photo) }}')">
                                @else
                                <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                    <span class="text-gray-500">No Photo</span>
                                </div>
                                @endif
                            </td>
                            <!-- Rest of the desktop table remains the same -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($asset->qr_code)
                                <img src="{{ asset('storage/' . $asset->qr_code) }}" alt="QR Code" class="w-20 h-20 cursor-pointer hover:opacity-75 transition-opacity" onclick="openImageModal('{{ asset('storage/' . $asset->qr_code) }}')">
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">{{ $asset->serial_number ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->name ?? '' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->category->name ?? '' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                                                @case('UPGRADE')
                                                    bg-blue-100 text-blue-800
                                                    @break
                                                @case('PENDING DEPLOYMENT')
                                                    bg-purple-100 text-purple-800
                                                    @break
                                                @case('PULLED OUT')
                                                    bg-orange-100 text-orange-800
                                                    @break
                                                @default
                                                    bg-gray-100 text-gray-800
                                            @endswitch">
                                    {{ $asset->status ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($asset->purchase_price ?? 0, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->location ?? '' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex space-x-2">
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
                                    <button onclick="confirmDelete({{ $asset->id }})" class="bg-red-600 text-white p-1.5 rounded hover:bg-red-700 tooltip" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                    <button onclick="confirmDispose({{ $asset->id }})" class="bg-gray-600 text-white p-1.5 rounded hover:bg-gray-700 tooltip" title="Dispose">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
            <input type="text" id="modalSearchInput" placeholder="Search by Serial Number" class="px-4 py-2 border border-gray-300 rounded-md w-full focus:outline-none focus:ring-1 focus:ring-red-500">
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
                            <span>Purchase Price</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="6" class="column-toggle">
                            <span>Category</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="7" class="column-toggle">
                            <span>Location</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="8" class="column-toggle">
                            <span>Status</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="9" class="column-toggle">
                            <span>Model</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="10" class="column-toggle">
                            <span>Specification</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="11" class="column-toggle">
                            <span>Vendor</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="12" class="column-toggle">
                            <span>Purchase Date</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="13" class="column-toggle">
                            <span>Warranty Period</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="14" class="column-toggle">
                            <span>Lifespan (Yrs)</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" checked data-column="15" class="column-toggle">
                            <span>End of Lifespan Date</span>
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">#</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">Photo</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">QR Code</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Asset Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Serial Number</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Purchase Price</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Location</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Model</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Specification</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Vendor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Purchase Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Warranty Period</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Lifespan (Yrs)</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">End of Lifespan Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($assets as $asset)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                        <td class="px-6 py-4 whitespace-nowrap">
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
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->name ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-bold">{{ $asset->serial_number ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($asset->purchase_price ?? 0, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->category->name ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->location ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                                        @case('UPGRADE')
                                            bg-blue-100 text-blue-800
                                            @break
                                        @case('PENDING DEPLOYMENT')
                                            bg-purple-100 text-purple-800
                                            @break
                                        @case('PULLED OUT')
                                            bg-orange-100 text-orange-800
                                            @break
                                        @default
                                            bg-gray-100 text-gray-800
                                    @endswitch">
                                {{ $asset->status ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->model ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->specification ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->vendor ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($asset->purchase_date)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($asset->warranty_period)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
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
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" style="z-index: 60;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Delete Asset</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Are you sure you want to delete this asset? This action cannot be undone.</p>
            </div>
            <div class="flex justify-center gap-4 mt-4">
                <button id="deleteConfirm" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                    Delete
                </button>
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Dispose Confirmation Modal -->
<div id="disposeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" style="z-index: 60;">
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
                </div>
            </div>
            <div class="flex justify-center gap-4 mt-4">
                <button id="disposeConfirm" class="px-4 py-2 bg-yellow-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-300">
                    Dispose
                </button>
                <button onclick="closeDisposeModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
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
        event.stopPropagation(); // Add this line
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
    });

    let currentAssetId = null;

    function confirmDelete(assetId) {
        currentAssetId = assetId;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function confirmDispose(assetId) {
        currentAssetId = assetId;
        document.getElementById('disposeModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        currentAssetId = null;
    }

    function closeDisposeModal() {
        document.getElementById('disposeModal').classList.add('hidden');
        document.getElementById('disposeReason').value = ''; // Clear the reason field
        document.body.style.overflow = 'auto'; // Restore page scrolling
        currentAssetId = null;
    }

    document.getElementById('deleteConfirm').addEventListener('click', function() {
        if (currentAssetId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/assets/${currentAssetId}`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';

            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }
    });

    document.getElementById('disposeConfirm').addEventListener('click', function() {
        if (currentAssetId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/assets/${currentAssetId}/dispose`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;

            // Add disposal reason input
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'disposal_reason';
            reasonInput.value = document.getElementById('disposeReason').value;

            const redirectInput = document.createElement('input');
            redirectInput.type = 'hidden';
            redirectInput.name = 'redirect';
            redirectInput.value = '{{ route("reports.disposal-history") }}';

            form.appendChild(csrfInput);
            form.appendChild(reasonInput); // Add the reason input to the form
            form.appendChild(redirectInput);
            document.body.appendChild(form);
            form.submit();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const modalSearchInput = document.getElementById('modalSearchInput');

        // Check for search parameter in URL
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search');
        
        if (searchParam) {
            // Set search input values
            if (searchInput) searchInput.value = searchParam;
            if (modalSearchInput) modalSearchInput.value = searchParam;
            
            // Trigger the search
            filterTables(searchParam);
        }

        function filterTables(searchValue) {
            searchValue = searchValue.toLowerCase();

            // Search in preview table
            const previewTable = document.querySelector('table:not(.full-list-table)');
            const previewRows = previewTable.querySelectorAll('tbody tr');
            previewRows.forEach(row => {
                const serialNumber = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                row.style.display = serialNumber.includes(searchValue) ? '' : 'none';
            });

            // Search in full list table
            const fullListTable = document.querySelector('.full-list-table');
            const fullListRows = fullListTable.querySelectorAll('tbody tr');
            fullListRows.forEach(row => {
                const serialNumber = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
                row.style.display = serialNumber.includes(searchValue) ? '' : 'none';
            });
            
            // Search in mobile cards
            const mobileCards = document.querySelectorAll('.md\\:hidden > div');
            mobileCards.forEach(card => {
                const serialNumber = card.querySelector('p.text-sm.text-gray-600').textContent.toLowerCase();
                card.style.display = serialNumber.includes(searchValue) ? '' : 'none';
            });
        }

        // Main search input event listener
        searchInput.addEventListener('input', function() {
            filterTables(this.value);
            if (modalSearchInput) modalSearchInput.value = this.value;
        });

        // Modal search input event listener
        modalSearchInput.addEventListener('input', function() {
            filterTables(this.value);
            if (searchInput) searchInput.value = this.value;
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const columnFilterBtn = document.getElementById('columnFilterBtn');
        const columnFilterMenu = document.getElementById('columnFilterMenu');
        const fullListTable = document.querySelector('.full-list-table');
        const toggles = document.querySelectorAll('.column-toggle');

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
</div>