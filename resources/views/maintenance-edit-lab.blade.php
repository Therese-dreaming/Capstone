@extends('layouts.app')

@section('content')
<div class="flex-1 px-4 py-6 md:p-8">
    <h2 class="text-2xl font-semibold mb-6">
        Edit Maintenance Schedule - Lab {{ $labNumber }}
        <span class="text-gray-600 text-lg ml-2">
            {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
        </span>
    </h2>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-medium">Current Tasks</h3>
            <button type="button" onclick="showAddTaskModal()" 
                class="px-4 py-2 bg-red-800 text-white rounded-md hover:bg-red-700">
                Add New Task
            </button>
        </div>

        <form action="{{ route('maintenance.updateByDate', ['lab' => $labNumber, 'date' => $date]) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            @foreach($maintenances as $index => $maintenance)
                <div class="border rounded-lg p-4 bg-gray-50">
                    <input type="hidden" name="maintenances[{{ $index }}][id]" value="{{ $maintenance->id }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maintenance Task</label>
                            <select name="maintenances[{{ $index }}][maintenance_task]" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                                @foreach($maintenanceTasks as $task)
                                    <option value="{{ $task }}" {{ $maintenance->maintenance_task == $task ? 'selected' : '' }}>
                                        {{ $task }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Technician</label>
                            <select name="maintenances[{{ $index }}][technician_id]" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                                @foreach($technicians as $technician)
                                    <option value="{{ $technician->id }}" {{ $maintenance->technician_id == $technician->id ? 'selected' : '' }}>
                                        {{ $technician->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button type="button" onclick="deleteTask('{{ $maintenance->id }}')"
                            class="text-sm px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                            Delete Task
                        </button>
                    </div>
                </div>
            @endforeach

            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('maintenance.upcoming') }}" 
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                    Cancel
                </a>
                <button type="submit" 
                    class="px-4 py-2 bg-red-800 text-white rounded-md hover:bg-red-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Add Task Modal -->
    <div id="addTaskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Maintenance Task</h3>
                <form action="{{ route('maintenance.addTask', ['lab' => $labNumber, 'date' => $date]) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Maintenance Task</label>
                        <select name="maintenance_task" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                            @php
                                $existingTasks = $maintenances->pluck('maintenance_task')->toArray();
                                $availableTasks = array_diff($maintenanceTasks, $existingTasks);
                            @endphp
                            @foreach($availableTasks as $task)
                                <option value="{{ $task }}">{{ $task }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Technician</label>
                        <select name="technician_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                            @foreach($technicians as $technician)
                                <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideAddTaskModal()"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Add Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteTaskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900">Delete Task</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to delete this maintenance task?</p>
                </div>
                <div class="flex justify-center space-x-3">
                    <form id="deleteTaskForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Delete
                        </button>
                        <button type="button" onclick="hideDeleteTaskModal()"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
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
        }

        function hideAddTaskModal() {
            document.getElementById('addTaskModal').classList.add('hidden');
        }

        function deleteTask(id) {
            document.getElementById('deleteTaskForm').action = `/maintenance/task/${id}`;
            document.getElementById('deleteTaskModal').classList.remove('hidden');
        }

        function hideDeleteTaskModal() {
            document.getElementById('deleteTaskModal').classList.add('hidden');
        }
    </script>
</div>
@endsection