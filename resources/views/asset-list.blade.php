@extends('layouts.app')

@section('content')
<div class="flex-1 ml-80">
    <div class="p-6">
        <!-- Main Container -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">ALL ASSETS</h1>
                <div class="flex space-x-3">
                    <a href="{{ route('assets.create') }}" 
                       class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Add New Asset
                    </a>
                    <button onclick="openFullList()" 
                            class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center">
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assets as $asset)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                            <!-- Add this right before the closing </div> of the main container -->
                            
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
                            
                            <!-- Update the photo and QR code cells in the table -->
                            <td class="px-6 py-4 whitespace-nowrap">
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($asset->qr_code)
                                    <img src="{{ asset('storage/' . $asset->qr_code) }}" 
                                         alt="QR Code" 
                                         class="w-20 h-20 cursor-pointer hover:opacity-75 transition-opacity"
                                         onclick="openImageModal('{{ asset('storage/' . $asset->qr_code) }}')"
                                    >
                                @endif
                            </td>
                            
                            <!-- Add this to your existing script section -->
                            <script>
                                // ... existing openFullList and closeFullList functions ...
                                
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
                            </script>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->name ?? '' }}</td>
                            <!-- In the first table -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->category->name ?? '' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full inline-flex items-center
                                    @if($asset->status ?? '' == 'UNDER REPAIR') bg-yellow-100 text-yellow-800
                                    @elseif($asset->status ?? '' == 'IN USE') bg-green-100 text-green-800
                                    @elseif($asset->status ?? '' == 'DISPOSED') bg-red-100 text-red-800
                                    @elseif($asset->status ?? '' == 'UPGRADE') bg-blue-100 text-blue-800
                                    @elseif($asset->status ?? '' == 'PENDING DEPLOYMENT') bg-purple-100 text-purple-800
                                    @endif">
                                    {{ $asset->status ?? '' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->location ?? '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Remove the old enlarge button that was here -->
            </div>
        </div>
    </div>
</div>

<!-- Full Asset List Modal -->
<div id="fullListModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Complete Asset List</h2>
            <button onclick="closeFullList()" class="text-gray-600 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <!-- Full Asset List Table -->
        <div class="overflow-y-auto max-h-[60vh]">
            <table class="min-w-full divide-y divide-gray-200">
                <!-- Same table structure as above but with more rows -->
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specification</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warranty Period</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lifespan (Yrs)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Life Remaining</th>
                                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($assets as $asset)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <img src="{{ asset($asset->photo ?? 'images/no-image.png') }}" alt="Asset Photo" class="w-12 h-12 object-cover">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->name ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->category ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->location ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($asset->status ?? '' == 'UNDER REPAIR') bg-yellow-100 text-yellow-800
                                @elseif($asset->status ?? '' == 'IN USE') bg-green-100 text-green-800
                                @elseif($asset->status ?? '' == 'DISPOSED') bg-red-100 text-red-800
                                @elseif($asset->status ?? '' == 'UPGRADE') bg-blue-100 text-blue-800
                                @elseif($asset->status ?? '' == 'PENDING DEPLOYMENT') bg-purple-100 text-purple-800
                                @endif">
                                {{ $asset->status ?? '' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->description ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->model ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->serial_number ?? '' }}</td>
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

<script>
    function openFullList() {
        document.getElementById('fullListModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeFullList() {
        document.getElementById('fullListModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>
@endsection