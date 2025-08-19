@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Page Header with Background Design -->
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                        <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Edit User</h1>
                        <p class="text-red-100 text-sm md:text-lg">Update user information and permissions</p>
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

    <!-- User Edit Form -->
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

            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <!-- Name Field -->
                    <div class="col-span-1 md:col-span-1">
                        <label class="block text-sm md:text-base font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Full Name
                        </label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" 
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
                        <input type="text" name="username" value="{{ old('username', $user->username) }}"
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
                        <select name="department" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4 text-base transition-colors duration-200">
                            <option value="">Select Department</option>
                            <option value="Early Childhood Education (ECE)" {{ old('department', $user->department) == 'Early Childhood Education (ECE)' ? 'selected' : '' }}>Early Childhood Education (ECE)</option>
                            <option value="Grade School" {{ old('department', $user->department) == 'Grade School' ? 'selected' : '' }}>Grade School</option>
                            <option value="Junior High School" {{ old('department', $user->department) == 'Junior High School' ? 'selected' : '' }}>Junior High School</option>
                            <option value="Senior High School" {{ old('department', $user->department) == 'Senior High School' ? 'selected' : '' }}>Senior High School</option>
                            <option value="College" {{ old('department', $user->department) == 'College' ? 'selected' : '' }}>College</option>
                            <option value="School of Graduate Studies" {{ old('department', $user->department) == 'School of Graduate Studies' ? 'selected' : '' }}>School of Graduate Studies</option>
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
                        <input type="text" name="position" id="position" value="{{ old('position', $user->position) }}"
                            onchange="toggleRFIDField()"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4 text-base transition-colors duration-200"
                            placeholder="Enter position">
                    </div>

                    <!-- RFID Number Field -->
                    <div class="col-span-1 md:col-span-1" id="rfid_field">
                        <label class="block text-sm md:text-base font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            RFID Number
                        </label>
                        <input type="text" name="rfid_number" value="{{ old('rfid_number', $user->rfid_number) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4 text-base transition-colors duration-200"
                            placeholder="Enter RFID number">
                    </div>

                    <!-- User Role Field -->
                    <div class="col-span-1 md:col-span-1">
                        <label class="block text-sm md:text-base font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            User Role
                        </label>
                        <select name="group_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4 text-base transition-colors duration-200">
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ old('group_id', $user->group_id) == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Password Section -->
                <div class="mt-6 md:mt-8 p-4 md:p-6 bg-blue-50 rounded-xl border border-blue-200">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 p-2 md:p-3 rounded-full mr-3 md:mr-4">
                            <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-lg md:text-xl font-semibold text-gray-900">Password Update</h3>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Leave password fields blank to keep the current password unchanged.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <!-- New Password Field -->
                        <div class="col-span-1 md:col-span-1">
                            <label class="block text-sm md:text-base font-medium text-gray-700 mb-2 flex items-center">
                                <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                New Password
                            </label>
                            <input type="password" name="password" placeholder="Leave blank to keep current password"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4 text-base transition-colors duration-200">
                        </div>

                        <!-- Confirm New Password Field -->
                        <div class="col-span-1 md:col-span-1">
                            <label class="block text-sm md:text-base font-medium text-gray-700 mb-2 flex items-center">
                                <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Confirm New Password
                            </label>
                            <input type="password" name="password_confirmation" placeholder="Leave blank to keep current password"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4 text-base transition-colors duration-200">
                        </div>
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
                    <button type="submit" 
                            class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update User
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
</script>
@endsection