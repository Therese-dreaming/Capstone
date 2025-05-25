@extends('layouts.app')

@section('content')
<div class="flex-1">
    <div class="p-4 md:p-6">
        <!-- Main Container -->
        <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
            <!-- Header Section -->
            <div class="mb-4">
                <h1 class="text-xl md:text-2xl font-bold">GROUPS</h1>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Divider Line -->
            <div class="border-b-2 border-red-800 mb-4 md:mb-6"></div>

            <!-- Mobile View Cards -->
            <div class="md:hidden space-y-4">
                @foreach($groups as $group)
                <div class="border rounded-lg p-4 bg-white shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-medium">{{ $group->name }}</h3>
                        <span class="px-2 py-1 text-xs rounded-full {{ $group->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $group->status }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <div class="flex justify-between py-1 border-b">
                            <span>Group Level:</span>
                            <span class="font-medium">{{ $group->level }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>ID:</span>
                            <span class="font-medium">#{{ $loop->iteration }}</span>
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
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $group->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $group->level }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full {{ $group->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $group->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection