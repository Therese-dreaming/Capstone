@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Page Header with Background Design -->
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center">
                <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                    <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">
                        Edit Maintenance Schedule
                        @if(isset($labNumber))
                            - Lab {{ $labNumber }}
                        @elseif(isset($locationId))
                            - Location {{ $locationId }}
                        @endif
                    </h1>
                    <p class="text-red-100 text-sm md:text-lg">
                        {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-xl shadow-md p-6 md:p-8">
        <!-- Success and Error Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center" role="alert">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-medium">Success!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-center" role="alert">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <div>
                    <p class="font-medium">Error!</p>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700" role="alert">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <p class="font-medium">Please fix the following errors:</p>
                </div>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-medium text-gray-800">Current Tasks</h3>
            <button type="button" onclick="showAddTaskModal()" 
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add New Task
            </button>
        </div>

        <form action="{{ route('maintenance.updateByDate', ['locationId' => isset($locationId) ? $locationId : $labNumber, 'date' => $date]) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            @if($maintenances->count() > 0)
                @foreach($maintenances as $index => $maintenance)
                    <div class="border border-gray-200 rounded-lg p-4 md:p-6 bg-gray-50 hover:bg-gray-100 transition-all duration-200 group">
                        <input type="hidden" name="maintenances[{{ $index }}][id]" value="{{ $maintenance->id }}">
                        
                        <!-- Task Header with Index Badge -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <span class="bg-red-100 text-red-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                    Task {{ $index + 1 }}
                                </span>
                                <h4 class="text-sm font-medium text-gray-600">Maintenance Item</h4>
                            </div>
                            <div class="group-hover:opacity-100 transition-opacity duration-200">
                                <button type="button" onclick="deleteTask('{{ $maintenance->id }}')"
                                    class="text-sm px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    Maintenance Task
                                </label>
                                <select name="maintenances[{{ $index }}][maintenance_task]" 
                                    class="w-full px-3 py-2 border @error("maintenances.{$index}.maintenance_task") border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 bg-white">
                                    @foreach($maintenanceTasks as $task)
                                        <option value="{{ $task }}" {{ $maintenance->maintenance_task == $task ? 'selected' : '' }}>
                                            {{ $task }}
                                        </option>
                                    @endforeach
                                </select>
                                @error("maintenances.{$index}.maintenance_task")
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Technician
                                </label>
                                <select name="maintenances[{{ $index }}][technician_id]" 
                                    class="w-full px-3 py-2 border @error("maintenances.{$index}.technician_id") border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 bg-white">
                                    @foreach($technicians as $technician)
                                        <option value="{{ $technician->id }}" {{ $maintenance->technician_id == $technician->id ? 'selected' : '' }}>
                                            {{ $technician->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error("maintenances.{$index}.technician_id")
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="bg-gray-50 rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Tasks Found</h3>
                    <p class="text-gray-500 mb-6">There are no maintenance tasks scheduled for this date.</p>
                    <button type="button" onclick="showAddTaskModal()" 
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add First Task
                    </button>
                </div>
            @endif

            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('maintenance.upcoming') }}" 
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </a>
                <button type="submit" 
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Add Task Modal -->
    <div id="addTaskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
            <div class="mt-3">
                <div class="flex items-center mb-4">
                    <div class="bg-green-100 p-2 rounded-full mr-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Add New Maintenance Task</h3>
                </div>
                <form action="{{ route('maintenance.addTask', ['locationId' => isset($locationId) ? $locationId : $labNumber, 'date' => $date]) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Maintenance Task
                        </label>
                        <select name="maintenance_task" required
                            class="w-full px-3 py-2 border @error('maintenance_task') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 bg-white">
                            @php
                                $existingTasks = $maintenances->pluck('maintenance_task')->toArray();
                                $availableTasks = array_diff($maintenanceTasks, $existingTasks);
                            @endphp
                            @if(count($availableTasks) > 0)
                                @foreach($availableTasks as $task)
                                    <option value="{{ $task }}" {{ old('maintenance_task') == $task ? 'selected' : '' }}>{{ $task }}</option>
                                @endforeach
                            @else
                                <option value="" disabled>No available tasks</option>
                            @endif
                        </select>
                        @if(count($availableTasks) === 0)
                            <p class="mt-1 text-xs text-amber-600">All maintenance tasks are already assigned</p>
                        @endif
                        @error('maintenance_task')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Technician
                        </label>
                        <select name="technician_id" required
                            class="w-full px-3 py-2 border @error('technician_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 bg-white">
                            @foreach($technicians as $technician)
                                <option value="{{ $technician->id }}" {{ old('technician_id') == $technician->id ? 'selected' : '' }}>{{ $technician->name }}</option>
                            @endforeach
                        </select>
                        @error('technician_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideAddTaskModal()"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm">
                            Add Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteTaskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Delete Task</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to delete this maintenance task?</p>
                </div>
                <div class="flex justify-center space-x-3 mt-6">
                    <form id="deleteTaskForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm">
                            Delete
                        </button>
                        <button type="button" onclick="hideDeleteTaskModal()"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                            Cancel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showAddTaskModal() {
            document.getElementById('addTaskModal').classList.remove('hidden');
            // Add backdrop blur effect
            document.body.style.overflow = 'hidden';
            // Clear any previous form data
            document.querySelector('#addTaskModal form').reset();
        }

        function hideAddTaskModal() {
            document.getElementById('addTaskModal').classList.add('hidden');
            // Remove backdrop blur effect
            document.body.style.overflow = 'auto';
        }

        function deleteTask(id) {
            document.getElementById('deleteTaskForm').action = `/maintenance/task/${id}`;
            document.getElementById('deleteTaskModal').classList.remove('hidden');
            // Add backdrop blur effect
            document.body.style.overflow = 'hidden';
        }

        function hideDeleteTaskModal() {
            document.getElementById('deleteTaskModal').classList.add('hidden');
            // Remove backdrop blur effect
            document.body.style.overflow = 'auto';
        }

        // Show success/error messages with auto-hide
        function showMessage(message, type = 'success') {
            // Remove existing messages
            const existingMessages = document.querySelectorAll('.message-toast');
            existingMessages.forEach(msg => msg.remove());

            // Create message element
            const messageDiv = document.createElement('div');
            messageDiv.className = `message-toast fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
            
            if (type === 'success') {
                messageDiv.className += ' bg-green-500 text-white';
                messageDiv.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>${message}</span>
                    </div>
                `;
            } else {
                messageDiv.className += ' bg-red-500 text-white';
                messageDiv.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <span>${message}</span>
                    </div>
                `;
            }

            // Add to page
            document.body.appendChild(messageDiv);

            // Animate in
            setTimeout(() => {
                messageDiv.classList.remove('translate-x-full');
            }, 100);

            // Auto-hide after 5 seconds
            setTimeout(() => {
                messageDiv.classList.add('translate-x-full');
                setTimeout(() => {
                    if (messageDiv.parentNode) {
                        messageDiv.parentNode.removeChild(messageDiv);
                    }
                }, 300);
            }, 5000);
        }

        // Close modals when clicking outside
        document.addEventListener('DOMContentLoaded', function() {
            const modals = ['addTaskModal', 'deleteTaskModal'];
            
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            if (modalId === 'addTaskModal') {
                                hideAddTaskModal();
                            } else {
                                hideDeleteTaskModal();
                            }
                        }
                    });
                }
            });

            // Close modals with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    hideAddTaskModal();
                    hideDeleteTaskModal();
                }
            });

            // Handle form submissions with better UX
            const addTaskForm = document.querySelector('#addTaskModal form');
            if (addTaskForm) {
                addTaskForm.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    
                    // Show loading state
                    submitBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Adding...
                    `;
                    submitBtn.disabled = true;
                });
            }

            // Handle main form submission
            const mainForm = document.querySelector('form[action*="updateByDate"]');
            if (mainForm) {
                mainForm.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    
                    // Show loading state
                    submitBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    `;
                    submitBtn.disabled = true;
                });
            }
        });
    </script>
</div>
@endsection