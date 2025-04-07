@extends('layouts.app')

@section('content')
<div class="flex-1 ml-80">
    <div class="p-6">
        <!-- Add success message section here -->
        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
            {{ session('error') }}
        </div>
        @endif
        <!-- Main Container -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">ALL ASSETS</h1>
                <!-- Add this in the header section where your other buttons are -->
                <div class="flex space-x-3">
                    <a href="{{ route('assets.create') }}" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Add New Asset
                    </a>
                    <a href="{{ route('reports.procurement-history') }}" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Procurement History
                    </a>
                    <a href="{{ route('reports.disposal-history') }}" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Disposal History
                    </a>
                    <button onclick="openFullList()" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Divider Line -->
            <div class="border-b-2 border-red-800 mb-6"></div>

            <!-- Asset List Preview -->
            <div class="relative overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Photo</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">QR Code</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serial Number</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assets as $asset)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($asset->photo)
                                <img src="{{ asset('storage/' . $asset->photo) }}" alt="Asset Photo" class="w-20 h-20 object-cover rounded cursor-pointer hover:opacity-75 transition-opacity" onclick="openImageModal('{{ asset('storage/' . $asset->photo) }}')">
                                @else
                                <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                    <span class="text-gray-500">No Photo</span>
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($asset->qr_code)
                                <img src="{{ asset('storage/' . $asset->qr_code) }}" alt="QR Code" class="w-20 h-20 cursor-pointer hover:opacity-75 transition-opacity" onclick="openImageModal('{{ asset('storage/' . $asset->qr_code) }}')">
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">{{ $asset->serial_number ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->name ?? '' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->category->name ?? '' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1.5 text-xs font-medium rounded-full inline-flex items-center justify-center min-w-[90px]
                                            @switch($asset->status)
                                                @case('UNDER REPAIR')
                                                    bg-yellow-100 text-yellow-800
                                                    @break
                                                @case('IN USE')
                                                    bg-green-100 text-green-800
                                                    @break
                                                @case('DISPOSED')
                                                    bg-red-100 text-red-800
                                                    @break
                                                @case('UPGRADE')
                                                    bg-blue-100 text-blue-800
                                                    @break
                                                @case('PENDING DEPLOYMENT')
                                                    bg-purple-100 text-purple-800
                                                    @break
                                                @default
                                                    bg-gray-100 text-gray-800
                                            @endswitch">
                                    {{ $asset->status ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($asset->purchase_price ?? 0, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->location ?? '' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex space-x-2">
                                    <a href="{{ route('reports.asset-history', $asset->id) }}" class="bg-blue-600 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-blue-700">
                                        History
                                    </a>
                                    <a href="{{ route('assets.edit', $asset->id) }}" class="bg-yellow-600 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-yellow-700">
                                        Edit
                                    </a>
                                    <button onclick="confirmDelete({{ $asset->id }})" class="bg-red-600 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-red-700">
                                        Delete
                                    </button>
                                    <button onclick="confirmDispose({{ $asset->id }})" class="bg-gray-600 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-gray-700">
                                        Dispose
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<!-- Image Modal -->
<!-- Move imageModal outside and adjust z-index -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center">
    <div class="relative" onclick="event.stopPropagation();">
        <img id="enlargedImage" src="" alt="Enlarged Image" class="max-h-[80vh] max-w-[80vw] object-contain">
        <button onclick="closeImageModal()" class="absolute -top-4 -right-4 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 transition-colors">
            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<!-- Update fullListModal z-index -->
<div id="fullListModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-6 border w-11/12 shadow-xl rounded-lg bg-white">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Complete Asset List</h2>
                <p class="text-sm text-gray-600 mt-1">Comprehensive view of all asset details</p>
            </div>
            <button onclick="closeFullList()" class="text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Table Container -->
        <div class="overflow-y-auto max-h-[70vh] rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <!-- Add to full list table header -->
                <thead class="bg-gray-50 sticky top-0 shadow-sm z-10">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">#</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">Photo</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">QR Code</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Asset Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Serial Number</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Purchase Price</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Location</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Model</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Specification</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Vendor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Purchase Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Warranty Period</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Lifespan (Yrs)</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Asset Life Remaining</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($assets as $asset)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($asset->photo)
                            <img src="{{ asset('storage/' . $asset->photo) }}" alt="Asset Photo" class="w-16 h-16 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity shadow-sm" onclick="openImageModal('{{ asset('storage/' . $asset->photo) }}')">
                            @else
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($asset->qr_code)
                            <img src="{{ asset('storage/' . $asset->qr_code) }}" alt="QR Code" class="w-16 h-16 cursor-pointer hover:opacity-75 transition-opacity shadow-sm rounded-lg" onclick="openImageModal('{{ asset('storage/' . $asset->qr_code) }}')">
                            @else
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->name ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-bold">{{ $asset->serial_number ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($asset->purchase_price ?? 0, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->category->name ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->location ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1.5 text-xs font-medium rounded-full inline-flex items-center justify-center min-w-[90px]
                                    @switch($asset->status)
                                        @case('UNDER REPAIR')
                                            bg-yellow-100 text-yellow-800
                                            @break
                                        @case('IN USE')
                                            bg-green-100 text-green-800
                                            @break
                                        @case('DISPOSED')
                                            bg-red-100 text-red-800
                                            @break
                                        @case('UPGRADE')
                                            bg-blue-100 text-blue-800
                                            @break
                                        @case('PENDING DEPLOYMENT')
                                            bg-purple-100 text-purple-800
                                            @break
                                        @default
                                            bg-gray-100 text-gray-800
                                    @endswitch">
                                {{ $asset->status ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->description ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->model ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->specification ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->vendor ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->purchase_date ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->warranty_period ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->lifespan ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->life_remaining ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Move the script section outside the fullListModal div and place it at the end of the content section -->
</div> <!-- end of fullListModal -->

<!-- Add these modals before the closing </div> of your main content -->

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Delete Asset</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Are you sure you want to delete this asset? This action cannot be undone.</p>
            </div>
            <div class="flex justify-center gap-4 mt-4">
                <button id="deleteConfirm" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                    Delete
                </button>
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Dispose Confirmation Modal -->
<div id="disposeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Dispose Asset</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">Are you sure you want to mark this asset as disposed?</p>
                <div class="text-left">
                    <label for="disposeReason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Disposal</label>
                    <textarea id="disposeReason" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500" rows="3" placeholder="Enter reason for disposal"></textarea>
                </div>
            </div>
            <div class="flex justify-center gap-4 mt-4">
                <button id="disposeConfirm" class="px-4 py-2 bg-yellow-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-300">
                    Dispose
                </button>
                <button onclick="closeDisposeModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Update the JavaScript for dispose confirmation -->
<script>
    function openFullList() {
        document.getElementById('fullListModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeFullList() {
        document.getElementById('fullListModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openImageModal(imageSrc) {
        event.stopPropagation(); // Add this line
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

    // Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        document.getElementById('fullListModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFullList();
            }
        });
    });

    let currentAssetId = null;

    function confirmDelete(assetId) {
        currentAssetId = assetId;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function confirmDispose(assetId) {
        currentAssetId = assetId;
        document.getElementById('disposeModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        currentAssetId = null;
    }

    function closeDisposeModal() {
        document.getElementById('disposeModal').classList.add('hidden');
        document.getElementById('disposeReason').value = ''; // Clear the reason field
        document.body.style.overflow = 'auto'; // Restore page scrolling
        currentAssetId = null;
    }

    document.getElementById('deleteConfirm').addEventListener('click', function() {
        if (currentAssetId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/assets/${currentAssetId}`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';

            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }
    });

    document.getElementById('disposeConfirm').addEventListener('click', function() {
        if (currentAssetId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/assets/${currentAssetId}/dispose`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;

            // Add disposal reason input
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'disposal_reason';
            reasonInput.value = document.getElementById('disposeReason').value;

            const redirectInput = document.createElement('input');
            redirectInput.type = 'hidden';
            redirectInput.name = 'redirect';
            redirectInput.value = '{{ route("reports.disposal-history") }}';

            form.appendChild(csrfInput);
            form.appendChild(reasonInput);  // Add the reason input to the form
            form.appendChild(redirectInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
</script>
@endsection
