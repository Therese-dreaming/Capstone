@extends('layouts.app')

@section('content')
<div class="flex-1 p-8 ml-72">
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
        <strong class="font-bold">Success!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Remove the old alertContainer div -->
    
    <h2 class="text-2xl font-semibold mb-6">Request Status</h2>

    <!-- Statistics Section -->
    <div class="grid grid-cols-2 gap-6 mb-6">
        <!-- Status Overview -->
        <div class="bg-yellow-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-2">Status Overview</h3>
            <p class="text-sm text-gray-600 mb-4">Key metrics for repair requests and technician performance</p>

            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-600">Total Open</p>
                    <p class="text-2xl font-semibold">{{ $totalOpen }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-600">Completed this Month</p>
                    <p class="text-2xl font-semibold">{{ $completedThisMonth }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-600">Average Response Time</p>
                    <p class="text-2xl font-semibold">{{ round($avgResponseTime) }} days</p>
                </div>
            </div>
            
            <!-- Add View Completed Button -->
            <div class="mt-4 flex justify-end">
                <button onclick="window.location.href='{{ route('repair.completed') }}'" 
                        class="bg-[#960106] text-white px-4 py-2 rounded hover:bg-red-700 transition-colors">
                    View Completed Requests
                </button>
            </div>
        </div>

        <!-- Technician Performance -->
        <div class="bg-teal-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-2">Technician Performance</h3>
            <p class="text-sm text-gray-600">Admin view only</p>
            <!-- Add technician performance metrics here -->
        </div>
    </div>

    <!-- Urgent Repairs Section -->
    @if($urgentRepairs->count() > 0)
    <div class="mb-6">
        <div class="bg-[#960106] text-white p-4 rounded-t-lg">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="text-xl font-semibold">URGENT REPAIRS ({{ $urgentRepairs->count() }})</h3>
            </div>
        </div>

        <div class="bg-white p-4 rounded-b-lg shadow-lg">
            <!-- Remove this div containing the dropdown -->
            <!-- <div class="flex justify-between items-center mb-4">
                <select id="urgentEntriesPerPage" class="border rounded px-2 py-1">
                    <option value="5" selected>5 per page</option>
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                </select>
            </div> -->

            <div id="urgentTableBody">
                <!-- Urgent repairs will be populated here -->
            </div>

            <div class="mt-4 flex items-center justify-between">
                <div id="urgentTableInfo" class="text-sm text-gray-700">
                    Showing <span id="urgentStartEntry">1</span> to <span id="urgentEndEntry">5</span> of <span id="urgentTotalEntries">0</span> entries
                </div>
                <div class="flex gap-2" id="urgentPagination">
                    <button class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50" id="urgentPrevPage">&lt;</button>
                    <div id="urgentPageNumbers" class="flex gap-1"></div>
                    <button class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50" id="urgentNextPage">&gt;</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        let urgentCurrentPage = 1;
        let urgentEntriesPerPage = 5;  // Fixed at 5
        let urgentData = @json($urgentRepairs);

        function updateUrgentTable() {
            const startIndex = (urgentCurrentPage - 1) * urgentEntriesPerPage;
            const endIndex = Math.min(startIndex + urgentEntriesPerPage, urgentData.length);
            
            const tableBody = document.getElementById('urgentTableBody');
            tableBody.innerHTML = '';

            for (let i = startIndex; i < endIndex; i++) {
                const repair = urgentData[i];
                const div = document.createElement('div');
                div.className = 'text-gray-800 p-2 border-b border-gray-100 last:border-0';
                div.innerHTML = `→ ${repair.office_room} - ${repair.equipment} - ${repair.technician ? repair.technician.name : 'Not Assigned'}
                    (${new Date(repair.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} 
                    ${new Date(repair.created_at).toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })})`;
                tableBody.appendChild(div);
            }

            updateUrgentTableInfo(startIndex + 1, endIndex, urgentData.length);
            updateUrgentPagination(urgentData.length);
        }

        function updateUrgentTableInfo(start, end, total) {
            document.getElementById('urgentStartEntry').textContent = total === 0 ? 0 : start;
            document.getElementById('urgentEndEntry').textContent = end;
            document.getElementById('urgentTotalEntries').textContent = total;
        }

        function updateUrgentPagination(totalItems) {
            const totalPages = Math.ceil(totalItems / urgentEntriesPerPage);
            const pageNumbers = document.getElementById('urgentPageNumbers');
            pageNumbers.innerHTML = '';

            for (let i = 1; i <= totalPages; i++) {
                const button = document.createElement('button');
                button.textContent = i;
                button.className = `px-3 py-1 rounded ${urgentCurrentPage === i ? 'bg-[#960106] text-white' : 'bg-gray-200'}`;
                button.onclick = () => {
                    urgentCurrentPage = i;
                    updateUrgentTable();
                };
                pageNumbers.appendChild(button);
            }

            document.getElementById('urgentPrevPage').disabled = urgentCurrentPage === 1;
            document.getElementById('urgentNextPage').disabled = urgentCurrentPage === totalPages;
        }

        // Remove the dropdown event listener
        // document.getElementById('urgentEntriesPerPage').addEventListener('change', ...);

        // Keep only these event listeners
        document.getElementById('urgentPrevPage').addEventListener('click', () => {
            if (urgentCurrentPage > 1) {
                urgentCurrentPage--;
                updateUrgentTable();
            }
        });

        document.getElementById('urgentNextPage').addEventListener('click', () => {
            const totalPages = Math.ceil(urgentData.length / urgentEntriesPerPage);
            if (urgentCurrentPage < totalPages) {
                urgentCurrentPage++;
                updateUrgentTable();
            }
        });

        // Initialize urgent repairs table when page loads
        document.addEventListener('DOMContentLoaded', updateUrgentTable);
    </script>

    <!-- Requests Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-[#960106] text-white p-4">
            <h3 class="text-xl font-semibold">Requests</h3>
        </div>

        <table class="min-w-full" id="requestsTable">
            <thead class="bg-[#960106]">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Request Date</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Item</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Department</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Lab Room</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Technician</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" id="tableBody">
                @foreach($requests as $request)
                <tr class="relative group cursor-pointer hover:bg-red-100 transition-colors duration-200" 
                    onclick="openUpdateModal('{{ $request->id }}')"
                    data-id="{{ $request->id }}">
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($request->created_at)->format('M j, Y (g:i A)') }}</td>
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
                    <td class="px-6 py-4">
                        <button onclick="event.preventDefault(); event.stopPropagation(); openDeleteModal('{{ $request->id }}')" class="text-red-600 hover:text-red-900">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </td>
                    <div class="absolute inset-0 hidden group-hover:flex items-center justify-center">
                        <span class="text-white text-xl font-bold">Update</span>
                    </div>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Add these modals before the closing div -->
        <!-- Update Modal -->
        <div id="updateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
            <div class="bg-white p-8 rounded-lg shadow-xl w-96">
                <h2 class="text-xl font-bold mb-4">Update Request</h2>
                <!-- In the Update Modal form, remove the JavaScript event handling -->
                <form id="updateForm" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="technician_id">
                            Technician
                        </label>
                        <select id="technician_id" name="technician_id" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
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
                        <select id="status" name="status" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Select Status</option>
                            <option value="urgent">Urgent</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="date_finished">
                            Date Finished
                        </label>
                        <input type="date" id="date_finished" name="date_finished" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="time_finished">
                            Time Finished
                        </label>
                        <input type="time" id="time_finished" name="time_finished" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="remarks">
                            Remarks
                        </label>
                        <textarea id="remarks" name="remarks" rows="3" required
                                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" onclick="closeUpdateModal()" 
                                class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                        <button type="submit" 
                                class="bg-[#960106] text-white px-4 py-2 rounded">Update</button>
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
                        <button type="button" onclick="event.preventDefault(); closeDeleteModal()" 
                                class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                        <button type="submit" 
                                class="bg-[#960106] text-white px-4 py-2 rounded">Delete</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add this to your existing script section -->
        <script>
            function openUpdateModal(requestId) {
                const modal = document.getElementById('updateModal');
                const form = document.getElementById('updateForm');
                form.action = `/repair-requests/${requestId}`;
                
                // Set current date and time as default
                const now = new Date();
                const dateStr = now.toISOString().split('T')[0];
                const timeStr = now.toTimeString().slice(0, 5);
                
                document.getElementById('date_finished').value = dateStr;
                document.getElementById('time_finished').value = timeStr;
                
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
                form.action = `/repair-requests/delete/${requestId}`;  // Updated route
                
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
            tableData = rows;  // Store the rows directly instead of creating objects
            updateTable();
        }

        // Update table display
        function updateTable() {
            const startIndex = (currentPage - 1) * entriesPerPage;
            const endIndex = Math.min(startIndex + entriesPerPage, tableData.length);
            
            const tableBody = document.getElementById('tableBody');
            tableBody.innerHTML = '';

            for (let i = startIndex; i < endIndex; i++) {
                const originalRow = tableData[i];
                const requestId = originalRow.getAttribute('data-id');
                
                const newRow = originalRow.cloneNode(true);
                newRow.addEventListener('click', function(e) {
                    if (!e.target.closest('button')) {
                        openUpdateModal(requestId);
                    }
                });

                const deleteButton = newRow.querySelector('button');
                if (deleteButton) {
                    deleteButton.onclick = (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        openDeleteModal(requestId);
                    };
                }

                tableBody.appendChild(newRow);
            }

            updateTableInfo(startIndex + 1, endIndex, tableData.length);
            updatePagination(tableData.length);
        }

        // Update table information
        function updateTableInfo(start, end, total) {
            document.getElementById('startEntry').textContent = total === 0 ? 0 : start;
            document.getElementById('endEntry').textContent = end;
            document.getElementById('totalEntries').textContent = tableData.length;  // Use actual table data length
        }

        // Update pagination controls
        function updatePagination(totalItems) {
            const totalPages = Math.ceil(tableData.length / entriesPerPage);  // Use actual table data length
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

        // Remove these event listeners since we removed the elements
        // document.getElementById('entriesPerPage').addEventListener('change', ...);
        // document.getElementById('tableSearch').addEventListener('input', ...);

        document.getElementById('prevPage').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                updateTable();  // Remove searchTerm parameter
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            const totalPages = Math.ceil(tableData.length / entriesPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                updateTable();  // Remove searchTerm parameter
            }
        });

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', initializeTable);
    </script>
    @endsection