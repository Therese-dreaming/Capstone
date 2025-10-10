@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 transition-all duration-300" id="mainContent">
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center" role="alert">
        <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <div class="font-semibold">Success!</div>
            <div>{{ session('success') }}</div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-center" role="alert">
        <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
            <div class="font-semibold">Error!</div>
            <div>{{ session('error') }}</div>
        </div>
    </div>
    @endif

    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center mb-4 sm:mb-0">
                    <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4 flex-shrink-0">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2 truncate">Repair Requests History</h1>
                        <p class="text-red-100 text-sm md:text-lg">View, filter, and manage completed and cancelled repairs</p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <button onclick="openSignatureModal()" class="text-sm px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-md hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 focus:ring-offset-2 focus:ring-offset-red-800 flex items-center justify-center border border-white/30">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span class="hidden sm:inline">Preview PDF</span>
                        <span class="sm:hidden">Preview</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-4 md:p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold">Repair Requests History</h1>
        </div>

        <!-- Summary Cards -->
        <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            
            <!-- Total Repairs Card -->
            <div class="bg-blue-600 rounded-xl shadow-lg p-4 sm:p-6 text-white">
                <div class="flex items-center">
                    <div class="bg-white/20 p-2 sm:p-3 rounded-full backdrop-blur-sm mr-3 sm:mr-4 flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-blue-100 text-xs sm:text-sm font-medium">Total Repairs</p>
                        <p class="text-xl sm:text-2xl lg:text-3xl font-bold">{{ number_format($totalRepairs) }}</p>
                    </div>
                </div>
            </div>

            <!-- Completed Repairs Card -->
            <div class="bg-green-600 rounded-xl shadow-lg p-4 sm:p-6 text-white">
                <div class="flex items-center">
                    <div class="bg-white/20 p-2 sm:p-3 rounded-full backdrop-blur-sm mr-3 sm:mr-4 flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-green-100 text-xs sm:text-sm font-medium">Completed Repairs</p>
                        <p class="text-xl sm:text-2xl lg:text-3xl font-bold">{{ number_format($completedRepairs) }}</p>
                    </div>
                </div>
            </div>

            <!-- Unregistered Items Card -->
            <div class="bg-red-600 rounded-xl shadow-lg p-4 sm:p-6 text-white">
                <div class="flex items-center">
                    <div class="bg-white/20 p-2 sm:p-3 rounded-full backdrop-blur-sm mr-3 sm:mr-4 flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-red-100 text-xs sm:text-sm font-medium">Unregistered Items</p>
                        <p class="text-xl sm:text-2xl lg:text-3xl font-bold">{{ number_format($unregisteredItems) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="mb-6 p-4 sm:p-6 bg-gray-50 rounded-xl border border-gray-200">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Filter Repair History</h3>
            
            <!-- Filter Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4 mb-4">
                <!-- Request Date Range -->
                <div class="xl:col-span-2">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Request Date Range</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="date" id="requestStartDate" class="h-8 sm:h-9 px-2 sm:px-3 text-xs sm:text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        <input type="date" id="requestEndDate" class="h-8 sm:h-9 px-2 sm:px-3 text-xs sm:text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>

                <!-- Registration Status Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Registration</label>
                    <select id="registrationFilter" onchange="filterHistory()" class="h-8 sm:h-9 w-full px-2 sm:px-3 text-xs sm:text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        <option value="">All Items</option>
                        <option value="registered">Registered</option>
                        <option value="unregistered">Unregistered</option>
                    </select>
                </div>

                <!-- Completion Date Range -->
                <div class="xl:col-span-2">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Completion Date Range</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="date" id="completionStartDate" class="h-8 sm:h-9 px-2 sm:px-3 text-xs sm:text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        <input type="date" id="completionEndDate" class="h-8 sm:h-9 px-2 sm:px-3 text-xs sm:text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Status</label>
                    <select id="statusFilter" onchange="filterHistory()" class="h-8 sm:h-9 w-full px-2 sm:px-3 text-xs sm:text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        <option value="">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="pulled_out">Pulled Out</option>
                    </select>
                </div>
            </div>

            <!-- Location Filter (Full Width) -->
            <div class="mb-4">
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Location</label>
                <select id="locationFilter" onchange="filterHistory()" class="h-8 sm:h-9 w-full px-2 sm:px-3 text-xs sm:text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Locations</option>
                    @php
                        $requestsCollection = ($completedRequests instanceof \Illuminate\Pagination\AbstractPaginator)
                            ? collect($completedRequests->items())
                            : collect($completedRequests);
                        
                        $uniqueLocations = $requestsCollection
                            ->map(function($r) {
                                return [
                                    'building' => $r->building ?? null,
                                    'floor' => $r->floor ?? null,
                                    'room' => $r->room ?? null,
                                ];
                            })
                            ->filter(function($loc) {
                                return !empty($loc['building']) && !empty($loc['floor']) && !empty($loc['room']);
                            })
                            ->unique(function($loc) {
                                return $loc['building'].'|'.$loc['floor'].'|'.$loc['room'];
                            })
                            ->values();
                    @endphp
                    @foreach($uniqueLocations as $loc)
                    <option value="{{ $loc['building'] }}-{{ $loc['floor'] }}-{{ $loc['room'] }}">
                        {{ $loc['building'] }} - {{ $loc['floor'] }} - {{ $loc['room'] }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4">
                <!-- Filter Actions -->
                <div class="flex flex-wrap gap-2">
                    <button onclick="filterHistory()" class="bg-red-800 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-md hover:bg-red-700 flex items-center text-xs sm:text-sm">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span class="hidden sm:inline">Apply Filters</span>
                        <span class="sm:hidden">Apply</span>
                    </button>
                    <button onclick="clearFilters()" class="bg-gray-500 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-md hover:bg-gray-600 flex items-center text-xs sm:text-sm">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="hidden sm:inline">Clear Filters</span>
                        <span class="sm:hidden">Clear</span>
                    </button>
                </div>

                <!-- Delete Selected Actions -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3">
                    <div class="text-xs sm:text-sm text-gray-600" id="selectedCount">0 items selected</div>
                    <button onclick="deleteSelected()" class="text-xs sm:text-sm px-3 sm:px-4 py-1.5 sm:py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 flex items-center" disabled id="deleteSelectedBtn">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span class="hidden sm:inline">Delete Selected</span>
                        <span class="sm:hidden">Delete</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Cards for mobile -->
        <div class="grid grid-cols-1 gap-4 md:hidden">
            @forelse($completedRequests as $request)
            <div class="bg-white rounded-lg shadow p-4 flex flex-col gap-2 border border-gray-200 relative transition ring-0" data-id="{{ $request->id }}" onclick="toggleCardSelection(this)">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-red-800">{{ \Carbon\Carbon::parse($request->created_at)->format('M j, Y (g:i A)') }}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($request->status === 'cancelled') bg-red-100 text-red-800
                        @elseif($request->status === 'pulled_out') bg-yellow-100 text-yellow-800
                        @else bg-green-100 text-green-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                    </span>
                </div>
                <div class="text-sm text-gray-700"><span class="font-semibold">Completion Date:</span> {{ \Carbon\Carbon::parse($request->completed_at)->format('M j, Y (g:i A)') }}</div>
                <div class="text-sm">
                    <span class="font-semibold">Item:</span>
                    @if($request->asset && $request->asset->serial_number)
                        <a href="{{ route('repair.details', ['id' => $request->id]) }}" class="font-bold text-red-600 hover:underline">{{ $request->asset->serial_number }}</a>
                    @elseif(!empty($request->serial_number))
                        <a href="{{ route('repair.details', ['id' => $request->id]) }}" class="font-bold text-red-600 hover:underline">{{ $request->serial_number }}</a>
                    @else
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-900">{{ $request->equipment }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Item unregistered
                            </span>
                        </div>
                    @endif
                </div>
                <div class="text-sm"><span class="font-semibold">Ticket No.:</span> {{ $request->ticket_number }}</div>
                @if(!in_array($request->creator->group_id ?? null, [1, 2]))
                <div class="text-sm"><span class="font-semibold">Caller's Name:</span> {{ $request->caller_name ?: 'N/A' }}</div>
                @endif
                <div class="text-sm"><span class="font-semibold">Findings:</span> {{ $request->findings }}</div>
                <div class="text-sm"><span class="font-semibold">Remarks:</span> {{ $request->remarks }}</div>
                <div class="flex gap-2 mt-2">
                    <button onclick="event.stopPropagation(); window.location.href='{{ route('repair.details', ['id' => $request->id]) }}'" class="text-blue-600 hover:text-blue-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                    <button onclick="event.stopPropagation(); confirmDelete({{ $request->id }})" class="text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
            @empty
            <div class="text-center text-gray-500 col-span-full">No repair history found</div>
            @endforelse
        </div>

        <!-- Table for desktop -->
        <div class="hidden md:block">
            <table id="repairTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-red-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Request Date</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Time Started</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Completion Date</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Duration</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Item</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Ticket No.</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($completedRequests as $request)
                    <tr data-location="{{ $request->building }} - {{ $request->floor }} - {{ $request->room }}" class="{{ strlen($request->remarks) > 50 ? 'hover:bg-gray-50 cursor-pointer' : '' }}" {{ strlen($request->remarks) > 50 ? 'onclick=toggleRemarks('.$request->id.')' : '' }}>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 whitespace-nowrap">
                            <input type="checkbox" name="selected[]" value="{{ $request->id }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($request->created_at)->format('M j, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($request->created_at)->format('g:i A') }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm text-gray-900">{{ $request->time_started ? \Carbon\Carbon::parse($request->time_started)->format('M j, Y') : 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $request->time_started ? \Carbon\Carbon::parse($request->time_started)->format('g:i A') : 'N/A' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($request->completed_at)->format('M j, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($request->completed_at)->format('g:i A') }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($request->duration !== 'N/A')
                                    @php
                                        $parts = explode(' ', trim($request->duration));
                                        $currentPart = '';
                                        foreach($parts as $part) {
                                            if(strpos($part, 'd') !== false || strpos($part, 'hrs') !== false || strpos($part, 'mins') !== false) {
                                                if($currentPart) {
                                                    echo '<div>' . $currentPart . '</div>';
                                                }
                                                $currentPart = $part;
                                            } else {
                                                $currentPart .= ' ' . $part;
                                            }
                                        }
                                        if($currentPart) {
                                            echo '<div>' . $currentPart . '</div>';
                                        }
                                    @endphp
                                @else
                                    N/A
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($request->status === 'cancelled') bg-red-100 text-red-800
                                @elseif($request->status === 'pulled_out') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($request->asset && $request->asset->serial_number)
                                <a href="{{ route('repair.details', ['id' => $request->id]) }}" class="font-bold text-red-600 hover:underline">{{ $request->asset->serial_number }}</a>
                            @elseif(!empty($request->serial_number))
                                <a href="{{ route('repair.details', ['id' => $request->id]) }}" class="font-bold text-red-600 hover:underline">{{ $request->serial_number }}</a>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-900">{{ $request->equipment }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Item unregistered
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->ticket_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex space-x-2">
                                <button onclick="window.location.href='{{ route('repair.details', ['id' => $request->id]) }}'" class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                <button onclick="event.stopPropagation(); confirmDelete({{ $request->id }})" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @if(strlen($request->remarks) > 50 || strlen($request->findings) > 50)
                    <tr id="remarks-{{ $request->id }}" class="hidden bg-gray-50">
                        <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="text-sm">
                                @if(strlen($request->findings) > 50)
                                <div class="mb-4">
                                    <span class="font-semibold">Full Findings:</span>
                                    <p class="mt-2 whitespace-pre-wrap">{{ $request->findings }}</p>
                                </div>
                                @endif
                                @if(strlen($request->remarks) > 50)
                                <div>
                                    <span class="font-semibold">Full Remarks:</span>
                                    <p class="mt-2 whitespace-pre-wrap">{{ $request->remarks }}</p>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">No repair history found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Add pagination links -->
    <div class="mt-6">
        {{ $completedRequests->links() }}
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[60] items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirm Delete</h3>
        <p class="text-gray-600 mb-6">Are you sure you want to delete the selected repair request(s)? This action cannot be undone.</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Cancel
            </button>
            <button onclick="executeDelete()" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Delete
            </button>
        </div>
    </div>
</div>

<!-- Add Image Modal -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center">
    <div class="relative" onclick="event.stopPropagation();">
        <img id="modalImage" src="" alt="Enlarged Photo" class="max-h-[80vh] max-w-[80vw] object-contain">
        <button onclick="closeImageModal()" class="absolute -top-4 -right-4 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 transition-colors">
            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<!-- Signature Modal -->
<div id="signatureModal" 
     class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4"
     style="z-index: 70;">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add Signatures for PDF</h3>
                <button onclick="closeSignatureModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div id="signatureEntries" class="space-y-4">
                <!-- Signature entries will be added here dynamically -->
            </div>
            
            <div class="flex justify-between items-center mt-6">
                <button onclick="addSignatureEntry()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Signature
                </button>
                
                <div class="flex space-x-3">
                    <button onclick="closeSignatureModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Cancel
                    </button>
                    <button onclick="generatePDFWithSignatures()" class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Generate PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    // Add event listeners for date filters
    document.getElementById('requestStartDate').addEventListener('change', filterHistory);
    document.getElementById('requestEndDate').addEventListener('change', filterHistory);
    document.getElementById('completionStartDate').addEventListener('change', filterHistory);
    document.getElementById('completionEndDate').addEventListener('change', filterHistory);

    function filterHistory() {
        const statusFilter = document.getElementById('statusFilter').value;
        const locationFilter = document.getElementById('locationFilter').value;
        const requestStartDate = document.getElementById('requestStartDate').value;
        const requestEndDate = document.getElementById('requestEndDate').value;
        const completionStartDate = document.getElementById('completionStartDate').value;
        const completionEndDate = document.getElementById('completionEndDate').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (statusFilter) params.append('status', statusFilter);
        if (locationFilter) params.append('location', locationFilter);
        
        // Request date parameters
        if (requestStartDate) params.append('request_start_date', requestStartDate);
        if (requestEndDate) params.append('request_end_date', requestEndDate);
        
        // Completion date parameters
        if (completionStartDate) params.append('completion_start_date', completionStartDate);
        if (completionEndDate) params.append('completion_end_date', completionEndDate);

        // Redirect to filtered URL
        window.location.href = `{{ route('repair.completed') }}?${params.toString()}`;
    }

    // Set initial filter values from URL parameters
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Set status and location filters
        document.getElementById('statusFilter').value = urlParams.get('status') || '';
        document.getElementById('locationFilter').value = urlParams.get('location') || '';
        
        // Set request date filters
        document.getElementById('requestStartDate').value = urlParams.get('request_start_date') || '';
        document.getElementById('requestEndDate').value = urlParams.get('request_end_date') || '';
        
        // Set completion date filters
        document.getElementById('completionStartDate').value = urlParams.get('completion_start_date') || '';
        document.getElementById('completionEndDate').value = urlParams.get('completion_end_date') || '';
    });

    // Signature functionality
    let signatureCounter = 0;
    let currentFilters = {};

    function openSignatureModal() {
        // Store current filters
        currentFilters = {
            status: document.getElementById('statusFilter').value,
            location: document.getElementById('locationFilter').value,
            request_start_date: document.getElementById('requestStartDate').value,
            request_end_date: document.getElementById('requestEndDate').value,
            completion_start_date: document.getElementById('completionStartDate').value,
            completion_end_date: document.getElementById('completionEndDate').value
        };

        // Clear existing entries and add one default entry
        document.getElementById('signatureEntries').innerHTML = '';
        signatureCounter = 0;
        addSignatureEntry();
        
        // Show modal
        document.getElementById('signatureModal').classList.remove('hidden');
        document.getElementById('signatureModal').classList.add('flex');
    }

    function closeSignatureModal() {
        document.getElementById('signatureModal').classList.add('hidden');
        document.getElementById('signatureModal').classList.remove('flex');
    }

    function addSignatureEntry() {
        signatureCounter++;
        const entryId = `signature-${signatureCounter}`;
        
        const entryHtml = `
            <div class="border border-gray-200 rounded-lg p-4" id="${entryId}">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-medium text-gray-900">Signature ${signatureCounter}</h4>
                    <button onclick="removeSignatureEntry('${entryId}')" class="text-red-600 hover:text-red-800" title="Remove">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
                
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Label</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500" 
                           placeholder="e.g., Checked by, Supervised by, Approved by" 
                           id="${entryId}-label">
                </div>
                
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500" 
                           placeholder="Enter name" 
                           id="${entryId}-name">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Signature</label>
                    <div class="border border-gray-300 rounded-md">
                        <canvas id="${entryId}-canvas" width="300" height="120" class="block w-full cursor-crosshair"></canvas>
                    </div>
                    <div class="flex justify-end mt-2">
                        <button onclick="clearSignature('${entryId}-canvas')" class="text-sm text-gray-600 hover:text-gray-800">
                            Clear Signature
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('signatureEntries').insertAdjacentHTML('beforeend', entryHtml);
        initializeSignaturePad(`${entryId}-canvas`);
    }

    function removeSignatureEntry(entryId) {
        const entry = document.getElementById(entryId);
        if (entry) {
            entry.remove();
        }
        
        // If no entries left, add one default entry
        if (document.getElementById('signatureEntries').children.length === 0) {
            addSignatureEntry();
        }
    }

    function initializeSignaturePad(canvasId) {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;

        // Set canvas background to white
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Set drawing styles
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        function getMousePos(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            
            return {
                x: (e.clientX - rect.left) * scaleX,
                y: (e.clientY - rect.top) * scaleY
            };
        }

        function getTouchPos(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            
            return {
                x: (e.touches[0].clientX - rect.left) * scaleX,
                y: (e.touches[0].clientY - rect.top) * scaleY
            };
        }

        // Mouse events
        canvas.addEventListener('mousedown', (e) => {
            isDrawing = true;
            const pos = getMousePos(e);
            lastX = pos.x;
            lastY = pos.y;
        });

        canvas.addEventListener('mousemove', (e) => {
            if (!isDrawing) return;
            const pos = getMousePos(e);
            
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            
            lastX = pos.x;
            lastY = pos.y;
        });

        canvas.addEventListener('mouseup', () => isDrawing = false);
        canvas.addEventListener('mouseout', () => isDrawing = false);

        // Touch events for mobile
        canvas.addEventListener('touchstart', (e) => {
            e.preventDefault();
            isDrawing = true;
            const pos = getTouchPos(e);
            lastX = pos.x;
            lastY = pos.y;
        });

        canvas.addEventListener('touchmove', (e) => {
            e.preventDefault();
            if (!isDrawing) return;
            const pos = getTouchPos(e);
            
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            
            lastX = pos.x;
            lastY = pos.y;
        });

        canvas.addEventListener('touchend', (e) => {
            e.preventDefault();
            isDrawing = false;
        });
    }

    function clearSignature(canvasId) {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
    }

    function generatePDFWithSignatures() {
        // Collect all signature data
        const signatures = [];
        const entries = document.getElementById('signatureEntries').children;
        
        for (let i = 0; i < entries.length; i++) {
            const entry = entries[i];
            const entryId = entry.id;
            const label = document.getElementById(`${entryId}-label`).value.trim();
            const name = document.getElementById(`${entryId}-name`).value.trim();
            const canvas = document.getElementById(`${entryId}-canvas`);
            
            if (label && name) {
                signatures.push({
                    label: label,
                    name: name,
                    signature: canvas.toDataURL('image/png')
                });
            }
        }

        // Build query parameters with filters and signatures
        const params = new URLSearchParams();
        Object.keys(currentFilters).forEach(key => {
            if (currentFilters[key]) {
                params.append(key, currentFilters[key]);
            }
        });
        
        // Add signatures as JSON
        if (signatures.length > 0) {
            params.append('signatures', JSON.stringify(signatures));
        }

        // Generate PDF URL and open in new tab
        const pdfUrl = `{{ route('repair.exportPDF') }}?${params.toString()}`;
        
        // Close modal and open PDF
        closeSignatureModal();
        window.open(pdfUrl, '_blank');
    }


    // --- Desktop delete selected logic ---
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('input[name="selected[]"]');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    let itemsToDelete = [];

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    function updateSelectedCount() {
        const selectedItems = document.querySelectorAll('input[name="selected[]"]:checked').length;
        document.getElementById('selectedCount').textContent = `${selectedItems} items selected`;
        deleteSelectedBtn.disabled = selectedItems === 0;
    }

    window.confirmDelete = function(id) {
        itemsToDelete = [id];
        window.itemsToDelete = itemsToDelete;
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    window.deleteSelected = function() {
        itemsToDelete = Array.from(document.querySelectorAll('input[name="selected[]"]:checked')).map(cb => cb.value);
        window.itemsToDelete = itemsToDelete;
        if (itemsToDelete.length === 0) {
            alert('Please select items to delete');
            return;
        }
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    window.closeDeleteModal = function() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    window.executeDelete = function() {
        if (!window.itemsToDelete || window.itemsToDelete.length === 0) {
            alert('No items selected for deletion');
            return;
        }

        fetch('{{ route("repair.destroyMultiple") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                ids: window.itemsToDelete
            })
        })
        .then(response => response.json())
        .then(data => {
            closeDeleteModal();
            if (data.success) {
                // Remove deleted rows from the table and cards
                window.itemsToDelete.forEach(id => {
                    const row = document.querySelector(`tr input[value="${id}"]`)?.closest('tr');
                    const remarksRow = document.getElementById(`remarks-${id}`);
                    const card = document.querySelector(`[data-id="${id}"]`);
                    if (row) row.remove();
                    if (remarksRow) remarksRow.remove();
                    if (card) card.remove();
                });

                // Reset checkboxes and update count
                const selectAll = document.getElementById('selectAll');
                if (selectAll) selectAll.checked = false;
                updateSelectedCount();
                selectedCardIds = [];

                // Create and show success message
                const successMessage = document.createElement('div');
                successMessage.className = 'mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700';
                successMessage.textContent = 'Request(s) deleted successfully';

                // Insert at the top of the content area
                const contentArea = document.querySelector('.flex-1.p-4') || document.querySelector('.flex-1.p-8') || document.querySelector('.flex-1');
                const existingMessage = contentArea.querySelector('.mb-4.p-4');
                if (existingMessage) {
                    existingMessage.remove();
                }
                contentArea.insertBefore(successMessage, contentArea.firstChild);

                // Remove success message after 3 seconds
                setTimeout(() => {
                    successMessage.remove();
                }, 3000);

                // Reload the page after successful deletion
                window.location.reload();
            } else {
                // Create and show error message
                const errorMessage = document.createElement('div');
                errorMessage.className = 'mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700';
                errorMessage.textContent = data.message || 'Error deleting request(s)';

                // Insert at the top of the content area
                const contentArea = document.querySelector('.flex-1.p-4') || document.querySelector('.flex-1.p-8') || document.querySelector('.flex-1');
                const existingMessage = contentArea.querySelector('.mb-4.p-4');
                if (existingMessage) {
                    existingMessage.remove();
                }
                contentArea.insertBefore(errorMessage, contentArea.firstChild);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            closeDeleteModal();

            // Create and show error message
            const errorMessage = document.createElement('div');
            errorMessage.className = 'mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700';
            errorMessage.textContent = 'An error occurred while deleting the request(s)';

            // Insert at the top of the content area
            const contentArea = document.querySelector('.flex-1.p-4') || document.querySelector('.flex-1.p-8') || document.querySelector('.flex-1');
            const existingMessage = contentArea.querySelector('.mb-4.p-4');
            if (existingMessage) {
                existingMessage.remove();
            }
            contentArea.insertBefore(errorMessage, contentArea.firstChild);
        });
    }

    // --- Mobile card selection logic ---
    let selectedCardIds = [];
    function toggleCardSelection(card) {
        const id = card.getAttribute('data-id');
        const idx = selectedCardIds.indexOf(id);
        if (idx === -1) {
            selectedCardIds.push(id);
            card.classList.add('ring-2', 'ring-red-600');
        } else {
            selectedCardIds.splice(idx, 1);
            card.classList.remove('ring-2', 'ring-red-600');
        }
        // Mobile cards now use the same selection system
    }
    
    // Clear all filters function
    window.clearFilters = function() {
        document.getElementById('requestStartDate').value = '';
        document.getElementById('requestEndDate').value = '';
        document.getElementById('registrationFilter').value = '';
        document.getElementById('completionStartDate').value = '';
        document.getElementById('completionEndDate').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('locationFilter').value = '';
        
        // Trigger filter to show all results
        filterHistory();
    }
</script>
@endsection
