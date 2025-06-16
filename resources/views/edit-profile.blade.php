@extends('layouts.app')

@section('content')
<div class="flex-1 p-8">
    <div class="max-w-5xl mx-auto bg-white rounded-lg shadow-md p-12">
        <h2 class="text-3xl font-semibold mb-8">Edit Profile Information</h2>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="flex flex-col md:flex-row gap-8 md:gap-12">
            <!-- Left Column - Profile Picture --> 
            <div class="w-full md:w-1/3 mb-6 md:mb-0">
                <div class="text-center">
                    <div class="relative w-64 h-64 mx-auto mb-6">
                        <img src="{{ auth()->user()->profile_picture ? asset(auth()->user()->profile_picture) : asset('images/default-profile.png') }}" 
                            alt="Profile Picture" 
                            id="preview-image"
                            class="rounded-full w-full h-full object-cover bg-yellow-300">
                    </div>
                    <input type="file" 
                        id="profile_picture" 
                        name="profile_picture" 
                        accept="image/*" 
                        class="hidden"
                        form="profile-form">
                    <button type="button" 
                        onclick="document.getElementById('profile_picture').click()" 
                        class="bg-white border-2 border-blue-300 text-blue-200 px-6 py-3 rounded-md hover:bg-blue-600 text-base">
                        Change Picture
                    </button>
                </div>
            </div>

            <!-- Right Column - Form -->
            <div class="w-full md:w-2/3">
                <form id="profile-form" action="{{ route('profile.update') }}" method="POST" class="space-y-8" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-base font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}"
                            class="w-full px-4 py-3 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 @error('name') border-red-500 @enderror"
                            placeholder="Enter name">
                    </div>

                    <div>
                        <label for="username" class="block text-base font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" id="username" name="username" value="{{ old('username', auth()->user()->username) }}"
                            class="w-full px-4 py-3 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 @error('username') border-red-500 @enderror"
                            placeholder="Enter username">
                    </div>

                    <div>
                        <label for="department" class="block text-base font-medium text-gray-700 mb-2">Department</label>
                        <select id="department" name="department" 
                            class="w-full px-4 py-3 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 @error('department') border-red-500 @enderror">
                            <option value="">Select Department</option>
                            <option value="Early Childhood Education (ECE)" {{ old('department', auth()->user()->department) == 'Early Childhood Education (ECE)' ? 'selected' : '' }}>Early Childhood Education (ECE)</option>
                            <option value="Grade School" {{ old('department', auth()->user()->department) == 'Grade School' ? 'selected' : '' }}>Grade School</option>
                            <option value="Junior High School" {{ old('department', auth()->user()->department) == 'Junior High School' ? 'selected' : '' }}>Junior High School</option>
                            <option value="Senior High School" {{ old('department', auth()->user()->department) == 'Senior High School' ? 'selected' : '' }}>Senior High School</option>
                            <option value="College" {{ old('department', auth()->user()->department) == 'College' ? 'selected' : '' }}>College</option>
                            <option value="School of Graduate Studies" {{ old('department', auth()->user()->department) == 'School of Graduate Studies' ? 'selected' : '' }}>School of Graduate Studies</option>
                        </select>
                    </div>

                    <div>
                        <label for="position" class="block text-base font-medium text-gray-700 mb-2">Position</label>
                        <input type="text" id="position" name="position" value="{{ old('position', auth()->user()->position) }}"
                            class="w-full px-4 py-3 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 @error('position') border-red-500 @enderror"
                            placeholder="Enter position">
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-base font-medium text-gray-700 mb-2">Current Password</label>
                            <input type="password" id="current_password" name="current_password"
                                class="w-full px-4 py-3 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 @error('current_password') border-red-500 @enderror"
                                placeholder="Enter current password">
                        </div>

                        <div>
                            <label for="password" class="block text-base font-medium text-gray-700 mb-2">New Password</label>
                            <input type="password" id="password" name="password"
                                class="w-full px-4 py-3 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 @error('password') border-red-500 @enderror"
                                placeholder="Enter new password">
                        </div>
                    </div>

                    <div class="mt-10">
                        <button type="submit" class="bg-white border-2 border-red-700 text-red-600 px-6 py-3 rounded-md hover:bg-red-700 hover:text-white text-base">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        // Image preview functionality
        document.getElementById('profile_picture').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-image').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Sidebar dropdown functionality
        document.querySelectorAll('.sidebar-dropdown-button').forEach(button => {
            button.addEventListener('click', () => {
                const dropdownContent = button.nextElementSibling;
                dropdownContent.classList.toggle('hidden');
                
                // Toggle active state (orange color)
                button.classList.toggle('text-orange-500');
                button.classList.toggle('bg-orange-100');
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.sidebar-dropdown-button')) {
                document.querySelectorAll('.sidebar-dropdown-content').forEach(content => {
                    if (!content.classList.contains('hidden')) {
                        content.classList.add('hidden');
                        // Remove active state from button
                        content.previousElementSibling.classList.remove('text-orange-500', 'bg-orange-100');
                    }
                });
            }
        });
    </script>
@endsection