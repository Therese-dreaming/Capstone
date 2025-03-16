@extends('layouts.app')

@section('content')
<div class="flex-1 ml-80">
    <div class="p-6">
        <!-- Main Container -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <!-- Header Section -->
            <!-- Update the buttons in the header section -->
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">QR LIST</h1>
                <div class="flex space-x-3">
                    <button id="previewBtn" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                        Preview PDF
                    </button>
                    <button id="exportBtn" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                        Download PDF
                    </button>
                </div>
            </div>

            <!-- Table Section -->
            <div class="mt-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="w-10 px-6 py-3 text-left">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                            </th>
                            <th class="w-40 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">QR Code</th>
                            <th class="w-40 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Photo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serial Number</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assets as $asset)
                        <tr>
                            <td class="px-6 py-4">
                                <input type="checkbox" name="selected_items[]" value="{{ $asset->id }}" class="rounded border-gray-300">
                            </td>
                            <!-- Add this before the closing </div> of the main container -->
                            <!-- Image Modal -->
                            <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                                <div class="relative">
                                    <img id="enlargedImage" src="" alt="Enlarged Image" class="max-h-[80vh] max-w-[80vw] object-contain">
                                    <button onclick="closeImageModal()" 
                                            class="absolute -top-4 -right-4 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 transition-colors">
                                        <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Update the QR code and photo cells -->
                            <td class="px-6 py-4">
                                @if($asset->qr_code)
                                    <img src="{{ asset('storage/' . $asset->qr_code) }}" 
                                         alt="QR Code" 
                                         class="w-20 h-20 cursor-pointer hover:opacity-75 transition-opacity"
                                         onclick="openImageModal('{{ asset('storage/' . $asset->qr_code) }}')"
                                    >
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($asset->photo)
                                    <img src="{{ asset('storage/' . $asset->photo) }}" 
                                         alt="Asset Photo" 
                                         class="w-20 h-20 object-cover rounded cursor-pointer hover:opacity-75 transition-opacity"
                                         onclick="openImageModal('{{ asset('storage/' . $asset->photo) }}')"
                                    >
                                @else
                                    <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                        <span class="text-gray-500">No Photo</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $asset->name }}</td>
                            <td class="px-6 py-4">{{ $asset->serial_number }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add this after your table -->
<form id="exportForm" action="{{ route('qrcodes.export') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="selected_items" id="selectedItemsInput">
</form>

<!-- Add preview form -->
<form id="previewForm" action="{{ route('qrcodes.preview') }}" method="POST" target="_blank" class="hidden">
    @csrf
    <input type="hidden" name="selected_items" id="previewItemsInput">
</form>

<!-- Update script -->
<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.getElementsByName('selected_items[]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    document.getElementById('previewBtn').addEventListener('click', function() {
        const selectedItems = Array.from(document.getElementsByName('selected_items[]'))
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        if (selectedItems.length === 0) {
            alert('Please select items to preview');
            return;
        }

        document.getElementById('previewItemsInput').value = JSON.stringify(selectedItems);
        document.getElementById('previewForm').submit();
    });

    document.getElementById('exportBtn').addEventListener('click', function() {
        const selectedItems = Array.from(document.getElementsByName('selected_items[]'))
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        if (selectedItems.length === 0) {
            alert('Please select items to export');
            return;
        }

        document.getElementById('selectedItemsInput').value = JSON.stringify(selectedItems);
        document.getElementById('exportForm').submit();
    });

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
</script>
@endsection