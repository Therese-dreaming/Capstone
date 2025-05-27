@extends('layouts.app')

@section('content')
<!-- Confirmation Modal - Move outside the form -->
<div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Confirm User Creation</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Please confirm the user details:</p>
                <div id="userDetails" class="mt-2 text-left text-sm text-gray-600"></div>
            </div>
            <div class="items-center px-4 py-3">
                <div class="flex justify-center space-x-4">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                    <button type="button" onclick="submitForm()" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Create User
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="flex-1 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Add New User</h2>
            <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>

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

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST" id="createUserForm">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4 text-base">
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4 text-base">
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select name="department" required 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4 text-base">
                        <option value="">Select Department</option>
                        <option value="Early Childhood Education (ECE)" {{ old('department') == 'Early Childhood Education (ECE)' ? 'selected' : '' }}>Early Childhood Education (ECE)</option>
                        <option value="Grade School" {{ old('department') == 'Grade School' ? 'selected' : '' }}>Grade School</option>
                        <option value="Junior High School" {{ old('department') == 'Junior High School' ? 'selected' : '' }}>Junior High School</option>
                        <option value="Senior High School" {{ old('department') == 'Senior High School' ? 'selected' : '' }}>Senior High School</option>
                        <option value="College" {{ old('department') == 'College' ? 'selected' : '' }}>College</option>
                        <option value="School of Graduate Studies" {{ old('department') == 'School of Graduate Studies' ? 'selected' : '' }}>School of Graduate Studies</option>
                    </select>
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                    <input type="text" name="position" id="position" value="{{ old('position') }}" required
                        onchange="toggleRFIDField()"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4 text-base">
                </div>

                <div class="col-span-2 sm:col-span-1" id="rfid_field" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-1">RFID Number</label>
                    <input type="text" name="rfid_number" value="{{ old('rfid_number') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4 text-base">
                </div>

                <script>
                    function toggleRFIDField() {
                        const position = document.getElementById('position').value.toLowerCase();
                        const rfidField = document.getElementById('rfid_field');
                        rfidField.style.display = (position === 'teacher' || position === 'faculty') ? 'block' : 'none';
                    }
                    // Run on page load
                    toggleRFIDField();
                </script>
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4 text-base">
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4 text-base">
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">User Role</label>
                    <select name="group_id" required 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4 text-base">
                        <option value="">Select Role</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end mt-6 space-x-3">
                <button type="button" onclick="showConfirmModal()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showConfirmModal() {
        // Validate required fields first
        const form = document.getElementById('createUserForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const name = document.getElementsByName('name')[0].value;
        const username = document.getElementsByName('username')[0].value;
        const departmentSelect = document.getElementsByName('department')[0];
        const department = departmentSelect.options[departmentSelect.selectedIndex].text;
        const position = document.getElementsByName('position')[0].value;
        const roleSelect = document.getElementsByName('group_id')[0];
        const role = roleSelect.options[roleSelect.selectedIndex].text;

        const details = `
            <ul class="list-disc list-inside">
                <li>Name: ${name}</li>
                <li>Username: ${username}</li>
                <li>Department: ${department}</li>
                <li>Position: ${position}</li>
                <li>Role: ${role}</li>
            </ul>
        `;

        document.getElementById('userDetails').innerHTML = details;
        document.getElementById('confirmModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('confirmModal').classList.add('hidden');
    }

    function submitForm() {
        document.getElementById('createUserForm').submit();
    }

    window.onclick = function(event) {
        const modal = document.getElementById('confirmModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endsection
