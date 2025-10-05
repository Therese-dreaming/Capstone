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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Add New Asset</h1>
                        <p class="text-red-100 text-sm md:text-lg">Enter the details of the new asset below</p>
                    </div>
                </div>
                <a href="{{ auth()->check() && auth()->user()->group_id === 4 ? route('custodian.assets.index') : route('assets.index') }}" class="inline-flex items-center px-4 py-2 bg-white/20 text-white font-medium rounded-lg hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 focus:ring-offset-2 focus:ring-offset-red-800 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                    </svg>
                    Back to Assets
                </a>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="mb-4 md:mb-6 p-3 md:p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm md:text-base font-medium">Please correct the following errors:</span>
            </div>
            <ul class="list-disc list-inside ml-4 space-y-1 text-sm md:text-base">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Content Card -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
        <form action="{{ auth()->check() && auth()->user()->group_id === 4 ? route('custodian.assets.store') : route('assets.store') }}" method="POST" enctype="multipart/form-data" id="assetForm">
            @csrf
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            
            <!-- Hidden field for repair request ID when linking from repair details -->
            @if($fromNonRegistered ?? false && !empty($repairRequestId ?? ''))
            <input type="hidden" name="repair_request_id" value="{{ $repairRequestId }}">
            @endif
            
            <!-- Non-Registered Asset Context Warning -->
            @if($fromNonRegistered ?? false)
            @php
                $contextStatus = request('status', 'PULLED OUT');
            @endphp
            <div class="mb-6 flex items-center p-4 bg-blue-50 border border-blue-200 rounded-xl">
                <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <span class="text-sm md:text-base text-blue-700 font-medium">Registering Asset from Repair Request</span>
                    <p class="text-xs text-blue-600 mt-1">This asset is being registered from a repair request and will be automatically linked. The status will be set to "{{ $contextStatus }}" based on the repair request status and cannot be changed.</p>
                </div>
            </div>
            @endif

            <!-- File Size Warning -->
            <div class="mb-6 flex items-center p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                <svg class="w-5 h-5 text-yellow-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm md:text-base text-yellow-700">Maximum file size allowed is 2MB</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div class="pb-3 mb-6 border-b border-gray-200">
                        <h4 class="text-lg md:text-xl font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Basic Asset Information
                        </h4>
                        <p class="text-sm text-gray-500 mt-1">Essential details about the asset</p>
                    </div>
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Asset Name</label>
                                <input type="text" name="name" required value="{{ request('equipment') ?? old('name') }}" 
                                       class="w-full px-4 py-3 border @error('name') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                <input type="number" name="quantity" min="1" max="100" value="{{ ($fromNonRegistered ?? false) ? 1 : (old('quantity', 1)) }}" {{ ($fromNonRegistered ?? false) ? 'readonly' : '' }}
                                       class="w-full px-4 py-3 border @error('quantity') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                                @error('quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Create multiple identical assets. Each will get a unique serial number and QR code.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                                <div class="relative">
                                    <input type="text" id="locationSearch" 
                                           class="w-full px-4 py-3 border @error('location_id') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200" 
                                           placeholder="Type to search location (e.g., 401, Computer Lab, Gabriel)"
                                           autocomplete="off"
                                           value="{{ old('location_id') ? $locations->firstWhere('id', old('location_id'))?->full_location : '' }}">
                                    <input type="hidden" name="location_id" id="locationId" value="{{ old('location_id') }}" required>
                                    <!-- Autocomplete dropdown -->
                                    <div id="locationAutocomplete" class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden mt-1">
                                        <!-- Suggestions will be populated here -->
                                    </div>
                                </div>
                                @error('location_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Type to search and select a location. If not listed, contact the administrator.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                @if($fromNonRegistered ?? false)
                                    @php
                                        $dynamicStatus = request('status', 'PULLED OUT');
                                        $statusColor = match($dynamicStatus) {
                                            'IN USE' => 'bg-green-100 text-green-800',
                                            'UNDER REPAIR' => 'bg-blue-100 text-blue-800',
                                            'PULLED OUT' => 'bg-yellow-100 text-yellow-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                        $statusDescription = match($dynamicStatus) {
                                            'IN USE' => 'Status set based on completed repair',
                                            'UNDER REPAIR' => 'Status set based on ongoing repair',
                                            'PULLED OUT' => 'Status set because asset was pulled out for repair',
                                            default => 'Status set based on repair request status'
                                        };
                                    @endphp
                                    <!-- Status is locked based on repair request status -->
                                    <div class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium">{{ $dynamicStatus }}</span>
                                            <span class="text-xs {{ $statusColor }} px-2 py-1 rounded-full">Auto-Set</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">{{ $statusDescription }}</p>
                                    </div>
                                    <input type="hidden" name="status" value="{{ $dynamicStatus }}">
                                    <input type="hidden" name="from_non_registered" value="1">
                                @else
                                    <select name="status" required 
                                            class="w-full px-4 py-3 border @error('status') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                                        <option value="">Select Status</option>
                                        @foreach(['IN USE', 'PULLED OUT'] as $status)
                                        <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <h5 class="text-lg font-medium mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    Physical Attributes
                                </h5>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                                        <input type="text" name="model" value="{{ old('model') }}" 
                                               class="w-full px-4 py-3 border @error('model') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                                        @error('model')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Specification</label>
                                        <textarea name="specification" rows="3" 
                                                  class="w-full px-4 py-3 border @error('specification') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">{{ old('specification') }}</textarea>
                                        @error('specification')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Serial Number Info -->
                                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-sm md:text-base text-blue-700">Serial Number will be automatically generated in format: ASST-YYYYMMDD-XXXX</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div class="pb-3 mb-6 border-b border-gray-200">
                        <h4 class="text-lg md:text-xl font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            Financial Information
                        </h4>
                        <p class="text-sm text-gray-500 mt-1">Financial and purchase details</p>
                    </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Vendor</label>
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
                                        <select name="vendor_id" required 
                                                class="w-full px-4 py-3 border @error('vendor_id') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
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
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Date</label>
                                    <input type="date" name="purchase_date" value="{{ old('purchase_date') }}" 
                                           class="w-full px-4 py-3 border @error('purchase_date') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                                    @error('purchase_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Price (₱)</label>
                                    <input type="number" name="purchase_price" id="purchase_price" min="0" step="0.01" value="{{ old('purchase_price') }}" required 
                                           class="w-full px-4 py-3 border @error('purchase_price') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                                    @error('purchase_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Warranty Period</label>
                                    <input type="date" name="warranty_period" value="{{ old('warranty_period') }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                    <select name="category_id" required 
                                            class="w-full px-4 py-3 border @error('category_id') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
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
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Asset Photo</label>
                                    <input id="photo" name="photo" type="file" class="sr-only" accept="image/*">
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-gray-400 transition-colors duration-200" id="dropzone">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <span class="relative bg-white rounded-md font-medium text-red-600">
                                                    Upload a file
                                                </span>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                        </div>
                                    </div>
                                    <!-- Image Preview -->
                                    <div class="mt-4 hidden" id="imagePreviewContainer">
                                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span class="text-sm font-medium text-gray-700" id="imageFileName">Image Preview</span>
                                                </div>
                                                <button type="button" id="removeImageButton" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <img id="imagePreview" src="#" alt="Image Preview" class="w-full h-40 object-cover rounded-lg shadow">
                                        </div>
                                    </div>
                                    @error('photo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="text-xs text-red-500 mt-1 hidden" id="fileError"></p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Acquisition Document (Scanned Copy)</label>
                                    <input id="acquisition_document" name="acquisition_document" type="file" class="sr-only" accept="image/*,application/pdf">
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-gray-400 transition-colors duration-200" id="acquisitionDropzone">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <span class="relative bg-white rounded-md font-medium text-red-600">
                                                    Upload a file
                                                </span>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF, PDF up to 2MB</p>
                                        </div>
                                    </div>
                                    <!-- Acquisition Document Preview -->
                                    <div class="mt-4 hidden" id="acquisitionPreviewContainer">
                                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <span class="text-sm font-medium text-gray-700" id="acquisitionFileName">Document Preview</span>
                                                </div>
                                                <button type="button" id="removeAcquisitionButton" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div id="acquisitionPreviewContent">
                                                <!-- Preview content will be inserted here -->
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-xs text-red-500 mt-1 hidden" id="acquisitionFileError"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Action Buttons -->
            <div class="mt-10 pt-6 border-t border-gray-200 flex flex-col sm:flex-row gap-4 justify-end">
                <a href="{{ auth()->check() && auth()->user()->group_id === 4 ? route('custodian.assets.index') : route('assets.index') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </a>
                <button type="submit" id="submitButton" 
                        class="inline-flex items-center justify-center px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-md">
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

    // Initialize form validation
    function initializeFormValidation() {
        const form = document.querySelector('form');
        const submitButton = document.getElementById('submitButton');
        const buttonText = document.getElementById('buttonText');
        const loadingIndicator = document.getElementById('loadingIndicator');

        if (!form || !submitButton || !buttonText || !loadingIndicator) {
            return;
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            let errors = [];

            // Required fields validation
            const requiredFields = {
                'name': 'Asset Name',
                'location_id': 'Location',
                'status': 'Status',
                'model': 'Model',
                'specification': 'Specification',
                'vendor_id': 'Vendor',
                'purchase_date': 'Purchase Date',
                'purchase_price': 'Purchase Price',
                'category_id': 'Category'
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

            if (purchaseDate && purchaseDate.value && warrantyPeriod && warrantyPeriod.value) {
                if (new Date(warrantyPeriod.value) < new Date(purchaseDate.value)) {
                    errors.push('Warranty Period must be after or equal to Purchase Date');
                    warrantyPeriod.classList.add('border-red-500');
                }
            }

            const purchasePrice = form.querySelector('[name="purchase_price"]');
            if (purchasePrice && purchasePrice.value && parseFloat(purchasePrice.value) <= 0) {
                errors.push('Purchase Price must be greater than 0');
                purchasePrice.classList.add('border-red-500');
            }

            if (errors.length > 0) {
                const errorMessage = document.getElementById('errorMessageText');
                if (errorMessage) {
                    errorMessage.innerHTML = errors.join('<br>');
                    document.getElementById('errorModal').classList.remove('hidden');
                }
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
    }

    // Main initialization when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeFormValidation();
        initializeImagePreview();
        initializeAcquisitionDocument();
        initializeVendorAutocomplete();
        initializeLocationAutocomplete();
    });

    // Initialize image preview functionality
    function initializeImagePreview() {
        const photoInput = document.getElementById('photo');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const removeImageButton = document.getElementById('removeImageButton');
        const dropzone = document.getElementById('dropzone');

        if (!photoInput || !imagePreviewContainer || !imagePreview || !removeImageButton || !dropzone) {
            return;
        }

        // Handle file selection via input change
        photoInput.addEventListener('change', function() {
            previewImage(this.files[0]);
        });

        // Handle file drop
        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('border-blue-400');
        });

        dropzone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('border-blue-400');
        });

        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('border-blue-400');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                photoInput.files = files;
                previewImage(files[0]);
            }
        });

        // Handle click on dropzone to open file input
        dropzone.addEventListener('click', function() {
            photoInput.click();
        });

        // Remove image button functionality
        removeImageButton.addEventListener('click', function() {
            previewImage(null);
        });

        // Initial validation
        validateFileSize(photoInput);
    }

    // Function to preview images
    function previewImage(file) {
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const photoInput = document.getElementById('photo');
        
        if (!imagePreviewContainer || !imagePreview || !photoInput) {
            return;
        }

        if (file) {
            const reader = new FileReader();
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2);

            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreviewContainer.classList.remove('hidden');
                
                // Update filename display
                const imageFileName = document.getElementById('imageFileName');
                if (imageFileName) {
                    imageFileName.textContent = `${fileName} (${fileSize} MB)`;
                }
                
                validateFileSize(photoInput);
            }

            reader.readAsDataURL(file);
        } else {
            // Reset preview
            imagePreview.src = '#';
            imagePreviewContainer.classList.add('hidden');
            photoInput.value = '';
            
            const imageFileName = document.getElementById('imageFileName');
            if (imageFileName) {
                imageFileName.textContent = 'Image Preview';
            }
            
            // Clear errors
            const fileError = document.getElementById('fileError');
            if (fileError) {
                fileError.classList.add('hidden');
            }
            
            const submitButton = document.getElementById('submitButton');
            if (submitButton) {
                submitButton.disabled = false;
            }
        }
    }

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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                name: name
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
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
            const errorMessage = error.message || (error.errors ? Object.values(error.errors).flat().join(', ') : 'Failed to add vendor. Please try again.');
            showNotification(errorMessage, 'error');
        });

        return false;
    }

    // Initialize vendor autocomplete functionality
    function initializeVendorAutocomplete() {
        const vendorInput = document.getElementById('newVendorName');
        const vendorAutocomplete = document.getElementById('vendorAutocomplete');

        if (!vendorInput || !vendorAutocomplete) {
            return;
        }

        let vendorAutocompleteTimeout;

        vendorInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(vendorAutocompleteTimeout);
            
            if (query.length < 2) {
                hideVendorAutocomplete();
                return;
            }

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
    }

    function searchVendors(query) {
        const vendorAutocomplete = document.getElementById('vendorAutocomplete');
        if (!vendorAutocomplete) return;

        fetch(`{{ route('vendors.getAll') }}?search=${encodeURIComponent(query)}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch vendors');
            }
            return response.json();
        })
        .then(vendors => {
            // Ensure vendors is an array
            if (Array.isArray(vendors)) {
                displayVendorSuggestions(vendors, query);
            } else {
                console.error('Invalid vendor data received:', vendors);
                displayVendorSuggestions([], query);
            }
        })
        .catch(error => {
            console.error('Error searching vendors:', error);
            displayVendorSuggestions([], query);
        });
    }

    function displayVendorSuggestions(vendors, query) {
        const vendorAutocomplete = document.getElementById('vendorAutocomplete');
        if (!vendorAutocomplete) return;

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
        const vendorInput = document.getElementById('newVendorName');
        const vendorSelect = document.querySelector('select[name="vendor_id"]');
        
        if (!vendorInput || !vendorSelect) return;

        if (vendor) {
            // Vendor exists, select it in the dropdown
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
        const vendorAutocomplete = document.getElementById('vendorAutocomplete');
        if (!vendorAutocomplete) return;

        vendorAutocomplete.classList.add('hidden');
        vendorAutocomplete.querySelectorAll('.suggestion-item').forEach(item => {
            item.classList.remove('active');
        });
    }

    // Initialize location autocomplete functionality
    function initializeLocationAutocomplete() {
        const locationInput = document.getElementById('locationSearch');
        const locationAutocomplete = document.getElementById('locationAutocomplete');
        const locationIdInput = document.getElementById('locationId');

        if (!locationInput || !locationAutocomplete || !locationIdInput) {
            return;
        }

        // All locations data from server
        const allLocations = @json($locations->map(function($loc) {
            return ['id' => $loc->id, 'name' => $loc->full_location];
        })->values());

        let searchTimeout;

        locationInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            
            clearTimeout(searchTimeout);
            
            if (query.length === 0) {
                hideLocationAutocomplete();
                locationIdInput.value = '';
                return;
            }

            searchTimeout = setTimeout(() => {
                filterLocations(query, allLocations);
            }, 200);
        });

        // Handle keyboard navigation
        locationInput.addEventListener('keydown', function(e) {
            const suggestions = locationAutocomplete.querySelectorAll('.location-suggestion-item');
            const activeSuggestion = locationAutocomplete.querySelector('.location-suggestion-item.active');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (!activeSuggestion) {
                    suggestions[0]?.classList.add('active');
                } else {
                    const nextSuggestion = activeSuggestion.nextElementSibling;
                    if (nextSuggestion) {
                        activeSuggestion.classList.remove('active');
                        nextSuggestion.classList.add('active');
                        nextSuggestion.scrollIntoView({ block: 'nearest' });
                    }
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (activeSuggestion) {
                    const prevSuggestion = activeSuggestion.previousElementSibling;
                    if (prevSuggestion) {
                        activeSuggestion.classList.remove('active');
                        prevSuggestion.classList.add('active');
                        prevSuggestion.scrollIntoView({ block: 'nearest' });
                    } else {
                        activeSuggestion.classList.remove('active');
                    }
                }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (activeSuggestion) {
                    activeSuggestion.click();
                }
            } else if (e.key === 'Escape') {
                hideLocationAutocomplete();
            }
        });

        // Hide autocomplete when clicking outside
        document.addEventListener('click', function(e) {
            if (!locationInput.contains(e.target) && !locationAutocomplete.contains(e.target)) {
                hideLocationAutocomplete();
            }
        });
    }

    function filterLocations(query, allLocations) {
        const locationAutocomplete = document.getElementById('locationAutocomplete');
        if (!locationAutocomplete) return;

        // Filter locations that match the query
        const matches = allLocations.filter(loc => 
            loc.name.toLowerCase().includes(query)
        );

        displayLocationSuggestions(matches);
    }

    function displayLocationSuggestions(locations) {
        const locationAutocomplete = document.getElementById('locationAutocomplete');
        if (!locationAutocomplete) return;

        locationAutocomplete.innerHTML = '';
        
        if (locations.length === 0) {
            locationAutocomplete.innerHTML = `
                <div class="px-3 py-2 text-sm text-gray-500">
                    No locations found. Try a different search term.
                </div>
            `;
        } else {
            locations.slice(0, 50).forEach(location => {
                const suggestionItem = document.createElement('div');
                suggestionItem.className = 'location-suggestion-item px-3 py-2 text-sm cursor-pointer hover:bg-gray-100 border-b border-gray-100 last:border-b-0';
                suggestionItem.textContent = location.name;
                suggestionItem.dataset.locationId = location.id;
                suggestionItem.addEventListener('click', () => selectLocation(location));
                locationAutocomplete.appendChild(suggestionItem);
            });

            if (locations.length > 50) {
                const moreItem = document.createElement('div');
                moreItem.className = 'px-3 py-2 text-xs text-gray-400 italic';
                moreItem.textContent = `+ ${locations.length - 50} more results. Refine your search.`;
                locationAutocomplete.appendChild(moreItem);
            }
        }
        
        locationAutocomplete.classList.remove('hidden');
    }

    function selectLocation(location) {
        const locationInput = document.getElementById('locationSearch');
        const locationIdInput = document.getElementById('locationId');
        
        if (!locationInput || !locationIdInput) return;

        locationInput.value = location.name;
        locationIdInput.value = location.id;
        
        hideLocationAutocomplete();
    }

    function hideLocationAutocomplete() {
        const locationAutocomplete = document.getElementById('locationAutocomplete');
        if (!locationAutocomplete) return;

        locationAutocomplete.classList.add('hidden');
        locationAutocomplete.querySelectorAll('.location-suggestion-item').forEach(item => {
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

    // Initialize acquisition document functionality
    function initializeAcquisitionDocument() {
        const acquisitionInput = document.getElementById('acquisition_document');
        const acquisitionPreviewContainer = document.getElementById('acquisitionPreviewContainer');
        const removeAcquisitionButton = document.getElementById('removeAcquisitionButton');
        const acquisitionDropzone = document.getElementById('acquisitionDropzone');

        if (!acquisitionInput || !acquisitionPreviewContainer || !removeAcquisitionButton || !acquisitionDropzone) {
            return;
        }

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
        
        // Handle click on dropzone to open file input
        acquisitionDropzone.addEventListener('click', function() {
            acquisitionInput.click();
        });

        removeAcquisitionButton.addEventListener('click', function() {
            previewAcquisition(null);
        });

        // Initial validation
        validateAcquisitionFileSize(acquisitionInput);
    }

    // Function to preview acquisition documents
    function previewAcquisition(file) {
        const acquisitionPreviewContainer = document.getElementById('acquisitionPreviewContainer');
        const acquisitionPreviewContent = document.getElementById('acquisitionPreviewContent');
        const acquisitionFileName = document.getElementById('acquisitionFileName');
        const acquisitionInput = document.getElementById('acquisition_document');
        
        if (!acquisitionPreviewContainer || !acquisitionPreviewContent || !acquisitionFileName || !acquisitionInput) {
            return;
        }

        if (file) {
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
            
            // Update filename display
            acquisitionFileName.textContent = `${fileName} (${fileSize} MB)`;
            
            if (file.type === 'application/pdf') {
                // Show PDF icon and filename
                acquisitionPreviewContent.innerHTML = `
                    <div class="text-center">
                        <div class="mx-auto w-20 h-20 bg-red-100 rounded-lg flex items-center justify-center mb-3">
                            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-600">PDF Document</p>
                    </div>
                `;
            } else {
                // Show image preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    acquisitionPreviewContent.innerHTML = `
                        <img src="${e.target.result}" alt="Document Preview" class="w-full h-40 object-cover rounded-lg shadow">
                    `;
                }
                reader.readAsDataURL(file);
            }
            
            acquisitionPreviewContainer.classList.remove('hidden');
            validateAcquisitionFileSize(acquisitionInput);
        } else {
            // Reset preview
            acquisitionPreviewContent.innerHTML = '';
            acquisitionPreviewContainer.classList.add('hidden');
            acquisitionInput.value = '';
            acquisitionFileName.textContent = 'Document Preview';
            
            const fileError = document.getElementById('acquisitionFileError');
            if (fileError) {
                fileError.classList.add('hidden');
            }
            
            const submitButton = document.getElementById('submitButton');
            if (submitButton) {
                submitButton.disabled = false;
            }
        }
    }

    // Function to validate acquisition file size
    function validateAcquisitionFileSize(input) {
        const fileError = document.getElementById('acquisitionFileError');
        const submitButton = document.getElementById('submitButton');
        
        if (!fileError || !submitButton) {
            return;
        }
        
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

    .location-suggestion-item.active {
        background-color: #dbeafe;
        font-weight: 500;
    }

    .location-suggestion-item:hover {
        background-color: #f3f4f6;
    }

</style>
@endsection
