@extends('layouts.borrowing-app')

@section('content')
<div class="flex-1 bg-gradient-to-br from-gray-50 via-gray-50 to-red-50 min-h-screen" id="mainContent">
    <div class="max-w-7xl mx-auto px-6 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">All Assets</h1>
                    <p class="text-sm text-gray-600 mt-1">Complete inventory of all equipment regardless of status</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="px-3 py-1.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg text-sm font-semibold shadow-sm">
                        {{ $assets->total() }} Total
                    </span>
                    <a href="{{ route('borrowing.assets.create') }}" class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Asset
                    </a>
                    <a href="{{ route('borrowing.create') }}" class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        New Borrowing
                    </a>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 p-4 rounded-lg shadow-sm animate-fadeIn">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-green-800 font-medium text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-md p-5 mb-6 border border-gray-100">
            <form method="GET" action="{{ route('borrowing.assets.all') }}" class="space-y-4 md:space-y-0 md:flex md:items-end md:gap-3">
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

                <!-- Location Filter -->
                <div class="md:w-48">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Location</label>
                    <select name="location" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                        <option value="">All Locations</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ request('location') == $location->id ? 'selected' : '' }}>
                                {{ $location->building }} - {{ $location->floor }} - {{ $location->room_number }}
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
                        <option value="disposed" {{ request('status') == 'disposed' ? 'selected' : '' }}>Disposed</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-semibold rounded-lg transition-all shadow-sm hover:shadow-md">
                        Filter
                    </button>
                    <a href="{{ route('borrowing.assets.all') }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-all">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Assets Grid -->
        @if($assets->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mb-6">
                @foreach($assets as $asset)
                    <div class="group bg-white rounded-xl shadow-md hover:shadow-xl border-2 border-gray-200 hover:border-red-300 transition-all duration-300 overflow-hidden hover:-translate-y-1">
                        <!-- Image Container with Hover Effect -->
                        <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                            @if($asset->photo)
                                <!-- Asset Image (default) -->
                                <img src="{{ asset('storage/' . $asset->photo) }}" alt="{{ $asset->name }}" 
                                     class="w-full h-full object-cover transition-opacity duration-300 group-hover:opacity-0">
                                <!-- QR Code (shows on hover) -->
                                <div class="absolute inset-0 flex items-center justify-center bg-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="text-center p-4">
                                        @php
                                            $qrCode = new \Endroid\QrCode\QrCode($asset->serial_number, size: 140);
                                            $writer = new \Endroid\QrCode\Writer\PngWriter();
                                            $result = $writer->write($qrCode);
                                        @endphp
                                        <img src="{{ $result->getDataUri() }}" alt="QR Code" class="mx-auto">
                                        <p class="text-xs text-gray-600 mt-2 font-mono">{{ $asset->serial_number }}</p>
                                    </div>
                                </div>
                            @else
                                <!-- Only QR Code if no image -->
                                <div class="flex items-center justify-center h-full bg-white">
                                    <div class="text-center p-4">
                                        @php
                                            $qrCode = new \Endroid\QrCode\QrCode($asset->serial_number, size: 140);
                                            $writer = new \Endroid\QrCode\Writer\PngWriter();
                                            $result = $writer->write($qrCode);
                                        @endphp
                                        <img src="{{ $result->getDataUri() }}" alt="QR Code" class="mx-auto">
                                        <p class="text-xs text-gray-600 mt-2 font-mono">{{ $asset->serial_number }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Status Badge -->
                            <div class="absolute top-3 right-3">
                                @if($asset->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg">
                                        <span class="w-1.5 h-1.5 bg-white rounded-full mr-1.5 animate-pulse"></span>
                                        Available
                                    </span>
                                @elseif($asset->status === 'in_use')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg">
                                        <span class="w-1.5 h-1.5 bg-white rounded-full mr-1.5"></span>
                                        In Use
                                    </span>
                                @elseif($asset->status === 'missing')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg">
                                        <span class="w-1.5 h-1.5 bg-white rounded-full mr-1.5"></span>
                                        Missing
                                    </span>
                                @elseif($asset->status === 'disposed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-gray-500 to-gray-600 text-white shadow-lg">
                                        <span class="w-1.5 h-1.5 bg-white rounded-full mr-1.5"></span>
                                        Disposed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-gray-400 to-gray-500 text-white shadow-lg">
                                        <span class="w-1.5 h-1.5 bg-white rounded-full mr-1.5"></span>
                                        {{ ucfirst($asset->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-4">
                            <!-- Asset Name -->
                            <h3 class="font-bold text-gray-900 text-base mb-1 truncate" title="{{ $asset->name }}">
                                {{ $asset->name }}
                            </h3>
                            
                            @if($asset->model)
                                <p class="text-xs text-gray-500 mb-3">Model: {{ $asset->model }}</p>
                            @endif

                            <!-- Details Grid -->
                            <div class="space-y-2">
                                <!-- Category -->
                                <div class="flex items-start gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500 font-medium">Category</p>
                                        <p class="text-sm text-gray-900 truncate" title="{{ $asset->category->name ?? 'N/A' }}">
                                            {{ $asset->category->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="flex items-start gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500 font-medium">Location</p>
                                        <p class="text-sm text-gray-900 truncate" title="{{ $asset->location ? $asset->location->full_location : 'N/A' }}">
                                            {{ $asset->location ? $asset->location->full_location : 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Serial Number -->
                                <div class="flex items-start gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500 font-medium">Serial Number</p>
                                        <p class="text-sm text-gray-900 font-mono truncate" title="{{ $asset->serial_number }}">
                                            {{ $asset->serial_number }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    @if(request()->has('search') || request()->has('category') || request()->has('location') || request()->has('status'))
                        Try adjusting your filters to see more results.
                    @else
                        There are currently no assets in the system.
                    @endif
                </p>
                @if(request()->has('search') || request()->has('category') || request()->has('location') || request()->has('status'))
                    <a href="{{ route('borrowing.assets.all') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-semibold rounded-lg transition-all shadow-sm hover:shadow-md">
                        Clear Filters
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
