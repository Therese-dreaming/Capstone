@extends('layouts.app')

@section('content')
<div class="flex-1 p-8 ml-72">
    <h2 class="text-2xl font-semibold mb-6">REPAIR REQUEST</h2>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('repair.store') }}" method="POST" class="space-y-6" id="repairForm">
            @csrf
            <input type="hidden" name="_method" value="POST">

            <div class="grid grid-cols-2 gap-6">
                <!-- Date Called -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Called</label>
                    <div class="flex gap-2">
                        <input type="date" id="date_called" name="date_called" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" value="{{ date('Y-m-d') }}" required>
                        <button type="button" onclick="setCurrentDate()" class="px-3 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                            Set Current
                        </button>
                    </div>
                </div>

                <!-- Time Called -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Time Called</label>
                    <div class="flex gap-2">
                        <input type="time" id="time_called" name="time_called" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" value="{{ date('H:i') }}" required>
                        <button type="button" onclick="setCurrentTime()" class="px-3 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                            Set Current
                        </button>
                    </div>
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <select name="department" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" required>
                        <option value="">Select Department</option>
                        <option value="ICTC">ICTC</option>
                        <option value="Registrar">Registrar</option>
                        <option value="Accounting">Accounting</option>
                        <!-- Add more departments as needed -->
                    </select>
                </div>

                <!-- Office/Room -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Office/Room</label>
                    <input type="text" name="office_room" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="Enter office or room number" required>
                </div>

                <!-- Equipment -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Equipment</label>
                    <select id="equipment_select" 
                            name="equipment" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500"
                            onchange="handleEquipmentSelect()" 
                            required>
                        <option value="">Select Equipment</option>
                        <optgroup label="Hardware" class="font-medium text-gray-700 bg-gray-50">
                            <option value="Projector" class="py-2">Projector</option>
                            <option value="Hardware Parts" class="py-2">Hardware Parts</option>
                            <option value="Laptop" class="py-2">Laptop</option>
                            <option value="Printer" class="py-2">Printer</option>
                            <option value="Mouse" class="py-2">Mouse</option>
                            <option value="Keyboard" class="py-2">Keyboard</option>
                            <option value="Monitor" class="py-2">Monitor</option>
                            <option value="Scanner" class="py-2">Scanner</option>
                            <option value="UPS" class="py-2">UPS</option>
                            <option value="CPU" class="py-2">CPU</option>
                        </optgroup>
                        <optgroup label="Network" class="font-medium text-gray-700 bg-gray-50">
                            <option value="Router" class="py-2">Router</option>
                            <option value="Switch" class="py-2">Switch</option>
                            <option value="Network Cable" class="py-2">Network Cable</option>
                            <option value="WiFi Access Point" class="py-2">WiFi Access Point</option>
                        </optgroup>
                        <optgroup label="Peripherals" class="font-medium text-gray-700 bg-gray-50">
                            <option value="Webcam" class="py-2">Webcam</option>
                            <option value="Headset" class="py-2">Headset</option>
                            <option value="Speaker" class="py-2">Speaker</option>
                            <option value="Microphone" class="py-2">Microphone</option>
                        </optgroup>
                        <optgroup label="Software" class="font-medium text-gray-700 bg-gray-50">
                            <option value="Orange Apps Account" class="py-2">Orange Apps Account</option>
                            <option value="MS Teams Account" class="py-2">MS Teams Account</option>
                            <option value="Chrome Browser" class="py-2">Chrome Browser</option>
                            <option value="Windows OS" class="py-2">Windows OS</option>
                        </optgroup>
                        <option value="custom" class="font-medium text-gray-700">Other (Specify Below)</option>
                    </select>
                    <input type="text" 
                           id="equipment_input" 
                           name="equipment" 
                           class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" 
                           placeholder="Specify equipment if not in list above" 
                           style="display: none;">
                </div>

                <!-- Category -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select id="category_select" 
                            name="category_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 dropdown-menu"
                            required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Issue -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Issue</label>
                    <textarea name="issue" 
                             rows="4" 
                             class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500"
                             placeholder="Describe the issue..."
                             required></textarea>
                </div>
            </div> <!-- End of grid -->

            <!-- Submit Buttons -->
            <div class="mt-6 flex gap-4">
                <button type="button" 
                        onclick="openConfirmModal('pending')"
                        class="flex-1 bg-red-800 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Submit Request
                </button>
                <button type="button" 
                        onclick="openConfirmModal('urgent')"
                        class="flex-1 bg-yellow-600 text-white py-2 px-4 rounded-md hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">
                    Submit as Urgent
                </button>
            </div>
        </form>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-xl max-w-md w-full mx-4">
            <h3 class="text-xl font-semibold mb-4" id="modalTitle">Confirm Submission</h3>
            <p class="text-gray-600 mb-6" id="modalMessage"></p>
            <div class="flex justify-end space-x-4">
                <button onclick="closeConfirmModal()" 
                        class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Cancel
                </button>
                <button onclick="submitForm()" 
                        class="px-4 py-2 bg-red-800 text-white rounded hover:bg-red-700">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <script>
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

        function handleEquipmentSelect() {
            const select = document.getElementById('equipment_select');
            const input = document.getElementById('equipment_input');
            const categorySelect = document.getElementById('category_select');

            // Auto-select category based on equipment group
            const selectedOption = select.options[select.selectedIndex];
            const optgroup = selectedOption.parentElement;
            if (optgroup && optgroup.label) {
                const categoryOptions = categorySelect.options;
                for (let i = 0; i < categoryOptions.length; i++) {
                    if (categoryOptions[i].text === optgroup.label) {
                        categorySelect.value = categoryOptions[i].value;
                        break;
                    }
                }
            }

            if (select.value === 'custom') {
                select.removeAttribute('name');
                input.style.display = 'block';
                input.required = true;
                input.value = '';
                input.focus();
            } else {
                select.setAttribute('name', 'equipment');
                input.style.display = 'none';
                input.required = false;
                input.value = select.value;
            }
        }

        function getEquipmentValue() {
            const select = document.getElementById('equipment_select');
            const input = document.getElementById('equipment_input');
            return select.value === 'custom' ? input.value : select.value;
        }

        function openConfirmModal(status) {
            currentStatus = status;
            const modal = document.getElementById('confirmModal');
            const message = document.getElementById('modalMessage');
            const title = document.getElementById('modalTitle');

            title.textContent = status === 'urgent' ? 'Confirm Urgent Request' : 'Confirm Submission';
            message.textContent = status === 'urgent' 
                ? 'Are you sure you want to submit this as an urgent repair request?' 
                : 'Are you sure you want to submit this repair request?';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeConfirmModal() {
            const modal = document.getElementById('confirmModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function submitForm() {
            const form = document.getElementById('repairForm');
            
            // Remove any existing status input to prevent duplicates
            const existingStatus = form.querySelector('input[name="status"]');
            if (existingStatus) {
                existingStatus.remove();
            }
            
            // Create and append the new status input
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = currentStatus;
            form.appendChild(statusInput);
            
            // Submit the form
            form.submit();
        }

        // Initialize equipment input display
        document.getElementById('equipment_input').style.display = 'none';
    </script>
</div>
</div>
@endsection
