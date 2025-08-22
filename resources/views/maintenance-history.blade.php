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
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                        <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Maintenance History</h1>
                        <p class="text-red-100 text-sm md:text-lg">View maintenance records</p>
                    </div>
                </div>
                <button onclick="exportToPDF()" class="inline-flex items-center px-4 py-2 bg-white/20 text-white font-medium rounded-lg hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 focus:ring-offset-2 focus:ring-offset-red-800 transition-colors duration-200">
                    Export to PDF
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">

        <div class="mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
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
                <div>
                    <label for="issueFilter" class="block text-sm font-medium text-gray-700 mb-1">Asset Issues</label>
                    <select id="issueFilter" onchange="filterHistory()" class="h-9 w-full px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        <option value="">All Records</option>
                        <option value="with_issues">With Issues</option>
                        <option value="no_issues">No Issues</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Delete Actions -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-2 md:gap-0">
            <div class="flex space-x-4">
                <button onclick="deleteSelected()" class="text-sm px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50" disabled id="deleteSelectedBtn">
                    Delete Selected
                </button>
            </div>
            <div class="text-sm text-gray-600" id="selectedCount">0 items selected</div>
        </div>

        <!-- Maintenance Records as Cards (Mobile Only) -->
        <div class="grid grid-cols-1 gap-6 md:hidden" id="maintenanceCards">
            @forelse($maintenances as $maintenance)
            <div class="bg-white rounded-lg shadow p-4 flex flex-col gap-2 border border-gray-200">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-red-800">{{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('M d, Y') }}</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $maintenance->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($maintenance->status) }}
                    </span>
                </div>
                <div class="text-sm text-gray-700">Location <span class="font-semibold">{{ $maintenance->location ? ($maintenance->location->building . ' - Floor ' . $maintenance->location->floor . ' - Room ' . $maintenance->location->room_number) : 'N/A' }}</span></div>
                <div class="text-sm"><span class="font-semibold">Technician:</span> {{ $maintenance->technician->name }}</div>
                @if($maintenance->asset_issues && is_array($maintenance->asset_issues) && !empty($maintenance->asset_issues))
                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 w-fit">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Issues Found
                    </div>
                @endif
                <div class="flex gap-2 mt-2">
                    <a href="{{ route('maintenance.show', $maintenance->id) }}" class="text-xs px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">View Details</a>
                    <button onclick="confirmDelete({{ $maintenance->id }})" class="text-xs px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center text-gray-500">No maintenance history found</div>
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
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $maintenance->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($maintenance->status) }}
                            </span>
                            @if($maintenance->asset_issues && is_array($maintenance->asset_issues) && !empty($maintenance->asset_issues))
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
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No maintenance history found</td>
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
     class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center"
     style="z-index: 60;">
    <div class="p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Delete Maintenance</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to delete the selected maintenance(s)? This action cannot be undone.</p>
                </div>
                <div class="flex justify-center gap-4 mt-4">
                    <button onclick="executeDelete()" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Delete
                    </button>
                    <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Preview Modal -->
    <div id="pdfPreviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-3 md:p-5 border w-full max-w-[98vw] md:max-w-[75vw] shadow-lg rounded-md bg-white">
            <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-2 md:gap-0">
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
            <div class="mt-4 flex flex-col md:flex-row justify-end space-y-2 md:space-y-0 md:space-x-3">
                <button onclick="closePdfPreview()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 w-full md:w-auto">
                    Cancel
                </button>
                <button onclick="downloadPDF()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 w-full md:w-auto">
                    Download PDF
                </button>
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
                                emptyRow.innerHTML = '<td colspan="6" class="px-6 py-4 text-center text-gray-500">No maintenance records found</td>';
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
                                emptyRow.innerHTML = '<td colspan="6" class="px-6 py-4 text-center text-gray-500">No maintenance records found</td>';
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
