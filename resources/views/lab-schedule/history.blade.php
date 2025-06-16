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

    <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4 md:gap-0">
            <h1 class="text-2xl font-bold">Lab Attendance History</h1>
            <div class="space-x-3">
                <button onclick="exportToPDF()" class="text-sm px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Export to PDF
                </button>
            </div>
        </div>

        <div class="mb-4 flex flex-col md:flex-row flex-wrap items-center w-full gap-4">
            <!-- Left: Filter Controls -->
            <div class="flex flex-col gap-2 w-full md:w-auto">
                <div class="flex flex-col gap-2 w-full">
                    <label class="text-xs font-semibold text-gray-600">Time In Date Range</label>
                    <div class="flex flex-col sm:flex-row items-stretch gap-2 w-full">
                        <input type="date" id="timeInStartDate" class="h-9 w-full md:w-auto px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        <span class="text-sm text-gray-500 flex items-center justify-center">to</span>
                        <input type="date" id="timeInEndDate" class="h-9 w-full md:w-auto px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
                <div class="flex flex-col gap-2 w-full mt-2">
                    <label class="text-xs font-semibold text-gray-600">Time Out Date Range</label>
                    <div class="flex flex-col sm:flex-row items-stretch gap-2 w-full">
                        <input type="date" id="timeOutStartDate" class="h-9 w-full md:w-auto px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        <span class="text-sm text-gray-500 flex items-center justify-center">to</span>
                        <input type="date" id="timeOutEndDate" class="h-9 w-full md:w-auto px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
            </div>

            <!-- Middle: More Filters -->
            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto md:ml-4 md:mt-0 mt-2">
                <select id="statusFilter" onchange="filterHistory()" class="h-9 w-full md:w-48 px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Status</option>
                    <option value="on-going">On-going</option>
                    <option value="completed">Completed</option>
                </select>
                <select id="laboratoryFilter" onchange="filterHistory()" class="h-9 w-full md:w-48 px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Laboratories</option>
                    @foreach($laboratories as $lab)
                    <option value="{{ $lab }}">{{ $lab }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Right: Delete Actions (Desktop) -->
            <div class="hidden md:flex items-center space-x-4 md:ml-auto">
                <div class="text-sm text-gray-600" id="selectedCount">0 items selected</div>
                <button onclick="deleteSelected()" class="text-sm px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50" disabled id="deleteSelectedBtn">
                    Delete Selected
                </button>
            </div>
            <!-- Delete Actions (Mobile) -->
            <div class="flex flex-col sm:flex-row items-center gap-2 md:hidden">
                <div class="text-sm text-gray-600" id="selectedCountMobile">0 items selected</div>
                <button onclick="deleteSelectedMobile()" class="text-sm px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50" disabled id="deleteSelectedBtnMobile">
                    Delete Selected
                </button>
            </div>
        </div>

        <!-- Cards for mobile -->
        <div class="grid grid-cols-1 gap-4 md:hidden">
            @forelse($logs as $log)
            <div class="bg-white rounded-lg shadow p-4 flex flex-col gap-2 border border-gray-200 relative transition ring-0" data-id="{{ $log->id }}" onclick="toggleCardSelection(this)">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-red-800">{{ $log->time_in->format('M d, Y h:i A') }}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ strtolower($log->status) === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $log->status }}
                    </span>
                </div>
                <div class="text-sm text-gray-700"><span class="font-semibold">Faculty:</span> {{ $log->user->name }}</div>
                <div class="text-sm"><span class="font-semibold">Position:</span> {{ $log->user->position }}</div>
                <div class="text-sm"><span class="font-semibold">Laboratory:</span> {{ $log->laboratory }}</div>
                <div class="text-sm"><span class="font-semibold">Time In:</span> {{ $log->time_in->format('M d, Y h:i A') }}</div>
                <div class="text-sm"><span class="font-semibold">Time Out:</span> {{ $log->time_out ? $log->time_out->format('M d, Y h:i A') : '-' }}</div>
                <div class="flex gap-2 mt-2">
                    <button onclick="event.stopPropagation(); confirmDelete({{ $log->id }})" class="text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
            @empty
            <div class="text-center text-gray-500 col-span-full">No attendance records found</div>
            @endforelse
        </div>

        <!-- Table for desktop -->
        <div class="overflow-x-auto hidden md:block">
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

    <!-- Add pagination links -->
    <div class="mt-6">
        {{ $logs->links() }}
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
        const timeInStartDate = document.getElementById('timeInStartDate').value;
        const timeInEndDate = document.getElementById('timeInEndDate').value;
        const timeOutStartDate = document.getElementById('timeOutStartDate').value;
        const timeOutEndDate = document.getElementById('timeOutEndDate').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (statusFilter) params.append('status', statusFilter);
        if (laboratoryFilter) params.append('laboratory', laboratoryFilter);
        if (timeInStartDate) params.append('time_in_start_date', timeInStartDate);
        if (timeInEndDate) params.append('time_in_end_date', timeInEndDate);
        if (timeOutStartDate) params.append('time_out_start_date', timeOutStartDate);
        if (timeOutEndDate) params.append('time_out_end_date', timeOutEndDate);

        // Redirect to filtered URL
        window.location.href = `{{ route('lab-schedule.history') }}?${params.toString()}`;
    }

    // Set initial filter values from URL parameters
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Set status and laboratory filters
        document.getElementById('statusFilter').value = urlParams.get('status') || '';
        document.getElementById('laboratoryFilter').value = urlParams.get('laboratory') || '';
        
        // Set time in date filters
        document.getElementById('timeInStartDate').value = urlParams.get('time_in_start_date') || '';
        document.getElementById('timeInEndDate').value = urlParams.get('time_in_end_date') || '';
        
        // Set time out date filters
        document.getElementById('timeOutStartDate').value = urlParams.get('time_out_start_date') || '';
        document.getElementById('timeOutEndDate').value = urlParams.get('time_out_end_date') || '';
    });

    // Add event listeners for filters
    document.getElementById('timeInStartDate').addEventListener('change', filterHistory);
    document.getElementById('timeInEndDate').addEventListener('change', filterHistory);
    document.getElementById('timeOutStartDate').addEventListener('change', filterHistory);
    document.getElementById('timeOutEndDate').addEventListener('change', filterHistory);
    document.getElementById('statusFilter').addEventListener('change', filterHistory);
    document.getElementById('laboratoryFilter').addEventListener('change', filterHistory);

    // Delete Functionality
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('input[name="selected[]"]');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    const deleteSelectedBtnMobile = document.getElementById('deleteSelectedBtnMobile');
    let itemsToDelete = [];
    let selectedCards = [];

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
        document.getElementById('selectedCountMobile').textContent = `${selectedItems} items selected`;
        deleteSelectedBtn.disabled = selectedItems === 0;
        deleteSelectedBtnMobile.disabled = selectedItems === 0;
    }

    function toggleCardSelection(card) {
        const id = card.getAttribute('data-id');
        
        if (card.classList.contains('ring-2')) {
            card.classList.remove('ring-2', 'ring-red-500');
            const index = selectedCards.indexOf(id);
            if (index > -1) {
                selectedCards.splice(index, 1);
            }
        } else {
            card.classList.add('ring-2', 'ring-red-500');
            selectedCards.push(id);
        }
        
        document.getElementById('selectedCountMobile').textContent = `${selectedCards.length} items selected`;
        deleteSelectedBtnMobile.disabled = selectedCards.length === 0;
    }

    function deleteSelectedMobile() {
        if (selectedCards.length === 0) {
            alert('Please select items to delete');
            return;
        }
        itemsToDelete = selectedCards;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
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
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    ids: itemsToDelete
                })
            })
            .then(response => response.json())
            .then(data => {
                closeDeleteModal();
                if (data.success) {
                    // Remove deleted items from the page
                    itemsToDelete.forEach(id => {
                        // Remove from table
                        const row = document.querySelector(`tr input[value="${id}"]`)?.closest('tr');
                        if (row) row.remove();
                        
                        // Remove from mobile cards
                        const card = document.querySelector(`div[data-id="${id}"]`);
                        if (card) card.remove();
                    });
                    
                    // Reset selections
                    selectedCards = [];
                    updateSelectedCount();
                    
                    // Show success message - Fixed insertion point
                    const successDiv = document.createElement('div');
                    successDiv.className = 'mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700';
                    successDiv.textContent = data.message || 'Records deleted successfully';
                    
                    // Get the container and insert at the beginning
                    const container = document.querySelector('.flex-1');
                    const firstChild = container.firstChild;
                    container.insertBefore(successDiv, firstChild);
                    
                    // Auto-remove after 3 seconds
                    setTimeout(() => {
                        if (successDiv.parentNode) {
                            successDiv.remove();
                        }
                    }, 3000);
                    
                    // Reload the page if all items were deleted
                    if (document.querySelectorAll('#labLogsTable tbody tr').length === 0 && 
                        document.querySelectorAll('div.grid > [data-id]').length === 0) {
                        location.reload();
                    }
                } else {
                    throw new Error(data.message || 'Failed to delete records');
                }
            })
            .catch(error => {
                closeDeleteModal();
                const errorDiv = document.createElement('div');
                errorDiv.className = 'mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700';
                errorDiv.textContent = error.message;
                
                // Get the container and insert at the beginning
                const container = document.querySelector('.flex-1');
                const firstChild = container.firstChild;
                container.insertBefore(errorDiv, firstChild);
                
                // Auto-remove after 3 seconds
                setTimeout(() => {
                    if (errorDiv.parentNode) {
                        errorDiv.remove();
                    }
                }, 3000);
            });
    }

    function exportToPDF() {
        const modal = document.getElementById('pdfPreviewModal');
        const previewContent = modal.querySelector('.preview-content');

        // Get current filters
        const statusFilter = document.getElementById('statusFilter').value;
        const laboratoryFilter = document.getElementById('laboratoryFilter').value;
        const timeInStartDate = document.getElementById('timeInStartDate').value;
        const timeInEndDate = document.getElementById('timeInEndDate').value;
        const timeOutStartDate = document.getElementById('timeOutStartDate').value;
        const timeOutEndDate = document.getElementById('timeOutEndDate').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (statusFilter) params.append('status', statusFilter);
        if (laboratoryFilter) params.append('laboratory', laboratoryFilter);
        if (timeInStartDate) params.append('time_in_start_date', timeInStartDate);
        if (timeInEndDate) params.append('time_in_end_date', timeInEndDate);
        if (timeOutStartDate) params.append('time_out_start_date', timeOutStartDate);
        if (timeOutEndDate) params.append('time_out_end_date', timeOutEndDate);

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
        const timeInStartDate = document.getElementById('timeInStartDate').value;
        const timeInEndDate = document.getElementById('timeInEndDate').value;
        const timeOutStartDate = document.getElementById('timeOutStartDate').value;
        const timeOutEndDate = document.getElementById('timeOutEndDate').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (statusFilter) params.append('status', statusFilter);
        if (laboratoryFilter) params.append('laboratory', laboratoryFilter);
        if (timeInStartDate) params.append('time_in_start_date', timeInStartDate);
        if (timeInEndDate) params.append('time_in_end_date', timeInEndDate);
        if (timeOutStartDate) params.append('time_out_start_date', timeOutStartDate);
        if (timeOutEndDate) params.append('time_out_end_date', timeOutEndDate);

        // Redirect to download URL
        window.location.href = `{{ route('lab-schedule.exportPDF') }}?${params.toString()}`;
        closePdfPreview();
    }

    function closePdfPreview() {
        const modal = document.getElementById('pdfPreviewModal');
        modal.classList.add('hidden');
        modal.querySelector('.preview-content').innerHTML = '';
    }
</script>
@endsection
