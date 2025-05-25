@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8">
    <h1 class="text-xl md:text-2xl font-bold mb-4 md:mb-6">My Tasks</h1>

    <!-- Grid Container for Both Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
        <!-- Maintenance Tasks Section -->
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg md:text-xl font-semibold">Scheduled Maintenance Tasks</h2>
                <span class="text-xs md:text-sm text-gray-600">{{ $maintenanceTasks->count() }} Tasks</span>
            </div>
            
            @if($maintenanceTasks->count() > 0)
                <div class="space-y-3 md:space-y-4">
                    @foreach($maintenanceTasks as $task)
                    <div class="border rounded-lg p-3 md:p-4 hover:bg-gray-50 transition duration-150">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <h3 class="font-medium text-gray-900">{{ $task->maintenance_task }}</h3>
                                    <span class="px-2 md:px-3 py-1 rounded-full text-xs font-medium
                                        {{ $task->scheduled_date == today() ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $task->scheduled_date == today() ? 'Today' : 'Upcoming' }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 md:gap-4 text-xs md:text-sm text-gray-600">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span>Lab {{ $task->lab_number }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 md:py-8">
                    <svg class="mx-auto h-10 w-10 md:h-12 md:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="mt-2 text-sm md:text-base text-gray-600">No maintenance tasks assigned.</p>
                </div>
            @endif
        </div>

        <!-- Repair Requests Section -->
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg md:text-xl font-semibold">Active Repair Requests</h2>
                <span class="text-xs md:text-sm text-gray-600">{{ $repairRequests->count() }} Requests</span>
            </div>
            
            @if($repairRequests->count() > 0)
                <div class="space-y-3 md:space-y-4">
                    @foreach($repairRequests as $request)
                    <div class="border rounded-lg p-3 md:p-4 hover:bg-gray-50 transition duration-150">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <h3 class="font-medium text-gray-900">{{ $request->equipment }}</h3>
                                    <span class="px-2 md:px-3 py-1 rounded-full text-xs font-medium
                                        {{ $request->status === 'urgent' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex flex-wrap items-center gap-2 md:gap-4 text-xs md:text-sm text-gray-600">
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span>{{ $request->location }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                            </svg>
                                            <span>Ticket: {{ $request->ticket_number }}</span>
                                        </div>
                                    </div>
                                    <p class="text-xs md:text-sm text-gray-500 mt-2">{{ $request->issue }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 md:py-8">
                    <svg class="mx-auto h-10 w-10 md:h-12 md:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="mt-2 text-sm md:text-base text-gray-600">No repair requests assigned.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection