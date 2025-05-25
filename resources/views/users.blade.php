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
        <a href="{{ route('users.create') }}" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 text-center w-full sm:w-auto">
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

    <!-- Mobile View Table (Simplified) -->
            <div class="md:hidden">
                <div class="space-y-3">
                    @foreach($users as $user)
                    <div class="border rounded-lg p-3 cursor-pointer hover:bg-gray-50 user-row" data-user-id="{{ $user->id }}">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="font-medium">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $user->username }}</div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs rounded-full {{ $user->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->status }}
                                </span>
                                @if(Auth::user()->group->name === 'Admin')
                                <a href="{{ route('users.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <button type="button" onclick="showDeleteModal('{{ route('users.destroy', $user->id) }}', '{{ $user->name }}')" class="text-red-600 hover:text-red-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Users Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th class="hidden md:table-cell px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="hidden md:table-cell px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                            <th class="hidden md:table-cell px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Role</th>
                            <th class="hidden md:table-cell px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RFID Number</th>
                            <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="hidden md:table-cell px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                            <th class="px-3 md:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr>
                            <td class="px-3 md:px-6 py-4 whitespace-nowrap text-xs md:text-sm">{{ $loop->iteration }}</td>
                            <td class="px-3 md:px-6 py-4 whitespace-nowrap text-xs md:text-sm">{{ $user->name }}</td>
                            <td class="px-3 md:px-6 py-4 whitespace-nowrap text-xs md:text-sm">{{ $user->username }}</td>
                            <td class="hidden md:table-cell px-3 md:px-6 py-4 whitespace-nowrap text-xs md:text-sm">{{ $user->department }}</td>
                            <td class="hidden md:table-cell px-3 md:px-6 py-4 whitespace-nowrap text-xs md:text-sm">{{ $user->position }}</td>
                            <td class="hidden md:table-cell px-3 md:px-6 py-4 whitespace-nowrap text-xs md:text-sm">{{ $user->group->name ?? 'No Group' }}</td>
                            <td class="hidden md:table-cell px-3 md:px-6 py-4 whitespace-nowrap text-xs md:text-sm">
                                @if($user->position === 'Teacher' || $user->position === 'Faculty')
                                {{ $user->rfid_number ?? 'Not Set' }}
                                @else
                                -
                                @endif
                            </td>
                            <td class="px-3 md:px-6 py-4 whitespace-nowrap text-xs md:text-sm">
                                <span class="px-2 py-1 text-xs rounded-full {{ $user->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->status }}
                                </span>
                            </td>
                            <td class="hidden md:table-cell px-3 md:px-6 py-4 whitespace-nowrap text-xs md:text-sm">
                                <div>
                                    <div class="text-gray-900">{{ \Carbon\Carbon::parse($user->last_login)->format('M j, Y') }}</div>
                                    <div class="text-gray-500">{{ \Carbon\Carbon::parse($user->last_login)->format('g:i A') }}</div>
                                </div>
                            </td>
                            <td class="px-3 md:px-6 py-4 whitespace-nowrap text-right text-xs md:text-sm font-medium">
                                @if(Auth::user()->group->name === 'Admin')
                                <a href="{{ route('users.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <button type="button" onclick="showDeleteModal('{{ route('users.destroy', $user->id) }}', '{{ $user->name }}')" class="text-red-600 hover:text-red-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
                        <!-- Mobile View - User Details -->
                        <div class="md:hidden mt-6">
                            <h2 class="text-lg font-semibold mb-3">User Details</h2>
                            <p class="text-sm text-gray-600 mb-4">Tap on a user row to see more information.</p>
                            
                            @foreach($users as $user)
                            <div class="user-details hidden border rounded-lg p-4 mb-4 bg-gray-50" id="user-details-{{ $user->id }}">
                                <h3 class="font-bold text-lg mb-2">{{ $user->name }}</h3>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div class="font-medium">Department:</div>
                                    <div>{{ $user->department }}</div>
                                    
                                    <div class="font-medium">Position:</div>
                                    <div>{{ $user->position }}</div>
                                    
                                    <div class="font-medium">User Role:</div>
                                    <div>{{ $user->group->name ?? 'No Group' }}</div>
                                    
                                    @if($user->position === 'Teacher' || $user->position === 'Faculty')
                                    <div class="font-medium">RFID Number:</div>
                                    <div>{{ $user->rfid_number ?? 'Not Set' }}</div>
                                    @endif
                                    
                                    <div class="font-medium">Last Login:</div>
                                    <div>{{ \Carbon\Carbon::parse($user->last_login)->format('M j, Y g:i A') }}</div>
                                </div>
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

    // Mobile view - Show user details on row click
    document.addEventListener('DOMContentLoaded', function() {
        const userRows = document.querySelectorAll('.user-row');
        const userDetails = document.querySelectorAll('.user-details');
        
        userRows.forEach(row => {
            row.addEventListener('click', function(e) {
                // Don't trigger if clicking on action buttons
                if (e.target.closest('a') || e.target.closest('button')) {
                    return;
                }
                
                const userId = this.dataset.userId;
                
                // Hide all details first
                userDetails.forEach(detail => detail.classList.add('hidden'));
                
                // Show the clicked user's details
                const detailElement = document.getElementById(`user-details-${userId}`);
                if (detailElement) {
                    detailElement.classList.remove('hidden');
                    
                    // Scroll to the details
                    detailElement.scrollIntoView({behavior: 'smooth'});
                }
            });
        });
    });
</script>
@endsection