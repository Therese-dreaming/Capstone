@extends('layouts.app')

@section('content')
<div class="flex-1 p-8 ml-72">
    <h2 class="text-2xl font-semibold mb-6">SCHEDULE MAINTENANCE</h2>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="confirmation-modal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Warning</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">This asset already has a scheduled maintenance. Do you want to overwrite it?</p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <form action="{{ route('maintenance.schedule.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="asset_id" value="{{ session('form_data.asset_id') }}">
                            <input type="hidden" name="maintenance_task" value="{{ session('form_data.maintenance_task') }}">
                            <input type="hidden" name="technician_id" value="{{ session('form_data.technician_id') }}">
                            <input type="hidden" name="scheduled_date" value="{{ session('form_data.scheduled_date') }}">
                            <input type="hidden" name="confirm_completion" value="1">
                            <input type="hidden" name="confirm_overwrite" value="1">
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                Yes, Overwrite
                            </button>
                            <a href="{{ route('maintenance.schedule') }}" class="ml-3 px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 inline-block">
                                Cancel
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="flex gap-6">
        <!-- Left Column - Form -->
        <div class="flex-1 bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('maintenance.schedule.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Asset Category</label>
                    <select name="category_id" id="category_select" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">Select a category...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Asset for Maintenance</label>
                    <div class="relative">
                        <input type="text" 
                               id="asset_search" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" 
                               placeholder="Search by serial number..."
                               autocomplete="off">
                        <input type="hidden" name="asset_id" id="asset_id">
                        <div id="search_results" class="absolute z-10 w-full mt-1 bg-white shadow-lg rounded-md hidden">
                        </div>
                    </div>
                    @error('asset_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assign Maintenance Task</label>
                    <input type="text" name="maintenance_task" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="Enter maintenance task">
                    @error('maintenance_task')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Schedule Date</label>
                    <input type="date" 
                           name="scheduled_date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500"
                           min="{{ date('Y-m-d') }}">
                    @error('scheduled_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assign Technician</label>
                    <select name="technician_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">Select a technician...</option>
                        @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                        @endforeach
                    </select>
                    @error('technician_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="confirm_completion" id="confirm_completion" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                    <label for="confirm_completion" class="ml-2 block text-sm text-gray-700">Confirm</label>
                    @error('confirm_completion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="w-full bg-red-800 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        Submit
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Column - Asset Details -->
        <div class="flex-1 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Asset Details</h3>
            <div id="asset_details" class="space-y-4">
                <div class="text-center text-gray-500">
                    Select an asset to view details
                </div>
            </div>
        </div>
    </div>
    <!-- Calendar Section -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Maintenance Calendar</h3>
            <div class="flex gap-4 items-center">
                <button onclick="changeYear(-1)" class="p-2 hover:bg-gray-100 rounded-full">≪</button>
                <button onclick="changeMonth(-1)" class="p-2 hover:bg-gray-100 rounded-full">＜</button>
                <span id="current-month-year" class="text-lg font-medium min-w-[200px] text-center"></span>
                <button onclick="changeMonth(1)" class="p-2 hover:bg-gray-100 rounded-full">＞</button>
                <button onclick="changeYear(1)" class="p-2 hover:bg-gray-100 rounded-full">≫</button>
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
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900" id="modal-date"></h3>
                        <div class="flex gap-2">
                            <input type="text" 
                                   id="schedule-search" 
                                   placeholder="Search by serial number..." 
                                   class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                            <button onclick="closeSchedulesModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
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

<!-- Update the showSchedules function in your script section -->
<script>
    let assets = [];
    
    document.getElementById('category_select').addEventListener('change', function() {
        const categoryId = this.value;
        const assetSearch = document.getElementById('asset_search');
        const searchResults = document.getElementById('search_results');
        const assetDetails = document.getElementById('asset_details');
        
        if (categoryId) {
            assetSearch.disabled = false;
            
            fetch(`/categories/${categoryId}/assets`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                assets = data;
                assetSearch.value = '';
                document.getElementById('asset_id').value = '';
                searchResults.innerHTML = '';
                assetDetails.innerHTML = '<div class="text-center text-gray-500">Search and select an asset</div>';
            })
            .catch(error => {
                console.error('Error:', error);
                assetSearch.value = 'Error loading assets';
                assetSearch.disabled = true;
            });
        } else {
            assetSearch.disabled = true;
            assetSearch.value = '';
            document.getElementById('asset_id').value = '';
            searchResults.innerHTML = '';
            assets = [];
        }
    });

    document.getElementById('asset_search').addEventListener('input', function() {
        const searchResults = document.getElementById('search_results');
        const query = this.value.toLowerCase();
        
        if (query.length > 0) {
            const filtered = assets.filter(asset => 
                asset.serial_number.toLowerCase().includes(query)
            );
            
            searchResults.innerHTML = filtered.map(asset => `
                <div class="p-2 hover:bg-gray-100 cursor-pointer" 
                     onclick="selectAsset('${asset.id}', '${asset.serial_number}')">
                    ${asset.serial_number}
                </div>
            `).join('');
            
            searchResults.classList.remove('hidden');
        } else {
            searchResults.classList.add('hidden');
        }
    });

    function selectAsset(id, serialNumber) {
        document.getElementById('asset_search').value = serialNumber;
        document.getElementById('asset_id').value = id;
        document.getElementById('search_results').classList.add('hidden');
        
        fetch(`/assets/${id}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(asset => {
            document.getElementById('asset_details').innerHTML = `
                <div class="space-y-4">
                    <div class="flex justify-center">
                        <img src="${asset.photo_url || '/images/no-image.png'}" 
                             alt="${asset.name}" 
                             class="w-64 h-64 object-cover rounded-lg shadow-md">
                    </div>
                    <div class="space-y-2">
                        <p class="text-lg font-semibold">${asset.name}</p>
                        <p class="text-gray-600">Serial Number: ${asset.serial_number || 'N/A'}</p>
                        <p class="text-gray-600">Status: ${asset.status || 'N/A'}</p>
                    </div>
                </div>
            `;
        });
    }

    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#asset_search')) {
            document.getElementById('search_results').classList.add('hidden');
        }
    });

    let currentDate = new Date();

    function changeMonth(delta) {
        currentDate.setMonth(currentDate.getMonth() + delta);
        generateCalendar();
    }

    function changeYear(delta) {
        currentDate.setFullYear(currentDate.getFullYear() + delta);
        generateCalendar();
    }

    function generateCalendar() {
        const calendar = document.getElementById('calendar');
        const monthYearDisplay = document.getElementById('current-month-year');
        const currentMonth = currentDate.getMonth();
        const currentYear = currentDate.getFullYear();
        
        // Update month/year display
        monthYearDisplay.textContent = new Date(currentYear, currentMonth).toLocaleDateString('en-US', {
            month: 'long',
            year: 'numeric'
        });

        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        
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
        
        // Empty cells before first day
        for (let i = 0; i < firstDay; i++) {
            calendar.innerHTML += '<div class="p-2 border rounded-md bg-gray-50"></div>';
        }
        
        // Calendar days
        for (let day = 1; day <= daysInMonth; day++) {
            const date = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            calendar.innerHTML += `
                <div class="p-2 border rounded-md hover:bg-gray-50 cursor-pointer text-center" 
                     onclick="showSchedules('${date}')">
                    ${day}
                </div>
            `;
        }
    }

    let currentSchedules = []; // Add this at the top with your other variables

    function showSchedules(date) {
        fetch(`/maintenance/schedules/${date}`)
            .then(response => response.json())
            .then(schedules => {
                currentSchedules = schedules; // Store schedules for searching
                const modal = document.getElementById('schedules-modal');
                const dateDisplay = document.getElementById('modal-date');
                const list = document.getElementById('schedules-list');
                const noSchedules = document.getElementById('no-schedules');
                
                dateDisplay.textContent = new Date(date).toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                
                renderSchedules(schedules);
                modal.classList.remove('hidden');
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
</script>

    <!-- Completed Maintenance Section -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Completed Maintenance History</h3>
            <input type="text" 
                   id="completed-search" 
                   placeholder="Search by serial number..." 
                   class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Completed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technician</th>
                    </tr>
                </thead>
                <tbody id="completed-maintenance-list" class="bg-white divide-y divide-gray-200">
                    <!-- Will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add this to your script section -->
    <script>
        // Add this function to load completed maintenance
        function loadCompletedMaintenance() {
            fetch('/maintenance/completed')
                .then(response => response.json())
                .then(completedMaintenance => {
                    renderCompletedMaintenance(completedMaintenance);
                });
        }

        function renderCompletedMaintenance(maintenance) {
            const list = document.getElementById('completed-maintenance-list');
            list.innerHTML = maintenance.map(item => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${new Date(item.completion_date).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.asset.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.asset.serial_number}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.task}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.technician.name}</td>
                </tr>
            `).join('');
        }

        // Add search functionality for completed maintenance
        document.getElementById('completed-search').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            fetch(`/maintenance/completed?search=${searchTerm}`)
                .then(response => response.json())
                .then(filteredMaintenance => {
                    renderCompletedMaintenance(filteredMaintenance);
                });
        });

        // Load completed maintenance when page loads
        document.addEventListener('DOMContentLoaded', function() {
            generateCalendar();
            loadCompletedMaintenance();
        });
    </script>
@endsection
