@extends('layouts.app')

@section('content')
<div class="flex-1 p-8 ml-72">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">All Completed Requests</h2>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-[#960106]">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Request Date</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Completion Date</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Item</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Department</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Lab Room</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Technician</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Remarks</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($completedRequests as $request)
                <tr class="{{ strlen($request->remarks) > 50 ? 'hover:bg-gray-50 cursor-pointer' : '' }}" 
                    {{ strlen($request->remarks) > 50 ? 'onclick=toggleRemarks('.$request->id.')' : '' }}>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($request->created_at)->format('M j, Y (g:i A)') }}</td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($request->updated_at)->format('M j, Y (g:i A)') }}</td>
                    <td class="px-6 py-4">{{ $request->equipment }}</td>
                    <td class="px-6 py-4">{{ $request->department }}</td>
                    <td class="px-6 py-4">{{ $request->office_room }}</td>
                    <td class="px-6 py-4">{{ $request->technician ? $request->technician->name : 'Not Assigned' }}</td>
                    <td class="px-6 py-4">
                        <span>{{ strlen($request->remarks) > 50 ? Str::limit($request->remarks, 50) : $request->remarks }}</span>
                    </td>
                </tr>
                @if(strlen($request->remarks) > 50)
                <tr id="remarks-{{ $request->id }}" class="hidden bg-gray-50">
                    <td colspan="7" class="px-6 py-4">
                        <div class="text-sm">
                            <span class="font-semibold">Full Remarks:</span>
                            <p class="mt-2 whitespace-pre-wrap">{{ $request->remarks }}</p>
                        </div>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        function toggleRemarks(requestId) {
            const remarksRow = document.getElementById(`remarks-${requestId}`);
            const allRemarksRows = document.querySelectorAll('[id^="remarks-"]');
            
            // Hide all other expanded remarks
            allRemarksRows.forEach(row => {
                if (row.id !== `remarks-${requestId}`) {
                    row.classList.add('hidden');
                }
            });

            // Toggle the clicked remarks
            remarksRow.classList.toggle('hidden');
        }
    </script>
</div>
@endsection