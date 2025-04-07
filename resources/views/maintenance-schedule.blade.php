@extends('layouts.app')

@section('content')
<div class="flex-1 p-8 ml-72">
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

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">SCHEDULE LAB MAINTENANCE</h2>
        <div class="border-b-2 border-red-800 mb-6"></div>
        <form id="maintenanceForm" action="{{ route('maintenance.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Select Computer Laboratory</label>
                        <select name="lab_number" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 bg-gray-50">
                            <option value="">Select a laboratory...</option>
                            @foreach($labs as $number => $name)
                            <option value="{{ $number }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('lab_number')
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
                </div>

                <!-- Right Column -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Select Maintenance Tasks</label>
                    <div class="border border-gray-300 rounded-md bg-gray-50 p-4">
                        <!-- Add New Task Input -->
                        <div class="mb-4 flex space-x-2">
                            <input type="text" id="newTask" 
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 bg-white" 
                                placeholder="Enter new maintenance task">
                            <button type="button" onclick="return addNewTask(event)" 
                                class="px-4 py-2 bg-red-800 text-white rounded-md hover:bg-red-700 
                                focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">
                                Add Task
                            </button>
                        </div>
                        <div class="task-container space-y-3 max-h-[300px] overflow-y-auto pr-2">
                            @foreach($maintenanceTasks as $task)
                            <div class="flex items-center bg-white p-3 rounded-md shadow-sm hover:shadow-md transition-shadow cursor-pointer" 
                                onclick="toggleCheckbox(this)">
                                <input type="checkbox" name="maintenance_tasks[]" value="{{ $task }}" 
                                    class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
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
        <div class="relative top-20 mx-auto p-5 border w-[500px] shadow-lg rounded-md bg-white">
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

    <script>
        function showConfirmationModal() {
            const form = document.getElementById('maintenanceForm');
            // Fix the optional chaining syntax
            const lab = form.lab_number.options[form.lab_number.selectedIndex]?.text || 'Not selected';
            const selectedTasks = Array.from(form.querySelectorAll('input[name="maintenance_tasks[]"]:checked'))
                .map(checkbox => checkbox.value);
            const date = form.scheduled_date.value ? new Date(form.scheduled_date.value).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }) : 'Not selected';
            // Fix the optional chaining syntax
            const technician = form.technician_id.options[form.technician_id.selectedIndex]?.text || 'Not selected';

            if (!form.lab_number.value || selectedTasks.length === 0 || !form.scheduled_date.value || !form.technician_id.value) {
                alert('Please fill in all required fields');
                return;
            }

            document.getElementById('scheduleDetails').innerHTML = `
                <div class="space-y-4">
                    <div>
                        <p class="text-gray-600 font-medium mb-1">Laboratory:</p>
                        <p class="text-gray-800">${lab}</p>
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
                </div>
            `;

            document.getElementById('confirmationModal').classList.remove('hidden');
        }

        function hideConfirmationModal() {
            document.getElementById('confirmationModal').classList.add('hidden');
        }

        document.getElementById('confirmButton').addEventListener('click', function() {
            const form = document.getElementById('maintenanceForm');
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
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ task: task })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add new task to the list
                    const tasksContainer = document.querySelector('.task-container'); // Ensure this is the correct task list
                    const newTaskElement = document.createElement('div');
                    newTaskElement.className = 'flex items-center bg-white p-3 rounded-md shadow-sm hover:shadow-md transition-shadow cursor-pointer';
                    newTaskElement.onclick = function() { toggleCheckbox(this); };
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
            notification.classList.remove('hidden', 'bg-green-100', 'border-green-400', 'text-green-700', 
                'bg-red-100', 'border-red-400', 'text-red-700');
            
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
    </script>
</div>
@endsection
