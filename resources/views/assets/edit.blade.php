@extends('layouts.app')

@section('content')
<div class="flex-1">
    <div class="p-6">
        <!-- Main Container -->
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-5xl mx-auto">
            <!-- Header -->
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Edit Asset</h2>
                <p class="text-sm text-gray-600 mt-1">Update the information for this asset</p>
            </div>

            <form action="{{ route('assets.update', $asset->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Error Messages -->
                @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
                    <p class="font-medium">Please correct the following errors:</p>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Form Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Basic Information Section -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Basic Information</h3>

                            <!-- Example of consistent error handling for each field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                    Asset Name
                                </label>
                                <input type="text" name="name" id="name" 
                                    value="{{ old('name', $asset->name) }}" 
                                    class="w-full p-2.5 border @error('name') border-red-500 @enderror border-gray-300 rounded-lg focus:ring-2 focus:ring-red-200 focus:border-red-400 transition-colors">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Apply to all input fields including selects -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="category_id">
                                    Category
                                </label>
                                <select name="category_id" id="category_id" 
                                    class="w-full p-2.5 border @error('category_id') border-red-500 @enderror border-gray-300 rounded-lg focus:ring-2 focus:ring-red-200 focus:border-red-400 transition-colors">
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
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Location & Status</h3>

                            <!-- Location field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="location">
                                    Location
                                </label>
                                <select name="location_select" id="locationSelect" class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-200 focus:border-red-400 transition-colors mb-2">
                                    <option value="">Select Location</option>
                                    @foreach(['Computer Lab 401', 'Computer Lab 402', 'Computer Lab 403', 'Computer Lab 404', 'Computer Lab 405', 'Computer Lab 406'] as $lab)
                                    <option value="{{ $lab }}" {{ $asset->location == $lab ? 'selected' : '' }}>
                                        {{ $lab }}
                                    </option>
                                    @endforeach
                                    <option value="others" {{ !in_array($asset->location, ['Computer Lab 401', 'Computer Lab 402', 'Computer Lab 403', 'Computer Lab 404', 'Computer Lab 405', 'Computer Lab 406']) ? 'selected' : '' }}>
                                        Others (Specify)
                                    </option>
                                </select>
                                <input type="text" name="location" id="otherLocation" value="{{ !in_array($asset->location, ['Computer Lab 401', 'Computer Lab 402', 'Computer Lab 403', 'Computer Lab 404', 'Computer Lab 405', 'Computer Lab 406']) ? $asset->location : '' }}" placeholder="Please specify location" class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-200 focus:border-red-400 transition-colors mt-2 {{ in_array($asset->location, ['Computer Lab 401', 'Computer Lab 402', 'Computer Lab 403', 'Computer Lab 404', 'Computer Lab 405', 'Computer Lab 406']) ? 'hidden' : '' }}">
                            </div>

                            <!-- Status field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                                    Status
                                </label>
                                <select name="status" id="status" class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-200 focus:border-red-400 transition-colors" {{ $asset->status == 'UNDER REPAIR' ? 'disabled' : '' }}>
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
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Purchase Details</h3>

                            <!-- Purchase Price field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="purchase_price">
                                    Purchase Price
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-600">₱</span>
                                    <input type="number" step="0.01" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $asset->purchase_price) }}" class="w-full pl-8 p-2.5 border @error('purchase_price') border-red-500 @enderror border-gray-300 rounded-lg focus:ring-2 focus:ring-red-200 focus:border-red-400 transition-colors">                                </div>
                            </div>

                            <!-- Purchase Date field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="purchase_date">
                                    Purchase Date
                                </label>
                                <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', $asset->purchase_date) }}" class="w-full p-2.5 border @error('purchase_date') border-red-500 @enderror border-gray-300 rounded-lg focus:ring-2 focus:ring-red-200 focus:border-red-400 transition-colors">
                            </div>

                            <!-- Vendor field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="vendor">
                                    Vendor
                                </label>
                                <div class="border border-gray-300 rounded-md bg-gray-50 p-4">
                                    <!-- Add New Vendor Input -->
                                    <div class="mb-4 flex space-x-2">
                                        <div class="flex-1 relative">
                                            <input type="text" id="newVendorName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 bg-white" placeholder="Enter new vendor name">
                                            <!-- Autocomplete dropdown -->
                                            <div id="vendorAutocomplete" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-y-auto hidden">
                                                <!-- Suggestions will be populated here -->
                                            </div>
                                        </div>
                                        <button type="button" onclick="addNewVendor(event)" class="px-4 py-2 bg-red-800 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">
                                            Add Vendor
                                        </button>
                                    </div>
                                    <select name="vendor_id" class="w-full px-3 py-2 border @error('vendor_id') border-red-500 @enderror border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">
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
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Technical Details</h3>

                            <!-- Serial Number field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="serial_number">
                                    Serial Number
                                </label>
                                <input type="text" name="serial_number" id="serial_number" 
                                    value="{{ old('serial_number', $asset->serial_number) }}" 
                                    class="w-full p-2.5 border @error('serial_number') border-red-500 @enderror border-gray-300 rounded-lg focus:ring-2 focus:ring-red-200 focus:border-red-400 transition-colors">
                                @error('serial_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Model field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="model">
                                    Model
                                </label>
                                <input type="text" name="model" id="model" value="{{ old('model', $asset->model) }}" class="w-full p-2.5 border @error('model') border-red-500 @enderror border-gray-300 rounded-lg focus:ring-2 focus:ring-red-200 focus:border-red-400 transition-colors">
                            </div>

                            <!-- Specification field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="specification">
                                    Specification
                                </label>
                                <textarea name="specification" id="specification" rows="3" class="w-full p-2.5 border @error('specification') border-red-500 @enderror border-gray-300 rounded-lg focus:ring-2 focus:ring-red-200 focus:border-red-400 transition-colors">{{ old('specification', $asset->specification) }}</textarea>
                            </div>
                        </div>

                        <!-- Warranty & Lifespan Section -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Warranty & Lifespan</h3>

                            <!-- Warranty Period field -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="warranty_period">
                                    Warranty Period
                                </label>
                                <input type="date" name="warranty_period" id="warranty_period" value="{{ old('warranty_period', $asset->warranty_period) }}" class="w-full p-2.5 border @error('warranty_period') border-red-500 @enderror border-gray-300 rounded-lg focus:ring-2 focus:ring-red-200 focus:border-red-400 transition-colors">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('assets.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-red-800 text-white font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Update Asset
                    </button>
                </div>
            </form>
        </div>
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
</script>
@endsection
