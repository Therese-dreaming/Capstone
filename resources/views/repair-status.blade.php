@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8">
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

    <!-- Page Header with Background Design -->
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                        <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Repair Request Status</h1>
                        <p class="text-red-100 text-sm md:text-lg">Track and manage repair requests</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="bg-white rounded-xl shadow-lg p-4 md:p-6">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">Request Status</h2>
                <div class="flex flex-col md:flex-row gap-4 items-center">
                    @if(auth()->user()->group_id == 2)
                    <div class="flex items-center gap-3">
                        <label for="showAssigned" class="text-sm font-semibold text-gray-700">Show My Requests Only</label>
                        <input type="checkbox" id="showAssigned" class="h-5 w-5 text-red-600 rounded border-gray-300 focus:ring-red-500 focus:ring-2">
                    </div>
                    @endif
                    <div class="flex flex-col md:flex-row gap-3">
                        <div class="relative">
                            <input type="text" id="ticketSearch" placeholder="Search Ticket No." class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 w-full md:w-64">
                        </div>
                        <select id="statusFilter" class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 bg-white">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="pulled_out">Pulled Out</option>
                            <option value="disposed">Disposed</option>
                        </select>
                        <select id="urgencyFilter" class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 bg-white">
                            <option value="">All Urgency Levels</option>
                            <option value="1">Level 1 - Highest</option>
                            <option value="2">Level 2 - Medium</option>
                            <option value="3">Level 3 - Low</option>
                        </select>
                        <div class="flex gap-2">
                            <input type="date" id="dateFrom" class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                            <input type="date" id="dateTo" class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                        </div>
                        
                        <!-- Update Urgency Levels Button (Admin Only) -->
                        @if(auth()->user()->group_id == 1)
                        <button id="updateUrgencyBtn" class="px-4 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors duration-200 font-medium flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Update Urgency
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Container for dynamically inserted messages --}}
            <div id="message-container"></div>
        </div>

        @if($requests->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 px-4 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
            <div class="bg-white p-6 rounded-full mb-6">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Repair Requests</h3>
            <p class="text-gray-600 text-center max-w-sm">
                There are currently no repair requests to display. New requests will appear here once they are created.
            </p>
        </div>
        @else
        <!-- Cards for all screen sizes -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="requestsCards">
            @foreach($requests as $request)
            <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col gap-4 border border-gray-200 hover:shadow-xl transition-all duration-200">
                <!-- Header with Date and Status -->
                <div class="flex justify-between items-start">
                    <div class="space-y-1">
                        <div class="font-bold text-lg text-red-800">{{ \Carbon\Carbon::parse($request->created_at)->format('M j, Y') }}</div>
                        <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($request->created_at)->format('g:i A') }}</div>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <!-- Urgency Level Badge -->
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold whitespace-nowrap
                            @if($request->urgency_level == 1) bg-red-100 text-red-800 border border-red-200
                            @elseif($request->urgency_level == 2) bg-orange-100 text-orange-800 border border-orange-200
                            @else bg-blue-100 text-blue-800 border border-blue-200 @endif">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Level {{ $request->urgency_level ?? 3 }}
                        </span>
                        
                        <!-- Status Badge -->
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap
                            @if($request->status === 'completed') bg-green-100 text-green-800
                            @elseif($request->status === 'cancelled') bg-red-100 text-red-800
                            @elseif($request->status === 'in_progress') bg-blue-100 text-blue-800
                            @elseif($request->status === 'pulled_out') bg-yellow-100 text-yellow-800
                            @elseif($request->status === 'disposed') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            @if($request->status === 'in_progress')
                                In Progress
                            @elseif($request->status === 'pulled_out')
                                Pulled Out
                            @else
                                {{ ucfirst($request->status) }}
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Request Details -->
                <div class="space-y-3">
                    <div class="text-sm">
                        <span class="font-semibold text-gray-700">Ticket No.:</span> 
                        <span class="text-gray-900">{{ $request->ticket_number ?? 'N/A' }}</span>
                    </div>
                    
                    <div class="text-sm">
                        <span class="font-semibold text-gray-700">Item:</span>
                        @if($request->asset && $request->asset->name !== $request->equipment)
                            <div class="text-gray-500 text-xs mt-1">Original: {{ $request->equipment }}</div>
                            <div class="text-red-600 font-medium">Current: {{ $request->asset->name }}</div>
                        @else
                            <span class="text-gray-900">{{ $request->equipment }}</span>
                        @endif
                    </div>
                    
                    <div class="text-sm">
                        <span class="font-semibold text-gray-700">Location:</span> 
                        <span class="text-gray-900">{{ $request->building }} - {{ $request->floor }} - {{ $request->room }}</span>
                    </div>
                    
                    <div class="text-sm">
                        <span class="font-semibold text-gray-700">Technician:</span> 
                        <span class="text-gray-900">{{ $request->technician ? $request->technician->name : 'Not Assigned' }}</span>
                    </div>
                    
                    <div class="text-sm">
                        <span class="font-semibold text-gray-700">Time Started:</span> 
                        @if($request->time_started)
                            <div class="text-gray-900">{{ \Carbon\Carbon::parse($request->time_started)->format('M j, Y') }}</div>
                            <div class="text-gray-500">{{ \Carbon\Carbon::parse($request->time_started)->format('g:i A') }}</div>
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                    </div>
                    
                    <div class="text-sm">
                        <span class="font-semibold text-gray-700">Issue:</span> 
                        <div class="mt-1 text-gray-600 bg-gray-50 p-3 rounded-lg">{{ $request->issue }}</div>
                    </div>
                    
                    <div class="text-sm">
                        <span class="font-semibold text-gray-700">Asset:</span>
                        @if($request->asset)
                            <a href="{{ route('assets.index', ['search' => $request->asset->serial_number]) }}" class="font-bold text-red-600 hover:text-red-800 hover:underline transition-colors duration-200">{{ $request->asset->serial_number }}</a>
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2 mt-auto pt-4 border-t border-gray-200">
                    @if(auth()->user()->group_id == 1 || (auth()->user()->group_id == 2 && $request->technician_id == auth()->id()))
                    <button onclick="openUpdateModal('{{ $request->id }}')" class="bg-yellow-600 text-white p-2 rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-all duration-200" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    
                    @if(!$request->technician_id)
                    <button onclick="openAssignTechnicianModal('{{ $request->id }}')" class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200" title="Assign Technician">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </button>
                    @elseif(!$request->time_started)
                    <a href="{{ route('repair.identify-asset', $request->id) }}" class="bg-green-600 text-white p-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 inline-block" title="Complete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3-3m0 0l-3 3m3-3v12M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9" />
                        </svg>
                    </a>
                    <button onclick="startRepair('{{ $request->id }}')" class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200" title="Start Repair">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                    @else
                    <a href="{{ route('repair.completion-form', $request->id) }}" class="bg-green-600 text-white p-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 inline-block" title="Complete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </a>
                    @endif
                    
                    <button onclick="openCancelModal('{{ $request->id }}')" class="bg-red-600 text-white p-2 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200" title="Cancel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    @else
                    <span class="text-gray-500 italic text-sm">No actions available</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Pagination -->
        <div class="mt-8">
            {{ $requests->links() }}
        </div>
    </div>

    <!-- Update Modal -->
    <div id="updateModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center"
    style="z-index: 60;">
        <div class="p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="bg-red-800 text-white p-4 rounded-t-lg -mt-3 -mx-3 mb-4">
                    <h3 class="text-lg font-semibold">Update Request</h3>
                    <p class="text-red-100 text-sm">Modify the request details below</p>
                </div>

                <!-- Form Content -->
                <form id="updateForm" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Display Location (Read-only) -->
                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold" for="location_display">
                            Location
                        </label>
                        <p id="location_display" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 select-text"></p>
                    </div>

                    <!-- Technician Selection (Conditional) -->
                    @if(auth()->user()->group_id == 1)
                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold" for="technician_id">
                            Technician Assignment
                        </label>
                        <select id="technician_id" name="technician_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                            <option value="">Select a technician...</option>
                            @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    {{-- Display Technician for non-Admins --}}
                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold">
                            Assigned Technician
                        </label>
                         <p id="technician_display" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 select-text"></p>
                        <input type="hidden" name="technician_id" id="technician_id_hidden">
                    </div>
                    @endif

                    <!-- Status Selection -->
                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold" for="status">
                            Request Status
                        </label>
                        <select id="status" name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                            <option value="pending" class="text-gray-800">Pending</option>
                            <option value="in_progress" class="text-blue-800">In Progress</option>
                            <option value="completed" class="text-green-800">Completed</option>
                            <option value="cancelled" class="text-red-800">Cancelled</option>
                            <option value="pulled_out" class="text-yellow-800">Pulled Out</option>
                            <option value="disposed" class="text-red-800">Disposed</option>
                        </select>
                    </div>

                    <!-- Urgency Level Selection (Admin Only) -->
                    @if(auth()->user()->group_id == 1)
                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold" for="urgency_level">
                            Urgency Level
                        </label>
                        <select id="urgency_level" name="urgency_level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                            <option value="1" class="text-red-800">Level 1 - Highest (Ongoing Class/Event)</option>
                            <option value="2" class="text-orange-800">Level 2 - Medium (Over 1 Week Old)</option>
                            <option value="3" class="text-blue-800">Level 3 - Low (New Request)</option>
                        </select>
                        <p class="text-xs text-gray-500">Automatically calculated, but can be manually adjusted</p>
                    </div>
                    @endif

                    <!-- Issue Textarea -->
                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold" for="issue">
                            Issue
                        </label>
                        <textarea id="issue" name="issue" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 resize-none" placeholder="Enter the issue description..."></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeUpdateModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-800 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                            Update Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50" style="z-index: 60;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Confirm Delete</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to delete this request?</p>
                </div>
                <div class="flex justify-center gap-4 mt-4">
                    <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                    <button onclick="executeDelete()" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div id="cancelModal" 
     class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center"
     style="z-index: 60;">
    <div class="p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="bg-red-800 text-white p-4 rounded-t-lg -mt-3 -mx-3 mb-4">
                    <h3 class="text-lg font-semibold">Cancel Request</h3>
                    <p class="text-red-100 text-sm">Provide a reason for cancellation</p>
                </div>

                <form id="cancelForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="cancelled">
                    <input type="hidden" name="date_finished" id="cancelDateFinished">
                    <input type="hidden" name="time_finished" id="cancelTimeFinished">

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2" for="cancel_remarks">
                            Cancellation Reason
                        </label>
                        <textarea name="remarks" id="cancel_remarks" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200" placeholder="Enter reason for cancellation..." required></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeCancelModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                            Confirm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Start Repair Confirmation Modal -->
    <div id="startRepairModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center"
    style="z-index: 60;">
        <div class="top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Start Repair</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to start this repair?</p>
                </div>
                <div class="flex justify-center gap-4 mt-4">
                    <button onclick="closeStartRepairModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        No
                    </button>
                    <button type="button" id="confirmStartRepairBtn" onclick="confirmStartRepair()" class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 flex items-center">
                        <span class="mr-2">Yes</span>
                        <svg class="animate-spin h-5 w-5 text-white hidden" id="startRepairSpinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Assign Technician Modal -->
    <div id="assignTechnicianModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50" style="z-index: 60;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="bg-blue-800 text-white p-4 rounded-t-lg -mt-3 -mx-3 mb-4">
                    <h3 class="text-lg font-semibold">Assign Technician</h3>
                    <p class="text-blue-100 text-sm">Select a technician for this request</p>
                </div>

                <form id="assignTechnicianForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="pending">
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2" for="technician_id">
                            Select Technician
                        </label>
                        <select name="technician_id" id="assign_technician_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" required>
                            <option value="">Select a technician...</option>
                            @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeAssignTechnicianModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                            Assign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Urgency Levels Modal -->
    <div id="updateUrgencyModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center" style="z-index: 60;">
        <div class="p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="bg-orange-600 text-white p-4 rounded-t-lg -mt-3 -mx-3 mb-4">
                    <h3 class="text-lg font-semibold">Update Urgency Levels</h3>
                    <p class="text-orange-100 text-sm">Recalculate urgency levels for all pending requests</p>
                </div>

                <!-- Content -->
                <div class="mb-6">
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                        <h4 class="font-semibold text-orange-800 mb-2">Urgency Level Criteria:</h4>
                        <ul class="text-sm text-orange-700 space-y-1">
                            <li class="flex items-start">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200 mr-2 mt-0.5">Level 1</span>
                                <span>Highest - Ongoing class/event</span>
                            </li>
                            <li class="flex items-start">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 border border-orange-200 mr-2 mt-0.5">Level 2</span>
                                <span>Medium - Over 1 week old</span>
                            </li>
                            <li class="flex items-start">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200 mr-2 mt-0.5">Level 3</span>
                                <span>Low - New request (within week)</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-2">What this will do:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Scan all pending repair requests</li>
                            <li>• Check for ongoing activities</li>
                            <li>• Calculate request age</li>
                            <li>• Update urgency levels automatically</li>
                            <li>• Only affects pending requests (completed/cancelled excluded)</li>
                        </ul>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeUpdateUrgencyModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" id="confirmUpdateUrgencyBtn" onclick="confirmUpdateUrgency()" class="px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors duration-200 flex items-center">
                        <span class="mr-2">Update Urgency</span>
                        <svg class="animate-spin h-4 w-4 text-white hidden" id="updateUrgencySpinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

            <script>
                function openUpdateModal(requestId) {
                    const modal = document.getElementById('updateModal');
                    const form = document.getElementById('updateForm');
                    
                    // Use relative URLs
                    const dataUrl = `repair-requests/${requestId}/data`;
                    form.action = `repair-requests/${requestId}`;

                    // Get CSRF token
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // Fetch request data via AJAX
                    fetch(dataUrl, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Populate modal fields with fetched data
                        // Fix location display by constructing the full location string
                        const locationString = `${data.building || ''} - ${data.floor || ''} - ${data.room || ''}`.replace(/^ - | - $/g, '').trim();
                        document.getElementById('location_display').textContent = locationString || 'Location not specified';

                        const technicianSelect = document.getElementById('technician_id');
                        const technicianDisplay = document.getElementById('technician_display');
                        const technicianIdHidden = document.getElementById('technician_id_hidden');

                        if (technicianSelect) { // If technician select is visible (for Admins/Technicians)
                            technicianSelect.value = data.technician_id || '';
                        } else if (technicianDisplay) { // If technician display is visible (for Secretaries)
                            technicianDisplay.textContent = data.technician ? data.technician.name : 'Not Assigned';
                            technicianIdHidden.value = data.technician_id || ''; // Keep hidden input for submission
                        }

                        const statusSelect = document.getElementById('status');
                        // Select the correct status option, defaulting to 'pending' if status is not in the list
                        if (statusSelect.querySelector(`option[value="${data.status}"]`)) {
                            statusSelect.value = data.status;
                        } else {
                            statusSelect.value = 'pending'; // Default to pending if current status isn't an option
                        }

                        document.getElementById('issue').value = data.issue;

                        // Set urgency level if the field exists (admin only)
                        const urgencyLevelSelect = document.getElementById('urgency_level');
                        if (urgencyLevelSelect) {
                            urgencyLevelSelect.value = data.urgency_level || 3;
                        }

                        // Show the modal after data is populated
                        if (modal) {
                            modal.classList.remove('hidden');
                            modal.classList.add('flex');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching request data:', error);
                        alert('Could not load request data. Please try again.');
                    });
                }

                function closeUpdateModal() {
                    const modal = document.getElementById('updateModal');
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    // Clear fields on close (optional, but good practice)
                     document.getElementById('updateForm').reset();
                }

                function openDeleteModal(requestId) {
                    const modal = document.getElementById('deleteModal');
                    const form = document.getElementById('deleteForm');
                    form.action = `/repair-requests/delete/${requestId}`; // Updated route

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }

                function closeDeleteModal() {
                    const modal = document.getElementById('deleteModal');
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                function executeDelete() {
                    const form = document.getElementById('deleteForm');
                    const requestId = form.action.split('/').pop();
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: form.innerHTML // Send the form content to trigger the CSRF token
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            closeDeleteModal();
                            // Show success message
                            const successMessage = document.createElement('div');
                            successMessage.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6';
                            successMessage.innerHTML = `
                                <strong class="font-bold">Success!</strong>
                                <span class="block sm:inline">${data.message}</span>
                            `;
                            document.getElementById('message-container').appendChild(successMessage);
                            // Auto-hide the message after 5 seconds
                            setTimeout(() => {
                                successMessage.remove();
                            }, 5000);
                            
                            // Refresh the page to show updated status
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            throw new Error(data.message || 'Failed to delete request');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || 'An error occurred while deleting the request');
                    });
                }

                // Update the openCancelModal function
                function openCancelModal(requestId) {
                    const modal = document.getElementById('cancelModal');
                    const form = document.getElementById('cancelForm');
                    form.action = `/repair-requests/${requestId}`;

                    // Set current date and time
                    const now = new Date();
                    const dateStr = now.getFullYear() + '-' +
                        String(now.getMonth() + 1).padStart(2, '0') + '-' +
                        String(now.getDate()).padStart(2, '0');
                    const timeStr = String(now.getHours()).padStart(2, '0') + ':' +
                        String(now.getMinutes()).padStart(2, '0');

                    document.getElementById('cancelDateFinished').value = dateStr;
                    document.getElementById('cancelTimeFinished').value = timeStr;

                    // Clear any previous remarks
                    document.getElementById('cancel_remarks').value = '';

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }

                function closeCancelModal() {
                    const modal = document.getElementById('cancelModal');
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                // Update the cancel form handler
                document.getElementById('cancelForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    formData.append('_method', 'PUT');
                    formData.append('status', 'cancelled');
                    formData.append('date_finished', document.getElementById('cancelDateFinished').value);
                    formData.append('time_finished', document.getElementById('cancelTimeFinished').value);
                    formData.append('remarks', document.getElementById('cancel_remarks').value);

                    const requestId = this.action.split('/').pop();

                    fetch(`repair-requests/${requestId}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            closeCancelModal();
                            // Show success message
                            const successMessage = document.createElement('div');
                            successMessage.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6';
                            successMessage.innerHTML = `
                                <strong class="font-bold">Success!</strong>
                                <span class="block sm:inline">${data.message}</span>
                            `;
                            document.getElementById('message-container').appendChild(successMessage);
                            // Auto-hide the message after 5 seconds
                            setTimeout(() => {
                                successMessage.remove();
                            }, 5000);
                            
                            // Refresh the page to show updated status
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            throw new Error(data.message || 'Failed to cancel request');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || 'An error occurred while cancelling the request');
                    });
                });

                // Add these new functions for start repair modal
                function startRepair(requestId) {
                    const modal = document.getElementById('startRepairModal');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    // Store the requestId in a data attribute on the modal
                    modal.setAttribute('data-request-id', requestId);
                }

                function closeStartRepairModal() {
                    const modal = document.getElementById('startRepairModal');
                    const confirmBtn = document.getElementById('confirmStartRepairBtn');
                    const spinner = document.getElementById('startRepairSpinner');
                    
                    // Reset button state
                    confirmBtn.disabled = false;
                    confirmBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    spinner.classList.add('hidden');
                    
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                function confirmStartRepair() {
                    const confirmBtn = document.getElementById('confirmStartRepairBtn');
                    const spinner = document.getElementById('startRepairSpinner');
                    
                    // Disable button and show spinner
                    confirmBtn.disabled = true;
                    confirmBtn.classList.add('opacity-75', 'cursor-not-allowed');
                    spinner.classList.remove('hidden');
                    
                    const modal = document.getElementById('startRepairModal');
                    const requestId = modal.getAttribute('data-request-id');
                    
                    const formData = new FormData();
                    formData.append('_method', 'PUT');
                    
                    // Format current date and time
                    const now = new Date();
                    const dateStr = now.getFullYear() + '-' +
                        String(now.getMonth() + 1).padStart(2, '0') + '-' +
                        String(now.getDate()).padStart(2, '0');
                    const timeStr = String(now.getHours()).padStart(2, '0') + ':' +
                        String(now.getMinutes()).padStart(2, '0');
                    
                    const timeStarted = `${dateStr} ${timeStr}`;
                    
                    formData.append('time_started', timeStarted);
                    formData.append('status', 'in_progress');

                    // Get CSRF token
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // Use relative URL
                    const url = `repair-requests/${requestId}`;

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                    .then(async response => {
                        const responseText = await response.text();
                        
                        if (!response.ok) {
                            let errorMessage = 'Failed to start repair';
                            try {
                                const errorData = JSON.parse(responseText);
                                errorMessage = errorData.message || errorMessage;
                            } catch (e) {
                                if (responseText.includes('<!DOCTYPE')) {
                                    const errorMatch = responseText.match(/<title>(.*?)<\/title>/);
                                    if (errorMatch) {
                                        errorMessage = errorMatch[1];
                                    }
                                }
                            }
                            throw new Error(errorMessage);
                        }
                        
                        try {
                            return JSON.parse(responseText);
                        } catch (e) {
                            throw new Error('Invalid server response');
                        }
                    })
                    .then(data => {
                        if (data.success) {
                            closeStartRepairModal();
                            // Show success message
                            const successMessage = document.createElement('div');
                            successMessage.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6';
                            successMessage.innerHTML = `
                                <strong class="font-bold">Success!</strong>
                                <span class="block sm:inline">${data.message}</span>
                            `;
                            document.getElementById('message-container').appendChild(successMessage);
                            
                            // Auto-hide the message after 5 seconds
                            setTimeout(() => {
                                successMessage.remove();
                            }, 5000);
                            
                            // Refresh the page to show updated buttons
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            throw new Error(data.message || 'Failed to start repair');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Show error message
                        const errorMessage = document.createElement('div');
                        errorMessage.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6';
                        errorMessage.innerHTML = `
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">${error.message}</span>
                        `;
                        document.getElementById('message-container').appendChild(errorMessage);
                        
                        // Auto-hide the message after 5 seconds
                        setTimeout(() => {
                            errorMessage.remove();
                        }, 5000);
                    })
                    .finally(() => {
                        // Re-enable button and hide spinner
                        confirmBtn.disabled = false;
                        confirmBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                        spinner.classList.add('hidden');
                    });
                }

                // Add these new functions for assign technician modal
                function openAssignTechnicianModal(requestId) {
                    const modal = document.getElementById('assignTechnicianModal');
                    const form = document.getElementById('assignTechnicianForm');
                    form.action = `repair-requests/${requestId}`;
                    
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }

                function closeAssignTechnicianModal() {
                    const modal = document.getElementById('assignTechnicianModal');
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                // Update Urgency Modal Functions
                function openUpdateUrgencyModal() {
                    const modal = document.getElementById('updateUrgencyModal');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }

                function closeUpdateUrgencyModal() {
                    const modal = document.getElementById('updateUrgencyModal');
                    const confirmBtn = document.getElementById('confirmUpdateUrgencyBtn');
                    const spinner = document.getElementById('updateUrgencySpinner');
                    
                    // Reset button state
                    confirmBtn.disabled = false;
                    confirmBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    spinner.classList.add('hidden');
                    
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                function confirmUpdateUrgency() {
                    const confirmBtn = document.getElementById('confirmUpdateUrgencyBtn');
                    const spinner = document.getElementById('updateUrgencySpinner');
                    
                    // Disable button and show spinner
                    confirmBtn.disabled = true;
                    confirmBtn.classList.add('opacity-75', 'cursor-not-allowed');
                    spinner.classList.remove('hidden');
                    
                    fetch('{{ route("repair-requests.update-urgency-levels") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            closeUpdateUrgencyModal();
                            // Show success message
                            const messageContainer = document.getElementById('message-container');
                            messageContainer.innerHTML = `
                                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center" role="alert">
                                    <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <div class="font-semibold">Success!</div>
                                        <div>${data.message}</div>
                                    </div>
                                </div>
                            `;
                            
                            // Reload the page to show updated urgency levels
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            throw new Error(data.message || 'Failed to update urgency levels');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Show error message
                        const messageContainer = document.getElementById('message-container');
                        messageContainer.innerHTML = `
                            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-center" role="alert">
                                <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <div class="font-semibold">Error!</div>
                                    <div>${error.message}</div>
                                </div>
                            </div>
                        `;
                    })
                    .finally(() => {
                        // Re-enable button and hide spinner
                        confirmBtn.disabled = false;
                        confirmBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                        spinner.classList.add('hidden');
                    });
                }

                // Add event listener for assign technician form
                document.getElementById('assignTechnicianForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    formData.append('_method', 'PUT');

                    const requestId = this.action.split('/').pop();

                    fetch(`repair-requests/${requestId}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            closeAssignTechnicianModal();
                            // Show success message
                            const successMessage = document.createElement('div');
                            successMessage.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6';
                            successMessage.innerHTML = `
                                <strong class="font-bold">Success!</strong>
                                <span class="block sm:inline">${data.message}</span>
                            `;
                            document.getElementById('message-container').appendChild(successMessage);
                            // Auto-hide the message after 5 seconds
                            setTimeout(() => {
                                successMessage.remove();
                            }, 5000);
                            
                            // Refresh the page to show updated status
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            throw new Error(data.message || 'Failed to assign technician');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || 'An error occurred while assigning the technician');
                    });
                });

            </script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('ticketSearch');
                    const statusFilter = document.getElementById('statusFilter');
                    const urgencyFilter = document.getElementById('urgencyFilter');
                    const dateFrom = document.getElementById('dateFrom');
                    const dateTo = document.getElementById('dateTo');
                    const cards = document.querySelectorAll('#requestsCards > div.bg-white');
                    const showAssignedCheckbox = document.getElementById('showAssigned');

                    // Function to filter cards
                    function filterCards() {
                        const searchTerm = searchInput.value.toLowerCase().trim();
                        const selectedStatus = statusFilter.value.toLowerCase();
                        const selectedUrgency = urgencyFilter.value;
                        const fromDate = dateFrom.value ? new Date(dateFrom.value) : null;
                        const toDate = dateTo.value ? new Date(dateTo.value) : null;
                        const showAssignedOnly = showAssignedCheckbox ? showAssignedCheckbox.checked : false;
                        const currentUser = '{{ auth()->user()->name }}';

                        cards.forEach(card => {
                            let ticketNo = '';
                            let item = '';
                            let location = '';
                            let technician = '';
                            let status = '';
                            let date = '';

                            // Get all text content from the card
                            card.querySelectorAll('.text-sm').forEach(div => {
                                const text = div.textContent.toLowerCase();
                                if (text.includes('ticket no.')) {
                                    ticketNo = text.replace('ticket no.:', '').trim();
                                } else if (text.includes('item:')) {
                                    item = text.replace('item:', '').trim();
                                } else if (text.includes('location:')) {
                                    location = text.replace('location:', '').trim();
                                } else if (text.includes('technician:')) {
                                    technician = text.replace('technician:', '').trim();
                                }
                            });

                            // Get status from the badge
                            const statusBadge = card.querySelector('.inline-flex.items-center');
                            if (statusBadge) {
                                status = statusBadge.textContent.trim().toLowerCase();
                                // Convert "in progress" to "in_progress" for comparison
                                if (status === 'in progress') {
                                    status = 'in_progress';
                                }
                            }

                            // Get urgency level from the urgency badge
                            let urgencyLevel = '';
                            const urgencyBadge = card.querySelector('.inline-flex.items-center:first-child');
                            if (urgencyBadge) {
                                const urgencyText = urgencyBadge.textContent.trim();
                                const urgencyMatch = urgencyText.match(/Level (\d+)/);
                                if (urgencyMatch) {
                                    urgencyLevel = urgencyMatch[1];
                                }
                            }

                            // Get date from the first line
                            const dateElement = card.querySelector('.font-semibold.text-red-800');
                            if (dateElement) {
                                // Parse the date string (e.g., "Jun 10, 2025")
                                const dateStr = dateElement.textContent.trim();
                                const [month, day, year] = dateStr.split(/[\s,]+/);
                                const monthIndex = new Date(`${month} 1, 2000`).getMonth();
                                date = new Date(year, monthIndex, parseInt(day));
                            }

                            // Check if card matches all filters
                            const matchesSearch = searchTerm === '' || 
                                ticketNo.includes(searchTerm) || 
                                item.includes(searchTerm) || 
                                location.includes(searchTerm);

                            const matchesStatus = selectedStatus === '' || 
                                status === selectedStatus.toLowerCase();

                            const matchesUrgency = selectedUrgency === '' || 
                                urgencyLevel === selectedUrgency;

                            // Compare dates by setting time to midnight for accurate date comparison
                            const matchesDate = (!fromDate || (date && date.setHours(0,0,0,0) >= fromDate.setHours(0,0,0,0))) && 
                                              (!toDate || (date && date.setHours(0,0,0,0) <= toDate.setHours(0,0,0,0)));

                            const matchesAssigned = !showAssignedOnly || 
                                technician === currentUser.toLowerCase();

                            // Show/hide card based on all filters
                            card.style.display = (matchesSearch && matchesStatus && matchesUrgency && matchesDate && matchesAssigned) ? '' : 'none';
                        });

                        // Update pagination info
                        updatePaginationInfo();
                    }

                    // Function to update pagination info
                    function updatePaginationInfo() {
                        const visibleCards = Array.from(cards).filter(card => card.style.display !== 'none');
                        const totalVisible = visibleCards.length;
                        
                        // Update the pagination info if it exists
                        const tableInfo = document.getElementById('tableInfo');
                        if (tableInfo) {
                            const startEntry = document.getElementById('startEntry');
                            const endEntry = document.getElementById('endEntry');
                            const totalEntries = document.getElementById('totalEntries');
                            
                            if (startEntry && endEntry && totalEntries) {
                                startEntry.textContent = totalVisible > 0 ? '1' : '0';
                                endEntry.textContent = totalVisible;
                                totalEntries.textContent = totalVisible;
                            }
                        }
                    }

                    // Add event listeners
                    if (searchInput) {
                        searchInput.addEventListener('input', filterCards);
                    }

                    if (statusFilter) {
                        statusFilter.addEventListener('change', filterCards);
                    }

                    if (urgencyFilter) {
                        urgencyFilter.addEventListener('change', filterCards);
                    }

                    if (dateFrom) {
                        dateFrom.addEventListener('change', filterCards);
                    }

                    if (dateTo) {
                        dateTo.addEventListener('change', filterCards);
                    }

                    if (showAssignedCheckbox) {
                        showAssignedCheckbox.addEventListener('change', filterCards);
                    }

                    // Initial filter
                    filterCards();

                    // Auto-hide session messages after 5 seconds
                    const sessionError = document.getElementById('session-error');
                    const sessionSuccess = document.getElementById('session-success');
                    
                    if (sessionError) {
                        setTimeout(() => {
                            sessionError.style.transition = 'opacity 0.5s ease-out';
                            sessionError.style.opacity = '0';
                            setTimeout(() => {
                                sessionError.remove();
                            }, 500);
                        }, 5000);
                    }
                    
                    if (sessionSuccess) {
                        setTimeout(() => {
                            sessionSuccess.style.transition = 'opacity 0.5s ease-out';
                            sessionSuccess.style.opacity = '0';
                            setTimeout(() => {
                                sessionSuccess.remove();
                            }, 500);
                        }, 5000);
                    }

                    // Urgency Level Update Functionality
                    const updateUrgencyBtn = document.getElementById('updateUrgencyBtn');
                    if (updateUrgencyBtn) {
                        updateUrgencyBtn.addEventListener('click', function() {
                            openUpdateUrgencyModal();
                        });
                    }
                });
            </script>

        </div>
    </div>
</div>
@endsection

