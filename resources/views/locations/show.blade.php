@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Back navigation -->
    <div class="mb-4 md:mb-6">
        <a href="{{ route('locations.index') }}" class="inline-flex items-center text-gray-600 hover:text-red-800 transition-colors duration-200 text-sm md:text-base">
            <svg class="w-4 h-4 md:w-5 md:h-5 mr-1 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            <span>Back to Locations</span>
        </a>
    </div>

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
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">{{ $location->full_location }}</h1>
                        <p class="text-red-100 text-sm md:text-lg">Location Details & Assets</p>
                    </div>
                </div>
                <a href="{{ route('locations.edit', $location->id) }}" 
                   class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white p-3 md:p-4 rounded-full shadow-lg transition-all duration-200 transform hover:scale-105 flex items-center justify-center">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Location details cards -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6 mb-6">
        <h2 class="text-lg md:text-xl font-semibold text-gray-900 mb-4 md:mb-6 flex items-center">
            <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Location Information
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 md:p-6 rounded-xl border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-blue-700 mb-1">Building</p>
                        <p class="text-lg md:text-xl font-bold text-blue-900">{{ $location->building }}</p>
                    </div>
                    <div class="bg-blue-200 p-2 md:p-3 rounded-full">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 md:p-6 rounded-xl border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-green-700 mb-1">Floor</p>
                        <p class="text-lg md:text-xl font-bold text-green-900">{{ $location->floor }}</p>
                    </div>
                    <div class="bg-green-200 p-2 md:p-3 rounded-full">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-4 md:p-6 rounded-xl border border-purple-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-purple-700 mb-1">Room Number</p>
                        <p class="text-lg md:text-xl font-bold text-purple-900">{{ $location->room_number }}</p>
                    </div>
                    <div class="bg-purple-200 p-2 md:p-3 rounded-full">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assets section -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
        <div class="flex items-center justify-between mb-4 md:mb-6">
            <h2 class="text-lg md:text-xl font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Assets in this Location
            </h2>
            <span class="bg-blue-100 text-blue-800 text-xs md:text-sm font-medium px-3 py-1 rounded-full">
                {{ $location->assets->count() }} {{ Str::plural('Asset', $location->assets->count()) }}
            </span>
        </div>
        
        @if($location->assets->count() > 0)
            <!-- Mobile view: Asset cards with improved design -->
            <div class="md:hidden space-y-3 md:space-y-4">
                @foreach($location->assets as $asset)
                <div class="bg-gray-50 rounded-xl p-4 md:p-6 hover:bg-gray-100 transition-colors duration-200 border border-gray-200">
                    <div class="flex justify-between items-start mb-3 md:mb-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center mb-2">
                                <div class="bg-blue-100 p-2 rounded-full mr-3">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-gray-900 text-base md:text-lg truncate">{{ $asset->name }}</h3>
                            </div>
                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span class="truncate">{{ $asset->serial_number }}</span>
                            </div>
                        </div>
                        <span class="px-3 py-1 text-xs font-medium rounded-full flex-shrink-0 ml-2
                            @if($asset->status === 'Active') bg-green-100 text-green-800
                            @elseif($asset->status === 'Inactive') bg-red-100 text-red-800
                            @elseif($asset->status === 'Under Maintenance') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $asset->status }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-white p-3 rounded-lg border border-gray-200">
                            <div class="flex items-center text-xs text-gray-500 mb-1">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                Category
                            </div>
                            <p class="text-sm font-medium text-gray-900">{{ $asset->category->name ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-gray-200">
                            <div class="flex items-center text-xs text-gray-500 mb-1">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Vendor
                            </div>
                            <p class="text-sm font-medium text-gray-900">{{ $asset->vendor->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Desktop view: Table with improved styling -->
            <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($location->assets as $asset)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-2 rounded-full mr-3">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">{{ $asset->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->serial_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->category->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->vendor->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($asset->status === 'Active') bg-green-100 text-green-800
                                    @elseif($asset->status === 'Inactive') bg-red-100 text-red-800
                                    @elseif($asset->status === 'Under Maintenance') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $asset->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 md:py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                <div class="bg-gray-100 rounded-full p-3 md:p-4 w-16 h-16 md:w-20 md:h-20 mx-auto mb-3 md:mb-4 flex items-center justify-center">
                    <svg class="h-8 w-8 md:h-10 md:w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">No assets found</h3>
                <p class="text-xs md:text-sm text-gray-500 mb-4">No assets are currently assigned to this location.</p>
                <a href="{{ route('assets.create') }}" 
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add New Asset
                </a>
            </div>
        @endif
    </div>
</div>
@endsection