@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Page Header with Background Design -->
    <div class="mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center">
                <div class="bg-white/20 p-4 rounded-full backdrop-blur-sm mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">All Completed Maintenance History</h1>
                    <p class="text-red-100 text-lg">Track and review all completed maintenance tasks</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ auth()->check() && auth()->user()->group_id == 1 ? url('/dashboard') : url('/secretary-dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Dashboard
        </a>
    </div>

    <!-- Date Range Filter Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-800 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h2 class="text-lg font-semibold text-gray-900">Filter by Date Range</h2>
        </div>
        
        <form action="{{ route('user.maintenance.history') }}" method="GET" class="mb-4" id="dateFilterForm">
            <div class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-4">
                <div class="flex items-center space-x-2 w-full sm:w-auto">
                    <label for="start_date" class="text-sm font-medium text-gray-700 flex-shrink-0">From:</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()" class="form-input h-10 w-full md:w-auto px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                </div>
                <div class="flex items-center space-x-2 w-full sm:w-auto">
                    <label for="end_date" class="text-sm font-medium text-gray-700 flex-shrink-0">To:</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()" class="form-input h-10 w-full md:w-auto px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                </div>
                @if(request('start_date') || request('end_date'))
                    <a href="{{ route('user.maintenance.history') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- Date Range Info (if filtered) --}}
        @if(request('start_date') || request('end_date'))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-800 text-center sm:text-left">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Showing results from
            <span class="font-semibold">{{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') : 'the beginning' }}</span>
            to
            <span class="font-semibold">{{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') : 'present' }}</span>
        </div>
        @endif
    </div>
    
    @if($maintenance->count() > 0)
        <!-- Results Summary -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="text-lg font-semibold text-gray-900">Maintenance History</h2>
                <span class="ml-3 px-3 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">{{ $maintenance->total() }} completed tasks</span>
            </div>

            {{-- Table view for larger screens --}}
            <div class="overflow-x-auto hidden sm:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($maintenance as $item)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->location ? $item->location->full_location : 'Unknown Location' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ is_array($item->maintenance_task) ? implode(', ', $item->maintenance_task) : $item->maintenance_task }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($item->completed_at)->format('M j, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Card view for mobile screens --}}
            <div class="sm:hidden space-y-4">
                @php
                    $currentDate = null;
                @endphp
                @foreach($maintenance as $item)
                    @php
                        $itemDate = \Carbon\Carbon::parse($item->completed_at)->format('M j, Y');
                    @endphp
                    @if ($itemDate != $currentDate)
                        <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-3 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-800 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $itemDate }}
                        </h3>
                        @php
                            $currentDate = $itemDate;
                        @endphp
                    @endif
                    <div class="bg-gray-50 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200 border-l-4 border-green-500">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-gray-900">{{ $item->location ? $item->location->full_location : 'Unknown Location' }}</span>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        </div>
                        <div class="text-sm text-gray-600 mb-2">Task: {{ is_array($item->maintenance_task) ? implode(', ', $item->maintenance_task) : $item->maintenance_task }}</div>
                        <div class="text-sm text-gray-500">Completed: {{ $itemDate }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($maintenance->lastPage() > 1)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between">
                    <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                        Showing {{ $maintenance->firstItem() ?? 0 }} to {{ $maintenance->lastItem() ?? 0 }} of {{ $maintenance->total() }} results
                    </div>
                    <div class="flex items-center space-x-2">
                        {{ $maintenance->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="bg-gray-100 rounded-full p-4 w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No maintenance history found</h3>
            <p class="text-gray-500 mb-4">You have not completed any maintenance tasks at this time.</p>
            <div class="flex justify-center space-x-3">
                <a href="{{ auth()->check() && auth()->user()->group_id == 1 ? url('/dashboard') : url('/secretary-dashboard') }}" class="inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const dateFilterForm = document.getElementById('dateFilterForm');

        if (startDateInput && endDateInput && dateFilterForm) {
            startDateInput.addEventListener('change', function() {
                dateFilterForm.submit();
            });

            endDateInput.addEventListener('change', function() {
                dateFilterForm.submit();
            });
        }

        // Add hover effects for table rows
        const tableRows = document.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', () => {
                row.classList.add('bg-gray-50');
            });
            row.addEventListener('mouseleave', () => {
                row.classList.remove('bg-gray-50');
            });
        });
    });
</script>
@endpush