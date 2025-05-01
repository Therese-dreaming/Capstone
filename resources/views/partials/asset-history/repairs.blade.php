<div class="mb-8">
    <div class="overflow-x-auto shadow-md rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Ticket No.</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Issue</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Technician</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Remarks</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse(($history['REPAIR'] ?? []) as $record)
                    @php
                        // Extract ticket number to find the repair request
                        preg_match('/Ticket: (REQ-\d{8}-\d{4})/', $record->remarks, $matches);
                        $ticketNo = $matches[1] ?? null;
                        
                        // Skip if repair request is cancelled
                        $repairRequest = $ticketNo ? \App\Models\RepairRequest::where('ticket_number', $ticketNo)->first() : null;
                        if ($repairRequest && $repairRequest->status === 'cancelled') continue;
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
                <!-- Empty state remains the same -->
                @endforelse
            </tbody>
        </table>
    </div>
</div>