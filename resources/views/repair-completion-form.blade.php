@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold">Repair Completion Form</h2>
            <p class="text-sm text-gray-600 mt-1">Please fill out the details of the repair completion</p>
        </div>


        <form id="completionForm" method="POST" action="{{ route('repair-requests.update', $repairRequest->id) }}" class="space-y-6" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="completed">
            <input type="hidden" name="completed_at" id="completedAt">
            <input type="hidden" name="serial_number" id="serialNumber" value="{{ $repairRequest->serial_number }}">

            @if(!$repairRequest->serial_number)
            <!-- Manual Serial Number Entry -->
            <div class="space-y-2">
                <label class="block text-gray-700 text-sm font-semibold" for="manualSerialInput">
                    Enter Serial Number (Manual Input)
                </label>
                <div class="relative">
                    <input type="text" id="manualSerialInput"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Type the serial number if QR scanning is unavailable"
                        value="{{ old('serial_number') }}">
                    <div id="serialSuggestions" class="absolute mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-48 overflow-y-auto hidden z-10"></div>
                </div>
                <p class="text-sm text-gray-500">
                    Start typing to search for existing serial numbers. Use this field when you cannot scan the QR code.
                </p>
            </div>
            @endif

            <!-- Asset Information Display -->
            <div class="space-y-2">
                <label class="block text-gray-700 text-sm font-semibold">
                    Asset Information
                </label>
                <div class="border border-gray-300 rounded-md p-4 bg-gray-50">
                    @if($repairRequest->serial_number)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Serial Number: {{ $repairRequest->serial_number }}</span>
                        </div>
                        @if($repairRequest->asset)
                            <div class="mt-2 text-sm text-gray-600">
                                Asset Name: {{ $repairRequest->asset->name }}
                            </div>
                        @endif
                    @else
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">No asset linked to this repair request</span>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('repair.identify-asset', $repairRequest->id) }}" class="text-sm text-red-600 hover:text-red-800 font-medium">
                                Click here to identify the asset first
                            </a>
                        </div>
                    @endif
                </div>
                <p class="text-sm text-gray-500">Asset information is displayed based on the identification done in the previous step.</p>
            </div>

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

            <!-- Photo Evidence Section (Optional) -->
            <div class="space-y-3 bg-blue-50 p-4 rounded-lg border border-blue-200">
                <div class="flex items-center space-x-2 mb-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <label class="text-sm font-semibold text-blue-900">Photo Evidence (Optional)</label>
                </div>
                <p class="text-xs text-blue-700 mb-3">Optionally upload before and after photos of the repair work</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-blue-900 mb-2">Before Photos</label>
                        <input type="file" name="before_photos[]" id="beforePhotosInput" multiple accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700"
                            onchange="previewImages(this, 'beforePhotosPreview')">
                        <p class="text-xs text-blue-600 mt-1">Upload 1 or more photos (Max 10MB each) - Optional</p>
                        <!-- Preview Container -->
                        <div id="beforePhotosPreview" class="grid grid-cols-2 gap-2 mt-3"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-900 mb-2">After Photos</label>
                        <input type="file" name="after_photos[]" id="afterPhotosInput" multiple accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700"
                            onchange="previewImages(this, 'afterPhotosPreview')">
                        <p class="text-xs text-blue-600 mt-1">Upload 1 or more photos (Max 10MB each) - Optional</p>
                        <!-- Preview Container -->
                        <div id="afterPhotosPreview" class="grid grid-cols-2 gap-2 mt-3"></div>
                    </div>
                </div>
            </div>

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

            <!-- Caller Presence Section -->
            <div class="space-y-3 bg-purple-50 p-4 rounded-lg border border-purple-200">
                <div class="flex items-center space-x-2 mb-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <label class="text-sm font-semibold text-purple-900">Is the caller present?</label>
                </div>
                <div class="flex items-center space-x-6 ml-7">
                    <label class="inline-flex items-center hover:cursor-pointer">
                        <input type="radio" name="caller_present" value="yes" class="form-radio text-purple-600 focus:ring-purple-600" checked onclick="toggleCallerPresence(true)">
                        <span class="ml-2 text-gray-700">Yes, Present</span>
                    </label>
                    <label class="inline-flex items-center hover:cursor-pointer">
                        <input type="radio" name="caller_present" value="no" class="form-radio text-purple-600 focus:ring-purple-600" onclick="toggleCallerPresence(false)">
                        <span class="ml-2 text-gray-700">No, Not Available</span>
                    </label>
                </div>
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

                <!-- Caller's Signature (shown when caller is present) -->
                @if($repairRequest->creator_id != auth()->id())
                <div id="callerSignatureSection" class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold">
                        Caller's Signature <span class="text-red-600">*</span>
                    </label>
                    <div class="border border-gray-300 rounded-md p-4 bg-green-50">
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

                <!-- Delegate Section (shown when caller is not present) -->
                <div id="delegateSection" class="hidden space-y-3 bg-amber-50 p-4 rounded-lg border border-amber-200">
                    <div class="flex items-center space-x-2 mb-2">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <label class="text-sm font-semibold text-amber-900">Authorized Delegate (Optional)</label>
                    </div>
                    <p class="text-xs text-amber-700 mb-3">If an authorized person is available to sign on behalf of the caller, enter their name below. Otherwise, leave blank and the caller will be notified to sign within 48 hours.</p>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-amber-900">Delegate Name</label>
                        <input type="text" name="delegate_name" id="delegateNameInput" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                            placeholder="Enter delegate's full name (optional)"
                            oninput="toggleDelegateSignature()">
                        <p class="text-xs text-amber-600">Enter the name of the person authorized to sign on behalf of the caller</p>
                    </div>

                    <!-- Delegate Signature (shown when delegate name is entered) -->
                    <div id="delegateSignatureSection" class="hidden space-y-3 mt-4">
                        <label class="block text-gray-700 text-sm font-semibold">
                            Delegate's Signature <span class="text-red-600">*</span>
                        </label>
                        <div class="border border-gray-300 rounded-md p-4 bg-white">
                            <canvas id="delegateSignature" class="signature-pad"></canvas>
                            <input type="hidden" name="delegate_signature" id="delegateSignatureInput">
                            <div class="flex justify-between items-center mt-2">
                                <button type="button" onclick="clearDelegateSignature()" class="text-sm text-red-600 hover:text-red-800">
                                    Clear Signature
                                </button>
                                <span class="text-sm text-gray-500">Delegate please sign above</span>
                            </div>
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
    let technicianPad, callerPad, delegatePad;
    let html5QrCode;
    const STORAGE_KEY_PREFIX = 'repair_completion_form_';
    const repairRequestId = {{ $repairRequest->id }};

    // Function to save signature to localStorage
    function saveSignatureToStorage(padName, pad) {
        if (pad && !pad.isEmpty()) {
            const key = STORAGE_KEY_PREFIX + repairRequestId + '_' + padName;
            localStorage.setItem(key, pad.toDataURL());
        }
    }

    // Function to restore signature from localStorage
    function restoreSignatureFromStorage(padName, pad) {
        if (!pad) return;
        const key = STORAGE_KEY_PREFIX + repairRequestId + '_' + padName;
        const savedSignature = localStorage.getItem(key);
        if (savedSignature) {
            pad.fromDataURL(savedSignature);
        }
    }

    // Function to clear saved signatures
    function clearSavedSignatures() {
        const keys = [
            STORAGE_KEY_PREFIX + repairRequestId + '_technician',
            STORAGE_KEY_PREFIX + repairRequestId + '_caller',
            STORAGE_KEY_PREFIX + repairRequestId + '_delegate'
        ];
        keys.forEach(key => localStorage.removeItem(key));
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize signature pads
        const technicianCanvas = document.getElementById('technicianSignature');
        const callerCanvas = document.getElementById('callerSignature');
        const manualSerialInput = document.getElementById('manualSerialInput');
        const hiddenSerialInput = document.getElementById('serialNumber');
        const serialSuggestionsEl = document.getElementById('serialSuggestions');
        let serialSuggestionTimeout = null;

        // Sync manual serial input with hidden field
        if (manualSerialInput && hiddenSerialInput) {
            // Ensure hidden input picks up any pre-filled value
            if (manualSerialInput.value) {
                hiddenSerialInput.value = manualSerialInput.value.trim();
            }

            manualSerialInput.addEventListener('input', function(e) {
                hiddenSerialInput.value = e.target.value.trim();
                handleSerialSuggestionFetch(e.target.value.trim());
            });

            manualSerialInput.addEventListener('focus', function(e) {
                if (e.target.value.trim()) {
                    handleSerialSuggestionFetch(e.target.value.trim());
                }
            });
        }

        function handleSerialSuggestionFetch(query) {
            if (!serialSuggestionsEl) return;
            if (serialSuggestionTimeout) {
                clearTimeout(serialSuggestionTimeout);
            }

            if (!query || query.length < 2) {
                serialSuggestionsEl.classList.add('hidden');
                return;
            }

            serialSuggestionTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(`/assets/search-serials?q=${encodeURIComponent(query)}`);
                    if (!response.ok) {
                        throw new Error('Failed to fetch serial numbers');
                    }
                    const data = await response.json();
                    renderSerialSuggestions(data);
                } catch (error) {
                    console.error('Serial suggestion error:', error);
                    serialSuggestionsEl.classList.add('hidden');
                }
            }, 250);
        }

        function renderSerialSuggestions(items) {
            if (!serialSuggestionsEl) return;
            if (!items || items.length === 0) {
                serialSuggestionsEl.classList.add('hidden');
                return;
            }

            serialSuggestionsEl.innerHTML = items.map(item => `
                <button type="button"
                        class="w-full text-left px-3 py-2 hover:bg-red-50 flex flex-col border-b border-gray-100 last:border-b-0"
                        data-serial="${item.serial_number}">
                    <span class="font-semibold text-gray-800">${item.serial_number}</span>
                    <span class="text-xs text-gray-500">${item.name ?? 'Unnamed Asset'}${item.status ? ` • ${item.status}` : ''}</span>
                </button>
            `).join('');

            serialSuggestionsEl.classList.remove('hidden');

            serialSuggestionsEl.querySelectorAll('button').forEach(button => {
                button.addEventListener('click', () => {
                    const serial = button.dataset.serial || '';
                    manualSerialInput.value = serial;
                    hiddenSerialInput.value = serial;
                    serialSuggestionsEl.classList.add('hidden');
                });
            });
        }

        document.addEventListener('click', function(event) {
            if (!serialSuggestionsEl || !manualSerialInput) return;
            const isClickInside = manualSerialInput.parentElement.contains(event.target) || serialSuggestionsEl.contains(event.target);
            if (!isClickInside) {
                serialSuggestionsEl.classList.add('hidden');
            }
        });

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

        // Restore technician signature from localStorage
        restoreSignatureFromStorage('technician', technicianPad);

        // Save technician signature when it changes
        technicianPad.addEventListener('endStroke', function() {
            saveSignatureToStorage('technician', technicianPad);
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

            // Restore caller signature from localStorage
            restoreSignatureFromStorage('caller', callerPad);

            // Save caller signature when it changes
            callerPad.addEventListener('endStroke', function() {
                saveSignatureToStorage('caller', callerPad);
            });
        }

        // Note: Delegate signature pad is initialized on-demand when delegate name is entered



        // Set current timestamp
        document.getElementById('completedAt').value = new Date().toISOString();

        // Handle form submission
        document.getElementById('completionForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate required fields
            const findings = document.getElementById('findings').value.trim();
            const remarks = document.getElementById('remarks').value.trim();
            const technicianSignature = technicianPad.toDataURL();
            const callerSignature = callerPad ? callerPad.toDataURL() : '';
            const callerPresent = (document.querySelector('input[name="caller_present"]:checked')?.value === 'yes');
            const delegateName = (document.getElementById('delegateNameInput')?.value || '').trim();

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

            // Signature rules
            if (callerPresent) {
                // Caller present: require caller signature
                if (!callerPad || callerPad.isEmpty()) {
                    showMessage('Please provide caller signature', 'error');
                    return;
                }
            } else {
                // Caller not present
                if (delegateName) {
                    // Delegate selected: require delegate signature
                    if (!delegatePad || delegatePad.isEmpty()) {
                        showMessage('Please provide delegate signature', 'error');
                        return;
                    }
                }
                // If no delegate name, allow deferred (no signature required here)
            }

            // Set signature data to hidden inputs
            document.getElementById('technicianSignatureInput').value = technicianSignature;
            
            // Only set caller signature if caller is present AND signed
            const callerSigInput = document.getElementById('callerSignatureInput');
            if (callerSigInput) {
                if (callerPresent && callerPad && !callerPad.isEmpty()) {
                    callerSigInput.value = callerPad.toDataURL();
                } else {
                    callerSigInput.value = ''; // Explicitly set to empty
                }
            }
            
            // Only set delegate signature if delegate name provided AND signed
            const delegateSigInput = document.getElementById('delegateSignatureInput');
            if (delegateSigInput) {
                if (delegateName && delegatePad && !delegatePad.isEmpty()) {
                    delegateSigInput.value = delegatePad.toDataURL();
                } else {
                    delegateSigInput.value = ''; // Explicitly set to empty
                }
            }

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
                    // Clear saved signatures on successful submission
                    clearSavedSignatures();
                    
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

        // Handle window resize - preserve signatures
        window.addEventListener('resize', function() {
            // Save current signatures before resize
            const techSig = technicianPad && !technicianPad.isEmpty() ? technicianPad.toDataURL() : null;
            const callerSig = callerPad && !callerPad.isEmpty() ? callerPad.toDataURL() : null;
            const delegateSig = delegatePad && !delegatePad.isEmpty() ? delegatePad.toDataURL() : null;
            
            // Resize technician canvas
            technicianCanvas.width = technicianCanvas.offsetWidth;
            technicianPad.clear();
            if (techSig) {
                technicianPad.fromDataURL(techSig);
            } else {
                restoreSignatureFromStorage('technician', technicianPad);
            }
            
            // Resize caller canvas if it exists
            if (callerCanvas && callerPad) {
                callerCanvas.width = callerCanvas.offsetWidth;
                callerPad.clear();
                if (callerSig) {
                    callerPad.fromDataURL(callerSig);
                } else {
                    restoreSignatureFromStorage('caller', callerPad);
                }
            }
            
            // Resize delegate canvas if it exists
            if (delegatePad) {
                const delegateCanvas = document.getElementById('delegateSignature');
                if (delegateCanvas) {
                    delegateCanvas.width = delegateCanvas.offsetWidth;
                    delegatePad.clear();
                    if (delegateSig) {
                        delegatePad.fromDataURL(delegateSig);
                    } else {
                        restoreSignatureFromStorage('delegate', delegatePad);
                    }
                }
            }
        });

        // Save signatures before page unload
        window.addEventListener('beforeunload', function() {
            saveSignatureToStorage('technician', technicianPad);
            if (callerPad) {
                saveSignatureToStorage('caller', callerPad);
            }
            if (delegatePad) {
                saveSignatureToStorage('delegate', delegatePad);
            }
        });

        // Save signatures periodically (every 5 seconds) as backup
        setInterval(function() {
            saveSignatureToStorage('technician', technicianPad);
            if (callerPad) {
                saveSignatureToStorage('caller', callerPad);
            }
            if (delegatePad) {
                saveSignatureToStorage('delegate', delegatePad);
            }
        }, 5000);
    });



    function clearTechnicianSignature() {
        if (technicianPad) {
            technicianPad.clear();
            // Clear from localStorage
            const key = STORAGE_KEY_PREFIX + repairRequestId + '_technician';
            localStorage.removeItem(key);
        }
    }

    function clearCallerSignature() {
        if (callerPad) {
            callerPad.clear();
            // Clear from localStorage
            const key = STORAGE_KEY_PREFIX + repairRequestId + '_caller';
            localStorage.removeItem(key);
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
        // For pull-out, do not force caller signature when caller is absent

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

        // Caller signature not mandatory here if opting for delegate/deferred

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
                // Clear saved signatures on successful submission
                clearSavedSignatures();
                
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

    // Function to show messages (updated to use toast)
    function showMessage(message, type = 'success') {
        if (type === 'success') {
            showSuccessToast(message);
        } else {
            showErrorToast(message);
        }
    }

    // Caller presence toggle
    function toggleCallerPresence(isPresent) {
        const callerSection = document.getElementById('callerSignatureSection');
        const delegateSection = document.getElementById('delegateSection');
        
        if (isPresent) {
            if (callerSection) callerSection.classList.remove('hidden');
            if (delegateSection) delegateSection.classList.add('hidden');
        } else {
            if (callerSection) callerSection.classList.add('hidden');
            if (delegateSection) delegateSection.classList.remove('hidden');
        }
    }

    // Toggle delegate signature section
    function toggleDelegateSignature() {
        const delegateNameInput = document.getElementById('delegateNameInput');
        const signatureSection = document.getElementById('delegateSignatureSection');
        
        if (delegateNameInput && delegateNameInput.value.trim()) {
            signatureSection.classList.remove('hidden');
            
            // Wait for the element to be visible before initializing
            setTimeout(() => {
                const delegateCanvas = document.getElementById('delegateSignature');
                if (delegateCanvas && !delegatePad) {
                    // Set canvas size based on visible dimensions
                    delegateCanvas.width = delegateCanvas.offsetWidth;
                    delegateCanvas.height = 200;
                    
                    delegatePad = new SignaturePad(delegateCanvas, {
                        backgroundColor: 'rgb(255, 255, 255)',
                        penColor: 'rgb(0, 0, 0)',
                        velocityFilterWeight: 0.7,
                        minWidth: 0.5,
                        maxWidth: 2.5,
                        throttle: 16
                    });
                    
                    // Restore delegate signature from localStorage
                    restoreSignatureFromStorage('delegate', delegatePad);
                    
                    // Save delegate signature when it changes
                    delegatePad.addEventListener('endStroke', function() {
                        saveSignatureToStorage('delegate', delegatePad);
                    });
                    
                    console.log('Delegate signature pad initialized');
                }
            }, 100);
        } else {
            signatureSection.classList.add('hidden');
            // Clear the pad when hiding
            if (delegatePad) {
                delegatePad.clear();
            }
        }
    }

    // Clear delegate signature
    function clearDelegateSignature() {
        if (delegatePad) {
            delegatePad.clear();
            // Clear from localStorage
            const key = STORAGE_KEY_PREFIX + repairRequestId + '_delegate';
            localStorage.removeItem(key);
        }
    }

    // Preview uploaded images
    function previewImages(input, previewContainerId) {
        const previewContainer = document.getElementById(previewContainerId);
        previewContainer.innerHTML = ''; // Clear previous previews
        
        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach((file, index) => {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    console.error('File is not an image:', file.name);
                    showErrorToast(`${file.name} is not a valid image file`);
                    return;
                }
                
                // Validate file size (10MB limit)
                const maxSize = 10 * 1024 * 1024; // 10MB in bytes
                if (file.size > maxSize) {
                    const fileSizeMB = (file.size / 1024 / 1024).toFixed(2);
                    showErrorToast(`${file.name} exceeds 10MB limit (${fileSizeMB}MB)`);
                    return;
                }
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    console.log('FileReader loaded, data length:', e.target.result.length);
                    console.log('Data starts with:', e.target.result.substring(0, 50));
                    
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'relative group';
                    
                    // Create image element
                    const img = document.createElement('img');
                    img.className = 'w-full h-32 object-cover rounded-lg border-2 border-blue-300 cursor-pointer hover:border-blue-500 transition-colors bg-white';
                    img.alt = `Preview ${index + 1}`;
                    img.style.minHeight = '128px';
                    img.style.display = 'block';
                    img.style.position = 'relative';
                    img.style.zIndex = '1';
                    
                    // Set src after adding to DOM to ensure proper loading
                    img.onload = function() {
                        console.log('Image loaded successfully:', file.name);
                        console.log('Image dimensions:', this.naturalWidth, 'x', this.naturalHeight);
                    };
                    
                    img.onerror = function(err) {
                        console.error('Failed to load image:', file.name, err);
                        img.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100"%3E%3Crect fill="%23ddd" width="100" height="100"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3EError%3C/text%3E%3C/svg%3E';
                    };
                    
                    img.onclick = function() {
                        openImagePreviewModal(e.target.result);
                    };
                    
                    // Create overlay (for hover effect only)
                    const overlay = document.createElement('div');
                    overlay.className = 'absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity rounded-lg flex items-center justify-center pointer-events-none';
                    overlay.style.zIndex = '10';
                    overlay.innerHTML = `
                        <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                        </svg>
                    `;
                    
                    // Create filename label
                    const filename = document.createElement('p');
                    filename.className = 'text-xs text-center text-blue-700 mt-1 truncate';
                    filename.textContent = file.name;
                    
                    // Append elements (don't add overlay for now to test)
                    previewDiv.appendChild(img);
                    // previewDiv.appendChild(overlay); // Temporarily disabled
                    previewDiv.appendChild(filename);
                    previewContainer.appendChild(previewDiv);
                    
                    // Set src AFTER appending to DOM
                    img.src = e.target.result;
                }
                
                reader.onerror = function() {
                    console.error('FileReader error for:', file.name);
                };
                
                reader.readAsDataURL(file);
            });
        }
    }

    // Open image preview modal
    function openImagePreviewModal(imageSrc) {
        const modal = document.createElement('div');
        modal.id = 'imagePreviewModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4';
        modal.onclick = function() { this.remove(); };
        
        // Create container
        const container = document.createElement('div');
        container.className = 'relative max-w-4xl max-h-full';
        container.onclick = function(e) { e.stopPropagation(); };
        
        // Create image
        const img = document.createElement('img');
        img.src = imageSrc;
        img.className = 'max-w-full max-h-screen object-contain rounded-lg shadow-2xl';
        
        // Create close button
        const closeBtn = document.createElement('button');
        closeBtn.className = 'absolute top-4 right-4 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors shadow-lg';
        closeBtn.onclick = function() {
            document.getElementById('imagePreviewModal').remove();
        };
        closeBtn.innerHTML = `
            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        `;
        
        // Append elements
        container.appendChild(img);
        container.appendChild(closeBtn);
        modal.appendChild(container);
        document.body.appendChild(modal);
    }

    // Toast notification functions
    function showToastMessage() {
        @if(session('success'))
            showSuccessToast("{{ session('success') }}");
        @endif

        @if(session('error'))
            showErrorToast("{{ session('error') }}");
        @endif
    }

    function showSuccessToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 z-[70] bg-green-50 border border-green-200 rounded-xl text-green-700 p-4 flex items-center shadow-lg max-w-md animate-slide-in';
        toast.innerHTML = `
            <svg class="w-5 h-5 mr-3 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex-1">
                <div class="font-semibold">Success!</div>
                <div class="text-sm">${message}</div>
            </div>
            <button onclick="this.parentElement.remove()" class="ml-3 text-green-600 hover:text-green-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }

    function showErrorToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 z-[70] bg-red-50 border border-red-200 rounded-xl text-red-700 p-4 flex items-center shadow-lg max-w-md animate-slide-in';
        toast.innerHTML = `
            <svg class="w-5 h-5 mr-3 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="flex-1">
                <div class="font-semibold">Error!</div>
                <div class="text-sm">${message}</div>
            </div>
            <button onclick="this.parentElement.remove()" class="ml-3 text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }

    // Show toast on page load if there are session messages
    document.addEventListener('DOMContentLoaded', showToastMessage);
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

    @keyframes slide-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
    }
</style>
@endsection 