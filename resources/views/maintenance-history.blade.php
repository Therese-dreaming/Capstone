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
            <h1 class="text-2xl font-bold">Maintenance History</h1>
            <div class="space-x-3">
                <button onclick="exportToPDF()" class="text-sm px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Export to PDF
                </button>
            </div>
        </div>

        <div class="mb-6">
            <div class="grid grid-cols-4 gap-4">
                <div>
                    <label for="dateFilter" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" id="startDate" class="h-9 w-full px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label for="dateFilter" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date" id="endDate" class="h-9 w-full px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label for="labFilter" class="block text-sm font-medium text-gray-700 mb-1">Laboratories</label>
                    <select id="labFilter" onchange="filterHistory()" class="h-9 w-full px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        <option value="">All Laboratories</option>
                        @foreach(['401', '402', '403', '404', '405', '406'] as $lab)
                        <option value="{{ $lab }}">Laboratory {{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="statusFilter" onchange="filterHistory()" class="h-9 w-full px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        <option value="">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Delete Actions -->
        <div class="flex justify-between items-center mb-4">
            <div class="flex space-x-4">
                <button onclick="deleteSelected()" class="text-sm px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50" disabled id="deleteSelectedBtn">
                    Delete Selected
                </button>
            </div>
            <div class="text-sm text-gray-600" id="selectedCount">0 items selected</div>
        </div>

        <div class="overflow-x-auto">
            <table id="maintenanceTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#960106]">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Date</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Laboratory</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Task</th>
                        <!-- Add this new column -->
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Excluded Assets</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Technician</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Action By</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Completion Time</th>
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
                            Laboratory {{ $maintenance->lab_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @php
                            $tasks = is_array($maintenance->maintenance_task) ? $maintenance->maintenance_task : json_decode($maintenance->maintenance_task, true);
                            @endphp
                            @if(is_array($tasks))
                            <ul class="list-disc list-inside">
                                @foreach($tasks as $task)
                                <li>{{ $task }}</li>
                                @endforeach
                            </ul>
                            @else
                            {{ $maintenance->maintenance_task }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @php
                            $excludedAssets = is_array($maintenance->excluded_assets) ?
                            $maintenance->excluded_assets :
                            json_decode($maintenance->excluded_assets, true);
                            @endphp
                            @if(is_array($excludedAssets) && count($excludedAssets) > 0)
                            <ul class="list-disc list-inside">
                                @foreach($excludedAssets as $asset)
                                <li>{{ $asset }}</li>
                                @endforeach
                            </ul>
                            @else
                            <span class="text-gray-500">None</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $maintenance->technician->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $maintenance->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($maintenance->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $maintenance->actionBy ? $maintenance->actionBy->name : 'System' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($maintenance->status === 'completed' && $maintenance->completed_at)
                            <div>
                                <div>{{ \Carbon\Carbon::parse($maintenance->completed_at)->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($maintenance->completed_at)->format('g:i A') }}</div>
                            </div>
                            @else
                            -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button onclick="confirmDelete({{ $maintenance->id }})" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-4 text-center text-gray-500">No maintenance history found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-8 max-w-md mx-auto">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Deletion</h3>
            <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete the selected maintenance(s)? This action cannot be undone.</p>
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
        <div class="relative top-20 mx-auto p-5 border w-3/4 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Maintenance History Preview</h3>
                <button onclick="closePdfPreview()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="preview-content" style="max-height: 70vh; overflow-y: auto;">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="mt-4 flex justify-end space-x-3">
                <button onclick="closePdfPreview()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Cancel
                </button>
                <button onclick="downloadPDF()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Download PDF
                </button>
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
        const rows = document.querySelectorAll('#maintenanceTable tbody tr');

        rows.forEach(row => {
            const labMatch = !labFilter || row.dataset.lab === labFilter;
            const statusMatch = !statusFilter || row.dataset.status === statusFilter;

            // Date filtering
            let dateMatch = true;
            if (startDate || endDate) {
                const dateCell = row.querySelector('td:nth-child(2)'); // Changed from first-child to nth-child(2)
                if (dateCell) {
                    const dateText = dateCell.textContent.trim();
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
            }

            row.style.display = labMatch && statusMatch && dateMatch ? '' : 'none';
        });
    }

    // Add event listeners for date inputs
    document.getElementById('startDate').addEventListener('change', filterHistory);
    document.getElementById('endDate').addEventListener('change', filterHistory);

    function exportToPDF() {
        const modal = document.getElementById('pdfPreviewModal');
        const previewContent = modal.querySelector('.preview-content');

        // Get current filters
        const labFilter = document.getElementById('labFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (labFilter) params.append('lab', labFilter);
        if (statusFilter) params.append('status', statusFilter);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        // Show loading state
        previewContent.innerHTML = '<div class="text-center py-4">Loading preview...</div>';
        modal.classList.remove('hidden');

        // Fetch preview content with filters
        fetch('{{ route("maintenance.previewPDF") }}?' + params.toString())
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

    function closePdfPreview() {
        const modal = document.getElementById('pdfPreviewModal');
        const previewContent = modal.querySelector('.preview-content');
        modal.classList.add('hidden');
        previewContent.innerHTML = ''; // Clear the preview content
    }

    function downloadPDF() {
        const labFilter = document.getElementById('labFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (labFilter) params.append('lab', labFilter);
        if (statusFilter) params.append('status', statusFilter);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        // Append query parameters to the export URL
        window.location.href = "{{ route('maintenance.exportPDF') }}?" + params.toString();
        closePdfPreview();
    }

    function exportToPDF() {
        const modal = document.getElementById('pdfPreviewModal');
        const previewContent = modal.querySelector('.preview-content');

        // Get current filters
        const labFilter = document.getElementById('labFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (labFilter) params.append('lab', labFilter);
        if (statusFilter) params.append('status', statusFilter);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        // Show loading state
        previewContent.innerHTML = '<div class="text-center py-4">Loading preview...</div>';
        modal.classList.remove('hidden');

        // Fetch preview content with filters
        fetch('{{ route("maintenance.previewPDF") }}?' + params.toString())
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

    document.addEventListener('DOMContentLoaded', function() {
        // Delete functionality
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('input[name="selected[]"]');
        const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
        const selectedCount = document.getElementById('selectedCount');
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
                            // Remove the deleted rows from the table
                            itemsToDelete.forEach(id => {
                                const row = document.querySelector(`input[name="selected[]"][value="${id}"]`).closest('tr');
                                if (row) row.remove();
                            });

                            // Create and show success notification
                            showNotification('success', data.message || 'Maintenance records deleted successfully');

                            // Update selected count
                            updateSelectedCount();

                            // Check if table is empty and add "No records found" row if needed
                            const tbody = document.querySelector('tbody');
                            if (tbody.children.length === 0) {
                                const emptyRow = document.createElement('tr');
                                emptyRow.innerHTML = '<td colspan="10" class="px-6 py-4 text-center text-gray-500">No maintenance records found</td>';
                                tbody.appendChild(emptyRow);
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
                            // Remove the deleted row from the table
                            const row = document.querySelector(`input[name="selected[]"][value="${itemsToDelete[0]}"]`).closest('tr');
                            if (row) row.remove();

                            // Show success notification
                            showNotification('success', data.message || 'Maintenance record deleted successfully');

                            // Update selected count
                            updateSelectedCount();

                            // Check if table is empty
                            const tbody = document.querySelector('tbody');
                            if (tbody.children.length === 0) {
                                const emptyRow = document.createElement('tr');
                                emptyRow.innerHTML = '<td colspan="10" class="px-6 py-4 text-center text-gray-500">No maintenance records found</td>';
                                tbody.appendChild(emptyRow);
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
