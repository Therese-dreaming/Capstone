@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8">
    <h2 class="text-2xl font-semibold mb-6">REPAIR REQUEST</h2>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        <div class="font-semibold">Success!</div>
        <div>{{ session('success') }}</div>
        <div class="mt-2 text-sm">Please keep this ticket number for future reference.</div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <div class="font-semibold">Error!</div>
        <div>{{ session('error') }}</div>
    </div>
    @endif

    <div id="assetMessage" class="mb-4 p-4 rounded hidden">
        <div class="font-semibold message-title"></div>
        <div class="message-content"></div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
        <form action="{{ route('repair.request.store') }}" method="POST" class="space-y-6" id="repairForm">
            @csrf
            <input type="hidden" name="_method" value="POST">
            <input type="hidden" name="created_by" value="{{ auth()->id() }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Class/Event Ongoing -->
                <div class="col-span-1 md:col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <label class="text-lg font-medium text-gray-700">Class/Event Ongoing?</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="ongoing_activity" value="yes" class="sr-only peer" onchange="this.value = this.checked ? 'yes' : 'no'">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-red-600"></div>
                            <span class="ml-3 text-sm font-medium text-gray-700 peer-checked:text-red-600">
                                <span class="ongoing-status">Yes</span>
                            </span>
                        </label>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Toggle this if there is an ongoing class or event that requires immediate attention.</p>
                </div>

                <!-- Date Called -->
                <div class="flex flex-col">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Called</label>
                    <div class="flex gap-2">
                        <input type="date" id="date_called" name="date_called" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" value="{{ date('Y-m-d') }}" required>
                        <button type="button" onclick="setCurrentDate()" class="w-auto md:w-auto px-3 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                            Set Current
                        </button>
                    </div>
                </div>

                <!-- Time Called -->
                <div class="flex flex-col">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Time Called</label>
                    <div class="flex gap-2">
                        <input type="time" id="time_called" name="time_called" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" value="{{ date('H:i') }}" required>
                        <button type="button" onclick="setCurrentTime()" class="w-auto md:w-auto px-3 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                            Set Current
                        </button>
                    </div>
                </div>

                <!-- Location (full width) -->
                <div class="flex flex-col md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" name="location" id="location" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="Enter the location (e.g., Room 101, Library, Admin Office)" required>
                </div>

                <!-- Equipment -->
                <div class="flex flex-col">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Equipment</label>
                    <input type="text" id="equipment" name="equipment" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="Enter equipment name" required>
                </div>

                <!-- Category -->
                <div class="flex flex-col">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select id="category_select" name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 dropdown-menu" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Technician (Admin only) -->
                @if(auth()->user()->group_id == 1)
                <div class="flex flex-col">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assign Technician (Optional)</label>
                    <select name="technician_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
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
                <div class="flex flex-col md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Issue</label>
                    <textarea name="issue" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="Describe the issue..." required></textarea>
                </div>

                <!-- Photo Upload (full width) -->
                <div class="flex flex-col md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Photo of the Issue (Optional)</label>
                    <div class="space-y-4">
                        <!-- Preview Container -->
                        <div id="photoPreview" class="hidden w-full max-w-md mx-auto">
                            <img id="previewImage" src="" alt="Preview" class="w-full h-64 object-contain rounded-lg border border-gray-300">
                            <button type="button" onclick="removePhoto()" class="mt-2 text-sm text-red-600 hover:text-red-800">
                                Remove Photo
                            </button>
                        </div>

                        <!-- Upload Options -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <!-- Camera Input -->
                            <div class="flex-1">
                                <button type="button" onclick="openCamera()" class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Take Photo
                                </button>
                                <input type="file" id="cameraInput" accept="image/*" capture="environment" class="hidden" onchange="handlePhotoUpload(event)">
                            </div>

                            <!-- File Upload -->
                            <div class="flex-1">
                                <button type="button" onclick="document.getElementById('fileInput').click()" class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Upload Photo
                                </button>
                                <input type="file" id="fileInput" accept="image/*" class="hidden" onchange="handlePhotoUpload(event)">
                            </div>
                        </div>
                        <input type="hidden" name="photo" id="photoData">
                        <p class="text-sm text-gray-500">Take a photo or upload an image of the issue. Maximum file size: 5MB</p>
                    </div>
                </div>
            </div> <!-- End of grid -->

            <!-- Replace the existing Submit Buttons div -->
            <div class="mt-6 flex gap-4">
                <button type="button" onclick="handleSubmission()" class="flex-1 bg-red-800 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Submit Request
                </button>
            </div>
        </form>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-4 md:p-8 rounded-lg shadow-xl max-w-xs sm:max-w-md w-full mx-4">
            <h3 class="text-xl font-semibold mb-4" id="modalTitle">Confirm Submission</h3>
            <p class="text-gray-600 mb-6" id="modalMessage"></p>
            <div class="flex justify-end space-x-4">
                <button onclick="closeConfirmModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Cancel
                </button>
                <button onclick="submitForm()" class="px-4 py-2 bg-red-800 text-white rounded hover:bg-red-700">
                    Confirm
                </button>
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

        document.addEventListener('DOMContentLoaded', hideMessages);

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

        function isUrgentRequest() {
            const location = document.getElementById('location').value;
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
