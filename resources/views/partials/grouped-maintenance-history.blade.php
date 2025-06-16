@php
    // Group maintenances by laboratory and completion date
    $groupedMaintenances = $maintenances->groupBy(function($maintenance) {
        return $maintenance->lab_number . '_' . ($maintenance->completed_at ? \Carbon\Carbon::parse($maintenance->completed_at)->format('Y-m-d') : 'pending');
    });
@endphp

<!-- Grouped Maintenance Records as Cards (Mobile Only) -->
<div class="grid grid-cols-1 gap-6 md:hidden" id="maintenanceCards">
    @forelse($groupedMaintenances as $groupKey => $group)
        @php
            $firstMaintenance = $group->first();
            $labNumber = $firstMaintenance->lab_number;
            $completionDate = $firstMaintenance->completed_at ? \Carbon\Carbon::parse($firstMaintenance->completed_at)->format('Y-m-d') : null;
        @endphp
        <div class="bg-white rounded-lg shadow p-4 flex flex-col gap-2 border border-gray-200">
            <div class="flex justify-between items-center">
                <span class="font-semibold text-red-800">
                    @if($completionDate)
                        {{ \Carbon\Carbon::parse($completionDate)->format('M d, Y') }}
                    @else
                        Pending
                    @endif
                </span>
                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                    {{ $firstMaintenance->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($firstMaintenance->status) }}
                </span>
            </div>
            <div class="text-sm text-gray-700">Laboratory <span class="font-semibold">{{ $labNumber }}</span></div>
            
            <div class="mt-2">
                <button onclick="viewGroupDetails({{ $group->toJson() }})" class="text-sm text-blue-600 hover:text-blue-800">
                    View Details ({{ $group->count() }} records)
                </button>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center text-gray-500">No maintenance history found</div>
    @endforelse
</div>

<!-- Grouped Maintenance Records as Table (Desktop Only) -->
<div class="overflow-x-auto hidden md:block">
    <table id="maintenanceTable" class="min-w-full divide-y divide-gray-200 text-xs md:text-sm">
        <thead class="bg-[#960106]">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-medium text-white">
                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                </th>
                <th class="px-6 py-3 text-left text-sm font-medium text-white">Date</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-white">Laboratory</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-white">Status</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-white">Records</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-white">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($groupedMaintenances as $groupKey => $group)
                @php
                    $firstMaintenance = $group->first();
                    $labNumber = $firstMaintenance->lab_number;
                    $completionDate = $firstMaintenance->completed_at ? \Carbon\Carbon::parse($firstMaintenance->completed_at)->format('Y-m-d') : null;
                    $hasIssues = $group->contains(function($maintenance) {
                        return !empty($maintenance->asset_issues);
                    });
                @endphp
                <tr data-lab="{{ $labNumber }}" data-status="{{ $firstMaintenance->status }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" name="selected[]" value="{{ $group->pluck('id')->join(',') }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($completionDate)
                            {{ \Carbon\Carbon::parse($completionDate)->format('M d, Y') }}
                        @else
                            Pending
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        Laboratory {{ $labNumber }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $firstMaintenance->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($firstMaintenance->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $group->count() }} records
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex space-x-2">
                            <button onclick="viewGroupDetails({{ $group->toJson() }})" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                            @if($hasIssues)
                                <button onclick="viewGroupAssetIssues({{ $group->toJson() }})" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No maintenance history found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Group Details Modal -->
<div id="groupDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg max-w-4xl mx-auto w-full shadow-xl transform transition-all">
        <div class="bg-[#960106] text-white p-4 md:p-6 rounded-t-lg relative">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold">Maintenance Details</h3>
                <button onclick="closeGroupDetailsModal()" class="text-white/70 hover:text-white transition-colors duration-150">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-4 md:p-6">
            <div id="groupDetailsContent" class="space-y-4"></div>
        </div>
    </div>
</div>

<script>
function viewGroupDetails(group) {
    const modal = document.getElementById('groupDetailsModal');
    const content = document.getElementById('groupDetailsContent');
    content.innerHTML = '';

    // Ensure group is an array
    const maintenanceRecords = Array.isArray(group) ? group : [group];

    maintenanceRecords.forEach(maintenance => {
        // Get the maintenance task
        const task = maintenance.maintenance_task;

        // Get excluded assets
        let excludedAssets = [];
        try {
            if (maintenance.excluded_assets) {
                excludedAssets = typeof maintenance.excluded_assets === 'string' 
                    ? JSON.parse(maintenance.excluded_assets) 
                    : maintenance.excluded_assets;
            }
        } catch (e) {
            console.error('Error parsing excluded assets:', e);
        }

        // Format the completion time
        const completionTime = maintenance.completed_at 
            ? new Date(maintenance.completed_at).toLocaleString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
              })
            : 'Pending';

        // Get technician and action by names
        const technicianName = maintenance.technician && maintenance.technician.name 
            ? maintenance.technician.name 
            : 'Not assigned';
        
        const actionByName = maintenance.actionBy && maintenance.actionBy.name 
            ? maintenance.actionBy.name 
            : 'System';

        content.innerHTML += `
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h4 class="font-medium text-gray-900">Technician: ${technicianName}</h4>
                        <p class="text-sm text-gray-600">Action By: ${actionByName}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Completion Time:</p>
                        <p class="font-medium">${completionTime}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h5 class="font-medium text-gray-700 mb-2">Task:</h5>
                        <p class="text-sm text-gray-600">${task}</p>
                    </div>
                    <div>
                        <h5 class="font-medium text-gray-700 mb-2">Excluded Assets:</h5>
                        <ul class="list-disc list-inside text-sm text-gray-600">
                            ${excludedAssets && excludedAssets.length > 0 
                                ? excludedAssets.map(asset => `<li>${asset}</li>`).join('')
                                : '<li>None</li>'}
                        </ul>
                    </div>
                </div>
            </div>
        `;
    });

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeGroupDetailsModal() {
    const modal = document.getElementById('groupDetailsModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('groupDetailsContent').innerHTML = '';
}

function viewGroupAssetIssues(group) {
    const modal = document.getElementById('assetIssuesModal');
    const assetIssuesList = document.getElementById('assetIssuesList');
    assetIssuesList.innerHTML = '';

    group.forEach(maintenance => {
        if (maintenance.asset_issues) {
            const assetIssues = Array.isArray(maintenance.asset_issues) ? 
                maintenance.asset_issues : 
                JSON.parse(maintenance.asset_issues || '[]');

            const serialNumbers = Array.isArray(maintenance.serial_number) ? 
                maintenance.serial_number : 
                JSON.parse(maintenance.serial_number || '[]');

            if (assetIssues.length > 0) {
                assetIssues.forEach((issue, index) => {
                    const serialNumber = serialNumbers[index] || 'Unknown';
                    assetIssuesList.innerHTML += `
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <h4 class="font-medium text-gray-900">Asset: ${serialNumber}</h4>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="flex items-start space-x-3">
                                    <div class="bg-red-50 p-2 rounded-full mt-1">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="flex-grow">
                                        <p class="text-gray-700">${issue.issue_description}</p>
                                    </div>
                                </div>
                                <div class="mt-4 text-right">
                                    <button onclick="createRepairRequest('${serialNumber}', '${encodeURIComponent(issue.issue_description)}', '${maintenance.lab_number}')" class="text-xs px-3 py-1 bg-red-800 text-white rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                        Create Repair Request
                                    </button>
                                </div>
                            </div>
                        </div>`;
                });
            }
        }
    });

    if (assetIssuesList.innerHTML === '') {
        assetIssuesList.innerHTML = '<div class="text-gray-500 italic text-center">No issues reported</div>';
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
</script> 