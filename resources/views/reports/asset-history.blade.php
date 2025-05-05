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
            </div>

            <!-- Remove the first style block entirely and keep only one style block at the bottom -->
            
            <!-- Tab Buttons -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="grid grid-cols-5 w-full gap-1">
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
                        Maintenance
                    </button>
                    <button onclick="showTab('repairs')" class="tab-button !py-6" data-tab="repairs">
                        Repairs
                    </button>
                </nav>
            </div>

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
                @include('partials.asset-history.maintenance', ['history' => $history])
            </div>
            <div id="repairs" class="tab-content hidden">
                @include('partials.asset-history.repairs', ['history' => $history])
            </div>
        </div>
    </div>
</div>

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
