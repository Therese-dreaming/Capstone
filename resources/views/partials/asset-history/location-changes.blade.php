<div class="mb-8">
    @php
        // Get location changes and apply pagination
        $locationChanges = collect($history['LOCATION_ID'] ?? []);
        $perPage = 10;
        $currentPage = request()->get('location_page', 1);
        $totalRecords = $locationChanges->count();
        $totalPages = ceil($totalRecords / $perPage);
        
        // Get records for current page
        $offset = ($currentPage - 1) * $perPage;
        $currentPageRecords = $locationChanges->slice($offset, $perPage);
        
        // Get all unique location IDs from the records
        $locationIds = $currentPageRecords->flatMap(function($record) {
            return [$record->old_value, $record->new_value];
        })->filter()->unique();
        
        // Fetch location details
        $locations = \App\Models\Location::whereIn('id', $locationIds)->get()->keyBy('id');
        
        // Helper function to get location display
        $getLocationDisplay = function($locationId) use ($locations) {
            if (!$locationId || $locationId === 'N/A') {
                return 'Not Assigned';
            }
            $location = $locations->get($locationId);
            return $location ? $location->full_location : 'Location #' . $locationId;
        };
    @endphp

    <div class="overflow-x-auto shadow-md rounded-lg hidden md:block">
        <table class="min-w-full divide-y divide-gray-200 bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">From</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">To</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Changed By</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Remarks</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($currentPageRecords as $record)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $record->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-3 py-1.5 text-xs font-medium rounded-full inline-flex items-center justify-center min-w-[90px] bg-gray-100 text-gray-800">
                            {{ $getLocationDisplay($record->old_value) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-3 py-1.5 text-xs font-medium rounded-full inline-flex items-center justify-center min-w-[90px] bg-green-100 text-green-800">
                            {{ $getLocationDisplay($record->new_value) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $record->user->name }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if(str_contains($record->remarks, 'Reason:'))
                            @php
                                [$mainRemark, $reason] = explode('Reason:', $record->remarks);
                            @endphp
                            <div class="text-gray-600">{{ trim($mainRemark) }}</div>
                            <div class="mt-1">
                                <span class="text-xs font-medium bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                    Reason: {{ trim($reason) }}
                                </span>
                            </div>
                        @else
                            <div class="text-gray-600">{{ $record->remarks }}</div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm bg-gray-50">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>No location changes recorded</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Location Change Cards (Mobile View) -->
    <div class="md:hidden">
        @php
            // Sort location changes by date (newest first) and apply pagination
            $sortedLocationChanges = $currentPageRecords->sortByDesc('created_at');

            // Group location changes by day
            $locationChangesByDay = $sortedLocationChanges->groupBy(function($record) {
                return $record->created_at->format('Y-m-d');
            });
        @endphp

        @forelse($locationChangesByDay as $day => $records)
            @php
                $firstRecord = $records->first(); // Still need this to get the date for the header
                $fullDate = $firstRecord->created_at->format('F d, Y');
            @endphp

            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-3 border-b border-gray-200 pb-2">{{ $fullDate }}</h3>
                <div class="grid grid-cols-1 gap-4">
                    @foreach($records as $record)
                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="text-sm text-gray-500 mb-2">{{ $record->created_at->format('M d, Y - h:i A') }}</div>
                            <div class="mb-2">
                                <span class="font-medium text-gray-700">From:</span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full inline-flex items-center justify-center bg-gray-100 text-gray-800">
                                    {{ $getLocationDisplay($record->old_value) }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <span class="font-medium text-gray-700">To:</span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full inline-flex items-center justify-center bg-green-100 text-green-800">
                                    {{ $getLocationDisplay($record->new_value) }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-600 mb-2">
                                <span class="font-medium">Changed By:</span> {{ $record->user->name }}
                            </div>
                            @if($record->remarks)
                            <div class="text-sm text-gray-600">
                                <span class="font-medium">Remarks:</span>
                                @if(str_contains($record->remarks, 'Reason:'))
                                    @php
                                        [$mainRemark, $reason] = explode('Reason:', $record->remarks);
                                    @endphp
                                    <div>{{ trim($mainRemark) }}</div>
                                    <div class="mt-1 text-xs font-medium bg-gray-100 text-gray-700 px-2 py-1 rounded inline-block">
                                        Reason: {{ trim($reason) }}
                                    </div>
                                @else
                                    {{ $record->remarks }}
                                @endif
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
                    <span>No location changes recorded</span>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination Controls -->
    @if($totalPages > 1)
        <div class="mt-8 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing {{ ($currentPage - 1) * $perPage + 1 }} to {{ min($currentPage * $perPage, $totalRecords) }} of {{ $totalRecords }} location changes
            </div>
            <div class="flex items-center space-x-2">
                @if($currentPage > 1)
                    <a href="?location_page={{ $currentPage - 1 }}&page={{ request()->get('page') }}&start_date={{ request()->get('start_date') }}&end_date={{ request()->get('end_date') }}&active_tab={{ request()->get('active_tab', 'timeline') }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 transition-colors">
                        Previous
                    </a>
                @endif
                
                @for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                    @if($i == $currentPage)
                        <span class="px-3 py-2 text-sm font-medium text-white bg-red-800 border border-red-800 rounded-md">
                            {{ $i }}
                        </span>
                    @else
                        <a href="?location_page={{ $i }}&page={{ request()->get('page') }}&start_date={{ request()->get('start_date') }}&end_date={{ request()->get('end_date') }}&active_tab={{ request()->get('active_tab', 'timeline') }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 transition-colors">
                            {{ $i }}
                        </a>
                    @endif
                @endfor
                
                @if($currentPage < $totalPages)
                    <a href="?location_page={{ $currentPage + 1 }}&page={{ request()->get('page') }}&start_date={{ request()->get('start_date') }}&end_date={{ request()->get('end_date') }}&active_tab={{ request()->get('active_tab', 'timeline') }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 transition-colors">
                        Next
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>