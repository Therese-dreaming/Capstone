@extends('layouts.app')

@section('content')
<div class="flex-1">
    <div class="p-6">
        <!-- Update the error display section at the top -->
        @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-md">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-red-800 font-medium">Please correct the following errors:</h3>
            </div>
            <ul class="list-disc list-inside text-sm text-red-700">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Add New Asset</h1>
                <p class="text-sm text-gray-600 mt-1">Enter the details of the new asset below</p>
            </div>
            <a href="{{ route('assets.index') }}" class="flex items-center text-gray-600 hover:text-gray-800">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                Back to Assets
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
            <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data" id="assetForm">
                @csrf
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <!-- Add max file size warning -->
                <div class="mb-6 flex items-center p-4 bg-yellow-50 rounded-lg">
                    <svg class="w-5 h-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm text-yellow-700">Maximum file size allowed is 2MB</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <div class="pb-3 mb-6 border-b border-gray-200">
                            <h4 class="text-xl font-semibold text-gray-800">Basic Asset Information</h4>
                            <p class="text-sm text-gray-500 mt-1">Essential details about the asset</p>
                        </div>
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Asset Name</label>
                                <input type="text" name="name" required value="{{ request('equipment') ?? old('name') }}" class="w-full p-2.5 border @error('name') border-red-500 @enderror border-gray-300 rounded-lg focus:ring-2 focus:ring-red-200 focus:border-red-400 transition-colors">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                                <select name="location_select" id="locationSelect" class="w-full p-2 border @error('location') border-red-500 @enderror border-gray-300 rounded-md focus:ring-2 focus:ring-red-200 mb-2">
                                    <option value="">Select Location</option>
                                    <option value="Computer Lab 401" {{ request('location') == 'Computer Lab 401' ? 'selected' : '' }}>Computer Lab 401</option>
                                    <option value="Computer Lab 402" {{ request('location') == 'Computer Lab 402' ? 'selected' : '' }}>Computer Lab 402</option>
                                    <option value="Computer Lab 403" {{ request('location') == 'Computer Lab 403' ? 'selected' : '' }}>Computer Lab 403</option>
                                    <option value="Computer Lab 404" {{ request('location') == 'Computer Lab 404' ? 'selected' : '' }}>Computer Lab 404</option>
                                    <option value="Computer Lab 405" {{ request('location') == 'Computer Lab 405' ? 'selected' : '' }}>Computer Lab 405</option>
                                    <option value="Computer Lab 406" {{ request('location') == 'Computer Lab 406' ? 'selected' : '' }}>Computer Lab 406</option>
                                    <option value="others" {{ !in_array(request('location'), ['Computer Lab 401', 'Computer Lab 402', 'Computer Lab 403', 'Computer Lab 404', 'Computer Lab 405', 'Computer Lab 406']) && request('location') ? 'selected' : '' }}>Others (Specify)</option>
                                </select>
                                @error('location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <input type="text" name="location" id="otherLocation" placeholder="Please specify location" value="{{ !in_array(request('location'), ['Computer Lab 401', 'Computer Lab 402', 'Computer Lab 403', 'Computer Lab 404', 'Computer Lab 405', 'Computer Lab 406']) ? request('location') : '' }}" class="w-full p-2 border @error('location') border-red-500 @enderror border-gray-300 rounded-md focus:ring-2 focus:ring-red-200 {{ !in_array(request('location'), ['Computer Lab 401', 'Computer Lab 402', 'Computer Lab 403', 'Computer Lab 404', 'Computer Lab 405', 'Computer Lab 406']) && request('location') ? '' : 'hidden' }}">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" required class="w-full p-2 border @error('status') border-red-500 @enderror border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">
                                    <option value="">Select Status</option>
                                    @foreach(['IN USE', 'PULLED OUT'] as $status)
                                    <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <h5 class="text-lg font-medium mb-2">Physical Attributes</h5>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                                        <input type="text" name="model" value="{{ old('model') }}" class="w-full p-2 border @error('model') border-red-500 @enderror border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">
                                        @error('model')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Serial Number</label>
                                        <input type="text" name="serial_number" value="{{ old('serial_number') }}" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-200 @error('serial_number') border-red-500 @enderror">
                                        @error('serial_number')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Specification</label>
                                        <textarea name="specification" rows="2" class="w-full p-2 border @error('specification') border-red-500 @enderror border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">{{ old('specification') }}</textarea>                                        @error('specification')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div>
                        <div class="space-y-6">
                            <div class="pb-3 mb-6 border-b border-gray-200">
                                <h4 class="text-xl font-semibold text-gray-800">Financial Information</h4>
                                <p class="text-sm text-gray-500 mt-1">Financial and purchase details</p>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
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
                                        <select name="vendor_id" required class="w-full px-3 py-2 border @error('vendor_id') border-red-500 @enderror border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">
                                            <option value="">Select Vendor</option>
                                            @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('vendor_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Date</label>
                                    <input type="date" name="purchase_date" value="{{ old('purchase_date') }}" class="w-full p-2 border @error('purchase_date') border-red-500 @enderror border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">
                                    @error('purchase_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Price (₱)</label>
                                    <input type="number" name="purchase_price" id="purchase_price" min="0" step="0.01" value="{{ old('purchase_price') }}" required class="w-full p-2 border @error('purchase_price') border-red-500 @enderror border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">
                                    @error('purchase_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Warranty Period</label>
                                    <input type="date" name="warranty_period" value="{{ old('warranty_period') }}" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                    <select name="category_id" required class="w-full p-2 border @error('category_id') border-red-500 @enderror border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">
                                        <option value="">Select Product Category</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : (old('category_id') == $category->id ? 'selected' : '') }}>
                                            {{ $category->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Asset Photo</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md cursor-pointer hover:border-gray-400" id="dropzone">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="photo" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                                                    <span>Upload a file</span>
                                                    <input id="photo" name="photo" type="file" class="sr-only" accept="image/*" max="2048" onchange="validateFileSize(this)">
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                        </div>
                                    </div>
                                    <!-- Image Preview -->
                                    <div class="mt-4 hidden" id="imagePreviewContainer">
                                        <img id="imagePreview" src="#" alt="Image Preview" class="max-w-full h-40 object-cover rounded-md shadow">
                                        <button type="button" id="removeImageButton" class="mt-2 text-red-600 hover:text-red-800 text-sm">Remove Image</button>
                                    </div>
                                    @error('photo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="text-xs text-red-500 mt-1 hidden" id="fileError"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 pt-6 border-t border-gray-200 flex justify-end space-x-4">
                    <a href="{{ route('assets.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" id="submitButton" class="px-6 py-2.5 bg-red-800 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center">
                        <span id="buttonText">Add Asset</span>
                        <svg class="w-5 h-5 ml-2 hidden" id="loadingIndicator" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex flex-col items-center">
            <!-- Error Icon -->
            <div class="mb-4">
                <svg class="w-20 h-20 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>

            <!-- Error Message -->
            <div class="mb-8 text-center">
                <h3 class="text-lg font-bold text-red-600 mb-2">Error</h3>
                <p class="text-sm text-gray-700" id="errorMessageText"></p>
            </div>

            <!-- Close Button -->
            <button onclick="closeErrorModal()" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Remove the nested script tags and combine the JavaScript -->
<script>
    // Show modal if there's an error
    @if(session('showErrorModal'))
    document.getElementById('errorModal').classList.remove('hidden');
    document.getElementById('errorMessageText').textContent = "{{ session('errorMessage') }}";
    @endif

    function closeErrorModal() {
        document.getElementById('errorModal').classList.add('hidden');
    }

    // File size validation
    function validateFileSize(input) {
        const fileError = document.getElementById('fileError');
        const submitButton = document.getElementById('submitButton');

        if (input.files && input.files[0]) {
            const fileSize = input.files[0].size / 1024 / 1024; // Convert to MB

            if (fileSize > 2) {
                fileError.textContent = 'File size exceeds 2MB limit. Please choose a smaller file.';
                fileError.classList.remove('hidden');
                input.value = ''; // Clear the file input
                submitButton.disabled = true;
            } else {
                fileError.classList.add('hidden');
                submitButton.disabled = false;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const submitButton = document.getElementById('submitButton');
        const buttonText = document.getElementById('buttonText');
        const loadingIndicator = document.getElementById('loadingIndicator');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            let errors = [];

            // Required fields validation
            const requiredFields = {
                'name': 'Asset Name'
                , 'location_select': 'Location'
                , 'status': 'Status'
                , 'model': 'Model'
                , 'serial_number': 'Serial Number'
                , 'specification': 'Specification'
                , 'vendor_id': 'Vendor'
                , 'purchase_date': 'Purchase Date'
                , 'warranty_period': 'Warranty Period'
                , 'purchase_price': 'Purchase Price'
                , 'category_id': 'Category'
            };

            Object.entries(requiredFields).forEach(([fieldName, label]) => {
                const field = form.querySelector(`[name="${fieldName}"]`);
                if (field && !field.value.trim()) {
                    errors.push(`${label} is required`);
                    field.classList.add('border-red-500');
                }
            });

            // Specific validations
            const purchaseDate = form.querySelector('[name="purchase_date"]');
            const warrantyPeriod = form.querySelector('[name="warranty_period"]');

            if (purchaseDate.value && warrantyPeriod.value) {
                if (new Date(warrantyPeriod.value) < new Date(purchaseDate.value)) {
                    errors.push('Warranty Period must be after or equal to Purchase Date');
                    warrantyPeriod.classList.add('border-red-500');
                }
            }

            const purchasePrice = form.querySelector('[name="purchase_price"]');
            if (purchasePrice.value && parseFloat(purchasePrice.value) <= 0) {
                errors.push('Purchase Price must be greater than 0');
                purchasePrice.classList.add('border-red-500');
            }

            // Location validation
            const locationSelect = document.getElementById('locationSelect');
            const otherLocation = document.getElementById('otherLocation');
            if (locationSelect.value === 'others' && !otherLocation.value.trim()) {
                errors.push('Please specify the other location');
                otherLocation.classList.add('border-red-500');
            }

            if (errors.length > 0) {
                const errorMessage = document.getElementById('errorMessageText');
                errorMessage.innerHTML = errors.join('<br>');
                document.getElementById('errorModal').classList.remove('hidden');
                return;
            }

            // Show loading state and submit
            submitButton.disabled = true;
            buttonText.textContent = 'Adding Asset...';
            loadingIndicator.classList.remove('hidden');
            form.submit();
        });

        // Clear error styling on input
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('input', function() {
                this.classList.remove('border-red-500');
            });
        });
    });

    document.getElementById('locationSelect').addEventListener('change', function() {
        const otherLocation = document.getElementById('otherLocation');
        const locationValue = this.value;

        if (locationValue === 'others') {
            otherLocation.classList.remove('hidden');
            otherLocation.required = true;
            otherLocation.value = ''; // Clear the other location input
            document.querySelector('input[name="location"]').value = ''; // Clear the main location value
        } else {
            otherLocation.classList.add('hidden');
            otherLocation.required = false;
            otherLocation.value = '';
            document.querySelector('input[name="location"]').value = locationValue;
        }
    });

    // Image preview and file handling
    document.addEventListener('DOMContentLoaded', function() {
        const photoInput = document.getElementById('photo');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const removeImageButton = document.getElementById('removeImageButton');
        const dropzone = document.getElementById('dropzone');

        // Handle file selection via input change
        photoInput.addEventListener('change', function() {
            previewImage(this.files[0]);
        });

        // Handle file drop
        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('border-blue-400'); // Optional: add a visual indicator
        });

        dropzone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('border-blue-400'); // Optional: remove the visual indicator
        });

        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('border-blue-400'); // Optional: remove the visual indicator

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                // Assign the dropped file to the input element
                photoInput.files = files;
                previewImage(files[0]);
            }
        });

        // Handle click on dropzone to open file input
        dropzone.addEventListener('click', function() {
            photoInput.click();
        });

        // Preview image function
        function previewImage(file) {
            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.classList.remove('hidden');
                    // Also re-run file size validation
                    validateFileSize(photoInput);
                }

                reader.readAsDataURL(file);
            } else {
                // If no file, hide preview and reset input
                imagePreview.src = '#';
                imagePreviewContainer.classList.add('hidden');
                photoInput.value = '';
                // Clear any existing file size error
                const fileError = document.getElementById('fileError');
                fileError.classList.add('hidden');
                const submitButton = document.getElementById('submitButton');
                submitButton.disabled = false;
            }
        }

        // Remove image button functionality
        removeImageButton.addEventListener('click', function() {
            previewImage(null);
        });

        // Re-run initial validation on page load in case of old input with errors
        validateFileSize(photoInput);
    });

    // Existing location select script (ensure it's outside or correctly integrated)
    document.getElementById('locationSelect').addEventListener('change', function() {
        const otherLocation = document.getElementById('otherLocation');
        const locationValue = this.value;

        if (locationValue === 'others') {
            otherLocation.classList.remove('hidden');
            otherLocation.required = true;
            otherLocation.value = ''; // Clear the other location input
            document.querySelector('input[name="location"]').value = ''; // Clear the main location value
        } else {
            otherLocation.classList.add('hidden');
            otherLocation.required = false;
            otherLocation.value = '';
            document.querySelector('input[name="location"]').value = locationValue;
        }
    });

    // Handle pre-filled location on page load
    document.addEventListener('DOMContentLoaded', function() {
        const locationSelect = document.getElementById('locationSelect');
        const otherLocation = document.getElementById('otherLocation');
        
        if (locationSelect.value === 'others') {
            otherLocation.classList.remove('hidden');
            otherLocation.required = true;
        }
    });

    // Add new vendor function
    function addNewVendor(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        const newVendorName = document.getElementById('newVendorName');
        const name = newVendorName.value.trim();

        if (!name) {
            showNotification('Please enter a vendor name', 'error');
            return false;
        }

        // Send AJAX request to add new vendor
        fetch('{{ route("vendors.addNew") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                name: name
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add new vendor to the dropdown
                const vendorSelect = document.querySelector('select[name="vendor_id"]');
                const newOption = document.createElement('option');
                newOption.value = data.vendor.id;
                newOption.textContent = data.vendor.name;
                vendorSelect.appendChild(newOption);
                
                // Select the new vendor
                vendorSelect.value = data.vendor.id;
                
                // Clear the input field
                newVendorName.value = '';
                
                // Hide autocomplete dropdown
                hideVendorAutocomplete();
                
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message || 'Failed to add vendor', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to add vendor. Please try again.', 'error');
        });

        return false;
    }

    // Vendor autocomplete functionality
    let vendorAutocompleteTimeout;
    const vendorInput = document.getElementById('newVendorName');
    const vendorAutocomplete = document.getElementById('vendorAutocomplete');

    vendorInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(vendorAutocompleteTimeout);
        
        if (query.length < 2) {
            hideVendorAutocomplete();
            return;
        }

        // Debounce the search
        vendorAutocompleteTimeout = setTimeout(() => {
            searchVendors(query);
        }, 300);
    });

    // Handle keyboard navigation
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
                selectVendorSuggestion(activeSuggestion);
            } else {
                addNewVendor();
            }
        } else if (e.key === 'Escape') {
            hideVendorAutocomplete();
        }
    });

    // Hide autocomplete when clicking outside
    document.addEventListener('click', function(e) {
        if (!vendorInput.contains(e.target) && !vendorAutocomplete.contains(e.target)) {
            hideVendorAutocomplete();
        }
    });

    function searchVendors(query) {
        fetch(`{{ route('vendors.getAll') }}?search=${encodeURIComponent(query)}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(vendors => {
            displayVendorSuggestions(vendors, query);
        })
        .catch(error => {
            console.error('Error searching vendors:', error);
        });
    }

    function displayVendorSuggestions(vendors, query) {
        vendorAutocomplete.innerHTML = '';
        
        if (vendors.length === 0) {
            vendorAutocomplete.innerHTML = `
                <div class="px-3 py-2 text-sm text-gray-500">
                    No vendors found. Press Enter to add "${query}" as a new vendor.
                </div>
            `;
        } else {
            vendors.forEach(vendor => {
                const suggestionItem = document.createElement('div');
                suggestionItem.className = 'suggestion-item px-3 py-2 text-sm cursor-pointer hover:bg-gray-100';
                suggestionItem.textContent = vendor.name;
                suggestionItem.addEventListener('click', () => selectVendorSuggestion(suggestionItem, vendor));
                vendorAutocomplete.appendChild(suggestionItem);
            });
        }
        
        vendorAutocomplete.classList.remove('hidden');
    }

    function selectVendorSuggestion(suggestionItem, vendor = null) {
        if (vendor) {
            // Vendor exists, select it in the dropdown
            const vendorSelect = document.querySelector('select[name="vendor_id"]');
            vendorSelect.value = vendor.id;
            vendorInput.value = vendor.name;
        } else {
            // Use the text content as the vendor name
            const vendorName = suggestionItem.textContent;
            vendorInput.value = vendorName;
        }
        
        hideVendorAutocomplete();
    }

    function hideVendorAutocomplete() {
        vendorAutocomplete.classList.add('hidden');
        vendorAutocomplete.querySelectorAll('.suggestion-item').forEach(item => {
            item.classList.remove('active');
        });
    }

    // Add notification function
    function showNotification(message, type) {
        // Create notification element if it doesn't exist
        let notification = document.getElementById('notification');
        if (!notification) {
            notification = document.createElement('div');
            notification.id = 'notification';
            notification.className = 'fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg hidden';
            document.body.appendChild(notification);
        }

        notification.textContent = message;
        notification.classList.remove('hidden', 'bg-green-100', 'border-green-400', 'text-green-700', 'bg-red-100', 'border-red-400', 'text-red-700');

        if (type === 'success') {
            notification.classList.add('bg-green-100', 'border', 'border-green-400', 'text-green-700');
        } else {
            notification.classList.add('bg-red-100', 'border', 'border-red-400', 'text-red-700');
        }

        // Show notification
        notification.classList.remove('hidden');

        // Hide notification after 3 seconds
        setTimeout(() => {
            notification.classList.add('hidden');
        }, 3000);
    }
</script>

<style>
    * {
        font-family: 'Poppins', sans-serif !important;
    }

    .menu-open {
        display: block !important;
    }

    /* Mobile sidebar */
    @media (max-width: 768px) {
        .sidebar-open {
            transform: translateX(0) !important;
        }

        .sidebar-overlay {
            display: block !important;
            opacity: 0.5 !important;
        }

        .content-shifted {
            margin-left: 0 !important;
        }
    }

    /* Autocomplete styling */
    .suggestion-item.active {
        background-color: #f3f4f6;
        font-weight: 500;
    }

    .suggestion-item:hover {
        background-color: #f9fafb;
    }

</style>
@endsection
