@extends('layouts.app')

@section('content')
<div class="flex-1 p-8 @auth ml-72 @else mx-auto max-w-6xl @endauth">
    <div class="@auth max-w-4xl @else max-w-full @endauth mx-auto">
        <div class="flex items-center mb-8 justify-center">
            <svg class="w-8 h-8 mr-3 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h1 class="text-3xl font-bold">Lab Logging</h1>
        </div>

        <!-- RFID Attendance Section -->
        <div class="mb-8 p-8 bg-white rounded-lg shadow-lg transform transition-all duration-300 hover:shadow-xl">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
                <h2 class="text-lg font-semibold">RFID Attendance</h2>
            </div>

            <!-- Laboratory Selection -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Select Laboratory</span>
                    </div>
                    <span id="selectedLabText" class="text-sm font-medium text-red-600">Selected: None</span>
                </label>

                <div class="grid grid-cols-3 gap-4" id="labCards">
                    @foreach($laboratories as $lab)
                    <div class="lab-card cursor-pointer transform transition-all duration-300 hover:scale-105" data-value="{{ $lab }}" onclick="selectLab(this)">
                        <div class="relative bg-white rounded-lg border-2 border-transparent hover:border-red-500 shadow-md p-4 group transition-all duration-300">
                            <!-- Active State Indicator -->
                            <div class="absolute inset-0 bg-red-50 opacity-0 transition-opacity duration-300 rounded-lg"></div>

                            <!-- Checkmark Icon (Visible when active) -->
                            <div class="absolute top-3 right-3 transition-all duration-300 opacity-0 group-hover:opacity-100 active-checkmark">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                                </svg>
                            </div>

                            <!-- Lab Number -->
                            <div class="text-2xl font-bold text-gray-800 mb-2 relative">{{ $lab }}</div>

                            <!-- Status Indicator -->
                            <div class="flex items-center gap-2 text-sm text-gray-500 relative">
                                <div class="flex items-center">
                                    <span class="inline-block w-2 h-2 rounded-full mr-2"></span>
                                    <span class="status-text">Available</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Attendance Information Display -->
            <div id="attendanceInfo" class="hidden mt-6 border border-gray-200 rounded-xl p-6 transform transition-all duration-300 opacity-0 bg-white shadow-lg hover:shadow-xl">
                <div class="mb-6 pb-4 border-b border-gray-200 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="text-xl font-bold text-gray-800">Attendance Details</h3>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <!-- Faculty Information -->
                    <div class="p-5 bg-white rounded-xl transition-all duration-300 hover:bg-gray-50 border border-gray-200 shadow-sm hover:shadow">
                        <div class="flex items-center mb-3">
                            <div class="p-2 bg-red-50 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Faculty Name</p>
                        </div>
                        <p id="facultyName" class="text-lg font-bold mt-1 text-gray-900 pl-12">-</p>
                    </div>

                    <!-- Date -->
                    <div class="p-5 bg-white rounded-xl transition-all duration-300 hover:bg-gray-50 border border-gray-200 shadow-sm hover:shadow">
                        <div class="flex items-center mb-3">
                            <div class="p-2 bg-red-50 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Date</p>
                        </div>
                        <p id="currentDate" class="text-lg font-bold mt-1 text-gray-900 pl-12">-</p>
                    </div>

                    <!-- Time In -->
                    <div class="p-5 bg-white rounded-xl transition-all duration-300 hover:bg-gray-50 border border-gray-200 shadow-sm hover:shadow">
                        <div class="flex items-center mb-3">
                            <div class="p-2 bg-green-50 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Time In</p>
                        </div>
                        <p id="timeIn" class="text-lg font-bold mt-1 text-green-600 pl-12">-</p>
                    </div>

                    <!-- Time Out -->
                    <div class="p-5 bg-white rounded-xl transition-all duration-300 hover:bg-gray-50 border border-gray-200 shadow-sm hover:shadow">
                        <div class="flex items-center mb-3">
                            <div class="p-2 bg-red-50 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Time Out</p>
                        </div>
                        <p id="timeOut" class="text-lg font-bold mt-1 text-red-600 pl-12">-</p>
                    </div>

                    <!-- Status Section with Color Legend -->
                    <div class="col-span-2 p-5 bg-white rounded-xl transition-all duration-300 hover:bg-gray-50 border border-gray-200 shadow-sm hover:shadow">
                        <div class="flex items-center mb-3">
                            <div class="p-2 bg-blue-50 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Status</p>
                        </div>
                        <p id="logStatus" class="text-lg font-bold mt-1 pl-12">-</p>

                        <!-- Status Legend -->
                        <div class="mt-4 flex gap-6 text-sm pl-12">
                            <div class="flex items-center px-3 py-2 bg-red-50 rounded-lg">
                                <span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                                <span class="text-gray-700 font-medium">On-going</span>
                            </div>
                            <div class="flex items-center px-3 py-2 bg-green-50 rounded-lg">
                                <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                                <span class="text-gray-700 font-medium">Completed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Indicator -->
            <div id="statusIndicator" class="hidden mt-4 p-4 rounded-xl text-center transform transition-all duration-300 scale-95 opacity-0"></div>
        </div>

        <!-- Instructions Card -->
        <div class="bg-white rounded-lg shadow-lg p-8 transform transition-all duration-300 hover:shadow-xl">
            <div class="flex items-center mb-3">
                <svg class="w-5 h-5 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-lg font-semibold">Instructions</h3>
            </div>
            <ul class="list-none space-y-3 text-gray-600">
                <li class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Select the laboratory where you will conduct your class
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Tap your RFID card when entering the laboratory (Time In)
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Tap your RFID card again when leaving the laboratory (Time Out)
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Ensure your card is properly scanned and wait for the confirmation
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Tap Card Modal -->
<div id="tapCardModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
    <div class="relative bg-white rounded-lg shadow-xl mx-auto p-8 max-w-md w-full transform transition-all duration-300 scale-95">
        <!-- Exit Button -->
        <button onclick="closeTapCardModal()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Ready to Scan</h3>
            <p class="text-sm text-gray-500 mb-4">Please tap your RFID card to record your attendance.</p>
            <div class="animate-pulse flex justify-center">
                <div class="h-2 w-2 bg-red-600 rounded-full mx-1"></div>
                <div class="h-2 w-2 bg-red-600 rounded-full mx-1 animation-delay-200"></div>
                <div class="h-2 w-2 bg-red-600 rounded-full mx-1 animation-delay-400"></div>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    let selectedLab = null;
    let isProcessing = false;

    // Function to handle lab selection
    function selectLab(element) {
        // Remove active state from all cards
        document.querySelectorAll('.lab-card').forEach(card => {
            card.querySelector('.absolute').classList.remove('opacity-50');
            card.querySelector('.active-checkmark').classList.remove('opacity-100');
            card.querySelector('.border-2').classList.remove('border-red-500');
        });

        // Add active state to selected card
        element.querySelector('.absolute').classList.add('opacity-50');
        element.querySelector('.active-checkmark').classList.add('opacity-100');
        element.querySelector('.border-2').classList.add('border-red-500');

        // Update selected lab
        selectedLab = element.dataset.value;
        document.getElementById('selectedLabText').textContent = `Selected: ${selectedLab}`;

        // Show tap card modal
        document.getElementById('tapCardModal').classList.remove('hidden');
        startRFIDListener();
    }

    // Function to close tap card modal
    function closeTapCardModal() {
        document.getElementById('tapCardModal').classList.add('hidden');
        stopRFIDListener();
    }

    // Function to start RFID listener
    function startRFIDListener() {
        if (window.RFIDListener) return; // Prevent multiple listeners

        window.RFIDListener = document.addEventListener('keypress', async function(e) {
            if (isProcessing) return;

            let rfidNumber = '';
            let lastKeyTime = Date.now();
            let collectingRFID = false;

            if (!collectingRFID) {
                collectingRFID = true;
                rfidNumber = e.key;

                // Collect RFID number
                document.addEventListener('keypress', function collectKeys(e) {
                    const currentTime = Date.now();
                    if (currentTime - lastKeyTime > 100) { // Reset if too much time between keys
                        rfidNumber = e.key;
                    } else {
                        rfidNumber += e.key;
                    }
                    lastKeyTime = currentTime;

                    // Check if we have a complete RFID number (usually 10 digits)
                    if (rfidNumber.length >= 10) {
                        document.removeEventListener('keypress', collectKeys);
                        handleRFIDScan(rfidNumber);
                        collectingRFID = false;
                    }
                });
            }
        });
    }

    // Function to stop RFID listener
    function stopRFIDListener() {
        if (window.RFIDListener) {
            document.removeEventListener('keypress', window.RFIDListener);
            window.RFIDListener = null;
        }
    }

    // Function to handle RFID scan
    async function handleRFIDScan(rfidNumber) {
        if (!selectedLab || isProcessing) return;
        isProcessing = true;

        try {
            const response = await fetch('/lab-schedule/rfid-attendance', {
                method: 'POST'
                , headers: {
                    'Content-Type': 'application/json'
                    , 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
                , body: JSON.stringify({
                    rfid_number: rfidNumber
                    , laboratory: selectedLab
                })
            });

            const data = await response.json();

            if (data.success) {
                // Update attendance information
                document.getElementById('facultyName').textContent = data.faculty_name;
                document.getElementById('currentDate').textContent = new Date().toLocaleDateString();

                // Format time in
                const timeInDate = data.time_in ? new Date(data.time_in) : null;
                document.getElementById('timeIn').textContent = timeInDate ? 
                    timeInDate.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : '-';

                // Format time out
                const timeOutDate = data.time_out ? new Date(data.time_out) : null;
                document.getElementById('timeOut').textContent = timeOutDate ? 
                    timeOutDate.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : '-';

                const logStatus = document.getElementById('logStatus');
                logStatus.textContent = data.status;
                // Update status color based on status
                if (data.status === 'on-going') {
                    logStatus.className = 'text-lg font-semibold mt-1 text-red-600';
                } else if (data.status === 'completed') {
                    logStatus.className = 'text-lg font-semibold mt-1 text-green-600';
                }

                // Show attendance info
                const attendanceInfo = document.getElementById('attendanceInfo');
                attendanceInfo.classList.remove('hidden', 'opacity-0');

                // Show success message
                showStatus('success', data.message);
            } else {
                showStatus('error', data.message || 'Failed to process RFID');
            }
        } catch (error) {
            showStatus('error', 'Error processing RFID scan');
            console.error('Error:', error);
        } finally {
            isProcessing = false;
            closeTapCardModal();
        }
    }

    // Function to show status message
    function showStatus(type, message) {
        const statusIndicator = document.getElementById('statusIndicator');
        statusIndicator.className = `mt-4 p-3 rounded-lg text-center transform transition-all duration-300 ${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
        statusIndicator.textContent = message;
        statusIndicator.classList.remove('hidden', 'opacity-0', 'scale-95');

        // Hide status after 3 seconds
        setTimeout(() => {
            statusIndicator.classList.add('opacity-0', 'scale-95');
            setTimeout(() => statusIndicator.classList.add('hidden'), 300);
        }, 3000);
    }

    // Clean up event listeners when the page is unloaded
    window.addEventListener('unload', () => {
        stopRFIDListener();
    });

    // Add this function to check lab availability
    async function checkLabAvailability(labNumber) {
        try {
            const response = await fetch(`/lab-schedule/check-availability/${labNumber}`);
            const data = await response.json();

            const statusDot = document.querySelector(`[data-value="${labNumber}"] .rounded-full`);
            const statusText = document.querySelector(`[data-value="${labNumber}"] .text-gray-500 span:last-child`);

            if (data.status === 'ongoing') {
                statusDot.classList.remove('bg-green-500');
                statusDot.classList.add('bg-red-500');
                statusText.textContent = 'Unavailable';
            } else {
                statusDot.classList.remove('bg-red-500');
                statusDot.classList.add('bg-green-500');
                statusText.textContent = 'Available';
            }
        } catch (error) {
            console.error('Error checking lab availability:', error);
        }
    }

    // Call this function when the page loads and periodically
    document.addEventListener('DOMContentLoaded', () => {
        const labCards = document.querySelectorAll('.lab-card');
        labCards.forEach(card => {
            const labNumber = card.dataset.value;
            checkLabAvailability(labNumber);
        });

        // Check availability every 30 seconds
        setInterval(() => {
            labCards.forEach(card => {
                const labNumber = card.dataset.value;
                checkLabAvailability(labNumber);
            });
        }, 30000);
    });

    function updateLabStatus(labNumber, status) {
        const labCard = document.querySelector(`[data-value="${labNumber}"]`);
        if (!labCard) return;

        const statusDot = labCard.querySelector('.rounded-full');
        const statusText = labCard.querySelector('.status-text');

        if (status === 'on-going') {
            statusDot.className = 'inline-block w-2 h-2 rounded-full mr-2 bg-red-500';
            statusText.textContent = 'Unavailable';
        } else {
            statusDot.className = 'inline-block w-2 h-2 rounded-full mr-2 bg-green-500';
            statusText.textContent = 'Available';
        }
    }

    // Function to fetch all labs status
    async function fetchLabsStatus() {
        try {
            const response = await fetch('/lab-schedule/all-labs-status');
            const data = await response.json();
            data.forEach(lab => {
                updateLabStatus(lab.laboratory, lab.status);
            });
        } catch (error) {
            console.error('Error fetching lab status:', error);
        }
    }

    // Initial load
    document.addEventListener('DOMContentLoaded', () => {
        fetchLabsStatus(); // Fetch immediately when page loads

        // Update every 5 seconds
        setInterval(fetchLabsStatus, 5000);
    });

</script>
