<div class="flex flex-col items-center justify-center py-12 text-gray-500">
    <div class="bg-gray-100 rounded-full p-4 w-20 h-20 mx-auto mb-4 flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>
    @if (request('filter') || request('start_date') || request('end_date'))
        <p class="text-xl text-gray-900">No actions found matching the applied filters.</p>
        <p class="text-sm mt-2 text-gray-600">
            <a href="{{ route('user.actions.history') }}" class="text-red-600 hover:text-red-800 hover:underline transition-colors duration-200">Clear filters</a> to see all actions.
        </p>
    @else
        <p class="text-xl text-gray-900">No {{ $type ?? 'actions' }} found</p>
        <p class="text-sm mt-2 text-gray-600 text-center">
            {{ $type ?? 'Actions' }} will appear here as you make changes to assets, complete repairs, or perform maintenance.
        </p>
    @endif
</div> 