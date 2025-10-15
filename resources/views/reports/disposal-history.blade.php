@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 transition-all duration-300" id="mainContent">
    <div class="max-w-7xl mx-auto">
        <!-- Main Container -->
        <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 md:gap-0">
                <div class="flex items-center">
                    <div class="bg-red-100 p-3 rounded-full mr-4">
                        <svg class="w-8 h-8 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Disposal History Report</h1>
                        <p class="text-gray-600 text-sm md:text-base">Total Assets Disposed: {{ $disposedAssets->count() }}</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 w-full sm:w-auto items-start sm:items-center">
                    <!-- Date Filter Form -->
                    <form action="{{ route('reports.disposal-history') }}" method="GET" class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 w-full sm:w-auto" id="dateFilterForm">
                        <div class="flex items-center space-x-2 w-full sm:w-auto">
                            <label for="start_date" class="text-sm font-medium text-gray-600 flex-shrink-0">From:</label>
                            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" 
                                onchange="this.form.submit()"
                                class="form-input h-10 w-full md:w-auto px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                        </div>
                        <div class="flex items-center space-x-2 w-full sm:w-auto">
                            <label for="end_date" class="text-sm font-medium text-gray-600 flex-shrink-0">To:</label>
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                                onchange="this.form.submit()"
                                class="form-input h-10 w-full md:w-auto px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                        </div>
                        @if(request('start_date') || request('end_date'))
                            <a href="{{ route('reports.disposal-history') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 flex items-center justify-center w-full sm:w-auto text-sm transition-colors">
                                Reset
                            </a>
                        @endif
                    </form>
                    <button onclick="openSignatureModal()" class="bg-red-800 text-white px-6 py-3 rounded-lg hover:bg-red-700 flex items-center justify-center sm:justify-start w-full sm:w-auto text-sm font-medium transition-colors duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span class="hidden sm:inline">Preview PDF</span>
                        <span class="sm:hidden">Preview</span>
                    </button>
                </div>
            </div>

            <!-- Date Range Info (if filtered) -->
            @if(request('start_date') || request('end_date'))
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl text-blue-800 text-center md:text-left">
                <div class="flex items-center justify-center md:justify-start">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">
                        Showing results from 
                        {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') : 'the beginning' }}
                        to
                        {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') : 'present' }}
                    </span>
                </div>
            </div>
            @endif

            <!-- Summary Card -->
            <div class="mb-8">
                <div class="bg-red-800 text-white rounded-xl shadow-lg p-6 md:p-8">
                    <div class="flex items-center mb-4">
                        <div class="bg-white/20 p-3 rounded-full mr-4 backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold">Disposal Summary</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
                            <div class="text-3xl md:text-4xl font-bold mb-2">{{ $disposedAssets->count() }}</div>
                            <p class="text-red-100 text-sm md:text-base font-medium">Total Assets Disposed</p>
                        </div>
                        <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm sm:text-right">
                            <div class="text-3xl md:text-4xl font-bold mb-2">₱{{ number_format($disposedAssets->sum('purchase_price'), 2) }}</div>
                            <p class="text-red-100 text-sm md:text-base font-medium">Total Value Lost</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assets Table (Desktop View) -->
            <div class="overflow-x-auto hidden md:block">
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2-2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Disposed Assets Details
                    </h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white rounded-lg">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Name</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disposal Date</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disposal Reason</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Price</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($disposedAssets as $asset)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $asset->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <a href="{{ route('assets.index', ['search' => $asset->serial_number]) }}" class="font-bold text-red-600 hover:underline">{{ $asset->serial_number }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->category->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ \Carbon\Carbon::parse($asset->disposal_date)->format('M d, Y') }}</span>
                                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($asset->disposal_date)->format('h:iA') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->disposal_reason }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">₱{{ number_format($asset->purchase_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination for Desktop -->
            <div class="mt-6 hidden md:block">
                {{ $disposedAssets->appends(request()->query())->links() }}
            </div>

            <!-- Assets List (Mobile View) -->
            <div class="grid grid-cols-1 gap-4 md:hidden">
                @foreach($disposedAssets as $asset)
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-800 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-start justify-between mb-3">
                        <div class="font-bold text-lg text-gray-900">{{ $asset->name }}</div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">₱{{ number_format($asset->purchase_price, 2) }}</div>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <span class="text-gray-600"><strong>Category:</strong> {{ $asset->category->name }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <span class="text-gray-600"><strong>Serial Number:</strong> <a href="{{ route('assets.index', ['search' => $asset->serial_number]) }}" class="font-bold text-red-600 hover:underline">{{ $asset->serial_number }}</a></span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-gray-600"><strong>Disposal Date:</strong> {{ \Carbon\Carbon::parse($asset->disposal_date)->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-gray-600"><strong>Time:</strong> {{ \Carbon\Carbon::parse($asset->disposal_date)->format('h:iA') }}</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-4 h-4 text-red-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-gray-600"><strong>Reason:</strong> {{ $asset->disposal_reason }}</span>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Total Purchase Price Summary for Mobile -->
                <div class="bg-gray-50 rounded-xl shadow-lg p-6 flex justify-between items-center mt-2">
                    <div class="text-lg font-bold text-gray-900">Total Value of Disposed Assets</div>
                    <div class="text-lg font-bold text-red-800">₱{{ number_format($disposedAssets->sum('purchase_price'), 2) }}</div>
                </div>
            </div>

            <!-- Pagination for Mobile -->
            <div class="mt-6 md:hidden">
                {{ $disposedAssets->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    // Signature functionality
    let signatureCounter = 0;
    let currentFilters = {};

    function openSignatureModal() {
        // Store current filters
        currentFilters = {
            start_date: document.getElementById('start_date').value,
            end_date: document.getElementById('end_date').value
        };

        // Clear existing entries and add one default entry
        document.getElementById('signatureEntries').innerHTML = '';
        signatureCounter = 0;
        addSignatureEntry();
        
        // Show modal
        document.getElementById('signatureModal').classList.remove('hidden');
        document.getElementById('signatureModal').classList.add('flex');
    }

    function closeSignatureModal() {
        document.getElementById('signatureModal').classList.add('hidden');
        document.getElementById('signatureModal').classList.remove('flex');
    }

    function addSignatureEntry() {
        signatureCounter++;
        const entryId = `signature-${signatureCounter}`;
        
        const entryHtml = `
            <div class="border border-gray-200 rounded-lg p-4" id="${entryId}">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-medium text-gray-900">Signature ${signatureCounter}</h4>
                    <button onclick="removeSignatureEntry('${entryId}')" class="text-red-600 hover:text-red-800" title="Remove">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
                
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Label</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500" 
                           placeholder="e.g., Checked by, Supervised by, Approved by" 
                           id="${entryId}-label">
                </div>
                
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500" 
                           placeholder="Enter name" 
                           id="${entryId}-name">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Signature</label>
                    <div class="border border-gray-300 rounded-md">
                        <canvas id="${entryId}-canvas" width="300" height="120" class="block w-full cursor-crosshair"></canvas>
                    </div>
                    <div class="flex justify-end mt-2">
                        <button onclick="clearSignature('${entryId}-canvas')" class="text-sm text-gray-600 hover:text-gray-800">
                            Clear Signature
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('signatureEntries').insertAdjacentHTML('beforeend', entryHtml);
        initializeSignaturePad(`${entryId}-canvas`);
    }

    function removeSignatureEntry(entryId) {
        const entry = document.getElementById(entryId);
        if (entry) {
            entry.remove();
        }
        
        // If no entries left, add one default entry
        if (document.getElementById('signatureEntries').children.length === 0) {
            addSignatureEntry();
        }
    }

    function initializeSignaturePad(canvasId) {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;

        // Set canvas background to white
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Set drawing styles
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        function getMousePos(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            
            return {
                x: (e.clientX - rect.left) * scaleX,
                y: (e.clientY - rect.top) * scaleY
            };
        }

        function getTouchPos(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            
            return {
                x: (e.touches[0].clientX - rect.left) * scaleX,
                y: (e.touches[0].clientY - rect.top) * scaleY
            };
        }

        // Mouse events
        canvas.addEventListener('mousedown', (e) => {
            isDrawing = true;
            const pos = getMousePos(e);
            lastX = pos.x;
            lastY = pos.y;
        });

        canvas.addEventListener('mousemove', (e) => {
            if (!isDrawing) return;
            const pos = getMousePos(e);
            
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            
            lastX = pos.x;
            lastY = pos.y;
        });

        canvas.addEventListener('mouseup', () => isDrawing = false);
        canvas.addEventListener('mouseout', () => isDrawing = false);

        // Touch events for mobile
        canvas.addEventListener('touchstart', (e) => {
            e.preventDefault();
            isDrawing = true;
            const pos = getTouchPos(e);
            lastX = pos.x;
            lastY = pos.y;
        });

        canvas.addEventListener('touchmove', (e) => {
            e.preventDefault();
            if (!isDrawing) return;
            const pos = getTouchPos(e);
            
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            
            lastX = pos.x;
            lastY = pos.y;
        });

        canvas.addEventListener('touchend', (e) => {
            e.preventDefault();
            isDrawing = false;
        });
    }

    function clearSignature(canvasId) {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
    }

    function generatePDFWithSignatures() {
        // Collect all signature data
        const signatures = [];
        const entries = document.getElementById('signatureEntries').children;
        
        for (let i = 0; i < entries.length; i++) {
            const entry = entries[i];
            const entryId = entry.id;
            const label = document.getElementById(`${entryId}-label`).value.trim();
            const name = document.getElementById(`${entryId}-name`).value.trim();
            const canvas = document.getElementById(`${entryId}-canvas`);
            
            if (label && name) {
                signatures.push({
                    label: label,
                    name: name,
                    signature: canvas.toDataURL('image/png')
                });
            }
        }

        // Create a form to submit via POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('reports.disposal-history.export-pdf') }}';
        form.target = '_blank';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Add filter parameters
        if (currentFilters.start_date) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'start_date';
            input.value = currentFilters.start_date;
            form.appendChild(input);
        }
        
        if (currentFilters.end_date) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'end_date';
            input.value = currentFilters.end_date;
            form.appendChild(input);
        }
        
        // Add signatures as JSON
        if (signatures.length > 0) {
            const sigInput = document.createElement('input');
            sigInput.type = 'hidden';
            sigInput.name = 'signatures';
            sigInput.value = JSON.stringify(signatures);
            form.appendChild(sigInput);
        }
        
        // Submit form
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
        
        // Close modal
        closeSignatureModal();
    }
</script>

<!-- Signature Modal -->
<div id="signatureModal" 
     class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4"
     style="z-index: 70;">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add Signatures for PDF</h3>
                <button onclick="closeSignatureModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div id="signatureEntries" class="space-y-4">
                <!-- Signature entries will be added here dynamically -->
            </div>
            
            <div class="flex justify-between items-center mt-6">
                <button onclick="addSignatureEntry()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Signature
                </button>
                
                <div class="flex space-x-3">
                    <button onclick="closeSignatureModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Cancel
                    </button>
                    <button onclick="generatePDFWithSignatures()" class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Generate PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        /* Hide navigation elements and buttons */
        aside.fixed,
        nav.bg-white,
        .sidebar-nav,
        header,
        .header,
        #header,
        [x-data],
        button,
        .print-hide,
        .flex.flex-col.md\:flex-row.justify-between.items-start.md\:items-center.mb-8.gap-4.md\:gap-0 > div:last-child, /* Hide button group container */
        .flex.flex-col.md\:flex-row.justify-between.items-start.md\:items-center.mb-8.gap-4.md\:gap-0, /* Hide header section */
        .grid.grid-cols-1.gap-4.md\:hidden, /* Hide mobile cards */
        form { /* Hide filter form */
            display: none !important;
        }

        /* Hide summary card */
        .mb-8:has(.bg-red-800) {
            display: none !important;
        }

        /* Ensure main content area is visible and uses full width */
        .flex-1.p-4,
        .flex-1.p-8 {
             padding: 0 !important;
        }

        /* Remove left margin from main content added for sidebar */
        .md\:ml-80 {
            margin-left: 0 !important;
        }

        /* Ensure container is visible and uses full width */
        .max-w-7xl, .max-w-full {
            max-width: 100% !important;
            width: 100% !important;
        }

        /* Reset layout */
        body, html {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background: white !important;
        }

        /* Style the title */
        h1 {
            text-align: center !important;
            margin-bottom: 20px;
            font-size: 18pt;
            width: 100% !important;
            display: block !important;
        }

        /* Show total assets count in print */
        .text-gray-600.text-sm.md\:text-base {
            text-align: center !important;
            margin-bottom: 20px;
            font-size: 10pt;
            display: block !important;
        }

        /* Show date range in print if filtered */
        .mb-6.p-4.bg-blue-50.border.border-blue-200.rounded-xl.text-blue-800.text-center.md\:text-left {
            display: block !important;
            text-align: center !important;
            margin-bottom: 15px;
            background-color: white !important;
            border: 1px solid #000 !important;
            color: black !important;
        }

        /* Ensure the table is visible and styled for print */
        .overflow-x-auto.hidden.md\:block {
            display: block !important;
            overflow-x: visible !important; /* Ensure table is not scrollable in print */
        }

        .bg-gray-50.rounded-xl.p-6.mb-6 {
            background-color: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        table {
            width: 100% !important;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: auto !important; /* Allow columns to size naturally */
        }

        th {
            background-color: #f3f4f6;
            color: #6b7280;
            font-size: 9pt !important;
            font-weight: 600;
            text-transform: uppercase;
            padding: 8px !important;
            text-align: left;
            white-space: normal !important;
        }

        td {
            padding: 8px !important;
            font-size: 9pt !important;
            border-bottom: 1px solid #e5e7eb;
            white-space: normal !important;
        }

        /* Ensure proper page breaks */
        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        /* Remove color-adjust for backgrounds/colors */
        thead th {
            -webkit-print-color-adjust: unset !important;
            print-color-adjust: unset !important;
        }

        /* Remove status colors in print */
        .px-2.inline-flex {
            background-color: transparent !important;
            color: black !important;
        }
    }
</style>
@endsection