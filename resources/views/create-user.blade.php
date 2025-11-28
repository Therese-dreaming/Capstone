@extends('layouts.app')

@section('content')
<!-- Confirmation Modal - Move outside the form -->
<div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
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
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-lg shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" onclick="submitForm()" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">
                        Create User
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Page Header with Background Design -->
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                        <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Add New User</h1>
                        <p class="text-red-100 text-sm md:text-lg">Create a new user account with appropriate permissions</p>
                    </div>
                </div>
                <a href="{{ route('users.index') }}" class="text-white/80 hover:text-white transition-colors duration-200">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
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

    @if($errors->any())
        <div class="mb-4 md:mb-6 p-3 md:p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm md:text-base font-medium">Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside text-sm md:text-base ml-6">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- User Creation Form -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
            <div class="flex items-center mb-4 md:mb-6">
                <div class="bg-red-100 p-2 md:p-3 rounded-full mr-3 md:mr-4">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h2 class="text-lg md:text-xl font-semibold text-gray-900">User Information</h2>
            </div>

            <form action="{{ route('users.store') }}" method="POST" id="createUserForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <!-- Name Field -->
                    <div class="col-span-1 md:col-span-1">
                        <label class="block text-sm md:text-base font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Full Name
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4 text-base transition-colors duration-200"
                            placeholder="Enter full name">
                    </div>

                    <!-- Username Field -->
                    <div class="col-span-1 md:col-span-1">
                        <label class="block text-sm md:text-base font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Username
                        </label>
                        <input type="text" name="username" value="{{ old('username') }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4 text-base transition-colors duration-200"
                            placeholder="Enter username">
                    </div>

                    <!-- Department Field -->
                    <div class="col-span-1 md:col-span-1">
                        <label class="block text-sm md:text-base font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Department
                        </label>
                        <select name="department" required 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4 text-base transition-colors duration-200">
                            <option value="">Select Department</option>
                            <option value="Early Childhood Education (ECE)" {{ old('department') == 'Early Childhood Education (ECE)' ? 'selected' : '' }}>Early Childhood Education (ECE)</option>
                            <option value="Grade School" {{ old('department') == 'Grade School' ? 'selected' : '' }}>Grade School</option>
                            <option value="Junior High School" {{ old('department') == 'Junior High School' ? 'selected' : '' }}>Junior High School</option>
                            <option value="Senior High School" {{ old('department') == 'Senior High School' ? 'selected' : '' }}>Senior High School</option>
                            <option value="College" {{ old('department') == 'College' ? 'selected' : '' }}>College</option>
                            <option value="ICTC" {{ old('department') == 'ICTC' ? 'selected' : '' }}>ICTC</option>
                            <option value="School of Graduate Studies" {{ old('department') == 'School of Graduate Studies' ? 'selected' : '' }}>School of Graduate Studies</option>
                            <option value="ICTC" {{ old('department') == 'ICTC' ? 'selected' : '' }}>ICTC</option>
                        </select>
                    </div>

                    <!-- Position Field -->
                    <div class="col-span-1 md:col-span-1">
                        <label class="block text-sm md:text-base font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6" />
                            </svg>
                            Position
                        </label>
                        <input type="text" name="position" id="position" value="{{ old('position') }}" required
                            onchange="toggleRFIDField()"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4 text-base transition-colors duration-200"
                            placeholder="Enter position">
                    </div>

                    <!-- Gender Field -->
                    <div class="col-span-1 md:col-span-1">
                        <label class="block text-sm md:text-base font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Gender
                        </label>
                        <select name="gender" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4 text-base transition-colors duration-200">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <!-- RFID Number Field -->
                    <div class="col-span-1 md:col-span-1" id="rfid_field" style="display: none;">
                        <label class="block text-sm md:text-base font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            RFID Number
                        </label>
                        <input type="text" name="rfid_number" value="{{ old('rfid_number') }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4 text-base transition-colors duration-200"
                            placeholder="Enter RFID number">
                    </div>

                    <!-- Password Field -->
                    <div class="col-span-1 md:col-span-1">
                        <label class="block text-sm md:text-base font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Password
                        </label>
                        <input type="password" name="password" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4 text-base transition-colors duration-200"
                            placeholder="Enter password">
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="col-span-1 md:col-span-1">
                        <label class="block text-sm md:text-base font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Confirm Password
                        </label>
                        <input type="password" name="password_confirmation" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4 text-base transition-colors duration-200"
                            placeholder="Confirm password">
                    </div>

                    <!-- User Role Field -->
                    <div class="col-span-1 md:col-span-1">
                        <label class="block text-sm md:text-base font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            User Role
                        </label>
                        <select name="group_id" required 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4 text-base transition-colors duration-200">
                            <option value="">Select Role</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end mt-6 md:mt-8 space-y-3 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('users.index') }}" 
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 font-medium text-center flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Cancel
                    </a>
                    <button type="button" onclick="showConfirmModal()" 
                            class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleRFIDField() {
        const position = document.getElementById('position').value.toLowerCase();
        const rfidField = document.getElementById('rfid_field');
        rfidField.style.display = (position === 'teacher' || position === 'faculty') ? 'block' : 'none';
    }
    
    // Run on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleRFIDField();
    });

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
        const genderSelect = document.getElementsByName('gender')[0];
        const gender = genderSelect.options[genderSelect.selectedIndex].text;
        const roleSelect = document.getElementsByName('group_id')[0];
        const role = roleSelect.options[roleSelect.selectedIndex].text;

        const details = `
            <ul class="list-disc list-inside space-y-1">
                <li><span class="font-medium">Name:</span> ${name}</li>
                <li><span class="font-medium">Username:</span> ${username}</li>
                <li><span class="font-medium">Department:</span> ${department}</li>
                <li><span class="font-medium">Position:</span> ${position}</li>
                <li><span class="font-medium">Gender:</span> ${gender}</li>
                <li><span class="font-medium">Role:</span> ${role}</li>
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
