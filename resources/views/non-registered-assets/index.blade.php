@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8">
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center" role="alert">
        <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <div class="font-semibold">Success!</div>
            <div>{{ session('success') }}</div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-center" role="alert">
        <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
            <div class="font-semibold">Error!</div>
            <div>{{ session('error') }}</div>
        </div>
    </div>
    @endif

    <!-- Page Header with Background Design -->
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                        <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Non-Registered Pulled Out Assets</h1>
                        <p class="text-red-100 text-sm md:text-lg">Track and manage unregistered assets that have been pulled out</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="bg-white rounded-xl shadow-lg p-4 md:p-6">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">Asset Overview</h2>
                <div class="flex items-center gap-3">
                    <div class="bg-gray-50 px-4 py-2 rounded-lg">
                        <span class="text-sm font-medium text-gray-600">Total Assets: </span>
                        <span class="text-lg font-bold text-red-800">{{ $assets->total() }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($assets->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 px-4 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
            <div class="bg-white p-6 rounded-full mb-6">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Non-Registered Assets</h3>
            <p class="text-gray-600 text-center max-w-sm">
                There are currently no non-registered pulled out assets to display. Assets will appear here once they are pulled out.
            </p>
        </div>
        @else
        <!-- Mobile Cards View -->
        <div class="md:hidden space-y-4">
            @foreach($assets as $asset)
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-lg hover:shadow-xl transition-all duration-200">
                <div class="space-y-4">
                    <!-- Header with Equipment Name and Status -->
                    <div class="flex justify-between items-start">
                        <h3 class="font-bold text-lg text-gray-900">{{ $asset->equipment_name }}</h3>
                        <span class="px-3 py-1.5 inline-flex text-xs font-semibold rounded-full 
                            {{ $asset->status === 'PULLED OUT' ? 'bg-yellow-100 text-yellow-800' : 
                               ($asset->status === 'DISPOSED' ? 'bg-red-100 text-red-800' : 
                                'bg-green-100 text-green-800') }}">
                            {{ $asset->status }}
                        </span>
                    </div>

                    <!-- Asset Details -->
                    <div class="space-y-3">
                        <div class="text-sm">
                            <span class="font-semibold text-gray-700">Location:</span> 
                            <span class="text-gray-900">{{ $asset->location ?? 'Location not specified' }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="font-semibold text-gray-700">Ticket Number:</span> 
                            <span class="text-gray-900">{{ $asset->ticket_number }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="font-semibold text-gray-700">Pulled Out By:</span> 
                            <span class="text-gray-900">{{ $asset->pulled_out_by }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="font-semibold text-gray-700">Date:</span> 
                            <span class="text-gray-900">{{ $asset->pulled_out_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-3 border-t border-gray-200">
                        @if($asset->repairRequest)
                            <a href="{{ route('repair.details', ['id' => $asset->repairRequest->id]) }}" 
                               class="inline-flex items-center text-red-600 hover:text-red-800 text-sm font-semibold transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View Repair Details
                            </a>
                        @else
                            <div class="space-y-2">
                                <span class="text-gray-500 text-sm italic block">No repair request linked</span>
                                <button onclick="openLinkRepairModal('{{ $asset->id }}', '{{ $asset->equipment_name }}')" 
                                        class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-semibold transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    Link to Repair Request
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Mobile Pagination -->
            <div class="mt-6">
                {{ $assets->links() }}
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Equipment</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ticket Number</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pulled Out By</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($assets as $asset)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ $asset->equipment_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-700">{{ $asset->location ?? 'Location not specified' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-700 font-medium">{{ $asset->ticket_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1.5 inline-flex text-xs font-semibold rounded-full 
                                {{ $asset->status === 'PULLED OUT' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($asset->status === 'DISPOSED' ? 'bg-red-100 text-red-800' : 
                                    'bg-green-100 text-green-800') }}">
                                {{ $asset->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-700">{{ $asset->pulled_out_by }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-700">{{ $asset->pulled_out_at->format('M d, Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($asset->repairRequest)
                                <a href="{{ route('repair.details', ['id' => $asset->repairRequest->id]) }}" 
                                   class="inline-flex items-center text-red-600 hover:text-red-800 text-sm font-semibold transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Repair Details
                                </a>
                            @else
                                <div class="space-y-2">
                                    <span class="text-gray-500 text-sm italic block">No repair request linked</span>
                                    <button onclick="openLinkRepairModal('{{ $asset->id }}', '{{ $asset->equipment_name }}')" 
                                            class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-semibold transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                        Link to Repair Request
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Desktop Pagination -->
            <div class="mt-6">
                {{ $assets->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Link Repair Request Modal -->
<div id="linkRepairModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center" style="z-index: 60;">
    <div class="p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Header -->
            <div class="bg-blue-800 text-white p-4 rounded-t-lg -mt-3 -mx-3 mb-4">
                <h3 class="text-lg font-semibold">Link to Repair Request</h3>
                <p class="text-blue-100 text-sm">Link this asset to an existing repair request</p>
            </div>

            <form id="linkRepairForm" method="POST" class="space-y-4">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="link_ticket_number">
                        Ticket Number
                    </label>
                    <input type="text" name="ticket_number" id="link_ticket_number" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                           placeholder="Enter ticket number..." required>
                    <p class="text-xs text-gray-500 mt-1">Enter the ticket number of the repair request to link this asset to.</p>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeLinkRepairModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                        Link Asset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openLinkRepairModal(assetId, equipmentName) {
        const modal = document.getElementById('linkRepairModal');
        const form = document.getElementById('linkRepairForm');
        
        // Set the form action
        form.action = `/non-registered-assets/${assetId}/link-repair`;
        
        // Clear any previous input
        document.getElementById('link_ticket_number').value = '';
        
        // Show the modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeLinkRepairModal() {
        const modal = document.getElementById('linkRepairModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Handle form submission
    document.getElementById('linkRepairForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const assetId = this.action.split('/').pop();
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeLinkRepairModal();
                // Show success message and reload page
                window.location.reload();
            } else {
                throw new Error(data.message || 'Failed to link asset');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'An error occurred while linking the asset');
        });
    });

    // Close modal when clicking outside
    document.getElementById('linkRepairModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLinkRepairModal();
        }
    });
</script>
@endsection 