@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold">Repair Completion Form</h2>
            <p class="text-sm text-gray-600 mt-1">Please fill out the details of the repair completion</p>
        </div>

        {{-- Message Container --}}
        <div id="message-container">
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
        </div>

        <form id="completionForm" method="POST" action="{{ route('repair-requests.update', $repairRequest->id) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="completed">
            <input type="hidden" name="completed_at" id="completedAt">

            <!-- Caller's Name -->
            @if(!in_array($repairRequest->creator->group_id ?? null, [1, 2]))
            <div class="space-y-2">
                <label class="block text-gray-700 text-sm font-semibold" for="caller_name">
                    Caller's Name (Pre-filled)
                </label>
                <input type="text" id="caller_name" name="caller_name" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    placeholder="Enter caller's name"
                    value="{{ $repairRequest->creator->name }}"
                    readonly>
                <p class="text-sm text-gray-500">This field is pre-filled with the name of the person who created the request.</p>
            </div>
            @endif

            <!-- Findings -->
            <div class="space-y-2">
                <label class="block text-gray-700 text-sm font-semibold" for="findings">
                    Findings
                </label>
                <textarea id="findings" name="findings" rows="3" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                    placeholder="Describe what was found during the repair"></textarea>
            </div>

            <!-- Remarks -->
            <div class="space-y-2">
                <label class="block text-gray-700 text-sm font-semibold" for="remarks">
                    Remarks
                </label>
                <textarea id="remarks" name="remarks" rows="3" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                    placeholder="Enter any additional remarks about the repair"></textarea>
            </div>

            <!-- Signatures Section -->
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-lg font-semibold">Acknowledgement</h3>
                
                <!-- Technician Signature -->
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold">
                        Technician's Signature <span class="text-red-600">*</span>
                    </label>
                    <div class="border border-gray-300 rounded-md p-4">
                        <canvas id="technicianSignature" class="signature-pad"></canvas>
                        <input type="hidden" name="technician_signature" id="technicianSignatureInput">
                        <div class="flex justify-between items-center mt-2">
                            <button type="button" onclick="clearTechnicianSignature()" class="text-sm text-red-600 hover:text-red-800">
                                Clear Signature
                            </button>
                            <span class="text-sm text-gray-500">Please sign above</span>
                        </div>
                    </div>
                </div>

                <!-- Caller's Signature -->
                @if(!in_array($repairRequest->creator->group_id ?? null, [1, 2]))
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold">
                        Caller's Signature <span class="text-red-600">*</span>
                    </label>
                    <div class="border border-gray-300 rounded-md p-4">
                        <canvas id="callerSignature" class="signature-pad"></canvas>
                        <input type="hidden" name="caller_signature" id="callerSignatureInput">
                        <div class="flex justify-between items-center mt-2">
                            <button type="button" onclick="clearCallerSignature()" class="text-sm text-red-600 hover:text-red-800">
                                Clear Signature
                            </button>
                            <span class="text-sm text-gray-500">Please sign above</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Evaluation Section -->
            @auth
            <!-- Technician Evaluation section removed and will be shown in Repair Calls page -->
            @endauth

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="window.history.back()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Cancel
                </button>
                <button type="button" onclick="showPullOutConfirmation()" 
                    class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">
                    Pull Out Asset
                </button>
                <button type="submit" 
                    class="px-4 py-2 text-sm font-medium text-white bg-[#960106] rounded-md hover:bg-[#7d0105] focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Complete Repair
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Pull Out Confirmation Modal -->
<div id="pullOutModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-[51]">
    <div class="bg-white p-8 rounded-lg shadow-xl relative">
        <h2 class="text-xl font-bold mb-4">Pull Out Asset</h2>
        <p class="mb-4">Do you want to pull out this asset?</p>
        <div class="flex justify-end">
            <button type="button" onclick="closePullOutModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">No</button>
            <button type="button" onclick="confirmPullOut()" class="bg-red-600 text-white px-4 py-2 rounded">Yes</button>
        </div>
    </div>
</div>

<!-- Add SignaturePad library -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
    let technicianPad, callerPad;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize signature pads
        const technicianCanvas = document.getElementById('technicianSignature');
        const callerCanvas = document.getElementById('callerSignature');

        // Set canvas dimensions
        technicianCanvas.width = technicianCanvas.offsetWidth;
        technicianCanvas.height = 200;
        
        // Initialize technician signature pad
        technicianPad = new SignaturePad(technicianCanvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)',
            velocityFilterWeight: 0.7,
            minWidth: 0.5,
            maxWidth: 2.5,
            throttle: 16
        });

        // Only initialize caller signature pad if the canvas exists
        if (callerCanvas) {
            callerCanvas.width = callerCanvas.offsetWidth;
            callerCanvas.height = 200;
            
            callerPad = new SignaturePad(callerCanvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)',
                velocityFilterWeight: 0.7,
                minWidth: 0.5,
                maxWidth: 2.5,
                throttle: 16
            });
        }

        // Set current timestamp
        document.getElementById('completedAt').value = new Date().toISOString();

        // Handle form submission
        document.getElementById('completionForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate required fields
            const findings = document.getElementById('findings').value.trim();
            const remarks = document.getElementById('remarks').value.trim();
            const technicianSignature = technicianPad.toDataURL();
            const callerSignature = callerPad.toDataURL();

            if (!findings) {
                showMessage('Please enter the findings', 'error');
                return;
            }

            if (!remarks) {
                showMessage('Please enter remarks', 'error');
                return;
            }

            if (technicianPad.isEmpty()) {
                showMessage('Please provide technician signature', 'error');
                return;
            }

            @if(!in_array($repairRequest->creator->group_id ?? null, [1, 2]))
            if (callerPad.isEmpty()) {
                showMessage('Please provide caller signature', 'error');
                return;
            }
            @endif

            // Set signature data to hidden inputs
            document.getElementById('technicianSignatureInput').value = technicianSignature;
            @if(!in_array($repairRequest->creator->group_id ?? null, [1, 2]))
            document.getElementById('callerSignatureInput').value = callerSignature;
            @endif

            // Submit the form via AJAX
            const formData = new FormData(this);
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
                    showMessage('Repair has been successfully completed.', 'success');
                    
                    // Redirect to repair status page after a short delay
                    setTimeout(() => {
                        window.location.href = '{{ route("repair.status") }}';
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Failed to complete repair');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage(error.message || 'An error occurred while completing the repair', 'error');
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            // Resize canvases
            technicianCanvas.width = technicianCanvas.offsetWidth;
            if (callerCanvas) {
                callerCanvas.width = callerCanvas.offsetWidth;
                callerPad.clear();
            }
            
            // Clear signatures
            technicianPad.clear();
        });
    });

    function clearTechnicianSignature() {
        if (technicianPad) {
            technicianPad.clear();
        }
    }

    function clearCallerSignature() {
        if (callerPad) {
            callerPad.clear();
        }
    }

    // Pull Out Asset Functions
    function showPullOutConfirmation() {
        document.getElementById('pullOutModal').classList.remove('hidden');
        document.getElementById('pullOutModal').classList.add('flex');
    }

    function closePullOutModal() {
        document.getElementById('pullOutModal').classList.remove('flex');
        document.getElementById('pullOutModal').classList.add('hidden');
    }

    function confirmPullOut() {
        const form = document.getElementById('completionForm');
        const formData = new FormData(form);
        formData.set('status', 'pulled_out');

        // Set current timestamp
        formData.set('completed_at', new Date().toISOString());

        // Add signatures to formData
        if (!technicianPad.isEmpty()) {
            formData.set('technician_signature', technicianPad.toDataURL());
        }
        @if(!in_array($repairRequest->creator->group_id ?? null, [1, 2]))
        if (!callerPad.isEmpty()) {
            formData.set('caller_signature', callerPad.toDataURL());
        }
        @endif

        // Validate required fields
        const findings = document.getElementById('findings').value.trim();
        const remarks = document.getElementById('remarks').value.trim();

        if (!findings) {
            showMessage('Please enter the findings', 'error');
            return;
        }

        if (!remarks) {
            showMessage('Please enter remarks', 'error');
            return;
        }

        if (technicianPad.isEmpty()) {
            showMessage('Please provide technician signature', 'error');
            return;
        }

        @if(!in_array($repairRequest->creator->group_id ?? null, [1, 2]))
        if (callerPad.isEmpty()) {
            showMessage('Please provide caller signature', 'error');
            return;
        }
        @endif

        // Submit the form with updated status
        fetch(form.action, {
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
                // Close modal
                closePullOutModal();
                
                // Show success message
                showMessage('Asset has been successfully pulled out.', 'success');
                
                // Redirect to repair details page if it's a non-registered asset
                @if(empty($repairRequest->serial_number))
                setTimeout(() => {
                    window.location.href = '{{ route("repair.details", ["id" => $repairRequest->id]) }}';
                }, 1000);
                @else
                // Redirect to repair status page for registered assets
                setTimeout(() => {
                    window.location.href = '{{ route("repair.status") }}';
                }, 1000);
                @endif
            } else {
                throw new Error(data.message || 'Failed to pull out asset');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage(error.message || 'An error occurred while pulling out the asset', 'error');
        });
    }

    // Function to show messages
    function showMessage(message, type = 'success') {
        const messageContainer = document.getElementById('message-container');
        const messageDiv = document.createElement('div');
        
        messageDiv.className = type === 'success' 
            ? 'mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700'
            : 'mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700';
        
        messageDiv.innerHTML = `
            <strong class="font-bold">${type === 'success' ? 'Success!' : 'Error!'}</strong>
            <span class="block sm:inline">${message}</span>
        `;
        
        // Clear any existing messages
        messageContainer.innerHTML = '';
        messageContainer.appendChild(messageDiv);
        
        // Auto-hide the message after 5 seconds
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
</script>

<style>
    .signature-pad {
        width: 100%;
        height: 200px;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        background-color: white;
        touch-action: none;
    }
</style>
@endsection 