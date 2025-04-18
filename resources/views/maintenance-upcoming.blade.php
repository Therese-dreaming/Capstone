@extends('layouts.app')

@section('content')
<div class="flex-1 p-8 ml-72">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">UPCOMING MAINTENANCE</h2>
        <a href="{{ route('maintenance.schedule') }}" class="px-4 py-2 bg-red-800 text-white rounded-md hover:bg-red-700 transition-colors duration-200">
            Schedule New Maintenance
        </a>
    </div>
    
    <div class="border-b-2 border-red-800 mb-6"></div>


    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
        <p class="font-medium">Success!</p>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-2 gap-6">
        @foreach(['401', '402', '403', '404', '405', '406'] as $labNumber)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="flex justify-between items-center p-6 bg-gray-50 cursor-pointer" onclick="toggleLab('lab-{{ $labNumber }}')">
                <h3 class="text-xl font-semibold text-gray-800">Computer Laboratory {{ $labNumber }}</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">
                        {{ $maintenances->where('lab_number', $labNumber)->count() }} tasks
                    </span>
                    <svg class="w-5 h-5 transform transition-transform duration-200" id="chevron-{{ $labNumber }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <div id="lab-{{ $labNumber }}" class="hidden p-6 border-t border-gray-100">
                @php
                $labMaintenance = $maintenances->where('lab_number', $labNumber);
                @endphp

                @if($labMaintenance->isEmpty())
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="mt-2 text-gray-500">No upcoming maintenance scheduled.</p>
                </div>
                @else
                <div class="space-y-6">
                    @php
                    $groupedMaintenance = $labMaintenance->groupBy(function($item) {
                    return \Carbon\Carbon::parse($item->scheduled_date)->format('Y-m-d');
                    });
                    @endphp

                    @foreach($groupedMaintenance as $date => $maintenanceItems)
                    <div class="border rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-4 py-3 border-b">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-gray-800">
                                        {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        {{ $maintenanceItems->count() }} tasks scheduled
                                    </p>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="completeAllTasks('{{ $labNumber }}', '{{ $date }}')" 
                                        class="text-sm px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200 flex items-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span class="ml-1">All</span>
                                    </button>
                                    <button onclick="cancelAllTasks('{{ $labNumber }}', '{{ $date }}')" 
                                        class="text-sm px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors duration-200 flex items-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span class="ml-1">All</span>
                                    </button>
                                    <a href="{{ route('maintenance.editByDate', ['lab' => $labNumber, 'date' => $date]) }}" 
                                        class="text-sm px-3 py-1.5 bg-red-800 text-white rounded-md hover:bg-red-700 transition-colors duration-200 flex items-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @foreach($maintenanceItems as $maintenance)
                            <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-800">{{ $maintenance->maintenance_task }}</h4>
                                        <div class="flex items-center mt-1 space-x-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <p class="text-sm text-gray-600">
                                                {{ $maintenance->technician->name }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end space-y-2">
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full 
                                                            bg-yellow-100 text-yellow-800">
                                            {{ ucfirst($maintenance->status) }}
                                        </span>
                                        <div class="flex space-x-2">
                                            <button onclick="markAsComplete('{{ $maintenance->id }}')" 
                                                class="text-sm px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                            <button onclick="cancelMaintenance('{{ $maintenance->id }}')" 
                                                class="text-sm px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Completion Modal -->
    <div id="completionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Complete Maintenance</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure this maintenance task has been completed?</p>
                </div>
                <div class="items-center px-4 py-3">
                    <form id="completeForm" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Confirm
                        </button>
                        <button type="button" onclick="hideModal('completionModal')" class="ml-3 px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancellation Modal -->
    <div id="cancellationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Cancel Maintenance</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to cancel this maintenance task?</p>
                </div>
                <div class="items-center px-4 py-3">
                    <form id="cancelForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Confirm
                        </button>
                        <button type="button" onclick="hideModal('cancellationModal')" class="ml-3 px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function markAsComplete(id) {
            document.getElementById('completeForm').action = `/maintenance/${id}/complete`;
            document.getElementById('completionModal').classList.remove('hidden');
        }

        function cancelMaintenance(id) {
            document.getElementById('cancelForm').action = `/maintenance/${id}`;
            document.getElementById('cancellationModal').classList.remove('hidden');
        }

        function hideModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function toggleLab(labId) {
            const content = document.getElementById(labId);
            const labNumber = labId.split('-')[1];
            const chevron = document.getElementById(`chevron-${labNumber}`);

            content.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }

        function completeAllTasks(labNumber, date) {
            const form = document.getElementById('completeForm');
            form.action = `/maintenance/${labNumber}/date/${date}/complete-all`;
            document.getElementById('completionModal').classList.remove('hidden');
        }

        function cancelAllTasks(labNumber, date) {
            const form = document.getElementById('cancelForm');
            form.action = `/maintenance/${labNumber}/date/${date}/cancel-all`;
            document.getElementById('cancellationModal').classList.remove('hidden');
        }
    </script>
</div>
@endsection
