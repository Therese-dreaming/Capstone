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

    <!-- Page Header -->
    <div class="mb-4 sm:mb-6 md:mb-8">
        <div class="bg-orange-600 rounded-xl shadow-lg p-3 sm:p-4 md:p-6 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <div class="flex items-center">
                    <div class="bg-white/20 p-2 sm:p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4 flex-shrink-0">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2 truncate">Pending Approval</h1>
                        <p class="text-orange-100 text-xs sm:text-sm md:text-lg">Review and approve completed maintenance tasks</p>
                    </div>
                </div>
                <div class="bg-white/20 px-4 py-2 rounded-lg">
                    <span class="text-white font-semibold">{{ $maintenances->total() }} Tasks Pending</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-3 sm:p-4 md:p-6">
        <!-- Filters -->
        <div class="mb-4 sm:mb-6">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-3 sm:mb-4">Filter Tasks</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div>
                    <label for="labFilter" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Laboratory</label>
                    <select id="labFilter" onchange="filterTasks()" class="h-8 sm:h-9 w-full px-2 sm:px-3 py-0 text-xs sm:text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Labs</option>
                        @foreach(['401', '402', '403', '404', '405', '406'] as $lab)
                        <option value="{{ $lab }}">Lab {{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="technicianFilter" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Technician</label>
                    <select id="technicianFilter" onchange="filterTasks()" class="h-8 sm:h-9 w-full px-2 sm:px-3 py-0 text-xs sm:text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Technicians</option>
                        @foreach($technicians as $technician)
                        <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="startDate" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" id="startDate" onchange="filterTasks()" class="h-8 sm:h-9 w-full px-2 sm:px-3 py-0 text-xs sm:text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label for="endDate" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date" id="endDate" onchange="filterTasks()" class="h-8 sm:h-9 w-full px-2 sm:px-3 py-0 text-xs sm:text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                </div>
            </div>
        </div>

        <!-- Pending Tasks Cards (Mobile/Tablet) -->
        <div class="grid grid-cols-1 gap-4 sm:gap-6 md:hidden" id="pendingCards">
            @forelse($maintenances as $maintenance)
            <div class="bg-white rounded-lg shadow border border-orange-200 p-3 sm:p-4">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-semibold text-gray-900 text-sm sm:text-base">{{ $maintenance->maintenance_task }}</h3>
                        <p class="text-xs sm:text-sm text-gray-600 mt-1">
                            <span class="font-medium">Location:</span> 
                            {{ $maintenance->location ? ($maintenance->location->building . ' - Floor ' . $maintenance->location->floor . ' - Room ' . $maintenance->location->room_number) : 'N/A' }}
                        </p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 whitespace-nowrap">
                        Pending Review
                    </span>
                </div>
                
                <div class="space-y-2 text-xs sm:text-sm text-gray-600 mb-4">
                    <div><span class="font-medium">Technician:</span> {{ $maintenance->technician->name }}</div>
                    <div><span class="font-medium">Completed:</span> {{ $maintenance->completed_at ? $maintenance->completed_at->format('M d, Y g:i A') : 'Not completed' }}</div>
                    @if($maintenance->notes)
                    <div><span class="font-medium">Notes:</span> {{ $maintenance->notes }}</div>
                    @endif
                    @if($maintenance->asset_issues && count($maintenance->asset_issues) > 0)
                    <div class="bg-red-50 p-2 rounded border-l-4 border-red-400">
                        <span class="font-medium text-red-800">Asset Issues Reported:</span>
                        <ul class="mt-1 text-red-700">
                            @foreach($maintenance->asset_issues as $issue)
                            <li class="text-xs">• {{ $issue['issue_description'] ?? 'No description' }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('maintenance.show', $maintenance->id) }}" 
                       class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-xs sm:text-sm flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View Details
                    </a>
                    <button onclick="openApprovalModal({{ $maintenance->id }}, '{{ $maintenance->maintenance_task }}', '{{ $maintenance->technician->name }}')" 
                            class="flex-1 px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-xs sm:text-sm flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Review & Approve
                    </button>
                    <button onclick="openRejectionModal({{ $maintenance->id }}, '{{ $maintenance->maintenance_task }}', '{{ $maintenance->technician->name }}')" 
                            class="flex-1 px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-xs sm:text-sm flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Reject
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center text-gray-500 py-8">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm sm:text-base">No tasks pending approval</p>
            </div>
            @endforelse
        </div>

        <!-- Pending Tasks Table (Desktop) -->
        <div class="overflow-x-auto hidden md:block">
            <table class="min-w-full divide-y divide-gray-200 text-xs md:text-sm">
                <thead class="bg-orange-600">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Task</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Location</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Technician</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Completed</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($maintenances as $maintenance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $maintenance->maintenance_task }}</div>
                            @if($maintenance->notes)
                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($maintenance->notes, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $maintenance->location ? ($maintenance->location->building . ' - Floor ' . $maintenance->location->floor . ' - Room ' . $maintenance->location->room_number) : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $maintenance->technician->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $maintenance->completed_at ? $maintenance->completed_at->format('M d, Y g:i A') : 'Not completed' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                Pending Review
                            </span>
                            @if($maintenance->asset_issues && count($maintenance->asset_issues) > 0)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Issues
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                <a href="{{ route('maintenance.show', $maintenance->id) }}" 
                                   class="text-blue-600 hover:text-blue-800" title="View Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <button onclick="openApprovalModal({{ $maintenance->id }}, '{{ $maintenance->maintenance_task }}', '{{ $maintenance->technician->name }}')" 
                                        class="text-green-600 hover:text-green-800" title="Review & Approve">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                                <button onclick="openRejectionModal({{ $maintenance->id }}, '{{ $maintenance->maintenance_task }}', '{{ $maintenance->technician->name }}')" 
                                        class="text-red-600 hover:text-red-800" title="Reject">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No tasks pending approval</td>
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
</div>

<!-- Approval Modal -->
<div id="approvalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative mx-auto border w-full max-w-2xl shadow-xl rounded-xl bg-white max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="bg-green-600 rounded-t-xl px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 p-2 rounded-full mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white">Review & Approve Maintenance</h3>
                </div>
                <button onclick="closeModal('approvalModal')" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6">

            <form id="approvalForm" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Task Details</h4>
                        <div id="approvalTaskDetails" class="text-sm text-gray-600"></div>
                    </div>

                    <!-- Admin Signature Section -->
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-indigo-100 p-2 rounded-full mr-3">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800">Admin Signature *</h4>
                        </div>
                        
                        <div class="bg-white rounded-lg border-2 border-indigo-200 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-700">Sign below to approve</span>
                                <button type="button" onclick="clearApprovalSignature()" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                    Clear
                                </button>
                            </div>
                            <div class="border-2 border-dashed border-indigo-300 rounded-lg bg-white">
                                <canvas id="approvalSignatureCanvas" width="500" height="150" class="block w-full cursor-crosshair rounded-lg"></canvas>
                            </div>
                            <input type="hidden" name="admin_signature" id="approvalSignatureData" required>
                            <div class="mt-2 flex items-center text-xs text-gray-500">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Your signature confirms the approval of this maintenance task
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-100 p-2 rounded-full mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800">Admin Notes</h4>
                            <span class="ml-2 text-sm text-gray-500">(optional)</span>
                        </div>
                        
                        <div class="bg-white rounded-lg border border-blue-200 p-4">
                            <div class="flex items-center mb-3">
                                <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Additional Comments & Observations</span>
                            </div>
                            <textarea name="admin_notes" rows="4" class="w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 resize-none" placeholder="Share any additional feedback, observations, or recommendations for the technician..."></textarea>
                            <div class="mt-2 flex items-center text-xs text-gray-500">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                This feedback will be shared with the technician
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Modal Footer -->
        <div class="bg-gray-50 rounded-b-xl px-6 py-4 flex justify-end space-x-3">
            <button type="button" onclick="closeModal('approvalModal')" class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors font-medium">
                Cancel
            </button>
            <button type="submit" form="approvalForm" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors font-medium flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Approve & Complete
            </button>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative mx-auto border w-full max-w-2xl shadow-xl rounded-xl bg-white max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="bg-red-600 rounded-t-xl px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 p-2 rounded-full mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white">Reject Maintenance Task</h3>
                </div>
                <button onclick="closeModal('rejectionModal')" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6">

            <form id="rejectionForm" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="space-y-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Task Details</h4>
                        <div id="rejectionTaskDetails" class="text-sm text-gray-600"></div>
                    </div>

                    <!-- Quality Issues Section -->
                    <div class="bg-gradient-to-br from-red-50 to-orange-50 border border-red-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-red-100 p-2 rounded-full mr-3">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800">Quality Issues</h4>
                            <span class="ml-2 text-sm text-gray-500">(select all that apply)</span>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="flex items-center p-3 bg-white rounded-lg border border-red-200 hover:border-red-300 hover:bg-red-50 transition-all cursor-pointer group">
                                <input type="checkbox" name="quality_issues[]" value="incomplete_tasks" class="w-4 h-4 text-red-600 border-red-300 rounded focus:ring-red-500">
                                <div class="ml-3 flex items-center">
                                    <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Incomplete tasks</span>
                                </div>
                            </label>
                            
                            <label class="flex items-center p-3 bg-white rounded-lg border border-red-200 hover:border-red-300 hover:bg-red-50 transition-all cursor-pointer group">
                                <input type="checkbox" name="quality_issues[]" value="poor_quality" class="w-4 h-4 text-red-600 border-red-300 rounded focus:ring-red-500">
                                <div class="ml-3 flex items-center">
                                    <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Poor quality work</span>
                                </div>
                            </label>
                            
                            <label class="flex items-center p-3 bg-white rounded-lg border border-red-200 hover:border-red-300 hover:bg-red-50 transition-all cursor-pointer group">
                                <input type="checkbox" name="quality_issues[]" value="missing_documentation" class="w-4 h-4 text-red-600 border-red-300 rounded focus:ring-red-500">
                                <div class="ml-3 flex items-center">
                                    <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Missing documentation</span>
                                </div>
                            </label>
                            
                            <label class="flex items-center p-3 bg-white rounded-lg border border-red-200 hover:border-red-300 hover:bg-red-50 transition-all cursor-pointer group">
                                <input type="checkbox" name="quality_issues[]" value="safety_concerns" class="w-4 h-4 text-red-600 border-red-300 rounded focus:ring-red-500">
                                <div class="ml-3 flex items-center">
                                    <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Safety concerns</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Requires Rework Section -->
                    <div class="bg-gradient-to-br from-amber-50 to-yellow-50 border border-amber-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-amber-100 p-2 rounded-full mr-3">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800">Rework Required</h4>
                        </div>
                        
                        <label class="flex items-center p-4 bg-white rounded-lg border border-amber-200 hover:border-amber-300 hover:bg-amber-50 transition-all cursor-pointer group">
                            <input type="checkbox" name="requires_rework" value="1" class="w-5 h-5 text-amber-600 border-amber-300 rounded focus:ring-amber-500" onchange="toggleRejectionReworkInstructions(this)">
                            <div class="ml-4 flex items-center">
                                <svg class="w-5 h-5 text-amber-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <span class="text-base font-semibold text-gray-700 group-hover:text-amber-700">This task requires rework</span>
                                    <p class="text-sm text-gray-500 mt-1">Check this if the work needs to be redone or improved</p>
                                </div>
                            </div>
                        </label>

                        <div id="rejectionReworkInstructions" class="hidden mt-4 p-4 bg-white rounded-lg border border-amber-200">
                            <label class="flex items-center mb-3">
                                <svg class="w-5 h-5 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span class="text-sm font-semibold text-gray-700">Rework Instructions</span>
                            </label>
                            <textarea name="rework_instructions" rows="4" class="w-full rounded-lg border-amber-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 resize-none" placeholder="Please specify what needs to be redone or improved..."></textarea>
                        </div>
                    </div>

                    <!-- Rejection Reason Section -->
                    <div class="bg-gradient-to-br from-red-50 to-pink-50 border border-red-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-red-100 p-2 rounded-full mr-3">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800">Rejection Reason</h4>
                            <span class="ml-2 text-sm text-red-600 font-medium">*required</span>
                        </div>
                        
                        <div class="bg-white rounded-lg border border-red-200 p-4">
                            <div class="flex items-center mb-3">
                                <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Detailed Explanation</span>
                            </div>
                            <textarea name="admin_notes" rows="4" class="w-full rounded-lg border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500 resize-none" placeholder="Provide a detailed explanation of why this task is being rejected and what needs to be corrected..." required></textarea>
                            <div class="mt-2 flex items-center text-xs text-gray-500">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                This feedback will be shared with the technician
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Modal Footer -->
        <div class="bg-gray-50 rounded-b-xl px-6 py-4 flex justify-end space-x-3">
            <button type="button" onclick="closeModal('rejectionModal')" class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors font-medium">
                Cancel
            </button>
            <button type="submit" form="rejectionForm" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors font-medium flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Reject & Send Back
            </button>
        </div>
    </div>
</div>

<script>
function filterTasks() {
    const lab = document.getElementById('labFilter').value;
    const technician = document.getElementById('technicianFilter').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    const params = new URLSearchParams();
    if (lab) params.append('lab', lab);
    if (technician) params.append('technician', technician);
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);

    window.location.href = '{{ route("maintenance.pending-approval") }}?' + params.toString();
}

function openApprovalModal(id, task, technician) {
    const modal = document.getElementById('approvalModal');
    const form = document.getElementById('approvalForm');
    const details = document.getElementById('approvalTaskDetails');
    
    form.action = `/maintenance/${id}/approve`;
    details.innerHTML = `<strong>Task:</strong> ${task}<br><strong>Technician:</strong> ${technician}`;
    
    // Reset form
    form.reset();
    
    // Clear signature
    clearApprovalSignature();
    
    // Initialize signature pad
    initializeApprovalSignaturePad();
    
    modal.classList.remove('hidden');
}

function openRejectionModal(id, task, technician) {
    const modal = document.getElementById('rejectionModal');
    const form = document.getElementById('rejectionForm');
    const details = document.getElementById('rejectionTaskDetails');
    
    form.action = `/maintenance/${id}/reject`;
    details.innerHTML = `<strong>Task:</strong> ${task}<br><strong>Technician:</strong> ${technician}`;
    
    // Reset form
    form.reset();
    document.getElementById('rejectionReworkInstructions').classList.add('hidden');
    
    modal.classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function toggleReworkInstructions(checkbox) {
    const instructions = document.getElementById('reworkInstructions');
    if (checkbox.checked) {
        instructions.classList.remove('hidden');
    } else {
        instructions.classList.add('hidden');
    }
}

function toggleRejectionReworkInstructions(checkbox) {
    const instructions = document.getElementById('rejectionReworkInstructions');
    if (checkbox.checked) {
        instructions.classList.remove('hidden');
    } else {
        instructions.classList.add('hidden');
    }
}

// Approval Signature Pad
let approvalSignaturePad = null;

function initializeApprovalSignaturePad() {
    const canvas = document.getElementById('approvalSignatureCanvas');
    if (!canvas) return;
    
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

    function saveSignature() {
        const signatureData = canvas.toDataURL('image/png');
        document.getElementById('approvalSignatureData').value = signatureData;
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

    canvas.addEventListener('mouseup', () => {
        isDrawing = false;
        saveSignature();
    });
    canvas.addEventListener('mouseout', () => {
        isDrawing = false;
    });

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
        saveSignature();
    });
    
    approvalSignaturePad = { canvas, ctx };
}

function clearApprovalSignature() {
    const canvas = document.getElementById('approvalSignatureCanvas');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = 'white';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    document.getElementById('approvalSignatureData').value = '';
}

// Set initial filter values from URL parameters
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('lab')) {
        document.getElementById('labFilter').value = urlParams.get('lab');
    }
    if (urlParams.has('technician')) {
        document.getElementById('technicianFilter').value = urlParams.get('technician');
    }
    if (urlParams.has('start_date')) {
        document.getElementById('startDate').value = urlParams.get('start_date');
    }
    if (urlParams.has('end_date')) {
        document.getElementById('endDate').value = urlParams.get('end_date');
    }
});
</script>
@endsection
