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

    <!-- Main Container -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Lab Schedule History</h1>
            <div class="space-x-3">
                <button onclick="exportToPDF()" class="text-sm px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Export to PDF
                </button>
            </div>
        </div>

        <!-- Enhanced Filters Section -->
        <div class="mb-6">
            <!-- Filter Grid -->
            <div class="grid grid-cols-5 gap-4">
                <div>
                    <label for="labFilter" class="block text-sm font-medium text-gray-700 mb-1">Laboratory</label>
                    <select id="labFilter" class="h-9 w-full px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        <option value="">All Laboratories</option>
                        <option value="Laboratory 401">Laboratory 401</option>
                        <option value="Laboratory 402">Laboratory 402</option>
                        <option value="Laboratory 403">Laboratory 403</option>
                        <option value="Laboratory 404">Laboratory 404</option>
                        <option value="Laboratory 405">Laboratory 405</option>
                        <option value="Laboratory 406">Laboratory 406</option>
                    </select>
                </div>
                <div>
                    <label for="deptFilter" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select id="deptFilter" class="h-9 w-full px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        <option value="">All Departments</option>
                        <option value="Grade School">Grade School</option>
                        <option value="Elementary">Elementary</option>
                        <option value="Junior High School">Junior High School</option>
                        <option value="Senior High School">Senior High School</option>
                        <option value="College">College</option>
                    </select>
                </div>
                <div>
                    <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="statusFilter" class="h-9 w-full px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        <option value="">All Status</option>
                        <option value="Scheduled">Scheduled</option>
                        <option value="On Going">On Going</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div>
                    <label for="startDateFilter" class="block text-sm font-medium text-gray-700 mb-1">Start Date From</label>
                    <input type="date" id="startDateFilter" class="h-9 w-full px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label for="endDateFilter" class="block text-sm font-medium text-gray-700 mb-1">Start Date To</label>
                    <input type="date" id="endDateFilter" class="h-9 w-full px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
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

        <!-- History Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg">
                <thead class="bg-[#960106]">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Laboratory</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Department</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Subject/Course</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Professor</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Start Time</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">End Time</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Coordinator</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($labHistory as $schedule)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="selected[]" value="{{ $schedule->id }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-3 py-1.5 text-xs font-medium rounded-full inline-flex items-center justify-center min-w-[90px]
                                        @if($schedule->laboratory == 'Laboratory 401') bg-blue-200 text-blue-800
                                        @elseif($schedule->laboratory == 'Laboratory 402') bg-green-200 text-green-800
                                        @elseif($schedule->laboratory == 'Laboratory 403') bg-yellow-200 text-yellow-800
                                        @elseif($schedule->laboratory == 'Laboratory 404') bg-purple-200 text-purple-800
                                        @elseif($schedule->laboratory == 'Laboratory 405') bg-pink-200 text-pink-800
                                        @else bg-indigo-200 text-indigo-800
                                        @endif">
                                {{ $schedule->laboratory }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $schedule->department }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $schedule->subject_course }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $schedule->professor }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($schedule->start)->format('M j, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($schedule->start)->format('g:i A') }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($schedule->end)->format('M j, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($schedule->end)->format('g:i A') }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1.5 text-xs font-medium rounded-full inline-flex items-center justify-center min-w-[90px]
                                @if($schedule->status === 'Completed') bg-gray-100 text-gray-800
                                @elseif($schedule->status === 'On Going') bg-yellow-100 text-yellow-800 
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ $schedule->status ?? 'Scheduled' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $schedule->collaborator->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button onclick="confirmDelete({{ $schedule->id }})" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">No past schedules found</td>
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
        <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete the selected schedule(s)? This action cannot be undone.</p>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const labFilter = document.getElementById('labFilter');
        const deptFilter = document.getElementById('deptFilter');
        const statusFilter = document.getElementById('statusFilter');
        const startDateFilter = document.getElementById('startDateFilter');
        const endDateFilter = document.getElementById('endDateFilter');
        const rows = document.querySelectorAll('tbody tr');

        function filterTable() {
            const selectedLab = labFilter.value;
            const selectedDept = deptFilter.value;
            const selectedStatus = statusFilter.value;
            const selectedStartDate = startDateFilter.value ? new Date(startDateFilter.value + 'T00:00:00') : null;
            const selectedEndDate = endDateFilter.value ? new Date(endDateFilter.value + 'T23:59:59') : null;

            rows.forEach(row => {
                if (row.cells.length === 1) return; // Skip the "No past schedules found" row

                const labText = row.cells[1].textContent.trim();
                const deptText = row.cells[2].textContent.trim();
                const statusText = row.cells[7].textContent.trim();

                // Get the start date from the 6th column (index 5)
                const startDateText = row.cells[5].textContent; // Format: "Apr 30, 2025 h:mm A"
                const startDate = new Date(startDateText);

                // Reset the time part of startDate to midnight for date-only comparison
                const rowDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());

                const matchLab = !selectedLab || labText.includes(selectedLab);
                const matchDept = !selectedDept || deptText === selectedDept;
                const matchStatus = !selectedStatus || statusText === selectedStatus;

                // Compare dates properly considering the full day
                const matchStartDate = !selectedStartDate || rowDate >= selectedStartDate;
                const matchEndDate = !selectedEndDate || rowDate <= selectedEndDate;

                row.style.display = (matchLab && matchDept && matchStatus && matchStartDate && matchEndDate) ? '' : 'none';
            });
        }

        labFilter.addEventListener('change', filterTable);
        deptFilter.addEventListener('change', filterTable);
        statusFilter.addEventListener('change', filterTable);
        startDateFilter.addEventListener('change', filterTable);
        endDateFilter.addEventListener('change', filterTable);

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
            // Send delete request to server
            fetch('/lab-schedule/delete', {
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
                    // Remove deleted rows from the table
                    itemsToDelete.forEach(id => {
                        const row = document.querySelector(`input[value="${id}"]`).closest('tr');
                        if (row) {
                            row.remove();
                        }
                    });

                    // Clear selection
                    document.getElementById('selectAll').checked = false;
                    updateSelectedCount();

                    // Create and show success message
                    const successDiv = document.createElement('div');
                    successDiv.className = 'mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700';
                    successDiv.textContent = 'Schedule(s) deleted successfully';

                    // Insert at the top of the content area
                    const contentArea = document.querySelector('.flex-1.p-8');
                    contentArea.insertBefore(successDiv, contentArea.firstChild);

                    // Remove the message after 3 seconds
                    setTimeout(() => {
                        successDiv.remove();
                    }, 3000);

                    // If all items are deleted, show the empty message
                    const remainingRows = document.querySelectorAll('tbody tr:not([style*="display: none"])');
                    if (remainingRows.length === 0) {
                        const tbody = document.querySelector('tbody');
                        const emptyRow = document.createElement('tr');
                        emptyRow.innerHTML = '<td colspan="9" class="px-6 py-4 text-center text-gray-500">No past schedules found</td>';
                        tbody.appendChild(emptyRow);
                    }
                } else {
                    // Create and show error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700';
                    errorDiv.textContent = data.message || 'Error deleting schedule(s)';

                    // Insert at the top of the content area
                    const contentArea = document.querySelector('.flex-1.p-8');
                    contentArea.insertBefore(errorDiv, contentArea.firstChild);

                    // Remove the message after 3 seconds
                    setTimeout(() => {
                        errorDiv.remove();
                    }, 3000);
                }
            })
            .catch(error => {
                closeDeleteModal();
                // Create and show error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700';
                errorDiv.textContent = 'An error occurred while deleting schedule(s)';

                // Insert at the top of the content area
                const contentArea = document.querySelector('.flex-1.p-8');
                contentArea.insertBefore(errorDiv, contentArea.firstChild);

                // Remove the message after 3 seconds
                setTimeout(() => {
                    errorDiv.remove();
                }, 3000);
            });
        }
    });

</script>
@endsection
