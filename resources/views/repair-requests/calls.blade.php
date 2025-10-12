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
        <div class="grid grid-cols-1 gap-6">
            @foreach($requests as $request)
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-4 pb-4 border-b">
                        <div class="mb-3 sm:mb-0">
                            <h3 class="text-lg font-bold text-gray-900">{{ $request->ticket_number }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $request->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold
                            @if($request->status === 'urgent') bg-red-100 text-red-800 border border-red-200
                            @elseif($request->status === 'completed') bg-green-100 text-green-800 border border-green-200
                            @elseif($request->status === 'pulled_out') bg-yellow-100 text-yellow-800 border border-yellow-200
                            @elseif($request->status === 'cancelled') bg-red-100 text-red-800 border border-red-200
                            @elseif($request->status === 'in_progress') bg-blue-100 text-blue-800 border border-blue-200
                            @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                        </span>
                    </div>

                    <!-- Request Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Equipment</p>
                            <p class="text-sm text-gray-900">{{ $request->equipment }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Location</p>
                            <p class="text-sm text-gray-900">{{ $request->building }} - {{ $request->floor }} - {{ $request->room }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Technician</p>
                            <p class="text-sm text-gray-900">{{ $request->technician ? $request->technician->name : 'Not Assigned' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Issue</p>
                            <p class="text-sm text-gray-900">{{ Str::limit($request->issue, 50) }}</p>
                        </div>
                    </div>
                    
                    {{-- Evaluation Section --}}
                    @if($request->status === 'completed' || $request->status === 'pulled_out')
                        <div class="mt-4">
                            @if(in_array($request->creator->group_id ?? null, [1, 2]))
                                <div class="bg-gray-50 border-l-4 border-gray-500 p-4 rounded">
                                    <div class="font-semibold text-gray-700 mb-1">Evaluation Status</div>
                                    <div class="text-sm text-gray-600">Evaluation not required for admin/technician-created requests.</div>
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
                                        <button type="submit" class="w-full px-4 py-3 text-sm font-semibold text-white bg-gradient-to-r from-orange-600 to-red-600 rounded-lg hover:from-orange-700 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Submit Evaluation
                                        </button>
                                    </form>
                                </div>
                            @elseif(!$request->evaluation)
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex items-start">
                                    <svg class="w-5 h-5 text-yellow-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <div>
                                        <div class="font-semibold text-yellow-800 mb-1">Evaluation Locked</div>
                                        <div class="text-sm text-yellow-700">You can evaluate after your signature is submitted and verified. Please go to <a href="{{ route('repair.pending-signature') }}" class="underline font-semibold hover:text-yellow-900">Pending Signatures</a> to review and sign.</div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-green-50 border border-green-200 rounded-lg p-5">
                                    <div class="flex items-center mb-3">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h4 class="font-semibold text-green-800">Your Evaluation</h4>
                                    </div>
                                    <div class="flex items-center mb-3">
                                        <span class="text-sm font-semibold text-gray-700 mr-2">Rating:</span>
                                        <span class="inline-flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= $request->evaluation->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.385 2.46a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.54 1.118l-3.385-2.46a1 1 0 00-1.175 0l-3.385 2.46c-.784.57-1.838-.196-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118l-3.385-2.46c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.967z"/></svg>
                                            @endfor
                                        </span>
                                    </div>
                                    @if($request->evaluation->feedback)
                                    <div class="mb-3 p-3 bg-white rounded border border-green-100">
                                        <span class="text-sm font-semibold text-gray-700">Feedback:</span>
                                        <p class="text-sm text-gray-600 mt-1">{{ $request->evaluation->feedback }}</p>
                                    </div>
                                    @endif
                                    <div class="text-xs text-gray-500 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Submitted on {{ $request->evaluation->created_at->format('M j, Y g:i A') }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide session messages after 5 seconds
        const sessionMessages = document.querySelectorAll('.mb-4.p-4');
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