@extends('layouts.app')

@section('content')
<div class="flex-1 p-8 ml-72">
    <h2 class="text-2xl font-semibold mb-6">REPAIR REQUEST</h2>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        <div class="font-semibold">Success!</div>
        <div>{{ session('success') }}</div>
        <div class="mt-2 text-sm">Please keep this ticket number for future reference.</div>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('repair.store') }}" method="POST" class="space-y-6" id="repairForm">
            @csrf
            <input type="hidden" name="_method" value="POST">

            <div class="grid grid-cols-2 gap-6">

                <!-- After Office/Room input and before Equipment -->
                <div class="col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <label class="text-lg font-medium text-gray-700">Class/Event Ongoing?</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="ongoing_activity" value="yes" class="sr-only peer" onchange="this.value = this.checked ? 'yes' : 'no'">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-red-600"></div>
                            <span class="ml-3 text-sm font-medium text-gray-700 peer-checked:text-red-600">
                                <span class="ongoing-status">No</span>
                            </span>
                        </label>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Toggle this if there is an ongoing class or event that requires immediate attention.</p>
                </div>

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
                    <select id="department_select" name="department" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" onchange="handleDepartmentSelect()" required>
                        <option value="">Select Department</option>
                        <option value="Academic Affairs Office">Academic Affairs Office</option>
                        <option value="Accounting Unit">Accounting Unit</option>
                        <option value="Alumni Office">Alumni Office</option>
                        <option value="Aula Minor Auditorium">Aula Minor Auditorium</option>
                        <option value="Bookstore Unit">Bookstore Unit</option>
                        <option value="Budget & Control Unit">Budget & Control Unit</option>
                        <option value="Campus Ministry Office">Campus Ministry Office</option>
                        <option value="Canteen Unit">Canteen Unit</option>
                        <option value="Caregiver & College Dean's Office">Caregiver & College Dean's Office</option>
                        <option value="Christian Formation Office">Christian Formation Office</option>
                        <option value="Control Booth">Control Booth</option>
                        <option value="CPArts">CPArts</option>
                        <option value="Electrician">Electrician</option>
                        <option value="Executive Vice-President's Office/Marketing">Executive Vice-President's Office/Marketing</option>
                        <option value="Finance and Business Affairs Office">Finance and Business Affairs Office</option>
                        <option value="General Services Unit">General Services Unit</option>
                        <option value="Grade School - Principal's Office">Grade School - Principal's Office</option>
                        <option value="Grade School - STL">Grade School - STL</option>
                        <option value="Grade School - E.C.E">Grade School - E.C.E</option>
                        <option value="Grade School - Faculty">Grade School - Faculty</option>
                        <option value="Grade School - Guidance">Grade School - Guidance</option>
                        <option value="Grade School - Library">Grade School - Library</option>
                        <option value="Grade School - Academics">Grade School - Academics</option>
                        <option value="Grade School - OSA">Grade School - OSA</option>
                        <option value="Human Resource Management Office">Human Resource Management Office</option>
                        <option value="Institutional OSA">Institutional OSA</option>
                        <option value="Junior High School - Principal's Office">Junior High School - Principal's Office</option>
                        <option value="Junior High School - Guidance Office">Junior High School - Guidance Office</option>
                        <option value="Junior High School - Academics">Junior High School - Academics</option>
                        <option value="Junior High School - Faculty">Junior High School - Faculty</option>
                        <option value="Junior High School - Laboratory">Junior High School - Laboratory</option>
                        <option value="Junior High School - Library">Junior High School - Library</option>
                        <option value="Junior High School - OSA">Junior High School - OSA</option>
                        <option value="Junior High School - Reading Center">Junior High School - Reading Center</option>
                        <option value="Medical - Dental Office">Medical - Dental Office</option>
                        <option value="Mini Hotel">Mini Hotel</option>
                        <option value="Pastoral Office Coordinator">Pastoral Office Coordinator</option>
                        <option value="President's Office">President's Office</option>
                        <option value="Physical Plant and General Services">Physical Plant and General Services</option>
                        <option value="Printing Unit">Printing Unit</option>
                        <option value="Purchasing Unit">Purchasing Unit</option>
                        <option value="Registrar's Office">Registrar's Office</option>
                        <option value="Research and Development Office">Research and Development Office</option>
                        <option value="San Pedro Calungsod Hall">San Pedro Calungsod Hall</option>
                        <option value="School of Graduate Studies">School of Graduate Studies</option>
                        <option value="Security Office">Security Office</option>
                        <option value="Senior High School - Principal's Office">Senior High School - Principal's Office</option>
                        <option value="Senior High School - Faculty">Senior High School - Faculty</option>
                        <option value="Senior High School - Guidance">Senior High School - Guidance</option>
                        <option value="Senior High School - OSA">Senior High School - OSA</option>
                        <option value="SGS Library/College Library">SGS Library/College Library</option>
                        <option value="Sisters Quarter">Sisters Quarter</option>
                        <option value="Sport Development Office">Sport Development Office</option>
                        <option value="Stock Issuance Section">Stock Issuance Section</option>
                        <option value="Treasury Unit">Treasury Unit</option>
                        <option value="custom">Other (Specify Below)</option>
                    </select>
                    <input type="text" id="department_input" name="department" class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="Specify department if not in list above" style="display: none;">
                </div>

                <!-- Add this to your existing script section -->
                <script>
                    // Add this new function to your existing script
                    function handleDepartmentSelect() {
                        const select = document.getElementById('department_select');
                        const input = document.getElementById('department_input');

                        if (select.value === 'custom') {
                            select.removeAttribute('name');
                            input.style.display = 'block';
                            input.required = true;
                            input.value = '';
                            input.focus();
                        } else {
                            select.setAttribute('name', 'department');
                            input.style.display = 'none';
                            input.required = false;
                            input.value = select.value;
                        }
                    }

                    // Initialize department input display
                    document.getElementById('department_input').style.display = 'none';

                </script>

                <!-- Office/Room -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Office/Room</label>
                    <input type="text" name="office_room" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="Enter office or room number" required>
                </div>

                <!-- Equipment -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Equipment</label>
                    <select id="equipment_select" name="equipment" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" onchange="handleEquipmentSelect()" required>
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
                    <input type="text" id="equipment_input" name="equipment" class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="Specify equipment if not in list above" style="display: none;">
                </div>

                <!-- Category -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select id="category_select" name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 dropdown-menu" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Issue -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Issue</label>
                    <textarea name="issue" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="Describe the issue..." required></textarea>
                </div>
            </div> <!-- End of grid -->

            <!-- Add this to your script section -->
            <script>
                // Update the isUrgentRequest function
                function isUrgentRequest() {
                    const department = document.getElementById('department_select').value;
                    const hasOngoingActivity = document.querySelector('input[name="ongoing_activity"]').checked;

                    return urgentDepartments.includes(department) || hasOngoingActivity;
                }

                // Add this for toggle text update
                document.querySelector('input[name="ongoing_activity"]').addEventListener('change', function() {
                    const statusText = document.querySelector('.ongoing-status');
                    const departmentSelect = document.getElementById('department_select');
                    const departmentInput = document.getElementById('department_input');

                    statusText.textContent = this.checked ? 'Yes' : 'No';

                    if (this.checked) {
                        // If ongoing activity, disable and clear department
                        departmentSelect.disabled = true;
                        departmentSelect.value = '';
                        departmentSelect.removeAttribute('required');
                        departmentSelect.classList.add('bg-gray-100', 'text-gray-500', 'cursor-not-allowed');
                        if (departmentInput) {
                            departmentInput.style.display = 'none';
                            departmentInput.value = '';
                            departmentInput.removeAttribute('required');
                        }
                    } else {
                        // If no ongoing activity, enable department
                        departmentSelect.disabled = false;
                        departmentSelect.setAttribute('required', 'required');
                        departmentSelect.classList.remove('bg-gray-100', 'text-gray-500', 'cursor-not-allowed');
                    }
                });

            </script>

            <!-- Replace the existing Submit Buttons div -->
            <div class="mt-6 flex gap-4">
                <button type="button" onclick="handleSubmission()" class="flex-1 bg-red-800 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Submit Request
                </button>
            </div>

            <!-- Add to your script section -->
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

                function isUrgentRequest() {
                    const department = document.getElementById('department_select').value;
                    const hasOngoingActivity = document.querySelector('input[name="ongoing_activity"]').checked;

                    return urgentDepartments.includes(department) || hasOngoingActivity;
                }

                function handleSubmission() {
                    const status = isUrgentRequest() ? 'urgent' : 'pending';
                    openConfirmModal(status);
                }

            </script>
        </form>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-xl max-w-md w-full mx-4">
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
