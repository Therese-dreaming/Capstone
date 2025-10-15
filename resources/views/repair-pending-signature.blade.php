@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Page Header -->
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                        <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Pending Signatures</h1>
                        <p class="text-red-100 text-sm md:text-lg">Review and sign completed repair work</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-4 md:mb-6 p-3 md:p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm md:text-base">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 md:mb-6 p-3 md:p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-center">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm md:text-base">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Pending Signatures Count Card -->
    <div class="mb-6">
        <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center">
                <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4 flex-shrink-0">
                    <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-orange-100 text-xs sm:text-sm font-medium mb-1">Pending Signatures</p>
                    <p class="text-3xl sm:text-4xl md:text-5xl font-bold">{{ $repairRequests->count() }}</p>
                    <p class="text-orange-100 text-xs sm:text-sm mt-1">
                        {{ $repairRequests->count() === 1 ? 'repair request' : 'repair requests' }} awaiting your signature
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if($repairRequests->isEmpty())
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-md p-6 md:p-8 text-center">
            <div class="bg-gray-50 rounded-full w-20 h-20 mx-auto mb-6 flex items-center justify-center">
                <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-xl md:text-2xl font-semibold text-gray-900 mb-3">No Pending Signatures</h3>
            <p class="text-gray-500 mb-6 text-sm md:text-base">All your repair requests have been signed or are not yet completed.</p>
        </div>
    @else
        <!-- Pending Repairs List -->
        <div class="space-y-4 md:space-y-6">
            @foreach($repairRequests as $repair)
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-4 md:p-6">
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-4 pb-4 border-b">
                        <div class="mb-3 sm:mb-0">
                            <h3 class="text-lg md:text-xl font-bold text-gray-900">{{ $repair->ticket_number }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $repair->equipment }}</p>
                        </div>
                        <div class="flex flex-col items-start sm:items-end space-y-2">
                            @php
                                $deadline = \Carbon\Carbon::parse($repair->signature_deadline);
                                $hoursLeft = now()->diffInHours($deadline, false);
                                $isUrgent = $hoursLeft < 24;
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $isUrgent ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                @if($hoursLeft > 0)
                                    {{ round($hoursLeft) }} hours left
                                @else
                                    Overdue
                                @endif
                            </span>
                            <p class="text-xs text-gray-500">Deadline: {{ $deadline->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>

                    <!-- Repair Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Location:</p>
                            <p class="text-sm text-gray-600">{{ $repair->building }} - Floor {{ $repair->floor }} - Room {{ $repair->room }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Technician:</p>
                            <p class="text-sm text-gray-600">{{ $repair->technician->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Completed At:</p>
                            <p class="text-sm text-gray-600">{{ $repair->completed_at->format('M d, Y g:i A') }}</p>
                        </div>
                        @if($repair->serial_number)
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Serial Number:</p>
                            <p class="text-sm text-gray-600">{{ $repair->serial_number }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Findings & Remarks -->
                    <div class="space-y-3 mb-4">
                        <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                            <p class="text-sm font-semibold text-blue-900 mb-1">Findings:</p>
                            <p class="text-sm text-blue-800">{{ $repair->findings }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <p class="text-sm font-semibold text-gray-900 mb-1">Remarks:</p>
                            <p class="text-sm text-gray-700">{{ $repair->remarks }}</p>
                        </div>
                    </div>

                    <!-- Photo Evidence -->
                    @if($repair->before_photos || $repair->after_photos)
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Photo Evidence</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($repair->before_photos && is_array($repair->before_photos))
                            <div>
                                <p class="text-xs font-medium text-gray-700 mb-2">Before Photos:</p>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($repair->before_photos as $photo)
                                    <img src="{{ asset('storage/' . $photo) }}" alt="Before" class="w-full h-32 object-cover rounded-lg border border-gray-300 cursor-pointer hover:opacity-75" onclick="openImageModal('{{ asset('storage/' . $photo) }}')">
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @if($repair->after_photos && is_array($repair->after_photos))
                            <div>
                                <p class="text-xs font-medium text-gray-700 mb-2">After Photos:</p>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($repair->after_photos as $photo)
                                    <img src="{{ asset('storage/' . $photo) }}" alt="After" class="w-full h-32 object-cover rounded-lg border border-gray-300 cursor-pointer hover:opacity-75" onclick="openImageModal('{{ asset('storage/' . $photo) }}')">
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Action Button -->
                    <div class="flex justify-end pt-4 border-t">
                        <button onclick="openSignatureModal({{ $repair->id }}, '{{ $repair->ticket_number }}')" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Review & Sign
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Signature Modal -->
<div id="signatureModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-xl bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900" id="modalTitle">Review & Sign</h3>
                <button onclick="closeSignatureModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="signatureForm" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="action" id="signatureAction" value="approve">

                <!-- Signature Pad -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Your Signature <span class="text-red-600">*</span></label>
                    <div class="border-2 border-gray-300 rounded-lg bg-white">
                        <canvas id="signaturePad" width="600" height="200" class="w-full cursor-crosshair"></canvas>
                    </div>
                    <div class="flex justify-between items-center">
                        <button type="button" onclick="clearSignature()" class="text-sm text-red-600 hover:text-red-800">
                            Clear Signature
                        </button>
                        <span class="text-sm text-gray-500">Please sign above</span>
                    </div>
                    <input type="hidden" name="caller_signature" id="callerSignatureData">
                </div>

                <!-- Rework Notes (shown when requesting rework) -->
                <div id="reworkNotesSection" class="hidden space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Rework Notes</label>
                    <textarea name="rework_notes" id="reworkNotes" rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                        placeholder="Describe what needs to be fixed or improved..."></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeSignatureModal()" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="button" onclick="submitRework()" 
                        class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        Request Rework
                    </button>
                    <button type="button" onclick="submitApproval()" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Approve & Sign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4" onclick="closeImageModal()">
    <div class="relative max-w-4xl max-h-full">
        <img id="modalImage" src="" alt="Full size" class="max-w-full max-h-screen object-contain rounded-lg">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<!-- Add SignaturePad library -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
    let signaturePad;
    let currentRepairId = null;

    function getSignatureCanvas() {
        return document.getElementById('signaturePad');
    }

    function resizeSignatureCanvas(canvas) {
        if (!canvas) return;
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        // Use the displayed size to compute the actual canvas size
        const displayWidth = canvas.offsetWidth || canvas.getBoundingClientRect().width || 600;
        const displayHeight = 200; // fixed display height via UI
        canvas.width = Math.floor(displayWidth * ratio);
        canvas.height = Math.floor(displayHeight * ratio);
        const ctx = canvas.getContext('2d');
        ctx.scale(ratio, ratio);
    }

    function initSignaturePad() {
        const canvas = getSignatureCanvas();
        if (!canvas) return;
        resizeSignatureCanvas(canvas);
        if (!signaturePad) {
            signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)',
                velocityFilterWeight: 0.7,
                minWidth: 0.5,
                maxWidth: 2.5,
                throttle: 16
            });
        } else {
            signaturePad.clear();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Defer init until modal is opened to avoid zero-size canvas
        window.addEventListener('resize', function() {
            if (!document.getElementById('signatureModal').classList.contains('hidden')) {
                const canvas = getSignatureCanvas();
                resizeSignatureCanvas(canvas);
                if (signaturePad) signaturePad.clear();
            }
        });
    });

    function openSignatureModal(repairId, ticketNumber) {
        currentRepairId = repairId;
        document.getElementById('modalTitle').textContent = `Review & Sign - ${ticketNumber}`;
        const modal = document.getElementById('signatureModal');
        modal.classList.remove('hidden');
        // Wait for modal to render so offsetWidth is correct
        requestAnimationFrame(() => {
            initSignaturePad();
        });
        document.getElementById('reworkNotesSection').classList.add('hidden');
        document.getElementById('signatureAction').value = 'approve';
    }

    function closeSignatureModal() {
        document.getElementById('signatureModal').classList.add('hidden');
        currentRepairId = null;
        if (signaturePad) {
            signaturePad.clear();
        }
    }

    function clearSignature() {
        if (signaturePad) {
            signaturePad.clear();
        }
    }

    function submitApproval() {
        if (!signaturePad || signaturePad.isEmpty()) {
            alert('Please provide your signature');
            return;
        }

        document.getElementById('signatureAction').value = 'approve';
        document.getElementById('callerSignatureData').value = signaturePad.toDataURL();
        
        const form = document.getElementById('signatureForm');
        form.action = `/repair-requests/${currentRepairId}/submit-signature`;
        form.submit();
    }

    function submitRework() {
        if (!signaturePad || signaturePad.isEmpty()) {
            alert('Please provide your signature');
            return;
        }

        // Show rework notes section if not visible
        const reworkSection = document.getElementById('reworkNotesSection');
        if (reworkSection.classList.contains('hidden')) {
            reworkSection.classList.remove('hidden');
            return;
        }

        const reworkNotes = document.getElementById('reworkNotes').value.trim();
        if (!reworkNotes) {
            alert('Please provide rework notes');
            return;
        }

        document.getElementById('signatureAction').value = 'rework';
        document.getElementById('callerSignatureData').value = signaturePad.toDataURL();
        
        const form = document.getElementById('signatureForm');
        form.action = `/repair-requests/${currentRepairId}/submit-signature`;
        form.submit();
    }

    function openImageModal(imageSrc) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }
</script>
@endsection
