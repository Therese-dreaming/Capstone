@extends('layouts.borrowing-app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('borrowing.reports.by-asset') }}" 
           class="inline-flex items-center px-4 py-2 bg-white border-2 border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Assets
        </a>
    </div>

    <!-- Asset Header -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-5">
        <div class="flex flex-col md:flex-row">
            <!-- Asset Image -->
            <div class="md:w-48 h-48 bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center flex-shrink-0">
                @if($asset->photo)
                    <img src="{{ asset('storage/' . $asset->photo) }}" alt="{{ $asset->name }}" 
                         class="w-full h-full object-cover">
                @else
                    <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                @endif
            </div>

            <!-- Asset Info -->
            <div class="flex-1 p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-1.5">{{ $asset->name }}</h1>
                        <div class="text-xs font-mono text-gray-500 bg-gray-100 inline-block px-2.5 py-1 rounded-full">
                            {{ $asset->serial_number }}
                        </div>
                    </div>
                    
                    <!-- Status Badge -->
                    <div>
                        @if($asset->status === 'active')
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-700 border-2 border-green-300">
                                <div class="w-2.5 h-2.5 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                                Available
                            </span>
                        @elseif($asset->status === 'in_use')
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700 border-2 border-orange-300">
                                <div class="w-2.5 h-2.5 bg-orange-500 rounded-full mr-2 animate-pulse"></div>
                                In Use
                            </span>
                        @elseif($asset->status === 'missing')
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border-2 border-red-300">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                Missing
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border-2 border-gray-300">
                                {{ ucfirst($asset->status) }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                    @if($asset->category)
                        <div class="flex items-center gap-2.5 text-sm">
                            <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 font-semibold uppercase">Category</div>
                                <div class="text-gray-900 font-semibold">{{ $asset->category->name }}</div>
                            </div>
                        </div>
                    @endif

                    @if($asset->model)
                        <div class="flex items-center gap-2.5 text-sm">
                            <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 font-semibold uppercase">Model</div>
                                <div class="text-gray-900 font-semibold">{{ $asset->model }}</div>
                            </div>
                        </div>
                    @endif

                    @if($asset->location)
                        <div class="flex items-center gap-2.5 text-sm">
                            <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 font-semibold uppercase">Location</div>
                                <div class="text-gray-900 font-semibold">{{ $asset->location->room_number }}</div>
                            </div>
                        </div>
                    @endif

                    @if($asset->specification)
                        <div class="flex items-center gap-2.5 text-sm">
                            <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 font-semibold uppercase">Specification</div>
                                <div class="text-gray-900 font-semibold">{{ $asset->specification }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-4 text-white">
            <div class="flex items-center justify-between mb-1">
                <div class="text-3xl font-bold">{{ $stats['total'] }}</div>
                <svg class="w-7 h-7 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div class="text-blue-100 font-semibold uppercase text-xs">Total Borrowings</div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-4 text-white">
            <div class="flex items-center justify-between mb-1">
                <div class="text-3xl font-bold">{{ $stats['active'] }}</div>
                <svg class="w-7 h-7 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="text-orange-100 font-semibold uppercase text-xs">Active Now</div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-4 text-white">
            <div class="flex items-center justify-between mb-1">
                <div class="text-3xl font-bold">{{ $stats['returned'] }}</div>
                <svg class="w-7 h-7 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="text-green-100 font-semibold uppercase text-xs">Returned</div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-4 text-white">
            <div class="flex items-center justify-between mb-1">
                <div class="text-3xl font-bold">{{ $stats['missing'] }}</div>
                <svg class="w-7 h-7 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="text-red-100 font-semibold uppercase text-xs">Missing</div>
        </div>
    </div>

    <!-- Borrowing History -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-5 py-3 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Complete Borrowing History</h2>
        </div>

        @if($borrowingItems->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Borrower
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Purpose
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Borrow Date
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Expected Return
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Actual Return
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Condition
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($borrowingItems as $item)
                            @php
                                $borrowing = $item->borrowing;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center">
                                                <span class="text-white text-sm font-bold">{{ strtoupper(substr($borrowing->borrower->name, 0, 1)) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-2.5">
                                            <div class="text-sm font-semibold text-gray-900">{{ $borrowing->borrower->name }}</div>
                                            @if($borrowing->borrower->department)
                                                <div class="text-xs text-gray-500">{{ $borrowing->borrower->department }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    <div class="max-w-xs">{{ $borrowing->purpose }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                    {{ $borrowing->borrow_date->format('M d, Y') }}
                                    <div class="text-xs text-gray-500">{{ $borrowing->borrow_date->format('h:i A') }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                    {{ $borrowing->expected_return_date->format('M d, Y') }}
                                    <div class="text-xs text-gray-500">{{ $borrowing->expected_return_date->format('h:i A') }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    @if($borrowing->actual_return_date)
                                        <div class="text-gray-900 font-semibold">{{ $borrowing->actual_return_date->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $borrowing->actual_return_date->format('h:i A') }}</div>
                                    @else
                                        <span class="text-gray-400 italic">Not returned</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs">
                                    @if($item->condition_on_borrow)
                                        <div class="mb-1">
                                            <span class="text-gray-600 font-medium">On Borrow:</span>
                                            <span class="ml-1 px-2 py-0.5 bg-blue-100 text-blue-700 rounded font-semibold">
                                                {{ ucfirst($item->condition_on_borrow) }}
                                            </span>
                                        </div>
                                    @endif
                                    @if($item->condition_on_return)
                                        <div>
                                            <span class="text-gray-600 font-medium">On Return:</span>
                                            <span class="ml-1 px-2 py-0.5 bg-green-100 text-green-700 rounded font-semibold">
                                                {{ ucfirst($item->condition_on_return) }}
                                            </span>
                                        </div>
                                    @endif
                                    @if(!$item->condition_on_borrow && !$item->condition_on_return)
                                        <span class="text-gray-400 italic">N/A</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($borrowing->status === 'active')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700">
                                            <div class="w-2 h-2 bg-orange-500 rounded-full mr-2 animate-pulse"></div>
                                            Active
                                        </span>
                                    @elseif($borrowing->status === 'returned')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            Returned
                                        </span>
                                    @elseif($borrowing->status === 'missing')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            Missing
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                                            {{ ucfirst($borrowing->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-5 py-3 bg-gray-50 border-t border-gray-200">
                {{ $borrowingItems->links() }}
            </div>
        @else
            <div class="px-5 py-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="mt-4 text-lg font-medium text-gray-700">No borrowing history found</p>
                <p class="mt-2 text-sm text-gray-500">This asset hasn't been borrowed yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection
