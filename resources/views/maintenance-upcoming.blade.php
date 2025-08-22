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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Upcoming Maintenance</h1>
                        <p class="text-red-100 text-sm md:text-lg">View and manage scheduled maintenance tasks</p>
                    </div>
                </div>
                <a href="{{ route('maintenance.schedule') }}" 
                   class="inline-flex items-center px-4 py-2 bg-white/20 text-white font-medium rounded-lg hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 focus:ring-offset-2 focus:ring-offset-red-800 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Schedule New Maintenance
                </a>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-4 md:mb-6 p-3 md:p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center" role="alert">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="font-medium">Success!</p>
                <p class="text-sm md:text-base">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($maintenances->isEmpty())
        <!-- Main Content Card -->
        <div class="bg-white rounded-xl shadow-md p-6 md:p-8 text-center">
            <div class="bg-gray-50 rounded-full w-20 h-20 mx-auto mb-6 flex items-center justify-center">
                <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3 class="text-xl md:text-2xl font-semibold text-gray-900 mb-3">No Upcoming Maintenance</h3>
            <p class="text-gray-500 mb-6 text-sm md:text-base">There are currently no maintenance tasks scheduled.</p>
            <a href="{{ route('maintenance.schedule') }}" 
               class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Schedule New Maintenance
            </a>
        </div>
    @else
    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
        @php
        $locationMaintenances = $maintenances->groupBy('location_id');
        @endphp
        @foreach($locationMaintenances as $locationId => $locationMaintenance)
        @php
        $location = $locationMaintenance->first()->location;
        $locationName = $location ? $location->building . ' - Floor ' . $location->floor . ' - Room ' . $location->room_number : 'Unknown Location';
        @endphp
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="flex justify-between items-center p-4 md:p-6 bg-gray-50 cursor-pointer hover:bg-gray-100 transition-colors duration-200" onclick="toggleLab('location-{{ $locationId }}')">
                <h3 class="text-lg md:text-xl font-semibold text-gray-800">{{ $locationName }}</h3>
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full">
                        {{ $locationMaintenance->count() }} tasks
                    </span>
                    <svg class="w-5 h-5 transform transition-transform duration-200 text-gray-500" id="chevron-{{ $locationId }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="bg-gray-50 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm md:text-base">No upcoming maintenance scheduled.</p>
                </div>
                @else
                <div class="space-y-4 md:space-y-6">
                    @php
                    $groupedMaintenance = $labMaintenance->groupBy(function($item) {
                    return \Carbon\Carbon::parse($item->scheduled_date)->format('Y-m-d');
                    });
                    @endphp

                    @foreach($groupedMaintenance as $date => $maintenanceItems)
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-3 py-2 md:px-4 md:py-3 border-b border-gray-200 cursor-pointer hover:bg-gray-100 transition-colors duration-200" onclick="toggleDate('date-{{ str_replace('-', '', $date) }}-{{ $locationId }}')">
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
                                            class="text-xs md:text-sm px-2 py-1 md:px-3 md:py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200 flex items-center shadow-sm">
                                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="ml-1">All</span>
                                        </button>
                                        <button onclick="cancelAllTasks('{{ $locationId}}', '{{ $date }}'); event.stopPropagation();" 
                                            class="text-xs md:text-sm px-2 py-1 md:px-3 md:py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 flex items-center shadow-sm">
                                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            <span class="ml-1">All</span>
                                        </button>
                                        <a href="{{ route('maintenance.editByDate', ['locationId' => $locationId, 'date' => $date]) }}" 
                                            onclick="event.stopPropagation();"
                                            class="text-xs md:text-sm px-2 py-1 md:px-3 md:py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 flex items-center shadow-sm">
                                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    </div>
                                    <svg class="w-4 h-4 md:w-5 md:h-5 transform transition-transform duration-200 ml-2 text-gray-500" id="chevron-date-{{ str_replace('-', '', $date) }}-{{ $locationId}}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <div class="p-3 md:p-4 bg-gray-50 border-l-4 border-blue-400">
                                        <p class="text-xs md:text-sm text-gray-700 font-medium mb-2 flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Excluded Assets for this Schedule:
                                        </p>
                                        <ul class="list-disc list-inside text-xs md:text-sm text-gray-600 ml-4 space-y-1">
                                            @foreach($allExcludedAssets as $asset)
                                                <li>{{ $asset }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Maintenance tasks list -->
                                @foreach($maintenanceItems as $maintenance)
                                <div class="p-3 md:p-4 hover:bg-gray-50 transition-colors duration-200 border-b border-gray-100 last:border-b-0">
                                    <div class="flex flex-col md:flex-row md:justify-between md:items-start">
                                        <div class="mb-3 md:mb-0">
                                            <h4 class="font-medium text-gray-800">{{ $maintenance->maintenance_task }}</h4>
                                            <div class="flex items-center mt-2 space-x-2">
                                                <div class="bg-blue-50 p-1 rounded-full">
                                                    <svg class="w-3 h-3 md:w-4 md:h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                </div>
                                                <p class="text-xs md:text-sm text-gray-600">
                                                    {{ $maintenance->technician->name }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex flex-row md:flex-col items-center md:items-end justify-between md:space-y-2">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                                @if($maintenance->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($maintenance->status === 'completed') bg-green-100 text-green-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($maintenance->status) }}
                                            </span>
                                            <div class="flex space-x-2 mt-2">
                                                <button onclick="markAsComplete('{{ $maintenance->id }}', '{{ $locationId}}')" 
                                                    class="text-xs md:text-sm px-2 py-1 md:px-3 md:py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm">
                                                    <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                                <button onclick="cancelMaintenance('{{ $maintenance->id }}')" 
                                                    class="text-xs md:text-sm px-2 py-1 md:px-3 md:py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm">
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

    <!-- Cancellation Modal -->
    <div class="items-center px-4 py-3">
        <form id="cancelForm" method="POST">
            @csrf
            @method('PATCH')
            <div id="cancellationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
                    <div class="mt-3 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Cancel Maintenance</h3>
                        <div class="mt-2 px-7 py-3">
                            <p class="text-sm text-gray-500 mb-4">Are you sure you want to cancel this maintenance task?</p>
                            
                            <!-- Notes Field -->
                            <div class="text-left">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                                <textarea name="notes" rows="3" placeholder="Add any notes regarding cancellation (e.g., reason for cancellation)" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 px-3 py-2 text-sm"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-center space-x-3 mt-6">
                            <button type="submit" 
                                    class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                                Confirm
                            </button>
                            <button type="button" onclick="hideModal('cancellationModal')" 
                                    class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-lg shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
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



    function cancelMaintenance(id) {
        const form = document.getElementById('cancelForm');
        form.action = `{{ url('maintenance') }}/${id}/cancel`;
        document.getElementById('cancellationModal').classList.remove('hidden');
    }

    function cancelAllTasks(locationId, date) {
        const form = document.getElementById('cancelForm');
        form.action = `{{ url('maintenance') }}/${locationId}/date/${date}/cancel-all`;
        document.getElementById('cancellationModal').classList.remove('hidden');
    } 
</script>
@endsection