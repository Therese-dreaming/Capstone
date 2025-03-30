@extends('layouts.app')

@section('content')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<div class="flex-1 p-8 ml-72">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">UPCOMING MAINTENANCE</h2>
        <div class="flex justify-end space-x-4">
            <button onclick="switchView('table')" id="table-view-btn" class="px-4 py-2 bg-red-800 text-white rounded-md hover:bg-red-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                Table View
            </button>
            <button onclick="switchView('calendar')" id="calendar-view-btn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Calendar View
            </button>
        </div>
    </div>

    <!-- Table View Section -->
    <div id="table-view" class="bg-white rounded-lg shadow-md p-6 hidden">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Scheduled Maintenance List</h3>
            <div class="relative">
                <input type="text" id="table-search" placeholder="Search by serial number..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <!-- In the table header, add the Actions column -->
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technician</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <!-- Modify the renderMaintenanceTable function to include the action buttons -->
                <script>
                    // Add the action handler functions
                    function markAsDone(id) {
                        if (confirm('Mark this maintenance as completed?')) {
                            fetch(`/maintenance/${id}/complete`, {
                                    method: 'POST'
                                    , headers: {
                                        'Content-Type': 'application/json'
                                        , 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    loadMaintenanceTable();
                                    alert('Maintenance marked as completed');
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Error marking maintenance as completed');
                                });
                        }
                    }

                    function deleteMaintenance(id) {
                        if (confirm('Are you sure you want to delete this maintenance schedule?')) {
                            fetch(`/maintenance/${id}`, {
                                    method: 'DELETE'
                                    , headers: {
                                        'Content-Type': 'application/json'
                                        , 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    loadMaintenanceTable();
                                    alert('Maintenance schedule deleted');
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Error deleting maintenance schedule');
                                });
                        }
                    }

                </script>
                <tbody id="maintenance-table-body" class="bg-white divide-y divide-gray-200">
                    <!-- Table content will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Calendar Section -->
    <div id="calendar-view" class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Maintenance Calendar</h3>
            <div class="flex gap-4 items-center bg-gray-50 rounded-lg p-2">
                <button onclick="changeYear(-1)" class="p-2 hover:bg-gray-200 rounded-full transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>
                <button onclick="changeMonth(-1)" class="p-2 hover:bg-gray-200 rounded-full transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <span id="current-month-year" class="text-lg font-medium min-w-[200px] text-center text-gray-700"></span>
                <button onclick="changeMonth(1)" class="p-2 hover:bg-gray-200 rounded-full transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <button onclick="changeYear(1)" class="p-2 hover:bg-gray-200 rounded-full transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
        <div id="calendar" class="grid grid-cols-7 gap-2">
            <!-- Calendar will be populated by JavaScript -->
        </div>
    </div>

    <!-- Schedule Details Modal -->
    <div id="schedules-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-3/4 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-900" id="modal-date"></h3>
                    <div class="flex gap-4 items-center">
                        <div class="relative">
                            <input type="text" id="schedule-search" placeholder="Search by serial number..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <button onclick="closeSchedulesModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Close
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technician</th>
                            </tr>
                        </thead>
                        <tbody id="schedules-list" class="bg-white divide-y divide-gray-200">
                            <!-- Schedules will be populated here -->
                        </tbody>
                    </table>
                    <div id="no-schedules" class="hidden text-center py-4 text-gray-500">
                        No maintenance scheduled for this date.
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Delete Maintenance Schedule</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to delete this maintenance schedule? This action cannot be undone.</p>
                </div>
                <div class="flex justify-center gap-4 mt-5">
                    <button id="confirm-delete-btn" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Delete
                    </button>
                    <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mark as Done Confirmation Modal -->
    <div id="complete-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Complete Maintenance</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to mark this maintenance as completed?</p>
                </div>
                <div class="flex justify-center gap-4 mt-5">
                    <button id="confirm-complete-btn" class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Complete
                    </button>
                    <button onclick="closeCompleteModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Keep your existing JavaScript -->
    <script>
        let currentSchedules = []; // Move this to the top
        let currentDate = new Date(); // Move this to the top

        // Fix the calendar functions
        function generateCalendar() {
            const calendar = document.getElementById('calendar');
            const monthYearDisplay = document.getElementById('current-month-year');
            const currentMonth = currentDate.getMonth();
            const currentYear = currentDate.getFullYear();

            // Update month/year display
            monthYearDisplay.textContent = new Date(currentYear, currentMonth).toLocaleDateString('en-US', {
                month: 'long'
                , year: 'numeric'
            });

            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            const firstDay = new Date(currentYear, currentMonth, 1).getDay();
            const lastDayPrevMonth = new Date(currentYear, currentMonth, 0).getDate();

            // Calendar header
            calendar.innerHTML = `
                <div class="col-span-7 grid grid-cols-7 gap-2 mb-2">
                    <div class="text-center font-semibold">Sun</div>
                    <div class="text-center font-semibold">Mon</div>
                    <div class="text-center font-semibold">Tue</div>
                    <div class="text-center font-semibold">Wed</div>
                    <div class="text-center font-semibold">Thu</div>
                    <div class="text-center font-semibold">Fri</div>
                    <div class="text-center font-semibold">Sat</div>
                </div>
            `;

            let dayCount = 1;
            let nextMonthDay = 1;

            // Create 6 rows to ensure consistent calendar height
            for (let week = 0; week < 6; week++) {
                for (let dayOfWeek = 0; dayOfWeek < 7; dayOfWeek++) {
                    const dayNumber = week * 7 + dayOfWeek;
                    const date = new Date(currentYear, currentMonth, dayCount);

                    if (dayNumber < firstDay) {
                        // Previous month days
                        const prevDay = lastDayPrevMonth - firstDay + dayNumber + 1;
                        calendar.innerHTML += `
                            <div class="p-2 border rounded-md bg-gray-50 text-gray-400 text-center">
                                ${prevDay}
                            </div>`;
                    } else if (dayCount > daysInMonth) {
                        // Next month days
                        calendar.innerHTML += `
                            <div class="p-2 border rounded-md bg-gray-50 text-gray-400 text-center">
                                ${nextMonthDay++}
                            </div>`;
                    } else {
                        // Current month days
                        const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(dayCount).padStart(2, '0')}`;
                        calendar.innerHTML += `
                            <div class="p-2 border rounded-md hover:bg-gray-50 cursor-pointer text-center" 
                                onclick="showSchedules('${dateStr}')">
                                ${dayCount}
                            </div>`;
                        dayCount++;
                    }
                }
            }

            // After calendar is generated, fetch and mark scheduled dates
            const monthStart = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-01`;
            const monthEnd = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(daysInMonth).padStart(2, '0')}`;

            fetch(`/maintenance/schedules/month/${monthStart}/${monthEnd}`)
                .then(response => response.json())
                .then(schedules => {
                    schedules.forEach(schedule => {
                        const scheduleDate = new Date(schedule.scheduled_date);
                        const dayElement = calendar.querySelector(`[onclick*="${scheduleDate.getFullYear()}-${String(scheduleDate.getMonth() + 1).padStart(2, '0')}-${String(scheduleDate.getDate()).padStart(2, '0')}"]`);
                        if (dayElement) {
                            dayElement.classList.add('border-red-500', 'border-2');
                        }
                    });
                });
        }

        function showSchedules(date) {
            const modal = document.getElementById('schedules-modal');
            const dateDisplay = document.getElementById('modal-date');

            // Format and display the date
            dateDisplay.textContent = new Date(date).toLocaleDateString('en-US', {
                weekday: 'long'
                , year: 'numeric'
                , month: 'long'
                , day: 'numeric'
            });

            // Show the modal before fetching data
            modal.classList.remove('hidden');

            // Fetch schedules for the selected date
            fetch(`/maintenance/schedules/${date}`)
                .then(response => response.json())
                .then(schedules => {
                    currentSchedules = schedules;
                    renderSchedules(schedules);
                })
                .catch(error => {
                    console.error('Error fetching schedules:', error);
                    document.getElementById('schedules-list').innerHTML = `
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-red-500">
                                Error loading schedules
                            </td>
                        </tr>
                    `;
                });
        }

        function renderSchedules(schedules) {
            const list = document.getElementById('schedules-list');
            const noSchedules = document.getElementById('no-schedules');

            if (schedules.length === 0) {
                list.innerHTML = '';
                noSchedules.classList.remove('hidden');
            } else {
                noSchedules.classList.add('hidden');
                list.innerHTML = schedules.map(schedule => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${schedule.asset.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${schedule.serial_number}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${schedule.task}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${schedule.technician.name}</td>
                </tr>
            `).join('');
            }
        }

        // Add search functionality
        document.getElementById('schedule-search').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const filteredSchedules = currentSchedules.filter(schedule =>
                schedule.serial_number.toLowerCase().includes(searchTerm)
            );
            renderSchedules(filteredSchedules);
        });

        function closeSchedulesModal() {
            document.getElementById('schedules-modal').classList.add('hidden');
        }

        // Initialize calendar when page loads
        document.addEventListener('DOMContentLoaded', generateCalendar);

        // View switching functionality
        function switchView(view) {
            const tableView = document.getElementById('table-view');
            const calendarView = document.getElementById('calendar-view');
            const tableBtn = document.getElementById('table-view-btn');
            const calendarBtn = document.getElementById('calendar-view-btn');

            if (view === 'table') {
                tableView.classList.remove('hidden');
                calendarView.classList.add('hidden');
                tableBtn.classList.remove('bg-gray-200', 'text-gray-700');
                tableBtn.classList.add('bg-red-800', 'text-white');
                calendarBtn.classList.remove('bg-red-800', 'text-white');
                calendarBtn.classList.add('bg-gray-200', 'text-gray-700');
                loadMaintenanceTable();
            } else {
                tableView.classList.add('hidden');
                calendarView.classList.remove('hidden');
                calendarBtn.classList.remove('bg-gray-200', 'text-gray-700');
                calendarBtn.classList.add('bg-red-800', 'text-white');
                tableBtn.classList.remove('bg-red-800', 'text-white');
                tableBtn.classList.add('bg-gray-200', 'text-gray-700');
            }
        }

        // Load maintenance table data
        function loadMaintenanceTable() {
            fetch('/maintenance/upcoming')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(schedules => {
                    console.log('Received schedules:', schedules); // Debug line
                    renderMaintenanceTable(schedules);
                })
                .catch(error => {
                    console.error('Error loading maintenance data:', error);
                    document.getElementById('maintenance-table-body').innerHTML = `
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Error loading maintenance data
                            </td>
                        </tr>
                    `;
                });
        }

        // Initialize both views when page loads
        document.addEventListener('DOMContentLoaded', function() {
            generateCalendar();
            loadMaintenanceTable(); // Load table data immediately
            switchView('table'); // Changed from 'calendar' to 'table'
        });

        // Add error handling to table search
        document.getElementById('table-search').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            fetch(`/maintenance/upcoming?search=${encodeURIComponent(searchTerm)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(filteredSchedules => {
                    renderMaintenanceTable(filteredSchedules);
                })
                .catch(error => {
                    console.error('Error searching maintenance data:', error);
                });
        });

        function renderMaintenanceTable(schedules) {
            const tableBody = document.getElementById('maintenance-table-body');

            if (!schedules || schedules.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No scheduled maintenance found
                        </td>
                    </tr>
                `;
                return;
            }

            tableBody.innerHTML = schedules.map(schedule => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${new Date(schedule.scheduled_date).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        ${schedule.asset ? schedule.asset.name : 'N/A'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${schedule.serial_number || 'N/A'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${schedule.task || schedule.maintenance_task || 'N/A'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${schedule.technician ? schedule.technician.name : 'N/A'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            ${schedule.status || 'Scheduled'}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="flex space-x-2">
                            <button onclick="markAsDone(${schedule.id})" 
                                class="px-3 py-1 bg-green-100 text-green-800 rounded-md hover:bg-green-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                            <a href="/maintenance/${schedule.id}/edit" 
                        class="px-3 py-1 bg-blue-100 text-blue-800 rounded-md hover:bg-blue-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                            <button onclick="deleteMaintenance(${schedule.id})" 
                                class="px-3 py-1 bg-red-100 text-red-800 rounded-md hover:bg-red-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Add calendar navigation functions
        function changeMonth(delta) {
            currentDate.setMonth(currentDate.getMonth() + delta);
            generateCalendar();
        }

        function changeYear(delta) {
            currentDate.setFullYear(currentDate.getFullYear() + delta);
            generateCalendar();
        }

        let maintenanceToDelete = null;
        let maintenanceToComplete = null;

        function showDeleteModal(id) {
            maintenanceToDelete = id;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            maintenanceToDelete = null;
            document.getElementById('delete-modal').classList.add('hidden');
        }

        function showCompleteModal(id) {
            maintenanceToComplete = id;
            document.getElementById('complete-modal').classList.remove('hidden');
        }

        function closeCompleteModal() {
            maintenanceToComplete = null;
            document.getElementById('complete-modal').classList.add('hidden');
        }

        // Update the existing functions to use the modals
        function deleteMaintenance(id) {
            showDeleteModal(id);
        }

        function markAsDone(id) {
            showCompleteModal(id);
        }

        // Add event listeners for the confirmation buttons
        document.getElementById('confirm-delete-btn').addEventListener('click', function() {
            if (maintenanceToDelete) {
                fetch(`/maintenance/${maintenanceToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    closeDeleteModal();
                    loadMaintenanceTable();
                    generateCalendar();
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.location.reload();
                });
            }
        });

        document.getElementById('confirm-complete-btn').addEventListener('click', function() {
            if (maintenanceToComplete) {
                fetch(`/maintenance/${maintenanceToComplete}/complete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    closeCompleteModal();
                    loadMaintenanceTable();
                    generateCalendar();
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.location.reload();
                });
            }
        });

    </script>
</div>
@endsection
