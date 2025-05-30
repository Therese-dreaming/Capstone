@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8">
    <!-- Requests Table -->
    <div class="bg-white rounded-lg shadow-lg p-6">

        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-2">
                <h2 class="text-2xl font-bold">Request Status</h2>
                <div class="flex gap-4 items-center">
                    @if(auth()->user()->group_id == 2)
                    <div class="flex items-center gap-2">
                        <label for="showAssigned" class="text-sm font-medium text-gray-700">Show My Requests Only</label>
                        <input type="checkbox" id="showAssigned" class="form-checkbox h-4 w-4 text-red-600 rounded border-gray-300 focus:ring-red-500">
                    </div>
                    @endif
                    <div class="relative">
                        <input type="text" id="ticketSearch" placeholder="Search Ticket No." class="px-4 py-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                    </div>
                </div>
            </div>

            {{-- Container for dynamically inserted messages --}}
            <div id="message-container"></div>

            <!-- Cards for mobile -->
            <div class="grid grid-cols-1 gap-4 md:hidden" id="requestsCards">
                @foreach($requests as $request)
                <div class="bg-white rounded-lg shadow p-4 flex flex-col gap-2 border border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-red-800">{{ \Carbon\Carbon::parse($request->created_at)->format('M j, Y (g:i A)') }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($request->status === 'urgent') bg-red-100 text-red-800
                            @elseif($request->status === 'completed') bg-green-100 text-green-800
                            @elseif($request->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($request->status) }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-700"><span class="font-semibold">Ticket No.:</span> {{ $request->ticket_number ?? 'N/A' }}</div>
                    <div class="text-sm"><span class="font-semibold">Item:</span> {{ $request->equipment }}</div>
                    <div class="text-sm"><span class="font-semibold">Location:</span> {{ $request->location }}</div>
                    <div class="text-sm"><span class="font-semibold">Technician:</span> {{ $request->technician ? $request->technician->name : 'Not Assigned' }}</div>
                    <div class="text-sm"><span class="font-semibold">Issue:</span> {{ $request->issue }}</div>
                    <div class="text-sm"><span class="font-semibold">Asset:</span>
                        @if($request->asset)
                            <a href="{{ route('assets.index', ['search' => $request->asset->serial_number]) }}" class="font-bold text-red-600 hover:underline">{{ $request->asset->serial_number }}</a>
                        @else
                            Non-existent / Not Linked
                        @endif
                    </div>
                    <div class="flex gap-2 mt-2">
                        @if(auth()->user()->group_id == 1 || (auth()->user()->group_id == 2 && $request->technician_id == auth()->id()))
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
                        @else
                        <span class="text-gray-500 italic">No actions available</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Table for desktop -->
            <div class="overflow-x-auto hidden md:block">
                <table class="min-w-full" id="requestsTable">
                    <thead class="bg-[#960106]">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white w-[15%]">Request Date</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white w-[12%]">Ticket No.</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white w-[10%]">Asset / Serial No.</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white w-[15%]">Item</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white w-[15%]">Location</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white w-[8%]">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white w-[12%]">Technician</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white w-[15%]">Issue</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white w-[8%]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="tableBody">
                        @foreach($requests as $request)
                        <tr id="request-{{ $request->id }}" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900 truncate max-w-0">{{ \Carbon\Carbon::parse($request->created_at)->format('M j, Y (g:i A)') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium max-w-0">{{ $request->ticket_number ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 truncate max-w-0">
                                @if($request->asset)
                                    <a href="{{ route('assets.index', ['search' => $request->asset->serial_number]) }}" class="font-bold text-red-600 hover:underline">{{ $request->asset->serial_number }}</a>
                                @else
                                    Non-existent / Not Linked
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 truncate max-w-0">{{ $request->equipment }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 truncate max-w-0">{{ $request->location }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($request->status === 'urgent')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Urgent</span>
                                @elseif($request->status === 'completed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                                @elseif($request->status === 'cancelled')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-854medium bg-red-100 text-red-800">Cancelled</span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 truncate max-w-0">{{ $request->technician ? $request->technician->name : 'Not Assigned' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 truncate max-w-0">{{ $request->issue }}</td>                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="flex space-x-2">
                                    @if(auth()->user()->group_id == 1 || (auth()->user()->group_id == 2 && $request->technician_id == auth()->id()))
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
                                    @else
                                    <span class="text-gray-500 italic">No actions available</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
                            {{-- Removed 'in progress' as per user's implied status options from edit.blade.php --}}
                        </select>
                    </div>

                    <!-- Issue Textarea (Replacing Remarks) -->
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
                        <button type="button" onclick="closeCancelModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">No</button>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded" onclick="showPullOutConfirmation(event)">Yes</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pull Out Confirmation Modal -->
        <div id="pullOutModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-[51]">
            <div class="bg-white p-8 rounded-lg shadow-xl relative">
                <h2 class="text-xl font-bold mb-4">Pull Out Asset</h2>
                <p class="mb-4">Do you want to pull out this asset?</p>
                <div class="flex justify-end">
                    <button type="button" onclick="closePullOutModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">No</button>
                    <button type="button" onclick="confirmPullOut()" class="bg-red-600 text-white px-4 py-2 rounded">Yes</button>
                </div>
            </div>
        </div>

        <script>
            function showPullOutConfirmation(event) {
                event.preventDefault();
                document.getElementById('pullOutModal').classList.remove('hidden');
                document.getElementById('pullOutModal').classList.add('flex');
            }

            function closePullOutModal() {
                // Close the pull out modal
                document.getElementById('pullOutModal').classList.remove('flex');
                document.getElementById('pullOutModal').classList.add('hidden');
                
                // Close the cancel modal as well
                closeCancelModal();

                // Get the form data
                const form = document.getElementById('cancelForm');
                const formData = new FormData(form);

                // Set status to 'cancelled'
                formData.set('status', 'cancelled');

                // Set current date and time
                const now = new Date();
                const dateStr = now.getFullYear() + '-' +
                    String(now.getMonth() + 1).padStart(2, '0') + '-' +
                    String(now.getDate()).padStart(2, '0');
                const timeStr = String(now.getHours()).padStart(2, '0') + ':' +
                    String(now.getMinutes()).padStart(2, '0');
                formData.set('date_finished', dateStr);
                formData.set('time_finished', timeStr);

                // Submit the form with cancelled status
                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            const successMessage = document.createElement('div');
                            successMessage.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6';
                            successMessage.innerHTML = `
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">Asset status has been set to IN USE.</span>
                `;
                            document.querySelector('.flex-1').insertBefore(successMessage, document.querySelector('.flex-1').firstChild);
                            // Auto-hide the message after 5 seconds
                            setTimeout(() => {
                                successMessage.remove();
                            }, 5000);
                            
                            // Refresh the page to show updated status
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            throw new Error(data.message || 'Failed to update asset status');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || 'An error occurred while updating the asset status');
                    });
            }

            function confirmPullOut() {
                // Get the form data
                const form = document.getElementById('cancelForm');
                const formData = new FormData(form);
                formData.set('status', 'pulled_out');

                // Set current date and time
                const now = new Date();
                const dateStr = now.getFullYear() + '-' +
                    String(now.getMonth() + 1).padStart(2, '0') + '-' +
                    String(now.getDate()).padStart(2, '0');
                const timeStr = String(now.getHours()).padStart(2, '0') + ':' +
                    String(now.getMinutes()).padStart(2, '0');
                formData.set('date_finished', dateStr);
                formData.set('time_finished', timeStr);

                // Submit the form with updated status
                fetch(form.action, {
                        method: 'POST'
                        , body: formData
                        , headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Close both modals
                            document.getElementById('pullOutModal').classList.remove('flex');
                            document.getElementById('pullOutModal').classList.add('hidden');
                            closeCancelModal();
                            
                            // Show success message
                            const successMessage = document.createElement('div');
                            successMessage.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6';
                            successMessage.innerHTML = `
                            <strong class="font-bold">Success!</strong>
                            <span class="block sm:inline">Asset has been successfully pulled out.</span>
                        `;
                            document.querySelector('.flex-1').insertBefore(successMessage, document.querySelector('.flex-1').firstChild);
                            // Auto-hide the message after 5 seconds
                            setTimeout(() => {
                                successMessage.remove();
                            }, 5000);
                            
                            // Refresh the page to show updated status
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            throw new Error(data.message || 'Failed to pull out asset');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || 'An error occurred while pulling out the asset');
                    });
            }

            function openCompleteModal(requestId) {
                const modal = document.getElementById('completeModal');
                const form = document.getElementById('completeForm');
                form.action = `/repair-requests/${requestId}`;

                // Set current date and time
                const now = new Date();
                const dateStr = now.getFullYear() + '-' +
                    String(now.getMonth() + 1).padStart(2, '0') + '-' +
                    String(now.getDate()).padStart(2, '0');
                const timeStr = String(now.getHours()).padStart(2, '0') + ':' +
                    String(now.getMinutes()).padStart(2, '0');

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

                // Fetch request data via AJAX
                fetch(`/repair-requests/${requestId}/data`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to fetch request data');
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
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    })
                    .catch(error => {
                        console.error('Error fetching request data:', error);
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

                // --- Desktop Table Filtering ---
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

                // --- Mobile Card Filtering ---
                const cards = document.querySelectorAll('#requestsCards > div.bg-white');
                cards.forEach(card => {
                    let ticketNo = '', item = '', location = '';
                    card.querySelectorAll('.text-sm').forEach(div => {
                        const text = div.textContent.toLowerCase();
                        if (text.includes('ticket no.')) {
                            ticketNo = text.replace('ticket no.:', '').trim();
                        } else if (text.includes('item:')) {
                            item = text.replace('item:', '').trim();
                        } else if (text.includes('location:')) {
                            location = text.replace('location:', '').trim();
                        }
                    });
                    if (
                        ticketNo.includes(searchTerm) ||
                        item.includes(searchTerm) ||
                        location.includes(searchTerm)
                    ) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
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
                .then(async response => {
                    if (!response.ok) {
                        let errorMsg = 'Network response was not ok';
                        try {
                            const data = await response.json();
                            errorMsg = data.message || JSON.stringify(data);
                        } catch (err) {
                            errorMsg = await response.text();
                        }
                        throw new Error(errorMsg);
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
                    console.error('Error completing request:', error);
                    alert(error.message || 'An error occurred while completing the request');
                });
        });

        // Add this new event listener for the update form
        document.getElementById('updateForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const requestId = this.action.split('/').pop();
            const formData = new FormData(this);

             // If the technician select was hidden, ensure the hidden input's value is used
             @if(auth()->user()->group_id > 1)
                  formData.set('technician_id', document.getElementById('technician_id_hidden').value);
              @endif

            fetch(this.action, {
                    method: 'POST'
                    , body: formData
                    , headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                        , 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                    , credentials: 'same-origin'
                })
                .then(async response => {
                     if (!response.ok) {
                         const error = await response.json();
                         throw new Error(error.message || 'Failed to update request');
                     }
                     return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showSuccessMessage(data.message);

                        // Close modal
                        closeUpdateModal();

                        // Find the index of the request in the tableData array
                        const requestIndex = tableData.findIndex(item => item.id == requestId);

                        if (requestIndex !== -1) {
                            // Update the data in the tableData array
                            tableData[requestIndex].status = document.getElementById('status').value;
                            tableData[requestIndex].issue = document.getElementById('issue').value;

                             // Update technician data if applicable
                             const technicianSelect = document.getElementById('technician_id');
                             if (technicianSelect) { // If technician select is visible (Admin)
                                 const selectedTechnicianId = technicianSelect.value;
                                  tableData[requestIndex].technician_id = selectedTechnicianId;

                                  // Find the technician name from the options
                                 const selectedOption = technicianSelect.options[technicianSelect.selectedIndex];
                                  tableData[requestIndex].technician = selectedTechnicianId ? { name: selectedOption.text } : null; // Update technician object/name
                             } else { // If technician display is visible (Non-Admin)
                                  // Technician assignment cannot be changed by non-admins via this modal,
                                 // so we don't need to update technician_id in tableData here.
                                 // The technician name displayed in the table should already be correct.
                             }

                            // Re-render the table to show the updated data
                            updateTable();
                        }

                        // Reload the page after successful update
                        window.location.reload();
                    } else {
                        throw new Error(data.message || 'Failed to update request');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message || 'An error occurred while updating the request');
                });
        });

        // Add 'cancelled' status to the getStatusBadgeHTML function
        function getStatusBadgeHTML(status) {
            const statusMap = {
                'urgent': ['bg-red-100 text-red-800', 'Urgent']
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
            // Add z-50 class for high z-index
            alertDiv.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 z-50';
            alertDiv.innerHTML = `
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">${message}</span>
            `;

            // Insert alert at the top of the main content
            const messageContainer = document.getElementById('message-container');
            if (messageContainer) {
                messageContainer.appendChild(alertDiv);
            }

            // Automatically remove the message after 3 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }

    </script>
    @endsection

    <!-- Add this JavaScript code at the bottom of the file -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const showAssignedCheckbox = document.getElementById('showAssigned');
            const tableBody = document.getElementById('tableBody');
            const rows = tableBody.getElementsByTagName('tr');

            if (showAssignedCheckbox) {
                showAssignedCheckbox.addEventListener('change', function() {
                    const userId = '{{ auth()->id() }}';

                    Array.from(rows).forEach(row => {
                        const technicianCell = row.cells[5]; // Index of technician column
                        const technicianName = '{{ auth()->user()->name }}';

                        if (this.checked) {
                            // Show only rows where technician matches the current user
                            row.style.display = technicianCell.textContent.trim() === technicianName ? '' : 'none';
                        } else {
                            // Show all rows
                            row.style.display = '';
                        }
                    });
                });
            }
        });

    </script>
