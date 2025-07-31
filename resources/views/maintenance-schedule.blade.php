@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8">
    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Notification div for JavaScript -->
    <div id="notification" class="mb-4 p-4 rounded hidden"></div>

    <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
        <h2 class="text-2xl font-bold mb-6">SCHEDULE LAB MAINTENANCE</h2>
        <div class="border-b-2 border-red-800 mb-6"></div>
        <form id="maintenanceForm" action="{{ route('maintenance.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Select Location</label>
                        <select name="location_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 bg-gray-50">
                            <option value="">Select a location...</option>
                            @foreach($locations as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('location_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Schedule Date</label>
                        <input type="date" name="scheduled_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 bg-gray-50" min="{{ date('Y-m-d') }}">
                        @error('scheduled_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Assign Technician</label>
                        <select name="technician_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 bg-gray-50">
                            <option value="">Select a technician...</option>
                            @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                            @endforeach
                        </select>
                        @error('technician_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Exclude Assets (Optional)</label>
                        <div class="border border-gray-300 rounded-md bg-gray-50 p-4">
                            <div class="flex space-x-2 mb-3">
                                <select id="assetSelect" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 bg-white">
                                    <option value="">Select an asset...</option>
                                </select>
                                <button type="button" onclick="addExcludedAsset()" class="px-4 py-2 bg-red-800 text-white rounded-md hover:bg-red-700 
                                    focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">
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
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Select Maintenance Tasks</label>
                    <div class="border border-gray-300 rounded-md bg-gray-50 p-4">
                        <!-- Add New Task Input -->
                        <div class="mb-4 flex space-x-2">
                            <input type="text" id="newTask" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 bg-white" placeholder="Enter new maintenance task">
                            <button type="button" onclick="return addNewTask(event)" class="px-4 py-2 bg-red-800 text-white rounded-md hover:bg-red-700 
                                focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">
                                Add Task
                            </button>
                        </div>
                        <div class="task-container space-y-3 max-h-[300px] overflow-y-auto pr-2">
                            @foreach($maintenanceTasks as $task)
                            <div class="flex items-center bg-white p-3 rounded-md shadow-sm hover:shadow-md transition-shadow cursor-pointer" onclick="toggleCheckbox(this)">
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

            <div class="pt-4 border-t border-gray-200">
                <button type="button" onclick="showConfirmationModal()" class="w-full bg-red-800 text-white py-3 px-4 rounded-md hover:bg-red-700 
                    focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 
                    font-medium transition-colors duration-200">
                    Schedule Maintenance
                </button>
            </div>
        </form>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-3 md:p-5 border w-full max-w-[95%] md:max-w-[500px] shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-xl font-semibold text-gray-900 mb-4 text-center">Confirm Maintenance Schedule</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 text-center mb-4">Please review the maintenance schedule details below:</p>
                    <div id="scheduleDetails" class="bg-gray-50 p-4 rounded-md text-sm space-y-3">
                        <!-- Details will be filled by JavaScript -->
                    </div>
                </div>
                <div class="flex justify-center space-x-3 mt-6">
                    <button id="confirmButton" class="px-6 py-2 bg-red-800 text-white text-base font-medium rounded-md 
                        hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 
                        transition-colors duration-200">
                        Confirm Schedule
                    </button>
                    <button onclick="hideConfirmationModal()" class="px-6 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md 
                        hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 
                        transition-colors duration-200">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

<!-- Validation Modal -->
<div id="validationModal"
     class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    
    <div class="p-5 border max-w-sm w-full sm:w-auto shadow-lg rounded-md bg-white">
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
                        class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>



    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-[400px] shadow-lg rounded-md bg-white">
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
                    <button onclick="hideSuccessModal()" class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelector('select[name="location_id"]').addEventListener('change', function() {
            const locationId = this.value;
            const assetSelect = document.getElementById('assetSelect');

            if (!locationId) {
                assetSelect.innerHTML = '<option value="">Select an asset...</option>';
                return;
            }

            // Show loading state
            assetSelect.innerHTML = '<option value="">Loading assets...</option>';

            // Fetch assets for the selected location
            fetch(`{{ url('maintenance/get-location-assets') }}/${locationId}`, {
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
                    assetSelect.innerHTML = '<option value="">No assets available</option>';
                    return;
                }

                assetSelect.innerHTML = '<option value="">Select an asset...</option>' + 
                    assets.map(asset => `
                        <option value="${asset.id}" data-name="${asset.name}" data-serial="${asset.serial_number}">
                            ${asset.name} (SN: ${asset.serial_number})
                        </option>
                    `).join('');
            })
            .catch(error => {
                console.error('Error:', error);
                assetSelect.innerHTML = '<option value="">Error loading assets</option>';
            });
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
            
            const location = form.location_id.options[form.location_id.selectedIndex]?.text || 'Not selected';
            const selectedTasks = Array.from(form.querySelectorAll('input[name="maintenance_tasks[]"]:checked'))
                .map(checkbox => checkbox.value);
            const date = form.scheduled_date.value ? new Date(form.scheduled_date.value).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }) : 'Not selected';
            const technician = form.technician_id.options[form.technician_id.selectedIndex]?.text || 'Not selected';

            if (!form.location_id.value || selectedTasks.length === 0 || !form.scheduled_date.value || !form.technician_id.value) {
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
                        <p class="text-gray-600 font-medium mb-1">Scheduled Date:</p>
                        <p class="text-gray-800">${date}</p>
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

    </script>
</div>
@endsection