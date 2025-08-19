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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Edit Asset</h1>
                        <p class="text-red-100 text-sm md:text-lg">Update the information for this asset</p>
                    </div>
                </div>
                <a href="{{ route('assets.index') }}" class="inline-flex items-center px-4 py-2 bg-white/20 text-white font-medium rounded-lg hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 focus:ring-offset-2 focus:ring-offset-red-800 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                    </svg>
                    Back to Assets
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6 max-w-6xl mx-auto">
        <form action="{{ route('assets.update', $asset->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <form action="{{ route('assets.update', $asset->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">Please correct the following errors:</span>
                    </div>
                    <ul class="ml-4 list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Basic Information Section -->
                        <div class="bg-gray-50 p-6 rounded-xl">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Basic Information
                            </h3>

                            <!-- Asset Name field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="name">
                                    Asset Name
                                </label>
                                <input type="text" name="name" id="name" 
                                    value="{{ old('name', $asset->name) }}" 
                                    class="w-full px-4 py-3 border @error('name') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Category field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="category_id">
                                    Category
                                </label>
                                <select name="category_id" id="category_id" 
                                    class="w-full px-4 py-3 border @error('category_id') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $asset->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Location & Status Section -->
                        <div class="bg-gray-50 p-6 rounded-xl">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Location & Status
                            </h3>

                            <!-- Location field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="location_id">
                                    Location
                                </label>
                                <select name="location_id" id="location_id" 
                                        class="w-full px-4 py-3 border @error('location_id') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                                    <option value="">Select Location</option>
                                    @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ $asset->location_id == $location->id ? 'selected' : '' }}>
                                        {{ $location->full_location }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('location_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">If your location is not listed, please contact the administrator to add it.</p>
                            </div>

                            <!-- Status field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="status">
                                    Status
                                </label>
                                <select name="status" id="status" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200" 
                                        {{ $asset->status == 'UNDER REPAIR' ? 'disabled' : '' }}>
                                    @php
                                        $statusOptions = ['IN USE', 'PULLED OUT'];
                                        if ($asset->status == 'UNDER REPAIR') {
                                            $statusOptions[] = 'UNDER REPAIR';
                                        }
                                    @endphp
                                    @foreach($statusOptions as $statusOption)
                                    <option value="{{ $statusOption }}" {{ $asset->status == $statusOption ? 'selected' : '' }}>
                                        {{ $statusOption }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Purchase Details Section -->
                        <div class="bg-gray-50 p-6 rounded-xl">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                Purchase Details
                            </h3>

                            <!-- Purchase Price field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="purchase_price">
                                    Purchase Price
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-600">₱</span>
                                    <input type="number" step="0.01" name="purchase_price" id="purchase_price" 
                                           value="{{ old('purchase_price', $asset->purchase_price) }}" 
                                           class="w-full pl-8 px-4 py-3 border @error('purchase_price') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                                </div>
                            </div>

                            <!-- Purchase Date field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="purchase_date">
                                    Purchase Date
                                </label>
                                <input type="date" name="purchase_date" id="purchase_date" 
                                       value="{{ old('purchase_date', $asset->purchase_date) }}" 
                                       class="w-full px-4 py-3 border @error('purchase_date') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                            </div>

                            <!-- Vendor field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="vendor">
                                    Vendor
                                </label>
                                <div class="border border-gray-300 rounded-lg bg-gray-50 p-4">
                                    <!-- Add New Vendor Input -->
                                    <div class="mb-4 flex space-x-2">
                                        <div class="flex-1 relative">
                                            <input type="text" id="newVendorName" 
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white transition-colors duration-200" 
                                                   placeholder="Enter new vendor name">
                                            <!-- Autocomplete dropdown -->
                                            <div id="vendorAutocomplete" class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden">
                                                <!-- Suggestions will be populated here -->
                                            </div>
                                        </div>
                                        <button type="button" onclick="addNewVendor(event)" 
                                                class="px-4 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-md">
                                            Add Vendor
                                        </button>
                                    </div>
                                    <select name="vendor_id" 
                                            class="w-full px-4 py-3 border @error('vendor_id') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                                        <option value="">Select Vendor</option>
                                        @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ $asset->vendor_id == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('vendor_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Technical Details Section -->
                        <div class="bg-gray-50 p-6 rounded-xl">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                Technical Details
                            </h3>

                            <!-- Serial Number field (Read-only) -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="serial_number">
                                    Serial Number
                                </label>
                                <div class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                                    {{ $asset->serial_number }}
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Serial numbers are automatically generated and cannot be modified</p>
                            </div>

                            <!-- Model field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="model">
                                    Model
                                </label>
                                <input type="text" name="model" id="model" 
                                       value="{{ old('model', $asset->model) }}" 
                                       class="w-full px-4 py-3 border @error('model') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                            </div>

                            <!-- Specification field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="specification">
                                    Specification
                                </label>
                                <textarea name="specification" id="specification" rows="3" 
                                          class="w-full px-4 py-3 border @error('specification') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">{{ old('specification', $asset->specification) }}</textarea>
                            </div>
                        </div>

                        <!-- Warranty & Lifespan Section -->
                        <div class="bg-gray-50 p-6 rounded-xl">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Warranty & Lifespan
                            </h3>

                            <!-- Warranty Period field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="warranty_period">
                                    Warranty Period
                                </label>
                                <input type="date" name="warranty_period" id="warranty_period" 
                                       value="{{ old('warranty_period', $asset->warranty_period) }}" 
                                       class="w-full px-4 py-3 border @error('warranty_period') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Acquisition Document Upload -->
            <div class="bg-gray-50 p-6 rounded-xl mt-8">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Acquisition Document
                </h3>
                @if($asset->acquisition_document)
                    <div class="mb-4">
                        <a href="{{ asset('storage/' . $asset->acquisition_document) }}" target="_blank" 
                           class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            View Current Document
                        </a>
                    </div>
                @endif
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-gray-400 transition-colors duration-200" id="acquisitionDropzone">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="acquisition_document" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                                <span>Upload a file</span>
                                <input id="acquisition_document" name="acquisition_document" type="file" class="sr-only" accept="image/*,application/pdf" max="2048" onchange="validateAcquisitionFileSize(this)">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF, PDF up to 2MB</p>
                    </div>
                </div>
                <!-- Acquisition Document Preview -->
                <div class="mt-4 hidden" id="acquisitionPreviewContainer">
                    <img id="acquisitionPreview" src="#" alt="Acquisition Document Preview" class="max-w-full h-40 object-cover rounded-lg shadow">
                    <button type="button" id="removeAcquisitionButton" class="mt-2 text-red-600 hover:text-red-800 text-sm">Remove Document</button>
                </div>
                <p class="text-xs text-red-500 mt-1 hidden" id="acquisitionFileError"></p>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-6 border-t border-gray-200 justify-end">
                <a href="{{ route('assets.index') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center justify-center px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Update Asset
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('locationSelect').addEventListener('change', function() {
        const otherLocation = document.getElementById('otherLocation');
        const locationValue = this.value;

        if (locationValue === 'others') {
            otherLocation.classList.remove('hidden');
            otherLocation.required = true;
            otherLocation.value = '';
            document.querySelector('input[name="location"]').value = '';
        } else {
            otherLocation.classList.add('hidden');
            otherLocation.required = false;
            otherLocation.value = '';
            document.querySelector('input[name="location"]').value = locationValue;
        }
    });

    // Vendor autocomplete functionality
    let vendorAutocompleteTimeout;

    const vendorInput = document.getElementById('newVendorName');
    const vendorAutocomplete = document.getElementById('vendorAutocomplete');
    const vendorSelect = document.querySelector('select[name="vendor_id"]');

    vendorInput.addEventListener('input', function() {
        clearTimeout(vendorAutocompleteTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            hideVendorAutocomplete();
            return;
        }

        vendorAutocompleteTimeout = setTimeout(() => {
            fetch(`/vendors/search?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    displayVendorSuggestions(data);
                })
                .catch(error => {
                    console.error('Error fetching vendors:', error);
                });
        }, 300);
    });

    // Keyboard navigation
    vendorInput.addEventListener('keydown', function(e) {
        const suggestions = vendorAutocomplete.querySelectorAll('.suggestion-item');
        const activeSuggestion = vendorAutocomplete.querySelector('.suggestion-item.active');

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (!activeSuggestion) {
                suggestions[0]?.classList.add('active');
            } else {
                const nextSuggestion = activeSuggestion.nextElementSibling;
                if (nextSuggestion) {
                    activeSuggestion.classList.remove('active');
                    nextSuggestion.classList.add('active');
                }
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (activeSuggestion) {
                const prevSuggestion = activeSuggestion.previousElementSibling;
                if (prevSuggestion) {
                    activeSuggestion.classList.remove('active');
                    prevSuggestion.classList.add('active');
                } else {
                    activeSuggestion.classList.remove('active');
                }
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeSuggestion) {
                selectVendor(activeSuggestion.dataset.id, activeSuggestion.dataset.name);
            }
        } else if (e.key === 'Escape') {
            hideVendorAutocomplete();
        }
    });

    // Click outside to hide
    document.addEventListener('click', function(e) {
        if (!vendorInput.contains(e.target) && !vendorAutocomplete.contains(e.target)) {
            hideVendorAutocomplete();
        }
    });

    function displayVendorSuggestions(vendors) {
        vendorAutocomplete.innerHTML = '';

        if (vendors.length === 0) {
            vendorAutocomplete.innerHTML = '<div class="px-4 py-2 text-gray-500 text-sm">No vendors found</div>';
        } else {
            vendors.forEach(vendor => {
                const suggestionItem = document.createElement('div');
                suggestionItem.className = 'suggestion-item px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm';
                suggestionItem.dataset.id = vendor.id;
                suggestionItem.dataset.name = vendor.name;
                suggestionItem.textContent = vendor.name;
                suggestionItem.addEventListener('click', () => selectVendor(vendor.id, vendor.name));
                vendorAutocomplete.appendChild(suggestionItem);
            });
        }

        vendorAutocomplete.classList.remove('hidden');
    }

    function selectVendor(id, name) {
        vendorSelect.value = id;
        vendorInput.value = name;
        hideVendorAutocomplete();
    }

    function hideVendorAutocomplete() {
        vendorAutocomplete.classList.add('hidden');
        vendorAutocomplete.querySelectorAll('.suggestion-item').forEach(item => {
            item.classList.remove('active');
        });
    }

    // Add new vendor functionality
    function addNewVendor(event) {
        event.preventDefault();
        const vendorName = document.getElementById('newVendorName').value.trim();
        
        if (!vendorName) {
            alert('Please enter a vendor name');
            return;
        }

        fetch('/vendors', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ name: vendorName })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add new vendor to select dropdown
                const option = document.createElement('option');
                option.value = data.vendor.id;
                option.textContent = data.vendor.name;
                vendorSelect.appendChild(option);
                
                // Select the new vendor
                vendorSelect.value = data.vendor.id;
                vendorInput.value = data.vendor.name;
                
                // Clear input
                document.getElementById('newVendorName').value = '';
                
                // Show success message
                alert('Vendor added successfully!');
            } else {
                alert(data.message || 'Error adding vendor');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding vendor');
        });
    }

    // Add JS for acquisition document preview and validation
    document.addEventListener('DOMContentLoaded', function() {
        const acquisitionInput = document.getElementById('acquisition_document');
        const acquisitionPreviewContainer = document.getElementById('acquisitionPreviewContainer');
        const acquisitionPreview = document.getElementById('acquisitionPreview');
        const removeAcquisitionButton = document.getElementById('removeAcquisitionButton');
        const acquisitionDropzone = document.getElementById('acquisitionDropzone');

        acquisitionInput.addEventListener('change', function() {
            previewAcquisition(this.files[0]);
        });

        acquisitionDropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            acquisitionDropzone.classList.add('border-blue-400');
        });
        acquisitionDropzone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            acquisitionDropzone.classList.remove('border-blue-400');
        });
        acquisitionDropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            acquisitionDropzone.classList.remove('border-blue-400');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                acquisitionInput.files = files;
                previewAcquisition(files[0]);
            }
        });
        acquisitionDropzone.addEventListener('click', function() {
            acquisitionInput.click();
        });
        function previewAcquisition(file) {
            if (file) {
                if (file.type === 'application/pdf') {
                    acquisitionPreview.src = 'https://cdn.jsdelivr.net/gh/stephentian/pdf-icon@main/pdf-icon.png';
                } else {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        acquisitionPreview.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
                acquisitionPreviewContainer.classList.remove('hidden');
                validateAcquisitionFileSize(acquisitionInput);
            } else {
                acquisitionPreview.src = '#';
                acquisitionPreviewContainer.classList.add('hidden');
                acquisitionInput.value = '';
                document.getElementById('acquisitionFileError').classList.add('hidden');
                document.querySelector('button[type="submit"]').disabled = false;
            }
        }
        removeAcquisitionButton.addEventListener('click', function() {
            previewAcquisition(null);
        });
        validateAcquisitionFileSize(acquisitionInput);
    });
    function validateAcquisitionFileSize(input) {
        const fileError = document.getElementById('acquisitionFileError');
        const submitButton = document.querySelector('button[type="submit"]');
        if (input.files && input.files[0]) {
            const fileSize = input.files[0].size / 1024 / 1024;
            if (fileSize > 2) {
                fileError.textContent = 'File size exceeds 2MB limit. Please choose a smaller file.';
                fileError.classList.remove('hidden');
                input.value = '';
                submitButton.disabled = true;
            } else {
                fileError.classList.add('hidden');
                submitButton.disabled = false;
            }
        }
    }
</script>
@endsection
