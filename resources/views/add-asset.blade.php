@extends('layouts.app')

@section('content')
<div class="flex-1 ml-80">
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold">ADD NEW ASSET</h1>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-2 gap-8">
                    <!-- Left Column -->
                    <div>
                        <h4 class="text-xl font-semibold mb-4">Basic Asset Information</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Asset Name</label>
                                <!-- For each input field except serial number, add old() helper -->
                                <input type="text" name="name" required value="{{ old('name') }}" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                <input type="text" name="location" required value="{{ old('location') }}" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" required class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">
                                    <option value="">Select Status</option>
                                    @foreach(['IN USE', 'UNDER REPAIR', 'DISPOSED', 'UPGRADE', 'PENDING DEPLOYMENT'] as $status)
                                    <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <h5 class="text-lg font-medium mb-2">Physical Attributes</h5>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea name="description" rows="2" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-200"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                                        <input type="text" name="model" value="{{ old('model') }}" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">
                                    </div>
                                    <!-- Add this right after the Serial Number input field -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Serial Number</label>
                                        <!-- Serial number field remains without old() -->
                                        <input type="text" name="serial_number" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-200 @error('serial_number') border-red-500 @enderror">
                                        @error('serial_number')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Specification</label>
                                        <textarea name="specification" rows="2" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">{{ old('specification') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div>
                        <h4 class="text-xl font-semibold mb-4">Financial Information</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                                <input type="text" name="vendor" value="{{ old('vendor') }}" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Date</label>
                                <input type="date" name="purchase_date" value="{{ old('purchase_date') }}" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Warranty Period</label>
                                <input type="date" name="warranty_period" value="{{ old('warranty_period') }}" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select name="category_id" required class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">
                                    <option value="">Select Product Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Expected Asset Life (Years)</label>
                                <input type="number" name="lifespan" min="0" step="1" value="{{ old('lifespan') }}" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Asset Photo</label>
                                <input type="file" name="photo" accept="image/*" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-200">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('assets.list') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-red-800 text-white rounded-md hover:bg-red-700">
                        Add Asset
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

<script>
    // Show modal if there's an error
    @if(session('showErrorModal'))
    document.getElementById('errorModal').classList.remove('hidden');
    document.getElementById('errorMessageText').textContent = "{{ session('errorMessage') }}";
    @endif

    function closeErrorModal() {
        document.getElementById('errorModal').classList.add('hidden');
    }

</script>
@endsection
