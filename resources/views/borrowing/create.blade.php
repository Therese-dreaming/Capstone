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
                        <pattern id="pattern-borrow" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                            <circle cx="20" cy="20" r="1" fill="white"/>
                        </pattern>
                    </defs>
                    <rect x="0" y="0" width="100%" height="100%" fill="url(#pattern-borrow)"/>
                </svg>
            </div>
            
            <div class="relative flex items-center gap-4">
                <div class="bg-white/20 p-3 rounded-lg backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">New Asset Borrowing</h1>
                    <p class="text-red-100 text-sm mt-0.5">Scan RFID to borrow assets</p>
                </div>
            </div>
        </div>

        <!-- Borrowing Form -->
        <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
            <div class="space-y-6">
                <!-- Search and Filter Section -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Search & Select Assets <span class="text-red-500">*</span>
                        </span>
                    </label>

                    <!-- Asset Selection Error -->
                    <div id="assetError" class="hidden mb-4 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-shake">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-sm font-semibold text-red-800 flex-1">Please select at least one asset</p>
                            <button type="button" onclick="hideAssetError()" class="text-red-500 hover:text-red-700">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Search and Category Filter Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <!-- Search Bar -->
                        <div class="md:col-span-2">
                            <div class="relative">
                                <input type="text" id="assetSearch" placeholder="Search by asset name or serial number..." 
                                       class="w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                                <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <select id="categoryFilter" class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                                <option value="">All Categories</option>
                                @foreach($assetsByCategory as $category)
                                    <option value="{{ $category->category_id }}">{{ $category->category->name }} ({{ $category->total }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Asset Grid (Hidden until search/filter) -->
                    <div id="assetGrid" class="hidden grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 max-h-[400px] overflow-y-auto p-2 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        @foreach($availableAssets as $asset)
                            <label class="asset-card cursor-pointer" data-asset-id="{{ $asset->id }}" 
                                   data-asset-name="{{ strtolower($asset->name) }}" 
                                   data-asset-serial="{{ strtolower($asset->serial_number) }}"
                                   data-category-id="{{ $asset->category_id }}">
                                <input type="checkbox" name="asset_ids[]" value="{{ $asset->id }}" class="asset-checkbox hidden">
                                <div class="bg-white border-2 border-gray-300 rounded-lg p-3 transition-all hover:border-red-400 hover:shadow-md h-full relative">
                                    <!-- Checkmark -->
                                    <div class="checkmark-circle hidden absolute -top-2 -right-2 w-6 h-6 bg-red-600 rounded-full flex items-center justify-center shadow-lg">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    
                                    <h3 class="font-bold text-gray-800 text-sm mb-1 truncate" title="{{ $asset->name }}">{{ $asset->name }}</h3>
                                    <p class="text-xs text-gray-500 font-mono mb-2 truncate">{{ $asset->serial_number }}</p>
                                    <div class="space-y-1">
                                        <p class="text-xs text-gray-600 truncate">
                                            <span class="font-medium">Cat:</span> {{ $asset->category->name ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs text-gray-600 truncate" title="{{ $asset->location ? $asset->location->full_location : 'N/A' }}">
                                            <span class="font-medium">Loc:</span> {{ $asset->location ? $asset->location->room_number : 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <!-- No Results Message -->
                    <div id="noResults" class="hidden text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-gray-600 text-sm">No assets found. Try a different search term or category.</p>
                    </div>

                    <!-- Search Prompt -->
                    <div id="searchPrompt" class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p class="text-gray-600 font-medium mb-1">Search for assets to borrow</p>
                        <p class="text-gray-500 text-sm">Use the search bar or category filter above</p>
                    </div>

                    <p class="text-sm text-gray-600 mt-3">
                        <span id="selectedCount" class="font-bold text-red-600">0</span> asset(s) selected
                    </p>
                </div>

                <hr class="border-gray-200">

                <!-- Borrowing Details -->
                <!-- Borrowing Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Purpose Dropdown -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                            Purpose <span class="text-red-500">*</span>
                        </label>
                        <select id="purpose" class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            <option value="">Select Purpose</option>
                            <option value="Class Use">Class Use</option>
                            <option value="Laboratory Activity">Laboratory Activity</option>
                            <option value="Research">Research</option>
                            <option value="Presentation">Presentation</option>
                            <option value="Meeting">Meeting</option>
                            <option value="Project">Project</option>
                            <option value="Exam/Assessment">Exam/Assessment</option>
                            <option value="Training">Training</option>
                            <option value="Others">Others (Specify in notes)</option>
                        </select>
                    </div>

                    <!-- Expected Return Date -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                            Expected Return Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="expected_return_date" 
                               value="{{ date('Y-m-d') }}"
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                    </div>

                    <!-- Notes (Full Width) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                            Additional Notes
                        </label>
                        <textarea id="notes" rows="3" placeholder="Any additional notes or details (required if purpose is 'Others')..."
                                  class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"></textarea>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm text-red-800 font-semibold mb-0.5">RFID Required</p>
                            <p class="text-sm text-red-700">You will need to scan your RFID card to complete the borrowing process.</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('borrowing.dashboard') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all text-center">
                        Cancel
                    </a>
                    <button type="button" onclick="openRfidModal()" 
                            class="px-8 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        Scan RFID to Borrow
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- RFID Scanning Modal -->
<div id="rfidModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="p-8">
            <div id="rfidReady" class="text-center">
                <div class="mb-6">
                    <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center animate-pulse">
                        <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Ready to Scan</h3>
                <p class="text-gray-600 mb-6">Please tap your RFID card to borrow assets</p>
                <input type="text" id="rfidInput" autocomplete="off" 
                       class="w-full px-4 py-3 border-2 border-red-500 rounded-lg text-center text-lg font-mono focus:ring-2 focus:ring-red-500"
                       placeholder="Waiting for RFID scan...">
            </div>

            <div id="rfidLoading" class="hidden text-center">
                <div class="mb-6">
                    <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-red-600 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Processing...</h3>
                <p class="text-gray-600">Please wait while we process your borrowing</p>
            </div>

            <div id="rfidSuccess" class="hidden text-center">
                <div class="mb-6">
                    <div class="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-green-600 mb-2">Success!</h3>
                <p id="successMessage" class="text-gray-700 mb-6"></p>
                <button onclick="closeRfidModal(); window.location.reload();" 
                        class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
                    Done
                </button>
            </div>

            <div id="rfidError" class="hidden text-center">
                <div class="mb-6">
                    <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-red-600 mb-2">Error</h3>
                <p id="errorMessage" class="text-gray-700 mb-6"></p>
                <button onclick="closeRfidModal()" 
                        class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition">
                    Try Again
                </button>
            </div>

            <div id="rfidRegistration" class="hidden">
                <div class="text-center mb-6">
                    <div class="mb-4">
                        <div class="mx-auto w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">First Time Scan</h3>
                    <p class="text-gray-600 mb-1">This RFID is not registered yet.</p>
                    <p class="text-sm text-gray-500 mb-4">RFID: <span id="newRfidNumber" class="font-mono font-semibold"></span></p>
                </div>

                <form onsubmit="handleRegistration(event)" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                        <input type="text" id="registrationName" required
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="Enter full name">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Department *</label>
                        <select id="registrationDepartment" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">Select Department</option>
                            <option value="ECE">ECE</option>
                            <option value="Grade School">Grade School</option>
                            <option value="Elementary">Elementary</option>
                            <option value="High School">High School</option>
                            <option value="Senior High School">Senior High School</option>
                            <option value="College">College</option>
                            <option value="SGS">SGS</option>
                        </select>
                    </div>

                    <button type="submit" 
                            class="w-full px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold rounded-lg transition shadow-md hover:shadow-lg">
                        Register and Borrow
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-gray-50 px-8 py-4 rounded-b-xl">
            <button onclick="closeRfidModal()" class="w-full text-sm text-gray-600 hover:text-gray-800 transition">
                Cancel
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<style>
    .asset-card input:checked + div {
        border-color: #dc2626;
        background: linear-gradient(to bottom right, #fef2f2, #fee2e2);
        box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.1), 0 2px 4px -1px rgba(220, 38, 38, 0.06);
    }

    .asset-card input:checked + div .checkmark-circle {
        display: flex !important;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }

    .animate-shake {
        animation: shake 0.5s;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedAssets = new Set();
    const assetGrid = document.getElementById('assetGrid');
    const noResults = document.getElementById('noResults');
    const searchPrompt = document.getElementById('searchPrompt');

    // Asset selection
    document.querySelectorAll('.asset-card').forEach(card => {
        card.addEventListener('click', function() {
            const checkbox = this.querySelector('.asset-checkbox');
            const assetId = checkbox.value;
            
            checkbox.checked = !checkbox.checked;
            
            if (checkbox.checked) {
                selectedAssets.add(assetId);
            } else {
                selectedAssets.delete(assetId);
            }
            
            updateSelectedCount();
            hideAssetError();
        });
    });

    // Search functionality
    document.getElementById('assetSearch').addEventListener('input', function(e) {
        filterAssets();
    });

    // Category filter
    document.getElementById('categoryFilter').addEventListener('change', function() {
        filterAssets();
    });

    function filterAssets() {
        const searchTerm = document.getElementById('assetSearch').value.trim().toLowerCase();
        const categoryId = document.getElementById('categoryFilter').value;

        // If nothing is searched or filtered, show prompt
        if (searchTerm === '' && categoryId === '') {
            assetGrid.classList.add('hidden');
            noResults.classList.add('hidden');
            searchPrompt.classList.remove('hidden');
            return;
        }

        // Show grid, hide prompt
        searchPrompt.classList.add('hidden');
        assetGrid.classList.remove('hidden');

        let visibleCount = 0;
        
        document.querySelectorAll('.asset-card').forEach(card => {
            const assetName = card.dataset.assetName;
            const assetSerial = card.dataset.assetSerial;
            const assetCategory = card.dataset.categoryId;

            const matchesSearch = searchTerm === '' || 
                                assetName.includes(searchTerm) || 
                                assetSerial.includes(searchTerm);
            const matchesCategory = categoryId === '' || assetCategory === categoryId;

            if (matchesSearch && matchesCategory) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (visibleCount === 0) {
            assetGrid.classList.add('hidden');
            noResults.classList.remove('hidden');
        } else {
            assetGrid.classList.remove('hidden');
            noResults.classList.add('hidden');
        }
    }

    function updateSelectedCount() {
        document.getElementById('selectedCount').textContent = selectedAssets.size;
    }

    window.hideAssetError = function() {
        document.getElementById('assetError').classList.add('hidden');
    };

    window.openRfidModal = function() {
        const purpose = document.getElementById('purpose').value.trim();
        const returnDate = document.getElementById('expected_return_date').value;

        if (selectedAssets.size === 0) {
            document.getElementById('assetError').classList.remove('hidden');
            document.getElementById('assetError').scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        if (!purpose) {
            alert('Please select a purpose for borrowing');
            document.getElementById('purpose').focus();
            return;
        }

        if (!returnDate) {
            alert('Please select expected return date');
            document.getElementById('expected_return_date').focus();
            return;
        }

        // Check if purpose is Others and notes is empty
        if (purpose === 'Others' && !document.getElementById('notes').value.trim()) {
            alert('Please provide details in notes when selecting "Others" as purpose');
            document.getElementById('notes').focus();
            return;
        }

        document.getElementById('rfidModal').classList.remove('hidden');
        resetRfidModal();
        document.getElementById('rfidInput').focus();
    };

    window.closeRfidModal = function() {
        document.getElementById('rfidModal').classList.add('hidden');
        resetRfidModal();
    };

    let currentRfidNumber = '';

    function resetRfidModal() {
        document.getElementById('rfidReady').classList.remove('hidden');
        document.getElementById('rfidLoading').classList.add('hidden');
        document.getElementById('rfidSuccess').classList.add('hidden');
        document.getElementById('rfidError').classList.add('hidden');
        document.getElementById('rfidRegistration').classList.add('hidden');
        document.getElementById('rfidInput').value = '';
        document.getElementById('registrationName').value = '';
        document.getElementById('registrationDepartment').value = '';
        currentRfidNumber = '';
    }

    // RFID Input handling
    document.getElementById('rfidInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const rfidNumber = this.value.trim();
            if (rfidNumber) {
                processBorrowing(rfidNumber);
            }
        }
    });

    async function processBorrowing(rfidNumber) {
        document.getElementById('rfidReady').classList.add('hidden');
        document.getElementById('rfidLoading').classList.remove('hidden');

        try {
            const response = await fetch('{{ route("borrowing.rfid-borrow") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    rfid_number: rfidNumber,
                    asset_ids: Array.from(selectedAssets),
                    purpose: document.getElementById('purpose').value,
                    expected_return_date: document.getElementById('expected_return_date').value,
                    notes: document.getElementById('notes').value
                })
            });

            const data = await response.json();

            document.getElementById('rfidLoading').classList.add('hidden');

            if (data.success) {
                document.getElementById('successMessage').textContent = 
                    `${data.borrower_name} (${data.borrower_id}) has borrowed ${data.asset_count} asset(s). Expected return: ${data.expected_return}`;
                document.getElementById('rfidSuccess').classList.remove('hidden');
            } else if (data.first_time) {
                // First time RFID scan - show registration form
                currentRfidNumber = rfidNumber;
                document.getElementById('newRfidNumber').textContent = rfidNumber;
                document.getElementById('rfidRegistration').classList.remove('hidden');
                document.getElementById('registrationName').focus();
            } else {
                document.getElementById('errorMessage').textContent = data.message;
                document.getElementById('rfidError').classList.remove('hidden');
            }
        } catch (error) {
            document.getElementById('rfidLoading').classList.add('hidden');
            document.getElementById('errorMessage').textContent = 'An error occurred. Please try again.';
            document.getElementById('rfidError').classList.remove('hidden');
        }
    }

    window.handleRegistration = async function(event) {
        event.preventDefault();
        
        const name = document.getElementById('registrationName').value.trim();
        const department = document.getElementById('registrationDepartment').value;

        if (!name || !department) {
            alert('Please fill in all required fields');
            return;
        }

        // Hide form, show loading
        document.getElementById('rfidRegistration').classList.add('hidden');
        document.getElementById('rfidLoading').classList.remove('hidden');

        try {
            const response = await fetch('{{ route("borrowing.register-and-borrow") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    rfid_number: currentRfidNumber,
                    name: name,
                    department: department,
                    asset_ids: Array.from(selectedAssets),
                    purpose: document.getElementById('purpose').value,
                    expected_return_date: document.getElementById('expected_return_date').value,
                    notes: document.getElementById('notes').value
                })
            });

            const data = await response.json();

            document.getElementById('rfidLoading').classList.add('hidden');

            if (data.success) {
                document.getElementById('successMessage').textContent = 
                    `${data.borrower_name} (${data.borrower_id}) has been registered and borrowed ${data.asset_count} asset(s). Expected return: ${data.expected_return}`;
                document.getElementById('rfidSuccess').classList.remove('hidden');
            } else {
                document.getElementById('errorMessage').textContent = data.message;
                document.getElementById('rfidError').classList.remove('hidden');
            }
        } catch (error) {
            document.getElementById('rfidLoading').classList.add('hidden');
            document.getElementById('errorMessage').textContent = 'An error occurred during registration. Please try again.';
            document.getElementById('rfidError').classList.remove('hidden');
        }
    };
});
</script>
@endsection
