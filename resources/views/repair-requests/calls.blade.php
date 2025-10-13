@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Page Header -->
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center">
                <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                    <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">My Repair Calls</h1>
                    <p class="text-red-100 text-sm md:text-lg">Track your repair requests and evaluate completed work</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    @php
        $totalRequests = $requests->count();
        $completedCount = $requests->where('status', 'completed')->count();
        $inProgressCount = $requests->where('status', 'in_progress')->count();
        $pendingEvaluation = $requests->filter(function($r) {
            return in_array($r->status, ['completed', 'pulled_out']) && 
                   !$r->evaluation && 
                   !in_array($r->creator->group_id ?? null, [1, 2]) &&
                   ($r->verification_status === 'verified');
        })->count();
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Total Requests</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $totalRequests }}</div>
                </div>
                <div class="bg-blue-50 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">In Progress</div>
                    <div class="text-2xl font-bold text-blue-700">{{ $inProgressCount }}</div>
                </div>
                <div class="bg-blue-50 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Completed</div>
                    <div class="text-2xl font-bold text-green-700">{{ $completedCount }}</div>
                </div>
                <div class="bg-green-50 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Pending Evaluation</div>
                    <div class="text-2xl font-bold {{ $pendingEvaluation > 0 ? 'text-orange-700' : 'text-gray-900' }}">{{ $pendingEvaluation }}</div>
                </div>
                <div class="bg-orange-50 p-3 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Session Messages --}}
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center">
        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <div class="font-semibold">Success!</div>
            <div>{{ session('success') }}</div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-center">
        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <div class="font-semibold">Error!</div>
            <div>{{ session('error') }}</div>
        </div>
    </div>
    @endif

    @if($requests->isEmpty())
        <div class="bg-white rounded-xl shadow p-12 text-center">
            <div class="bg-gray-100 rounded-full w-20 h-20 mx-auto mb-6 flex items-center justify-center">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Repair Requests</h3>
            <p class="text-gray-600">You haven't submitted any repair requests yet.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 auto-rows-fr">
            @foreach($requests as $index => $request)
            <!-- Request Card with Enhanced Visual Separation -->
            <div class="relative flex flex-col">
                <!-- Request Number Indicator on Left -->
                <div class="absolute -left-3 top-4 bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm shadow-lg z-10 border-2 border-white">
                    {{ $index + 1 }}
                </div>
                
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border-3 flex flex-col h-full
                    @if($request->status === 'urgent') border-red-400
                    @elseif($request->status === 'completed') border-green-400
                    @elseif($request->status === 'pulled_out') border-yellow-400
                    @elseif($request->status === 'cancelled') border-gray-400
                    @elseif($request->status === 'in_progress') border-blue-400
                    @else border-gray-300 @endif
                    hover:shadow-xl transition-all duration-300 ml-5">
                    
                <!-- Colored Top Border Indicator -->
                <div class="h-2 
                    @if($request->status === 'urgent') bg-gradient-to-r from-red-500 to-red-600
                    @elseif($request->status === 'completed') bg-gradient-to-r from-green-500 to-green-600
                    @elseif($request->status === 'pulled_out') bg-gradient-to-r from-yellow-500 to-yellow-600
                    @elseif($request->status === 'cancelled') bg-gradient-to-r from-gray-500 to-gray-600
                    @elseif($request->status === 'in_progress') bg-gradient-to-r from-blue-500 to-blue-600
                    @else bg-gradient-to-r from-gray-400 to-gray-500 @endif">
                </div>
                
                <div class="p-5 flex-1 flex flex-col">
                    <!-- Header Section -->
                    <div class="flex flex-col mb-4 pb-4 border-b-2 border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span class="truncate">{{ $request->ticket_number }}</span>
                            </h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold shadow-sm flex-shrink-0
                                @if($request->status === 'urgent') bg-red-100 text-red-800 border border-red-300
                                @elseif($request->status === 'completed') bg-green-100 text-green-800 border border-green-300
                                @elseif($request->status === 'pulled_out') bg-yellow-100 text-yellow-800 border border-yellow-300
                                @elseif($request->status === 'cancelled') bg-gray-100 text-gray-800 border border-gray-300
                                @elseif($request->status === 'in_progress') bg-blue-100 text-blue-800 border border-blue-300
                                @else bg-gray-100 text-gray-800 border border-gray-300 @endif">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5
                                    @if($request->status === 'urgent') bg-red-600
                                    @elseif($request->status === 'completed') bg-green-600
                                    @elseif($request->status === 'pulled_out') bg-yellow-600
                                    @elseif($request->status === 'cancelled') bg-gray-600
                                    @elseif($request->status === 'in_progress') bg-blue-600 animate-pulse
                                    @else bg-gray-600 @endif">
                                </span>
                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $request->created_at->format('M j, Y g:i A') }}
                        </p>
                    </div>

                    <!-- Request Details Section with Icons -->
                    <div class="bg-gray-50 rounded-lg p-3 mb-4">
                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wide mb-2 flex items-center">
                            <svg class="w-3 h-3 mr-1 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Request Details
                        </h4>
                        <div class="grid grid-cols-1 gap-3">
                            <div class="flex items-start space-x-2">
                                <div class="bg-white p-1.5 rounded shadow-sm">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Equipment</p>
                                    <p class="text-xs font-medium text-gray-900 break-words">{{ $request->equipment }}</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-2">
                                <div class="bg-white p-1.5 rounded shadow-sm">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Location</p>
                                    <p class="text-xs font-medium text-gray-900 break-words">{{ $request->building }} - {{ $request->floor }} - {{ $request->room }}</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-2">
                                <div class="bg-white p-1.5 rounded shadow-sm">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Technician</p>
                                    <p class="text-xs font-medium text-gray-900 break-words">{{ $request->technician ? $request->technician->name : 'Not Assigned' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-2">
                                <div class="bg-white p-1.5 rounded shadow-sm">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Issue</p>
                                    <p class="text-xs font-medium text-gray-900 break-words">{{ $request->issue }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Evaluation Section - Always Show --}}
                    <!-- Divider -->
                    <div class="border-t-2 border-gray-100 my-4"></div>
                    
                    @if($request->status === 'completed' || $request->status === 'pulled_out')
                        <div class="bg-gradient-to-br from-orange-50 to-yellow-50 rounded-lg p-4 border border-orange-200">
                            <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-1.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                                Evaluation
                            </h4>
                            
                            @if(in_array($request->creator->group_id ?? null, [1, 2]))
                                <div class="bg-white border-l-4 border-gray-500 p-3 rounded shadow-sm">
                                    <div class="font-semibold text-gray-700 mb-1 flex items-center text-xs">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Not Required
                                    </div>
                                    <div class="text-xs text-gray-600">Admin/technician requests don't need evaluation.</div>
                                </div>
                            @elseif(!$request->evaluation && ($request->verification_status === 'verified'))
                                <div class="bg-gradient-to-r from-orange-50 to-yellow-50 rounded-lg p-5 border border-orange-200">
                                    <h4 class="text-sm font-bold text-gray-900 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                        Rate Your Experience
                                    </h4>
                                    <form method="POST" action="{{ route('repair.calls.evaluate', $request->id) }}" class="space-y-4" id="evaluation-form-{{ $request->id }}">
                                        @csrf
                                        <div>
                                            <label class="block text-gray-700 text-sm font-semibold mb-2">How would you rate the technician? <span class="text-red-600">*</span></label>
                                            <div class="flex items-center space-x-2" id="star-rating-{{ $request->id }}">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <button type="button" class="star-btn text-gray-300 hover:text-yellow-400 focus:outline-none transition-colors duration-150" data-value="{{ $i }}" aria-label="Rate {{ $i }}">
                                                        <svg class="w-8 h-8" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.385 2.46a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.54 1.118l-3.385-2.46a1 1 0 00-1.175 0l-3.385 2.46c-.784.57-1.838-.196-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118l-3.385-2.46c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.967z"/>
                                                        </svg>
                                                    </button>
                                                @endfor
                                                <input type="hidden" name="rating" id="rating-{{ $request->id }}" value="">
                                            </div>
                                            <div class="text-sm text-red-600 mt-1 hidden" id="rating-error-{{ $request->id }}"></div>
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 text-sm font-semibold mb-2" for="feedback-{{ $request->id }}">Share your feedback (Optional)</label>
                                            <textarea id="feedback-{{ $request->id }}" name="feedback" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none" placeholder="Tell us about your experience with the technician..."></textarea>
                                            <div class="text-sm text-red-600 mt-1 hidden" id="feedback-error-{{ $request->id }}"></div>
                                        </div>
                                        <button type="submit" class="w-full px-4 py-3 text-sm font-semibold text-white bg-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Submit Evaluation
                                        </button>
                                    </form>
                                </div>
                            @elseif(!$request->evaluation)
                                <div class="bg-white border-l-4 border-yellow-500 p-5 rounded-lg shadow-sm">
                                    <div class="flex items-start">
                                        <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        <div>
                                            <div class="font-bold text-yellow-800 mb-2 text-base">Evaluation Locked</div>
                                            <div class="text-sm text-gray-700 mb-3">You can evaluate after your signature is submitted and verified.</div>
                                            <a href="{{ route('repair.pending-signature') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors font-semibold text-sm">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                                Go to Pending Signatures
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-white border-l-4 border-green-500 p-5 rounded-lg shadow-sm">
                                    <div class="flex items-center mb-4">
                                        <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h4 class="font-bold text-green-800 text-base">Your Evaluation</h4>
                                    </div>
                                    <div class="flex items-center mb-4 bg-green-50 p-3 rounded-lg">
                                        <span class="text-sm font-bold text-gray-700 mr-3">Rating:</span>
                                        <span class="inline-flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-6 h-6 {{ $i <= $request->evaluation->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.385 2.46a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.54 1.118l-3.385-2.46a1 1 0 00-1.175 0l-3.385 2.46c-.784.57-1.838-.196-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118l-3.385-2.46c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.967z"/></svg>
                                            @endfor
                                        </span>
                                        <span class="ml-2 text-sm font-semibold text-gray-700">({{ $request->evaluation->rating }}/5)</span>
                                    </div>
                                    @if($request->evaluation->feedback)
                                    <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                        <span class="text-sm font-bold text-gray-700 block mb-2">Your Feedback:</span>
                                        <p class="text-sm text-gray-700 leading-relaxed">{{ $request->evaluation->feedback }}</p>
                                    </div>
                                    @endif
                                    <div class="text-xs text-gray-500 flex items-center bg-gray-50 p-2 rounded">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Submitted on {{ $request->evaluation->created_at->format('M j, Y g:i A') }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        {{-- Empty State for Pending/In Progress Requests --}}
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                                Evaluation
                            </h4>
                            <div class="flex flex-col items-center justify-center py-6 text-center">
                                <div class="bg-gray-200 rounded-full p-3 mb-3">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-gray-600 mb-1">Evaluation Not Available Yet</p>
                                <p class="text-xs text-gray-500">
                                    @if($request->status === 'pending')
                                        Request is pending assignment
                                    @elseif($request->status === 'in_progress')
                                        Repair work is in progress
                                    @elseif($request->status === 'urgent')
                                        Urgent request is being processed
                                    @elseif($request->status === 'cancelled')
                                        Request was cancelled
                                    @else
                                        Request is being processed
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide session messages after 5 seconds (only success/error messages)
        const sessionMessages = document.querySelectorAll('.mb-6.p-4.bg-green-50, .mb-6.p-4.bg-red-50');
        sessionMessages.forEach(message => {
            setTimeout(() => {
                message.style.transition = 'opacity 0.5s';
                message.style.opacity = '0';
                setTimeout(() => message.remove(), 500);
            }, 5000);
        });

        // Star rating selection functionality
        document.querySelectorAll('[id^="star-rating-"]').forEach(function(container) {
            const requestId = container.id.split('-').pop();
            const stars = container.querySelectorAll('.star-btn');
            const hiddenInput = document.getElementById(`rating-${requestId}`);

            function paintStars(value) {
                stars.forEach((btn) => {
                    const starVal = parseInt(btn.getAttribute('data-value'), 10);
                    if (starVal <= value) {
                        btn.classList.remove('text-gray-300');
                        btn.classList.add('text-yellow-400');
                    } else {
                        btn.classList.add('text-gray-300');
                        btn.classList.remove('text-yellow-400');
                    }
                });
            }

            stars.forEach(btn => {
                btn.addEventListener('click', function() {
                    const value = parseInt(this.getAttribute('data-value'), 10);
                    hiddenInput.value = value;
                    paintStars(value);
                });
            });
        });

        // Form submission handling
        document.querySelectorAll('[id^="evaluation-form-"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formId = this.id.split('-')[2];
                const submitButton = this.querySelector('button[type="submit"]');
                const ratingError = document.getElementById(`rating-error-${formId}`);
                const feedbackError = document.getElementById(`feedback-error-${formId}`);
                
                // Clear previous errors
                ratingError.classList.add('hidden');
                feedbackError.classList.add('hidden');
                
                // Disable submit button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Submitting...
                `;

                // Get form data
                const formData = new FormData(this);

                // Validate star rating
                const ratingInput = this.querySelector('input[name="rating"]');
                if (!ratingInput || !ratingInput.value) {
                    const formId = this.id.split('-')[2];
                    const ratingError = document.getElementById(`rating-error-${formId}`);
                    ratingError.textContent = 'Please select a rating';
                    ratingError.classList.remove('hidden');
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Submit Evaluation';
                    return;
                }
                
                // Submit form via AJAX
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        const messageContainer = document.createElement('div');
                        messageContainer.className = 'mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700';
                        messageContainer.innerHTML = `
                            <div class="font-semibold">Success!</div>
                            <div>${data.message}</div>
                        `;
                        form.parentElement.insertBefore(messageContainer, form);
                        
                        // Remove the form and show evaluation display
                        form.remove();
                        
                        // Auto-hide success message after 5 seconds
                        setTimeout(() => {
                            messageContainer.style.transition = 'opacity 0.5s';
                            messageContainer.style.opacity = '0';
                            setTimeout(() => messageContainer.remove(), 500);
                        }, 5000);
                        
                        // Reload the page to show the updated evaluation
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        throw new Error(data.message || 'Failed to submit evaluation');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    // Show error message
                    const messageContainer = document.createElement('div');
                    messageContainer.className = 'mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700';
                    messageContainer.innerHTML = `
                        <div class="font-semibold">Error!</div>
                        <div>${error.message}</div>
                    `;
                    form.parentElement.insertBefore(messageContainer, form);
                    
                    // Reset submit button
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Submit Evaluation';
                    
                    // Auto-hide error message after 5 seconds
                    setTimeout(() => {
                        messageContainer.style.transition = 'opacity 0.5s';
                        messageContainer.style.opacity = '0';
                        setTimeout(() => messageContainer.remove(), 500);
                    }, 5000);
                });
            });
        });
    });
</script>
@endsection