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
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800">Manual Logout</h1>
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
</div>
@endsection


