@extends('layouts.borrowing-app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('borrowing.reports.by-user') }}" 
           class="inline-flex items-center px-4 py-2 bg-white border-2 border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Users
        </a>
    </div>

    <!-- User Header -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-5 mb-5">
        <div class="flex items-center gap-4">
            <!-- Avatar -->
            <div class="flex-shrink-0">
                <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-lg">
                    <span class="text-white text-xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
            </div>

            <!-- User Info -->
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ $user->name }}</h1>
                <div class="flex flex-wrap items-center gap-3 text-xs text-gray-600">
                    @if($user->department)
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="font-semibold">{{ $user->department }}</span>
                        </div>
                    @endif
                    @if($user->rfid_number)
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                            <span class="font-mono font-semibold">{{ $user->rfid_number }}</span>
                        </div>
                    @endif
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="font-semibold">{{ ucfirst($user->role) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-4 text-white">
            <div class="flex items-center justify-between mb-1">
                <div class="text-3xl font-bold">{{ $stats['total'] }}</div>
                <svg class="w-7 h-7 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div class="text-red-100 font-semibold uppercase text-xs">Total Borrowings</div>
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

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-4 text-white">
            <div class="flex items-center justify-between mb-1">
                <div class="text-3xl font-bold">{{ $stats['missing'] }}</div>
                <svg class="w-7 h-7 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="text-purple-100 font-semibold uppercase text-xs">Missing</div>
        </div>
    </div>

    <!-- Borrowing History -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-5 py-3 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Complete Borrowing History</h2>
        </div>

        @if($borrowings->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Borrow Date
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Purpose
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Assets
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Expected Return
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Actual Return
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($borrowings as $borrowing)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                    {{ $borrowing->borrow_date->format('M d, Y') }}
                                    <div class="text-xs text-gray-500">{{ $borrowing->borrow_date->format('h:i A') }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    <div class="max-w-xs">{{ $borrowing->purpose }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($borrowing->items->count() > 0)
                                        <div class="space-y-1">
                                            @foreach($borrowing->items as $item)
                                                <div class="flex items-center gap-2 text-gray-700">
                                                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                                    <span class="font-medium">{{ $item->borrowableAsset->name }}</span>
                                                </div>
                                                <div class="text-xs text-gray-500 ml-4 font-mono">
                                                    SN: {{ $item->borrowableAsset->serial_number }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">No assets</span>
                                    @endif
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
                {{ $borrowings->links() }}
            </div>
        @else
            <div class="px-5 py-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="mt-4 text-lg font-medium text-gray-700">No borrowing history found</p>
                <p class="mt-2 text-sm text-gray-500">This user hasn't borrowed any assets yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection
