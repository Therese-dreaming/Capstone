@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8">
    <!-- Requests Table -->
    <div class="bg-white rounded-lg shadow-lg p-6">

        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-2">
                <h2 class="text-2xl font-bold">Request Status</h2>
                <div class="flex flex-col md:flex-row gap-4 items-center">
                    @if(auth()->user()->group_id == 2)
                    <div class="flex items-center gap-2">
                        <label for="showAssigned" class="text-sm font-medium text-gray-700">Show My Requests Only</label>
                        <input type="checkbox" id="showAssigned" class="form-checkbox h-4 w-4 text-red-600 rounded border-gray-300 focus:ring-red-500">
                    </div>
                    @endif
                    <div class="flex flex-col md:flex-row gap-2">
                        <div class="relative">
                            <input type="text" id="ticketSearch" placeholder="Search Ticket No." class="px-4 py-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                        </div>
                        <select id="statusFilter" class="px-4 py-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="urgent">Urgent</option>
                            <option value="in_progress">In Progress</option>
                        </select>
                        <div class="flex gap-2">
                            <input type="date" id="dateFrom" class="px-4 py-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                            <input type="date" id="dateTo" class="px-4 py-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Container for dynamically inserted messages --}}
            <div id="message-container"></div>

            @if($requests->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 px-4 bg-white rounded-lg border-2 border-dashed border-gray-300">
                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No Repair Requests</h3>
                <p class="text-sm text-gray-500 text-center max-w-sm">
                    There are currently no repair requests to display. New requests will appear here once they are created.
                </p>
            </div>
            @else
            <!-- Cards for all screen sizes -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="requestsCards">
                @foreach($requests as $request)
                <div class="bg-white rounded-lg shadow p-4 flex flex-col gap-2 border border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="font-semibold text-red-800">{{ \Carbon\Carbon::parse($request->created_at)->format('M j, Y') }}</div>
                            <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($request->created_at)->format('g:i A') }}</div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium whitespace-nowrap
                            @if($request->status === 'urgent') bg-red-100 text-red-800
                            @elseif($request->status === 'completed') bg-green-100 text-green-800
                            @elseif($request->status === 'cancelled') bg-red-100 text-red-800
                            @elseif($request->status === 'in_progress') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif">
                            @if($request->status === 'in_progress')
                                In Progress
                            @else
                                {{ ucfirst($request->status) }}
                            @endif
                        </span>
                    </div>
                    <div class="text-sm text-gray-700"><span class="font-semibold">Ticket No.:</span> {{ $request->ticket_number ?? 'N/A' }}</div>
                    <div class="text-sm"><span class="font-semibold">Item:</span>
                        @if($request->asset && $request->asset->name !== $request->equipment)
                            <div class="text-gray-500 text-xs">Original: {{ $request->equipment }}</div>
                            <div class="text-red-600">Current: {{ $request->asset->name }}</div>
                        @else
                            {{ $request->equipment }}
                        @endif
                    </div>
                    <div class="text-sm"><span class="font-semibold">Location:</span> {{ $request->location }}</div>
                    <div class="text-sm"><span class="font-semibold">Technician:</span> {{ $request->technician ? $request->technician->name : 'Not Assigned' }}</div>
                    <div class="text-sm"><span class="font-semibold">Time Started:</span> 
                        @if($request->time_started)
                            <div>{{ \Carbon\Carbon::parse($request->time_started)->format('M j, Y') }}</div>
                            <div class="text-gray-500">{{ \Carbon\Carbon::parse($request->time_started)->format('g:i A') }}</div>
                        @else
                            -
                        @endif
                    </div>
                    <div class="text-sm"><span class="font-semibold">Issue:</span> 
                        <div class="mt-1 text-gray-600">{{ $request->issue }}</div>
                    </div>
                    <div class="text-sm"><span class="font-semibold">Asset:</span>
                        @if($request->asset)
                            <a href="{{ route('assets.index', ['search' => $request->asset->serial_number]) }}" class="font-bold text-red-600 hover:underline">{{ $request->asset->serial_number }}</a>
                        @else
                            -
                        @endif
                    </div>
                    <div class="flex gap-2 mt-auto pt-2 border-t">
                        @if(auth()->user()->group_id == 1 || (auth()->user()->group_id == 2 && $request->technician_id == auth()->id()))
                        <button onclick="openUpdateModal('{{ $request->id }}')" class="bg-yellow-600 text-white p-1.5 rounded hover:bg-yellow-700 tooltip" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        @if(!$request->technician_id)
                        <button onclick="openAssignTechnicianModal('{{ $request->id }}')" class="bg-blue-600 text-white p-1.5 rounded hover:bg-blue-700 tooltip" title="Assign Technician">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </button>
                        @elseif(!$request->time_started)
                        <a href="{{ route('repair.identify-asset', $request->id) }}" class="bg-green-600 text-white p-1.5 rounded hover:bg-green-700 tooltip inline-block" title="Complete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3-3m0 0l-3 3m3-3v12M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9" />
                            </svg>
                        </a>
                        <button onclick="startRepair('{{ $request->id }}')" class="bg-blue-600 text-white p-1.5 rounded hover:bg-blue-700 tooltip" title="Start Repair">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                        @else
                        <a href="{{ route('repair.completion-form', $request->id) }}" class="bg-green-600 text-white p-1.5 rounded hover:bg-green-700 tooltip inline-block" title="Complete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </a>
                        @endif
                        <button onclick="openCancelModal('{{ $request->id }}')" class="bg-red-600 text-white p-1.5 rounded hover:bg-red-700 tooltip" title="Cancel">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        @else
                        <span class="text-gray-500 italic">No actions</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Pagination -->
            <div class="mt-6">
                {{ $requests->links() }}
            </div>

            <!-- Update Modal -->
            <div id="updateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-[95%] md:max-w-[500px] p-3 md:p-5 relative">
                    <!-- Header -->
                    <div class="bg-gray-50 p-6 rounded-t-lg border-b">
                        <h2 class="text-xl font-bold text-gray-800">Update Request</h2>
                        <p class="text-sm text-gray-600 mt-1">Modify the request details below</p>
                    </div>

                    <!-- Form Content -->
                    <form id="updateForm" method="POST" class="p-6 space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Display Location (Read-only) -->
                         <div class="space-y-2">
                            <label class="block text-gray-700 text-sm font-semibold" for="location_display">
                                Location
                            </label>
                            <p id="location_display" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700 select-text"></p>
                        </div>

                        <!-- Technician Selection (Conditional) -->
                        @if(auth()->user()->group_id == 1)
                        <div class="space-y-2">
                            <label class="block text-gray-700 text-sm font-semibold" for="technician_id">
                                Technician Assignment
                            </label>
                            <select id="technician_id" name="technician_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition duration-150 ease-in-out">
                                <option value="">Select a technician...</option>
                                @foreach($technicians as $technician)
                                <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        {{-- Display Technician for non-Admins --}}
                        <div class="space-y-2">
                            <label class="block text-gray-700 text-sm font-semibold">
                                Assigned Technician
                            </label>
                             <p id="technician_display" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700 select-text"></p>
                            <input type="hidden" name="technician_id" id="technician_id_hidden">
                        </div>
                        @endif

                        <!-- Status Selection -->
                        <div class="space-y-2">
                            <label class="block text-gray-700 text-sm font-semibold" for="status">
                                Request Status
                            </label>
                            <select id="status" name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition duration-150 ease-in-out">
                                <option value="pending" class="text-gray-800">Pending</option>
                                <option value="urgent" class="text-red-800">Urgent</option>
                                <option value="pulled_out" class="text-yellow-800">Pulled Out</option>
                                <option value="disposed" class="text-red-800">Disposed</option>
                            </select>
                        </div>

                        <!-- Issue Textarea -->
                        <div class="space-y-2">
                            <label class="block text-gray-700 text-sm font-semibold" for="issue">
                                Issue
                            </label>
                            <textarea id="issue" name="issue" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition duration-150 ease-in-out resize-none" placeholder="Enter the issue description..."></textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <button type="button" onclick="closeUpdateModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#960106] rounded-md hover:bg-[#7d0105] focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                                Update Request
                            </button>
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
                            <button type="button" onclick="closeCancelModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">No</button>
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Yes</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Start Repair Confirmation Modal -->
            <div id="startRepairModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white p-8 rounded-lg shadow-xl relative">
                    <h2 class="text-xl font-bold mb-4">Start Repair</h2>
                    <p class="mb-4">Are you sure you want to start this repair?</p>
                    <div class="flex justify-end">
                        <button type="button" onclick="closeStartRepairModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">No</button>
                        <button type="button" id="confirmStartRepairBtn" onclick="confirmStartRepair()" class="bg-blue-600 text-white px-4 py-2 rounded flex items-center">
                            <span class="mr-2">Yes</span>
                            <svg class="animate-spin h-5 w-5 text-white hidden" id="startRepairSpinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Add Assign Technician Modal -->
            <div id="assignTechnicianModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white p-8 rounded-lg shadow-xl relative">
                    <h2 class="text-xl font-bold mb-4">Assign Technician</h2>
                    <form id="assignTechnicianForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="pending">
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="technician_id">
                                Select Technician
                            </label>
                            <select name="technician_id" id="assign_technician_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-1 focus:ring-red-500" required>
                                <option value="">Select a technician...</option>
                                @foreach($technicians as $technician)
                                <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" onclick="closeAssignTechnicianModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Assign</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                function openUpdateModal(requestId) {
                    const modal = document.getElementById('updateModal');
                    const form = document.getElementById('updateForm');
                    
                    // Use relative URLs
                    const dataUrl = `repair-requests/${requestId}/data`;
                    form.action = `repair-requests/${requestId}`;

                    // Get CSRF token
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // Fetch request data via AJAX
                    fetch(dataUrl, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Populate modal fields with fetched data
                        document.getElementById('location_display').textContent = data.location;

                        const technicianSelect = document.getElementById('technician_id');
                        const technicianDisplay = document.getElementById('technician_display');
                        const technicianIdHidden = document.getElementById('technician_id_hidden');

                        if (technicianSelect) { // If technician select is visible (for Admins/Technicians)
                            technicianSelect.value = data.technician_id || '';
                        } else if (technicianDisplay) { // If technician display is visible (for Secretaries)
                            technicianDisplay.textContent = data.technician ? data.technician.name : 'Not Assigned';
                            technicianIdHidden.value = data.technician_id || ''; // Keep hidden input for submission
                        }

                        const statusSelect = document.getElementById('status');
                        // Select the correct status option, defaulting to 'pending' if status is not in the list
                        if (statusSelect.querySelector(`option[value="${data.status}"]`)) {
                            statusSelect.value = data.status;
                        } else {
                            statusSelect.value = 'pending'; // Default to pending if current status isn't an option
                        }

                        document.getElementById('issue').value = data.issue;

                        // Show the modal after data is populated
                        if (modal) {
                            modal.classList.remove('hidden');
                            modal.classList.add('flex');
                        }
                    })
                    .catch(error => {
                        alert('Could not load request data. Please try again.');
                    });
                }

                function closeUpdateModal() {
                    const modal = document.getElementById('updateModal');
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    // Clear fields on close (optional, but good practice)
                     document.getElementById('updateForm').reset();
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
                    const dateStr = now.getFullYear() + '-' +
                        String(now.getMonth() + 1).padStart(2, '0') + '-' +
                        String(now.getDate()).padStart(2, '0');
                    const timeStr = String(now.getHours()).padStart(2, '0') + ':' +
                        String(now.getMinutes()).padStart(2, '0');

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

                    fetch(`repair-requests/${requestId}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            closeCancelModal();
                            // Show success message
                            const successMessage = document.createElement('div');
                            successMessage.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6';
                            successMessage.innerHTML = `
                                <strong class="font-bold">Success!</strong>
                                <span class="block sm:inline">${data.message}</span>
                            `;
                            document.getElementById('message-container').appendChild(successMessage);
                            // Auto-hide the message after 5 seconds
                            setTimeout(() => {
                                successMessage.remove();
                            }, 5000);
                            
                            // Refresh the page to show updated status
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            throw new Error(data.message || 'Failed to cancel request');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || 'An error occurred while cancelling the request');
                    });
                });

                // Add these new functions for start repair modal
                function startRepair(requestId) {
                    const modal = document.getElementById('startRepairModal');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    // Store the requestId in a data attribute on the modal
                    modal.setAttribute('data-request-id', requestId);
                }

                function closeStartRepairModal() {
                    const modal = document.getElementById('startRepairModal');
                    const confirmBtn = document.getElementById('confirmStartRepairBtn');
                    const spinner = document.getElementById('startRepairSpinner');
                    
                    // Reset button state
                    confirmBtn.disabled = false;
                    confirmBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    spinner.classList.add('hidden');
                    
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                function confirmStartRepair() {
                    const confirmBtn = document.getElementById('confirmStartRepairBtn');
                    const spinner = document.getElementById('startRepairSpinner');
                    
                    // Disable button and show spinner
                    confirmBtn.disabled = true;
                    confirmBtn.classList.add('opacity-75', 'cursor-not-allowed');
                    spinner.classList.remove('hidden');
                    
                    const modal = document.getElementById('startRepairModal');
                    const requestId = modal.getAttribute('data-request-id');
                    
                    const formData = new FormData();
                    formData.append('_method', 'PUT');
                    
                    // Format current date and time
                    const now = new Date();
                    const dateStr = now.getFullYear() + '-' +
                        String(now.getMonth() + 1).padStart(2, '0') + '-' +
                        String(now.getDate()).padStart(2, '0');
                    const timeStr = String(now.getHours()).padStart(2, '0') + ':' +
                        String(now.getMinutes()).padStart(2, '0');
                    
                    const timeStarted = `${dateStr} ${timeStr}`;
                    
                    formData.append('time_started', timeStarted);
                    formData.append('status', 'in_progress');

                    // Get CSRF token
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // Use relative URL
                    const url = `repair-requests/${requestId}`;

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                    .then(async response => {
                        const responseText = await response.text();
                        
                        if (!response.ok) {
                            let errorMessage = 'Failed to start repair';
                            try {
                                const errorData = JSON.parse(responseText);
                                errorMessage = errorData.message || errorMessage;
                            } catch (e) {
                                if (responseText.includes('<!DOCTYPE')) {
                                    const errorMatch = responseText.match(/<title>(.*?)<\/title>/);
                                    if (errorMatch) {
                                        errorMessage = errorMatch[1];
                                    }
                                }
                            }
                            throw new Error(errorMessage);
                        }
                        
                        try {
                            return JSON.parse(responseText);
                        } catch (e) {
                            throw new Error('Invalid server response');
                        }
                    })
                    .then(data => {
                        if (data.success) {
                            closeStartRepairModal();
                            // Show success message
                            const successMessage = document.createElement('div');
                            successMessage.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6';
                            successMessage.innerHTML = `
                                <strong class="font-bold">Success!</strong>
                                <span class="block sm:inline">${data.message}</span>
                            `;
                            document.getElementById('message-container').appendChild(successMessage);
                            
                            // Auto-hide the message after 5 seconds
                            setTimeout(() => {
                                successMessage.remove();
                            }, 5000);
                            
                            // Refresh the page to show updated buttons
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            throw new Error(data.message || 'Failed to start repair');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Show error message
                        const errorMessage = document.createElement('div');
                        errorMessage.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6';
                        errorMessage.innerHTML = `
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">${error.message}</span>
                        `;
                        document.getElementById('message-container').appendChild(errorMessage);
                        
                        // Auto-hide the message after 5 seconds
                        setTimeout(() => {
                            errorMessage.remove();
                        }, 5000);
                    })
                    .finally(() => {
                        // Re-enable button and hide spinner
                        confirmBtn.disabled = false;
                        confirmBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                        spinner.classList.add('hidden');
                    });
                }

                // Add these new functions for assign technician modal
                function openAssignTechnicianModal(requestId) {
                    const modal = document.getElementById('assignTechnicianModal');
                    const form = document.getElementById('assignTechnicianForm');
                    form.action = `repair-requests/${requestId}`;
                    
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }

                function closeAssignTechnicianModal() {
                    const modal = document.getElementById('assignTechnicianModal');
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                // Add event listener for assign technician form
                document.getElementById('assignTechnicianForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    formData.append('_method', 'PUT');

                    const requestId = this.action.split('/').pop();

                    fetch(`repair-requests/${requestId}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            closeAssignTechnicianModal();
                            // Show success message
                            const successMessage = document.createElement('div');
                            successMessage.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6';
                            successMessage.innerHTML = `
                                <strong class="font-bold">Success!</strong>
                                <span class="block sm:inline">${data.message}</span>
                            `;
                            document.getElementById('message-container').appendChild(successMessage);
                            // Auto-hide the message after 5 seconds
                            setTimeout(() => {
                                successMessage.remove();
                            }, 5000);
                            
                            // Refresh the page to show updated status
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            throw new Error(data.message || 'Failed to assign technician');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || 'An error occurred while assigning the technician');
                    });
                });

            </script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('ticketSearch');
                    const statusFilter = document.getElementById('statusFilter');
                    const dateFrom = document.getElementById('dateFrom');
                    const dateTo = document.getElementById('dateTo');
                    const cards = document.querySelectorAll('#requestsCards > div.bg-white');
                    const showAssignedCheckbox = document.getElementById('showAssigned');

                    // Function to filter cards
                    function filterCards() {
                        const searchTerm = searchInput.value.toLowerCase().trim();
                        const selectedStatus = statusFilter.value.toLowerCase();
                        const fromDate = dateFrom.value ? new Date(dateFrom.value) : null;
                        const toDate = dateTo.value ? new Date(dateTo.value) : null;
                        const showAssignedOnly = showAssignedCheckbox ? showAssignedCheckbox.checked : false;
                        const currentUser = '{{ auth()->user()->name }}';

                        cards.forEach(card => {
                            let ticketNo = '';
                            let item = '';
                            let location = '';
                            let technician = '';
                            let status = '';
                            let date = '';

                            // Get all text content from the card
                            card.querySelectorAll('.text-sm').forEach(div => {
                                const text = div.textContent.toLowerCase();
                                if (text.includes('ticket no.')) {
                                    ticketNo = text.replace('ticket no.:', '').trim();
                                } else if (text.includes('item:')) {
                                    item = text.replace('item:', '').trim();
                                } else if (text.includes('location:')) {
                                    location = text.replace('location:', '').trim();
                                } else if (text.includes('technician:')) {
                                    technician = text.replace('technician:', '').trim();
                                }
                            });

                            // Get status from the badge
                            const statusBadge = card.querySelector('.inline-flex.items-center');
                            if (statusBadge) {
                                status = statusBadge.textContent.trim().toLowerCase();
                                // Convert "in progress" to "in_progress" for comparison
                                if (status === 'in progress') {
                                    status = 'in_progress';
                                }
                            }

                            // Get date from the first line
                            const dateElement = card.querySelector('.font-semibold.text-red-800');
                            if (dateElement) {
                                // Parse the date string (e.g., "Jun 10, 2025")
                                const dateStr = dateElement.textContent.trim();
                                const [month, day, year] = dateStr.split(/[\s,]+/);
                                const monthIndex = new Date(`${month} 1, 2000`).getMonth();
                                date = new Date(year, monthIndex, parseInt(day));
                            }

                            // Check if card matches all filters
                            const matchesSearch = searchTerm === '' || 
                                ticketNo.includes(searchTerm) || 
                                item.includes(searchTerm) || 
                                location.includes(searchTerm);

                            const matchesStatus = selectedStatus === '' || 
                                status === selectedStatus.toLowerCase();

                            // Compare dates by setting time to midnight for accurate date comparison
                            const matchesDate = (!fromDate || (date && date.setHours(0,0,0,0) >= fromDate.setHours(0,0,0,0))) && 
                                              (!toDate || (date && date.setHours(0,0,0,0) <= toDate.setHours(0,0,0,0)));

                            const matchesAssigned = !showAssignedOnly || 
                                technician === currentUser.toLowerCase();

                            // Show/hide card based on all filters
                            card.style.display = (matchesSearch && matchesStatus && matchesDate && matchesAssigned) ? '' : 'none';
                        });

                        // Update pagination info
                        updatePaginationInfo();
                    }

                    // Function to update pagination info
                    function updatePaginationInfo() {
                        const visibleCards = Array.from(cards).filter(card => card.style.display !== 'none');
                        const totalVisible = visibleCards.length;
                        
                        // Update the pagination info if it exists
                        const tableInfo = document.getElementById('tableInfo');
                        if (tableInfo) {
                            const startEntry = document.getElementById('startEntry');
                            const endEntry = document.getElementById('endEntry');
                            const totalEntries = document.getElementById('totalEntries');
                            
                            if (startEntry && endEntry && totalEntries) {
                                startEntry.textContent = totalVisible > 0 ? '1' : '0';
                                endEntry.textContent = totalVisible;
                                totalEntries.textContent = totalVisible;
                            }
                        }
                    }

                    // Add event listeners
                    if (searchInput) {
                        searchInput.addEventListener('input', filterCards);
                    }

                    if (statusFilter) {
                        statusFilter.addEventListener('change', filterCards);
                    }

                    if (dateFrom) {
                        dateFrom.addEventListener('change', filterCards);
                    }

                    if (dateTo) {
                        dateTo.addEventListener('change', filterCards);
                    }

                    if (showAssignedCheckbox) {
                        showAssignedCheckbox.addEventListener('change', filterCards);
                    }

                    // Initial filter
                    filterCards();
                });
            </script>

        </div>
    </div>
</div>
@endsection

