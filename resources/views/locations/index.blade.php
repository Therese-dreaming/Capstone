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
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
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
                                <button type="button" 
                                        class="text-red-600 hover:text-red-800 bg-red-100 hover:bg-red-200 p-2 rounded-lg transition-colors duration-200"
                                        onclick="openDeleteModal({{ $location->id }}, '{{ $location->full_location }}')" title="Delete Location">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
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
                        <button type="button" 
                                class="text-red-600 hover:text-red-800 bg-red-100 hover:bg-red-200 p-2 rounded-lg transition-colors duration-200"
                                onclick="openDeleteModal({{ $location->id }}, '{{ $location->full_location }}')" title="Delete Location">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
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
                <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Location</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Are you sure you want to delete <span id="locationNameToDelete" class="font-semibold"></span>?
                </p>
                <p class="text-xs text-red-600 mb-4">
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

<script>
    let locationToDelete = null;

    function openDeleteModal(locationId, locationName) {
        locationToDelete = locationId;
        document.getElementById('locationNameToDelete').textContent = locationName;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function hideDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        locationToDelete = null;
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (locationToDelete) {
            // Create and submit the delete form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("locations.destroy", ":id") }}'.replace(':id', locationToDelete);
            
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

    // Close modal when clicking outside
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideDeleteModal();
        }
    });
</script>

@endsection
