@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Page Header with Background Design -->
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center">
                <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                    <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">User Groups Management</h1>
                    <p class="text-red-100 text-sm md:text-lg">Manage and organize user access levels</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-4 md:mb-6 p-3 md:p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm md:text-base">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 md:mb-6 p-3 md:p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-center">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm md:text-base">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Groups Statistics -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6 mb-6">
        <h2 class="text-lg md:text-xl font-semibold text-gray-900 mb-4 md:mb-6 flex items-center">
            <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Groups Overview
        </h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 md:p-6 rounded-xl border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-blue-700 mb-1">Total Groups</p>
                        <p class="text-2xl md:text-3xl font-bold text-blue-900">{{ $groups->count() }}</p>
                    </div>
                    <div class="bg-blue-200 p-2 md:p-3 rounded-full">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 md:p-6 rounded-xl border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-green-700 mb-1">Active Groups</p>
                        <p class="text-2xl md:text-3xl font-bold text-green-900">{{ $groups->where('status', 'Active')->count() }}</p>
                    </div>
                    <div class="bg-green-200 p-2 md:p-3 rounded-full">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 p-4 md:p-6 rounded-xl border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-yellow-700 mb-1">Inactive Groups</p>
                        <p class="text-2xl md:text-3xl font-bold text-yellow-900">{{ $groups->where('status', 'Inactive')->count() }}</p>
                    </div>
                    <div class="bg-yellow-200 p-2 md:p-3 rounded-full">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Groups List -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
        <div class="flex justify-between items-center mb-4 md:mb-6">
            <h2 class="text-lg md:text-xl font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                All Groups
            </h2>
            <div class="flex items-center space-x-2">
                <span class="text-xs md:text-sm text-gray-500">Total: {{ $groups->count() }} groups</span>
            </div>
        </div>

        <!-- Mobile View Cards -->
        <div class="md:hidden space-y-3 md:space-y-4">
            @foreach($groups as $group)
            <div class="bg-gray-50 rounded-xl p-4 md:p-6 hover:bg-gray-100 transition-colors duration-200 border border-gray-200">
                <div class="flex justify-between items-start mb-3 md:mb-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-base md:text-lg mb-1 truncate">{{ $group->name }}</h3>
                        <div class="flex items-center text-sm text-gray-600 mb-2">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Level: {{ $group->level }}
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            ID: #{{ $loop->iteration }}
                        </div>
                    </div>
                    <div class="flex flex-col items-end space-y-2">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $group->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} flex items-center">
                            <div class="w-2 h-2 rounded-full mr-2 {{ $group->status === 'Active' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                            {{ $group->status }}
                        </span>
                        <div class="text-xs text-gray-500">Group #{{ $loop->iteration }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Desktop Groups Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($groups as $group)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">#{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="bg-red-100 p-2 rounded-full mr-3">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $group->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $group->level }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $group->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} flex items-center w-fit">
                                <div class="w-2 h-2 rounded-full mr-2 {{ $group->status === 'Active' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                {{ $group->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        @if($groups->count() === 0)
        <div class="text-center py-8 md:py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
            <div class="bg-gray-100 rounded-full p-3 md:p-4 w-16 h-16 md:w-20 md:h-20 mx-auto mb-3 md:mb-4 flex items-center justify-center">
                <svg class="h-8 w-8 md:h-10 md:w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">No groups found</h3>
            <p class="text-xs md:text-sm text-gray-500">There are no user groups to display at this time.</p>
        </div>
        @endif
    </div>
</div>
@endsection