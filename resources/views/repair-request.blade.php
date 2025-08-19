@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8">
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center" role="alert">
        <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <div class="font-semibold">Success!</div>
            <div>{{ session('success') }}</div>
            <div class="mt-1 text-sm text-green-600">Please keep this ticket number for future reference.</div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-center" role="alert">
        <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
            <div class="font-semibold">Error!</div>
            <div>{{ session('error') }}</div>
        </div>
    </div>
    @endif

    <div id="assetMessage" class="mb-6 p-4 rounded-xl hidden">
        <div class="font-semibold message-title"></div>
        <div class="message-content"></div>
    </div>

    <!-- Page Header with Background Design -->
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                        <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Repair Request</h1>
                        <p class="text-red-100 text-sm md:text-lg">Submit a new repair request for equipment or facilities</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-4 md:p-6">
        <form action="{{ route('repair.request.store') }}" method="POST" class="space-y-6" id="repairForm">
            @csrf
            <input type="hidden" name="_method" value="POST">
            <input type="hidden" name="created_by" value="{{ auth()->id() }}">
            <input type="hidden" name="urgency_level" id="urgency_level" value="3">
            <input type="hidden" name="status" value="pending">

            <!-- Class/Event Ongoing Section -->
            <div class="bg-gradient-to-r from-red-50 to-orange-50 p-6 rounded-xl border border-red-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="bg-red-100 p-3 rounded-full mr-4">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <label class="text-lg font-semibold text-gray-800">Class/Event Ongoing?</label>
                            <p class="text-sm text-gray-600">Toggle this if there is an ongoing class or event that requires immediate attention.</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="ongoing_activity" value="yes" class="sr-only peer" onchange="updateOngoingStatus(this)">
                        <div class="w-16 h-8 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-7 after:w-7 after:transition-all peer-checked:bg-red-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700 peer-checked:text-red-600">
                            <span class="ongoing-status">No</span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date Called -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Date Called</label>
                    <div class="flex gap-3">
                        <input type="date" id="date_called" name="date_called" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200" value="{{ date('Y-m-d') }}" required>
                        <button type="button" onclick="setCurrentDate()" class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200 font-medium">
                            Set Current
                        </button>
                    </div>
                </div>

                <!-- Time Called -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Time Called</label>
                    <div class="flex gap-3">
                        <input type="time" id="time_called" name="time_called" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200" value="{{ date('H:i') }}" required>
                        <button type="button" onclick="setCurrentTime()" class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200 font-medium">
                            Set Current
                        </button>
                    </div>
                </div>

                <!-- Building -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Building</label>
                    <input type="text" name="building" id="building" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200" placeholder="Enter building name" required>
                </div>

                <!-- Floor -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Floor</label>
                    <input type="text" name="floor" id="floor" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200" placeholder="Enter floor (e.g., 1st Floor, 2nd Floor)" required>
                </div>

                <!-- Room -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Room</label>
                    <input type="text" name="room" id="room" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200" placeholder="Enter room number or name" required>
                </div>

                <!-- Equipment -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Equipment</label>
                    <input type="text" id="equipment" name="equipment" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200" placeholder="Enter equipment name" required>
                </div>

                <!-- Serial Number (Optional) -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Serial Number (Optional)</label>
                    <input type="text" id="serial_number" name="serial_number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200" placeholder="Enter serial number if known">
                </div>

                <!-- Category -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Category</label>
                    <select id="category_select" name="category_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 bg-white" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Technician (Admin only) -->
                @if(auth()->user()->group_id == 1)
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Assign Technician (Optional)</label>
                    <select name="technician_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 bg-white">
                        <option value="">Select Technician</option>
                        @foreach($technicians as $technician)
                        <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                        @endforeach
                    </select>
                </div>
                @elseif(auth()->user()->group_id == 2)
                    <input type="hidden" name="technician_id" value="{{ auth()->id() }}">
                @endif

                <!-- Issue (full width) -->
                <div class="md:col-span-2 space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Issue Description</label>
                    <textarea name="issue" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200" placeholder="Describe the issue in detail..." required></textarea>
                </div>

                <!-- Photo Upload (full width) -->
                <div class="md:col-span-2 space-y-4">
                    <label class="block text-sm font-semibold text-gray-700">Photo of the Issue (Optional)</label>
                    
                    <!-- Preview Container -->
                    <div id="photoPreview" class="hidden w-full max-w-md mx-auto">
                        <div class="relative">
                            <img id="previewImage" src="" alt="Preview" class="w-full h-64 object-contain rounded-lg border-2 border-gray-200 shadow-sm">
                            <button type="button" onclick="removePhoto()" class="absolute top-2 right-2 bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Upload Options -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Camera Input -->
                        <div class="space-y-2">
                            <button type="button" onclick="openCamera()" class="w-full px-6 py-4 bg-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center font-medium shadow-lg">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Take Photo
                            </button>
                            <input type="file" id="cameraInput" accept="image/*" capture="environment" class="hidden" onchange="handlePhotoUpload(event)">
                        </div>

                        <!-- File Upload -->
                        <div class="space-y-2">
                            <button type="button" onclick="document.getElementById('fileInput').click()" class="w-full px-6 py-4 bg-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center font-medium shadow-lg">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Upload Photo
                            </button>
                            <input type="file" id="fileInput" accept="image/*" class="hidden" onchange="handlePhotoUpload(event)">
                        </div>
                    </div>
                    
                    <input type="hidden" name="photo" id="photoData">
                    <p class="text-sm text-gray-600 text-center">Take a photo or upload an image of the issue. Maximum file size: 5MB</p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-6 border-t border-gray-200">
                <button type="submit" class="w-full bg-red-800 text-white py-4 px-6 rounded-xl hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 font-semibold text-lg shadow-lg">
                    Submit Repair Request
                </button>
            </div>
        </form>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" style="z-index: 60;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4" id="modalTitle">Confirm Submission</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="modalMessage"></p>
                </div>
                <div class="flex justify-center gap-4 mt-4">
                    <button onclick="submitForm()" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Confirm
                    </button>
                    <button onclick="closeConfirmModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const urgentDepartments = [
            'Accounting Unit'
            , 'Caregiver & College Dean\'s Office'
            , 'Executive Vice-President\'s Office/Marketing'
            , 'Grade School - Principal\'s Office'
            , 'Junior High School - Principal\'s Office'
            , 'President\'s Office'
            , 'Registrar\'s Office'
            , 'Senior High School - Principal\'s Office'
        ];

        function hideMessages() {
            // Hide session messages
            const sessionMessages = document.querySelectorAll('.mb-4.p-4.bg-green-100, .mb-4.p-4.bg-red-100');
            sessionMessages.forEach(msg => {
                setTimeout(() => {
                    msg.style.transition = 'opacity 0.5s';
                    msg.style.opacity = '0';
                    setTimeout(() => msg.style.display = 'none', 500);
                }, 3000);
            });

            // Hide asset message if visible
            const assetMessage = document.getElementById('assetMessage');
            if (!assetMessage.classList.contains('hidden')) {
                setTimeout(() => {
                    assetMessage.style.transition = 'opacity 0.5s';
                    assetMessage.style.opacity = '0';
                    setTimeout(() => {
                        assetMessage.classList.add('hidden');
                        assetMessage.style.opacity = '1';
                    }, 500);
                }, 3000);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            hideMessages();
            
            // Add event listeners for urgency level calculation
            document.getElementById('date_called').addEventListener('change', calculateUrgencyLevel);
            document.querySelector('input[name="ongoing_activity"]').addEventListener('change', calculateUrgencyLevel);
            
            // Calculate initial urgency level
            calculateUrgencyLevel();
            
            // Add form submission event listener
            document.getElementById('repairForm').addEventListener('submit', function(e) {
                // Calculate urgency level before submission
                calculateUrgencyLevel();
                
                // Set status based on urgency
                const isUrgent = isUrgentRequest();
                document.querySelector('input[name="status"]').value = isUrgent ? 'urgent' : 'pending';
                
                console.log('Form submitting with urgency level:', document.getElementById('urgency_level').value);
                console.log('Form submitting with status:', document.querySelector('input[name="status"]').value);
            });
        });

        let currentStatus = '';

        function setCurrentDate() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            document.getElementById('date_called').value = `${year}-${month}-${day}`;
        }

        function setCurrentTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('time_called').value = `${hours}:${minutes}`;
        }

        function openConfirmModal(status) {
            currentStatus = status;
            const modal = document.getElementById('confirmModal');
            const message = document.getElementById('modalMessage');
            const title = document.getElementById('modalTitle');

            title.textContent = status === 'urgent' ? 'Confirm Urgent Request' : 'Confirm Submission';
            message.textContent = status === 'urgent' ?
                'Are you sure you want to submit this as an urgent repair request?' :
                'Are you sure you want to submit this repair request?';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeConfirmModal() {
            const modal = document.getElementById('confirmModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function updateOngoingStatus(checkbox) {
            const statusSpan = checkbox.parentElement.querySelector('.ongoing-status');
            statusSpan.textContent = checkbox.checked ? 'Yes' : 'No';
            calculateUrgencyLevel();
        }

        function calculateUrgencyLevel() {
            const ongoingActivity = document.querySelector('input[name="ongoing_activity"]').checked;
            const dateCalled = document.getElementById('date_called').value;
            const urgencyLevelField = document.getElementById('urgency_level');
            
            let urgencyLevel = 3; // Default to level 3 (new requests)
            
            if (ongoingActivity) {
                urgencyLevel = 1; // Level 1 for ongoing class/event
            } else if (dateCalled) {
                const requestDate = new Date(dateCalled);
                const oneWeekAgo = new Date();
                oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
                
                if (requestDate < oneWeekAgo) {
                    urgencyLevel = 2; // Level 2 for requests over a week old
                }
            }
            
            urgencyLevelField.value = urgencyLevel;
        }

        function isUrgentRequest() {
            const building = document.getElementById('building').value;
            const floor = document.getElementById('floor').value;
            const room = document.getElementById('room').value;
            const location = `${building} ${floor} ${room}`.trim();
            const hasOngoingActivity = document.querySelector('input[name="ongoing_activity"]').checked;

            // Check if location contains any of the urgent departments
            return urgentDepartments.some(dept => location.includes(dept)) || hasOngoingActivity;
        }

        function handleSubmission() {
            const status = isUrgentRequest() ? 'urgent' : 'pending';
            openConfirmModal(status);
        }

        function submitForm() {
            const form = document.getElementById('repairForm');

            // Create and append the status input if it doesn't exist
            let statusInput = form.querySelector('input[name="status"]');
            if (!statusInput) {
                statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                form.appendChild(statusInput);
            }

            // Set the current status
            statusInput.value = currentStatus;

            // Close the modal
            closeConfirmModal();

            // Debug log
            console.log('Submitting form with status:', currentStatus);

            // Submit the form
            form.submit();
        }

        // Photo handling functions
        function openCamera() {
            document.getElementById('cameraInput').click();
        }

        function handlePhotoUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Check file size (5MB limit)
            if (file.size > 5 * 1024 * 1024) {
                alert('File size exceeds 5MB limit');
                return;
            }

            // Check file type
            if (!file.type.startsWith('image/')) {
                alert('Please upload an image file');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('photoPreview');
                const previewImage = document.getElementById('previewImage');
                const photoData = document.getElementById('photoData');

                previewImage.src = e.target.result;
                photoData.value = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        function removePhoto() {
            const preview = document.getElementById('photoPreview');
            const previewImage = document.getElementById('previewImage');
            const photoData = document.getElementById('photoData');
            const cameraInput = document.getElementById('cameraInput');
            const fileInput = document.getElementById('fileInput');

            previewImage.src = '';
            photoData.value = '';
            preview.classList.add('hidden');
            cameraInput.value = '';
            fileInput.value = '';
        }
    </script>
</div>
@endsection
