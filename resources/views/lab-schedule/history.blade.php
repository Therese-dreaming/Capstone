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
            <h1 class="text-2xl font-bold">Lab Attendance History</h1>
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
                    <option value="time_in">Filter by Time In</option>
                    <option value="time_out">Filter by Time Out</option>
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
                    <option value="on-going">On-going</option>
                    <option value="completed">Completed</option>
                </select>
                <select id="laboratoryFilter" onchange="filterHistory()" class="h-9 w-48 px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Laboratories</option>
                    @foreach($laboratories as $lab)
                    <option value="{{ $lab }}">{{ $lab }}</option>
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
                <table id="labLogsTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#960106]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white">Faculty</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white">Laboratory</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white">Time In</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white">Time Out</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($logs as $log)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <input type="checkbox" name="selected[]" value="{{ $log->id }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $log->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $log->user->position }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->laboratory }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $log->time_in->format('M d, Y h:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($log->time_out)
                                        {{ $log->time_out->format('M d, Y h:i A') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ strtolower($log->status) === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $log->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <button onclick="confirmDelete({{ $log->id }})" class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No attendance records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-8 max-w-md mx-auto">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Deletion</h3>
        <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete this attendance record? This action cannot be undone.</p>
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

<script>
    function filterHistory() {
        const statusFilter = document.getElementById('statusFilter').value;
        const laboratoryFilter = document.getElementById('laboratoryFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const dateFilterType = document.getElementById('dateFilterType').value;
        const rows = document.querySelectorAll('#labLogsTable tbody tr');

        rows.forEach(row => {
            const statusCell = row.querySelector('td:nth-child(6)');
            const laboratoryCell = row.querySelector('td:nth-child(3)');
            const timeInCell = row.querySelector('td:nth-child(4)');
            const timeOutCell = row.querySelector('td:nth-child(5)');

            const statusText = statusCell.textContent.trim().toLowerCase();
            const statusMatch = !statusFilter || statusText.includes(statusFilter);
            const laboratoryMatch = !laboratoryFilter || laboratoryCell.textContent.trim() === laboratoryFilter;

            let dateMatch = true;
            if (startDate || endDate) {
                const dateCell = dateFilterType === 'time_in' ? timeInCell : timeOutCell;
                const dateText = dateCell.textContent.trim();
                if (dateText !== '-') {
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
                } else {
                    dateMatch = false;
                }
            }

            row.style.display = statusMatch && laboratoryMatch && dateMatch ? '' : 'none';
        });
    }

    // Add event listeners for filters
    document.getElementById('dateFilterType').addEventListener('change', filterHistory);
    document.getElementById('startDate').addEventListener('change', filterHistory);
    document.getElementById('endDate').addEventListener('change', filterHistory);
    document.getElementById('statusFilter').addEventListener('change', filterHistory);
    document.getElementById('laboratoryFilter').addEventListener('change', filterHistory);

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
        document.getElementById('selectedCount').textContent = `${selectedItems} items selected`;
        deleteSelectedBtn.disabled = selectedItems === 0;
    }

    function confirmDelete(id) {
        itemsToDelete = [id];
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }

    function deleteSelected() {
        itemsToDelete = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
        if (itemsToDelete.length === 0) {
            alert('Please select items to delete');
            return;
        }
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }

    function executeDelete() {
        fetch('{{ route("lab-schedule.destroyMultiple") }}', {
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
                    itemsToDelete.forEach(id => {
                        const row = document.querySelector(`tr input[value="${id}"]`).closest('tr');
                        row.remove();
                    });
                    updateSelectedCount();
                    // Show success message
                    const successDiv = document.createElement('div');
                    successDiv.className = 'mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700';
                    successDiv.textContent = data.message || 'Records deleted successfully';
                    document.querySelector('.flex-1').insertBefore(successDiv, document.querySelector('.bg-white'));
                    setTimeout(() => successDiv.remove(), 3000);
                } else {
                    throw new Error(data.message || 'Failed to delete records');
                }
            })
            .catch(error => {
                closeDeleteModal();
                const errorDiv = document.createElement('div');
                errorDiv.className = 'mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700';
                errorDiv.textContent = error.message;
                document.querySelector('.flex-1').insertBefore(errorDiv, document.querySelector('.bg-white'));
                setTimeout(() => errorDiv.remove(), 3000);
            });
    }

    function exportToPDF() {
        const modal = document.getElementById('pdfPreviewModal');
        const previewContent = modal.querySelector('.preview-content');

        // Get current filters
        const statusFilter = document.getElementById('statusFilter').value;
        const laboratoryFilter = document.getElementById('laboratoryFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (statusFilter) params.append('status', statusFilter);
        if (laboratoryFilter) params.append('laboratory', laboratoryFilter);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        // Show loading state
        previewContent.innerHTML = '<div class="text-center py-4">Loading preview...</div>';
        modal.classList.remove('hidden');

        // Fetch preview content
        fetch(`{{ route('lab-schedule.previewPDF') }}?${params.toString()}`)
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
        const laboratoryFilter = document.getElementById('laboratoryFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (statusFilter) params.append('status', statusFilter);
        if (laboratoryFilter) params.append('laboratory', laboratoryFilter);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        // Redirect to download URL
        window.location.href = `{{ route('lab-schedule.exportPDF') }}?${params.toString()}`;
        closePdfPreview();
    }

    function closePdfPreview() {
        const modal = document.getElementById('pdfPreviewModal');
        modal.classList.add('hidden');
        modal.querySelector('.preview-content').innerHTML = '';
    }

    // Add event listeners for filters
    document.getElementById('dateFilterType').addEventListener('change', filterHistory);
    document.getElementById('startDate').addEventListener('change', filterHistory);
    document.getElementById('endDate').addEventListener('change', filterHistory);
    document.getElementById('statusFilter').addEventListener('change', filterHistory);
    document.getElementById('laboratoryFilter').addEventListener('change', filterHistory);

</script>
@endsection
