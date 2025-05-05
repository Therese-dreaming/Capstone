@extends('layouts.app')

@section('content')
<div class="flex-1 p-8 ml-72">
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

    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Repair Requests History</h1>
            <div class="space-x-3">
                <button onclick="exportToPDF()" class="text-sm px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Export to PDF
                </button>
            </div>
        </div>

        <div class="mb-4 flex flex-wrap items-center w-full gap-4">
            <!-- Left: Filter Controls -->
            <div class="flex flex-col gap-2">
                <select id="dateFilterType" onchange="filterHistory()" class="h-9 px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                    <option value="request">Filter by Request Date</option>
                    <option value="completion">Filter by Completion Date</option>
                </select>
                <div class="flex items-center gap-2">
                    <input type="date" id="startDate" class="h-9 px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                    <span class="text-sm text-gray-500">to</span>
                    <input type="date" id="endDate" class="h-9 px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                </div>
            </div>

            <!-- Middle: More Filters -->
            <div class="flex items-center gap-4">
                <select id="statusFilter" onchange="filterHistory()" class="h-9 w-48 px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Status</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="pulled out">Pulled Out</option>
                </select>
                <select id="locationFilter" onchange="filterHistory()" class="h-9 w-48 px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Locations</option>
                    @foreach($completedRequests->pluck('location')->unique() as $location)
                    @if($location)
                    <option value="{{ $location }}">{{ $location }}</option>
                    @endif
                    @endforeach
                </select>
            </div>

            <!-- Right: Delete Actions -->
            <div class="flex items-center space-x-4 ml-auto">
                <div class="text-sm text-gray-600" id="selectedCount">0 items selected</div>
                <button onclick="deleteSelected()" class="text-sm px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50" disabled id="deleteSelectedBtn">
                    Delete Selected
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="repairTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#960106]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Request Date</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Completion Date</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Item</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Ticket No.</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Location</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Technician</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Remarks</th>
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
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($request->completed_at)->format('M j, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($request->completed_at)->format('g:i A') }}</div>
                            </div>
                        </td> 
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->equipment }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->ticket_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->location }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->technician ? $request->technician->name : 'Not Assigned' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($request->status === 'cancelled')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Cancelled
                            </span>
                            @elseif($request->status === 'pulled_out')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pulled Out
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Completed
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span>{{ strlen($request->remarks) > 50 ? Str::limit($request->remarks, 50) : $request->remarks }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <button onclick="event.stopPropagation(); confirmDelete({{ $request->id }})" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @if(strlen($request->remarks) > 50)
                    <tr id="remarks-{{ $request->id }}" class="hidden bg-gray-50">
                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="text-sm">
                                <span class="font-semibold">Full Remarks:</span>
                                <p class="mt-2 whitespace-pre-wrap">{{ $request->remarks }}</p>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-4 text-center text-gray-500">No repair history found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-8 max-w-md mx-auto">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Deletion</h3>
        <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete this repair request? This action cannot be undone.</p>
        <div class="flex justify-end space-x-4">
            <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                Cancel
            </button>
            <button onclick="executeDelete()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                Delete
            </button>
        </div>
    </div>
</div>

<!-- PDF Preview Modal -->
<div id="pdfPreviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-4 border w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-2">
            <h3 class="text-lg font-medium">Preview</h3>
            <button onclick="closePdfPreview()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="preview-content" style="max-height: 60vh; overflow-y: auto;">
            <!-- Preview content will be loaded here -->
        </div>
        <div class="mt-3 flex justify-end space-x-2">
            <button onclick="closePdfPreview()" class="px-3 py-1.5 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                Cancel
            </button>
            <button onclick="downloadPDF()" class="px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700">
                Download
            </button>
        </div>
    </div>
</div>
</div>

<script>
    function filterHistory() {
        const statusFilter = document.getElementById('statusFilter').value;
        const locationFilter = document.getElementById('locationFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const dateFilterType = document.getElementById('dateFilterType').value;
        const rows = document.querySelectorAll('#repairTable tbody tr:not([id^="remarks-"])');

        rows.forEach(row => {
            const statusCell = row.querySelector('td:nth-child(8)');
            const locationCell = row.querySelector('td:nth-child(6)');

            const statusMatch = !statusFilter || statusCell.textContent.trim().toLowerCase().includes(statusFilter.toLowerCase());
            const locationMatch = !locationFilter || locationCell.textContent.trim() === locationFilter;

            // Date filtering
            let dateMatch = true;
            if (startDate || endDate) {
                const columnIndex = dateFilterType === 'request' ? 1 : 2;
                const dateText = row.querySelector(`td:nth-child(${columnIndex})`).textContent.trim();
                const cellDate = new Date(dateText);
                const start = startDate ? new Date(startDate + 'T00:00:00') : null;
                const end = endDate ? new Date(endDate + 'T23:59:59') : null;

                if (start && end) {
                    dateMatch = cellDate >= start && cellDate <= end;
                } else if (start) {
                    dateMatch = cellDate >= start;
                } else if (end) {
                    dateMatch = cellDate <= end;
                }
            }

            row.style.display = statusMatch && locationMatch && dateMatch ? '' : 'none';

            // Hide associated remarks row if main row is hidden
            const remarksId = row.onclick ? row.onclick.toString().match(/toggleRemarks\((\d+)\)/) ?. [1] : null;
            if (remarksId) {
                const remarksRow = document.getElementById(`remarks-${remarksId}`);
                if (remarksRow) {
                    remarksRow.style.display = 'none';
                }
            }
        });
    }

    // Add event listeners
    document.getElementById('dateFilterType').addEventListener('change', filterHistory);
    document.getElementById('startDate').addEventListener('change', filterHistory);
    document.getElementById('endDate').addEventListener('change', filterHistory);

    // Keep only one exportToPDF function
    function exportToPDF() {
        const modal = document.getElementById('pdfPreviewModal');
        const previewContent = modal.querySelector('.preview-content');

        // Get current filters
        const statusFilter = document.getElementById('statusFilter').value;
        const locationFilter = document.getElementById('locationFilter').value;
        const dateFilterType = document.getElementById('dateFilterType').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (statusFilter) params.append('status', statusFilter);
        if (locationFilter) params.append('location', locationFilter);
        if (dateFilterType) params.append('date_filter_type', dateFilterType);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

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
        const dateFilterType = document.getElementById('dateFilterType').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (statusFilter) params.append('status', statusFilter);
        if (locationFilter) params.append('location', locationFilter);
        if (dateFilterType) params.append('date_filter_type', dateFilterType);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        // Redirect to download URL
        window.location.href = `{{ route('repair.exportPDF') }}?${params.toString()}`;
        closePdfPreview();
    }

    function closePdfPreview() {
        const modal = document.getElementById('pdfPreviewModal');
        modal.classList.add('hidden');
        modal.querySelector('.preview-content').innerHTML = '';
    }

    // Delete Functionality
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
        selectedCount.textContent = `${selectedItems} items selected`;
        deleteSelectedBtn.disabled = selectedItems === 0;
    }

    window.confirmDelete = function(id) {
        itemsToDelete = [id];
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }

    window.deleteSelected = function() {
        itemsToDelete = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
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
        fetch('{{ route("repair.destroyMultiple") }}', {
                method: 'POST'
                , headers: {
                    'Content-Type': 'application/json'
                    , 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
                , body: JSON.stringify({
                    ids: itemsToDelete
                })
            })
            .then(response => response.json())
            .then(data => {
                closeDeleteModal();
                if (data.success) {
                    // Remove deleted rows from the table
                    itemsToDelete.forEach(id => {
                        const row = document.querySelector(`tr input[value="${id}"]`).closest('tr');
                        const remarksRow = document.getElementById(`remarks-${id}`);
                        if (row) row.remove();
                        if (remarksRow) remarksRow.remove();
                    });

                    // Reset checkboxes and update count
                    selectAll.checked = false;
                    updateSelectedCount();

                    // Create and show success message
                    const successMessage = document.createElement('div');
                    successMessage.className = 'mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700';
                    successMessage.textContent = 'Request(s) deleted successfully';

                    // Insert at the top of the content area
                    const contentArea = document.querySelector('.flex-1.p-8');
                    const existingMessage = contentArea.querySelector('.mb-4.p-4');
                    if (existingMessage) {
                        existingMessage.remove();
                    }
                    contentArea.insertBefore(successMessage, contentArea.firstChild);

                    // Remove success message after 3 seconds
                    setTimeout(() => {
                        successMessage.remove();
                    }, 3000);
                } else {
                    // Create and show error message
                    const errorMessage = document.createElement('div');
                    errorMessage.className = 'mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700';
                    errorMessage.textContent = data.message || 'Error deleting request(s)';

                    // Insert at the top of the content area
                    const contentArea = document.querySelector('.flex-1.p-8');
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
                const contentArea = document.querySelector('.flex-1.p-8');
                const existingMessage = contentArea.querySelector('.mb-4.p-4');
                if (existingMessage) {
                    existingMessage.remove();
                }
                contentArea.insertBefore(errorMessage, contentArea.firstChild);
            });
    }

</script>
@endsection
