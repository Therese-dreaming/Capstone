@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 transition-all duration-300" id="mainContent">
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

    <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4 md:gap-0">
            <h1 class="text-2xl font-bold">Repair Requests History</h1>
            <div class="space-x-3">
                <button onclick="exportToPDF()" class="text-sm px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Export to PDF
                </button>
            </div>
        </div>

        <!-- Filters and Delete Selected (Mobile) -->
        <div class="mb-4 flex flex-col gap-4 w-full">
            <!-- Filters -->
            <div class="flex flex-col md:flex-row md:items-center md:gap-4 w-full">
                <div class="flex flex-col gap-2 w-full md:w-auto">
                    <div class="flex flex-col gap-2 w-full">
                        <label class="text-xs font-semibold text-gray-600">Request Date Range</label>
                        <div class="flex flex-col sm:flex-row items-stretch gap-2 w-full">
                            <input type="date" id="requestStartDate" class="h-9 w-full md:w-auto px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                            <span class="text-sm text-gray-500 flex items-center justify-center">to</span>
                            <input type="date" id="requestEndDate" class="h-9 w-full md:w-auto px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 w-full mt-2">
                        <label class="text-xs font-semibold text-gray-600">Completion Date Range</label>
                        <div class="flex flex-col sm:flex-row items-stretch gap-2 w-full">
                            <input type="date" id="completionStartDate" class="h-9 w-full md:w-auto px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                            <span class="text-sm text-gray-500 flex items-center justify-center">to</span>
                            <input type="date" id="completionEndDate" class="h-9 w-full md:w-auto px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto md:ml-4 md:mt-0 mt-2">
                    <select id="statusFilter" onchange="filterHistory()" class="h-9 w-full md:w-48 px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        <option value="">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="pulled_out">Pulled Out</option>
                    </select>
                    <select id="locationFilter" onchange="filterHistory()" class="h-9 w-full md:w-48 px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        <option value="">All Locations</option>
                        @foreach($completedRequests->pluck('building', 'floor', 'room')->unique() as $request)
                        @if($request->building && $request->floor && $request->room)
                        <option value="{{ $request->building }}-{{ $request->floor }}-{{ $request->room }}">
                            {{ $request->building }} - {{ $request->floor }} - {{ $request->room }}
                        </option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Delete Selected (Desktop) -->
            <div class="hidden md:flex items-center space-x-4 md:ml-auto">
                <div class="text-sm text-gray-600" id="selectedCount">0 items selected</div>
                <button onclick="deleteSelected()" class="text-sm px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50" disabled id="deleteSelectedBtn">
                    Delete Selected
                </button>
            </div>
            <!-- Delete Selected (Mobile) -->
            <div class="flex flex-col sm:flex-row items-center gap-2 md:hidden">
                <div class="text-sm text-gray-600" id="selectedCountMobile">0 items selected</div>
                <button onclick="deleteSelectedMobile()" class="text-sm px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50" disabled id="deleteSelectedBtnMobile">
                    Delete Selected
                </button>
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
                <div class="text-sm"><span class="font-semibold">Item:</span> <a href="{{ route('repair.details', ['id' => $request->id]) }}" class="font-bold text-red-600 hover:underline">{{ $request->asset->serial_number }}</a></div>
                <div class="text-sm"><span class="font-semibold">Ticket No.:</span> {{ $request->ticket_number }}</div>
                <div class="text-sm"><span class="font-semibold">Location:</span> 
                    {{ $request->building }} - {{ $request->floor }} - {{ $request->room }}
                </div>
                <div class="text-sm"><span class="font-semibold">Technician:</span> {{ $request->technician ? $request->technician->name : 'Not Assigned' }}</div>
                @if(!in_array($request->creator->group_id ?? null, [1, 2]))
                <div class="text-sm"><span class="font-semibold">Caller's Name:</span> {{ $request->caller_name ?: 'N/A' }}</div>
                @endif
                <div class="text-sm"><span class="font-semibold">Findings:</span> {{ $request->findings }}</div>
                <div class="text-sm"><span class="font-semibold">Remarks:</span> {{ $request->remarks }}</div>
                <div class="flex gap-2 mt-2">
                    <button onclick="event.stopPropagation(); window.location.href={{ route('repair.details', ['id' => $request->id]) }}" class="text-blue-600 hover:text-blue-800">
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
        <div class="overflow-x-auto hidden md:block">
            <table id="repairTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#960106]">
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
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Location</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Technician</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($completedRequests as $request)
                    <tr data-location="{{ $request->location }}" class="{{ strlen($request->remarks) > 50 ? 'hover:bg-gray-50 cursor-pointer' : '' }}" {{ strlen($request->remarks) > 50 ? 'onclick=toggleRemarks('.$request->id.')' : '' }}>
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
                            <a href="{{ route('repair.details', ['id' => $request->id]) }}" class="font-bold text-red-600 hover:underline">{{ $request->asset->serial_number }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->ticket_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->location }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->technician ? $request->technician->name : 'Not Assigned' }}</td>
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
                        <td colspan="11" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
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
                        <td colspan="11" class="px-6 py-4 text-center text-gray-500">No repair history found</td>
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

    // Keep only one exportToPDF function
    function exportToPDF() {
        const modal = document.getElementById('pdfPreviewModal');
        const previewContent = modal.querySelector('.preview-content');

        // Get current filters
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
        if (requestStartDate) params.append('request_start_date', requestStartDate);
        if (requestEndDate) params.append('request_end_date', requestEndDate);
        if (completionStartDate) params.append('completion_start_date', completionStartDate);
        if (completionEndDate) params.append('completion_end_date', completionEndDate);

        // Show loading state
        previewContent.innerHTML = '<div class="text-center py-4">Loading preview...</div>';
        modal.classList.remove('hidden');

        // Fetch preview content with filters
        fetch(`{{ route('repair.previewPDF') }}?${params.toString()}`)
            .then(response => response.text())
            .then(html => {
                const iframe = document.createElement('iframe');
                iframe.style.width = '100%';
                iframe.style.height = '70vh';
                iframe.style.border = 'none';

                previewContent.innerHTML = '';
                previewContent.appendChild(iframe);

                const iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
                iframeDocument.open();
                iframeDocument.write(html);
                iframeDocument.close();
            })
            .catch(error => {
                previewContent.innerHTML = '<div class="text-center py-4 text-red-600">Error loading preview</div>';
            });
    }

    function downloadPDF() {
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
        if (requestStartDate) params.append('request_start_date', requestStartDate);
        if (requestEndDate) params.append('request_end_date', requestEndDate);
        if (completionStartDate) params.append('completion_start_date', completionStartDate);
        if (completionEndDate) params.append('completion_end_date', completionEndDate);

        // Redirect to download URL
        window.location.href = `{{ route('repair.exportPDF') }}?${params.toString()}`;
        closePdfPreview();
    }

    function closePdfPreview() {
        const modal = document.getElementById('pdfPreviewModal');
        modal.classList.add('hidden');
        modal.querySelector('.preview-content').innerHTML = '';
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
                updateSelectedCountMobile();

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
        updateSelectedCountMobile();
    }
    function updateSelectedCountMobile() {
        const count = selectedCardIds.length;
        document.getElementById('selectedCountMobile').textContent = `${count} items selected`;
        document.getElementById('deleteSelectedBtnMobile').disabled = count === 0;
    }
    function deleteSelectedMobile() {
        if (selectedCardIds.length === 0) {
            alert('Please select items to delete');
            return;
        }
        // Show modal and set up deletion
        window.itemsToDelete = selectedCardIds;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }
</script>
@endsection
