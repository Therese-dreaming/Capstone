@extends('layouts.app')

@section('content')
<div class="flex-1 ml-72 p-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">All Completed Repairs History</h1>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <a href="{{ url('/secretary-dashboard') }}" class="text-red-600 hover:text-red-800 text-sm font-medium mb-4 inline-block">Back to Dashboard</a>
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
                    @foreach($repairs as $repair)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <a href="{{ url('assets?search=' . urlencode($repair->asset->name)) }}" class="font-bold text-red-600 hover:underline">{{ $repair->asset->name }}</a>
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
</div>
@endsection 