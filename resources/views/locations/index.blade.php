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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Location Management</h1>
                        <p class="text-red-100 text-sm md:text-lg">Manage and organize asset locations</p>
                    </div>
                </div>
                <a href="{{ route('locations.create') }}" 
                   class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white p-3 md:p-4 rounded-full shadow-lg transition-all duration-200 transform hover:scale-105 flex items-center justify-center">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-4 md:mb-6 p-3 md:p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm md:text-base">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error') || $errors->has('error'))
        <div class="mb-4 md:mb-6 p-3 md:p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-center">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm md:text-base">{{ session('error') ?? $errors->first('error') }}</span>
        </div>
    @endif

    <!-- Building Management Section -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h2 class="text-lg md:text-xl font-semibold text-gray-900">Building Management</h2>
            </div>
            <button onclick="openAddBuildingModal()" class="inline-flex items-center px-3 py-2 bg-blue-800 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Building
            </button>
        </div>

        @if($buildings->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($buildings as $building)
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div class="bg-gray-100 p-2 rounded-lg mr-3">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $building->name }}</div>
                            <div class="text-xs text-gray-500">{{ $building->floors->count() }} floors</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-1">
                        @if($building->is_active)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Active
                        </span>
                        @else
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Inactive
                        </span>
                        @endif
                        <a href="{{ route('buildings.show', $building) }}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200" title="View Details">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        <button onclick="openEditBuildingModal({{ $building->id }}, '{{ $building->name }}', '{{ $building->description }}', {{ $building->is_active ? 'true' : 'false' }})" class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200" title="Edit Building">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        
                        @php
                            $buildingHasReferences = $building->hasAssets() || $building->hasMaintenanceRecords();
                        @endphp
                        
                        @if($buildingHasReferences)
                            <div class="relative group">
                                <button type="button" 
                                        class="text-gray-400 bg-gray-100 p-1 rounded cursor-not-allowed" 
                                        title="Cannot delete building - has associated records"
                                        disabled>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block z-20">
                                    <div class="bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                        Building has associated records
                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-800"></div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <button type="button" onclick="openDeleteModal('building', {{ $building->id }}, '{{ $building->name }}', '{{ route('buildings.destroy', $building) }}')" class="text-red-600 hover:text-red-900 transition-colors duration-200" title="Delete Building">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
                
                <!-- Floors List -->
                @if($building->floors->count() > 0)
                <div class="space-y-2">
                    @foreach($building->floors->take(3) as $floor)
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                            </svg>
                            <span class="text-gray-600">{{ $floor->name }}</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            @if($floor->is_active)
                            <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                            @else
                            <span class="w-2 h-2 bg-red-400 rounded-full"></span>
                            @endif
                            <button onclick="openEditFloorModal({{ $building->id }}, {{ $floor->id }}, '{{ $floor->name }}', {{ $floor->floor_number ?? 'null' }}, '{{ $floor->description }}', {{ $floor->is_active ? 'true' : 'false' }})" class="text-indigo-500 hover:text-indigo-700">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            
                            @php
                                $floorHasReferences = $floor->hasAssets() || $floor->hasMaintenanceRecords();
                            @endphp
                            
                            @if($floorHasReferences)
                                <div class="relative group">
                                    <button type="button" 
                                            class="text-gray-400 bg-gray-100 p-0.5 rounded cursor-not-allowed" 
                                            title="Cannot delete floor - has associated records"
                                            disabled>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-1 hidden group-hover:block z-20">
                                        <div class="bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                            Floor has associated records
                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-800"></div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <button type="button" onclick="openDeleteModal('floor', {{ $floor->id }}, '{{ $floor->name }}', '{{ route('buildings.floors.destroy', [$building, $floor]) }}')" class="text-red-500 hover:text-red-700">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @if($building->floors->count() > 3)
                    <div class="text-xs text-gray-500 text-center">+{{ $building->floors->count() - 3 }} more floors</div>
                    @endif
                </div>
                @else
                <div class="text-xs text-gray-500 text-center py-2">No floors added yet</div>
                @endif
                
                <!-- Add Floors Button -->
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <button onclick="openBulkFloorModal({{ $building->id }})" class="w-full text-xs text-green-600 hover:text-green-800 transition-colors duration-200 flex items-center justify-center py-2 px-3 rounded-lg hover:bg-green-50 group">
                        <svg class="w-3 h-3 mr-1 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Add Floors
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No buildings</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new building.</p>
        </div>
        @endif
    </div>

    <!-- Search and Filter Bar -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6 mb-6">
        <form method="GET" action="{{ route('locations.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="relative flex-grow">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="   Search locations..." 
       class="pl-12 pr-4 py-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 text-sm md:text-base">
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-lg flex items-center transition-colors duration-200 text-sm md:text-base font-medium">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('locations.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-3 px-6 rounded-lg flex items-center transition-colors duration-200 text-sm md:text-base font-medium">
                        <svg class="w-4 h-4 md:w-5 md:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Locations Statistics -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6 mb-6">
        <h2 class="text-lg md:text-xl font-semibold text-gray-900 mb-4 md:mb-6 flex items-center">
            <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Locations Overview
        </h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 md:p-6 rounded-xl border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-blue-700 mb-1">Total Locations</p>
                        <p class="text-2xl md:text-3xl font-bold text-blue-900">{{ count($locations) }}</p>
                    </div>
                    <div class="bg-blue-200 p-2 md:p-3 rounded-full">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 md:p-6 rounded-xl border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-green-700 mb-1">Total Assets</p>
                        <p class="text-2xl md:text-3xl font-bold text-green-900">{{ $locations->sum('assets_count') }}</p>
                    </div>
                    <div class="bg-green-200 p-2 md:p-3 rounded-full">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 p-4 md:p-6 rounded-xl border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-yellow-700 mb-1">Buildings</p>
                        <p class="text-2xl md:text-3xl font-bold text-yellow-900">{{ $locations->pluck('building')->unique()->count() }}</p>
                    </div>
                    <div class="bg-yellow-200 p-2 md:p-3 rounded-full">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-4 md:p-6 rounded-xl border border-purple-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-purple-700 mb-1">Floors</p>
                        <p class="text-2xl md:text-3xl font-bold text-purple-900">{{ $locations->pluck('floor')->unique()->count() }}</p>
                    </div>
                    <div class="bg-purple-200 p-2 md:p-3 rounded-full">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Locations List -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
        <div class="flex justify-between items-center mb-4 md:mb-6">
            <h2 class="text-lg md:text-xl font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                All Locations
            </h2>
            <div class="flex items-center space-x-2">
                <span class="text-xs md:text-sm text-gray-500">Total: {{ count($locations) }} locations</span>
            </div>
        </div>

        <!-- Table view for desktop -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Location</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Building</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Floor</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room Number</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assets Count</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider sticky right-0 bg-gray-50 z-10">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($locations as $location)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="bg-red-100 p-2 rounded-full mr-3">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div class="text-sm font-medium text-gray-900">{{ $location->full_location }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $location->building }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $location->floor }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $location->room_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $location->assets_count }} {{ Str::plural('asset', $location->assets_count) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium sticky right-0 bg-white z-10">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('locations.show', $location->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 bg-blue-100 hover:bg-blue-200 p-2 rounded-lg transition-colors duration-200" title="View Location">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('locations.edit', $location->id) }}" 
                                   class="text-amber-600 hover:text-amber-800 bg-amber-100 hover:bg-amber-200 p-2 rounded-lg transition-colors duration-200" title="Edit Location">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                
                                @php
                                    $locationHasReferences = $location->assets()->exists() || $location->maintenances()->exists();
                                @endphp
                                
                                @if($locationHasReferences)
                                    <div class="relative group">
                                        <button type="button" 
                                                class="text-gray-400 bg-gray-100 p-2 rounded-lg cursor-not-allowed" 
                                                title="Cannot delete location - has associated records"
                                                disabled>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block z-20">
                                            <div class="bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                                Location has associated records
                                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-800"></div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <button type="button" 
                                            class="text-red-600 hover:text-red-800 bg-red-100 hover:bg-red-200 p-2 rounded-lg transition-colors duration-200"
                                            onclick="openDeleteModal('location', {{ $location->id }}, '{{ $location->full_location }}', '{{ route('locations.destroy', $location) }}')" title="Delete Location">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <div class="bg-gray-100 rounded-full p-4 w-16 h-16 mb-4 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <p class="text-lg font-medium text-gray-900 mb-2">No locations found</p>
                                <p class="text-gray-500 mb-4">Get started by creating your first location</p>
                                <a href="{{ route('locations.create') }}" 
                                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Add New Location
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Card view for mobile with improved design -->
        <div class="md:hidden space-y-3 md:space-y-4">
            @forelse($locations as $location)
                <div class="bg-gray-50 rounded-xl p-4 md:p-6 hover:bg-gray-100 transition-colors duration-200 border border-gray-200">
                    <div class="flex justify-between items-start mb-3 md:mb-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center mb-2">
                                <div class="bg-red-100 p-2 rounded-full mr-3">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-gray-900 text-base md:text-lg truncate">{{ $location->full_location }}</h3>
                            </div>
                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span class="truncate">{{ $location->building }}</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 flex-shrink-0 ml-2">
                            {{ $location->assets_count }} {{ Str::plural('asset', $location->assets_count) }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-white p-3 rounded-lg border border-gray-200">
                            <div class="flex items-center text-xs text-gray-500 mb-1">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                </svg>
                                Floor
                            </div>
                            <p class="text-sm font-medium text-gray-900">{{ $location->floor }}</p>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-gray-200">
                            <div class="flex items-center text-xs text-gray-500 mb-1">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" />
                                </svg>
                                Room
                            </div>
                            <p class="text-sm font-medium text-gray-900">{{ $location->room_number }}</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-end items-center pt-2 space-x-2">
                        <a href="{{ route('locations.show', $location->id) }}" 
                           class="text-blue-600 hover:text-blue-800 bg-blue-100 hover:bg-blue-200 p-2 rounded-lg transition-colors duration-200" title="View Location">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        <a href="{{ route('locations.edit', $location->id) }}" 
                           class="text-amber-600 hover:text-amber-800 bg-amber-100 hover:bg-amber-200 p-2 rounded-lg transition-colors duration-200" title="Edit Location">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        
                        @php
                            $locationHasReferences = $location->assets()->exists() || $location->maintenances()->exists();
                        @endphp
                        
                        @if($locationHasReferences)
                            <div class="relative group">
                                <button type="button" 
                                        class="text-gray-400 bg-gray-100 p-2 rounded-lg cursor-not-allowed" 
                                        title="Cannot delete location - has associated records"
                                        disabled>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                <div class="absolute bottom-full right-0 mb-2 hidden group-hover:block z-20">
                                    <div class="bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                        Location has associated records
                                        <div class="absolute top-full right-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-800"></div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <button type="button" 
                                    class="text-red-600 hover:text-red-800 bg-red-100 hover:bg-red-200 p-2 rounded-lg transition-colors duration-200"
                                    onclick="openDeleteModal('location', {{ $location->id }}, '{{ $location->full_location }}', '{{ route('locations.destroy', $location) }}')" title="Delete Location">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8 md:py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                    <div class="bg-gray-100 rounded-full p-3 md:p-4 w-16 h-16 md:w-20 md:h-20 mx-auto mb-3 md:mb-4 flex items-center justify-center">
                        <svg class="h-8 w-8 md:h-10 md:w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">No locations found</h3>
                    <p class="text-xs md:text-sm text-gray-500 mb-4">Get started by creating your first location</p>
                    <a href="{{ route('locations.create') }}" 
                       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                        <svg class="w-4 h-4 md:w-5 md:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add New Location
                    </a>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Pagination if needed -->
    @if(isset($locations) && method_exists($locations, 'links') && $locations->hasPages())
    <div class="mt-6">
        {{ $locations->links() }}
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <div class="mt-2 text-center">
                <h3 id="deleteModalTitle" class="text-lg font-medium text-gray-900 mb-2">Confirm Deletion</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Are you sure you want to delete <span id="itemNameToDelete" class="font-semibold"></span>?
                </p>
                <div id="relatedRecordsInfo" class="hidden mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <div class="text-sm text-yellow-800">
                            <p class="font-medium">Warning: This location has related records</p>
                            <div id="relatedRecordsList" class="mt-1 text-xs"></div>
                        </div>
                    </div>
                </div>
                <p id="deleteWarningText" class="text-xs text-red-600 mb-4">
                    This action cannot be undone.
                </p>
            </div>
            <div class="flex justify-center space-x-3">
                <button id="confirmDeleteBtn" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 transition-colors duration-200">
                    Delete
                </button>
                <button onclick="hideDeleteModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Floor Addition Modal -->
<div id="bulkFloorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-lg bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <div class="flex items-center">
                <div class="bg-green-100 p-2 rounded-full mr-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Add Multiple Floors</h3>
                    <p class="text-sm text-gray-600">Add floors to <span id="selectedBuildingName" class="font-medium text-red-800"></span></p>
                </div>
            </div>
            <button type="button" onclick="closeBulkFloorModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="py-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Floor Names</label>
                <p class="text-xs text-gray-500 mb-3">Add one floor name per line. You can use formats like "1st Floor", "Ground Floor", "B1", etc.</p>
            </div>
            
            <!-- Scrollable Floor Input Area -->
            <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4 bg-gray-50">
                <div id="bulkFloorInputs" class="space-y-3">
                    <!-- Dynamic floor inputs will be added here -->
                </div>
                
                <!-- Add More Button -->
                <button type="button" id="addMoreBulkFloors" class="mt-3 w-full py-2 px-4 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-green-400 hover:text-green-600 transition-colors duration-200 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Another Floor
                </button>
            </div>
            
            <!-- Quick Add Options -->
            <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <h4 class="text-sm font-medium text-blue-800 mb-2">Quick Add Options</h4>
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="quick-add-bulk-btn px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs rounded-full transition-colors" data-floors="Ground Floor,1st Floor,2nd Floor,3rd Floor">
                        4-Story Building
                    </button>
                    <button type="button" class="quick-add-bulk-btn px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs rounded-full transition-colors" data-floors="B1,Ground Floor,1st Floor,2nd Floor,3rd Floor">
                        5-Story with Basement
                    </button>
                    <button type="button" class="quick-add-bulk-btn px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs rounded-full transition-colors" data-floors="1,2,3,4,5,6,7,8,9,10">
                        10-Story Numbered
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
            <div class="text-sm text-gray-500">
                <span id="bulkFloorCount">0</span> floors ready to add
            </div>
            <div class="flex space-x-3">
                <button type="button" onclick="closeBulkFloorModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-lg transition-colors duration-200">
                    Cancel
                </button>
                <button type="button" id="saveBulkFloors" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Add Floors
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let itemToDelete = null;
    let deleteAction = null;
    let deleteType = null;

    function openDeleteModal(type, itemId, itemName, actionUrl = null) {
        itemToDelete = itemId;
        deleteType = type;
        deleteAction = actionUrl;
        
        // Update modal content based on type
        const titleElement = document.getElementById('deleteModalTitle');
        const nameElement = document.getElementById('itemNameToDelete');
        const warningElement = document.getElementById('deleteWarningText');
        
        nameElement.textContent = itemName;
        
        switch(type) {
            case 'location':
                titleElement.textContent = 'Delete Location';
                warningElement.textContent = 'This action cannot be undone.';
                // Check for related records for locations
                checkLocationRelatedRecords(itemId);
                break;
            case 'building':
                titleElement.textContent = 'Delete Building';
                warningElement.textContent = 'This will also delete all floors in this building. This action cannot be undone.';
                break;
            case 'floor':
                titleElement.textContent = 'Delete Floor';
                warningElement.textContent = 'This action cannot be undone.';
                break;
            default:
                titleElement.textContent = 'Confirm Deletion';
                warningElement.textContent = 'This action cannot be undone.';
        }
        
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        // Hide related records info
        document.getElementById('relatedRecordsInfo').classList.add('hidden');
        itemToDelete = null;
        deleteAction = null;
        deleteType = null;
    }

    function checkLocationRelatedRecords(locationId) {
        // Reset related records info
        const relatedRecordsInfo = document.getElementById('relatedRecordsInfo');
        const relatedRecordsList = document.getElementById('relatedRecordsList');
        relatedRecordsInfo.classList.add('hidden');
        
        // Make AJAX call to check for related records
        fetch(`/locations/${locationId}/check-relations`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.hasRelations) {
                let relationsList = [];
                if (data.assetsCount > 0) {
                    relationsList.push(`${data.assetsCount} asset(s)`);
                }
                if (data.maintenancesCount > 0) {
                    relationsList.push(`${data.maintenancesCount} maintenance record(s)`);
                }
                
                if (relationsList.length > 0) {
                    relatedRecordsList.textContent = `This location has: ${relationsList.join(', ')}`;
                    relatedRecordsInfo.classList.remove('hidden');
                }
            }
        })
        .catch(error => {
            console.log('Could not check related records:', error);
        });
    }

    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
        if (itemToDelete && deleteAction) {
            // Create and submit the delete form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = deleteAction;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
        });
    }

    // Close modal when clicking outside
    const deleteModalForClick = document.getElementById('deleteModal');
    if (deleteModalForClick) {
        deleteModalForClick.addEventListener('click', function(e) {
            if (e.target === this) {
                hideDeleteModal();
            }
        });
    }

    // ESC key to close delete modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('deleteModal').classList.contains('hidden')) {
            hideDeleteModal();
        }
    });

    // Building Management Functions
    function openAddBuildingModal() {
        document.getElementById('addBuildingModal').classList.remove('hidden');
    }

    function closeAddBuildingModal() {
        document.getElementById('addBuildingModal').classList.add('hidden');
        document.getElementById('addBuildingModal').querySelector('form').reset();
    }

    function openEditBuildingModal(id, name, description, isActive) {
        document.getElementById('editBuildingForm').action = `/buildings/${id}`;
        document.getElementById('edit_building_name').value = name;
        document.getElementById('edit_building_description').value = description || '';
        document.getElementById('edit_building_active').checked = isActive;
        document.getElementById('editBuildingModal').classList.remove('hidden');
    }

    function closeEditBuildingModal() {
        document.getElementById('editBuildingModal').classList.add('hidden');
    }


    function openEditFloorModal(buildingId, floorId, name, floorNumber, description, isActive) {
        document.getElementById('editFloorForm').action = `/buildings/${buildingId}/floors/${floorId}`;
        document.getElementById('edit_floor_name').value = name;
        document.getElementById('edit_floor_number').value = floorNumber || '';
        document.getElementById('edit_floor_description').value = description || '';
        document.getElementById('edit_floor_active').checked = isActive;
        document.getElementById('editFloorModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        // Focus on the first input
        setTimeout(() => {
            document.getElementById('edit_floor_name').focus();
        }, 100);
    }

    function closeEditFloorModal() {
        document.getElementById('editFloorModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }


    // Bulk Floor Management
    const bulkFloorModal = document.getElementById('bulkFloorModal');
    const bulkFloorInputs = document.getElementById('bulkFloorInputs');
    const bulkFloorCount = document.getElementById('bulkFloorCount');
    const addMoreBulkFloors = document.getElementById('addMoreBulkFloors');
    const saveBulkFloors = document.getElementById('saveBulkFloors');
    const selectedBuildingName = document.getElementById('selectedBuildingName');

    // Only initialize bulk floor functionality if all elements exist
    if (bulkFloorModal && bulkFloorInputs && bulkFloorCount && addMoreBulkFloors && saveBulkFloors && selectedBuildingName) {

    let currentBulkFloorIndex = 0;
    let selectedBuildingId = null;

    function openBulkFloorModal(buildingId) {
        selectedBuildingId = buildingId;
        
        // Find building name
        const buildingCard = document.querySelector(`[onclick*="openBulkFloorModal(${buildingId})"]`).closest('.border');
        const buildingName = buildingCard.querySelector('.text-sm.font-medium.text-gray-900').textContent;
        selectedBuildingName.textContent = buildingName;
        
        bulkFloorModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Initialize with one floor input
        clearBulkFloorInputs();
        addBulkFloorInput();
    }

    function closeBulkFloorModal() {
        bulkFloorModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        clearBulkFloorInputs();
        selectedBuildingId = null;
    }

    // Close modal when clicking outside
    bulkFloorModal.addEventListener('click', function(e) {
        if (e.target === bulkFloorModal) {
            closeBulkFloorModal();
        }
    });

    // Add more floors button
    addMoreBulkFloors.addEventListener('click', function() {
        addBulkFloorInput();
    });

    function addBulkFloorInput(value = '') {
        const floorDiv = document.createElement('div');
        floorDiv.className = 'flex items-center gap-2 bulk-floor-input-row';
        floorDiv.innerHTML = `
            <div class="flex-1">
                <input type="text" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" 
                       placeholder="e.g., Ground Floor, 1st Floor, B1" 
                       value="${value}">
            </div>
            <button type="button" class="remove-bulk-floor-btn p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Remove floor">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        `;
        
        bulkFloorInputs.appendChild(floorDiv);
        currentBulkFloorIndex++;
        
        // Add remove functionality
        const removeBtn = floorDiv.querySelector('.remove-bulk-floor-btn');
        removeBtn.addEventListener('click', function() {
            floorDiv.remove();
            updateBulkFloorCount();
        });
        
        // Add input event listener for real-time count update
        const input = floorDiv.querySelector('input');
        input.addEventListener('input', updateBulkFloorCount);
        
        updateBulkFloorCount();
        
        // Focus on the new input
        input.focus();
    }

    function clearBulkFloorInputs() {
        bulkFloorInputs.innerHTML = '';
        currentBulkFloorIndex = 0;
        updateBulkFloorCount();
    }

    function updateBulkFloorCount() {
        const inputs = bulkFloorInputs.querySelectorAll('input');
        const validFloors = Array.from(inputs).filter(input => input.value.trim() !== '').length;
        bulkFloorCount.textContent = validFloors;
    }

    // Quick add buttons
    document.querySelectorAll('.quick-add-bulk-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const floors = this.dataset.floors.split(',');
            clearBulkFloorInputs();
            floors.forEach(floor => addBulkFloorInput(floor.trim()));
        });
    });

    // Save bulk floors functionality
    saveBulkFloors.addEventListener('click', function() {
        if (!selectedBuildingId) {
            alert('No building selected');
            return;
        }

        const inputs = bulkFloorInputs.querySelectorAll('input');
        const newFloors = Array.from(inputs)
            .map(input => input.value.trim())
            .filter(value => value !== '');
        
        if (newFloors.length === 0) {
            alert('Please add at least one floor');
            return;
        }
        
        // Create form to submit bulk floors
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/buildings/${selectedBuildingId}/floors/bulk`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Add floor names as hidden inputs
        newFloors.forEach((floor, index) => {
            const floorInput = document.createElement('input');
            floorInput.type = 'hidden';
            floorInput.name = `floors[${index}][name]`;
            floorInput.value = floor;
            form.appendChild(floorInput);
        });
        
        document.body.appendChild(form);
        form.submit();
    });

    } // End of bulk floor functionality conditional block

    // Enhanced modal management
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Close bulk floor modal
            if (bulkFloorModal && !bulkFloorModal.classList.contains('hidden')) {
                closeBulkFloorModal();
            }
            // Close add building modal
            else if (!document.getElementById('addBuildingModal').classList.contains('hidden')) {
                closeAddBuildingModal();
            }
            // Close edit building modal
            else if (!document.getElementById('editBuildingModal').classList.contains('hidden')) {
                closeEditBuildingModal();
            }
            // Close edit floor modal
            else if (!document.getElementById('editFloorModal').classList.contains('hidden')) {
                closeEditFloorModal();
            }
            // Close delete modal
            else if (!document.getElementById('deleteModal').classList.contains('hidden')) {
                hideDeleteModal();
            }
        }
    });

    // Enhanced building modal functionality
    function openAddBuildingModal() {
        document.getElementById('addBuildingModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        // Focus on the first input
        setTimeout(() => {
            document.getElementById('building_name').focus();
        }, 100);
    }

    function closeAddBuildingModal() {
        document.getElementById('addBuildingModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        // Reset form
        document.getElementById('addBuildingModal').querySelector('form').reset();
        // Reset toggle to checked state
        document.getElementById('building_active').checked = true;
    }

    function openEditBuildingModal(buildingId, name, description, isActive) {
        document.getElementById('editBuildingForm').action = `/buildings/${buildingId}`;
        document.getElementById('edit_building_name').value = name;
        document.getElementById('edit_building_description').value = description || '';
        document.getElementById('edit_building_active').checked = isActive;
        document.getElementById('editBuildingModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        // Focus on the first input
        setTimeout(() => {
            document.getElementById('edit_building_name').focus();
        }, 100);
    }

    function closeEditBuildingModal() {
        document.getElementById('editBuildingModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modals when clicking outside (with null checks)
    const addBuildingModal = document.getElementById('addBuildingModal');
    if (addBuildingModal) {
        addBuildingModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddBuildingModal();
            }
        });
    }

    const editBuildingModal = document.getElementById('editBuildingModal');
    if (editBuildingModal) {
        editBuildingModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditBuildingModal();
            }
        });
    }

    const editFloorModal = document.getElementById('editFloorModal');
    if (editFloorModal) {
        editFloorModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditFloorModal();
            }
        });
    }

    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideDeleteModal();
            }
        });
    }
</script>

<!-- Add Building Modal -->
<div id="addBuildingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add New Building</h3>
                <button onclick="closeAddBuildingModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <!-- Modal Body -->
        <form action="{{ route('buildings.store') }}" method="POST" class="py-4">
            @csrf
            <div class="space-y-5">
                <!-- Building Name -->
                <div>
                    <label for="building_name" class="block text-sm font-medium text-gray-700 mb-2">Building Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <input type="text" name="name" id="building_name" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="e.g., Main Building, Tower A" required>
                    </div>
                </div>
                
                <!-- Description -->
                <div>
                    <label for="building_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <div class="relative">
                        <div class="absolute top-3 left-3 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <textarea name="description" id="building_description" rows="3" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none" placeholder="Optional description about the building..."></textarea>
                    </div>
                </div>
                
                <!-- Status Toggle -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <label for="building_active" class="text-sm font-medium text-gray-700">Active Building</label>
                                <p class="text-xs text-gray-500">Building will be available for use immediately</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" id="building_active" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 mt-6">
                <div class="text-sm text-gray-500">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    You can add floors after creating the building
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeAddBuildingModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Create Building
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Building Modal -->
<div id="editBuildingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-lg shadow-lg rounded-xl bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <div class="flex items-center">
                <div class="bg-amber-100 p-2 rounded-full mr-3">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Edit Building</h3>
                    <p class="text-sm text-gray-600">Update building information</p>
                </div>
            </div>
            <button type="button" onclick="closeEditBuildingModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <!-- Modal Body -->
        <form id="editBuildingForm" method="POST" class="py-4">
            @csrf
            @method('PUT')
            <div class="space-y-5">
                <!-- Building Name -->
                <div>
                    <label for="edit_building_name" class="block text-sm font-medium text-gray-700 mb-2">Building Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <input type="text" name="name" id="edit_building_name" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors" required>
                    </div>
                </div>
                
                <!-- Description -->
                <div>
                    <label for="edit_building_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <div class="relative">
                        <div class="absolute top-3 left-3 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <textarea name="description" id="edit_building_description" rows="3" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors resize-none"></textarea>
                    </div>
                </div>
                
                <!-- Status Toggle -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <label for="edit_building_active" class="text-sm font-medium text-gray-700">Active Building</label>
                                <p class="text-xs text-gray-500">Building availability status</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" id="edit_building_active" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end pt-6 border-t border-gray-200 mt-6">
                <div class="flex space-x-3">
                    <button type="button" onclick="closeEditBuildingModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Building
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Edit Floor Modal -->
<div id="editFloorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-lg shadow-lg rounded-xl bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <div class="flex items-center">
                <div class="bg-purple-100 p-2 rounded-full mr-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Edit Floor</h3>
                    <p class="text-sm text-gray-600">Update floor information</p>
                </div>
            </div>
            <button type="button" onclick="closeEditFloorModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <!-- Modal Body -->
        <form id="editFloorForm" method="POST" class="py-4">
            @csrf
            @method('PUT')
            <div class="space-y-5">
                <!-- Floor Name -->
                <div>
                    <label for="edit_floor_name" class="block text-sm font-medium text-gray-700 mb-2">Floor Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                            </svg>
                        </div>
                        <input type="text" name="name" id="edit_floor_name" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" placeholder="e.g., Ground Floor, 1st Floor" required>
                    </div>
                </div>
                
                <!-- Floor Number -->
                <div>
                    <label for="edit_floor_number" class="block text-sm font-medium text-gray-700 mb-2">Floor Number</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                        </div>
                        <input type="number" name="floor_number" id="edit_floor_number" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" placeholder="e.g., 0, 1, 2" min="0">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Optional numeric identifier for the floor</p>
                </div>
                
                <!-- Description -->
                <div>
                    <label for="edit_floor_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <div class="relative">
                        <div class="absolute top-3 left-3 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <textarea name="description" id="edit_floor_description" rows="3" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors resize-none" placeholder="Optional description about the floor..."></textarea>
                    </div>
                </div>
                
                <!-- Status Toggle -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <label for="edit_floor_active" class="text-sm font-medium text-gray-700">Active Floor</label>
                                <p class="text-xs text-gray-500">Floor availability status</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" id="edit_floor_active" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end pt-6 border-t border-gray-200 mt-6">
                <div class="flex space-x-3">
                    <button type="button" onclick="closeEditFloorModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Floor
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-xl bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
        </div>
        
        <!-- Modal Body -->
        <div class="text-center">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirm Deletion</h3>
            <p class="text-sm text-gray-600 mb-4">
                Are you sure you want to delete <span id="itemNameToDelete" class="font-semibold text-gray-900"></span>?
            </p>
            <div class="bg-red-50 p-3 rounded-lg border border-red-200 mb-6">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <p class="text-xs text-red-700">This action cannot be undone.</p>
                </div>
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex justify-center space-x-3">
            <button type="button" onclick="hideDeleteModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-lg transition-colors duration-200">
                Cancel
            </button>
            <button id="confirmDeleteBtn" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete
            </button>
        </div>
    </div>
</div>

@endsection
