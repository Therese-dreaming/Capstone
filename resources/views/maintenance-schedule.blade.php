@extends('layouts.app')

@section('content')
<div class="flex-1 p-8 ml-72">
    <h2 class="text-2xl font-semibold mb-6">SCHEDULE MAINTENANCE</h2>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        {{ session('success') }}
    </div>
    @endif

    @if(session('warning'))
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="confirmation-modal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Warning</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">This asset already has a scheduled maintenance. Do you want to overwrite it?</p>
                </div>
                <div class="items-center px-4 py-3">
                    <form action="{{ route('maintenance.schedule.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="asset_id" value="{{ session('form_data.asset_id') }}">
                        <input type="hidden" name="maintenance_task" value="{{ session('form_data.maintenance_task') }}">
                        <input type="hidden" name="technician_id" value="{{ session('form_data.technician_id') }}">
                        <input type="hidden" name="scheduled_date" value="{{ session('form_data.scheduled_date') }}">
                        <input type="hidden" name="confirm_completion" value="1">
                        <input type="hidden" name="confirm_overwrite" value="1">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Yes, Overwrite
                        </button>
                        <a href="{{ route('maintenance.schedule') }}" class="ml-3 px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 inline-block">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="flex gap-6">
        <!-- Left Column - Form -->
        <div class="flex-1 bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('maintenance.schedule.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Asset Category</label>
                    <select name="category_id" id="category_select" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">Select a category...</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Asset for Maintenance</label>
                    <div class="relative">
                        <input type="text" id="asset_search" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="Search by serial number..." autocomplete="off">
                        <input type="hidden" name="asset_id" id="asset_id">
                        <div id="search_results" class="absolute z-10 w-full mt-1 bg-white shadow-lg rounded-md hidden">
                        </div>
                    </div>
                    @error('asset_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assign Maintenance Task</label>
                    <input type="text" name="maintenance_task" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="Enter maintenance task">
                    @error('maintenance_task')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Schedule Date</label>
                    <input type="date" name="scheduled_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" min="{{ date('Y-m-d') }}">
                    @error('scheduled_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assign Technician</label>
                    <select name="technician_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">Select a technician...</option>
                        @foreach($technicians as $technician)
                        <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                        @endforeach
                    </select>
                    @error('technician_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="confirm_completion" id="confirm_completion" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                    <label for="confirm_completion" class="ml-2 block text-sm text-gray-700">Confirm</label>
                    @error('confirm_completion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="w-full bg-red-800 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        Submit
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Column - Asset Details -->
        <div class="flex-1 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Asset Details</h3>
            <div id="asset_details" class="space-y-4">
                <div class="text-center text-gray-500">
                    Select an asset to view details
                </div>
            </div>
        </div>
    </div>

    <script>
        let assets = [];

        document.getElementById('category_select').addEventListener('change', function() {
            const categoryId = this.value;
            const assetSearch = document.getElementById('asset_search');
            const searchResults = document.getElementById('search_results');
            const assetDetails = document.getElementById('asset_details');

            if (categoryId) {
                assetSearch.disabled = false;

                fetch(`/categories/${categoryId}/assets`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    assets = data;
                    assetSearch.value = '';
                    document.getElementById('asset_id').value = '';
                    searchResults.innerHTML = '';
                    assetDetails.innerHTML = '<div class="text-center text-gray-500">Search and select an asset</div>';
                })
                .catch(error => {
                    console.error('Error:', error);
                    assetSearch.value = 'Error loading assets';
                    assetSearch.disabled = true;
                });
            } else {
                assetSearch.disabled = true;
                assetSearch.value = '';
                document.getElementById('asset_id').value = '';
                searchResults.innerHTML = '';
                assets = [];
            }
        });

        document.getElementById('asset_search').addEventListener('input', function() {
            const searchResults = document.getElementById('search_results');
            const query = this.value.toLowerCase();

            if (query.length > 0) {
                const filtered = assets.filter(asset =>
                    asset.serial_number.toLowerCase().includes(query)
                );

                searchResults.innerHTML = filtered.map(asset => `
                    <div class="p-2 hover:bg-gray-100 cursor-pointer" 
                         onclick="selectAsset('${asset.id}', '${asset.serial_number}')">
                        ${asset.serial_number}
                    </div>
                `).join('');

                searchResults.classList.remove('hidden');
            } else {
                searchResults.classList.add('hidden');
            }
        });

        function selectAsset(id, serialNumber) {
            document.getElementById('asset_search').value = serialNumber;
            document.getElementById('asset_id').value = id;
            document.getElementById('search_results').classList.add('hidden');

            fetch(`/assets/${id}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(asset => {
                document.getElementById('asset_details').innerHTML = `
                    <div class="space-y-4">
                        <div class="flex justify-center">
                            <img src="${asset.photo_url || '/images/no-image.png'}" 
                                 alt="${asset.name}" 
                                 class="w-64 h-64 object-cover rounded-lg shadow-md">
                        </div>
                        <div class="space-y-2">
                            <p class="text-lg font-semibold">${asset.name}</p>
                            <p class="text-gray-600">Serial Number: ${asset.serial_number || 'N/A'}</p>
                            <p class="text-gray-600">Status: ${asset.status || 'N/A'}</p>
                        </div>
                    </div>
                `;
            });
        }

        // Close search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#asset_search')) {
                document.getElementById('search_results').classList.add('hidden');
            }
        });
    </script>
</div>
@endsection
