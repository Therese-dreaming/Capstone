@extends('layouts.app')

@section('content')
<div class="flex-1 p-8 ml-72">
    <h1 class="text-2xl font-bold mb-6">My Tasks</h1>

    <!-- Maintenance Tasks Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Scheduled Maintenance Tasks</h2>
        @if($maintenanceTasks->count() > 0)
            <div class="space-y-4">
                @foreach($maintenanceTasks as $task)
                <div class="border rounded-lg p-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-medium">{{ $task->maintenance_task }}</h3>
                            <p class="text-sm text-gray-600">Lab {{ $task->lab_number }}</p>
                            <p class="text-sm text-gray-600">
                                Scheduled: {{ Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y') }}
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm 
                            {{ $task->scheduled_date == today() ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $task->scheduled_date == today() ? 'Today' : 'Upcoming' }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600">No maintenance tasks assigned.</p>
        @endif
    </div>

    <!-- Repair Requests Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Active Repair Requests</h2>
        @if($repairRequests->count() > 0)
            <div class="space-y-4">
                @foreach($repairRequests as $request)
                <div class="border rounded-lg p-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-medium">{{ $request->equipment }}</h3>
                            <p class="text-sm text-gray-600">{{ $request->location }}</p>
                            <p class="text-sm text-gray-600">Ticket: {{ $request->ticket_number }}</p>
                            <p class="text-sm text-gray-500 mt-2">{{ $request->issue }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm 
                            {{ $request->status === 'urgent' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($request->status) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600">No repair requests assigned.</p>
        @endif
    </div>
</div>
@endsection