@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Page Header with Background Design -->
    <div class="mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center">
                <div class="bg-white/20 p-4 rounded-full backdrop-blur-sm mr-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">My Tasks</h1>
                    <p class="text-red-100 text-lg">Manage your scheduled maintenance and repair requests</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Container for Both Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
        <!-- Maintenance Tasks Section -->
        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-5 border-b border-blue-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Scheduled Maintenance
                    </h2>
                    <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold border border-blue-200">{{ $maintenanceTasks->total() }} Tasks</span>
                </div>
            </div>
            
            <div class="p-6">
                @if($maintenanceTasks->count() > 0)
                    <div class="space-y-4">
                        @foreach($maintenanceTasks as $task)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-all duration-200 transform hover:-translate-y-1 hover:shadow-md border-l-4 border-blue-500">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-3">
                                        <h3 class="font-semibold text-gray-900">{{ $task->maintenance_task }}</h3>
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            @if($task->scheduled_date < today())
                                                bg-red-100 text-red-800 ring-1 ring-red-400
                                            @elseif($task->scheduled_date == today())
                                                bg-yellow-100 text-yellow-800 ring-1 ring-yellow-400
                                            @else
                                                bg-blue-100 text-blue-800 ring-1 ring-blue-400
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
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span class="font-medium">Lab {{ $task->lab_number }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <button class="p-2 rounded-full hover:bg-blue-100 transition-colors duration-200 text-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination for Maintenance Tasks -->
                    @if($maintenanceTasks->hasPages())
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row items-center justify-between">
                            <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                                Showing {{ $maintenanceTasks->firstItem() ?? 0 }} to {{ $maintenanceTasks->lastItem() ?? 0 }} of {{ $maintenanceTasks->total() }} tasks
                            </div>
                            <div class="flex items-center space-x-2">
                                {{ $maintenanceTasks->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <div class="bg-gray-100 rounded-full p-4 w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                            <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-lg font-medium text-gray-900 mb-2">No maintenance tasks assigned</p>
                        <p class="text-gray-600">You're all caught up! New tasks will appear here when assigned.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Repair Requests Section -->
        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-5 border-b border-green-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Active Repair Requests
                    </h2>
                    <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold border border-green-200">{{ $repairRequests->total() }} Requests</span>
                </div>
            </div>
            
            <div class="p-6">
                @if($repairRequests->count() > 0)
                    <div class="space-y-4">
                        @foreach($repairRequests as $request)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-all duration-200 transform hover:-translate-y-1 hover:shadow-md border-l-4 border-green-500">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-3">
                                        <h3 class="font-semibold text-gray-900">{{ $request->equipment }}</h3>
                                        <!-- Display Asset Status -->
                                        @if($request->asset)
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ring-1 ring-blue-400">
                                            Asset: {{ ucfirst($request->asset->status) }}
                                        </span>
                                        @endif
                                    </div>
                                    <div class="space-y-3">
                                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span class="font-medium">{{ $request->location }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                                </svg>
                                                <span>Ticket: {{ $request->ticket_number }}</span>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 p-4 rounded-md border-l-2 border-green-300">
                                            <p class="text-sm text-gray-700">{{ $request->issue }}</p>
                                        </div>
                                    </div>
                                </div>
                                <button class="p-2 rounded-full hover:bg-green-100 transition-colors duration-200 text-green-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination for Repair Requests -->
                    @if($repairRequests->hasPages())
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row items-center justify-between">
                            <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                                Showing {{ $repairRequests->firstItem() ?? 0 }} to {{ $repairRequests->lastItem() ?? 0 }} of {{ $repairRequests->total() }} requests
                            </div>
                            <div class="flex items-center space-x-2">
                                {{ $repairRequests->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <div class="bg-gray-100 rounded-full p-4 w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                            <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <p class="text-lg font-medium text-gray-900 mb-2">No repair requests assigned</p>
                        <p class="text-gray-600">You're all caught up! New requests will appear here when assigned.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Summary Cards Row -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Tasks Card -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Tasks</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $maintenanceTasks->total() + $repairRequests->total() }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Maintenance Tasks Card -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Maintenance</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $maintenanceTasks->total() }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Repair Requests Card -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Repair Requests</p>
                    <p class="text-2xl font-bold text-green-600">{{ $repairRequests->total() }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection