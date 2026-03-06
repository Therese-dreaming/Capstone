@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50" id="mainContent">
    <div class="max-w-7xl mx-auto">
        <!-- Header with Alerts -->
        <div class="mb-6">
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-xl shadow-lg p-6 text-white mb-4">
                <div class="flex items-center gap-4">
                    <div class="bg-white p-3 rounded-lg">
                        <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-2xl md:text-3xl font-bold">Manual Time Management</h1>
                        <p class="text-red-100 mt-1">Handle forgotten check-ins and check-outs</p>
                    </div>
                </div>
            </div>
            
            @if(session('status'))
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-green-100 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="text-green-800 font-medium">{{ session('status') }}</p>
                    </div>
                </div>
            @endif
            
            @if($errors->any())
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-red-500 mb-4">
                    <div class="flex items-start gap-3">
                        <div class="bg-red-100 p-2 rounded-lg mt-0.5">
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-red-800 mb-2">Please correct the following errors:</p>
                            <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Tab Navigation -->
        <div class="mb-6 bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="flex">
                <button type="button" onclick="switchTab('logout')" id="logoutTab" class="flex-1 px-6 py-4 text-base font-semibold text-center border-b-3 border-red-600 bg-red-50 text-red-700 transition-all">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Manual Logout</span>
                    </div>
                </button>
                <button type="button" onclick="switchTab('login')" id="loginTab" class="flex-1 px-6 py-4 text-base font-semibold text-center border-b-3 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-all">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>Manual Log-in</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Manual Logout Section -->
        <div id="logoutSection" class="space-y-6">
            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter Sessions
                </h3>
                <form method="GET" action="{{ route('lab.manualLogout') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Laboratory</label>
                        <select name="laboratory" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                            <option value="">All Laboratories</option>
                            @foreach($laboratories as $lab)
                                <option value="{{ $lab }}" @selected(request('laboratory') == $lab)>Laboratory {{ $lab }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Faculty ID</label>
                        <input type="number" name="user_id" value="{{ request('user_id') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition" placeholder="Enter ID..." />
                    </div>
                    <div class="md:col-span-2 flex items-end gap-3">
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-medium rounded-lg shadow-sm transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Apply Filters
                        </button>
                        @if(request()->has('laboratory') || request()->has('user_id'))
                            <a href="{{ route('lab.manualLogout') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Ongoing Sessions -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-red-100 p-2.5 rounded-lg">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-bold text-gray-800">Ongoing Sessions</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Total:</span>
                            <div class="bg-yellow-100 px-4 py-1.5 rounded-lg">
                                <span class="text-base font-bold text-yellow-800">{{ $ongoingLogs->total() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($ongoingLogs->isEmpty())
                    <div class="text-center py-16">
                        <div class="bg-green-100 p-4 rounded-2xl w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">All Clear!</h3>
                        <p class="text-gray-600">No ongoing sessions requiring manual logout.</p>
                    </div>
                @else
                    <div class="p-6">
                        <div class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
                            <div class="flex items-start gap-3">
                                <div class="bg-blue-100 p-2 rounded-lg mt-0.5">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-blue-900 mb-1">💡 How to Handle Forgotten Sessions</p>
                                    <p class="text-sm text-blue-800">Contact the faculty member to confirm their actual logout time, then set the time below and click Save.</p>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200 bg-gray-50">
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">Faculty</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">Laboratory</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">Purpose</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">Time In</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">Duration</th>
                                        <th class="text-right py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($ongoingLogs as $log)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="py-4 px-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                                        {{ substr($log->user->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-gray-900">{{ $log->user->name }}</div>
                                                        <div class="text-xs text-gray-500">ID: {{ $log->user_id }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-100 text-red-700 rounded-lg font-medium text-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                    Lab {{ $log->laboratory }}
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <span class="text-sm text-gray-700">{{ ucfirst($log->purpose) }}</span>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $log->time_in->format('M d, Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $log->time_in->format('h:i A') }}</div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <span class="text-sm font-semibold text-yellow-700">{{ $log->time_in->diffForHumans(null, true) }}</span>
                                            </td>
                                            <td class="py-4 px-4">
                                                <form method="POST" action="{{ route('lab.manualLogout.submit') }}" class="flex items-center justify-end gap-2">
                                                    @csrf
                                                    <input type="hidden" name="log_id" value="{{ $log->id }}" />
                                                    <input type="datetime-local" name="time_out" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" value="{{ now()->format('Y-m-d\TH:i') }}" required />
                                                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium rounded-lg shadow-sm transition-all flex items-center gap-2">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Save
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $ongoingLogs->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Manual Log-in Section -->
        <div id="loginSection" class="space-y-6 hidden">
            <!-- Create Manual Log-in Form -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-200">
                    <div class="flex items-center gap-3">
                        <div class="bg-green-100 p-2.5 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Create Manual Log-in</h2>
                            <p class="text-sm text-gray-600">For faculty who forgot their RFID card or need backdated entry</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('lab.manualLogin.submit') }}" class="p-6 space-y-6">
                    @csrf
                    
                    <!-- Faculty Search (Full Width) -->
                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Faculty/Teacher <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                id="userSearch" 
                                autocomplete="off"
                                class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" 
                                placeholder="Type ID or name to search..." 
                                value="{{ old('user_id') ? \App\Models\User::find(old('user_id'))->name ?? old('user_id') : '' }}"
                            />
                        </div>
                        <input type="hidden" name="user_id" id="selectedUserId" value="{{ old('user_id') }}" required />
                        
                        <!-- Dropdown suggestions -->
                        <div id="userDropdown" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                            <!-- Results will be populated here -->
                        </div>
                        <p class="text-xs text-gray-500 mt-1.5">Search by faculty ID or name</p>
                    </div>

                    <!-- Two Column Layout for Cards -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Laboratory Cards -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Laboratory <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3 max-h-96 overflow-y-auto p-1">
                                @foreach($laboratories as $lab)
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="laboratory" value="{{ $lab }}" class="peer hidden" 
                                               @checked(old('laboratory') == $lab)
                                               onchange="this.closest('label').classList.add('animate-pulse'); setTimeout(() => this.closest('label').classList.remove('animate-pulse'), 200);" />
                                        <div class="border-2 border-gray-300 rounded-lg p-4 text-center transition-all hover:border-green-400 hover:shadow-md peer-checked:border-green-600 peer-checked:bg-gradient-to-br peer-checked:from-green-50 peer-checked:to-green-100 peer-checked:shadow-lg">
                                            <div class="text-gray-600 peer-checked:text-green-700 text-xs font-medium mb-1">Laboratory</div>
                                            <div class="text-xl font-bold text-gray-800 peer-checked:text-green-800">{{ $lab }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Select one laboratory</p>
                        </div>

                        <!-- Purpose Cards -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Purpose <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3 max-h-96 overflow-y-auto p-1">
                                @php
                                    $purposes = [
                                        'lecture' => ['name' => 'Lecture', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                                        'examination' => ['name' => 'Examination', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                        'practical' => ['name' => 'Practical', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
                                        'research' => ['name' => 'Research', 'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                                        'training' => ['name' => 'Training', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                                        'other' => ['name' => 'Other', 'icon' => 'M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z']
                                    ];
                                @endphp
                                @foreach($purposes as $value => $purpose)
                                    <label class="relative cursor-pointer">
                                        <input type="checkbox" name="purpose[]" value="{{ $value }}" class="peer hidden" 
                                               @checked(in_array($value, old('purpose', [])))
                                               onchange="this.closest('label').classList.add('animate-pulse'); setTimeout(() => this.closest('label').classList.remove('animate-pulse'), 200);" />
                                        <div class="border-2 border-gray-300 rounded-lg p-3 text-center transition-all hover:border-green-400 hover:shadow-md peer-checked:border-green-600 peer-checked:bg-gradient-to-br peer-checked:from-green-50 peer-checked:to-green-100 peer-checked:shadow-lg h-full">
                                            <svg class="w-6 h-6 mx-auto mb-2 text-gray-500 peer-checked:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $purpose['icon'] }}" />
                                            </svg>
                                            <div class="text-sm font-semibold text-gray-700 peer-checked:text-green-800">{{ $purpose['name'] }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Select one or more purposes</p>
                        </div>
                    </div>

                    <!-- Time In and Notes (Full Width) -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Time In <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="time_in" value="{{ old('time_in', now()->format('Y-m-d\TH:i')) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" required />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Notes (Optional)</label>
                            <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" placeholder="Add any additional notes...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                        <button type="button" onclick="document.getElementById('loginSection').querySelector('form').reset()" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                            Clear Form
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium rounded-lg shadow-sm transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Log-in
                        </button>
                    </div>
                </form>
            </div>

            <!-- Recent Manual Log-ins -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-100 p-2.5 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-bold text-gray-800">Recent Manual Log-ins</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Total:</span>
                            <div class="bg-blue-100 px-4 py-1.5 rounded-lg">
                                <span class="text-base font-bold text-blue-800">{{ $recentManualLogins->total() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($recentManualLogins->isEmpty())
                    <div class="text-center py-16">
                        <div class="bg-gray-100 p-4 rounded-2xl w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">No Manual Log-ins Yet</h3>
                        <p class="text-gray-600">Manual log-ins created today will appear here.</p>
                    </div>
                @else
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200 bg-gray-50">
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">Faculty</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">Laboratory</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">Purpose</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">Time In</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($recentManualLogins as $log)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="py-4 px-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                                        {{ substr($log->user->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-gray-900">{{ $log->user->name }}</div>
                                                        <div class="text-xs text-gray-500">ID: {{ $log->user_id }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-100 text-green-700 rounded-lg font-medium text-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                    Lab {{ $log->laboratory }}
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <span class="text-sm text-gray-700">{{ ucfirst($log->purpose) }}</span>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $log->time_in->format('M d, Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $log->time_in->format('h:i A') }}</div>
                                            </td>
                                            <td class="py-4 px-4">
                                                @if($log->status === 'on-going')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                                        <span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span>
                                                        On-going
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                                        Completed
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-4">
                                                @if($log->notes)
                                                    <p class="text-sm text-gray-600 italic line-clamp-2">{{ $log->notes }}</p>
                                                @else
                                                    <span class="text-sm text-gray-400">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $recentManualLogins->links() }}
                    </div>
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
        logoutTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-50');
        logoutTab.classList.add('border-red-600', 'text-red-700', 'bg-red-50');
        
        // Style login tab as inactive
        loginTab.classList.remove('border-green-600', 'text-green-700', 'bg-green-50');
        loginTab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-50');
        
        // Show/hide sections
        logoutSection.classList.remove('hidden');
        loginSection.classList.add('hidden');
    } else {
        // Style login tab as active
        loginTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-50');
        loginTab.classList.add('border-green-600', 'text-green-700', 'bg-green-50');
        
        // Style logout tab as inactive
        logoutTab.classList.remove('border-red-600', 'text-red-700', 'bg-red-50');
        logoutTab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-50');
        
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
            userDropdown.innerHTML = '<div class="px-4 py-3 text-sm text-red-600">Error loading faculty. Please try again.</div>';
            userDropdown.classList.remove('hidden');
        });
}

function displayUserResults(users) {
    if (users.length === 0) {
        userDropdown.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">No faculty/teachers found</div>';
        userDropdown.classList.remove('hidden');
        return;
    }
    
    let html = '';
    users.forEach(user => {
        const username = user.username ? `@${user.username}` : '';
        const position = user.position || 'N/A';
        const department = user.department || 'N/A';
        
        html += `
            <div class="px-4 py-3 hover:bg-green-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition" 
                 onclick="selectUser(${user.id}, '${user.name.replace(/'/g, "\\'")}')">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold text-xs">
                        ${user.name.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900">${user.name} <span class="text-gray-500 font-normal text-sm">${username}</span></div>
                        <div class="text-xs text-gray-500">ID: ${user.id} • ${position} • ${department}</div>
                    </div>
                </div>
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


