@extends('layouts.borrowing-app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen" id="mainContent">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-lg shadow-lg p-5 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                        <pattern id="history-pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                            <circle cx="20" cy="20" r="2" fill="white"/>
                        </pattern>
                        <rect x="0" y="0" width="100%" height="100%" fill="url(#history-pattern)"/>
                    </svg>
                </div>
                <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white mb-0.5">Borrowing History</h1>
                            <p class="text-sm text-red-100">View past borrowing records</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="px-4 py-2 bg-white rounded-lg shadow-md">
                            <div class="text-xl font-bold text-red-600">{{ $borrowings->total() }}</div>
                            <div class="text-xs text-gray-600 font-semibold uppercase">Records</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-4 border-l-4 border-red-600">
            <form method="GET" action="{{ route('borrowing.history') }}" class="space-y-3 md:space-y-0 md:flex md:items-end md:gap-3">
                <!-- Search -->
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search by borrower name or asset..." 
                               class="w-full pl-9 pr-3 py-2 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="md:w-56">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Status</label>
                    <select name="status" class="w-full px-3 py-2 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                        <option value="">All Records</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                        <option value="missing" {{ request('status') == 'missing' ? 'selected' : '' }}>Missing</option>
                        <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 text-sm bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter
                        </span>
                    </button>
                    <a href="{{ route('borrowing.history') }}" class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- History List -->
        @if($borrowings->count() > 0)
            <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-red-600 to-red-700 text-white">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-xs font-bold uppercase tracking-wide">Borrower</th>
                                <th class="px-4 py-2.5 text-left text-xs font-bold uppercase tracking-wide">Assets</th>
                                <th class="px-4 py-2.5 text-left text-xs font-bold uppercase tracking-wide">Borrow Date</th>
                                <th class="px-4 py-2.5 text-left text-xs font-bold uppercase tracking-wide">Return Date</th>
                                <th class="px-4 py-2.5 text-left text-xs font-bold uppercase tracking-wide">Purpose</th>
                                <th class="px-4 py-2.5 text-center text-xs font-bold uppercase tracking-wide">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($borrowings as $borrowing)
                                <tr class="hover:bg-red-50 transition-colors group">
                                    <td class="px-4 py-3">
                                        <div>
                                            <div class="font-semibold text-sm text-gray-800">{{ $borrowing->borrower->name }}</div>
                                            @if($borrowing->borrower->department)
                                                <div class="text-xs text-gray-500 mt-0.5">{{ $borrowing->borrower->department }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="space-y-1">
                                            @foreach($borrowing->items as $item)
                                                <div class="flex items-center gap-2">
                                                    <div class="w-7 h-7 bg-gradient-to-br from-red-100 to-red-200 rounded flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-800">{{ $item->asset->name }}</div>
                                                        <div class="text-xs text-gray-500">{{ $item->asset->serial_number }}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="text-sm text-gray-700">{{ $borrowing->borrow_date->format('M d, Y') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($borrowing->actual_return_date)
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-sm text-gray-700">{{ $borrowing->actual_return_date->format('M d, Y') }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="inline-flex items-center px-2.5 py-1 bg-gray-100 rounded-full">
                                            <span class="text-xs text-gray-700 font-medium">{{ $borrowing->purpose }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($borrowing->status === 'returned')
                                            <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-gradient-to-r from-green-500 to-green-600 text-white shadow-sm">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                RETURNED
                                            </span>
                                        @elseif($borrowing->status === 'missing' || $borrowing->status === 'lost')
                                            <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-gradient-to-r from-red-500 to-red-600 text-white shadow-sm">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                                {{ strtoupper($borrowing->status) }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-gradient-to-r from-gray-500 to-gray-600 text-white shadow-sm">
                                                {{ strtoupper($borrowing->status) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($borrowings->hasPages())
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        {{ $borrowings->appends(request()->except('page'))->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-10 text-center border-l-4 border-red-600">
                <div class="mb-4">
                    <div class="mx-auto w-16 h-16 bg-gradient-to-br from-red-100 to-red-200 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-1.5">No History Found</h3>
                <p class="text-sm text-gray-600 mb-6 max-w-md mx-auto">
                    @if(request()->has('search') || request()->has('status'))
                        Try adjusting your filters to see more results, or clear all filters to view all records.
                    @else
                        There are currently no borrowing history records in the system.
                    @endif
                </p>
                @if(request()->has('search') || request()->has('status'))
                    <a href="{{ route('borrowing.history') }}" class="inline-flex items-center px-6 py-2.5 text-sm bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear All Filters
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
