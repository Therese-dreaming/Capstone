@extends('layouts.app')

@section('content')
<div class="flex-1 p-8 ml-72">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">All Completed Requests</h2>
        <div class="space-x-3">
            <button onclick="exportToPDF()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                Export to PDF
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-4 flex items-center gap-4">
            <div class="flex flex-col gap-2">
                <select id="dateFilterType" onchange="filterHistory()" class="h-9 px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                    <option value="request">Filter by Request Date</option>
                    <option value="completion">Filter by Completion Date</option>
                </select>
                <div class="flex items-center gap-2">
                    <input type="date" id="startDate" class="h-9 px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                    <span class="text-sm text-gray-500">to</span>
                    <input type="date" id="endDate" class="h-9 px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                </div>
            </div>
            <div class="flex items-center gap-4 ml-4">
                <select id="departmentFilter" onchange="filterHistory()" class="h-9 w-48 px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Departments</option>
                    @foreach($completedRequests->pluck('department')->unique() as $department)
                    @if($department)
                    <option value="{{ $department }}">{{ $department }}</option>
                    @endif
                    @endforeach
                </select>
                <select id="labRoomFilter" onchange="filterHistory()" class="h-9 w-48 px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Lab Rooms</option>
                    @foreach($completedRequests->pluck('office_room')->unique() as $room)
                    @if($room)
                    <option value="{{ $room }}">{{ $room }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="repairTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#960106]">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Request Date</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Completion Date</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Item</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Ticket No.</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Department</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Lab Room</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Technician</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-white">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($completedRequests as $request)
                    <tr data-department="{{ $request->department }}" class="{{ strlen($request->remarks) > 50 ? 'hover:bg-gray-50 cursor-pointer' : '' }}" {{ strlen($request->remarks) > 50 ? 'onclick=toggleRemarks('.$request->id.')' : '' }}>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($request->created_at)->format('M j, Y (g:i A)') }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($request->completed_at)->format('M j, Y (g:i A)') }}</td>
                        <td class="px-6 py-4">{{ $request->equipment }}</td>
                        <td class="px-6 py-4">{{ $request->ticket_number }}</td>
                        <td class="px-6 py-4">{{ $request->department }}</td>
                        <td class="px-6 py-4">{{ $request->office_room }}</td>
                        <td class="px-6 py-4">{{ $request->technician ? $request->technician->name : 'Not Assigned' }}</td>
                        <td class="px-6 py-4">
                            <span>{{ strlen($request->remarks) > 50 ? Str::limit($request->remarks, 50) : $request->remarks }}</span>
                        </td>
                    </tr>
                    @if(strlen($request->remarks) > 50)
                    <tr id="remarks-{{ $request->id }}" class="hidden bg-gray-50">
                        <td colspan="8" class="px-6 py-4">
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
    </div>

    <!-- PDF Preview Modal -->
    <div id="pdfPreviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-4 border w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-medium">Preview</h3>
                <button onclick="closePdfPreview()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="preview-content" style="max-height: 60vh; overflow-y: auto;">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="mt-3 flex justify-end space-x-2">
                <button onclick="closePdfPreview()" class="px-3 py-1.5 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Cancel
                </button>
                <button onclick="downloadPDF()" class="px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Download
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function filterHistory() {
        const departmentFilter = document.getElementById('departmentFilter').value;
        const labRoomFilter = document.getElementById('labRoomFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const dateFilterType = document.getElementById('dateFilterType').value;
        const rows = document.querySelectorAll('#repairTable tbody tr:not([id^="remarks-"])');

        rows.forEach(row => {
            const departmentMatch = !departmentFilter || row.dataset.department === departmentFilter;
            const labRoomMatch = !labRoomFilter || row.querySelector('td:nth-child(6)').textContent.trim() === labRoomFilter;

            // Date filtering
            let dateMatch = true;
            if (startDate || endDate) {
                const columnIndex = dateFilterType === 'request' ? 1 : 2;
                const dateText = row.querySelector(`td:nth-child(${columnIndex})`).textContent.trim();
                const cellDate = new Date(dateText);
                const start = startDate ? new Date(startDate + 'T00:00:00') : null;
                const end = endDate ? new Date(endDate + 'T23:59:59') : null;

                if (start && end) {
                    dateMatch = cellDate >= start && cellDate <= end;
                } else if (start) {
                    dateMatch = cellDate >= start;
                } else if (end) {
                    dateMatch = cellDate <= end;
                }
            }

            row.style.display = departmentMatch && labRoomMatch && dateMatch ? '' : 'none';

            // Hide associated remarks row if main row is hidden
            const remarksId = row.onclick ? row.onclick.toString().match(/toggleRemarks\((\d+)\)/) ?.[1] : null;
            if (remarksId) {
                const remarksRow = document.getElementById(`remarks-${remarksId}`);
                if (remarksRow) {
                    remarksRow.style.display = 'none';
                }
            }
        });
    }

    // Add event listeners
        document.getElementById('dateFilterType').addEventListener('change', filterHistory);
        document.getElementById('startDate').addEventListener('change', filterHistory);
        document.getElementById('endDate').addEventListener('change', filterHistory);

    // Keep only one exportToPDF function and update it
    function exportToPDF() {
        const modal = document.getElementById('pdfPreviewModal');
        const previewContent = modal.querySelector('.preview-content');
        
        // Get current filters
        const departmentFilter = document.getElementById('departmentFilter').value;
        const labRoomFilter = document.getElementById('labRoomFilter').value;
        const dateFilterType = document.getElementById('dateFilterType').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (departmentFilter) params.append('department', departmentFilter);
        if (labRoomFilter) params.append('lab_room', labRoomFilter);
        if (dateFilterType) params.append('date_filter_type', dateFilterType);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        // Show loading state
        previewContent.innerHTML = '<div class="text-center py-4">Loading preview...</div>';
        modal.classList.remove('hidden');

        // Fetch preview content with filters
        fetch(`{{ route('repair.previewPDF') }}?${params.toString()}`)
            .then(response => response.text())
            .then(html => {
                const iframe = document.createElement('iframe');
                iframe.style.width = '100%';
                iframe.style.height = '70vh';
                iframe.style.border = 'none';
                
                previewContent.innerHTML = '';
                previewContent.appendChild(iframe);
                
                const iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
                iframeDocument.open();
                iframeDocument.write(html);
                iframeDocument.close();
            })
            .catch(error => {
                previewContent.innerHTML = '<div class="text-center py-4 text-red-600">Error loading preview</div>';
            });
    }

    function downloadPDF() {
        const departmentFilter = document.getElementById('departmentFilter').value;
        const labRoomFilter = document.getElementById('labRoomFilter').value;
        const dateFilterType = document.getElementById('dateFilterType').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (departmentFilter) params.append('department', departmentFilter);
        if (labRoomFilter) params.append('lab_room', labRoomFilter);
        if (dateFilterType) params.append('date_filter_type', dateFilterType);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        // Redirect to download URL
        window.location.href = `{{ route('repair.exportPDF') }}?${params.toString()}`;
        closePdfPreview();
    }

    function closePdfPreview() {
        const modal = document.getElementById('pdfPreviewModal');
        modal.classList.add('hidden');
        modal.querySelector('.preview-content').innerHTML = '';
    }

    function downloadPDF() {
        const departmentFilter = document.getElementById('departmentFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (departmentFilter) params.append('department', departmentFilter);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        // Redirect to download URL
        window.location.href = `{{ route('repair.exportPDF') }}?${params.toString()}`;
        closePdfPreview();
    }

</script>
@endsection
