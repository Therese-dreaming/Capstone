@extends('layouts.app')

@section('content')
<div class="flex-1 ml-80">
    <div class="p-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">Asset History</h2>
                    <p class="text-gray-600">{{ $asset->name }} ({{ $asset->serial_number }})</p>
                </div>
                <button onclick="window.print()" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center print-hide">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print History
                </button>
            </div>

            <!-- Remove the first style block entirely and keep only one style block at the bottom -->
            
            <!-- Tab Buttons -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="grid grid-cols-4 w-full gap-1">
                    <button onclick="showTab('status')" class="tab-button !py-6 active bg-red-800 !text-white !font-bold" data-tab="status">
                        Status Changes
                    </button>
                    <button onclick="showTab('location')" class="tab-button !py-6" data-tab="location">
                        Location Changes
                    </button>
                    <button onclick="showTab('price')" class="tab-button !py-6" data-tab="price">
                        Price Changes
                    </button>
                    <button onclick="showTab('maintenance')" class="tab-button !py-6" data-tab="maintenance">
                        Maintenance & Repairs
                    </button>
                </nav>
            </div>

<!-- Remove the first style block and keep only one at the bottom -->

<style>
    .tab-button {
        @apply px-4 py-6 text-sm font-medium text-gray-500 
               hover:text-white hover:bg-red-700 
               border-b-2 border-transparent transition-all duration-200;
    }

    .tab-button.active {
        @apply border-red-900 text-white bg-red-800 font-bold !important;
    }
</style>

            <!-- Remove the first style block here -->

            <!-- Tab Contents -->
            <div id="status" class="tab-content">
                @include('partials.asset-history.status-changes', ['history' => $history])
            </div>
            <div id="location" class="tab-content hidden">
                @include('partials.asset-history.location-changes', ['history' => $history])
            </div>
            <div id="price" class="tab-content hidden">
                @include('partials.asset-history.price-changes', ['history' => $history])
            </div>
            <div id="maintenance" class="tab-content hidden">
                @include('partials.asset-history.maintenance-repairs', ['history' => $history])
            </div>
        </div>
    </div>
</div>

<style>
    .tab-button {
        @apply px-4 py-4 text-sm font-medium text-gray-500 
               hover:text-white hover:bg-red-700 
               border-b-2 border-transparent transition-all duration-200;
    }

    .tab-button.active {
        @apply border-red-900 text-white bg-red-800 font-bold;
    }

    @media print {

        .print-hide,
        .tab-button {
            display: none !important;
        }

        .tab-content {
            display: block !important;
        }

        .flex-1.ml-80 {
            margin-left: 0 !important;
        }
    }

</style>

<script>
    function showTab(tabId) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active class and all styling from all tabs
        document.querySelectorAll('.tab-button').forEach(tab => {
            tab.classList.remove('active');
            tab.classList.remove('bg-red-800');
            tab.classList.remove('!text-white');
            tab.classList.remove('!font-bold');
        });

        // Show selected tab content and activate tab with all styles
        document.getElementById(tabId).classList.remove('hidden');
        const activeTab = document.querySelector(`[data-tab="${tabId}"]`);
        activeTab.classList.add('active');
        activeTab.classList.add('bg-red-800');
        activeTab.classList.add('!text-white');
        activeTab.classList.add('!font-bold');
    }

    // Ensure the initial tab is properly styled
    document.addEventListener('DOMContentLoaded', function() {
        showTab('status');
    });
</script>
@endsection
