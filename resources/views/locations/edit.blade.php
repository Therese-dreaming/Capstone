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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Edit Location</h1>
                        <p class="text-red-100 text-sm md:text-lg">Update location information</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main form card -->
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
            @if ($errors->any())
            <div class="mb-6 p-4 md:p-6 bg-red-50 border border-red-200 rounded-xl text-red-700">
                <div class="flex items-center mb-3">
                    <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-red-800 font-medium text-sm md:text-base">Please correct the following errors:</h3>
                </div>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <form action="{{ route('locations.update', $location->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <!-- Building Field -->
                    <div>
                        <label class="block text-sm md:text-base font-medium text-gray-700 mb-2">Building</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <input type="text" name="building" 
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 text-sm md:text-base" 
                                   value="{{ old('building', $location->building) }}" 
                                   required 
                                   placeholder="Enter building name">
                        </div>
                    </div>
                    
                    <!-- Floor Field -->
                    <div>
                        <label class="block text-sm md:text-base font-medium text-gray-700 mb-2">Floor</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                </svg>
                            </div>
                            <input type="text" name="floor" 
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 text-sm md:text-base" 
                                   value="{{ old('floor', $location->floor) }}" 
                                   required 
                                   placeholder="Enter floor number or name">
                        </div>
                    </div>
                    
                    <!-- Room Number Field -->
                    <div>
                        <label class="block text-sm md:text-base font-medium text-gray-700 mb-2">Room Number</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="text" name="room_number" 
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 text-sm md:text-base" 
                                   value="{{ old('room_number', $location->room_number) }}" 
                                   required 
                                   placeholder="Enter room number">
                        </div>
                    </div>
                    
                    <!-- Location Preview Card -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 md:p-6 rounded-xl border border-blue-200">
                        <h3 class="text-sm md:text-base font-medium text-blue-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Current Location Preview
                        </h3>
                        <div class="text-sm md:text-base text-blue-800 space-y-2">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="font-medium">Full Location:</span>
                                <span class="ml-2">{{ $location->full_location }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <span class="font-medium">Assets at this location:</span>
                                <span class="ml-2">{{ $location->assets()->count() }} {{ Str::plural('asset', $location->assets()->count()) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="pt-6">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                            <a href="{{ route('locations.index') }}" 
                               class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm md:text-base font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 md:w-5 md:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-sm md:text-base font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-4 h-4 md:w-5 md:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Update Location
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection