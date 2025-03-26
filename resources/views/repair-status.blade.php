@extends('layouts.app')

@section('content')
<div class="flex-1 p-8 ml-72">
    <h2 class="text-2xl font-semibold mb-6">Request Status</h2>

    <!-- Urgent Repairs Section -->
    @if($urgentRepairs->count() > 0)
    <div class="bg-red-700 text-white p-6 rounded-lg mb-6">
        <div class="flex items-center gap-2 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <h3 class="text-xl font-semibold">URGENT REPAIRS ({{ $urgentRepairs->count() }})</h3>
        </div>
        
        @foreach($urgentRepairs as $repair)
        <div class="ml-4">
            → {{ $repair->equipment }} - {{ $repair->office_room }} - Not Assigned 
            ({{ \Carbon\Carbon::parse($repair->created_at)->format('M j, g:i A') }})
        </div>
        @endforeach
    </div>
    @endif

    <!-- Requests Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Request Date</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Item</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Lab Room</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Technician</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($requests as $request)
                <tr>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($request->created_at)->format('M j, Y (g:i A)') }}</td>
                    <td class="px-6 py-4">{{ $request->equipment }}</td>
                    <td class="px-6 py-4">{{ $request->office_room }}</td>
                    <td class="px-6 py-4">
                        @if($request->status === 'urgent')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Urgent
                            </span>
                        @elseif($request->status === 'in_progress')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                In Progress
                            </span>
                        @elseif($request->status === 'completed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Completed
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Pending
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        {{ $request->technician ? $request->technician->name : 'Not Assigned' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Statistics Section -->
    <div class="grid grid-cols-2 gap-6 mt-6">
        <!-- Status Overview -->
        <div class="bg-yellow-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-2">Status Overview</h3>
            <p class="text-sm text-gray-600 mb-4">Key metrics for repair requests and technician performance</p>
            
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-600">Total Open</p>
                    <p class="text-2xl font-semibold">{{ $totalOpen }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-600">Completed this Month</p>
                    <p class="text-2xl font-semibold">{{ $completedThisMonth }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-600">Average Response Time</p>
                    <p class="text-2xl font-semibold">{{ round($avgResponseTime) }} days</p>
                </div>
            </div>
        </div>

        <!-- Technician Performance -->
        <div class="bg-teal-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-2">Technician Performance</h3>
            <p class="text-sm text-gray-600">Admin view only</p>
            <!-- Add technician performance metrics here -->
        </div>
    </div>
</div>
@endsection