@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8">
    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
        {{ session('error') }}
    </div>
    @endif

    <!-- Back Button -->
    <div class="mb-3 sm:mb-4 md:mb-6">
        <a href="{{ route('maintenance.history') }}" class="inline-flex items-center text-gray-600 hover:text-red-800 transition-colors duration-200 text-xs sm:text-sm md:text-base">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-5 md:h-5 mr-1 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            <span class="hidden sm:inline">Back to Maintenance History</span>
            <span class="sm:hidden">Back</span>
        </a>
    </div>

    <!-- Page Header -->
    <div class="mb-4 sm:mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-3 sm:p-4 md:p-6 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <div class="flex items-center">
                    <div class="bg-white/20 p-2 sm:p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4 flex-shrink-0">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2 truncate">Maintenance Details</h1>
                        <p class="text-red-100 text-xs sm:text-sm md:text-lg">Complete information for this maintenance record</p>
                    </div>
                </div>
                <button onclick="confirmDelete({{ $maintenance->id }})" class="w-full sm:w-auto inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-red-800 transition-colors duration-200 text-sm sm:text-base">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span class="hidden sm:inline">Delete</span>
                    <span class="sm:hidden">Del</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 md:gap-8">
        <!-- Left Column -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Basic Information Card -->
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6">
                <h2 class="text-base sm:text-lg md:text-xl font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Basic Information
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div class="bg-blue-100 p-1.5 sm:p-2 rounded-lg flex-shrink-0">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <label class="block text-xs font-medium text-gray-500">Scheduled Date</label>
                            <p class="text-xs sm:text-sm font-semibold text-gray-900 break-words">{{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div class="bg-green-100 p-1.5 sm:p-2 rounded-lg flex-shrink-0">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <label class="block text-xs font-medium text-gray-500">Target Date</label>
                            <p class="text-xs sm:text-sm font-semibold text-gray-900 break-words">{{ optional($maintenance->target_date)->format('M d, Y') ?? 'Not specified' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div class="bg-purple-100 p-1.5 sm:p-2 rounded-lg flex-shrink-0">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <label class="block text-xs font-medium text-gray-500">Status</label>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ $maintenance->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($maintenance->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div class="bg-indigo-100 p-1.5 sm:p-2 rounded-lg flex-shrink-0">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <label class="block text-xs font-medium text-gray-500">Location</label>
                            <p class="text-xs sm:text-sm font-semibold text-gray-900 break-words">
                                {{ $maintenance->location ? ($maintenance->location->building . ' - Floor ' . $maintenance->location->floor . ' - Room ' . $maintenance->location->room_number) : 'N/A' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div class="bg-orange-100 p-1.5 sm:p-2 rounded-lg flex-shrink-0">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <label class="block text-xs font-medium text-gray-500">Technician</label>
                            <p class="text-xs sm:text-sm font-semibold text-gray-900 break-words">{{ $maintenance->technician->name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div class="bg-teal-100 p-1.5 sm:p-2 rounded-lg flex-shrink-0">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <label class="block text-xs font-medium text-gray-500">Action By</label>
                            <p class="text-xs sm:text-sm font-semibold text-gray-900 break-words">{{ $maintenance->actionBy ? $maintenance->actionBy->name : 'System' }}</p>
                        </div>
                    </div>
                    @if($maintenance->status === 'completed' && $maintenance->completed_at)
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div class="bg-emerald-100 p-1.5 sm:p-2 rounded-lg flex-shrink-0">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <label class="block text-xs font-medium text-gray-500">Completion Time</label>
                            <p class="text-xs sm:text-sm font-semibold text-gray-900 break-words">{{ \Carbon\Carbon::parse($maintenance->completed_at)->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>
                    @endif
                    @if($maintenance->lab_number)
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div class="bg-pink-100 p-1.5 sm:p-2 rounded-lg flex-shrink-0">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <label class="block text-xs font-medium text-gray-500">Laboratory Number</label>
                            <p class="text-xs sm:text-sm font-semibold text-gray-900 break-words">{{ $maintenance->lab_number }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Notes Card -->
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6">
                <h2 class="text-base sm:text-lg md:text-xl font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-purple-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Notes
                </h2>
                @if($maintenance->notes)
                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg p-3 sm:p-4 border border-purple-200">
                        <div class="flex items-start space-x-2 sm:space-x-3">
                            <div class="bg-purple-200 p-1.5 sm:p-2 rounded-full mt-1 flex-shrink-0">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-700 whitespace-pre-wrap flex-1 break-words">{{ $maintenance->notes }}</p>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 rounded-lg p-3 sm:p-4 border border-gray-200">
                        <div class="flex items-center space-x-2 sm:space-x-3">
                            <div class="bg-gray-200 p-1.5 sm:p-2 rounded-full flex-shrink-0">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-500 italic">No notes were added for this maintenance record</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Maintenance Tasks Card -->
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6">
                <h2 class="text-base sm:text-lg md:text-xl font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Maintenance Tasks
                </h2>
                @php
                    $tasks = is_array($maintenance->maintenance_task) ? $maintenance->maintenance_task : json_decode($maintenance->maintenance_task, true);
                @endphp
                @if(is_array($tasks))
                    <div class="space-y-2 sm:space-y-3">
                        @foreach($tasks as $index => $task)
                            <div class="flex items-center space-x-2 sm:space-x-3 p-2 sm:p-3 bg-green-50 rounded-lg border border-green-200">
                                <div class="bg-green-200 p-1.5 sm:p-2 rounded-full flex-shrink-0">
                                    <span class="text-xs font-bold text-green-800">{{ $index + 1 }}</span>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-700 flex-1 break-words">{{ $task }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-green-50 rounded-lg p-3 sm:p-4 border border-green-200">
                        <p class="text-xs sm:text-sm text-gray-700 break-words">{{ $maintenance->maintenance_task }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Excluded Assets Card -->
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6">
                <h2 class="text-base sm:text-lg md:text-xl font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-orange-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    Excluded Assets
                </h2>
                @php
                    $excludedAssets = is_array($maintenance->excluded_assets) ? $maintenance->excluded_assets : json_decode($maintenance->excluded_assets, true);
                @endphp
                @if(is_array($excludedAssets) && count($excludedAssets) > 0)
                    <div class="space-y-2 sm:space-y-3">
                        @foreach($excludedAssets as $index => $asset)
                            <div class="flex items-center space-x-2 sm:space-x-3 p-2 sm:p-3 bg-orange-50 rounded-lg border border-orange-200">
                                <div class="bg-orange-200 p-1.5 sm:p-2 rounded-full flex-shrink-0">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-700 flex-1 break-words">{{ $asset }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-orange-50 rounded-lg p-3 sm:p-4 border border-orange-200">
                        <div class="flex items-center space-x-2 sm:space-x-3">
                            <div class="bg-orange-200 p-1.5 sm:p-2 rounded-full flex-shrink-0">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-500 italic">No assets were excluded from this maintenance</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Asset Issues Card (if any) -->
            @if($maintenance->asset_issues && is_array($maintenance->asset_issues) && !empty($maintenance->asset_issues))
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6">
                <h2 class="text-base sm:text-lg md:text-xl font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Asset Issues Found
                </h2>
                <div class="space-y-3 sm:space-y-4">
                    @php
                        $serialNumbers = is_array($maintenance->serial_number) ? $maintenance->serial_number : json_decode($maintenance->serial_number, true);
                    @endphp
                    @foreach($maintenance->asset_issues as $index => $issue)
                        <div class="border border-red-200 rounded-lg p-3 sm:p-4 bg-red-50">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-2 sm:mb-3 space-y-2 sm:space-y-0">
                                <div class="flex items-center space-x-2 sm:space-x-3">
                                    <div class="bg-red-200 p-1.5 sm:p-2 rounded-full flex-shrink-0">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                    </div>
                                    <h4 class="font-medium text-gray-900 text-xs sm:text-sm break-words">
                                        Asset: {{ is_array($serialNumbers) ? ($serialNumbers[$index] ?? 'N/A') : $maintenance->serial_number }}
                                    </h4>
                                </div>
                                @php
                                    $issueDesc = is_array($issue) ? ($issue['issue_description'] ?? '') : (string)$issue;
                                @endphp
                                <a href="{{ route('repair.request') }}?serial_number={{ is_array($serialNumbers) ? ($serialNumbers[$index] ?? '') : $maintenance->serial_number }}&issue={{ urlencode($issueDesc) }}&location=Laboratory {{ $maintenance->lab_number }}" 
                                   class="inline-flex items-center px-2 sm:px-3 py-1 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700 transition-colors flex-shrink-0">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    <span class="hidden sm:inline">Create Repair Request</span>
                                    <span class="sm:hidden">Create</span>
                                </a>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-700 ml-0 sm:ml-11 break-words">{{ $issueDesc }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Additional Information Card -->
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6">
                <h2 class="text-base sm:text-lg md:text-xl font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Additional Information
                </h2>
                <div class="space-y-3 sm:space-y-4">
                    <div class="flex items-center space-x-2 sm:space-x-3 p-2 sm:p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                        <div class="bg-indigo-200 p-1.5 sm:p-2 rounded-lg flex-shrink-0">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <label class="block text-xs font-medium text-gray-500">Created At</label>
                            <p class="text-xs sm:text-sm font-semibold text-gray-900 break-words">{{ \Carbon\Carbon::parse($maintenance->created_at)->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 sm:space-x-3 p-2 sm:p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                        <div class="bg-indigo-200 p-1.5 sm:p-2 rounded-lg flex-shrink-0">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <label class="block text-xs font-medium text-gray-500">Last Updated</label>
                            <p class="text-xs sm:text-sm font-semibold text-gray-900 break-words">{{ \Carbon\Carbon::parse($maintenance->updated_at)->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="p-4 sm:p-5 border w-full max-w-sm shadow-lg rounded-md bg-white">
        <div class="mt-2 sm:mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-red-100">
                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-base sm:text-lg leading-6 font-medium text-gray-900 mt-3 sm:mt-4">Delete Maintenance</h3>
            <div class="mt-2 px-2 sm:px-7 py-3">
                <p class="text-xs sm:text-sm text-gray-500">Are you sure you want to delete this maintenance record? This action cannot be undone.</p>
            </div>
            <div class="flex flex-col sm:flex-row justify-center gap-2 sm:gap-4 mt-4">
                <button onclick="executeDelete()" class="w-full sm:w-auto px-4 py-2 bg-red-600 text-white text-sm sm:text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                    Delete
                </button>
                <button onclick="closeDeleteModal()" class="w-full sm:w-auto px-4 py-2 bg-gray-100 text-gray-700 text-sm sm:text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

function executeDelete() {
    fetch('/maintenance/' + {{ $maintenance->id }}, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        closeDeleteModal();
        if (data.success) {
            window.location.href = '{{ route("maintenance.history") }}';
        } else {
            alert('Error deleting maintenance record: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        closeDeleteModal();
        alert('An error occurred while deleting the maintenance record');
    });
}
</script>

<style>
</style>
@endsection 