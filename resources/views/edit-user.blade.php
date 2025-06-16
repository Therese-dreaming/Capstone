@extends('layouts.app')

@section('content')
<div class="flex-1 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Edit User</h2>
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

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <!-- Profile Picture Display -->
                <div class="col-span-2 flex items-center space-x-4 mb-4">
                    <img src="{{ $user->profile_picture ? asset($user->profile_picture) : asset('images/default-profile.png') }}" 
                         alt="{{ $user->name }}'s profile picture" 
                         class="w-20 h-20 rounded-full object-cover">
                    <div>
                        <p class="text-sm text-gray-600">Current Profile Picture</p>
                        <p class="text-xs text-gray-500">To change profile picture, go to your profile settings</p>
                    </div>
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4 text-base">
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4 text-base">
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select name="department" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4 text-base">
                        <option value="">Select Department</option>
                        <option value="Early Childhood Education (ECE)" {{ old('department', $user->department) == 'Early Childhood Education (ECE)' ? 'selected' : '' }}>Early Childhood Education (ECE)</option>
                        <option value="Grade School" {{ old('department', $user->department) == 'Grade School' ? 'selected' : '' }}>Grade School</option>
                        <option value="Junior High School" {{ old('department', $user->department) == 'Junior High School' ? 'selected' : '' }}>Junior High School</option>
                        <option value="Senior High School" {{ old('department', $user->department) == 'Senior High School' ? 'selected' : '' }}>Senior High School</option>
                        <option value="College" {{ old('department', $user->department) == 'College' ? 'selected' : '' }}>College</option>
                        <option value="School of Graduate Studies" {{ old('department', $user->department) == 'School of Graduate Studies' ? 'selected' : '' }}>School of Graduate Studies</option>
                    </select>
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                    <input type="text" name="position" id="position" value="{{ old('position', $user->position) }}"
                        onchange="toggleRFIDField()"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4 text-base">
                </div>

                <div class="col-span-2 sm:col-span-1" id="rfid_field">
                    <label class="block text-sm font-medium text-gray-700 mb-1">RFID Number</label>
                    <input type="text" name="rfid_number" value="{{ old('rfid_number', $user->rfid_number) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4 text-base">
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

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current password"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4 text-base">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" placeholder="Leave blank to keep current password"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4 text-base">
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">User Role</label>
                    <select name="group_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4 text-base">
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ old('group_id', $user->group_id) == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end mt-6 space-x-3">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection