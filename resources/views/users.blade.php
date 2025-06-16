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
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="flex-1">
    <div class="p-4 md:p-6">
<!-- Main Container -->
<div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-3">
        <h1 class="text-xl md:text-2xl font-bold">USERS</h1>
        @if(Auth::user()->group->name === 'Admin')
        <a href="{{ route('users.create') }}" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 text-center w-full sm:w-auto">
            Add New User
        </a>
        @endif
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

    <!-- Divider Line -->
    <div class="border-b-2 border-red-800 mb-4 md:mb-6"></div>
    
    <!-- User Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($users as $user)
        <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200 ease-in-out overflow-hidden flex flex-col">
            <div class="flex items-center p-4 border-b border-gray-200">
                <img src="{{ $user->profile_picture ? asset($user->profile_picture) : asset('images/default-profile.png') }}" alt="{{ $user->name }}'s profile picture" class="w-12 h-12 rounded-full object-cover mr-4">
                <div>
                    <h3 class="font-bold text-lg text-gray-800">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $user->username }}</p>
                </div>
            </div>
            <div class="p-4 text-sm text-gray-700 space-y-2 flex-grow">
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Department:</span>
                    <span>{{ $user->department }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Position:</span>
                    <span>{{ $user->position }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">User Role:</span>
                    <span>{{ $user->group->name ?? 'No Group' }}</span>
                </div>
                @if($user->position === 'Teacher' || $user->position === 'Faculty')
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">RFID Number:</span>
                    <span>{{ $user->rfid_number ?? 'Not Set' }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Last Login:</span>
                    <span>{{ \Carbon\Carbon::parse($user->last_login)->format('M j, Y g:i A') }}</span>
                </div>
            </div>
            @if(Auth::user()->group->name === 'Admin')
            <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3 items-center mt-auto">
                <a href="{{ route('users.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit User">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </a>
                <button type="button" onclick="showDeleteModal('{{ route('users.destroy', $user->id) }}', '{{ $user->name }}')" class="text-red-600 hover:text-red-900" title="Delete User">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
</div>
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