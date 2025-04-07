<div class="mb-8">
    <div class="overflow-x-auto shadow-md rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Details</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Performed By</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse(($history['MAINTENANCE'] ?? []) as $record)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $record->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-3 py-1.5 text-xs font-medium rounded-full inline-flex items-center justify-center min-w-[90px] bg-blue-100 text-blue-800">
                            Maintenance
                        </span>
                    </td>
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $record->user->name }}</td>
                </tr>
                @empty
                @endforelse

                @foreach(($history['REPAIR'] ?? []) as $record)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $record->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-3 py-1.5 text-xs font-medium rounded-full inline-flex items-center justify-center min-w-[90px] bg-yellow-100 text-yellow-800">
                            Repair
                        </span>
                    </td>
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $record->user->name }}</td>
                </tr>
                @endforeach

                @if(empty($history['MAINTENANCE']) && empty($history['REPAIR']))
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm bg-gray-50">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>No maintenance or repair records found</span>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>