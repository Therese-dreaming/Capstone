<div class="mb-8">
    <div class="overflow-x-auto shadow-md rounded-lg">
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
                @forelse(($history['REPAIR'] ?? []) as $record)
                    @php
                        // Extract ticket number to find the repair request
                        preg_match('/Ticket: (REQ-\d{8}-\d{4})/', $record->remarks, $matches);
                        $ticketNo = $matches[1] ?? null;
                        
                        // Get repair request
                        $repairRequest = $ticketNo ? \App\Models\RepairRequest::where('ticket_number', $ticketNo)->first() : null;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div>
                                <div class="font-medium">Requested: {{ $record->created_at->format('M d, Y') }}</div>
                                <div class="text-gray-600">Completed: {{ \Carbon\Carbon::parse($record->completed_at ?? $record->created_at)->format('M d, Y') }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @php
                                if (preg_match('/Ticket: (REQ-\d{8}-\d{4})/', $record->remarks, $matches)) {
                                    $ticketNo = $matches[1];
                                } else {
                                    $ticketNo = 'N/A';
                                }
                            @endphp
                            {{ $ticketNo }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @php
                                if (preg_match('/Issue: (.+?)\n/', $record->remarks . "\n", $matches)) {
                                    $issue = $matches[1];
                                } else {
                                    $issue = $record->old_value ?? 'N/A';
                                }
                            @endphp
                            {{ $issue }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $record->user->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($repairRequest)
                                @if($repairRequest->status === 'cancelled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Cancelled
                                    </span>
                                @elseif($repairRequest->status === 'pulled_out')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pulled Out
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    N/A
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @php
                                if (preg_match('/Remarks: (.+)$/', $record->remarks, $matches)) {
                                    $remarks = $matches[1];
                                } else {
                                    $remarks = $record->remarks ?? 'N/A';
                                }
                            @endphp
                            {{ $remarks }}
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
</div>