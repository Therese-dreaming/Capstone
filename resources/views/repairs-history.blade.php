@extends('layouts.app')

@section('content')
<div class="flex-1 p-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">All Completed Repairs History</h1>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <a href="{{ auth()->check() && auth()->user()->group_id == 1 ? url('/dashboard') : url('/secretary-dashboard') }}" class="text-red-600 hover:text-red-800 text-sm font-medium mb-4 inline-block">Back to Dashboard</a>
        
        {{-- Date Range Filter --}}
        <form action="{{ route('repairs.history') }}" method="GET" class="mb-6" id="dateFilterForm">
            <div class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <div class="flex items-center space-x-2 w-full sm:w-auto">
                    <label for="start_date" class="text-sm font-medium text-gray-700 flex-shrink-0">From:</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()" class="form-input h-9 w-full md:w-auto px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                </div>
                <div class="flex items-center space-x-2 w-full sm:w-auto">
                    <label for="end_date" class="text-sm font-medium text-gray-700 flex-shrink-0">To:</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()" class="form-input h-9 w-full md:w-auto px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                </div>
                @if(request('start_date') || request('end_date'))
                    <a href="{{ route('repairs.history') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center justify-center w-full sm:w-auto text-sm">Reset</a>
                @endif
            </div>
        </form>

        {{-- Date Range Info (if filtered) --}}
        @if(request('start_date') || request('end_date'))
        <div class="mb-4 text-sm text-gray-600 text-center sm:text-left">
            Showing results from
            {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') : 'the beginning' }}
            to
            {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') : 'present' }}
        </div>
        @endif
        
        @if($repairs->count() > 0)
            {{-- Table view for larger screens --}}
            <div class="overflow-x-auto hidden sm:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($repairs as $repair)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <a href="{{ url('assets?search=' . urlencode($repair->asset->name)) }}" class="font-bold text-red-600 hover:underline">{{ $repair->asset->name }}</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $repair->issue }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($repair->completed_at)->format('M j, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
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
                @foreach($repairs as $repair)
                    @php
                        $itemDate = \Carbon\Carbon::parse($repair->completed_at)->format('M j, Y');
                    @endphp
                    @if ($itemDate != $currentDate)
                        <h2 class="text-lg font-semibold text-gray-800 mt-4 mb-2">{{ $itemDate }}</h2>
                        @php
                            $currentDate = $itemDate;
                        @endphp
                    @endif
                    <div class="bg-gray-50 rounded-lg p-4 shadow">
                        <div class="text-sm font-medium text-gray-500">Asset: <a href="{{ url('assets?search=' . urlencode($repair->asset->name)) }}" class="font-bold text-red-600 hover:underline">{{ $repair->asset->name }}</a></div>
                        <div class="text-sm text-gray-500 mt-1">Issue: {{ $repair->issue }}</div>
                        <div class="text-sm text-gray-500 mt-1">Completion Date: {{ $itemDate }}</div>
                        <div class="text-sm mt-1">Status: <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span></div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($repairs->hasPages())
            <div class="mt-6">
                <div class="flex flex-col sm:flex-row items-center justify-between">
                    <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                        Showing {{ $repairs->firstItem() ?? 0 }} to {{ $repairs->lastItem() ?? 0 }} of {{ $repairs->total() }} results
                    </div>
                    <div class="flex items-center space-x-2">
                        {{ $repairs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
            @endif
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No repairs history</h3>
                <p class="mt-1 text-sm text-gray-500">You have not completed any repair tasks at this time.</p>
            </div>
        @endif
    </div>
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
    });
</script>
@endpush