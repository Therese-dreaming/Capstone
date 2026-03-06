@extends('layouts.borrowing-app')

@push('styles')
<style>
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-slideDown {
        animation: slideDown 0.4s ease-out;
    }
</style>
@endpush

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen" id="mainContent">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-lg shadow-lg p-5 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                        <pattern id="borrowed-pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                            <circle cx="20" cy="20" r="2" fill="white"/>
                        </pattern>
                        <rect x="0" y="0" width="100%" height="100%" fill="url(#borrowed-pattern)"/>
                    </svg>
                </div>
                <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white mb-0.5">Borrowed Assets</h1>
                            <p class="text-sm text-red-100">Track currently borrowed items and their status</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="px-4 py-2 bg-white rounded-lg shadow-md">
                            <div class="text-xl font-bold text-red-600">{{ $borrowedItems->total() }}</div>
                            <div class="text-xs text-gray-600 font-semibold uppercase">Borrowed</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <div id="successMessage" class="hidden mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-lg shadow-lg overflow-hidden animate-slideDown">
            <div class="p-4">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-green-900 mb-1">Success!</h3>
                        <p id="successMessageText" class="text-sm text-green-800"></p>
                    </div>
                    <button onclick="closeNotification('successMessage')" class="flex-shrink-0 text-green-600 hover:text-green-800 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-green-500 to-emerald-500"></div>
        </div>

        <!-- Error Message -->
        <div id="errorMessage" class="hidden mb-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-lg shadow-lg overflow-hidden animate-slideDown">
            <div class="p-4">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-red-900 mb-1">Error!</h3>
                        <p id="errorMessageText" class="text-sm text-red-800"></p>
                    </div>
                    <button onclick="closeNotification('errorMessage')" class="flex-shrink-0 text-red-600 hover:text-red-800 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-red-500 to-rose-500"></div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-4 border-l-4 border-red-600">
            <form method="GET" action="{{ route('borrowing.assets.borrowed') }}" class="space-y-3 md:space-y-0 md:flex md:items-end md:gap-3">
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
                               placeholder="Search by asset name, serial number, or borrower..." 
                               class="w-full pl-9 pr-3 py-2 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="md:w-56">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Category</label>
                    <select name="category" class="w-full px-3 py-2 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
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
                    <a href="{{ route('borrowing.assets.borrowed') }}" class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Borrowed Assets List -->
        @if($borrowedItems->count() > 0)
            <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-red-600 to-red-700 text-white">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-xs font-bold uppercase tracking-wide">Asset Name</th>
                                <th class="px-4 py-2.5 text-left text-xs font-bold uppercase tracking-wide">Serial Number</th>
                                <th class="px-4 py-2.5 text-left text-xs font-bold uppercase tracking-wide">Borrowed By</th>
                                <th class="px-4 py-2.5 text-left text-xs font-bold uppercase tracking-wide">Borrow Date</th>
                                <th class="px-4 py-2.5 text-left text-xs font-bold uppercase tracking-wide">Expected Return</th>
                                <th class="px-4 py-2.5 text-left text-xs font-bold uppercase tracking-wide">Purpose</th>
                                <th class="px-4 py-2.5 text-center text-xs font-bold uppercase tracking-wide">Status</th>
                                <th class="px-4 py-2.5 text-center text-xs font-bold uppercase tracking-wide">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($borrowedItems as $item)
                                @php
                                    $borrowing = $item->borrowing;
                                    $asset = $item->asset;
                                    $isOverdue = $borrowing->isOverdue();
                                @endphp
                                <tr class="hover:bg-red-50 transition-colors group">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-9 h-9 bg-gradient-to-br from-red-100 to-red-200 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-sm text-gray-800">{{ $asset->name }}</div>
                                                @if($asset->category)
                                                    <div class="text-xs text-gray-500 flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                                                        </svg>
                                                        {{ $asset->category->name }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="font-mono text-xs text-gray-700 bg-gray-100 px-2 py-1 rounded">{{ $asset->serial_number }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div>
                                            <div class="font-medium text-sm text-gray-800">{{ $borrowing->borrower->name }}</div>
                                            @if($borrowing->borrower->department)
                                                <div class="text-xs text-gray-500 mt-0.5">{{ $borrowing->borrower->department }}</div>
                                            @endif
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
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 {{ $isOverdue ? 'text-red-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm text-gray-700">{{ $borrowing->expected_return_date->format('M d, Y') }}</span>
                                        </div>
                                        @if($isOverdue)
                                            <div class="text-xs text-red-600 font-bold mt-0.5 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                Overdue by {{ abs($borrowing->getDaysRemaining()) }} day(s)
                                            </div>
                                        @else
                                            <div class="text-xs text-green-600 font-semibold mt-0.5">
                                                {{ $borrowing->getDaysRemaining() }} day(s) remaining
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="inline-flex items-center px-2.5 py-1 bg-gray-100 rounded-full">
                                            <span class="text-xs text-gray-700 font-medium">{{ $borrowing->purpose }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($isOverdue)
                                            <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-gradient-to-r from-red-500 to-red-600 text-white shadow-sm">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                                OVERDUE
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-sm">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                </svg>
                                                IN USE
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="openReturnModal({{ $borrowing->id }}, '{{ $asset->name }}')"
                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg shadow-sm hover:shadow-md transition-all">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Return
                                            </button>
                                            <button onclick="openMissingModal({{ $borrowing->id }}, '{{ $asset->name }}')"
                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white rounded-lg shadow-sm hover:shadow-md transition-all">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                Missing
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($borrowedItems->hasPages())
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        {{ $borrowedItems->appends(request()->except('page'))->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-10 text-center border-l-4 border-red-600">
                <div class="mb-4">
                    <div class="mx-auto w-16 h-16 bg-gradient-to-br from-red-100 to-red-200 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-1.5">No Borrowed Assets Found</h3>
                <p class="text-sm text-gray-600 mb-6 max-w-md mx-auto">
                    @if(request()->has('search') || request()->has('category'))
                        Try adjusting your filters to see more results, or clear all filters to view all borrowed assets.
                    @else
                        There are currently no borrowed assets in the system.
                    @endif
                </p>
                @if(request()->has('search') || request()->has('category'))
                    <a href="{{ route('borrowing.assets.borrowed') }}" class="inline-flex items-center px-6 py-2.5 text-sm bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
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

<!-- Mark as Missing Modal -->
<div id="missingModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-800">Mark Asset as Missing</h3>
                <button onclick="closeMissingModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mb-6">
                <div class="flex items-center gap-3 p-4 bg-yellow-50 rounded-lg border-2 border-yellow-400">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-yellow-800">Warning!</p>
                        <p class="text-sm text-yellow-700">This action will mark the asset as missing and make it unavailable for borrowing.</p>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Asset:</p>
                        <p id="missingAssetName" class="font-semibold text-gray-800"></p>
                    </div>
                </div>
            </div>

            <form id="missingForm" onsubmit="handleMissing(event)">
                <input type="hidden" id="missingBorrowingId" name="borrowing_id">
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Reason / Notes *</label>
                    <textarea id="missingNotes" name="notes" rows="3" required
                              class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 resize-none"
                              placeholder="Describe when and how the asset went missing..."></textarea>
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="closeMissingModal()" 
                            class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                        Mark as Missing
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Return Modal -->
<div id="returnModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-800">Return Asset</h3>
                <button onclick="closeReturnModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mb-6">
                <div class="flex items-center gap-3 p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Returning asset:</p>
                        <p id="returnAssetName" class="font-semibold text-gray-800"></p>
                    </div>
                </div>
            </div>

            <form id="returnForm" onsubmit="handleReturn(event)">
                <input type="hidden" id="returnBorrowingId" name="borrowing_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Condition on Return *</label>
                    <select id="returnCondition" name="condition" required class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">Select Condition</option>
                        <option value="Good">Good</option>
                        <option value="Fair">Fair</option>
                        <option value="Damaged">Damaged</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea id="returnNotes" name="notes" rows="3" 
                              class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"
                              placeholder="Any additional notes about the return..."></textarea>
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="closeReturnModal()" 
                            class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                        Confirm Return
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openReturnModal(borrowingId, assetName) {
        document.getElementById('returnBorrowingId').value = borrowingId;
        document.getElementById('returnAssetName').textContent = assetName;
        document.getElementById('returnModal').classList.remove('hidden');
    }

    function closeReturnModal() {
        document.getElementById('returnModal').classList.add('hidden');
        document.getElementById('returnForm').reset();
    }

    function openMissingModal(borrowingId, assetName) {
        document.getElementById('missingBorrowingId').value = borrowingId;
        document.getElementById('missingAssetName').textContent = assetName;
        document.getElementById('missingModal').classList.remove('hidden');
    }

    function closeMissingModal() {
        document.getElementById('missingModal').classList.add('hidden');
        document.getElementById('missingForm').reset();
    }

    function showNotification(type, message) {
        const successDiv = document.getElementById('successMessage');
        const errorDiv = document.getElementById('errorMessage');
        
        // Hide both first
        successDiv.classList.add('hidden');
        errorDiv.classList.add('hidden');
        
        if (type === 'success') {
            document.getElementById('successMessageText').textContent = message;
            successDiv.classList.remove('hidden');
            // Auto-hide after 5 seconds
            setTimeout(() => {
                successDiv.classList.add('hidden');
            }, 5000);
        } else {
            document.getElementById('errorMessageText').textContent = message;
            errorDiv.classList.remove('hidden');
            // Auto-hide after 8 seconds (longer for errors)
            setTimeout(() => {
                errorDiv.classList.add('hidden');
            }, 8000);
        }
        
        // Scroll to top to show notification
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    function closeNotification(id) {
        document.getElementById(id).classList.add('hidden');
    }

    async function handleReturn(event) {
        event.preventDefault();
        
        const borrowingId = document.getElementById('returnBorrowingId').value;
        const condition = document.getElementById('returnCondition').value;
        const notes = document.getElementById('returnNotes').value;

        if (!condition) {
            showNotification('error', 'Please select a condition before submitting.');
            return;
        }

        try {
            const response = await fetch('{{ route("borrowing.return") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    borrowing_id: borrowingId,
                    condition: condition,
                    notes: notes
                })
            });

            const data = await response.json();

            if (data.success) {
                closeReturnModal();
                showNotification('success', data.message);
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showNotification('error', data.message);
            }
        } catch (error) {
            showNotification('error', 'An error occurred. Please try again.');
            console.error(error);
        }
    }

    async function handleMissing(event) {
        event.preventDefault();
        
        const borrowingId = document.getElementById('missingBorrowingId').value;
        const notes = document.getElementById('missingNotes').value;

        if (!notes.trim()) {
            showNotification('error', 'Please provide a reason for marking this asset as missing.');
            return;
        }

        if (!confirm('Are you sure you want to mark this asset as missing? This action cannot be easily undone.')) {
            return;
        }

        try {
            const response = await fetch('{{ route("borrowing.mark-missing") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    borrowing_id: borrowingId,
                    notes: notes
                })
            });

            const data = await response.json();

            if (data.success) {
                closeMissingModal();
                showNotification('success', data.message || 'Asset marked as missing successfully.');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showNotification('error', data.message);
            }
        } catch (error) {
            showNotification('error', 'An error occurred. Please try again.');
            console.error(error);
        }
    }
</script>
@endsection
