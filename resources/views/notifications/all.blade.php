@extends('layouts.app')

@section('content')
<div class="container mx-auto px-3 sm:px-4 py-4 sm:py-6">
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-3 sm:gap-4">
            <div class="min-w-0 flex-1">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-1 sm:mb-2 truncate">All Notifications</h1>
                <p class="text-gray-600 text-sm sm:text-base">Manage and view all your notifications</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 w-full sm:w-auto">
                <button id="markAllReadBtn" class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-red-600 text-white text-xs sm:text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 h-3 sm:h-4 sm:w-4 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="hidden sm:inline">Mark All as Read</span>
                    <span class="sm:hidden">Mark All Read</span>
                </button>
                <a href="{{ route('my.tasks') }}" class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-gray-600 text-white text-xs sm:text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 h-3 sm:h-4 sm:w-4 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span class="hidden sm:inline">Back to Tasks</span>
                    <span class="sm:hidden">Back</span>
                </a>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('notifications.all') }}" class="bg-gray-50 rounded-lg p-3 sm:p-4 mb-4 sm:mb-6" id="filterForm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div>
                    <label for="statusFilter" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Status</label>
                    <select name="status" id="statusFilter" class="w-full px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-xs sm:text-sm">
                        <option value="">All Notifications</option>
                        <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread Only</option>
                        <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read Only</option>
                    </select>
                </div>
                <div>
                    <label for="typeFilter" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Type</label>
                    <select name="type" id="typeFilter" class="w-full px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-xs sm:text-sm">
                        <option value="">All Types</option>
                        <option value="repair" {{ request('type') === 'repair' ? 'selected' : '' }}>Repair</option>
                        <option value="maintenance" {{ request('type') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>General</option>
                    </select>
                </div>
                <div>
                    <label for="dateFilter" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Date Range</label>
                    <select name="date" id="dateFilter" class="w-full px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-xs sm:text-sm">
                        <option value="">All Time</option>
                        <option value="today" {{ request('date') === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ request('date') === 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ request('date') === 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="quarter" {{ request('date') === 'quarter' ? 'selected' : '' }}>This Quarter</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-3 sm:px-6 py-1.5 sm:py-2 bg-blue-600 text-white text-xs sm:text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200" id="applyFiltersBtn">
                        <span class="inline-flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                            </svg>
                            <span class="hidden sm:inline">Apply Filters</span>
                            <span class="sm:hidden">Apply</span>
                        </span>
                    </button>
                </div>
            </div>
            
            <!-- Clear Filters -->
            @if(request('status') || request('type') || request('date'))
                <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-200">
                    <a href="{{ route('notifications.all') }}" class="inline-flex items-center text-xs sm:text-sm text-gray-600 hover:text-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear All Filters
                    </a>
                </div>
            @endif
        </form>

        <!-- Filter Summary -->
        @if(request('status') || request('type') || request('date'))
            <div class="mb-3 sm:mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0">
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-2">
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 text-blue-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                            </svg>
                            <span class="text-xs sm:text-sm font-medium text-blue-800">Active Filters:</span>
                        </div>
                        <div class="flex flex-wrap gap-1 sm:gap-2">
                            @if(request('status'))
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                    Status: {{ ucfirst(request('status')) }}
                                </span>
                            @endif
                            @if(request('type'))
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                    Type: {{ ucfirst(request('type')) }}
                                </span>
                            @endif
                            @if(request('date'))
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                    Date: {{ ucfirst(request('date')) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('notifications.all') }}" class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 underline">
                        Clear All
                    </a>
                </div>
            </div>
        @endif

        <!-- Statistics -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-4 sm:mb-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4">
                <div class="flex items-center">
                    <div class="p-1.5 sm:p-2 bg-blue-100 rounded-lg flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-6 sm:w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <div class="ml-2 sm:ml-3 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-blue-600">
                            @if(request('status') || request('type') || request('date'))
                                Filtered Results
                            @else
                                Total Notifications
                            @endif
                        </p>
                        <p class="text-xl sm:text-2xl font-bold text-blue-900">{{ $filteredTotal }}</p>
                        @if(request('status') || request('type') || request('date'))
                            <p class="text-xs text-blue-600">of {{ $totalCount }} total</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 sm:p-4">
                <div class="flex items-center">
                    <div class="p-1.5 sm:p-2 bg-red-100 rounded-lg flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-6 sm:w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="ml-2 sm:ml-3 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-red-600">Unread</p>
                        <p class="text-xl sm:text-2xl font-bold text-red-900" id="unreadCount">{{ $filteredUnread }}</p>
                        @if(request('status') || request('type') || request('date'))
                            <p class="text-xs text-red-600">of {{ $unreadCount }} total</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-3 sm:p-4">
                <div class="flex items-center">
                    <div class="p-1.5 sm:p-2 bg-green-100 rounded-lg flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-6 sm:w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-2 sm:ml-3 min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-green-600">Read</p>
                        <p class="text-xl sm:text-2xl font-bold text-green-900" id="readCount">{{ $filteredRead }}</p>
                        @if(request('status') || request('type') || request('date'))
                            <p class="text-xs text-green-600">of {{ $readCount }} total</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            @if($notifications->count() > 0)
                <!-- Mobile Cards View -->
                <div class="block md:hidden">
                    @foreach($notifications as $notification)
                        @php
                            $typeBase = strtolower(explode('_', $notification->type)[0] ?? '');
                            $typeLabel = ucwords(str_replace('_', ' ', $notification->type));
                        @endphp
                        <div class="p-4 border-b border-gray-200 {{ $notification->is_read ? 'bg-white' : 'bg-red-50' }}" data-id="{{ $notification->id }}">
                            <div class="flex items-start space-x-3">
                                @if($typeBase === 'repair')
                                    <div class="p-2 bg-red-100 rounded-lg flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                                        </svg>
                                    </div>
                                @elseif($typeBase === 'maintenance')
                                    <div class="p-2 bg-blue-100 rounded-lg flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 00-1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                @else
                                    <div class="p-2 bg-gray-100 rounded-lg flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-900 capitalize">{{ $typeLabel }}</span>
                                        @if($notification->is_read)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Read
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                </svg>
                                                Unread
                                            </span>
                                        @endif
                                    </div>
                                    <a href="{{ route('notifications.redirect', ['id' => $notification->id]) }}" class="text-sm text-gray-900 hover:text-red-600 transition-colors duration-200 block mb-2">
                                        {{ $notification->message }}
                                    </a>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                        <div class="flex items-center space-x-2">
                                            @if(!$notification->is_read)
                                                <button onclick="markAsRead({{ $notification->id }})" class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            @endif
                                            @if($notification->link)
                                                <a href="{{ $notification->link }}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="notificationsTableBody">
                            @foreach($notifications as $notification)
                                @php
                                    $typeBase = strtolower(explode('_', $notification->type)[0] ?? '');
                                    $typeLabel = ucwords(str_replace('_', ' ', $notification->type));
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors duration-200 {{ $notification->is_read ? 'bg-white' : 'bg-red-50' }}" data-id="{{ $notification->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($typeBase === 'repair')
                                                <div class="p-2 bg-red-100 rounded-lg">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                                                    </svg>
                                                </div>
                                            @elseif($typeBase === 'maintenance')
                                                <div class="p-2 bg-blue-100 rounded-lg">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 00-1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="p-2 bg-gray-100 rounded-lg">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <span class="ml-2 text-sm font-medium text-gray-900 capitalize">{{ $typeLabel }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-xs">
                                            @if(true)
                                                <a href="{{ route('notifications.redirect', ['id' => $notification->id]) }}" class="text-sm text-gray-900 hover:text-red-600 transition-colors duration-200 line-clamp-2">
                                                    {{ $notification->message }}
                                                </a>
                                            @else
                                                <p class="text-sm text-gray-900">{{ $notification->message }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($notification->is_read)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Read
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                </svg>
                                                Unread
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            @if(!$notification->is_read)
                                                <button onclick="markAsRead({{ $notification->id }})" class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            @endif
                                            @if($notification->link)
                                                <a href="{{ $notification->link }}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-white px-3 sm:px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-8 sm:py-12 px-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm sm:text-base font-medium text-gray-900">No notifications found</h3>
                    <p class="mt-1 text-xs sm:text-sm text-gray-500 break-words">
                        @if(request('status') || request('type') || request('date'))
                            No notifications match your current filters. 
                            <a href="{{ route('notifications.all') }}" class="text-red-600 hover:text-red-800 underline">Clear filters</a> to see all notifications.
                        @else
                            You don't have any notifications yet.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    const notificationsTableBody = document.getElementById('notificationsTableBody');
    const unreadCountElement = document.getElementById('unreadCount');
    const readCountElement = document.getElementById('readCount');
    const filterForm = document.getElementById('filterForm');
    const applyFiltersBtn = document.getElementById('applyFiltersBtn');

    // Mark all as read functionality
    markAllReadBtn.addEventListener('click', function() {
        fetch('{{ route("notifications.markAllAsRead") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to reflect changes
                window.location.reload();
            }
        })
        .catch(error => {
            showNotification('error', 'Failed to mark all notifications as read');
        });
    });

    // Mark individual notification as read
    window.markAsRead = function(id) {
        fetch(`{{ url('notifications') }}/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to reflect changes
                window.location.reload();
            }
        })
        .catch(error => {
            showNotification('error', 'Failed to mark notification as read');
        });
    };

    // Auto-submit filters when select options change
    let filterTimeout;
    filterForm.addEventListener('change', function() {
        // Clear any existing timeout
        clearTimeout(filterTimeout);
        
        // Add loading state
        const originalText = applyFiltersBtn.innerHTML;
        applyFiltersBtn.innerHTML = `
            <span class="inline-flex items-center">
                <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Applying...
            </span>
        `;
        applyFiltersBtn.disabled = true;
        
        // Submit form after a short delay to prevent rapid successive requests
        filterTimeout = setTimeout(() => {
            filterForm.submit();
        }, 300);
    });

    // Manual submit handler for the Apply Filters button
    applyFiltersBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Add loading state
        this.innerHTML = `
            <span class="inline-flex items-center">
                <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Applying...
            </span>
        `;
        this.disabled = true;
        
        // Submit the form
        filterForm.submit();
    });

    // Show notification message
    function showNotification(type, message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-20 right-4 z-50 p-4 rounded-md shadow-lg ${
            type === 'success' 
                ? 'bg-green-500 text-white' 
                : 'bg-red-500 text-white'
        }`;
        notification.textContent = message;

        // Add to page
        document.body.appendChild(notification);

        // Auto-remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>
@endsection 