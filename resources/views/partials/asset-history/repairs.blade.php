<div class="mb-8">
    @php
        // Get repair histories and apply pagination
        $repairRecords = $repairHistories ?? collect();
        $perPage = 10;
        $currentPage = request()->get('repair_page', 1);
        $totalRecords = $repairRecords->count();
        $totalPages = ceil($totalRecords / $perPage);
        
        // Get records for current page
        $offset = ($currentPage - 1) * $perPage;
        $currentPageRecords = $repairRecords->slice($offset, $perPage);
    @endphp

    <div class="overflow-x-auto shadow-md rounded-lg hidden md:block">
        <table class="min-w-full divide-y divide-gray-200 bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Ticket No.</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Issue</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Technician</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Remarks</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($currentPageRecords as $record)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div>
                                <div class="font-medium">Requested: {{ $record->repairRequest->created_at->format('M d, Y') }}</div>
                                <div class="text-gray-600">Completed: {{ \Carbon\Carbon::parse($record->completed_at)->format('M d, Y') }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <a href="{{ route('repair.details', ['id' => $record->repairRequest->id]) }}" 
                               class="text-red-600 hover:text-red-800 hover:underline font-medium">
                                {{ $record->repairRequest->ticket_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $record->repairRequest->issue ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $record->technician->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($record->verification_status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Approved
                                </span>
                            @elseif($record->verification_status === 'disputed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Rework Requested
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div>
                                <div class="font-medium text-gray-700">Attempt #{{ $record->attempt_number }}</div>
                                @if($record->findings)
                                    <div class="mt-1">{{ Str::limit($record->findings, 100) }}</div>
                                @endif
                                @if($record->caller_feedback)
                                    <div class="mt-2 text-red-600">
                                        <span class="font-medium">Feedback:</span> {{ $record->caller_feedback }}
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-sm bg-gray-50">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>No repair records found</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Repair Cards (Mobile View) -->
    <div class="md:hidden">
        @php
            // Sort repair records by date (newest first)
            $sortedRepairs = $currentPageRecords->sortByDesc('completed_at');

            // Group repair records by day
            $repairsByDay = $sortedRepairs->groupBy(function($record) {
                return \Carbon\Carbon::parse($record->completed_at)->format('Y-m-d');
            });
        @endphp

        @forelse($repairsByDay as $day => $records)
            @php
                $firstRecord = $records->first();
                $fullDate = \Carbon\Carbon::parse($firstRecord->completed_at)->format('F d, Y');
            @endphp

            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-3 border-b border-gray-200 pb-2">{{ $fullDate }}</h3>
                <div class="grid grid-cols-1 gap-4">
                    @foreach($records as $record)
                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($record->completed_at)->format('M d, Y - h:i A') }}</div>
                                @if($record->verification_status === 'approved')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Approved
                                    </span>
                                @elseif($record->verification_status === 'disputed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Rework
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Pending
                                    </span>
                                @endif
                            </div>
                            <div class="mb-2">
                                <span class="font-medium text-gray-700">Attempt:</span> #{{ $record->attempt_number }}
                            </div>
                            <div class="mb-2">
                                <span class="font-medium text-gray-700">Ticket:</span>
                                <a href="{{ route('repair.details', ['id' => $record->repairRequest->id]) }}" 
                                   class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors">
                                    {{ $record->repairRequest->ticket_number }}
                                </a>
                            </div>
                            <div class="mb-2">
                                <span class="font-medium text-gray-700">Issue:</span>
                                <span class="text-gray-700">{{ $record->repairRequest->issue ?? 'N/A' }}</span>
                            </div>
                            <div class="text-sm text-gray-600 mb-2">
                                <span class="font-medium">Technician:</span> {{ $record->technician->name ?? 'N/A' }}
                            </div>
                            @if($record->findings)
                            <div class="text-sm text-gray-600 mt-2 pt-2 border-t border-gray-200">
                                <span class="font-medium">Findings:</span> {{ $record->findings }}
                            </div>
                            @endif
                            @if($record->caller_feedback)
                            <div class="text-sm text-red-600 mt-2 pt-2 border-t border-red-200">
                                <span class="font-medium">Feedback:</span> {{ $record->caller_feedback }}
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
                    <span>No repair records found</span>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination Controls -->
    @if($totalPages > 1)
        <div class="mt-8 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing {{ ($currentPage - 1) * $perPage + 1 }} to {{ min($currentPage * $perPage, $totalRecords) }} of {{ $totalRecords }} repair records
            </div>
            <div class="flex items-center space-x-2">
                @if($currentPage > 1)
                    <a href="?repair_page={{ $currentPage - 1 }}&page={{ request()->get('page') }}&start_date={{ request()->get('start_date') }}&end_date={{ request()->get('end_date') }}&active_tab={{ request()->get('active_tab', 'timeline') }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 transition-colors">
                    Previous
                </a>
                @endif
                
                @for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                    @if($i == $currentPage)
                        <span class="px-3 py-2 text-sm font-medium text-white bg-red-800 border border-red-800 rounded-md">
                            {{ $i }}
                        </span>
                    @else
                        <a href="?repair_page={{ $i }}&page={{ request()->get('page') }}&start_date={{ request()->get('start_date') }}&end_date={{ request()->get('end_date') }}&active_tab={{ request()->get('active_tab', 'timeline') }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 transition-colors">
                            {{ $i }}
                        </a>
                    @endif
                @endfor
                
                @if($currentPage < $totalPages)
                    <a href="?repair_page={{ $currentPage + 1 }}&page={{ request()->get('page') }}&start_date={{ request()->get('start_date') }}&end_date={{ request()->get('end_date') }}&active_tab={{ request()->get('active_tab', 'timeline') }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 transition-colors">
                    Next
                </a>
                @endif
            </div>
        </div>
    @endif
</div>