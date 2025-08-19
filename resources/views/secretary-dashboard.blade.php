@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Dashboard Header with Background Design -->
    <div class="mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center">
                <div class="bg-white/20 p-4 rounded-full backdrop-blur-sm mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Secretary Dashboard</h1>
                    <p class="text-red-100 text-lg">Welcome back! Here's your performance overview</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Personal Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-blue-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Repairs Completed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $personalStats['completed_repairs'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-xs text-green-600 font-medium flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    {{ number_format(($personalStats['completed_repairs'] ?? 0) / max(1, ($personalStats['total_tasks'] ?? 1)) * 100, 1) }}% completion rate
                </span>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-green-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Maintenance Tasks</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $personalStats['completed_maintenance'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-xs text-green-600 font-medium flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $personalStats['completed_maintenance'] ?? 0 }} completed
                </span>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-yellow-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Avg. Response Time</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $personalStats['avg_response_time'] ?? 0 }} <span class="text-sm font-normal">hours</span></p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-xs {{ ($personalStats['avg_response_time'] ?? 0) < 24 ? 'text-green-600' : 'text-red-600' }} font-medium flex items-center">
                    @if(($personalStats['avg_response_time'] ?? 0) < 24)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Below target time
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Above target time
                    @endif
                </span>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-red-800 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completion Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $personalStats['completion_rate'] ?? 0 }}%</p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2">
                <div class="w-full bg-gray-200 rounded-full h-1.5">
                    <div class="bg-red-800 h-1.5 rounded-full" style="width: {{ $personalStats['completion_rate'] ?? 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Repairs Table - Full Width -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-800 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z" />
                </svg>
                <h2 class="text-lg font-semibold text-gray-900">Completed Repairs History</h2>
            </div>
            <a href="{{ route('repairs.history') }}" class="inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                View All
            </a>
        </div>

        {{-- Mobile Card View --}}
        <div class="sm:hidden space-y-4">
            @forelse($personalStats['completed_repairs_history']->take(5) as $repair)
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-start mb-2">
                    <a href="{{ route('assets.index', ['search' => $repair->asset->serial_number]) }}" class="font-bold text-red-800 hover:underline">{{ $repair->asset->name }}</a>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                </div>
                <div class="text-sm text-gray-600 mb-1">Issue: {{ $repair->issue }}</div>
                <div class="text-sm text-gray-500">Completed: {{ \Carbon\Carbon::parse($repair->completed_at)->format('M j, Y') }}</div>
            </div>
            @empty
            <div class="text-center py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="mt-2 text-sm text-gray-500">No completed repairs to display</p>
            </div>
            @endforelse
        </div>

        {{-- Desktop Table View --}}
        <div class="overflow-x-auto hidden sm:block">
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
                    @forelse($personalStats['completed_repairs_history']->take(5) as $repair)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <a href="{{ route('assets.index', ['search' => $repair->asset->serial_number]) }}" class="font-bold text-red-800 hover:underline">{{ $repair->asset->serial_number }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $repair->issue }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($repair->completed_at)->format('M j, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                            No completed repairs to display
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Completed Maintenance Table - Full Width -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-800 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <h2 class="text-lg font-semibold text-gray-900">Completed Maintenance History</h2>
            </div>
            <a href="{{ route('user.maintenance.history') }}" class="inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                View All
            </a>
        </div>

        {{-- Mobile Card View --}}
        <div class="sm:hidden space-y-4">
            @forelse($personalStats['completed_maintenance_history']->take(5) as $maintenance)
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-start mb-2">
                    <span class="font-bold text-gray-900">Lab {{ $maintenance->lab_number }}</span>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                </div>
                <div class="text-sm text-gray-600 mb-1">Task: {{ is_array($maintenance->maintenance_task) ? implode(', ', $maintenance->maintenance_task) : $maintenance->maintenance_task }}</div>
                <div class="text-sm text-gray-500">Completed: {{ \Carbon\Carbon::parse($maintenance->completed_at)->format('M j, Y') }}</div>
            </div>
            @empty
            <div class="text-center py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <p class="mt-2 text-sm text-gray-500">No completed maintenance tasks to display</p>
            </div>
            @endforelse
        </div>

        {{-- Desktop Table View --}}
        <div class="overflow-x-auto hidden sm:block">
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
                    @forelse($personalStats['completed_maintenance_history']->take(5) as $maintenance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Lab {{ $maintenance->lab_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ is_array($maintenance->maintenance_task) ? implode(', ', $maintenance->maintenance_task) : $maintenance->maintenance_task }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($maintenance->completed_at)->format('M j, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                            No completed maintenance tasks to display
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Actions - Full Width -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-800 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="text-lg font-semibold text-gray-900">Recent Actions</h2>
            </div>
            <a href="{{ route('user.actions.history') }}" class="inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                View All
            </a>
        </div>
        @if(count($personalStats['recent_actions']) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($personalStats['recent_actions'] as $action)
                <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200 border-l-2 border-red-800">
                    <div class="flex justify-between items-center mb-3">
                        @if($action->action_source === 'asset_history')
                            <span class="font-medium flex items-center text-gray-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-red-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Asset {{ $action->change_type }}
                            </span>
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($action->created_at)->format('M j, Y g:i A') }}</span>
                        @elseif($action->action_source === 'repair')
                            <span class="font-medium flex items-center text-gray-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-red-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z" />
                                </svg>
                                Repair Completed
                            </span>
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($action->completed_at)->format('M j, Y g:i A') }}</span>
                        @else
                            <span class="font-medium flex items-center text-gray-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-red-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Maintenance Completed
                            </span>
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($action->completed_at)->format('M j, Y g:i A') }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600">
                        @if($action->action_source === 'asset_history')
                            @if($action->remarks)
                                {{ $action->remarks }}
                                @if($action->asset)
                                    <span class="font-medium text-red-800">(Asset: {{ $action->asset->name }})</span>
                                @endif
                            @else
                                Changed from "<span class="font-medium">{{ $action->old_value }}</span>" to "<span class="font-medium">{{ $action->new_value }}</span>"
                                @if($action->asset)
                                    <span class="font-medium text-red-800">(Asset: {{ $action->asset->name }})</span>
                                @endif
                            @endif
                        @elseif($action->action_source === 'repair')
                            {{ $action->issue }}
                            @if($action->asset)
                                <span class="font-medium text-red-800">(Asset: {{ $action->asset->name }})</span>
                            @endif
                        @else
                            <span class="font-medium">Lab {{ $action->lab_number }}</span> - {{ is_array($action->maintenance_task) ? implode(', ', $action->maintenance_task) : $action->maintenance_task }}
                        @endif
                    </p>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="mt-2 text-gray-500">No recent actions to display</p>
            </div>
        @endif
    </div>
</div>

<script>
    // Add any JavaScript functionality here
    document.addEventListener('DOMContentLoaded', function() {
        // Example: Add hover effects or other interactive elements
        const cards = document.querySelectorAll('.hover\\:shadow-lg');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.classList.add('transform', 'scale-[1.02]', 'transition-transform');
            });
            card.addEventListener('mouseleave', () => {
                card.classList.remove('transform', 'scale-[1.02]', 'transition-transform');
            });
        });
    });
</script>
@endsection                       