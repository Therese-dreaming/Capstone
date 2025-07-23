@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold">Repair Calls</h2>
            <p class="text-sm text-gray-600 mt-1">Track your repair requests and evaluate your technician after completion.</p>
        </div>

        {{-- Session Messages --}}
        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
            <div class="font-semibold">Success!</div>
            <div>{{ session('success') }}</div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
            <div class="font-semibold">Error!</div>
            <div>{{ session('error') }}</div>
        </div>
        @endif

        @if($requests->isEmpty())
            <div class="text-center text-gray-500 py-12">No repair requests found.</div>
        @else
            <div class="space-y-6">
                @foreach($requests as $request)
                <div class="border rounded-lg p-4 shadow-sm bg-gray-50">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-2">
                        <div>
                            <div class="font-semibold text-red-800">Ticket: {{ $request->ticket_number }}</div>
                            <div class="text-xs text-gray-500">Requested: {{ $request->created_at->format('M j, Y g:i A') }}</div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium whitespace-nowrap
                            @if($request->status === 'urgent') bg-red-100 text-red-800
                            @elseif($request->status === 'completed') bg-green-100 text-green-800
                            @elseif($request->status === 'pulled_out') bg-yellow-100 text-yellow-800
                            @elseif($request->status === 'cancelled') bg-red-100 text-red-800
                            @elseif($request->status === 'in_progress') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-700 mb-1"><span class="font-semibold">Equipment:</span> {{ $request->equipment }}</div>
                    <div class="text-sm text-gray-700 mb-1">
                        <span class="font-semibold">Location:</span> 
                        {{ $request->building }} - {{ $request->floor }} - {{ $request->room }}
                    </div>
                    <div class="text-sm text-gray-700 mb-1"><span class="font-semibold">Technician:</span> {{ $request->technician ? $request->technician->name : 'Not Assigned' }}</div>
                    <div class="text-sm text-gray-700 mb-1"><span class="font-semibold">Issue:</span> {{ $request->issue }}</div>
                    <div class="text-sm text-gray-700 mb-1"><span class="font-semibold">Status:</span> {{ ucfirst(str_replace('_', ' ', $request->status)) }}</div>
                    
                    {{-- Evaluation Section --}}
                    @if($request->status === 'completed' || $request->status === 'pulled_out')
                        <div class="mt-4">
                            @if(in_array($request->creator->group_id ?? null, [1, 2]))
                                <div class="bg-gray-50 border-l-4 border-gray-500 p-4 rounded">
                                    <div class="font-semibold text-gray-700 mb-1">Evaluation Status</div>
                                    <div class="text-sm text-gray-600">Evaluation not required for admin/technician-created requests.</div>
                                </div>
                            @elseif(!$request->evaluation)
                                <form method="POST" action="{{ route('repair.calls.evaluate', $request->id) }}" class="space-y-3" id="evaluation-form-{{ $request->id }}">
                                    @csrf
                                    <div>
                                        <label class="block text-gray-700 text-sm font-semibold mb-1">Technician Rating <span class="text-red-600">*</span></label>
                                        <div class="flex items-center space-x-4" id="rating-group-{{ $request->id }}">
                                            @for($i = 1; $i <= 5; $i++)
                                            <label class="flex items-center">
                                                <input type="radio" name="rating" value="{{ $i }}" class="hidden rating-radio" required>
                                                <div class="w-8 h-8 flex items-center justify-center border-2 border-gray-300 rounded-full cursor-pointer hover:bg-gray-100 rating-number">
                                                    {{ $i }}
                                                </div>
                                            </label>
                                            @endfor
                                        </div>
                                        <div class="text-sm text-red-600 mt-1 hidden" id="rating-error-{{ $request->id }}"></div>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-semibold mb-1" for="feedback-{{ $request->id }}">Feedback (Optional)</label>
                                        <textarea id="feedback-{{ $request->id }}" name="feedback" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none" placeholder="Share your experience with the technician"></textarea>
                                        <div class="text-sm text-red-600 mt-1 hidden" id="feedback-error-{{ $request->id }}"></div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox" id="is_anonymous-{{ $request->id }}" name="is_anonymous" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        <label for="is_anonymous-{{ $request->id }}" class="text-sm text-gray-700">Submit anonymously</label>
                                    </div>
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#960106] rounded-md hover:bg-[#7d0105] focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Submit Evaluation</button>
                                </form>
                            @else
                                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
                                    <div class="font-semibold text-green-700 mb-1">Your Evaluation</div>
                                    <div class="flex items-center mb-1">
                                        <span class="font-semibold mr-2">Rating:</span>
                                        <span class="inline-flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= $request->evaluation->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.385 2.46a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.54 1.118l-3.385-2.46a1 1 0 00-1.175 0l-3.385 2.46c-.784.57-1.838-.196-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118l-3.385-2.46c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.967z"/></svg>
                                            @endfor
                                        </span>
                                    </div>
                                    @if($request->evaluation->feedback)
                                    <div class="mb-1"><span class="font-semibold">Feedback:</span> {{ $request->evaluation->feedback }}</div>
                                    @endif
                                    <div class="text-xs text-gray-500">
                                        <span class="font-semibold">Submitted:</span> {{ $request->evaluation->created_at->format('M j, Y g:i A') }}
                                        ({{ $request->evaluation->is_anonymous ? 'Anonymously' : 'Not anonymous' }})
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

        // Rating selection functionality
        document.querySelectorAll('[id^="rating-group-"]').forEach(function(group) {
            const radios = group.querySelectorAll('.rating-radio');
            const numbers = group.querySelectorAll('.rating-number');
            
            numbers.forEach((number, idx) => {
                number.addEventListener('click', function() {
                    const radio = radios[idx];
                    radio.checked = true;
                    numbers.forEach(n => n.classList.remove('bg-red-500', 'text-white', 'border-red-500'));
                    this.classList.add('bg-red-500', 'text-white', 'border-red-500');
                });
            });

            radios.forEach((radio, idx) => {
                radio.addEventListener('change', function() {
                    numbers.forEach(n => n.classList.remove('bg-red-500', 'text-white', 'border-red-500'));
                    if (radio.checked) {
                        numbers[idx].classList.add('bg-red-500', 'text-white', 'border-red-500');
                    }
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
                
                // Handle is_anonymous checkbox
                const isAnonymousCheckbox = this.querySelector('input[name="is_anonymous"]');
                formData.set('is_anonymous', isAnonymousCheckbox.checked ? '1' : '0');
                
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