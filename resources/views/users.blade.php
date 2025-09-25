@extends('layouts.app')

@section('content')
<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Delete User</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Are you sure you want to delete this user? This action cannot be undone.</p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="deleteForm" method="POST" class="flex justify-center space-x-4">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Page Header with Background Design -->
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                        <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">User Management</h1>
                        <p class="text-red-100 text-sm md:text-lg">Manage system users and their permissions</p>
                    </div>
                </div>
                @if(Auth::user()->group->name === 'Admin')
                <a href="{{ route('users.create') }}" class="bg-white/20 backdrop-blur-sm text-white px-4 md:px-6 py-2 md:py-3 rounded-xl hover:bg-white/30 transition-all duration-200 font-medium flex items-center justify-center">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add New User
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="mb-4 md:mb-6 p-3 md:p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center">
        <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 md:mr-3 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="text-sm md:text-base">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 md:mb-6 p-3 md:p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-center">
        <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 md:mr-3 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <span class="text-sm md:text-base">{{ session('error') }}</span>
    </div>
    @endif

    <!-- User Statistics -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4 flex items-center">
            <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            User Overview
        </h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-3 md:p-4 rounded-xl border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-blue-700 mb-1">Total Users</p>
                        <p class="text-xl md:text-2xl font-bold text-blue-900">{{ $users->count() }}</p>
                    </div>
                    <div class="bg-blue-200 p-2 md:p-3 rounded-full">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-green-50 to-green-100 p-3 md:p-4 rounded-xl border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-green-700 mb-1">Active Users</p>
                        <p class="text-xl md:text-2xl font-bold text-green-900">{{ $users->where('last_login', '>=', now()->subDays(30))->count() }}</p>
                    </div>
                    <div class="bg-green-200 p-2 md:p-3 rounded-full">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 p-3 md:p-4 rounded-xl border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-yellow-700 mb-1">Teachers/Faculty</p>
                        <p class="text-xl md:text-2xl font-bold text-yellow-900">{{ $users->whereIn('position', ['Teacher', 'Faculty'])->count() }}</p>
                    </div>
                    <div class="bg-yellow-200 p-2 md:p-3 rounded-full">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-3 md:p-4 rounded-xl border border-purple-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-purple-700 mb-1">Admins</p>
                        <p class="text-xl md:text-2xl font-bold text-purple-900">{{ $users->where('group.name', 'Admin')->count() }}</p>
                    </div>
                    <div class="bg-purple-200 p-2 md:p-3 rounded-full">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- User Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        @foreach($users as $user)
        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-200 overflow-hidden border border-gray-100 h-full flex flex-col">
            <!-- User Header -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-4 md:p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="relative">
                        <img src="{{ $user->getProfilePictureUrl() }}" 
                             alt="{{ $user->name }}'s profile picture" 
                             class="w-12 h-12 md:w-16 md:h-16 rounded-full object-cover border-4 border-white shadow-md">
                    </div>
                    <div class="ml-3 md:ml-4 flex-1 min-w-0">
                        <h3 class="font-bold text-lg md:text-xl text-gray-900 mb-1 truncate">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-600 mb-2 truncate">{{ $user->username }}</p>
                        <span class="inline-flex items-center px-2 md:px-3 py-1 rounded-full text-xs font-medium 
                            @if($user->group->name === 'Admin') bg-purple-100 text-purple-800
                            @elseif($user->group->name === 'Secretary') bg-blue-100 text-blue-800
                            @elseif($user->group->name === 'Technician') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $user->group->name ?? 'No Group' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- User Details -->
            <div class="p-4 md:p-6 space-y-3 md:space-y-4 flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs font-medium text-gray-500 mb-1">Department</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->department }}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs font-medium text-gray-500 mb-1">Position</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->position }}</p>
                    </div>
                </div>
                
                @if($user->position === 'Teacher' || $user->position === 'Faculty')
                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                    <p class="text-xs font-medium text-blue-600 mb-1">RFID Number</p>
                    <p class="text-sm font-semibold text-blue-900 truncate">{{ $user->rfid_number ?? 'Not Set' }}</p>
                </div>
                @endif
                
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs font-medium text-gray-500 mb-1">Last Login</p>
                    <p class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($user->last_login)->format('M j, Y g:i A') }}</p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            @if(Auth::user()->group->name === 'Admin')
            <div class="p-3 md:p-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-2 md:space-x-3">
                <a href="{{ route('users.edit', $user->id) }}" 
                   class="p-2 text-yellow-600 hover:text-yellow-900 hover:bg-yellow-50 rounded-lg transition-colors duration-200" 
                   title="Edit User">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </a>
                <button type="button" 
                        onclick="showDeleteModal('{{ route('users.destroy', $user->id) }}', '{{ $user->name }}')" 
                        class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors duration-200" 
                        title="Delete User">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    
    <!-- Empty State -->
    @if($users->count() === 0)
    <div class="text-center py-8 md:py-12 bg-white rounded-xl shadow-md border border-dashed border-gray-300">
        <div class="bg-gray-100 rounded-full p-3 md:p-4 w-16 h-16 md:w-20 md:h-20 mx-auto mb-3 md:mb-4 flex items-center justify-center">
            <svg class="h-8 w-8 md:h-10 md:w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
            </svg>
        </div>
        <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">No users found</h3>
        <p class="text-sm text-gray-500 mb-4">Get started by adding your first user to the system.</p>
        @if(Auth::user()->group->name === 'Admin')
        <a href="{{ route('users.create') }}" class="inline-flex items-center px-3 md:px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 text-sm md:text-base">
            <svg class="w-3 h-3 md:w-4 md:h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add First User
        </a>
        @endif
    </div>
    @endif
</div>

<script>
    function showDeleteModal(formAction, userName) {
        const modal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const modalTitle = modal.querySelector('h3');

        modalTitle.textContent = `Delete User: ${userName}`;
        deleteForm.action = formAction;
        modal.classList.remove('hidden');
    }

    function closeModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
    }

    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endsection