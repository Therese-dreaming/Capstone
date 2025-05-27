@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Page Header with Subtle Gradient -->
    <div class="mb-6 md:mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-xl md:text-3xl font-bold text-gray-800 flex items-center">
            <svg class="w-6 h-6 md:w-8 md:h-8 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            My Tasks
        </h1>
        <p class="text-sm md:text-base text-gray-600 mt-1">Manage your scheduled maintenance and repair requests</p>
    </div>

    <!-- Grid Container for Both Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
        <!-- Maintenance Tasks Section -->
        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-5 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg md:text-xl font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Scheduled Maintenance
                    </h2>
                    <span class="px-3 py-1 bg-gray-200 text-gray-800 rounded-full text-xs font-medium">{{ $maintenanceTasks->count() }} Tasks</span>
                </div>
            </div>
            
            <div class="p-5">
                @if($maintenanceTasks->count() > 0)
                    <div class="space-y-4">
                        @foreach($maintenanceTasks as $task)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition duration-150 transform hover:-translate-y-1 hover:shadow-sm">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-3">
                                        <h3 class="font-medium text-gray-900">{{ $task->maintenance_task }}</h3>
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            @if($task->scheduled_date < today())
                                                bg-red-100 text-red-800 ring-1 ring-red-400
                                            @elseif($task->scheduled_date == today())
                                                bg-yellow-100 text-yellow-800 ring-1 ring-yellow-400
                                            @else
                                                bg-blue-100 text-blue-800
                                            @endif
                                        ">
                                            @if($task->scheduled_date < today())
                                                Overdue
                                            @elseif($task->scheduled_date == today())
                                                Today
                                            @else
                                                Upcoming
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 md:gap-5 text-xs md:text-sm text-gray-600">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span>Lab {{ $task->lab_number }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <button class="p-1.5 rounded-full hover:bg-gray-200 transition-colors duration-150">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="mt-3 text-sm md:text-base text-gray-600">No maintenance tasks assigned.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Repair Requests Section -->
        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-5 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg md:text-xl font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Active Repair Requests
                    </h2>
                    <span class="px-3 py-1 bg-gray-200 text-gray-800 rounded-full text-xs font-medium">{{ $repairRequests->count() }} Requests</span>
                </div>
            </div>
            
            <div class="p-5">
                @if($repairRequests->count() > 0)
                    <div class="space-y-4">
                        @foreach($repairRequests as $request)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition duration-150 transform hover:-translate-y-1 hover:shadow-sm">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-3">
                                        <h3 class="font-medium text-gray-900">{{ $request->equipment }}</h3>
                                        <!-- Display Asset Status -->
                                        @if($request->asset)
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Asset: {{ ucfirst($request->asset->status) }}
                                        </span>
                                        @endif
                                    </div>
                                    <div class="space-y-3">
                                        <div class="flex flex-wrap items-center gap-3 md:gap-5 text-xs md:text-sm text-gray-600">
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span>{{ $request->location }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                                </svg>
                                                <span>Ticket: {{ $request->ticket_number }}</span>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-md border-l-2 border-gray-300">
                                            <p class="text-xs md:text-sm text-gray-700">{{ $request->issue }}</p>
                                        </div>
                                    </div>
                                </div>
                                <button class="p-1.5 rounded-full hover:bg-gray-200 transition-colors duration-150">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <p class="mt-3 text-sm md:text-base text-gray-600">No repair requests assigned.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection