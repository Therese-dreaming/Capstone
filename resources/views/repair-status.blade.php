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
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Department</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Room</th>
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
                    <td class="px-6 py-4">{{ $request->department }}</td>
                    <td class="px-6 py-4">{{ $request->office_room }}</td>
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
                            <button onclick="openUpdateModal('{{ $request->id }}')" class="bg-yellow-600 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-yellow-700">
                                Edit
                            </button>
                            <button onclick="openCompleteModal('{{ $request->id }}')" class="bg-green-600 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-green-700">
                                Complete
                            </button>
                            <button onclick="openDeleteModal('{{ $request->id }}')" class="bg-red-600 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-red-700">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Add these modals before the closing div -->
        <!-- Update Modal -->
        <div id="updateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
            <div class="bg-white p-8 rounded-lg shadow-xl w-96">
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
        <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
            <div class="bg-white p-8 rounded-lg shadow-xl">
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
        <div id="completeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
            <div class="bg-white p-8 rounded-lg shadow-xl">
                <h2 class="text-xl font-bold mb-4">Mark as Complete</h2>
                <p class="mb-4">Are you sure you want to mark this request as complete?</p>
                <form id="completeForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="completed">
                    <input type="hidden" name="date_finished" id="completeDateFinished">
                    <input type="hidden" name="time_finished" id="completeTimeFinished">
                    <input type="hidden" name="technician_id" value="{{ auth()->user()->id }}">
                    <input type="hidden" name="remarks" value="Marked as completed">
                    <input type="hidden" name="redirect" value="completed">

                    <div class="flex justify-end">
                        <button type="button" onclick="closeCompleteModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Complete</button>
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

            function openUpdateModal(requestId) {
                const modal = document.getElementById('updateModal');
                const form = document.getElementById('updateForm');
                form.action = `/repair-requests/${requestId}`;

                // Find the request in the table
                const row = document.querySelector(`tr[id="request-${requestId}"]`);

                // Get current status from the status cell's text content
                const statusCell = row.querySelector('td:nth-child(6) span');
                let currentStatus = statusCell.textContent.trim().toLowerCase();

                // Convert display text to status value
                switch (currentStatus) {
                    case 'in progress':
                        currentStatus = 'in_progress';
                        break;
                    case 'pulled out':
                        currentStatus = 'pulled_out';
                        break;
                        // other statuses don't need conversion
                }

                // Set current status
                const statusSelect = document.getElementById('status');
                if (statusSelect.querySelector(`option[value="${currentStatus}"]`)) {
                    statusSelect.value = currentStatus;
                }

                // Set current technician if one is assigned
                const technicianName = row.querySelector('td:nth-child(7)').textContent.trim();
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

        // Initialize table data
        function initializeTable() {
            const rows = Array.from(document.querySelectorAll('#tableBody tr'));
            tableData = rows; // Store the rows directly instead of creating objects
            updateTable();
        }

        // Update table display
        // Remove the updateTable function and replace with this simplified version
        function updateTable() {
            const startIndex = (currentPage - 1) * entriesPerPage;
            const endIndex = Math.min(startIndex + entriesPerPage, tableData.length);

            const tableBody = document.getElementById('tableBody');
            tableBody.innerHTML = '';

            for (let i = startIndex; i < endIndex; i++) {
                const originalRow = tableData[i];
                const newRow = originalRow.cloneNode(true);
                tableBody.appendChild(newRow);
            }

            updateTableInfo(startIndex + 1, endIndex, tableData.length);
            updatePagination(tableData.length);
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

        // Modify the complete form submission handler
        document.getElementById('completeForm').addEventListener('submit', function(e) {
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
                        closeCompleteModal();

                        // Remove from main table and update
                        const tableBody = document.getElementById('tableBody');
                        const rows = Array.from(tableBody.getElementsByTagName('tr'));

                        for (let i = 0; i < rows.length; i++) {
                            if (rows[i].querySelector(`button[onclick*="${requestId}"]`)) {
                                rows[i].remove();
                                break;
                            }
                        }

                        // Update table data and display
                        tableData = Array.from(document.querySelectorAll('#tableBody tr'));
                        if (currentPage > Math.ceil(tableData.length / entriesPerPage)) {
                            currentPage = Math.max(1, Math.ceil(tableData.length / entriesPerPage));
                        }
                        updateTable();

                    }
                })
                .catch(error => {
                    console.error('Error:', error);
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
                            // Update status cell
                            const statusCell = row.querySelector('td:nth-child(6)');
                            const newStatus = document.getElementById('status').value;
                            statusCell.innerHTML = getStatusBadgeHTML(newStatus);

                            // Update technician cell
                            const technicianCell = row.querySelector('td:nth-child(7)');
                            const technicianSelect = document.getElementById('technician_id');
                            const selectedTechnician = technicianSelect.options[technicianSelect.selectedIndex];
                            technicianCell.textContent = selectedTechnician.text;

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

        // Helper function to generate status badge HTML
        function getStatusBadgeHTML(status) {
            const statusMap = {
                'urgent': ['bg-red-100 text-red-800', 'Urgent']
                , 'in_progress': ['bg-yellow-100 text-yellow-800', 'In Progress']
                , 'pulled_out': ['bg-gray-100 text-gray-800', 'Pulled Out']
                , 'disposed': ['bg-gray-100 text-gray-800', 'Disposed']
                , 'pending': ['bg-gray-100 text-gray-800', 'Pending']
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
