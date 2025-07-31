@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6">
        <h2 class="text-xl md:text-2xl font-bold mb-4 md:mb-0">UPCOMING MAINTENANCE</h2>
        <a href="{{ route('maintenance.schedule') }}" class="px-4 py-2 bg-red-800 text-white rounded-md hover:bg-red-700 transition-colors duration-200 text-center md:text-left">
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

    @if($maintenances->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Upcoming Maintenance</h3>
            <p class="text-gray-500 mb-4">There are currently no maintenance tasks scheduled.</p>
            <a href="{{ route('maintenance.schedule') }}" class="inline-flex items-center px-4 py-2 bg-red-800 text-white rounded-md hover:bg-red-700 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Schedule New Maintenance
            </a>
        </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
        @php
        $locationMaintenances = $maintenances->groupBy('location_id');
        @endphp
        @foreach($locationMaintenances as $locationId => $locationMaintenance)
        @php
        $location = $locationMaintenance->first()->location;
        $locationName = $location ? $location->building . ' - Floor ' . $location->floor . ' - Room ' . $location->room_number : 'Unknown Location';
        @endphp
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="flex justify-between items-center p-4 md:p-6 bg-gray-50 cursor-pointer" onclick="toggleLab('location-{{ $locationId }}')">
                <h3 class="text-lg md:text-xl font-semibold text-gray-800">{{ $locationName }}</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-xs md:text-sm text-gray-500">
                        {{ $locationMaintenance->count() }} tasks
                    </span>
                    <svg class="w-5 h-5 transform transition-transform duration-200" id="chevron-{{ $locationId }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <div id="location-{{ $locationId }}" class="hidden p-4 md:p-6 border-t border-gray-100">
                @php
                $labMaintenance = $locationMaintenance;
                @endphp

                @if($labMaintenance->isEmpty())
                <div class="text-center py-6 md:py-8">
                    <svg class="mx-auto h-10 w-10 md:h-12 md:w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="mt-2 text-gray-500">No upcoming maintenance scheduled.</p>
                </div>
                @else
                <div class="space-y-4 md:space-y-6">
                    @php
                    $groupedMaintenance = $labMaintenance->groupBy(function($item) {
                    return \Carbon\Carbon::parse($item->scheduled_date)->format('Y-m-d');
                    });
                    @endphp

                    @foreach($groupedMaintenance as $date => $maintenanceItems)
                    <div class="border rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-3 py-2 md:px-4 md:py-3 border-b cursor-pointer" onclick="toggleDate('date-{{ str_replace('-', '', $date) }}-{{ $locationId }}')">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                                <div class="mb-2 md:mb-0">
                                    <h4 class="font-medium text-gray-800">
                                        {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                                    </h4>
                                    <p class="text-xs md:text-sm text-gray-500">
                                        {{ $maintenanceItems->count() }} tasks scheduled
                                    </p>
                                </div>
                                <div class="flex items-center justify-between md:justify-end md:space-x-2">
                                    <div class="flex space-x-1 md:space-x-2">
                                        <button onclick="completeAllTasks('{{ $locationId}}', '{{ $date }}'); event.stopPropagation();" 
                                            class="text-xs md:text-sm px-2 py-1 md:px-3 md:py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200 flex items-center">
                                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="ml-1">All</span>
                                        </button>
                                        <button onclick="cancelAllTasks('{{ $locationId}}', '{{ $date }}'); event.stopPropagation();" 
                                            class="text-xs md:text-sm px-2 py-1 md:px-3 md:py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors duration-200 flex items-center">
                                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            <span class="ml-1">All</span>
                                        </button>
                                        <a href="{{ route('maintenance.editByDate', ['locationId' => $locationId, 'date' => $date]) }}" 
                                            onclick="event.stopPropagation();"
                                            class="text-xs md:text-sm px-2 py-1 md:px-3 md:py-1.5 bg-red-800 text-white rounded-md hover:bg-red-700 transition-colors duration-200 flex items-center">
                                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    </div>
                                    <svg class="w-4 h-4 md:w-5 md:h-5 transform transition-transform duration-200 ml-2" id="chevron-date-{{ str_replace('-', '', $date) }}-{{ $locationId}}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div id="date-{{ str_replace('-', '', $date) }}-{{ $locationId}}" class="hidden">
                            <!-- Rest of your date content -->
                            <div class="divide-y divide-gray-100">
                                @php
                                $allExcludedAssets = $maintenanceItems->pluck('excluded_assets')->flatten()->unique()->filter();
                                @endphp
                                @if($allExcludedAssets->isNotEmpty())
                                    <div class="p-3 md:p-4 bg-gray-50">
                                        <p class="text-xs md:text-sm text-gray-600 font-medium mb-2">Excluded Assets for this Schedule:</p>
                                        <ul class="list-disc list-inside text-xs md:text-sm text-gray-500">
                                            @foreach($allExcludedAssets as $asset)
                                                <li>{{ $asset }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Maintenance tasks list -->
                                @foreach($maintenanceItems as $maintenance)
                                <div class="p-3 md:p-4 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex flex-col md:flex-row md:justify-between md:items-start">
                                        <div class="mb-3 md:mb-0">
                                            <h4 class="font-medium text-gray-800">{{ $maintenance->maintenance_task }}</h4>
                                            <div class="flex items-center mt-1 space-x-2">
                                                <svg class="w-3 h-3 md:w-4 md:h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <p class="text-xs md:text-sm text-gray-600">
                                                    {{ $maintenance->technician->name }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex flex-row md:flex-col items-center md:items-end justify-between md:space-y-2">
                                            <span class="px-2 py-0.5 md:px-2.5 md:py-1 text-xs font-semibold rounded-full 
                                                                            bg-yellow-100 text-yellow-800">
                                                {{ ucfirst($maintenance->status) }}
                                            </span>
                                            <div class="flex space-x-2 mt-0 md:mt-2">
                                                <button onclick="markAsComplete('{{ $maintenance->id }}', '{{ $locationId}}')" 
                                                    class="text-xs md:text-sm px-2 py-1 md:px-3 md:py-1 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200">
                                                    <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                                <button onclick="cancelMaintenance('{{ $maintenance->id }}')" 
                                                    class="text-xs md:text-sm px-2 py-1 md:px-3 md:py-1 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors duration-200">
                                                    <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- Move this modal inside the form where the cancel buttons are -->
<div class="items-center px-4 py-3">
    <form id="cancelForm" method="POST">
        @csrf
        @method('DELETE')
        <!-- Add the modal inside the form -->
        <div id="cancellationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Cancel Maintenance</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">Are you sure you want to cancel this maintenance task?</p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Confirm
                        </button>
                        <button type="button" onclick="hideModal('cancellationModal')" class="ml-3 px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Include the completion modal partial -->
@include('partials.maintenance-completion-modal')

<script>
    function toggleDate(dateId) {
        const content = document.getElementById(dateId);
        const chevron = document.getElementById(`chevron-${dateId}`);

        content.classList.toggle('hidden');
        chevron.classList.toggle('rotate-180');
    }

    function toggleLab(labId) {
        const content = document.getElementById(labId);
        const locationId= labId.split('-')[1];
        const chevron = document.getElementById(`chevron-${locationId}`);

        content.classList.toggle('hidden');
        chevron.classList.toggle('rotate-180');
    }

    function completeAllTasks(locationId, date) {
        const form = document.getElementById('completeForm');
        form.action = `{{ url('maintenance') }}/${locationId}/date/${date}/complete-all`;
        form.setAttribute('data-location', locationId); // Add this line to set the location ID
        document.getElementById('completionModal').classList.remove('hidden');
    }

    function hideModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function markAsComplete(maintenanceId, locationId) {
        const form = document.getElementById('completeForm');
        form.action = `{{ url('maintenance') }}/${maintenanceId}/complete`;
        form.setAttribute('data-location', locationId);

        // Reset form and hide issue details
        form.reset();
        document.getElementById('issueDetails').classList.add('hidden');
        document.getElementById('assetMessage').classList.add('hidden');
        
        // Clear any additional issues
        document.getElementById('additionalIssues').innerHTML = '';

        // Show modal
        document.getElementById('completionModal').classList.remove('hidden');
    }

    function cancelMaintenance(id) {
        const form = document.getElementById('cancelForm');
        form.action = `{{ url('maintenance') }}/${id}`;
        document.getElementById('cancellationModal').classList.remove('hidden');
    }

    function cancelAllTasks(locationId, date) {
        const form = document.getElementById('cancelForm');
        form.action = `{{ url('maintenance') }}/${locationId}/date/${date}/cancel-all`;
        document.getElementById('cancellationModal').classList.remove('hidden');
    } 
</script>
@endsection