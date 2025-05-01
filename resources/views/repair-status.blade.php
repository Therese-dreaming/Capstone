@extends('layouts.app')

@section('content')
<div class="flex-1 p-8 ml-72">
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
        <strong class="font-bold">Success!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Request Status</h2>
        <div class="flex gap-4">
            <div class="relative">
                <input type="text" id="ticketSearch" placeholder="Search Ticket No." class="px-4 py-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
            </div>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-[#960106] text-white p-4">
            <h3 class="text-xl font-semibold">Requests</h3>
        </div>

        <table class="min-w-full" id="requestsTable">
            <thead class="bg-[#960106]">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Request Date</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Ticket No.</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Item</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Location</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Technician</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Issue</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" id="tableBody">
                @foreach($requests as $request)
                <tr id="request-{{ $request->id }}" class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($request->created_at)->format('M j, Y (g:i A)') }}</td>
                    <td class="px-6 py-4 font-medium">{{ $request->ticket_number ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $request->equipment }}</td>
                    <td class="px-6 py-4">{{ $request->location }}</td>
                    <td class="px-6 py-4">
                        @if($request->status === 'urgent')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Urgent
                        </span>
                        @elseif($request->status === 'in_progress')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            In Progress
                        </span>
                        @elseif($request->status === 'completed')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Completed
                        </span>
                        @elseif($request->status === 'cancelled')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Cancelled
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Pending
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        {{ $request->technician ? $request->technician->name : 'Not Assigned' }}
                    </td>
                    <td class="px-6 py-4">{{ $request->issue }}</td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <button onclick="openUpdateModal('{{ $request->id }}')" class="bg-yellow-600 text-white p-1.5 rounded hover:bg-yellow-700 tooltip" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button onclick="openCompleteModal('{{ $request->id }}')" class="bg-green-600 text-white p-1.5 rounded hover:bg-green-700 tooltip" title="Complete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                            <button onclick="openCancelModal('{{ $request->id }}')" class="bg-red-600 text-white p-1.5 rounded hover:bg-red-700 tooltip" title="Cancel">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Add these modals before the closing div -->
        <!-- Update Modal -->
        <div id="updateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white p-8 rounded-lg shadow-xl w-96 relative">
                <h2 class="text-xl font-bold mb-4">Update Request</h2>
                <form id="updateForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="technician_id">
                            Technician
                        </label>
                        <select id="technician_id" name="technician_id" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Select Technician</option>
                            @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                            Status
                        </label>
                        <select id="status" name="status" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="pending">Pending</option>
                            <option value="urgent">Urgent</option>
                            <option value="in_progress">In Progress</option>
                            <option value="pulled_out">Pulled Out</option>
                            <option value="disposed">Disposed</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="remarks">
                            Remarks
                        </label>
                        <textarea id="remarks" name="remarks" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter reason for status change..."></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" onclick="closeUpdateModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                        <button type="submit" class="bg-[#960106] text-white px-4 py-2 rounded">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white p-8 rounded-lg shadow-xl relative">
                <h2 class="text-xl font-bold mb-4">Confirm Delete</h2>
                <p class="mb-4">Are you sure you want to delete this request?</p>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-end">
                        <button type="button" onclick="event.preventDefault(); closeDeleteModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                        <button type="submit" class="bg-[#960106] text-white px-4 py-2 rounded">Delete</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Complete Modal -->
        <div id="completeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white p-8 rounded-lg shadow-xl relative">
                <h2 class="text-xl font-bold mb-4">Mark as Complete</h2>
                <form id="completeForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="completed">
                    <input type="hidden" name="date_finished" id="completeDateFinished">
                    <input type="hidden" name="time_finished" id="completeTimeFinished">
                    <input type="hidden" name="technician_id" value="{{ auth()->user()->id }}">

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="complete_remarks">
                            Completion Remarks
                        </label>
                        <textarea name="remarks" id="complete_remarks" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="Enter completion remarks..." required></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" onclick="closeCompleteModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Complete</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cancel Modal -->
        <div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white p-8 rounded-lg shadow-xl relative">
                <h2 class="text-xl font-bold mb-4">Cancel Request</h2>
                <form id="cancelForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="cancelled">
                    <input type="hidden" name="date_finished" id="cancelDateFinished">
                    <input type="hidden" name="time_finished" id="cancelTimeFinished">

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="cancel_remarks">
                            Cancellation Reason
                        </label>
                        <textarea name="remarks" id="cancel_remarks" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="Enter reason for cancellation..." required></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" onclick="closeCancelModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Back</button>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Cancel Request</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add this to your existing script section -->
        <script>
            function openCompleteModal(requestId) {
                const modal = document.getElementById('completeModal');
                const form = document.getElementById('completeForm');
                form.action = `/repair-requests/${requestId}`;

                // Set current date and time
                const now = new Date();
                const dateStr = now.toISOString().split('T')[0];
                const timeStr = now.toTimeString().slice(0, 5);

                document.getElementById('completeDateFinished').value = dateStr;
                document.getElementById('completeTimeFinished').value = timeStr;

                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeCompleteModal() {
                const modal = document.getElementById('completeModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            // Update the openUpdateModal function
            function openUpdateModal(requestId) {
                const modal = document.getElementById('updateModal');
                const form = document.getElementById('updateForm');
                form.action = `/repair-requests/${requestId}`;

                // Find the request in the table
                const row = document.querySelector(`tr[id="request-${requestId}"]`);
                if (!row) return;

                // Get current status from the status cell's text content
                const statusCell = row.querySelector('td:nth-child(5) span'); // Changed from 6 to 5
                let currentStatus = statusCell.textContent.trim().toLowerCase();

                // Convert display text to status value
                switch (currentStatus) {
                    case 'in progress':
                        currentStatus = 'in_progress';
                        break;
                    case 'pulled out':
                        currentStatus = 'pulled_out';
                        break;
                    case 'pending':
                        currentStatus = 'pending';
                        break;
                    case 'urgent':
                        currentStatus = 'urgent';
                        break;
                    case 'disposed':
                        currentStatus = 'disposed';
                        break;
                }

                // Set current status
                const statusSelect = document.getElementById('status');
                if (statusSelect.querySelector(`option[value="${currentStatus}"]`)) {
                    statusSelect.value = currentStatus;
                }

                // Set current technician if one is assigned
                const technicianName = row.querySelector('td:nth-child(6)').textContent.trim(); // Changed from 7 to 6
                const technicianSelect = document.getElementById('technician_id');

                if (technicianName !== 'Not Assigned') {
                    Array.from(technicianSelect.options).forEach(option => {
                        if (option.text === technicianName) {
                            option.selected = true;
                        }
                    });
                } else {
                    technicianSelect.value = ''; // Reset to "Select Technician"
                }

                // Clear remarks field
                document.getElementById('remarks').value = '';

                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeUpdateModal() {
                const modal = document.getElementById('updateModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            function openDeleteModal(requestId) {
                const modal = document.getElementById('deleteModal');
                const form = document.getElementById('deleteForm');
                form.action = `/repair-requests/delete/${requestId}`; // Updated route

                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeDeleteModal() {
                const modal = document.getElementById('deleteModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            // Update the openCancelModal function
            function openCancelModal(requestId) {
                const modal = document.getElementById('cancelModal');
                const form = document.getElementById('cancelForm');
                form.action = `/repair-requests/${requestId}`;

                // Set current date and time
                const now = new Date();
                const dateStr = now.toISOString().split('T')[0];
                const timeStr = now.toTimeString().slice(0, 5);

                document.getElementById('cancelDateFinished').value = dateStr;
                document.getElementById('cancelTimeFinished').value = timeStr;

                // Clear any previous remarks
                document.getElementById('cancel_remarks').value = '';

                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeCancelModal() {
                const modal = document.getElementById('cancelModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            // Update the cancel form handler
            document.getElementById('cancelForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append('_method', 'PUT');
                formData.append('status', 'cancelled');
                formData.append('date_finished', document.getElementById('cancelDateFinished').value);
                formData.append('time_finished', document.getElementById('cancelTimeFinished').value);
                formData.append('remarks', document.getElementById('cancel_remarks').value);

                const requestId = this.action.split('/').pop();

                fetch(`/repair-requests/${requestId}`, {
                        method: 'POST'
                        , body: formData
                        , headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                            , 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            closeCancelModal();
                            // Remove the row from the table instead of redirecting
                            const row = document.getElementById(`request-${requestId}`);
                            if (row) row.remove();
                            // Show success message
                            showSuccessMessage(data.message);
                        } else {
                            throw new Error(data.message || 'Failed to cancel request');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || 'An error occurred while cancelling the request');
                    });
            });

        </script>

        <div class="p-4 flex items-center justify-between border-t">
            <div id="tableInfo" class="text-sm text-gray-700">
                Showing <span id="startEntry">1</span> to <span id="endEntry">10</span> of <span id="totalEntries">0</span> entries
            </div>
            <div class="flex gap-2" id="pagination">
                <button class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50" id="prevPage">&lt;</button>
                <div id="pageNumbers" class="flex gap-1"></div>
                <button class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50" id="nextPage">&gt;</button>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let entriesPerPage = 10;
        let tableData = [];
        let originalData = []; // Store original data permanently

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Store initial data
            originalData = Array.from(document.querySelectorAll('#tableBody tr'));
            tableData = [...originalData];

            updateTable();

            // Add search event listener
            const searchInput = document.getElementById('ticketSearch');
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();

                if (searchTerm === '') {
                    tableData = [...originalData];
                } else {
                    tableData = originalData.filter(row => {
                        const ticketNo = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                        const item = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                        const location = row.querySelector('td:nth-child(4)').textContent.toLowerCase();

                        return ticketNo.includes(searchTerm) ||
                            item.includes(searchTerm) ||
                            location.includes(searchTerm);
                    });
                }

                currentPage = 1; // Reset to first page
                updateTable();
            });
        });

        function updateTable() {
            const startIndex = (currentPage - 1) * entriesPerPage;
            const endIndex = Math.min(startIndex + entriesPerPage, tableData.length);

            const tableBody = document.getElementById('tableBody');
            tableBody.innerHTML = '';

            for (let i = startIndex; i < endIndex; i++) {
                const clonedRow = tableData[i].cloneNode(true);
                tableBody.appendChild(clonedRow);
            }

            updateTableInfo(startIndex + 1, endIndex, tableData.length);
            updatePagination();
        }

        // Update table information
        function updateTableInfo(start, end, total) {
            document.getElementById('startEntry').textContent = total === 0 ? 0 : start;
            document.getElementById('endEntry').textContent = end;
            document.getElementById('totalEntries').textContent = tableData.length; // Use actual table data length
        }

        // Update pagination controls
        function updatePagination(totalItems) {
            const totalPages = Math.ceil(tableData.length / entriesPerPage); // Use actual table data length
            const pageNumbers = document.getElementById('pageNumbers');
            pageNumbers.innerHTML = '';

            // Add page numbers
            for (let i = 1; i <= totalPages; i++) {
                const button = document.createElement('button');
                button.textContent = i;
                button.className = `px-3 py-1 rounded ${currentPage === i ? 'bg-[#960106] text-white' : 'bg-gray-200'}`;
                button.onclick = () => {
                    currentPage = i;
                    updateTable();
                };
                pageNumbers.appendChild(button);
            }

            // Update navigation buttons
            document.getElementById('prevPage').disabled = currentPage === 1;
            document.getElementById('nextPage').disabled = currentPage === totalPages || totalPages === 0;
        }

        document.getElementById('prevPage').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                updateTable(); // Remove searchTerm parameter
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            const totalPages = Math.ceil(tableData.length / entriesPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                updateTable(); // Remove searchTerm parameter
            }
        });

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize table first
            initializeTable();

            // Check for stored request ID from urgent repairs page
            const highlightRequestId = sessionStorage.getItem('highlightRequestId');
            if (highlightRequestId) {
                // Clear the stored ID
                sessionStorage.removeItem('highlightRequestId');

                // Find the row index
                const targetRow = tableData.find(row => row.id === `request-${highlightRequestId}`);
                if (targetRow) {
                    // Calculate which page the row should be on
                    const rowIndex = tableData.indexOf(targetRow);
                    currentPage = Math.ceil((rowIndex + 1) / entriesPerPage);

                    // Update table to show correct page
                    updateTable();

                    // After table updates, highlight the row
                    setTimeout(() => {
                        const element = document.getElementById(`request-${highlightRequestId}`);
                        if (element) {
                            element.scrollIntoView({
                                behavior: 'smooth'
                                , block: 'center'
                            });
                            element.classList.add('bg-yellow-100');
                            setTimeout(() => {
                                element.classList.remove('bg-yellow-100');
                            }, 2000);
                        }
                    }, 100);
                }
            }
        });

        // Replace the complete form submission handler
        document.getElementById('completeForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('_method', 'PUT');
            formData.append('status', 'completed');

            const requestId = this.action.split('/').pop();

            fetch(`/repair-requests/${requestId}`, {
                    method: 'POST'
                    , body: formData
                    , headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                        , 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        closeCompleteModal();
                        // Remove the row from the table instead of redirecting
                        const row = document.getElementById(`request-${requestId}`);
                        if (row) row.remove();
                        // Show success message
                        showSuccessMessage(data.message);
                    } else {
                        throw new Error(data.message || 'Failed to complete request');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while completing the request');
                });
        });

        // Add this new event listener for the update form
        document.getElementById('updateForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const requestId = this.action.split('/').pop();
            const formData = new FormData(this);

            fetch(this.action, {
                    method: 'POST'
                    , body: formData
                    , headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                        , 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                    , credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessMessage(data.message);

                        // Close modal
                        closeUpdateModal();

                        // Find and update the row in tableData array
                        const rowIndex = tableData.findIndex(row => row.id === `request-${requestId}`);
                        if (rowIndex !== -1) {
                            const row = tableData[rowIndex];

                            // Update status cell (5th column)
                            const statusCell = row.querySelector('td:nth-child(5)');
                            const newStatus = document.getElementById('status').value;
                            statusCell.innerHTML = getStatusBadgeHTML(newStatus);

                            // Update technician cell (6th column)
                            const technicianCell = row.querySelector('td:nth-child(6)');
                            const technicianSelect = document.getElementById('technician_id');
                            const selectedTechnician = technicianSelect.options[technicianSelect.selectedIndex];
                            technicianCell.textContent = selectedTechnician.text || 'Not Assigned';

                            // Update the tableData array
                            tableData[rowIndex] = row;
                        }

                        // Update the table display
                        updateTable();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });

        // Add 'cancelled' status to the getStatusBadgeHTML function
        function getStatusBadgeHTML(status) {
            const statusMap = {
                'urgent': ['bg-red-100 text-red-800', 'Urgent']
                , 'in_progress': ['bg-yellow-100 text-yellow-800', 'In Progress']
                , 'pulled_out': ['bg-gray-100 text-gray-800', 'Pulled Out']
                , 'disposed': ['bg-gray-100 text-gray-800', 'Disposed']
                , 'pending': ['bg-gray-100 text-gray-800', 'Pending']
                , 'cancelled': ['bg-red-100 text-red-800', 'Cancelled'] // Add this line
            };

            const [classes, label] = statusMap[status] || statusMap['pending'];
            return `
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${classes}">
                        ${label}
                    </span>
                `;
        }

        function showSuccessMessage(message) {
            // Remove any existing success messages
            const existingAlerts = document.querySelectorAll('.bg-green-100.border-green-400');
            existingAlerts.forEach(alert => alert.remove());

            // Create and show new success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6';
            alertDiv.innerHTML = `
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">${message}</span>
            `;

            // Insert alert at the top of the content
            const content = document.querySelector('.flex-1.p-8.ml-72');
            content.insertBefore(alertDiv, content.firstChild);

            // Automatically remove the message after 3 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }

    </script>
    @endsection
