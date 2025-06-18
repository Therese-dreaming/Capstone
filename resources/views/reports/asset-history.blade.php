@extends('layouts.app')

@section('content')
<div class="flex-1 px-4 py-6 md:p-6">
    <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold">Asset History</h2>
                <p class="text-gray-600">{{ $asset->name }} ({{ $asset->serial_number }})</p>
            </div>
        </div>

        <!-- Remove the first style block entirely and keep only one style block at the bottom -->
        
        <!-- Tab Buttons -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 w-full gap-1">
                <button onclick="showTab('timeline')" class="tab-button !py-6 active bg-red-800 !text-white !font-bold" data-tab="timeline">
                    Timeline
                </button>
                <button onclick="showTab('status')" class="tab-button !py-6" data-tab="status">
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
        <div id="timeline" class="tab-content">
            <div class="mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <!-- Date Filter -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="text-lg font-semibold mb-3">Filter Timeline by Date</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                <input type="date" id="start_date" value="{{ request()->get('start_date') }}" class="w-full h-9 px-3 py-0 text-sm rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                <input type="date" id="end_date" value="{{ request()->get('end_date') }}" class="w-full h-9 px-3 py-0 text-sm rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200">
                            </div>
                            <div class="flex items-end">
                                <button onclick="filterTimeline()" class="bg-red-800 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out mr-2">
                                    Apply Filter
                                </button>
                                <button onclick="resetFilter()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    @php
                        // Combine all history records into a single collection
                        $allEvents = collect();
                        
                        // Add status changes
                        if(isset($history['STATUS'])) {
                            foreach($history['STATUS'] as $record) {
                                $allEvents->push([
                                    'date' => $record->created_at,
                                    'type' => 'status',
                                    'icon' => 'status-icon',
                                    'color' => 'blue',
                                    'title' => 'Status Changed',
                                    'from' => $record->old_value,
                                    'to' => $record->new_value,
                                    'user' => $record->user->name,
                                    'remarks' => $record->remarks
                                ]);
                            }
                        }
                        
                        // Add location changes
                        if(isset($history['LOCATION'])) {
                            foreach($history['LOCATION'] as $record) {
                                $allEvents->push([
                                    'date' => $record->created_at,
                                    'type' => 'location',
                                    'icon' => 'location-icon',
                                    'color' => 'green',
                                    'title' => 'Location Changed',
                                    'from' => $record->old_value,
                                    'to' => $record->new_value,
                                    'user' => $record->user->name,
                                    'remarks' => $record->remarks
                                ]);
                            }
                        }
                        
                        // Add price changes
                        if(isset($history['PRICE'])) {
                            foreach($history['PRICE'] as $record) {
                                $allEvents->push([
                                    'date' => $record->created_at,
                                    'type' => 'price',
                                    'icon' => 'price-icon',
                                    'color' => 'orange',
                                    'title' => 'Price Updated',
                                    'from' => $record->old_value,
                                    'to' => $record->new_value,
                                    'user' => $record->user->name,
                                    'remarks' => $record->remarks
                                ]);
                            }
                        }
                        
                        // Add repair records
                        if(isset($history['REPAIR'])) {
                            foreach($history['REPAIR'] as $record) {
                                // Extract ticket number
                                preg_match('/Ticket: (REQ-\d{8}-\d{4})/', $record->remarks, $matches);
                                $ticketNo = $matches[1] ?? null;
                                
                                // Extract issue
                                preg_match('/Issue: (.+?)\n/', $record->remarks . "\n", $matches);
                                $issue = $matches[1] ?? $record->old_value ?? 'N/A';
                                
                                $allEvents->push([
                                    'date' => $record->created_at,
                                    'type' => 'repair',
                                    'icon' => 'repair-icon',
                                    'color' => 'red',
                                    'title' => 'Repair Request',
                                    'ticket' => $ticketNo,
                                    'issue' => $issue,
                                    'user' => $record->user->name ?? 'N/A',
                                    'remarks' => $record->remarks
                                ]);
                            }
                        }
                        
                        // Add maintenance records
                        if(isset($assetMaintenances)) {
                            foreach($assetMaintenances as $maintenance) {
                                $tasks = is_array($maintenance->maintenance_task) ? $maintenance->maintenance_task : json_decode($maintenance->maintenance_task, true);
                                $taskList = is_array($tasks) ? implode(', ', $tasks) : $tasks;
                                
                                $allEvents->push([
                                    'date' => $maintenance->scheduled_date,
                                    'type' => 'maintenance',
                                    'icon' => 'maintenance-icon',
                                    'color' => 'purple',
                                    'title' => 'Maintenance Performed',
                                    'lab' => 'Laboratory ' . $maintenance->lab_number,
                                    'tasks' => $taskList,
                                    'technician' => $maintenance->technician->name,
                                    'status' => $maintenance->status
                                ]);
                            }
                        }
                        
                        // Sort all events by date (newest first)
                        $allEvents = $allEvents->sortByDesc('date');
                        
                        // Apply date filtering if provided
                        $startDate = request()->get('start_date');
                        $endDate = request()->get('end_date');
                        
                        if ($startDate || $endDate) {
                            $allEvents = $allEvents->filter(function($event) use ($startDate, $endDate) {
                                $eventDate = $event['date'];
                                
                                if ($startDate && $eventDate < \Carbon\Carbon::parse($startDate)->startOfDay()) {
                                    return false;
                                }
                                
                                if ($endDate && $eventDate > \Carbon\Carbon::parse($endDate)->endOfDay()) {
                                    return false;
                                }
                                
                                return true;
                            });
                        }
                        
                        // Paginate the events (10 per page)
                        $perPage = 10;
                        $currentPage = request()->get('page', 1);
                        $totalEvents = $allEvents->count();
                        $totalPages = ceil($totalEvents / $perPage);
                        
                        // Get events for current page
                        $offset = ($currentPage - 1) * $perPage;
                        $currentPageEvents = $allEvents->slice($offset, $perPage);
                        
                        // Group events by year and month for the timeline
                        $eventsByYearMonth = $currentPageEvents->groupBy(function($event) {
                            return $event['date']->format('Y-m');
                        });
                    @endphp
                    
                    <div class="timeline-container" id="timeline-container">
                        @forelse($eventsByYearMonth as $yearMonth => $events)
                            @php
                                $firstEvent = $events->first();
                                $monthYear = $firstEvent['date']->format('F Y');
                            @endphp
                            
                            <div class="timeline-month">
                                <div class="timeline-month-header">
                                    <h3 class="text-xl font-bold text-gray-800">{{ $monthYear }}</h3>
                                </div>
                                
                                <div class="timeline-items">
                                    @foreach($events as $event)
                                        <div class="timeline-item">
                                            <div class="timeline-item-point" style="background-color: {{ $event['color'] === 'blue' ? '#3b82f6' : ($event['color'] === 'green' ? '#10b981' : ($event['color'] === 'orange' ? '#f97316' : ($event['color'] === 'red' ? '#ef4444' : '#8b5cf6'))) }};"></div>
                                            <div class="timeline-item-content">
                                                <div class="timeline-item-date">{{ $event['date']->format('M d, Y - h:i A') }}</div>
                                                <div class="timeline-item-title" style="color: {{ $event['color'] === 'blue' ? '#3b82f6' : ($event['color'] === 'green' ? '#10b981' : ($event['color'] === 'orange' ? '#f97316' : ($event['color'] === 'red' ? '#ef4444' : '#8b5cf6'))) }};">{{ $event['title'] }}</div>
                                                
                                                <div class="timeline-item-details">
                                                    @switch($event['type'])
                                                        @case('status')
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <span class="font-medium">From:</span>
                                                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                                                    @switch(strtoupper($event['from']))
                                                                        @case('UNDER REPAIR')
                                                                            bg-yellow-100 text-yellow-800
                                                                            @break
                                                                        @case('IN USE')
                                                                            bg-green-100 text-green-800
                                                                            @break
                                                                        @case('DISPOSED')
                                                                            bg-red-100 text-red-800
                                                                            @break
                                                                        @case('UPGRADE')
                                                                            bg-blue-100 text-blue-800
                                                                            @break
                                                                        @case('PULLED OUT')
                                                                            bg-orange-100 text-orange-800
                                                                            @break
                                                                        @default
                                                                            bg-gray-100 text-gray-800
                                                                    @endswitch">
                                                                    {{ $event['from'] }}
                                                                </span>
                                                            </div>
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <span class="font-medium">To:</span>
                                                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                                                    @switch(strtoupper($event['to']))
                                                                        @case('UNDER REPAIR')
                                                                            bg-yellow-100 text-yellow-800
                                                                            @break
                                                                        @case('IN USE')
                                                                            bg-green-100 text-green-800
                                                                            @break
                                                                        @case('DISPOSED')
                                                                            bg-red-100 text-red-800
                                                                            @break
                                                                        @case('UPGRADE')
                                                                            bg-blue-100 text-blue-800
                                                                            @break
                                                                        @case('PULLED OUT')
                                                                            bg-orange-100 text-orange-800
                                                                            @break
                                                                        @default
                                                                            bg-gray-100 text-gray-800
                                                                    @endswitch">
                                                                    {{ $event['to'] }}
                                                                </span>
                                                            </div>
                                                            @break
                                                            
                                                        @case('location')
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <span class="font-medium">From:</span>
                                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                                    {{ $event['from'] }}
                                                                </span>
                                                            </div>
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <span class="font-medium">To:</span>
                                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                                    {{ $event['to'] }}
                                                                </span>
                                                            </div>
                                                            @break
                                                            
                                                        @case('price')
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <span class="font-medium">From:</span>
                                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">
                                                                    ₱{{ number_format($event['from'], 2) }}
                                                                </span>
                                                            </div>
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <span class="font-medium">To:</span>
                                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">
                                                                    ₱{{ number_format($event['to'], 2) }}
                                                                </span>
                                                            </div>
                                                            @break
                                                            
                                                        @case('repair')
                                                            @if(isset($event['ticket']))
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <span class="font-medium">Ticket:</span>
                                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                                    {{ $event['ticket'] }}
                                                                </span>
                                                            </div>
                                                            @endif
                                                            <div class="mb-1">
                                                                <span class="font-medium">Issue:</span>
                                                                <span class="text-gray-700">{{ $event['issue'] }}</span>
                                                            </div>
                                                            @break
                                                            
                                                        @case('maintenance')
                                                            <div class="mb-1">
                                                                <span class="font-medium">Laboratory:</span>
                                                                <span class="text-gray-700">{{ $event['lab'] }}</span>
                                                            </div>
                                                            <div class="mb-1">
                                                                <span class="font-medium">Tasks:</span>
                                                                <span class="text-gray-700">{{ $event['tasks'] }}</span>
                                                            </div>
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <span class="font-medium">Status:</span>
                                                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                                    {{ $event['status'] === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                                    {{ ucfirst($event['status']) }}
                                                                </span>
                                                            </div>
                                                            @break
                                                    @endswitch
                                                    
                                                    @if(isset($event['remarks']) && $event['remarks'])
                                                        <div class="mt-2 pt-2 border-t border-gray-200">
                                                            <span class="font-medium">Remarks:</span>
                                                            <span class="text-gray-700">{{ $event['remarks'] }}</span>
                                                        </div>
                                                    @endif
                                                    
                                                    <div class="mt-2 text-sm text-gray-500">
                                                        @if($event['type'] != 'maintenance')
                                                            <span>Changed by: {{ $event['user'] }}</span>
                                                        @else
                                                            <span>Technician: {{ $event['technician'] }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                                <p class="text-gray-500">No history records found for this asset.</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <!-- Pagination Controls -->
                    @if($totalPages > 1)
                        <div class="mt-8 flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ ($currentPage - 1) * $perPage + 1 }} to {{ min($currentPage * $perPage, $totalEvents) }} of {{ $totalEvents }} events
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($currentPage > 1)
                                    <a href="?page={{ $currentPage - 1 }}&start_date={{ request()->get('start_date') }}&end_date={{ request()->get('end_date') }}&active_tab={{ request()->get('active_tab', 'timeline') }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 transition-colors">
                                        Previous
                                    </a>
                                @endif
                                
                                @for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                                    @if($i == $currentPage)
                                        <span class="px-3 py-2 text-sm font-medium text-white bg-red-800 border border-red-800 rounded-md">
                                            {{ $i }}
                                        </span>
                                    @else
                                        <a href="?page={{ $i }}&start_date={{ request()->get('start_date') }}&end_date={{ request()->get('end_date') }}&active_tab={{ request()->get('active_tab', 'timeline') }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 transition-colors">
                                            {{ $i }}
                                        </a>
                                    @endif
                                @endfor
                                
                                @if($currentPage < $totalPages)
                                    <a href="?page={{ $currentPage + 1 }}&start_date={{ request()->get('start_date') }}&end_date={{ request()->get('end_date') }}&active_tab={{ request()->get('active_tab', 'timeline') }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 transition-colors">
                                        Next
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div id="status" class="tab-content hidden">
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

<style>
    .timeline-container {
        padding: 20px 0;
    }
    
    .timeline-month {
        margin-bottom: 40px;
    }
    
    .timeline-month-header {
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .timeline-items {
        position: relative;
    }
    
    .timeline-items:before {
        content: '';
        position: absolute;
        left: 7px;
        top: 0;
        height: 100%;
        width: 2px;
        background-color: #e5e7eb;
    }
    
    .timeline-item {
        position: relative;
        padding-left: 30px;
        margin-bottom: 25px;
    }
    
    .timeline-item:last-child {
        margin-bottom: 0;
    }
    
    .timeline-item-point {
        position: absolute;
        left: 0;
        top: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        z-index: 1;
    }
    
    .timeline-item-content {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
    }
    
    .timeline-item-date {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 8px;
    }
    
    .timeline-item-title {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 12px;
    }
    
    .timeline-item-details {
        font-size: 0.875rem;
    }
</style>

<script>
    function showTab(tabId) {
        // Update URL with active_tab parameter and redirect
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('active_tab', tabId);
        
        // Redirect to the new URL so the server can see the parameter
        window.location.href = currentUrl.toString();
    }

    // Date filtering functionality
    function filterTimeline() {
        const startDateInput = document.getElementById('start_date').value;
        const endDateInput = document.getElementById('end_date').value;
        
        // Validate dates
        if (!startDateInput && !endDateInput) {
            alert('Please select at least one date to filter');
            return;
        }
        
        // Build the filter URL with page reset to 1
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('page', '1');
        
        if (startDateInput) {
            currentUrl.searchParams.set('start_date', startDateInput);
        } else {
            currentUrl.searchParams.delete('start_date');
        }
        
        if (endDateInput) {
            currentUrl.searchParams.set('end_date', endDateInput);
        } else {
            currentUrl.searchParams.delete('end_date');
        }
        
        // Preserve active_tab parameter
        const activeTab = currentUrl.searchParams.get('active_tab') || 'timeline';
        currentUrl.searchParams.set('active_tab', activeTab);
        
        // Redirect to filtered URL
        window.location.href = currentUrl.toString();
    }
    
    function resetFilter() {
        // Reset to page 1 and clear all filters
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.delete('page');
        currentUrl.searchParams.delete('start_date');
        currentUrl.searchParams.delete('end_date');
        
        // Preserve active_tab parameter
        const activeTab = currentUrl.searchParams.get('active_tab') || 'timeline';
        currentUrl.searchParams.set('active_tab', activeTab);
        
        window.location.href = currentUrl.toString();
    }

    // Initialize the correct tab based on URL parameter
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('active_tab') || 'timeline';
        
        // Hide all tab contents first
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
        document.getElementById(activeTab).classList.remove('hidden');
        const activeTabElement = document.querySelector(`[data-tab="${activeTab}"]`);
        activeTabElement.classList.add('active');
        activeTabElement.classList.add('bg-red-800');
        activeTabElement.classList.add('!text-white');
        activeTabElement.classList.add('!font-bold');
    });
</script>
@endsection
