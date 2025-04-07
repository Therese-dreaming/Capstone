@extends('layouts.app')

@section('content')
<div class="flex-1 ml-80">
    <div class="p-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6">Edit Asset</h2>

            <form action="{{ route('assets.update', $asset->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Add this right after the form opening tag -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-6">
                    <!-- Asset Name field -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                            Asset Name
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $asset->name) }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <!-- Serial Number field -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="serial_number">
                            Serial Number
                        </label>
                        <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number', $asset->serial_number) }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline 
                            @error('serial_number') border-red-500 @enderror">
                        @error('serial_number')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category field -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="category_id">
                            Category
                        </label>
                        <select name="category_id" id="category_id"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $asset->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Purchase Price field -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="purchase_price">
                            Purchase Price
                        </label>
                        <input type="number" step="0.01" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $asset->purchase_price) }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <!-- Location field -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="location">
                            Location
                        </label>
                        <select name="location_select" id="locationSelect" 
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-2">
                            <option value="">Select Location</option>
                            <option value="Computer Lab 401" {{ $asset->location == 'Computer Lab 401' ? 'selected' : '' }}>Computer Lab 401</option>
                            <option value="Computer Lab 402" {{ $asset->location == 'Computer Lab 402' ? 'selected' : '' }}>Computer Lab 402</option>
                            <option value="Computer Lab 403" {{ $asset->location == 'Computer Lab 403' ? 'selected' : '' }}>Computer Lab 403</option>
                            <option value="Computer Lab 404" {{ $asset->location == 'Computer Lab 404' ? 'selected' : '' }}>Computer Lab 404</option>
                            <option value="Computer Lab 405" {{ $asset->location == 'Computer Lab 405' ? 'selected' : '' }}>Computer Lab 405</option>
                            <option value="Computer Lab 406" {{ $asset->location == 'Computer Lab 406' ? 'selected' : '' }}>Computer Lab 406</option>
                            <option value="others" {{ !in_array($asset->location, ['Computer Lab 401', 'Computer Lab 402', 'Computer Lab 403', 'Computer Lab 404', 'Computer Lab 405', 'Computer Lab 406']) ? 'selected' : '' }}>Others (Specify)</option>
                        </select>
                        <input type="text" name="location" id="otherLocation" 
                            value="{{ !in_array($asset->location, ['Computer Lab 401', 'Computer Lab 402', 'Computer Lab 403', 'Computer Lab 404', 'Computer Lab 405', 'Computer Lab 406']) ? $asset->location : '' }}"
                            placeholder="Please specify location" 
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ in_array($asset->location, ['Computer Lab 401', 'Computer Lab 402', 'Computer Lab 403', 'Computer Lab 404', 'Computer Lab 405', 'Computer Lab 406']) ? 'hidden' : '' }}">
                    </div>

                    <!-- Status field -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                            Status
                        </label>
                        <select name="status" id="status"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="IN USE" {{ $asset->status == 'IN USE' ? 'selected' : '' }}>In Use</option>
                            <option value="UNDER REPAIR" {{ $asset->status == 'UNDER REPAIR' ? 'selected' : '' }}>Under Repair</option>
                            <option value="UPGRADE" {{ $asset->status == 'UPGRADE' ? 'selected' : '' }}>Upgrade</option>
                            <option value="PENDING DEPLOYMENT" {{ $asset->status == 'PENDING DEPLOYMENT' ? 'selected' : '' }}>Pending Deployment</option>
                        </select>
                    </div>
                </div>

                <!-- Model field -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="model">
                        Model
                    </label>
                    <input type="text" name="model" id="model" value="{{ old('model', $asset->model) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Specification field -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="specification">
                        Specification
                    </label>
                    <input type="text" name="specification" id="specification" value="{{ old('specification', $asset->specification) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Vendor field -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="vendor">
                        Vendor
                    </label>
                    <input type="text" name="vendor" id="vendor" value="{{ old('vendor', $asset->vendor) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Purchase Date field -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="purchase_date">
                        Purchase Date
                    </label>
                    <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', $asset->purchase_date) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Warranty Period field -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="warranty_period">
                        Warranty Period
                    </label>
                    <input type="date" name="warranty_period" id="warranty_period" value="{{ old('warranty_period', $asset->warranty_period) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Lifespan field -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="lifespan">
                        Lifespan (in years)
                    </label>
                    <input type="number" name="lifespan" id="lifespan" value="{{ old('lifespan', $asset->lifespan) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Update Asset
                </button>
            </div>
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
</script>
@endsection
