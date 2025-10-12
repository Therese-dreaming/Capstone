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

    <!-- Page Header with Background Design -->
    <div class="mb-4 sm:mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-3 sm:p-4 md:p-6 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <div class="flex items-center">
                    <div class="bg-white/20 p-2 sm:p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4 flex-shrink-0">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2 truncate">Maintenance History</h1>
                        <p class="text-red-100 text-xs sm:text-sm md:text-lg">View maintenance records</p>
                    </div>
                </div>
                <button onclick="openSignatureModal()" class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-white/20 text-white font-medium rounded-lg hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 focus:ring-offset-2 focus:ring-offset-red-800 transition-colors duration-200 text-sm sm:text-base flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span class="hidden sm:inline">Preview PDF</span>
                    <span class="sm:hidden">Preview</span>
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-3 sm:p-4 md:p-6">

        <div class="mb-4 sm:mb-6">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-3 sm:mb-4">Filter Records</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4">
                <div class="grid grid-cols-2 gap-2 sm:col-span-2 lg:col-span-2 xl:col-span-2">
                    <div>
                        <label for="startDate" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" id="startDate" class="h-8 sm:h-9 w-full px-2 sm:px-3 py-0 text-xs sm:text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label for="endDate" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date" id="endDate" class="h-8 sm:h-9 w-full px-2 sm:px-3 py-0 text-xs sm:text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
                <div>
                    <label for="labFilter" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Laboratories</label>
                    <select id="labFilter" onchange="filterHistory()" class="h-8 sm:h-9 w-full px-2 sm:px-3 py-0 text-xs sm:text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        <option value="">All Labs</option>
                        @foreach(['401', '402', '403', '404', '405', '406'] as $lab)
                        <option value="{{ $lab }}">Lab {{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="statusFilter" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="statusFilter" onchange="filterHistory()" class="h-8 sm:h-9 w-full px-2 sm:px-3 py-0 text-xs sm:text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        <option value="">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div>
                    <label for="issueFilter" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Asset Issues</label>
                    <select id="issueFilter" onchange="filterHistory()" class="h-8 sm:h-9 w-full px-2 sm:px-3 py-0 text-xs sm:text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        <option value="">All Records</option>
                        <option value="with_issues">With Issues</option>
                        <option value="no_issues">No Issues</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Delete Actions -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3 sm:mb-4 gap-2 sm:gap-0">
            <div class="flex space-x-2 sm:space-x-4">
                <button onclick="deleteSelected()" class="text-xs sm:text-sm px-3 sm:px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50" disabled id="deleteSelectedBtn">
                    <span class="hidden sm:inline">Delete Selected</span>
                    <span class="sm:hidden">Delete</span>
                </button>
            </div>
            <div class="text-xs sm:text-sm text-gray-600" id="selectedCount">0 items selected</div>
        </div>

        <!-- Maintenance Records as Cards (Mobile/Tablet Only) -->
        <div class="grid grid-cols-1 gap-4 sm:gap-6 md:hidden" id="maintenanceCards">
            @forelse($maintenances as $maintenance)
            <div class="bg-white rounded-lg shadow p-3 sm:p-4 flex flex-col gap-2 sm:gap-3 border border-gray-200">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-0">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="mobile_selected[]" value="{{ $maintenance->id }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500 mobile-checkbox">
                        <span class="font-semibold text-red-800 text-sm sm:text-base">{{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('M d, Y') }}</span>
                    </div>
                    <div class="flex flex-col space-y-1">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap
                            {{ $maintenance->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($maintenance->status) }}
                        </span>
                        @if($maintenance->approval_status && $maintenance->approval_status !== 'not_required')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap
                            @if($maintenance->approval_status === 'approved') bg-blue-100 text-blue-800
                            @elseif($maintenance->approval_status === 'pending_approval') bg-orange-100 text-orange-800
                            @elseif($maintenance->approval_status === 'rejected') bg-red-100 text-red-800
                            @elseif($maintenance->approval_status === 'needs_rework') bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $maintenance->approval_status)) }}
                        </span>
                        @endif
                        @if($maintenance->admin_signature)
                        <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 w-fit mt-1">
                            <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Signed
                        </div>
                        @endif
                        @if($maintenance->rework_count > 0)
                        <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 w-fit mt-1 border border-amber-300">
                            <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reworked {{ $maintenance->rework_count }}x
                        </div>
                        @endif
                    </div>
                </div>
                <div class="text-xs sm:text-sm text-gray-700"><span class="font-semibold">Location:</span> <span class="break-words">{{ $maintenance->location ? ($maintenance->location->building . ' - Floor ' . $maintenance->location->floor . ' - Room ' . $maintenance->location->room_number) : 'N/A' }}</span></div>
                <div class="text-xs sm:text-sm text-gray-700"><span class="font-semibold">Technician:</span> <span class="break-words">{{ $maintenance->technician->name }}</span></div>
                @if($maintenance->asset_issues && is_array($maintenance->asset_issues) && !empty($maintenance->asset_issues))
                    <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 w-fit">
                        <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Issues Found
                    </div>
                @endif
                <div class="flex flex-wrap gap-2 mt-2">
                    <a href="{{ route('maintenance.show', $maintenance->id) }}" class="text-xs px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View
                    </a>
                    <button onclick="confirmDelete({{ $maintenance->id }})" class="text-xs px-3 py-1.5 bg-red-600 text-white rounded hover:bg-red-700 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center text-gray-500 py-8">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                <p class="text-sm sm:text-base">No maintenance history found</p>
            </div>
            @endforelse
        </div>

        <!-- Maintenance Records as Table (Desktop Only) -->
        <div class="overflow-x-auto hidden md:block">
            <table id="maintenanceTable" class="min-w-full divide-y divide-gray-200 text-xs md:text-sm">
                <thead class="bg-[#960106]">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Scheduled Date</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Location</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Technician</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Signature</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($maintenances as $maintenance)
                    <tr data-lab="{{ $maintenance->lab_number }}" data-status="{{ $maintenance->status }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="selected[]" value="{{ $maintenance->id }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $maintenance->location? ($maintenance->location->building . ' - Floor ' . $maintenance->location->floor . ' - Room ' . $maintenance->location->room_number) : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $maintenance->technician->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex flex-col space-y-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full w-fit
                                        {{ $maintenance->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($maintenance->status) }}
                                </span>
                                @if($maintenance->approval_status && $maintenance->approval_status !== 'not_required')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full w-fit
                                    @if($maintenance->approval_status === 'approved') bg-blue-100 text-blue-800
                                    @elseif($maintenance->approval_status === 'pending_approval') bg-orange-100 text-orange-800
                                    @elseif($maintenance->approval_status === 'rejected') bg-red-100 text-red-800
                                    @elseif($maintenance->approval_status === 'needs_rework') bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $maintenance->approval_status)) }}
                                </span>
                                @endif
                            </div>
                            @if($maintenance->asset_issues && is_array($maintenance->asset_issues) && !empty($maintenance->asset_issues))
                            <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Issues
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($maintenance->admin_signature)
                            <div class="flex flex-col space-y-1">
                                <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 w-fit">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Signed
                                </div>
                                @if($maintenance->approvedBy)
                                <div class="text-xs text-gray-500">
                                    By: {{ $maintenance->approvedBy->name }}
                                </div>
                                @endif
                            </div>
                            @else
                            <span class="text-xs text-gray-400">Not signed</span>
                            @endif
                            @if($maintenance->rework_count > 0)
                            <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-300">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reworked {{ $maintenance->rework_count }}x
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                <a href="{{ route('maintenance.show', $maintenance->id) }}" class="text-blue-600 hover:text-blue-800" title="View Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <button onclick="confirmDelete({{ $maintenance->id }})" class="text-red-600 hover:text-red-800" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No maintenance history found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $maintenances->links() }}
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" 
     class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4"
     style="z-index: 60;">
    <div class="p-4 sm:p-5 border w-full max-w-sm shadow-lg rounded-md bg-white">
            <div class="mt-2 sm:mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-red-100">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg leading-6 font-medium text-gray-900 mt-3 sm:mt-4">Delete Maintenance</h3>
                <div class="mt-2 px-2 sm:px-7 py-3">
                    <p class="text-xs sm:text-sm text-gray-500">Are you sure you want to delete the selected maintenance(s)? This action cannot be undone.</p>
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
    function filterHistory() {
        const labFilter = document.getElementById('labFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const issueFilter = document.getElementById('issueFilter').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (labFilter) params.append('lab', labFilter);
        if (statusFilter) params.append('status', statusFilter);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (issueFilter) params.append('issue', issueFilter);

        // Redirect to the filtered URL
        window.location.href = '{{ route("maintenance.history") }}?' + params.toString();
    }

    // Add event listeners for date inputs
    document.getElementById('startDate').addEventListener('change', filterHistory);
    document.getElementById('endDate').addEventListener('change', filterHistory);

    // Set initial filter values from URL parameters
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('lab')) {
            document.getElementById('labFilter').value = urlParams.get('lab');
        }
        if (urlParams.has('status')) {
            document.getElementById('statusFilter').value = urlParams.get('status');
        }
        if (urlParams.has('start_date')) {
            document.getElementById('startDate').value = urlParams.get('start_date');
        }
        if (urlParams.has('end_date')) {
            document.getElementById('endDate').value = urlParams.get('end_date');
        }
        if (urlParams.has('issue')) {
            document.getElementById('issueFilter').value = urlParams.get('issue');
        }
    });


    // Signature functionality
    let signatureCounter = 0;
    let currentFilters = {};

    function openSignatureModal() {
        // Store current filters
        currentFilters = {
            lab: document.getElementById('labFilter').value,
            status: document.getElementById('statusFilter').value,
            start_date: document.getElementById('startDate').value,
            end_date: document.getElementById('endDate').value,
            issue: document.getElementById('issueFilter').value
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
        const baseUrl = window.location.origin + window.location.pathname.replace('/maintenance/history', '');
        const pdfUrl = baseUrl + "/maintenance/history/export-pdf?" + params.toString();
        
        // Close modal and open PDF
        closeSignatureModal();
        window.open(pdfUrl, '_blank');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Delete functionality
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('input[name="selected[]"]');
        const mobileCheckboxes = document.querySelectorAll('input[name="mobile_selected[]"]');
        const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
        const selectedCount = document.getElementById('selectedCount');
        let itemsToDelete = [];

        // Desktop select all functionality
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelectedCount();
            });
        }

        // Desktop checkboxes
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        // Mobile checkboxes
        mobileCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        function updateSelectedCount() {
            const desktopSelected = document.querySelectorAll('input[name="selected[]"]:checked').length;
            const mobileSelected = document.querySelectorAll('input[name="mobile_selected[]"]:checked').length;
            const totalSelected = desktopSelected + mobileSelected;
            selectedCount.textContent = `${totalSelected} items selected`;
            deleteSelectedBtn.disabled = totalSelected === 0;
        }

        window.confirmDelete = function(id) {
            itemsToDelete = [id];
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }

        window.deleteSelected = function() {
            const desktopSelected = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
            const mobileSelected = Array.from(mobileCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
            itemsToDelete = [...desktopSelected, ...mobileSelected];
            if (itemsToDelete.length === 0) {
                alert('Please select items to delete');
                return;
            }
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }

        window.closeDeleteModal = function() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
        }

        window.executeDelete = function() {
            // For multiple items
            if (itemsToDelete.length > 1) {
                // Send request to delete multiple items
                fetch('{{ route("maintenance.destroyMultiple") }}', {
                        method: 'POST'
                        , headers: {
                            'Content-Type': 'application/json'
                            , 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            , 'Accept': 'application/json'
                        }
                        , body: JSON.stringify({
                            ids: itemsToDelete
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        closeDeleteModal();
                        if (data.success) {
                            // Remove the deleted rows from both desktop table and mobile cards
                            itemsToDelete.forEach(id => {
                                // Remove from desktop table
                                const desktopRow = document.querySelector(`input[name="selected[]"][value="${id}"]`);
                                if (desktopRow) {
                                    const row = desktopRow.closest('tr');
                                    if (row) row.remove();
                                }
                                
                                // Remove from mobile cards
                                const mobileCard = document.querySelector(`input[name="mobile_selected[]"][value="${id}"]`);
                                if (mobileCard) {
                                    const card = mobileCard.closest('.bg-white.rounded-lg.shadow');
                                    if (card) card.remove();
                                }
                            });

                            // Create and show success notification
                            showNotification('success', data.message || 'Maintenance records deleted successfully');

                            // Update selected count
                            updateSelectedCount();

                            // Check if table is empty and add "No records found" row if needed
                            const tbody = document.querySelector('tbody');
                            if (tbody && tbody.children.length === 0) {
                                const emptyRow = document.createElement('tr');
                                emptyRow.innerHTML = '<td colspan="6" class="px-6 py-4 text-center text-gray-500">No maintenance records found</td>';
                                tbody.appendChild(emptyRow);
                            }
                            
                            // Check if mobile cards container is empty
                            const mobileContainer = document.getElementById('maintenanceCards');
                            if (mobileContainer && mobileContainer.children.length === 0) {
                                mobileContainer.innerHTML = '<div class="col-span-full text-center text-gray-500 py-8"><svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg><p class="text-sm sm:text-base">No maintenance history found</p></div>';
                            }
                        } else {
                            // Show error notification
                            showNotification('error', data.message || 'Error deleting maintenance records');
                        }
                    })
                    .catch(error => {
                        closeDeleteModal();
                        // Show error notification for exceptions
                        showNotification('error', 'An error occurred while deleting maintenance records');
                    });
            } else {
                // For single item
                fetch('/maintenance/' + itemsToDelete[0], {
                        method: 'DELETE'
                        , headers: {
                            'Content-Type': 'application/json'
                            , 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            , 'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        closeDeleteModal();
                        if (data.success) {
                            // Remove the deleted item from both desktop table and mobile cards
                            const id = itemsToDelete[0];
                            
                            // Remove from desktop table
                            const desktopRow = document.querySelector(`input[name="selected[]"][value="${id}"]`);
                            if (desktopRow) {
                                const row = desktopRow.closest('tr');
                                if (row) row.remove();
                            }
                            
                            // Remove from mobile cards
                            const mobileCard = document.querySelector(`input[name="mobile_selected[]"][value="${id}"]`);
                            if (mobileCard) {
                                const card = mobileCard.closest('.bg-white.rounded-lg.shadow');
                                if (card) card.remove();
                            }

                            // Show success notification
                            showNotification('success', data.message || 'Maintenance record deleted successfully');

                            // Update selected count
                            updateSelectedCount();

                            // Check if table is empty
                            const tbody = document.querySelector('tbody');
                            if (tbody && tbody.children.length === 0) {
                                const emptyRow = document.createElement('tr');
                                emptyRow.innerHTML = '<td colspan="6" class="px-6 py-4 text-center text-gray-500">No maintenance records found</td>';
                                tbody.appendChild(emptyRow);
                            }
                            
                            // Check if mobile cards container is empty
                            const mobileContainer = document.getElementById('maintenanceCards');
                            if (mobileContainer && mobileContainer.children.length === 0) {
                                mobileContainer.innerHTML = '<div class="col-span-full text-center text-gray-500 py-8"><svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg><p class="text-sm sm:text-base">No maintenance history found</p></div>';
                            }
                        } else {
                            // Show error notification
                            showNotification('error', data.message || 'Error deleting maintenance record');
                        }
                    })
                    .catch(error => {
                        closeDeleteModal();
                        // Show error notification for exceptions
                        showNotification('error', 'An error occurred while deleting the maintenance record');
                    });
            }
        }

        // Add this helper function to show notifications
        function showNotification(type, message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = type === 'success' ?
                'mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700' :
                'mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700';
            notification.textContent = message;

            // Find the container to insert the notification
            const container = document.querySelector('.bg-white.rounded-lg.shadow-lg.p-6');

            // Insert at the top of the container
            container.insertBefore(notification, container.firstChild);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }

    });
</script>
@endsection
