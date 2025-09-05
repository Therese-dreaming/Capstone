@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Page Header with Background Design -->
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                        <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold">QR Code Generator</h1>
                        <p class="text-blue-100 text-sm md:text-base">Generate and export QR codes for your assets</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 md:gap-3">
                    <button id="previewBtn" class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-lg hover:bg-white/30 transition-all duration-200 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Preview PDF
                    </button>
                    <button id="exportBtn" class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition-all duration-200 flex items-center justify-center font-semibold">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Filter Section -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Filter Assets</h2>
                @if(request('date_from') || request('date_to'))
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                            </svg>
                            Filtered
                        </span>
                        <a href="{{ route('qr.list') }}" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 text-center flex items-center">
                            Clear filters
                        </a>
                    </div>
                @endif
            </div>
            
            <form method="GET" action="{{ route('qr.list') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input type="date" 
                           id="date_from" 
                           name="date_from" 
                           value="{{ request('date_from') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                </div>
                
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input type="date" 
                           id="date_to" 
                           name="date_to" 
                           value="{{ request('date_to') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full bg-red-800 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-all duration-200 flex items-center justify-center font-semibold">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                        </svg>
                        Apply Filter
                    </button>
                </div>
            </form>
            
            @if(request('date_from') || request('date_to'))
                <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-blue-800">
                            <strong>Showing assets:</strong> 
                            @if(request('date_from'))
                                from {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}
                            @endif
                            @if(request('date_from') && request('date_to'))
                                to
                            @endif
                            @if(request('date_to'))
                                {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                            @endif
                            <span class="ml-2 font-semibold">({{ $assets->count() }} assets found)</span>
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Assets Table Section -->
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Select Assets for QR Generation</h3>
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Select All</span>
                    </label>
                    <span class="text-sm text-gray-500" id="selectedCount">0 selected</span>
                </div>
            </div>

            @if($assets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="w-12 px-6 py-4 text-left">
                                    <input type="checkbox" id="selectAllHeader" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="w-20 px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QR Code</th>
                                <th class="w-20 px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($assets as $asset)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <input type="checkbox" name="selected_items[]" value="{{ $asset->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 asset-checkbox">
                                </td>
                                <td class="px-6 py-4">
                                    @if($asset->qr_code)
                                        <img src="{{ asset('storage/' . $asset->qr_code) }}" 
                                             alt="QR Code" 
                                             class="w-16 h-16 cursor-pointer hover:opacity-75 transition-opacity"
                                             onclick="openImageModal('{{ asset('storage/' . $asset->qr_code) }}')"
                                        >
                                    @else
                                        <div class="w-16 h-16 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center">
                                            <span class="text-gray-400 text-xs">No QR</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($asset->photo)
                                        <img src="{{ asset('storage/' . $asset->photo) }}" 
                                             alt="Asset Photo" 
                                             class="w-16 h-16 object-cover rounded cursor-pointer hover:opacity-75 transition-opacity"
                                             onclick="openImageModal('{{ asset('storage/' . $asset->photo) }}')"
                                        >
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                            <span class="text-gray-500 text-xs">No Photo</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $asset->name }}</div>
                                    @if($asset->category)
                                        <div class="text-sm text-gray-500">{{ $asset->category->name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-mono font-semibold text-gray-900">{{ $asset->serial_number }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($asset->purchase_date)->format('M d, Y') }}</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No assets found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request('date_from') || request('date_to'))
                            Try adjusting your date range filter.
                        @else
                            No assets are available for QR code generation.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <img id="enlargedImage" src="" alt="Enlarged Image" class="max-h-[80vh] max-w-full object-contain rounded-lg shadow-2xl">
        <button onclick="closeImageModal()" 
                class="absolute -top-4 -right-4 bg-white rounded-full p-3 shadow-lg hover:bg-gray-100 transition-colors duration-200">
            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<!-- Export and Preview Forms -->
<form id="exportForm" action="{{ route('qrcodes.export') }}" method="POST" class="hidden">
    @csrf
    @if(request('date_from'))
        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
    @endif
    @if(request('date_to'))
        <input type="hidden" name="date_to" value="{{ request('date_to') }}">
    @endif
    <input type="hidden" name="selected_items" id="selectedItemsInput">
</form>

<form id="previewForm" action="{{ route('qrcodes.preview') }}" method="POST" target="_blank" class="hidden">
    @csrf
    @if(request('date_from'))
        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
    @endif
    @if(request('date_to'))
        <input type="hidden" name="date_to" value="{{ request('date_to') }}">
    @endif
    <input type="hidden" name="selected_items" id="previewItemsInput">
</form>

<script>
    // Select All functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.getElementsByName('selected_items[]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });

    document.getElementById('selectAllHeader').addEventListener('change', function() {
        const checkboxes = document.getElementsByName('selected_items[]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        document.getElementById('selectAll').checked = this.checked;
        updateSelectedCount();
    });

    // Individual checkbox change
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('asset-checkbox')) {
            updateSelectedCount();
            updateSelectAllState();
        }
    });

    function updateSelectedCount() {
        const selectedItems = Array.from(document.getElementsByName('selected_items[]'))
            .filter(checkbox => checkbox.checked);
        document.getElementById('selectedCount').textContent = `${selectedItems.length} selected`;
    }

    function updateSelectAllState() {
        const checkboxes = document.getElementsByName('selected_items[]');
        const checkedBoxes = Array.from(checkboxes).filter(checkbox => checkbox.checked);
        const selectAll = document.getElementById('selectAll');
        const selectAllHeader = document.getElementById('selectAllHeader');
        
        if (checkedBoxes.length === 0) {
            selectAll.checked = false;
            selectAllHeader.checked = false;
        } else if (checkedBoxes.length === checkboxes.length) {
            selectAll.checked = true;
            selectAllHeader.checked = true;
        } else {
            selectAll.checked = false;
            selectAllHeader.checked = false;
        }
    }

    // Preview functionality
    document.getElementById('previewBtn').addEventListener('click', function() {
        const selectedItems = Array.from(document.getElementsByName('selected_items[]'))
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        if (selectedItems.length === 0) {
            showNotification('Please select items to preview', 'warning');
            return;
        }

        document.getElementById('previewItemsInput').value = JSON.stringify(selectedItems);
        document.getElementById('previewForm').submit();
    });

    // Export functionality
    document.getElementById('exportBtn').addEventListener('click', function() {
        const selectedItems = Array.from(document.getElementsByName('selected_items[]'))
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        if (selectedItems.length === 0) {
            showNotification('Please select items to export', 'warning');
            return;
        }

        document.getElementById('selectedItemsInput').value = JSON.stringify(selectedItems);
        document.getElementById('exportForm').submit();
    });

    // Image modal functionality
    function openImageModal(imageSrc) {
        const modal = document.getElementById('imageModal');
        const enlargedImage = document.getElementById('enlargedImage');
        enlargedImage.src = imageSrc;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside the image
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });

    // Notification function
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
            type === 'warning' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 
            type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' : 
            'bg-blue-100 text-blue-800 border border-blue-200'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                ${message}
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Initialize selected count
    updateSelectedCount();
</script>
@endsection