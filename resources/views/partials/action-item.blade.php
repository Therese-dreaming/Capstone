@php
    // Date grouping logic for filtered actions
    $actionDate = $action->action_source === 'asset_history' ? $action->created_at : $action->completed_at;
    if (!($actionDate instanceof \Carbon\Carbon)) {
        $actionDate = \Carbon\Carbon::parse($actionDate);
    }
    $actionDate->setTimezone('Asia/Manila');

    $formattedDate = $actionDate->format('Y-m-d');

    // Check if this is a new date group *among filtered items*
    if ($showDateHeader && (!isset($currentDate) || $currentDate !== $formattedDate)) {
        $currentDate = $formattedDate;
        $showDateHeader = true;
    } else {
        $showDateHeader = false;
    }
@endphp

@if($showDateHeader)
    <div class="border-b border-gray-200 pb-1 mb-3">
        <h3 class="text-md font-medium text-gray-700">{{ $actionDate->format('F j, Y') }}</h3>
    </div>
@endif

<div class="action-item p-4 border-l-4 {{ $action->action_source === 'asset_history' ? 'border-blue-500 bg-blue-50' : ($action->action_source === 'repair' ? 'border-green-500 bg-green-50' : 'border-purple-500 bg-purple-50') }} rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
    <div class="flex justify-between items-center">
        <div class="flex items-center">
            @if($action->action_source === 'asset_history')
            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="font-medium text-blue-700">Asset {{ $action->change_type }}</span>
            @elseif($action->action_source === 'repair')
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <span class="font-medium text-green-700">Repair Completed</span>
            @else
            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <span class="font-medium text-purple-700">Maintenance Completed</span>
            @endif
        </div>
        <span class="text-sm {{ $action->action_source === 'asset_history' ? 'text-blue-600' : ($action->action_source === 'repair' ? 'text-green-600' : 'text-purple-600') }}">
            {{ $actionDate->format('g:i A') }}
        </span>
    </div>

    <div class="mt-3 ml-13">
        @if($action->action_source === 'asset_history')
        <div class="flex items-start">
            <div class="flex-shrink-0 mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="font-semibold text-gray-800">
                    @if($action->asset)
                    {{ $action->asset->name }} <span class="text-sm text-gray-500">({{ $action->asset->serial_number }})</span>
                    @else
                    Unknown Asset
                    @endif
                </p>
                <p class="text-sm text-gray-600 mt-1">
                    @if($action->remarks)
                    {{ $action->remarks }}
                    @elseif($action->change_type !== 'CATEGORY_ID' || $action->old_value !== $action->new_value)
                    Changed from <span class="font-medium">"{{ $action->old_value }}"</span> to <span class="font-medium">"{{ $action->new_value }}"</span>
                    @endif
                </p>
                @if($action->asset)
                <p class="text-xs text-gray-500 mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg> {{ $action->asset->location ?? 'No location' }}
                    @if($action->asset->lab_number)
                    <span class="mx-2">|</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg> Lab {{ $action->asset->lab_number }}
                    @endif
                </p>
                @endif
            </div>
        </div>
        @elseif($action->action_source === 'repair')
        <div class="flex items-start">
            <div class="flex-shrink-0 mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.502 1.94a.5.5 0 010 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 01.707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 00-.121.196l-.805 2.414a.25.25 0 00.316.316l2.414-.805a.5.5 0 00.196-.12l6.813-6.814z" />
                </svg>
            </div>
            <div class="w-full">
                <p class="font-semibold text-gray-800">
                    @if($action->asset)
                    {{ $action->asset->name }} <span class="text-sm text-gray-500">({{ $action->asset->serial_number }})</span>
                    @else
                    Unknown Asset
                    @endif
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-2 bg-white p-3 rounded-md border border-green-100">
                    <!-- Ticket Number -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0 mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Ticket</p>
                            <p class="text-sm font-medium">{{ $action->ticket_number ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Issue -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0 mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Issue</p>
                            <p class="text-sm">{{ $action->issue }}</p>
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0 mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Remarks</p>
                            <p class="text-sm">{{ $action->remarks ?? 'No remarks' }}</p>
                        </div>
                    </div>
                </div>

                @if($action->asset)
                <p class="text-xs text-gray-500 mt-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg> {{ $action->asset->location ?? 'No location' }}
                    @if($action->asset->lab_number)
                    <span class="mx-2">|</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg> Lab {{ $action->asset->lab_number }}
                    @endif
                </p>
                @endif
            </div>
        </div>
        @else
        <div class="flex items-start">
            <div class="flex-shrink-0 mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="w-full">
                <p class="font-semibold text-gray-800">
                    Lab {{ $action->lab_number }}
                </p>

                <div class="bg-white p-3 rounded-md border border-purple-100 mt-2">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Tasks Completed</p>
                            <p class="text-sm">{{ is_array($action->maintenance_task) ? implode(', ', $action->maintenance_task) : $action->maintenance_task }}</p>
                        </div>
                    </div>

                    @if($action->remarks)
                    <div class="flex items-start mt-2 pt-2 border-t border-purple-50">
                        <div class="flex-shrink-0 mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Remarks</p>
                            <p class="text-sm">{{ $action->remarks }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div> 