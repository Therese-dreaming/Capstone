@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('locations.index') }}" class="inline-flex items-center text-gray-600 hover:text-red-800 transition-colors duration-200 group">
            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            <span class="font-medium">Back to Locations</span>
        </a>
    </div>

    <!-- Page Header with Background Design -->
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                        <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">{{ $building->name }}</h1>
                        <p class="text-red-100 text-sm md:text-lg">Building Management & Floor Organization</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button onclick="openBulkFloorModal()" class="bg-green-600/90 backdrop-blur-sm hover:bg-green-700/90 text-white p-3 md:p-4 rounded-full shadow-lg transition-all duration-200 transform hover:scale-105 flex items-center justify-center group" title="Add Floors">
                        <svg class="w-5 h-5 md:w-6 md:h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </button>
                    <button onclick="openEditBuildingModal()" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white p-3 md:p-4 rounded-full shadow-lg transition-all duration-200 transform hover:scale-105 flex items-center justify-center" title="Edit Building">
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button onclick="openDeleteBuildingModal()" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white p-3 md:p-4 rounded-full shadow-lg transition-all duration-200 transform hover:scale-105 flex items-center justify-center" title="Delete Building">
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
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

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Building Information Card -->
        <div class="xl:col-span-1">
            <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden">
                <!-- Header with gradient background -->
                <div class="bg-gradient-to-r from-red-800 to-red-900 p-3 text-white">
                    <div class="flex items-center">
                        <div class="bg-white/20 p-2 rounded-lg mr-3 backdrop-blur-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-white">Building Details</h2>
                            <p class="text-red-100 text-xs">Complete building information</p>
                        </div>
                    </div>
                </div>
                
                <!-- Content section -->
                <div class="p-3">
                    <!-- Building Name -->
                    <div class="mb-3">
                        <div class="flex items-center mb-1">
                            <svg class="w-3 h-3 text-red-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Building Name</label>
                        </div>
                        <div class="bg-gray-50 p-2 rounded border-l-2 border-red-500">
                            <div class="text-sm font-bold text-gray-900">{{ $building->name }}</div>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-3">
                        <div class="flex items-center mb-1">
                            <svg class="w-3 h-3 text-red-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Description</label>
                        </div>
                        <div class="bg-gray-50 p-2 rounded border-l-2 border-blue-500">
                            <div class="text-xs text-gray-800 leading-relaxed">
                                {{ $building->description ?: 'No description provided' }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div class="mb-3">
                        <div class="flex items-center mb-1">
                            <svg class="w-3 h-3 text-red-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</label>
                        </div>
                        <div class="bg-gray-50 p-2 rounded border-l-2 border-green-500">
                            @if($building->is_active)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1 animate-pulse"></div>
                                Active
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                <div class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1"></div>
                                Inactive
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Statistics Grid -->
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-2 rounded border border-blue-200">
                            <div class="flex items-center">
                                <div class="bg-blue-200 p-1 rounded mr-2">
                                    <svg class="w-3 h-3 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-lg font-bold text-blue-900">{{ $building->floors->count() }}</div>
                                    <div class="text-xs text-blue-700 font-medium">Total Floors</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-br from-green-50 to-green-100 p-2 rounded border border-green-200">
                            <div class="flex items-center">
                                <div class="bg-green-200 p-1 rounded mr-2">
                                    <svg class="w-3 h-3 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-lg font-bold text-green-900">{{ $building->floors->where('is_active', true)->count() }}</div>
                                    <div class="text-xs text-green-700 font-medium">Active Floors</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Timestamps -->
                    <div class="space-y-2">
                        <div class="bg-gray-50 p-2 rounded border-l-2 border-gray-400">
                            <div class="flex items-center mb-1">
                                <svg class="w-3 h-3 text-gray-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Created</label>
                            </div>
                            <div class="text-xs text-gray-800 font-medium">{{ $building->created_at->format('M d, Y \a\t g:i A') }}</div>
                        </div>
                        
                        <div class="bg-gray-50 p-2 rounded border-l-2 border-gray-400">
                            <div class="flex items-center mb-1">
                                <svg class="w-3 h-3 text-gray-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Last Updated</label>
                            </div>
                            <div class="text-xs text-gray-800 font-medium">{{ $building->updated_at->format('M d, Y \a\t g:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floors Management Card -->
        <div class="xl:col-span-2">
            <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="bg-red-100 p-2 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                            </svg>
                        </div>
                        <h2 class="text-lg md:text-xl font-semibold text-gray-900">Floor Management</h2>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        {{ $building->floors->count() }} floors
                    </span>
                </div>

                @if($building->floors->count() > 0)
                <div class="space-y-3">
                    @foreach($building->floors->sortBy('floor_number') as $floor)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-gray-100 p-2 rounded-lg mr-3">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $floor->name }}</div>
                                    @if($floor->floor_number !== null)
                                    <div class="text-xs text-gray-500">Floor #{{ $floor->floor_number }}</div>
                                    @endif
                                    @if($floor->description)
                                    <div class="text-xs text-gray-500 mt-1">{{ Str::limit($floor->description, 60) }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($floor->is_active)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Inactive
                                </span>
                                @endif
                                <button onclick="openEditFloorModal({{ $floor->id }}, '{{ $floor->name }}', {{ $floor->floor_number ?? 'null' }}, '{{ $floor->description }}', {{ $floor->is_active ? 'true' : 'false' }})" class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button onclick="openDeleteFloorModal({{ $floor->id }}, '{{ $floor->name }}')" class="text-red-600 hover:text-red-900 transition-colors duration-200" title="Delete Floor">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No floors</h3>
                    <p class="mt-1 text-sm text-gray-500">This building doesn't have any floors yet.</p>
                    <div class="mt-4">
                        <button onclick="openBulkFloorModal()" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Add Floors
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>


<!-- Edit Floor Modal -->
<div id="editFloorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Floor</h3>
                <button onclick="closeEditFloorModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="editFloorForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label for="edit_floor_name" class="block text-sm font-medium text-gray-700 mb-1">Floor Name</label>
                        <input type="text" name="name" id="edit_floor_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500" required>
                    </div>
                    <div>
                        <label for="edit_floor_number" class="block text-sm font-medium text-gray-700 mb-1">Floor Number</label>
                        <input type="number" name="floor_number" id="edit_floor_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500" min="0">
                    </div>
                    <div>
                        <label for="edit_floor_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="edit_floor_description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" id="edit_floor_active" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeEditFloorModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-medium rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-800 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Update Floor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Building Modal -->
<div id="editBuildingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Building</h3>
                <button onclick="closeEditBuildingModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('buildings.update', $building) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label for="edit_building_name" class="block text-sm font-medium text-gray-700 mb-1">Building Name</label>
                        <input type="text" name="name" id="edit_building_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500" value="{{ $building->name }}" required>
                    </div>
                    <div>
                        <label for="edit_building_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="edit_building_description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">{{ $building->description }}</textarea>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" id="edit_building_active" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50" {{ $building->is_active ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeEditBuildingModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-medium rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-800 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Update Building
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Building Modal -->
<div id="deleteBuildingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <div class="mt-2 text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Building</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Are you sure you want to delete <span class="font-semibold">{{ $building->name }}</span>?
                </p>
                <p class="text-xs text-red-600 mb-4">
                    This will also delete all floors in this building. This action cannot be undone.
                </p>
            </div>
            <div class="flex justify-center space-x-3">
                <form action="{{ route('buildings.destroy', $building) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 transition-colors duration-200">
                        Delete
                    </button>
                </form>
                <button onclick="closeDeleteBuildingModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Floor Modal -->
<div id="deleteFloorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <div class="mt-2 text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Floor</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Are you sure you want to delete <span id="floorNameToDelete" class="font-semibold"></span>?
                </p>
                <p class="text-xs text-red-600 mb-4">
                    This action cannot be undone.
                </p>
            </div>
            <div class="flex justify-center space-x-3">
                <button id="confirmDeleteFloorBtn" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 transition-colors duration-200">
                    Delete
                </button>
                <button onclick="closeDeleteFloorModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
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
                    <p class="text-sm text-gray-600">Add floors to <span class="font-medium text-red-800">{{ $building->name }}</span></p>
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

function openEditFloorModal(floorId, name, floorNumber, description, isActive) {
    document.getElementById('editFloorForm').action = `/buildings/{{ $building->id }}/floors/${floorId}`;
    document.getElementById('edit_floor_name').value = name;
    document.getElementById('edit_floor_number').value = floorNumber || '';
    document.getElementById('edit_floor_description').value = description || '';
    document.getElementById('edit_floor_active').checked = isActive;
    document.getElementById('editFloorModal').classList.remove('hidden');
}

function closeEditFloorModal() {
    document.getElementById('editFloorModal').classList.add('hidden');
}

function openEditBuildingModal() {
    document.getElementById('editBuildingModal').classList.remove('hidden');
}

function closeEditBuildingModal() {
    document.getElementById('editBuildingModal').classList.add('hidden');
}

function openDeleteBuildingModal() {
    document.getElementById('deleteBuildingModal').classList.remove('hidden');
}

function closeDeleteBuildingModal() {
    document.getElementById('deleteBuildingModal').classList.add('hidden');
}

let floorToDelete = null;

function openDeleteFloorModal(floorId, floorName) {
    floorToDelete = floorId;
    document.getElementById('floorNameToDelete').textContent = floorName;
    document.getElementById('deleteFloorModal').classList.remove('hidden');
}

function closeDeleteFloorModal() {
    document.getElementById('deleteFloorModal').classList.add('hidden');
    floorToDelete = null;
}

// Handle floor deletion
document.getElementById('confirmDeleteFloorBtn').addEventListener('click', function() {
    if (floorToDelete) {
        // Create and submit the delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/buildings/{{ $building->id }}/floors/${floorToDelete}`;
        
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

// Bulk Floor Management
const bulkFloorModal = document.getElementById('bulkFloorModal');
const bulkFloorInputs = document.getElementById('bulkFloorInputs');
const bulkFloorCount = document.getElementById('bulkFloorCount');
const addMoreBulkFloors = document.getElementById('addMoreBulkFloors');
const saveBulkFloors = document.getElementById('saveBulkFloors');

let currentBulkFloorIndex = 0;

function openBulkFloorModal() {
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
    const inputs = bulkFloorInputs.querySelectorAll('input');
    const newFloors = Array.from(inputs)
        .map(input => input.value.trim())
        .filter(value => value !== '');
    
    if (newFloors.length === 0) {
        alert('Please add at least one floor');
        return;
    }
    
    // Get existing floor names for duplicate check
    const existingFloors = @json($building->floors->pluck('name')->toArray());
    const duplicates = newFloors.filter(floor => existingFloors.includes(floor));
    
    if (duplicates.length > 0) {
        if (!confirm(`The following floors already exist: ${duplicates.join(', ')}. Do you want to continue adding the remaining floors?`)) {
            return;
        }
    }
    
    // Create form to submit bulk floors
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("buildings.floors.bulk-store", $building) }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Add floor names as hidden inputs
    newFloors.forEach((floor, index) => {
        if (!existingFloors.includes(floor)) {
            const floorInput = document.createElement('input');
            floorInput.type = 'hidden';
            floorInput.name = `floors[${index}][name]`;
            floorInput.value = floor;
            form.appendChild(floorInput);
        }
    });
    
    document.body.appendChild(form);
    form.submit();
});

// Escape key to close modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !bulkFloorModal.classList.contains('hidden')) {
        closeBulkFloorModal();
    }
});
</script>
@endsection

