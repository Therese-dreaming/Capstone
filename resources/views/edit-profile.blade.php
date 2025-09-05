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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Edit Profile</h1>
                        <p class="text-red-100 text-sm md:text-lg">Update your personal information and account settings</p>
                    </div>
                </div>
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
            <ul class="list-disc list-inside ml-4 space-y-1 text-sm md:text-base">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Content Card -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
        <div class="flex flex-col lg:flex-row gap-6 md:gap-8">
            <!-- Left Column - Profile Picture -->
            <div class="lg:w-1/3">
                <div class="bg-gray-50 rounded-xl p-6 md:p-8 text-center">
                    <div class="relative w-48 h-48 mx-auto mb-6">
                        <div class="w-full h-full rounded-full overflow-hidden border-4 border-red-100 shadow-lg">
                            <img src="{{ auth()->user()->getProfilePictureUrl() }}" 
                                alt="Profile Picture" 
                                id="preview-image"
                                class="w-full h-full object-cover">
                        </div>
                    </div>
                    
                    <input type="file" 
                        id="profile_picture" 
                        name="profile_picture" 
                        accept="image/*" 
                        class="hidden"
                        form="profile-form">
                    
                    <button type="button" 
                        onclick="document.getElementById('profile_picture').click()" 
                        class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Change Picture
                    </button>
                    
                    <p class="text-sm text-gray-500 mt-3">JPG, PNG or GIF. Max size 2MB.</p>
                </div>
            </div>

            <!-- Right Column - Form -->
            <div class="lg:w-2/3">
                <form id="profile-form" action="{{ route('profile.update') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Personal Information Section -->
                    <div class="mb-8">
                        <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-4 md:mb-6 flex items-center">
                            <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Personal Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 @error('name') border-red-500 @enderror"
                                    placeholder="Enter your full name">
                            </div>

                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                                <input type="text" id="username" name="username" value="{{ old('username', auth()->user()->username) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 @error('username') border-red-500 @enderror"
                                    placeholder="Enter username">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mt-4 md:mt-6">
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                <select id="gender" name="gender" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 @error('gender') border-red-500 @enderror">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', auth()->user()->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', auth()->user()->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>

                            <div>
                                <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                                <input type="text" id="position" name="position" value="{{ old('position', auth()->user()->position) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 @error('position') border-red-500 @enderror"
                                    placeholder="Enter your position">
                            </div>
                        </div>

                        <div class="mt-4 md:mt-6">
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <select id="department" name="department" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 @error('department') border-red-500 @enderror">
                                <option value="">Select Department</option>
                                <option value="Early Childhood Education (ECE)" {{ old('department', auth()->user()->department) == 'Early Childhood Education (ECE)' ? 'selected' : '' }}>Early Childhood Education (ECE)</option>
                                <option value="Grade School" {{ old('department', auth()->user()->department) == 'Grade School' ? 'selected' : '' }}>Grade School</option>
                                <option value="Junior High School" {{ old('department', auth()->user()->department) == 'Junior High School' ? 'selected' : '' }}>Junior High School</option>
                                <option value="Senior High School" {{ old('department', auth()->user()->department) == 'Senior High School' ? 'selected' : '' }}>Senior High School</option>
                                <option value="College" {{ old('department', auth()->user()->department) == 'College' ? 'selected' : '' }}>College</option>
                                <option value="School of Graduate Studies" {{ old('department', auth()->user()->department) == 'School of Graduate Studies' ? 'selected' : '' }}>School of Graduate Studies</option>
                            </select>
                        </div>
                    </div>

                    <!-- Password Section -->
                    <div class="mb-8">
                        <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-4 md:mb-6 flex items-center">
                            <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Change Password
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                <input type="password" id="current_password" name="current_password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 @error('current_password') border-red-500 @enderror"
                                    placeholder="Enter current password">
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                <input type="password" id="password" name="password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 @error('password') border-red-500 @enderror"
                                    placeholder="Enter new password">
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-500 mt-3">Leave password fields empty if you don't want to change your password.</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                        <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 md:px-8 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-md">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save Changes
                        </button>
                        
                        <a href="{{ route('dashboard') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 md:px-8 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel
                        </a>
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