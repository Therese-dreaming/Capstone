@extends('layouts.app')

@section('content')
<div class="flex-1 p-8 ml-72">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">MAINTENANCE HISTORY</h2>
        <div class="space-x-3">
            <button onclick="exportToPDF()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                Export to PDF
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-4 flex space-x-4">
            <div class="flex space-x-2 items-center">
                <input type="date" id="startDate" class="px-3 py-2 border border-gray-300 rounded-md">
                <span class="text-gray-500">to</span>
                <input type="date" id="endDate" class="px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <select id="labFilter" onchange="filterHistory()" class="px-3 py-2 border border-gray-300 rounded-md">
                <option value="">All Laboratories</option>
                @foreach(['401', '402', '403', '404', '405', '406'] as $lab)
                <option value="{{ $lab }}">Laboratory {{ $lab }}</option>
                @endforeach
            </select>
            <select id="statusFilter" onchange="filterHistory()" class="px-3 py-2 border border-gray-300 rounded-md">
                <option value="">All Status</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table id="maintenanceTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technician</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Time</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($maintenances as $maintenance)
                    <tr data-lab="{{ $maintenance->lab_number }}" data-status="{{ $maintenance->status }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            Laboratory {{ $maintenance->lab_number }}
                        </td>
                        <td class="px-6 py-4">
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
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $maintenance->technician->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $maintenance->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($maintenance->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $maintenance->actionBy ? $maintenance->actionBy->name : 'System' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $maintenance->status === 'completed' ? \Carbon\Carbon::parse($maintenance->completed_at)->format('M d, Y g:i A') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- PDF Preview Modal -->
    <div id="pdfPreviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-3/4 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Maintenance History Preview</h3>
                <button onclick="closePdfPreview()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="preview-content" style="max-height: 70vh; overflow-y: auto;">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="mt-4 flex justify-end space-x-3">
                <button onclick="closePdfPreview()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Cancel
                </button>
                <button onclick="downloadPDF()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Download PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function filterHistory() {
        const labFilter = document.getElementById('labFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const rows = document.querySelectorAll('#maintenanceTable tbody tr');

        rows.forEach(row => {
            const labMatch = !labFilter || row.dataset.lab === labFilter;
            const statusMatch = !statusFilter || row.dataset.status === statusFilter;
            
            // Date filtering
            let dateMatch = true;
            if (startDate || endDate) {
                const dateText = row.querySelector('td:first-child').textContent.trim();
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

            row.style.display = labMatch && statusMatch && dateMatch ? '' : 'none';
        });
    }

    // Add event listeners for date inputs
    document.getElementById('startDate').addEventListener('change', filterHistory);
    document.getElementById('endDate').addEventListener('change', filterHistory);

    function exportToPDF() {
        const modal = document.getElementById('pdfPreviewModal');
        const previewContent = modal.querySelector('.preview-content');

        // Get current filters
        const labFilter = document.getElementById('labFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (labFilter) params.append('lab', labFilter);
        if (statusFilter) params.append('status', statusFilter);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        // Show loading state
        previewContent.innerHTML = '<div class="text-center py-4">Loading preview...</div>';
        modal.classList.remove('hidden');

        // Fetch preview content with filters
        fetch('{{ route("maintenance.previewPDF") }}?' + params.toString())
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

    function closePdfPreview() {
        const modal = document.getElementById('pdfPreviewModal');
        const previewContent = modal.querySelector('.preview-content');
        modal.classList.add('hidden');
        previewContent.innerHTML = ''; // Clear the preview content
    }

    function downloadPDF() {
        const labFilter = document.getElementById('labFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (labFilter) params.append('lab', labFilter);
        if (statusFilter) params.append('status', statusFilter);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        // Append query parameters to the export URL
        window.location.href = "{{ route('maintenance.exportPDF') }}?" + params.toString();
        closePdfPreview();
    }

    function exportToPDF() {
        const modal = document.getElementById('pdfPreviewModal');
        const previewContent = modal.querySelector('.preview-content');
        
        // Get current filters
        const labFilter = document.getElementById('labFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (labFilter) params.append('lab', labFilter);
        if (statusFilter) params.append('status', statusFilter);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        // Show loading state
        previewContent.innerHTML = '<div class="text-center py-4">Loading preview...</div>';
        modal.classList.remove('hidden');

        // Fetch preview content with filters
        fetch('{{ route("maintenance.previewPDF") }}?' + params.toString())
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

</script>
@endsection
