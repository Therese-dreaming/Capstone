@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8" id="mainContent">
    <div class="max-w-5xl mx-auto">
        <div class="mb-6 bg-white rounded-xl shadow p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-red-100 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                        </svg>
                    </div>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800">Manual Log-in/Logout</h1>
                </div>
            </div>
            @if(session('status'))
                <div class="mt-4 p-3 rounded bg-green-100 text-green-800 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mt-4 p-3 rounded bg-red-100 text-red-800 border border-red-200">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Tab Navigation -->
        <div class="mb-6 bg-white rounded-xl shadow">
            <div class="flex border-b border-gray-200">
                <button type="button" onclick="switchTab('logout')" id="logoutTab" class="flex-1 px-4 py-3 text-sm font-medium text-center border-b-2 border-red-600 text-red-600">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Manual Logout</span>
                    </div>
                </button>
                <button type="button" onclick="switchTab('login')" id="loginTab" class="flex-1 px-4 py-3 text-sm font-medium text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>Manual Log-in</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Manual Logout Section -->
        <div id="logoutSection" class="space-y-6">
        <div class="mb-6 bg-white rounded-xl shadow p-4 md:p-6">
            <form method="GET" action="{{ route('lab.manualLogout') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Laboratory</label>
                    <select name="laboratory" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">All</option>
                        @foreach($laboratories as $lab)
                            <option value="{{ $lab }}" @selected(request('laboratory') == $lab)>Lab {{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Faculty ID</label>
                    <input type="number" name="user_id" value="{{ request('user_id') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Optional" />
                </div>
                <div class="flex items-end">
                    <button class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white rounded-lg">Filter</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow p-4 md:p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold">Ongoing Sessions Requiring Manual Logout</h2>
                <div class="bg-yellow-100 px-3 py-1 rounded-full">
                    <span class="text-sm font-medium text-yellow-800">{{ $ongoingLogs->total() }} session(s)</span>
                </div>
            </div>
            
            @if($ongoingLogs->isEmpty())
                <div class="text-center py-8">
                    <div class="bg-green-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">All Clear!</h3>
                    <p class="text-gray-600">No ongoing sessions requiring manual logout.</p>
                </div>
            @else
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start">
                        <div class="bg-blue-100 p-2 rounded-full mr-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="text-sm text-blue-800">
                            <div class="font-medium mb-1">💡 How to Handle Forgotten Sessions:</div>
                            <p>These are sessions where users forgot to tap out. Contact the faculty member to confirm their actual logout time, then use the form below to record the correct time.</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-4">
                    @foreach($ongoingLogs as $log)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                <div>
                                    <div class="font-semibold text-gray-800">{{ $log->user->name }} <span class="text-sm text-gray-500">(ID: {{ $log->user_id }})</span></div>
                                    <div class="text-sm text-gray-600">Laboratory {{ $log->laboratory }} • Time In: {{ $log->time_in->format('Y-m-d h:i A') }}</div>
                                </div>
                                <form method="POST" action="{{ route('lab.manualLogout.submit') }}" class="flex items-center gap-3">
                                    @csrf
                                    <input type="hidden" name="log_id" value="{{ $log->id }}" />
                                    <label class="text-sm text-gray-700">Time Out</label>
                                    <input type="datetime-local" name="time_out" class="border border-gray-300 rounded-lg px-3 py-2" value="{{ now()->format('Y-m-d\TH:i') }}" />
                                    <button class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">Save</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">{{ $ongoingLogs->links() }}</div>
            @endif
        </div>
        </div>

        <!-- Manual Log-in Section -->
        <div id="loginSection" class="space-y-6 hidden">
        <div class="bg-white rounded-xl shadow p-4 md:p-6">
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-2">Create Manual Log-in</h2>
                <p class="text-sm text-gray-600">For users who forgot their RFID card and need to manually log in to a laboratory.</p>
            </div>

            <form method="POST" action="{{ route('lab.manualLogin.submit') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Faculty/Teacher <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            id="userSearch" 
                            autocomplete="off"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2" 
                            placeholder="Type ID or name to search..." 
                            value="{{ old('user_id') ? \App\Models\User::find(old('user_id'))->name ?? old('user_id') : '' }}"
                        />
                        <input type="hidden" name="user_id" id="selectedUserId" value="{{ old('user_id') }}" required />
                        
                        <!-- Dropdown suggestions -->
                        <div id="userDropdown" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <!-- Results will be populated here -->
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Search by ID or name</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Laboratory <span class="text-red-500">*</span></label>
                        <select name="laboratory" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            <option value="">Select Laboratory</option>
                            @foreach($laboratories as $lab)
                                <option value="{{ $lab }}" @selected(old('laboratory') == $lab)>Lab {{ $lab }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Time In <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="time_in" value="{{ old('time_in', now()->format('Y-m-d\TH:i')) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purpose <span class="text-red-500">*</span></label>
                        <select name="purpose[]" multiple class="w-full border border-gray-300 rounded-lg px-3 py-2" style="min-height: 100px;" required>
                            <option value="teaching" {{ in_array('teaching', old('purpose', [])) ? 'selected' : '' }}>Teaching</option>
                            <option value="research" {{ in_array('research', old('purpose', [])) ? 'selected' : '' }}>Research</option>
                            <option value="personal" {{ in_array('personal', old('purpose', [])) ? 'selected' : '' }}>Personal</option>
                            <option value="other" {{ in_array('other', old('purpose', [])) ? 'selected' : '' }}>Other</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Add any additional notes...">{{ old('notes') }}</textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.querySelector('form').reset()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Clear</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">Create Log-in</button>
                </div>
            </form>
        </div>

        <!-- Recent Manual Log-ins -->
        <div class="bg-white rounded-xl shadow p-4 md:p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold">Recent Manual Log-ins</h2>
                <div class="bg-blue-100 px-3 py-1 rounded-full">
                    <span class="text-sm font-medium text-blue-800">{{ $recentManualLogins->total() }} log-in(s)</span>
                </div>
            </div>

            @if($recentManualLogins->isEmpty())
                <div class="text-center py-8">
                    <div class="bg-gray-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Manual Log-ins Yet</h3>
                    <p class="text-gray-600">Manual log-ins created today will appear here.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($recentManualLogins as $log)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="font-semibold text-gray-800">{{ $log->user->name }}</div>
                                        <span class="text-sm text-gray-500">(ID: {{ $log->user_id }})</span>
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">{{ ucfirst($log->status) }}</span>
                                    </div>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <div>Laboratory {{ $log->laboratory }} • Purpose: {{ ucfirst($log->purpose) }}</div>
                                        <div>Time In: {{ $log->time_in->format('Y-m-d h:i A') }}</div>
                                        @if($log->time_out)
                                            <div>Time Out: {{ $log->time_out->format('Y-m-d h:i A') }}</div>
                                        @endif
                                        @if($log->notes)
                                            <div class="text-gray-500 italic">Notes: {{ $log->notes }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-500">Created {{ $log->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">{{ $recentManualLogins->links() }}</div>
            @endif
        </div>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    const logoutTab = document.getElementById('logoutTab');
    const loginTab = document.getElementById('loginTab');
    const logoutSection = document.getElementById('logoutSection');
    const loginSection = document.getElementById('loginSection');

    if (tab === 'logout') {
        // Style logout tab as active
        logoutTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        logoutTab.classList.add('border-red-600', 'text-red-600');
        
        // Style login tab as inactive
        loginTab.classList.remove('border-green-600', 'text-green-600');
        loginTab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        
        // Show/hide sections
        logoutSection.classList.remove('hidden');
        loginSection.classList.add('hidden');
    } else {
        // Style login tab as active
        loginTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        loginTab.classList.add('border-green-600', 'text-green-600');
        
        // Style logout tab as inactive
        logoutTab.classList.remove('border-red-600', 'text-red-600');
        logoutTab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        
        // Show/hide sections
        loginSection.classList.remove('hidden');
        logoutSection.classList.add('hidden');
    }
}

// User autocomplete functionality
let searchTimeout;
const userSearchInput = document.getElementById('userSearch');
const userDropdown = document.getElementById('userDropdown');
const selectedUserIdInput = document.getElementById('selectedUserId');

if (userSearchInput) {
    userSearchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        if (query.length < 1) {
            userDropdown.classList.add('hidden');
            userDropdown.innerHTML = '';
            return;
        }
        
        // Debounce search
        searchTimeout = setTimeout(() => {
            searchUsers(query);
        }, 300);
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!userSearchInput.contains(e.target) && !userDropdown.contains(e.target)) {
            userDropdown.classList.add('hidden');
        }
    });
}

function searchUsers(query) {
    const url = `{{ url('/api/search-faculty') }}?q=${encodeURIComponent(query)}`;
    
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            displayUserResults(data);
        })
        .catch(error => {
            console.error('Error searching users:', error);
            userDropdown.innerHTML = `<div class="px-4 py-2 text-sm text-red-600">Error: ${error.message}</div>`;
            userDropdown.classList.remove('hidden');
        });
}

function displayUserResults(users) {
    if (users.length === 0) {
        userDropdown.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500">No faculty/teachers found</div>';
        userDropdown.classList.remove('hidden');
        return;
    }
    
    let html = '';
    users.forEach(user => {
        const username = user.username ? `@${user.username}` : '';
        const position = user.position || 'N/A';
        const department = user.department || 'N/A';
        
        html += `
            <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0" 
                 onclick="selectUser(${user.id}, '${user.name.replace(/'/g, "\\'")}')">
                <div class="font-medium text-gray-900">${user.name} <span class="text-gray-500 font-normal">${username}</span></div>
                <div class="text-xs text-gray-500">ID: ${user.id} • ${position} • ${department}</div>
            </div>
        `;
    });
    
    userDropdown.innerHTML = html;
    userDropdown.classList.remove('hidden');
}

function selectUser(userId, userName) {
    userSearchInput.value = userName;
    selectedUserIdInput.value = userId;
    userDropdown.classList.add('hidden');
}

// Check if there are validation errors for manual login, switch to that tab
@if($errors->any() && (old('user_id') || old('laboratory') || old('purpose')))
    switchTab('login');
@endif
</script>
@endsection


