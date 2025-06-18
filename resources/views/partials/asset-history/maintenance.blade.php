<div class="mb-8">
    @php
        // Get maintenance records and apply pagination
        $maintenanceRecords = collect($assetMaintenances ?? [])->filter(function($maintenance) use ($asset) {
            $excludedAssets = $maintenance->excluded_assets ?? [];
            $serialNumber = $asset->serial_number ?? null;
            return !(is_array($excludedAssets) && in_array($serialNumber, $excludedAssets));
        });
        
        $perPage = 10;
        $currentPage = request()->get('maintenance_page', 1);
        $totalRecords = $maintenanceRecords->count();
        $totalPages = ceil($totalRecords / $perPage);
        
        // Get records for current page
        $offset = ($currentPage - 1) * $perPage;
        $currentPageRecords = $maintenanceRecords->slice($offset, $perPage);
    @endphp

    <div class="overflow-x-auto shadow-md rounded-lg hidden md:block">
        <table class="min-w-full divide-y divide-gray-200 bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Laboratory</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Task</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Technician</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($currentPageRecords as $maintenance)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        Laboratory {{ $maintenance->lab_number }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @php
                            $tasks = is_array($maintenance->maintenance_task) ? $maintenance->maintenance_task : json_decode($maintenance->maintenance_task, true);
                        @endphp
                        @if(is_array($tasks))
                            <ul class="list-disc list-inside">
                                @foreach($tasks as $task)
                                    <li>{{ $task }}</li>
                                @endforeach
                            </ul>
                        @else
                            {{ $maintenance->maintenance_task }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $maintenance->technician->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $maintenance->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($maintenance->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm bg-gray-50">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>No maintenance records found</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Maintenance Cards (Mobile View) -->
    <div class="md:hidden">
        @php
            // Sort maintenance records by date (newest first) and apply pagination
            $sortedMaintenance = $currentPageRecords->sortByDesc('scheduled_date');

            // Group maintenance records by day
            $maintenanceByDay = $sortedMaintenance->groupBy(function($record) {
                return \Carbon\Carbon::parse($record->scheduled_date)->format('Y-m-d');
            });
        @endphp

        @forelse($maintenanceByDay as $day => $records)
            @php
                $firstRecord = $records->first(); // Still need this to get the date for the header
                $fullDate = \Carbon\Carbon::parse($firstRecord->scheduled_date)->format('F d, Y');
            @endphp

            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-3 border-b border-gray-200 pb-2">{{ $fullDate }}</h3>
                <div class="grid grid-cols-1 gap-4">
                    @foreach($records as $record)
                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="text-sm text-gray-500 mb-2">{{ \Carbon\Carbon::parse($record->scheduled_date)->format('M d, Y - h:i A') }}</div>
                            <div class="mb-2">
                                <span class="font-medium text-gray-700">Laboratory:</span> {{ $record->lab_number }}
                            </div>
                            <div class="mb-2">
                                <span class="font-medium text-gray-700">Task:</span>
                                @php
                                    $tasks = is_array($record->maintenance_task) ? $record->maintenance_task : json_decode($record->maintenance_task, true);
                                @endphp
                                @if(is_array($tasks))
                                    <ul class="list-disc list-inside ml-4">
                                        @foreach($tasks as $task)
                                            <li>{{ $task }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    {{ $record->maintenance_task }}
                                @endif
                            </div>
                            <div class="mb-2">
                                <span class="font-medium text-gray-700">Technician:</span> {{ $record->technician->name }}
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Status:</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $record->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </div>
                            @if($record->remarks)
                                <div class="mt-2 pt-2 border-t border-gray-200 text-sm text-gray-600">
                                    <span class="font-medium">Remarks:</span> {{ $record->remarks }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                <div class="flex flex-col items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>No maintenance records found</span>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination Controls -->
    @if($totalPages > 1)
        <div class="mt-8 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing {{ ($currentPage - 1) * $perPage + 1 }} to {{ min($currentPage * $perPage, $totalRecords) }} of {{ $totalRecords }} maintenance records
            </div>
            <div class="flex items-center space-x-2">
                @if($currentPage > 1)
                    <a href="?maintenance_page={{ $currentPage - 1 }}&page={{ request()->get('page') }}&start_date={{ request()->get('start_date') }}&end_date={{ request()->get('end_date') }}&active_tab={{ request()->get('active_tab', 'timeline') }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 transition-colors">
                        Previous
                    </a>
                @endif
                
                @for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                    @if($i == $currentPage)
                        <span class="px-3 py-2 text-sm font-medium text-white bg-red-800 border border-red-800 rounded-md">
                            {{ $i }}
                        </span>
                    @else
                        <a href="?maintenance_page={{ $i }}&page={{ request()->get('page') }}&start_date={{ request()->get('start_date') }}&end_date={{ request()->get('end_date') }}&active_tab={{ request()->get('active_tab', 'timeline') }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 transition-colors">
                            {{ $i }}
                        </a>
                    @endif
                @endfor
                
                @if($currentPage < $totalPages)
                    <a href="?maintenance_page={{ $currentPage + 1 }}&page={{ request()->get('page') }}&start_date={{ request()->get('start_date') }}&end_date={{ request()->get('end_date') }}&active_tab={{ request()->get('active_tab', 'timeline') }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 transition-colors">
                        Next
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>