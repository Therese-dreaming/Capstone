@extends('layouts.borrowing-app')

@section('content')
<div class="flex-1 bg-gradient-to-br from-gray-50 via-gray-50 to-red-50 min-h-screen" id="mainContent">
    <div class="max-w-7xl mx-auto px-6 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Borrowing Reports by User</h1>
                    <p class="text-sm text-gray-600 mt-1">View borrowing statistics and history for each user</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg text-sm font-semibold shadow-sm">
                        {{ $users->total() }} Users
                    </span>
                    <a href="{{ route('borrowing.reports.by-asset') }}" class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg shadow-md transition-all border border-gray-300">
                        View by Asset
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-md p-5 mb-6 border border-gray-100">
            <form method="GET" action="{{ route('borrowing.reports.by-user') }}" class="space-y-4 md:space-y-0 md:flex md:items-end md:gap-3">
                <!-- Search -->
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Search Users</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by name, username, RFID, or department..." 
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                </div>

                <!-- Department Filter -->
                <div class="md:w-56">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Department</label>
                    <select name="department" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>
                                {{ $department }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-semibold rounded-lg transition-all shadow-sm hover:shadow-md">
                        Filter
                    </button>
                    <a href="{{ route('borrowing.reports.by-user') }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-all">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Users List -->
        @if($users->count() > 0)
            <div class="space-y-4 mb-6">
                @foreach($users as $user)
                    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
                        <!-- User Header -->
                        <div class="bg-gradient-to-r from-red-50 to-red-100 p-5 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-md">
                                        <span class="text-white font-bold text-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">{{ $user->name }}</h3>
                                        <div class="flex items-center gap-3 text-sm text-gray-600 mt-1">
                                            @if($user->department)
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                    {{ $user->department }}
                                                </span>
                                            @endif
                                            @if($user->rfid_number)
                                                <span class="flex items-center gap-1 font-mono">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                                    </svg>
                                                    {{ $user->rfid_number }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex gap-3">
                                    <div class="text-center px-4 py-2 bg-white rounded-lg shadow-sm">
                                        <div class="text-2xl font-bold text-red-600">{{ $user->borrowings_count }}</div>
                                        <div class="text-xs text-gray-600 font-semibold uppercase">Total</div>
                                    </div>
                                    <div class="text-center px-4 py-2 bg-white rounded-lg shadow-sm">
                                        <div class="text-2xl font-bold text-orange-600">{{ $user->active_borrowings_count }}</div>
                                        <div class="text-xs text-gray-600 font-semibold uppercase">Active</div>
                                    </div>
                                    <div class="text-center px-4 py-2 bg-white rounded-lg shadow-sm">
                                        <div class="text-2xl font-bold text-green-600">{{ $user->returned_borrowings_count }}</div>
                                        <div class="text-xs text-gray-600 font-semibold uppercase">Returned</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Borrowing History -->
                        <div class="p-5">
                            <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Recent Borrowing History</h4>
                            @php
                                $recentBorrowings = $user->borrowings()->with('items.borrowableAsset')->latest()->take(5)->get();
                            @endphp
                            
                            @if($recentBorrowings->count() > 0)
                                <div class="space-y-2">
                                    @foreach($recentBorrowings as $borrowing)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <div class="text-sm font-semibold text-gray-800">
                                                        {{ $borrowing->items->count() }} item(s)
                                                    </div>
                                                    @if($borrowing->status === 'active')
                                                        <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-xs font-bold rounded-full">Active</span>
                                                    @elseif($borrowing->status === 'returned')
                                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-bold rounded-full">Returned</span>
                                                    @elseif($borrowing->status === 'missing')
                                                        <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-bold rounded-full">Missing</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-600">
                                                    <span class="font-medium">Borrowed:</span> {{ $borrowing->borrow_date->format('M d, Y') }}
                                                    @if($borrowing->actual_return_date)
                                                        <span class="mx-1">•</span>
                                                        <span class="font-medium">Returned:</span> {{ $borrowing->actual_return_date->format('M d, Y') }}
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1 truncate">
                                                    <span class="font-medium">Purpose:</span> {{ $borrowing->purpose }}
                                                </div>
                                            </div>
                                            <div class="ml-4 text-xs text-gray-500">
                                                @foreach($borrowing->items as $item)
                                                    <div class="truncate max-w-xs">• {{ $item->borrowableAsset->name }}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 italic">No borrowing history</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="bg-white rounded-xl shadow-md px-6 py-4 border border-gray-100">
                    {{ $users->appends(request()->except('page'))->links() }}
                </div>
            @endif
        @else
            <div class="bg-white rounded-xl shadow-md p-12 text-center border border-gray-100">
                <div class="mb-4">
                    <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-700 mb-2">No Users Found</h3>
                <p class="text-sm text-gray-600 mb-6">
                    @if(request()->has('search') || request()->has('department'))
                        Try adjusting your filters to see more results.
                    @else
                        There are no users with borrowing history.
                    @endif
                </p>
                @if(request()->has('search') || request()->has('department'))
                    <a href="{{ route('borrowing.reports.by-user') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-semibold rounded-lg transition-all shadow-sm hover:shadow-md">
                        Clear Filters
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
