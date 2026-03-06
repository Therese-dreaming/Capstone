@extends('layouts.borrowing-app')

@section('content')
<div class="flex-1 bg-gradient-to-br from-gray-50 via-gray-50 to-red-50 min-h-screen" id="mainContent">
    <div class="max-w-7xl mx-auto px-6 py-6">
        <!-- Header -->
        <div class="mb-6 bg-gradient-to-r from-red-600 to-red-700 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="pattern-create" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                            <circle cx="20" cy="20" r="1" fill="white"/>
                        </pattern>
                    </defs>
                    <rect x="0" y="0" width="100%" height="100%" fill="url(#pattern-create)"/>
                </svg>
            </div>
            
            <div class="relative flex items-center gap-4">
                <div class="bg-white/20 p-3 rounded-lg backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Add New Asset</h1>
                    <p class="text-red-100 text-sm mt-0.5">Register a new asset for the borrowing system</p>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-green-800 font-medium text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Error Messages -->
        @if($errors->any())
            <div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <div class="flex-1">
                        <p class="text-red-800 font-semibold text-sm mb-1">Please correct the following errors:</p>
                        <ul class="list-disc list-inside text-red-700 space-y-1 text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Asset Form -->
        <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
            <form action="{{ route('borrowing.assets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="space-y-6">
                    <!-- Photo Upload Section -->
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 bg-gray-50 hover:border-red-400 transition-colors">
                        <label class="block text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">
                            Asset Photo (Optional)
                        </label>
                        <div class="flex flex-col items-center">
                            <!-- Image Preview -->
                            <div id="imagePreview" class="hidden mb-4">
                                <img id="previewImage" src="" alt="Preview" class="max-h-48 rounded-lg shadow-md">
                            </div>
                            <!-- Upload Button -->
                            <div class="text-center">
                                <label for="photo" class="cursor-pointer inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow-md transition-all">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Choose Photo
                                </label>
                                <input type="file" id="photo" name="photo" accept="image/*" class="hidden" onchange="previewImageFile(this)">
                                <p class="text-xs text-gray-500 mt-2">PNG, JPG, JPEG up to 5MB</p>
                                <button type="button" id="removeImage" class="hidden mt-2 text-sm text-red-600 hover:text-red-700 font-medium" onclick="clearImagePreview()">Remove Photo</button>
                            </div>
                        </div>
                        @error('photo')
                            <p class="mt-2 text-sm text-red-600 text-center">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Asset Name -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                Asset Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   placeholder="e.g., Dell Laptop, HP Projector"
                                   class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Serial Number -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                Serial Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="serial_number" value="{{ old('serial_number') }}" required
                                   placeholder="e.g., SN-12345678"
                                   class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('serial_number') border-red-500 @enderror">
                            @error('serial_number')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select name="category_id" required
                                    class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('category_id') border-red-500 @enderror">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Location -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                Location <span class="text-red-500">*</span>
                            </label>
                            <select name="location_id" required
                                    class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('location_id') border-red-500 @enderror">
                                <option value="">Select Location</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->building }} - {{ $location->floor }} - {{ $location->room_number }}
                                    </option>
                                @endforeach
                            </select>
                            @error('location_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Model -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                Model
                            </label>
                            <input type="text" name="model" value="{{ old('model') }}"
                                   placeholder="e.g., Latitude 5420, EB-X41"
                                   class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('model') border-red-500 @enderror">
                            @error('model')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Purchase Date -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                Purchase Date
                            </label>
                            <input type="date" name="purchase_date" value="{{ old('purchase_date') }}"
                                   class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('purchase_date') border-red-500 @enderror">
                            @error('purchase_date')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Purchase Price -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                Purchase Price (₱)
                            </label>
                            <input type="number" name="purchase_price" value="{{ old('purchase_price') }}" min="0" step="0.01"
                                   placeholder="0.00"
                                   class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('purchase_price') border-red-500 @enderror">
                            @error('purchase_price')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Specification -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                            Specification
                        </label>
                        <textarea name="specification" rows="4"
                                  placeholder="Enter detailed specifications (e.g., Intel Core i5, 8GB RAM, 256GB SSD)"
                                  class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('specification') border-red-500 @enderror">{{ old('specification') }}</textarea>
                        @error('specification')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                            Additional Notes
                        </label>
                        <textarea name="notes" rows="3"
                                  placeholder="Any additional notes about this asset..."
                                  class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Info Box -->
                    <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 p-4 rounded-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm text-red-800 font-semibold mb-0.5">Asset Status</p>
                                <p class="text-sm text-red-700">This asset will be set to <strong>"active"</strong> status and will be immediately available for borrowing.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('borrowing.assets.available') }}" 
                           class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all text-center">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Add Asset to System
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImageFile(input) {
    const preview = document.getElementById('imagePreview');
    const previewImage = document.getElementById('previewImage');
    const removeButton = document.getElementById('removeImage');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            preview.classList.remove('hidden');
            removeButton.classList.remove('hidden');
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

function clearImagePreview() {
    const input = document.getElementById('photo');
    const preview = document.getElementById('imagePreview');
    const previewImage = document.getElementById('previewImage');
    const removeButton = document.getElementById('removeImage');
    
    input.value = '';
    previewImage.src = '';
    preview.classList.add('hidden');
    removeButton.classList.add('hidden');
}
</script>
@endsection
