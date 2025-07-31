@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6">
    <!-- Back navigation -->
    <div class="mb-4">
        <a href="{{ route('locations.index') }}" class="inline-flex items-center text-gray-600 hover:text-red-800 transition-colors">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            <span>Back to Locations</span>
        </a>
    </div>

    <!-- Main content card -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Header with location icon and actions -->
        <div class="bg-red-800 text-white p-4 flex justify-between items-center">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <h1 class="text-xl font-bold">{{ $location->full_location }}</h1>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('locations.edit', $location->id) }}" class="inline-flex items-center bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-md transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Edit</span>
                </a>
            </div>
        </div>

        <!-- Location details cards -->
        <div class="p-4 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex">
                    <div class="bg-red-100 text-red-800 p-3 rounded-full mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Building</label>
                        <p class="text-lg font-medium">{{ $location->building }}</p>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex">
                    <div class="bg-red-100 text-red-800 p-3 rounded-full mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                        </svg>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Floor</label>
                        <p class="text-lg font-medium">{{ $location->floor }}</p>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex">
                    <div class="bg-red-100 text-red-800 p-3 rounded-full mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Room Number</label>
                        <p class="text-lg font-medium">{{ $location->room_number }}</p>
                    </div>
                </div>
            </div>

            <!-- Assets section -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h2 class="text-lg font-semibold">Assets in this Location</h2>
                    </div>
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        {{ $location->assets->count() }} {{ Str::plural('Asset', $location->assets->count()) }}
                    </span>
                </div>
                
                @if($location->assets->count() > 0)
                    <!-- Mobile view: Asset cards -->
                    <div class="md:hidden space-y-3">
                        @foreach($location->assets as $asset)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-medium text-gray-900">{{ $asset->name }}</h3>
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($asset->status === 'Active') bg-green-100 text-green-800
                                    @elseif($asset->status === 'Inactive') bg-red-100 text-red-800
                                    @elseif($asset->status === 'Under Maintenance') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $asset->status }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div>
                                    <p class="text-gray-500">Serial Number:</p>
                                    <p class="font-medium">{{ $asset->serial_number }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Category:</p>
                                    <p class="font-medium">{{ $asset->category->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Vendor:</p>
                                    <p class="font-medium">{{ $asset->vendor->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Desktop view: Table -->
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
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $asset->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $asset->serial_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $asset->category->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $asset->vendor->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
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
                    <div class="bg-gray-50 rounded-lg p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No assets found</h3>
                        <p class="mt-1 text-sm text-gray-500">No assets are currently assigned to this location.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection