@extends('layouts.app')

@section('content')
<div class="flex-1 ml-80">
    <div class="p-6">
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

        <!-- Main Container -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold">Laboratory Logging</h1>
                    <p class="text-gray-600">Tap your RFID card to log laboratory usage</p>
                </div>
                @guest
                <a href="{{ route('login') }}" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700">
                    Login
                </a>
                @endguest
            </div>

            <!-- RFID Input Section -->
            <div class="mb-6">
                <div class="max-w-md mx-auto bg-gray-100 p-6 rounded-lg text-center">
                    <input type="text" id="rfidInput" class="w-full border rounded p-2 text-sm bg-white" placeholder="Tap your RFID card..." autofocus>
                    <p class="text-sm text-gray-600 mt-2">Please tap your RFID card to automatically log your schedule</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 text-center mt-4">Confirm Schedule</h3>
            <div class="mt-4 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">Please confirm this is your schedule:</p>
                <div class="space-y-3 text-sm">
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-semibold">Laboratory:</span>
                        <span id="modalLaboratory" class="col-span-2"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-semibold">Date:</span>
                        <span id="modalDate" class="col-span-2"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-semibold">Time:</span>
                        <span id="modalTime" class="col-span-2"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-semibold">Professor:</span>
                        <span id="modalProfessor" class="col-span-2"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-semibold">Subject:</span>
                        <span id="modalSubject" class="col-span-2"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end space-x-3 mt-6">
            <button id="cancelBtn" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded hover:bg-gray-300">
                Cancel
            </button>
            <button id="confirmBtn" class="px-4 py-2 bg-red-800 text-white text-sm font-medium rounded hover:bg-red-700">
                Confirm Log
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rfidInput = document.getElementById('rfidInput');
    const modal = document.getElementById('confirmationModal');
    let scheduleData = null;
    let lastTapTime = {}; // Store last tap time for each RFID

    rfidInput.addEventListener('input', async function() {
        if (this.value.length >= 8) { // Assuming RFID has minimum 8 characters
            const currentRfid = this.value;
            const now = new Date().getTime();
            const lastTap = lastTapTime[currentRfid] || 0;
            const timeDiff = now - lastTap;

            // Check if less than 15 minutes (900000 milliseconds) have passed since last tap
            if (timeDiff < 900000 && lastTap !== 0) {
                // Create and show error message for rapid tapping
                const errorDiv = document.createElement('div');
                errorDiv.className = 'mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700';
                errorDiv.textContent = 'Please wait 15 minutes before tapping again.';
                
                // Insert the message at the top of the content area
                const contentArea = document.querySelector('.p-6');
                contentArea.insertBefore(errorDiv, contentArea.firstChild);
                
                // Remove the message after 3 seconds
                setTimeout(() => {
                    errorDiv.remove();
                }, 3000);
                
                // Clear RFID input
                this.value = '';
                return;
            }

            try {
                const response = await fetch('/lab-logging/get-schedule', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        rfid: currentRfid
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Update last tap time for this RFID
                    lastTapTime[currentRfid] = now;
                    
                    scheduleData = data.schedule;
                    
                    // Fill modal with schedule data
                    document.getElementById('modalLaboratory').textContent = data.schedule.laboratory;
                    document.getElementById('modalDate').textContent = data.schedule.date;
                    document.getElementById('modalTime').textContent = `${data.schedule.time_in} - ${data.schedule.time_out}`;
                    document.getElementById('modalProfessor').textContent = data.schedule.professor;
                    document.getElementById('modalSubject').textContent = data.schedule.subject_course;
                    
                    // Show modal
                    modal.classList.remove('hidden');
                    
                    // Clear RFID input
                    this.value = '';
                } else {
                    // Create and show error message for no schedule
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700';
                    errorDiv.textContent = data.message || 'No schedule found for this RFID.';
                    
                    // Insert the message at the top of the content area
                    const contentArea = document.querySelector('.p-6');
                    contentArea.insertBefore(errorDiv, contentArea.firstChild);
                    
                    // Remove the message after 3 seconds
                    setTimeout(() => {
                        errorDiv.remove();
                    }, 3000);
                    
                    // Clear RFID input
                    this.value = '';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while processing your request.');
                this.value = '';
            }
        }
    });

    // Cancel button functionality
    document.getElementById('cancelBtn').addEventListener('click', function() {
        modal.classList.add('hidden');
        rfidInput.value = '';
        rfidInput.focus();
    });

    // Confirm button functionality
    document.getElementById('confirmBtn').addEventListener('click', async function() {
        try {
            const response = await fetch('/lab-logging/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    laboratory: scheduleData.laboratory,
                    date: scheduleData.date,
                    time_in: scheduleData.time_in,
                    time_out: scheduleData.time_out,
                    professor_name: scheduleData.professor,
                    subject_course: scheduleData.subject_course,
                    schedule_id: scheduleData.id
                })
            });

            const result = await response.json();
            
            if (result.success) {
                // Create and show success message
                const successDiv = document.createElement('div');
                successDiv.className = 'mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700';
                successDiv.textContent = 'Schedule logged successfully!';
                
                // Insert the message at the top of the content area
                const contentArea = document.querySelector('.p-6');
                contentArea.insertBefore(successDiv, contentArea.firstChild);
                
                // Hide modal
                modal.classList.add('hidden');
                rfidInput.focus();
                
                // Remove the message after 3 seconds
                setTimeout(() => {
                    successDiv.remove();
                }, 3000);
            } else {
                // Create and show error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700';
                errorDiv.textContent = result.message || 'Failed to log schedule';
                
                // Insert the message at the top of the content area
                const contentArea = document.querySelector('.p-6');
                contentArea.insertBefore(errorDiv, contentArea.firstChild);
                
                // Remove the message after 3 seconds
                setTimeout(() => {
                    errorDiv.remove();
                }, 3000);
            }
        } catch (error) {
            console.error('Error:', error);
            // Create and show error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700';
            errorDiv.textContent = 'An error occurred while saving the log.';
            
            // Insert the message before the main container
            const mainContainer = document.querySelector('.flex-1');
            mainContainer.parentNode.insertBefore(errorDiv, mainContainer);
            
            // Remove the message after 3 seconds
            setTimeout(() => {
                errorDiv.remove();
            }, 3000);
        }
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.classList.add('hidden');
            rfidInput.value = '';
            rfidInput.focus();
        }
    });
});
</script>
@endsection
