@extends('layouts.app')

@section('content')
<div class="flex-1 ml-72 p-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Secretary Dashboard</h1>

    <!-- Personal Statistics -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg shadow">
            <p class="text-sm text-gray-600">Completed Repairs</p>
            <p class="text-2xl font-bold text-blue-600">{{ $personalStats['completed_repairs'] }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg shadow">
            <p class="text-sm text-gray-600">Completed Maintenance</p>
            <p class="text-2xl font-bold text-green-600">{{ $personalStats['completed_maintenance'] }}</p>
        </div>
    </div>

    <!-- Completed Repairs Table -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Completed Repairs History</h2>
            <a href="{{ route('repairs.history') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($personalStats['completed_repairs_history']->take(5) as $repair)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <a href="{{ route('assets.index', ['search' => $repair->asset->serial_number]) }}" class="font-bold text-red-600 hover:underline">{{ $repair->asset->name }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $repair->issue }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($repair->completed_at)->format('M j, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Completed Maintenance Table -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Completed Maintenance History</h2>
            <a href="{{ route('user.maintenance.history') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($personalStats['completed_maintenance_history']->take(5) as $maintenance)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $maintenance->lab_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ is_array($maintenance->maintenance_task) ? implode(', ', $maintenance->maintenance_task) : $maintenance->maintenance_task }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($maintenance->completed_at)->format('M j, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Recent Actions</h2>
            <a href="{{ route('user.actions.history') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
        </div>
        @if(count($personalStats['recent_actions']) > 0)
            <div class="space-y-2">
                @foreach($personalStats['recent_actions'] as $action)
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        @if($action->action_source === 'asset_history')
                            <span class="font-medium">Asset {{ $action->change_type }}</span>
                            <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($action->created_at)->format('M j, Y g:i A') }}</span>
                        @elseif($action->action_source === 'repair')
                            <span class="font-medium">Repair Completed</span>
                            <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($action->completed_at)->format('M j, Y g:i A') }}</span>
                        @else
                            <span class="font-medium">Maintenance Completed</span>
                            <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($action->completed_at)->format('M j, Y g:i A') }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        @if($action->action_source === 'asset_history')
                            @if($action->remarks)
                                {{ $action->remarks }}
                                @if($action->asset)
                                    (Asset: {{ $action->asset->name }})
                                @endif
                            @else
                                Changed from "{{ $action->old_value }}" to "{{ $action->new_value }}"
                                @if($action->asset)
                                    (Asset: {{ $action->asset->name }})
                                @endif
                            @endif
                        @elseif($action->action_source === 'repair')
                            {{ $action->issue }}
                            @if($action->asset)
                                (Asset: {{ $action->asset->name }})
                            @endif
                        @else
                            Lab {{ $action->lab_number }} - {{ is_array($action->maintenance_task) ? implode(', ', $action->maintenance_task) : $action->maintenance_task }}
                        @endif
                    </p>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600">No recent actions.</p>
        @endif
    </div>
</div>
@endsection