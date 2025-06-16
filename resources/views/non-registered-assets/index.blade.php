@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Non-Registered Pulled Out Assets</h2>
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

        <!-- Mobile Cards View -->
        <div class="md:hidden space-y-4">
            @forelse($assets as $asset)
            <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                <div class="space-y-2">
                    <div class="flex justify-between items-start">
                        <h3 class="font-medium text-gray-900">{{ $asset->equipment_name }}</h3>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $asset->status === 'PULLED OUT' ? 'bg-yellow-100 text-yellow-800' : 
                               ($asset->status === 'DISPOSED' ? 'bg-red-100 text-red-800' : 
                                'bg-green-100 text-green-800') }}">
                            {{ $asset->status }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-500">
                        <p><span class="font-medium">Location:</span> {{ $asset->location }}</p>
                        <p><span class="font-medium">Ticket Number:</span> {{ $asset->ticket_number }}</p>
                        <p><span class="font-medium">Pulled Out By:</span> {{ $asset->pulled_out_by }}</p>
                        <p><span class="font-medium">Date:</span> {{ $asset->pulled_out_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="pt-2">
                        @if($asset->repairRequest)
                            <a href="{{ route('repair.details', ['id' => $asset->repairRequest->id]) }}" 
                               class="text-red-600 hover:text-red-900 text-sm font-medium">View Details</a>
                        @else
                            <span class="text-gray-500 text-sm">No repair request found</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-gray-500 py-4">
                No non-registered pulled out assets found.
            </div>
            @endforelse

            <!-- Mobile Pagination -->
            <div class="mt-4">
                {{ $assets->links() }}
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pulled Out By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($assets as $asset)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $asset->equipment_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $asset->location }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $asset->ticket_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $asset->status === 'PULLED OUT' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($asset->status === 'DISPOSED' ? 'bg-red-100 text-red-800' : 
                                    'bg-green-100 text-green-800') }}">
                                {{ $asset->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $asset->pulled_out_by }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $asset->pulled_out_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($asset->repairRequest)
                                <a href="{{ route('repair.details', ['id' => $asset->repairRequest->id]) }}" 
                                   class="text-red-600 hover:text-red-900">View Details</a>
                            @else
                                <span class="text-gray-500">No repair request found</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No non-registered pulled out assets found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Desktop Pagination -->
            <div class="mt-4">
                {{ $assets->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 