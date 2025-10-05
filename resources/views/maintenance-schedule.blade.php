@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Page Header with Background Design -->
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                        <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Schedule Lab Maintenance</h1>
                        <p class="text-red-100 text-sm md:text-lg">Plan and schedule maintenance activities for laboratory equipment</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-4 md:mb-6 p-3 md:p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm md:text-base">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 md:mb-6 p-3 md:p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm md:text-base font-medium">Please correct the following errors:</span>
            </div>
            <ul class="ml-4 list-disc list-inside text-sm md:text-base space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Notification div for JavaScript -->
    <div id="notification" class="mb-4 md:mb-6 p-3 md:p-4 rounded-xl hidden"></div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
        <form id="maintenanceForm" action="{{ route('maintenance.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Location</label>
                        <div class="relative">
                            <input type="text" id="maintenanceLocationSearch" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200" 
                                   placeholder="Type to search location..."
                                   autocomplete="off">
                            <input type="hidden" name="location_id" id="maintenanceLocationId" required>
                            <!-- Autocomplete dropdown -->
                            <div id="maintenanceLocationAutocomplete" class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden mt-1">
                                <!-- Suggestions will be populated here -->
                            </div>
                        </div>
                        @error('location_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Schedule Date</label>
                        <input type="date" name="scheduled_date" id="scheduledDate"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200" 
                               min="{{ date('Y-m-d') }}">
                        @error('scheduled_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Target Date (Deadline)</label>
                        <input type="date" name="target_date" id="targetDate"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200" 
                               min="{{ date('Y-m-d') }}">
                        @error('target_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign Technician</label>
                        <select name="technician_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                            <option value="">Select a technician...</option>
                            @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}"
                                    data-ongoing="{{ $technicianOngoingCounts[$technician->id] ?? 0 }}"
                                    data-repairs="{{ $technicianRepairCounts[$technician->id] ?? 0 }}"
                                    data-maintenance="{{ $technicianMaintenanceCounts[$technician->id] ?? 0 }}">
                                {{ $technician->name }} ({{ $technicianRepairCounts[$technician->id] ?? 0 }} repairs, {{ $technicianMaintenanceCounts[$technician->id] ?? 0 }} maintenance)
                            </option>
                            @endforeach
                        </select>
                        <p id="technicianOngoingHint" class="mt-1 text-sm text-gray-500 hidden">Repairs: <span id="technicianRepairsCount">0</span> • Maintenance: <span id="technicianMaintenanceCount">0</span> • Total: <span id="technicianOngoingCount">0</span></p>
                        @error('technician_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Exclude Assets (Optional)</label>
                        <div class="border border-gray-300 rounded-lg bg-gray-50 p-4">
                            <div class="flex space-x-2 mb-3">
                                <div class="relative flex-1">
                                    <input type="text" id="assetSearch" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200" 
                                           placeholder="Type to search asset by serial number or name..."
                                           autocomplete="off">
                                    <!-- Autocomplete dropdown -->
                                    <div id="assetAutocomplete" class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden mt-1">
                                        <!-- Asset suggestions will be populated here -->
                                    </div>
                                </div>
                                <button type="button" onclick="addExcludedAsset()" 
                                        class="px-4 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-md">
                                    Add
                                </button>
                            </div>
                            <div id="selectedAssetsList" class="max-h-[200px] overflow-y-auto space-y-2">
                                <!-- Selected assets will be shown here -->
                            </div>
                            <input type="hidden" name="excluded_assets" id="excludedAssetsInput">
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Maintenance Tasks</label>
                    <div class="border border-gray-300 rounded-lg bg-gray-50 p-4">
                        <!-- Add New Task Input -->
                        <div class="mb-4 flex space-x-2">
                            <input type="text" id="newTask" 
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200" 
                                   placeholder="Enter new maintenance task">
                            <button type="button" onclick="return addNewTask(event)" 
                                    class="px-4 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-md">
                                Add Task
                            </button>
                        </div>
                        
                        <!-- Select All Button -->
                        <div class="mb-4 flex justify-end">
                            <button type="button" id="selectAllBtn" onclick="toggleSelectAll()" 
                                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-md">
                                Select All
                            </button>
                        </div>
                        
                        <div class="task-container space-y-3 max-h-[300px] overflow-y-auto pr-2">
                            @foreach($maintenanceTasks as $task)
                            <div class="flex items-center bg-white p-3 rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer" onclick="toggleCheckbox(this)">
                                <input type="checkbox" name="maintenance_tasks[]" value="{{ $task }}" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                <label class="ml-3 text-sm text-gray-700 font-medium flex-1 cursor-pointer">{{ $task }}</label>
                            </div>
                            @endforeach
                        </div>
                        @error('maintenance_tasks')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Button Section -->
            <div class="pt-6 border-t border-gray-200">
                <button type="button" onclick="showConfirmationModal()" 
                        class="w-full bg-red-600 text-white py-4 px-6 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 font-medium transition-colors duration-200 shadow-md flex items-center justify-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Schedule Maintenance
                </button>
            </div>
        </form>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-3 md:p-5 border w-full max-w-[95%] md:max-w-[500px] shadow-lg rounded-xl bg-white">
            <div class="mt-3">
                <h3 class="text-xl font-semibold text-gray-900 mb-4 text-center">Confirm Maintenance Schedule</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 text-center mb-4">Please review the maintenance schedule details below:</p>
                    <div id="scheduleDetails" class="bg-gray-50 p-4 rounded-lg text-sm space-y-3">
                        <!-- Details will be filled by JavaScript -->
                    </div>
                </div>
                <div class="flex justify-center space-x-3 mt-6">
                    <button id="confirmButton" 
                            class="px-6 py-3 bg-red-600 text-white text-base font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-md">
                        Confirm Schedule
                    </button>
                    <button onclick="hideConfirmationModal()" 
                            class="px-6 py-3 bg-gray-100 text-gray-700 text-base font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Validation Modal -->
    <div id="validationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="p-5 border max-w-sm w-full sm:w-auto shadow-lg rounded-xl bg-white">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Validation Error</h3>
                <div class="mt-2 px-4 py-2">
                    <p id="validationMessage" class="text-sm text-gray-500"></p>
                </div>
                <div class="px-4 pt-2 pb-3">
                    <button onclick="hideValidationModal()"
                            class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>



    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-[400px] shadow-lg rounded-xl bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Success</h3>
                <div class="mt-2 px-7 py-3">
                    <p id="successMessage" class="text-sm text-gray-500"></p>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="hideSuccessModal()" 
                            class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-lg w-full shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize location autocomplete
        function initializeMaintenanceLocationAutocomplete() {
            const locationInput = document.getElementById('maintenanceLocationSearch');
            const locationAutocomplete = document.getElementById('maintenanceLocationAutocomplete');
            const locationIdInput = document.getElementById('maintenanceLocationId');

            if (!locationInput || !locationAutocomplete || !locationIdInput) {
                return;
            }

            // All locations data from server
            const allLocations = @json($locations->map(function($name, $id) {
                return ['id' => $id, 'name' => $name];
            })->values());

            let searchTimeout;

            locationInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                
                clearTimeout(searchTimeout);
                
                if (query.length === 0) {
                    hideMaintenanceLocationAutocomplete();
                    locationIdInput.value = '';
                    triggerLocationChange('');
                    return;
                }

                searchTimeout = setTimeout(() => {
                    filterMaintenanceLocations(query, allLocations);
                }, 200);
            });

            // Handle keyboard navigation
            locationInput.addEventListener('keydown', function(e) {
                const suggestions = locationAutocomplete.querySelectorAll('.maintenance-location-suggestion-item');
                const activeSuggestion = locationAutocomplete.querySelector('.maintenance-location-suggestion-item.active');
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (!activeSuggestion) {
                        suggestions[0]?.classList.add('active');
                    } else {
                        const nextSuggestion = activeSuggestion.nextElementSibling;
                        if (nextSuggestion && nextSuggestion.classList.contains('maintenance-location-suggestion-item')) {
                            activeSuggestion.classList.remove('active');
                            nextSuggestion.classList.add('active');
                            nextSuggestion.scrollIntoView({ block: 'nearest' });
                        }
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (activeSuggestion) {
                        const prevSuggestion = activeSuggestion.previousElementSibling;
                        if (prevSuggestion && prevSuggestion.classList.contains('maintenance-location-suggestion-item')) {
                            activeSuggestion.classList.remove('active');
                            prevSuggestion.classList.add('active');
                            prevSuggestion.scrollIntoView({ block: 'nearest' });
                        } else {
                            activeSuggestion.classList.remove('active');
                        }
                    }
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (activeSuggestion) {
                        activeSuggestion.click();
                    }
                } else if (e.key === 'Escape') {
                    hideMaintenanceLocationAutocomplete();
                }
            });

            // Hide autocomplete when clicking outside
            document.addEventListener('click', function(e) {
                if (!locationInput.contains(e.target) && !locationAutocomplete.contains(e.target)) {
                    hideMaintenanceLocationAutocomplete();
                }
            });
        }

        function filterMaintenanceLocations(query, allLocations) {
            const locationAutocomplete = document.getElementById('maintenanceLocationAutocomplete');
            if (!locationAutocomplete) return;

            const matches = allLocations.filter(loc => 
                loc.name.toLowerCase().includes(query)
            );

            displayMaintenanceLocationSuggestions(matches);
        }

        function displayMaintenanceLocationSuggestions(locations) {
            const locationAutocomplete = document.getElementById('maintenanceLocationAutocomplete');
            if (!locationAutocomplete) return;

            locationAutocomplete.innerHTML = '';
            
            if (locations.length === 0) {
                locationAutocomplete.innerHTML = `
                    <div class="px-3 py-2 text-sm text-gray-500">
                        No locations found. Try a different search term.
                    </div>
                `;
            } else {
                locations.slice(0, 50).forEach(location => {
                    const suggestionItem = document.createElement('div');
                    suggestionItem.className = 'maintenance-location-suggestion-item px-3 py-2 text-sm cursor-pointer hover:bg-gray-100 border-b border-gray-100 last:border-b-0';
                    suggestionItem.textContent = location.name;
                    suggestionItem.dataset.locationId = location.id;
                    suggestionItem.addEventListener('click', () => selectMaintenanceLocation(location));
                    locationAutocomplete.appendChild(suggestionItem);
                });

                if (locations.length > 50) {
                    const moreItem = document.createElement('div');
                    moreItem.className = 'px-3 py-2 text-xs text-gray-400 italic';
                    moreItem.textContent = `+ ${locations.length - 50} more results. Refine your search.`;
                    locationAutocomplete.appendChild(moreItem);
                }
            }
            
            locationAutocomplete.classList.remove('hidden');
        }

        function selectMaintenanceLocation(location) {
            const locationInput = document.getElementById('maintenanceLocationSearch');
            const locationIdInput = document.getElementById('maintenanceLocationId');
            
            if (!locationInput || !locationIdInput) return;

            locationInput.value = location.name;
            locationIdInput.value = location.id;
            
            hideMaintenanceLocationAutocomplete();
            
            // Trigger location change to load assets
            triggerLocationChange(location.id);
        }

        function hideMaintenanceLocationAutocomplete() {
            const locationAutocomplete = document.getElementById('maintenanceLocationAutocomplete');
            if (!locationAutocomplete) return;

            locationAutocomplete.classList.add('hidden');
            locationAutocomplete.querySelectorAll('.maintenance-location-suggestion-item').forEach(item => {
                item.classList.remove('active');
            });
        }

        function triggerLocationChange(locationId) {
            // Clear asset search when location changes
            const assetSearch = document.getElementById('assetSearch');
            if (assetSearch) {
                assetSearch.value = '';
                hideAssetAutocomplete();
            }
            
            // Store current location ID for asset search
            window.currentLocationId = locationId;
        }

        // Initialize asset autocomplete
        function initializeAssetAutocomplete() {
            const assetInput = document.getElementById('assetSearch');
            const assetAutocomplete = document.getElementById('assetAutocomplete');

            if (!assetInput || !assetAutocomplete) {
                return;
            }

            let searchTimeout;

            assetInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    hideAssetAutocomplete();
                    return;
                }

                if (!window.currentLocationId) {
                    showAssetMessage('Please select a location first', 'warning');
                    return;
                }

                searchTimeout = setTimeout(() => {
                    searchAssets(query);
                }, 300);
            });

            // Handle keyboard navigation
            assetInput.addEventListener('keydown', function(e) {
                const suggestions = assetAutocomplete.querySelectorAll('.asset-suggestion-item');
                const activeSuggestion = assetAutocomplete.querySelector('.asset-suggestion-item.active');
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (!activeSuggestion) {
                        suggestions[0]?.classList.add('active');
                    } else {
                        const nextSuggestion = activeSuggestion.nextElementSibling;
                        if (nextSuggestion && nextSuggestion.classList.contains('asset-suggestion-item')) {
                            activeSuggestion.classList.remove('active');
                            nextSuggestion.classList.add('active');
                            nextSuggestion.scrollIntoView({ block: 'nearest' });
                        }
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (activeSuggestion) {
                        const prevSuggestion = activeSuggestion.previousElementSibling;
                        if (prevSuggestion && prevSuggestion.classList.contains('asset-suggestion-item')) {
                            activeSuggestion.classList.remove('active');
                            prevSuggestion.classList.add('active');
                            prevSuggestion.scrollIntoView({ block: 'nearest' });
                        } else {
                            activeSuggestion.classList.remove('active');
                        }
                    }
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (activeSuggestion) {
                        activeSuggestion.click();
                    }
                } else if (e.key === 'Escape') {
                    hideAssetAutocomplete();
                }
            });

            // Hide autocomplete when clicking outside
            document.addEventListener('click', function(e) {
                if (!assetInput.contains(e.target) && !assetAutocomplete.contains(e.target)) {
                    hideAssetAutocomplete();
                }
            });
        }

        function searchAssets(query) {
            const assetAutocomplete = document.getElementById('assetAutocomplete');
            
            // Show loading state
            assetAutocomplete.innerHTML = '<div class="p-3 text-gray-500 text-sm">Searching assets...</div>';
            assetAutocomplete.classList.remove('hidden');

            // Fetch assets for the selected location
            fetch(`{{ url('maintenance/get-location-assets') }}/${window.currentLocationId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(assets => {
                if (!assets || assets.length === 0) {
                    assetAutocomplete.innerHTML = '<div class="p-3 text-gray-500 text-sm">No assets available in this location</div>';
                    return;
                }

                // Filter assets based on search query
                const filteredAssets = assets.filter(asset => 
                    asset.name.toLowerCase().includes(query) || 
                    asset.serial_number.toLowerCase().includes(query)
                );

                if (filteredAssets.length === 0) {
                    assetAutocomplete.innerHTML = '<div class="p-3 text-gray-500 text-sm">No assets found matching your search</div>';
                    return;
                }

                // Display filtered assets
                assetAutocomplete.innerHTML = filteredAssets.map(asset => `
                    <div class="asset-suggestion-item p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0"
                         onclick="selectAsset({id: ${asset.id}, name: '${asset.name}', serial_number: '${asset.serial_number}', status: '${asset.status}'})">
                        <div class="font-medium text-gray-900">${asset.name}</div>
                        <div class="text-sm text-gray-600">SN: ${asset.serial_number} • Status: ${asset.status}</div>
                    </div>
                `).join('');
            })
            .catch(error => {
                console.error('Error:', error);
                assetAutocomplete.innerHTML = '<div class="p-3 text-red-500 text-sm">Error loading assets</div>';
            });
        }

        function selectAsset(asset) {
            const assetInput = document.getElementById('assetSearch');
            
            if (!assetInput) return;

            assetInput.value = `${asset.name} (SN: ${asset.serial_number})`;
            assetInput.dataset.selectedAsset = JSON.stringify(asset);
            
            hideAssetAutocomplete();
        }

        function hideAssetAutocomplete() {
            const assetAutocomplete = document.getElementById('assetAutocomplete');
            if (!assetAutocomplete) return;

            assetAutocomplete.classList.add('hidden');
            assetAutocomplete.querySelectorAll('.asset-suggestion-item').forEach(item => {
                item.classList.remove('active');
            });
        }

        function showAssetMessage(message, type = 'info') {
            // You can implement a notification system here if needed
            console.log(`${type}: ${message}`);
        }

        // Add excluded asset function
        function addExcludedAsset() {
            const assetInput = document.getElementById('assetSearch');
            const selectedAssetsList = document.getElementById('selectedAssetsList');
            const excludedAssetsInput = document.getElementById('excludedAssetsInput');
            
            if (!assetInput.dataset.selectedAsset) {
                showAssetMessage('Please select an asset from the dropdown', 'warning');
                return;
            }
            
            const asset = JSON.parse(assetInput.dataset.selectedAsset);
            
            // Check if asset is already excluded
            const currentExcluded = excludedAssetsInput.value ? JSON.parse(excludedAssetsInput.value) : [];
            if (currentExcluded.some(excluded => excluded.id === asset.id)) {
                showAssetMessage('This asset is already excluded', 'warning');
                return;
            }
            
            // Add asset to excluded list
            currentExcluded.push(asset);
            excludedAssetsInput.value = JSON.stringify(currentExcluded);
            
            // Add visual representation
            const assetElement = document.createElement('div');
            assetElement.className = 'flex items-center justify-between bg-white p-3 rounded-lg border border-gray-200';
            assetElement.innerHTML = `
                <div>
                    <div class="font-medium text-gray-900">${asset.name}</div>
                    <div class="text-sm text-gray-600">SN: ${asset.serial_number} • Status: ${asset.status}</div>
                </div>
                <button type="button" onclick="removeExcludedAsset(${asset.id})" 
                        class="text-red-600 hover:text-red-800 p-1 rounded-full hover:bg-red-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
            
            selectedAssetsList.appendChild(assetElement);
            
            // Clear the input
            assetInput.value = '';
            delete assetInput.dataset.selectedAsset;
        }
        
        function removeExcludedAsset(assetId) {
            const selectedAssetsList = document.getElementById('selectedAssetsList');
            const excludedAssetsInput = document.getElementById('excludedAssetsInput');
            
            // Remove from data
            const currentExcluded = excludedAssetsInput.value ? JSON.parse(excludedAssetsInput.value) : [];
            const updatedExcluded = currentExcluded.filter(asset => asset.id !== assetId);
            excludedAssetsInput.value = JSON.stringify(updatedExcluded);
            
            // Remove visual element
            const assetElements = selectedAssetsList.children;
            for (let element of assetElements) {
                const removeButton = element.querySelector('button[onclick*="' + assetId + '"]');
                if (removeButton) {
                    element.remove();
                    break;
                }
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeMaintenanceLocationAutocomplete();
            initializeAssetAutocomplete();
        });

        document.querySelector('select[name="technician_id"]').addEventListener('change', function() {
            const ongoingCount = this.options[this.selectedIndex].dataset.ongoing;
            const repairsCount = this.options[this.selectedIndex].dataset.repairs;
            const maintenanceCount = this.options[this.selectedIndex].dataset.maintenance;

            document.getElementById('technicianOngoingCount').textContent = ongoingCount;
            document.getElementById('technicianOngoingHint').classList.remove('hidden');
            document.getElementById('technicianRepairsCount').textContent = repairsCount;
            document.getElementById('technicianMaintenanceCount').textContent = maintenanceCount;
        });

        const sched = document.getElementById('scheduledDate');
        const target = document.getElementById('targetDate');
        function syncTargetMin() {
            if (sched && target) {
                target.min = sched.value || '{{ date('Y-m-d') }}';
                if (target.value && target.value < target.min) {
                    target.value = target.min;
                }
            }
        }
        sched.addEventListener('change', syncTargetMin);
        window.addEventListener('DOMContentLoaded', syncTargetMin);
        
        // Initialize Select All button state on page load
        window.addEventListener('DOMContentLoaded', function() {
            updateSelectAllButton();
        });

         function addExcludedAsset() {
            const select = document.getElementById('assetSelect');
            const selectedOption = select.options[select.selectedIndex];
            
            if (!select.value) {
                showNotification('Please select an asset first', 'error');
                return;
            }

            const selectedAssetsList = document.getElementById('selectedAssetsList');
            const existingAsset = selectedAssetsList.querySelector(`[data-asset-id="${select.value}"]`);

            if (existingAsset) {
                showNotification('This asset is already excluded', 'error');
                return;
            }

            const assetElement = document.createElement('div');
            assetElement.className = 'flex items-center justify-between bg-white p-2 rounded-md';
            assetElement.dataset.assetId = select.value;
            assetElement.innerHTML = `
                <span class="text-sm text-gray-700">${selectedOption.text}</span>
                <button type="button" onclick="removeExcludedAsset(this)" 
                    class="text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;

            selectedAssetsList.appendChild(assetElement);
            updateExcludedAssetsInput();
            select.value = ''; // Reset select after adding
        }

        function removeExcludedAsset(button) {
            button.closest('div').remove();
            updateExcludedAssetsInput();
        }

        function updateExcludedAssetsInput() {
            const selectedAssets = Array.from(document.getElementById('selectedAssetsList').children)
                .map(div => div.dataset.assetId);
            document.getElementById('excludedAssetsInput').value = JSON.stringify(selectedAssets);
        }

        function showConfirmationModal() {
            const form = document.getElementById('maintenanceForm');
            // Update this part to get excluded assets from the selectedAssetsList
            const excludedAssets = Array.from(document.getElementById('selectedAssetsList').children)
                .map(div => div.querySelector('span').textContent.trim());
            
            // Get location from the autocomplete input (not a select element)
            const location = document.getElementById('maintenanceLocationSearch').value || 'Not selected';
            const selectedTasks = Array.from(form.querySelectorAll('input[name="maintenance_tasks[]"]:checked'))
                .map(checkbox => checkbox.value);
            const date = form.scheduled_date.value ? new Date(form.scheduled_date.value).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }) : 'Not selected';
            const targetDate = form.target_date.value ? new Date(form.target_date.value).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }) : 'Not selected';
            const technician = form.technician_id.options[form.technician_id.selectedIndex]?.text || 'Not selected';

            if (!form.location_id.value || selectedTasks.length === 0 || !form.scheduled_date.value || !form.technician_id.value || !form.target_date.value) {
                showValidationModal('Please fill in all required fields');
                return;
            }

            document.getElementById('scheduleDetails').innerHTML = `
                <div class="space-y-4">
                    <div>
                        <p class="text-gray-600 font-medium mb-1">Location:</p>
                        <p class="text-gray-800">${location}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium mb-1">Scheduled Date (Start):</p>
                        <p class="text-gray-800">${date}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium mb-1">Target Date (Deadline):</p>
                        <p class="text-gray-800">${targetDate}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium mb-1">Assigned Technician:</p>
                        <p class="text-gray-800">${technician}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium mb-2">Maintenance Tasks:</p>
                        <ul class="list-disc ml-4 text-gray-800 space-y-1">
                            ${selectedTasks.map(task => `<li>${task}</li>`).join('')}
                        </ul>
                    </div>
                    ${excludedAssets.length > 0 ? `
                    <div>
                        <p class="text-gray-600 font-medium mb-2">Excluded Assets:</p>
                        <ul class="list-disc ml-4 text-gray-800 space-y-1">
                        ${excludedAssets.map(asset => `<li>${asset}</li>`).join('')}
                        </ul>
                    </div>
                    ` : ''}          
                </div>
            `;

            document.getElementById('confirmationModal').classList.remove('hidden');
        }

        function hideConfirmationModal() {
            document.getElementById('confirmationModal').classList.add('hidden');
        }

        document.getElementById('confirmButton').addEventListener('click', function() {
            const form = document.getElementById('maintenanceForm');
            
            // Get excluded assets from the selectedAssetsList
            const excludedAssets = Array.from(document.getElementById('selectedAssetsList').children)
                .map(div => div.dataset.assetId);
            
            // Update the hidden input value
            document.getElementById('excludedAssetsInput').value = JSON.stringify(excludedAssets);
            
            form.submit();
            document.getElementById('confirmationModal').classList.add('hidden');
            showNotification('Processing your request...', 'success');
        });

        function toggleCheckbox(container) {
            const checkbox = container.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            updateSelectAllButton();
        }

        function addNewTask(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            const newTaskInput = document.getElementById('newTask');
            const task = newTaskInput.value.trim();

            if (!task) {
                showNotification('Please enter a task', 'error');
                return false;
            }

            // Send AJAX request to add new task
            fetch('{{ route("maintenance.addNewTask") }}', {
                    method: 'POST'
                    , headers: {
                        'Content-Type': 'application/json'
                        , 'Accept': 'application/json'
                        , 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                    , body: JSON.stringify({
                        task: task
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Add new task to the list
                        const tasksContainer = document.querySelector('.task-container'); // Ensure this is the correct task list
                        const newTaskElement = document.createElement('div');
                        newTaskElement.className = 'flex items-center bg-white p-3 rounded-md shadow-sm hover:shadow-md transition-shadow cursor-pointer';
                        newTaskElement.onclick = function() {
                            toggleCheckbox(this);
                        };
                        newTaskElement.innerHTML = `
                        <input type="checkbox" name="maintenance_tasks[]" value="${data.task}" 
                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                        <label class="ml-3 text-sm text-gray-700 font-medium flex-1 cursor-pointer">${data.task}</label>
                    `;
                        tasksContainer.insertBefore(newTaskElement, tasksContainer.firstChild);
                        newTaskInput.value = '';
                        updateSelectAllButton(); // Update button state after adding new task
                        showNotification(data.message, 'success');
                    } else {
                        showNotification(data.message || 'Failed to add task', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to add task. Please try again.', 'error');
                });

            return false;
        }

        // Add new notification function
        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.classList.remove('hidden', 'bg-green-100', 'border-green-400', 'text-green-700'
                , 'bg-red-100', 'border-red-400', 'text-red-700');

            if (type === 'success') {
                notification.classList.add('bg-green-100', 'border', 'border-green-400', 'text-green-700');
            } else {
                notification.classList.add('bg-red-100', 'border', 'border-red-400', 'text-red-700');
            }

            // Show notification
            notification.classList.remove('hidden');

            // Hide notification after 3 seconds
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 3000);
        }

        // Validation Modal Functions
        function showValidationModal(message) {
            document.getElementById('validationMessage').textContent = message;
            document.getElementById('validationModal').classList.remove('hidden');
        }

        function hideValidationModal() {
            document.getElementById('validationModal').classList.add('hidden');
        }

        // Success Modal Functions  
        function showSuccessModal(message) {
            document.getElementById('successMessage').textContent = message;
            document.getElementById('successModal').classList.remove('hidden');
        }

        function hideSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
        }

        // Select All/Deselect All functionality
        function toggleSelectAll() {
            const selectAllBtn = document.getElementById('selectAllBtn');
            const checkboxes = document.querySelectorAll('.task-container input[type="checkbox"][name="maintenance_tasks[]"]');
            const checkedBoxes = document.querySelectorAll('.task-container input[type="checkbox"][name="maintenance_tasks[]"]:checked');
            
            // If all checkboxes are checked, deselect all; otherwise, select all
            const shouldSelectAll = checkedBoxes.length !== checkboxes.length;
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = shouldSelectAll;
            });
            
            // Update button text based on current state
            selectAllBtn.textContent = shouldSelectAll ? 'Deselect All' : 'Select All';
        }

        // Update Select All button text when individual checkboxes are clicked
        function updateSelectAllButton() {
            const selectAllBtn = document.getElementById('selectAllBtn');
            const checkboxes = document.querySelectorAll('.task-container input[type="checkbox"][name="maintenance_tasks[]"]');
            const checkedBoxes = document.querySelectorAll('.task-container input[type="checkbox"][name="maintenance_tasks[]"]:checked');
            
            if (checkedBoxes.length === 0) {
                selectAllBtn.textContent = 'Select All';
            } else if (checkedBoxes.length === checkboxes.length) {
                selectAllBtn.textContent = 'Deselect All';
            } else {
                selectAllBtn.textContent = 'Select All';
            }
        }

    </script>
</div>
@endsection