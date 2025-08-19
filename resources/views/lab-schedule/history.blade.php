@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 transition-all duration-300" id="mainContent">
    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-100 border border-green-200 rounded-xl text-green-800 flex items-center">
        <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-100 border border-red-200 rounded-xl text-red-800 flex items-center">
        <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ session('error') }}
    </div>
    @endif

    <!-- Main Content Card -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8 gap-4 md:gap-0">
            <div class="flex items-center">
                <div class="bg-red-100 p-3 rounded-full mr-4">
                    <svg class="w-8 h-8 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Lab Attendance History</h1>
                    <p class="text-gray-600 text-sm md:text-base">View and manage laboratory attendance records</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="exportToPDF()" class="inline-flex items-center px-4 py-2 bg-red-800 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export to PDF
                </button>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="mb-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                </svg>
                Filter Records
            </h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Date Range Filters -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Time In Date Range</label>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input type="date" id="timeInStartDate" class="flex-1 h-10 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            <span class="text-sm text-gray-500 flex items-center justify-center">to</span>
                            <input type="date" id="timeInEndDate" class="flex-1 h-10 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Time Out Date Range</label>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input type="date" id="timeOutStartDate" class="flex-1 h-10 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            <span class="text-sm text-gray-500 flex items-center justify-center">to</span>
                            <input type="date" id="timeOutEndDate" class="flex-1 h-10 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                        </div>
                    </div>
                </div>

                <!-- Status and Laboratory Filters -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="statusFilter" onchange="filterHistory()" class="w-full h-10 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            <option value="">All Status</option>
                            <option value="on-going">On-going</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Laboratory</label>
                        <select id="laboratoryFilter" onchange="filterHistory()" class="w-full h-10 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            <option value="">All Laboratories</option>
                            @foreach($laboratories as $lab)
                            <option value="{{ $lab }}">{{ $lab }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Delete Actions -->
                <div class="space-y-4">
                    <div class="text-sm text-gray-600" id="selectedCount">0 items selected</div>
                    <button onclick="deleteSelected()" class="w-full text-sm px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors" disabled id="deleteSelectedBtn">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Selected
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Cards -->
        <div class="grid grid-cols-1 gap-4 md:hidden mb-6">
            @forelse($logs as $log)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-col gap-3 relative transition-all duration-200 hover:shadow-md" data-id="{{ $log->id }}" onclick="toggleCardSelection(this)">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="text-sm text-gray-500 mb-1">Time In</div>
                        <div class="font-semibold text-gray-900">{{ $log->time_in->format('M d, Y h:i A') }}</div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        {{ strtolower($log->status) === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $log->status }}
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-500">Faculty:</span>
                        <div class="font-medium text-gray-900">{{ $log->user->name }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500">Position:</span>
                        <div class="font-medium text-gray-900">{{ $log->user->position }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500">Laboratory:</span>
                        <div class="font-medium text-gray-900">{{ $log->laboratory }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500">Time Out:</span>
                        <div class="font-medium text-gray-900">{{ $log->time_out ? $log->time_out->format('M d, Y h:i A') : '-' }}</div>
                    </div>
                </div>
                
                <div class="flex justify-end pt-2 border-t border-gray-100">
                    <button onclick="event.stopPropagation(); confirmDelete({{ $log->id }})" class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-500">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2-2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="text-lg font-medium">No attendance records found</p>
                <p class="text-sm">Try adjusting your filters or check back later</p>
            </div>
            @endforelse
        </div>

        <!-- Desktop Table -->
        <div class="overflow-x-auto hidden md:block">
            <table id="labLogsTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-red-800">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-white">Faculty</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-white">Laboratory</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-white">Time In</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-white">Time Out</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-white">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
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
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ strtolower($log->status) === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $log->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <button onclick="confirmDelete({{ $log->id }})" class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2-2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="text-lg font-medium">No attendance records found</p>
                                    <p class="text-sm">Try adjusting your filters or check back later</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $logs->links() }}
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl p-8 max-w-md mx-auto transform transition-all duration-300 scale-95">
        <div class="flex items-center mb-4">
            <div class="bg-red-100 p-3 rounded-full mr-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900">Confirm Deletion</h3>
        </div>
        <p class="text-gray-600 mb-8">Are you sure you want to delete this attendance record? This action cannot be undone.</p>
        <div class="flex justify-end space-x-4">
            <button onclick="closeDeleteModal()" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                Cancel
            </button>
            <button onclick="executeDelete()" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                Delete
            </button>
        </div>
    </div>
</div>

<!-- PDF Preview Modal -->
<div id="pdfPreviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-11/12 max-w-4xl shadow-xl rounded-xl bg-white">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">PDF Preview</h3>
            <button onclick="closePdfPreview()" class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="preview-content bg-gray-50 rounded-lg p-4" style="max-height: 70vh; overflow-y: auto;">
            <!-- Preview content will be loaded here -->
        </div>
        <div class="mt-6 flex justify-end space-x-3">
            <button onclick="closePdfPreview()" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                Cancel
            </button>
            <button onclick="downloadPDF()" class="px-6 py-2 bg-red-800 text-white rounded-lg hover:bg-red-700 transition-colors">
                Download PDF
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
        deleteSelectedBtn.disabled = selectedItems === 0;
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
        
        document.getElementById('selectedCount').textContent = `${selectedCards.length} items selected`;
        deleteSelectedBtn.disabled = selectedCards.length === 0;
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
                    
                    // Show success message
                    const successDiv = document.createElement('div');
                    successDiv.className = 'mb-6 p-4 bg-green-100 border border-green-200 rounded-xl text-green-800 flex items-center';
                    successDiv.innerHTML = `
                        <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        ${data.message || 'Records deleted successfully'}
                    `;
                    
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
                errorDiv.className = 'mb-6 p-4 bg-red-100 border border-red-200 rounded-xl text-red-800 flex items-center';
                errorDiv.innerHTML = `
                    <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    ${error.message}
                `;
                
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
        previewContent.innerHTML = '<div class="text-center py-8 text-gray-500"><svg class="w-8 h-8 mx-auto animate-spin text-red-600 mb-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Loading preview...</div>';
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
                previewContent.innerHTML = '<div class="text-center py-8 text-red-600"><svg class="w-8 h-8 mx-auto text-red-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Error loading preview</div>';
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
