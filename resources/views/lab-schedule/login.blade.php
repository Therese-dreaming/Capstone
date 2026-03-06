@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen" id="mainContent">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8 bg-gradient-to-r from-red-600 to-red-700 rounded-2xl shadow-xl p-8 md:p-10 text-white">
            <div class="flex items-center gap-4">
                <div class="bg-white/20 p-4 rounded-xl backdrop-blur-sm shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold">Laboratory RFID Access</h1>
                    <p class="text-red-100 mt-2 text-lg">Faculty Time In/Out System</p>
                </div>
            </div>
        </div>

        <!-- Selection Form -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-10 border border-gray-100">
            <div class="space-y-8">
                <!-- Purpose Selection -->
                <div id="purposeSection">
                    <label class="block text-base font-bold text-gray-800 mb-4">
                        <span class="flex items-center gap-2">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Select Purpose(s) <span id="purposeRequired" class="text-red-500">*</span>
                        </span>
                    </label>
                    
                    <!-- Logout Notice -->
                    <div id="logoutNotice" class="hidden mb-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-blue-800">This laboratory is in use. Click "Continue" to logout.</p>
                                <p class="text-xs text-blue-600 mt-1">Purpose selection is not required for logout.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Purpose Error Message -->
                    <div id="purposeError" class="hidden mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-shake">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-red-800">Please select at least one purpose</p>
                            </div>
                            <button onclick="hidePurposeError()" class="flex-shrink-0 text-red-500 hover:text-red-700 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-3">
                            <!-- Lecture Card -->
                            <label class="purpose-card group cursor-pointer">
                                <input type="checkbox" name="purpose" value="lecture" class="purpose-checkbox sr-only">
                                <div class="relative h-full border-2 border-gray-300 rounded-lg p-4 transition-all duration-200 hover:border-red-400 hover:shadow-lg hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center gap-2">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center group-hover:from-red-50 group-hover:to-red-100 transition-all duration-200 shadow-sm">
                                            <svg class="w-5 h-5 text-gray-600 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-700">Lecture</span>
                                    </div>
                                    <div class="checkmark-badge">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </label>

                            <!-- Examination Card -->
                            <label class="purpose-card group cursor-pointer">
                                <input type="checkbox" name="purpose" value="examination" class="purpose-checkbox sr-only">
                                <div class="relative h-full border-2 border-gray-300 rounded-lg p-4 transition-all duration-200 hover:border-red-400 hover:shadow-lg hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center gap-2">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center group-hover:from-red-50 group-hover:to-red-100 transition-all duration-200 shadow-sm">
                                            <svg class="w-5 h-5 text-gray-600 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-700">Examination</span>
                                    </div>
                                    <div class="checkmark-badge">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </label>

                            <!-- Practical Card -->
                            <label class="purpose-card group cursor-pointer">
                                <input type="checkbox" name="purpose" value="practical" class="purpose-checkbox sr-only">
                                <div class="relative h-full border-2 border-gray-300 rounded-lg p-4 transition-all duration-200 hover:border-red-400 hover:shadow-lg hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center gap-2">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center group-hover:from-red-50 group-hover:to-red-100 transition-all duration-200 shadow-sm">
                                            <svg class="w-5 h-5 text-gray-600 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-700">Practical</span>
                                    </div>
                                    <div class="checkmark-badge">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </label>

                            <!-- Research Card -->
                            <label class="purpose-card group cursor-pointer">
                                <input type="checkbox" name="purpose" value="research" class="purpose-checkbox sr-only">
                                <div class="relative h-full border-2 border-gray-300 rounded-lg p-4 transition-all duration-200 hover:border-red-400 hover:shadow-lg hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center gap-2">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center group-hover:from-red-50 group-hover:to-red-100 transition-all duration-200 shadow-sm">
                                            <svg class="w-5 h-5 text-gray-600 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                            </svg>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-700">Research</span>
                                    </div>
                                    <div class="checkmark-badge">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </label>

                            <!-- Training Card -->
                            <label class="purpose-card group cursor-pointer">
                                <input type="checkbox" name="purpose" value="training" class="purpose-checkbox sr-only">
                                <div class="relative h-full border-2 border-gray-300 rounded-lg p-4 transition-all duration-200 hover:border-red-400 hover:shadow-lg hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center gap-2">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center group-hover:from-red-50 group-hover:to-red-100 transition-all duration-200 shadow-sm">
                                            <svg class="w-5 h-5 text-gray-600 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-700">Training</span>
                                    </div>
                                    <div class="checkmark-badge">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </label>

                            <!-- Other Card -->
                            <label class="purpose-card group cursor-pointer">
                                <input type="checkbox" name="purpose" value="other" class="purpose-checkbox sr-only">
                                <div class="relative h-full border-2 border-gray-300 rounded-lg p-4 transition-all duration-200 hover:border-red-400 hover:shadow-lg hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center gap-2">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center group-hover:from-red-50 group-hover:to-red-100 transition-all duration-200 shadow-sm">
                                            <svg class="w-5 h-5 text-gray-600 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-700">Other</span>
                                    </div>
                                    <div class="checkmark-badge">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </label>
                    </div>
                </div>

                <!-- Laboratory Selection -->
                <div>
                    <label class="block text-lg font-bold text-gray-800 mb-5">
                        <span class="flex items-center gap-2">
                            <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Select Laboratory <span class="text-red-500">*</span>
                        </span>
                    </label>
                    
                    <!-- Laboratory Error Message -->
                    <div id="laboratoryError" class="hidden mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-shake">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-red-800">Please select a laboratory</p>
                            </div>
                            <button onclick="hideLaboratoryError()" class="flex-shrink-0 text-red-500 hover:text-red-700 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-5">
                            @foreach($laboratories as $lab)
                            @php
                                $hasSession = in_array($lab->number, $labsWithSessions ?? []);
                            @endphp
                            <div class="lab-card-wrapper" @if($hasSession) data-lab-number="{{ $lab->number }}" @endif>
                                <label class="lab-card group cursor-pointer {{ $hasSession ? 'has-session' : '' }}">
                                    <input type="radio" name="laboratory" value="{{ $lab->number }}" class="lab-radio sr-only">
                                    <div class="relative h-full border-2 {{ $hasSession ? 'border-red-400 bg-gradient-to-br from-red-50 to-red-100' : 'border-gray-300' }} rounded-xl p-7 transition-all duration-200 hover:border-red-400 hover:shadow-lg hover:-translate-y-1">
                                        <div class="flex flex-col items-center text-center gap-4">
                                            <div class="w-20 h-20 rounded-xl {{ $hasSession ? 'bg-gradient-to-br from-red-200 to-red-300 ring-2 ring-red-400' : 'bg-gradient-to-br from-gray-100 to-gray-200' }} flex items-center justify-center group-hover:from-red-50 group-hover:to-red-100 transition-all duration-200 shadow-md">
                                                <span class="text-3xl font-bold {{ $hasSession ? 'text-red-800' : 'text-gray-700' }} group-hover:text-red-600 transition-colors">{{ $lab->number }}</span>
                                            </div>
                                            <span class="text-base font-bold {{ $hasSession ? 'text-red-800' : 'text-gray-700' }}">Lab {{ $lab->number }}</span>
                                            @if($hasSession)
                                            <button type="button" class="view-lab-users text-xs font-medium text-white bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded-full transition-colors shadow-sm" onclick="event.preventDefault(); showLabUsers({{ $lab->number }})">
                                                View Users
                                            </button>
                                            @endif
                                        </div>
                                        <div class="checkmark-badge">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                    </div>
                </div>

                <!-- Continue Button -->
                <button 
                    onclick="openModal()"
                    type="button"
                    class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold py-5 px-8 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-200 hover:-translate-y-1 flex items-center justify-center gap-3 text-lg"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <span>Continue to RFID Scan</span>
                </button>
            </div>
        </div>

        <!-- Lab Users Modal -->
        <div id="labUsersModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-hidden">
                <div class="bg-gradient-to-r from-red-600 to-red-700 p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span id="labUsersTitle">Current Users</span>
                        </h3>
                        <button onclick="closeLabUsersModal()" class="text-white hover:text-red-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <div id="labUsersContent" class="space-y-4">
                        <!-- Users will be populated here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="mt-8 bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg p-6 md:p-8 border-l-4 border-red-600">
            <div class="flex items-start gap-4">
                <div class="bg-gradient-to-br from-red-100 to-red-50 p-3 rounded-xl shadow-sm">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-gray-900 mb-3 text-lg">How to use:</h3>
                    <ul class="space-y-2.5 text-sm text-gray-700">
                        <li class="flex items-start gap-3">
                            <span class="text-red-600 font-bold text-base">1.</span>
                            <span>Click on one or more purpose cards (required for login only)</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-600 font-bold text-base">2.</span>
                            <span>Click on a laboratory card to select which lab you want to access</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-600 font-bold text-base">3.</span>
                            <span>Click "Continue to RFID Scan" button</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-600 font-bold text-base">4.</span>
                            <span>Tap your RFID card when the modal appears</span>
                        </li>
                        <li class="flex items-start gap-3 pt-1 border-t border-gray-200">
                            <span class="text-red-600 font-bold text-base">•</span>
                            <span><strong class="text-gray-900">For logout:</strong> Just select laboratory card and click continue (no purpose needed)</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-600 font-bold text-base">•</span>
                            <span><strong class="text-gray-900">Lab Status:</strong> Laboratories marked "In Use" (red) have ongoing sessions</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- RFID Scan Modal -->
<div id="rfidModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full transform transition-all">
        <!-- Modal Content - Ready to Scan -->
        <div id="modalReady" class="p-8 text-center">
            <div class="mb-6">
                <div class="mx-auto w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mb-4 animate-pulse">
                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Ready to Scan</h3>
                <p class="text-gray-600 mb-4">Please tap your RFID card now</p>
                
                <!-- Selected Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4 text-left">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="font-semibold text-gray-700">Laboratory:</span>
                        <span id="modalLaboratory" class="text-gray-900"></span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span class="font-semibold text-gray-700">Purpose:</span>
                        <span id="modalPurpose" class="text-gray-900"></span>
                    </div>
                </div>
            </div>
            
            <!-- RFID Input (hidden but functional) -->
            <input 
                type="text" 
                id="rfidInput" 
                class="w-full border-2 border-red-600 rounded-lg px-4 py-3 text-center text-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                placeholder="Scan RFID card..."
                autofocus
            >
            
            <button onclick="closeModal()" class="mt-6 w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </button>
        </div>
        
        <!-- Modal Content - Loading -->
        <div id="modalLoading" class="p-8 text-center hidden">
            <div class="mb-6">
                <div class="mx-auto w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-red-600 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Processing...</h3>
                <p class="text-gray-600">Please wait while we process your request</p>
            </div>
        </div>
        
        <!-- Modal Content - Success -->
        <div id="modalSuccess" class="p-8 hidden">
            <div class="text-center mb-6">
                <div class="mx-auto w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2" id="successTitle">Success!</h3>
                <p class="text-gray-600" id="successMessage"></p>
            </div>
            
            <!-- User Info Card -->
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 mb-6 border border-red-200">
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center text-white font-bold text-lg" id="userInitial">
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Faculty Name</p>
                            <p class="font-bold text-gray-900" id="userName"></p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 pt-3 border-t border-red-200">
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Laboratory</p>
                            <p class="font-semibold text-gray-900" id="successLab"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Status</p>
                            <p class="font-semibold" id="successStatus"></p>
                        </div>
                    </div>
                    
                    <div id="purposeDisplay" class="pt-3 border-t border-red-200">
                        <p class="text-xs text-gray-600 mb-1">Purpose</p>
                        <p class="font-semibold text-gray-900" id="successPurpose"></p>
                    </div>
                    
                    <div class="pt-3 border-t border-red-200 grid grid-cols-2 gap-4">
                        <div id="timeInDisplay">
                            <p class="text-xs text-gray-600 mb-1">Time In</p>
                            <p class="font-semibold text-gray-900 text-sm" id="successTimeIn"></p>
                        </div>
                        <div id="timeOutDisplay" class="hidden">
                            <p class="text-xs text-gray-600 mb-1">Time Out</p>
                            <p class="font-semibold text-gray-900 text-sm" id="successTimeOut"></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Warning Message (if user is logged into another lab) -->
            <div id="ongoingSessionWarning" class="hidden mb-6 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg shadow-sm">
                <div class="p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-yellow-800 mb-2">Multiple Laboratory Sessions</h4>
                            <p class="text-sm text-yellow-700" id="warningMessage"></p>
                            <div id="warningDetails" class="mt-3 pt-3 border-t border-yellow-200 text-xs text-yellow-600 space-y-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span><strong>Laboratory:</strong> <span id="warningLab"></span></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Time In:</strong> <span id="warningTimeIn"></span></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span><strong>Purpose:</strong> <span id="warningPurpose"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <button onclick="closeModal()" class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold py-3 px-6 rounded-lg transition">
                Done
            </button>
        </div>
        
        <!-- Modal Content - Error -->
        <div id="modalError" class="p-8 hidden">
            <div class="text-center mb-6">
                <div class="mx-auto w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Error</h3>
                <p class="text-gray-600 mb-4" id="errorMessage"></p>
            </div>
            
            <div class="space-y-3">
                <button onclick="retryRfidScan()" class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold py-3 px-6 rounded-lg transition">
                    Try Again
                </button>
                <button onclick="closeModal()" class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom card checkbox/radio styling */
.purpose-card .purpose-checkbox:checked ~ div,
.lab-card .lab-radio:checked ~ div {
    border-color: #16a34a;
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);
    transform: translateY(-4px);
}

.purpose-card .checkmark-badge,
.lab-card .checkmark-badge {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 28px;
    height: 28px;
    background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    box-shadow: 0 3px 8px rgba(22, 163, 74, 0.4);
    border: 2px solid white;
}

.purpose-card .purpose-checkbox:checked ~ div .checkmark-badge,
.lab-card .lab-radio:checked ~ div .checkmark-badge {
    display: flex;
    animation: checkmarkPop 0.3s ease;
}

@keyframes checkmarkPop {
    0% { transform: scale(0); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
}
</style>

<script>
// Lab session details from server
const labSessionDetails = @json($labSessionDetails ?? []);
const labsWithSessions = @json($labsWithSessions ?? []);

let selectedLaboratory = '';
let selectedPurposes = [];
let lastSelectedLab = null; // Track last selected lab for deselection

// Lab Users Modal Functions
function showLabUsers(labNumber) {
    const sessions = labSessionDetails[labNumber];
    
    if (!sessions || sessions.length === 0) {
        return;
    }
    
    document.getElementById('labUsersTitle').textContent = `Lab ${labNumber} - Current Users`;
    
    const content = sessions.map(session => `
        <div class="bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-xl p-4 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="bg-red-600 text-white w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg flex-shrink-0">
                    ${session.user_name.charAt(0).toUpperCase()}
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-gray-900 mb-1">${session.user_name}</h4>
                    <div class="space-y-1 text-sm text-gray-700">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span><strong>Purpose:</strong> ${session.purpose || 'N/A'}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><strong>Duration:</strong> ${session.duration}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span class="text-xs"><strong>Time In:</strong> ${new Date(session.time_in).toLocaleString()}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
    
    document.getElementById('labUsersContent').innerHTML = content;
    document.getElementById('labUsersModal').classList.remove('hidden');
}

function closeLabUsersModal() {
    document.getElementById('labUsersModal').classList.add('hidden');
}

// Error Message Functions
function showPurposeError() {
    const errorEl = document.getElementById('purposeError');
    errorEl.classList.remove('hidden');
    errorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        hidePurposeError();
    }, 5000);
}

function hidePurposeError() {
    document.getElementById('purposeError').classList.add('hidden');
}

function showLaboratoryError() {
    const errorEl = document.getElementById('laboratoryError');
    errorEl.classList.remove('hidden');
    errorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        hideLaboratoryError();
    }, 5000);
}

function hideLaboratoryError() {
    document.getElementById('laboratoryError').classList.add('hidden');
}

// Logout Notice Functions
function showLogoutNotice() {
    const noticeEl = document.getElementById('logoutNotice');
    noticeEl.classList.remove('hidden');
    
    // Make purpose section visually optional
    const purposeSection = document.getElementById('purposeSection');
    purposeSection.style.opacity = '0.6';
    
    // Hide required asterisk
    const requiredIndicator = document.getElementById('purposeRequired');
    if (requiredIndicator) {
        requiredIndicator.classList.add('hidden');
    }
}

function hideLogoutNotice() {
    document.getElementById('logoutNotice').classList.add('hidden');
    
    // Restore purpose section appearance
    const purposeSection = document.getElementById('purposeSection');
    purposeSection.style.opacity = '1';
    
    // Show required asterisk
    const requiredIndicator = document.getElementById('purposeRequired');
    if (requiredIndicator) {
        requiredIndicator.classList.remove('hidden');
    }
}

// Function definitions (available globally)
function updateSelectedLaboratory() {
    const selectedRadio = document.querySelector('.lab-radio:checked');
    selectedLaboratory = selectedRadio ? selectedRadio.value : '';
}

function updateSelectedPurposes() {
    selectedPurposes = Array.from(document.querySelectorAll('.purpose-checkbox:checked'))
        .map(cb => cb.value);
}

function openModal() {
    updateSelectedLaboratory();
    updateSelectedPurposes();
    
    // Hide any existing errors
    hideLaboratoryError();
    hidePurposeError();
    
    if (!selectedLaboratory) {
        showLaboratoryError();
        return;
    }
    
    // Check if selected lab has an ongoing session (logout operation)
    const isLogout = labsWithSessions.includes(parseInt(selectedLaboratory));
    
    // Only require purpose for check-in operations (not logout)
    if (!isLogout && selectedPurposes.length === 0) {
        showPurposeError();
        return;
    }
    
    // Update modal with selected info
    document.getElementById('modalLaboratory').textContent = 'Lab ' + selectedLaboratory;
    
    if (isLogout) {
        document.getElementById('modalPurpose').textContent = 'Logout';
    } else {
        document.getElementById('modalPurpose').textContent = selectedPurposes.map(p => 
            p.charAt(0).toUpperCase() + p.slice(1)
        ).join(', ');
    }
    
    // Show modal and focus on RFID input
    document.getElementById('rfidModal').classList.remove('hidden');
    document.getElementById('rfidModal').classList.add('flex');
    
    // Reset to ready state
    showModalState('ready');
    
    // Focus on RFID input after a short delay
    setTimeout(() => {
        document.getElementById('rfidInput').focus();
    }, 100);
}

function closeModal() {
    const isSuccess = !document.getElementById('modalSuccess').classList.contains('hidden');
    
    document.getElementById('rfidModal').classList.add('hidden');
    document.getElementById('rfidModal').classList.remove('flex');
    document.getElementById('rfidInput').value = '';
    
    // Reset form and reload page on success to get updated laboratory statuses
    if (isSuccess) {
        document.querySelectorAll('.lab-radio').forEach(radio => radio.checked = false);
        document.querySelectorAll('.purpose-checkbox').forEach(cb => cb.checked = false);
        selectedLaboratory = '';
        selectedPurposes = [];
        lastSelectedLab = null;
        hideLogoutNotice();
        
        // Reload page to refresh laboratory statuses
        location.reload();
    }
}

function showModalState(state) {
    document.getElementById('modalReady').classList.add('hidden');
    document.getElementById('modalLoading').classList.add('hidden');
    document.getElementById('modalSuccess').classList.add('hidden');
    document.getElementById('modalError').classList.add('hidden');
    
    if (state === 'ready') {
        document.getElementById('modalReady').classList.remove('hidden');
    } else if (state === 'loading') {
        document.getElementById('modalLoading').classList.remove('hidden');
    } else if (state === 'success') {
        document.getElementById('modalSuccess').classList.remove('hidden');
    } else if (state === 'error') {
        document.getElementById('modalError').classList.remove('hidden');
    }
}

function retryRfidScan() {
    showModalState('ready');
    setTimeout(() => {
        document.getElementById('rfidInput').focus();
    }, 100);
}

// Initialize after DOM loads
window.addEventListener('DOMContentLoaded', function() {
    // Handle laboratory radio changes with deselect capability
    document.querySelectorAll('.lab-radio').forEach(radio => {
        // Handle click to allow deselection
        radio.addEventListener('click', function(e) {
            if (lastSelectedLab === this.value && this.checked) {
                // If clicking the already selected radio, deselect it
                this.checked = false;
                lastSelectedLab = null;
                updateSelectedLaboratory();
                hideLaboratoryError();
                hideLogoutNotice();
            } else {
                lastSelectedLab = this.value;
            }
        });
        
        // Handle change for normal selection
        radio.addEventListener('change', function() {
            updateSelectedLaboratory();
            hideLaboratoryError();
            
            // Check if selected lab has ongoing session
            if (selectedLaboratory && labsWithSessions.includes(parseInt(selectedLaboratory))) {
                showLogoutNotice();
            } else {
                hideLogoutNotice();
            }
        });
    });

    // Handle purpose checkbox changes (checkboxes naturally allow deselection)
    document.querySelectorAll('.purpose-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedPurposes();
            hidePurposeError();
        });
    });

    // Handle RFID input
    document.getElementById('rfidInput').addEventListener('keypress', async function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const rfidNumber = this.value.trim();
            
            if (!rfidNumber) {
                return;
            }
            
            // Show loading state
            showModalState('loading');
            
            try {
                const response = await fetch('/lab-schedule/rfid-attendance', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').content || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        rfid_number: rfidNumber,
                        laboratory: selectedLaboratory,
                        purposes: selectedPurposes
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Show success state
                    showModalState('success');
                    
                    // Populate success info
                    document.getElementById('successMessage').textContent = data.message;
                    document.getElementById('userName').textContent = data.faculty_name;
                    document.getElementById('userInitial').textContent = data.faculty_name.charAt(0).toUpperCase();
                    document.getElementById('successLab').textContent = 'Lab ' + selectedLaboratory;
                    
                    const isLogout = data.status === 'completed';
                    document.getElementById('successStatus').textContent = isLogout ? 'Logged Out' : 'Logged In';
                    document.getElementById('successStatus').className = isLogout ? 'font-semibold text-red-600' : 'font-semibold text-green-600';
                    
                    // Show/hide purpose based on action
                    if (isLogout) {
                        document.getElementById('purposeDisplay').classList.add('hidden');
                    } else {
                        document.getElementById('purposeDisplay').classList.remove('hidden');
                        document.getElementById('successPurpose').textContent = selectedPurposes.map(p => 
                            p.charAt(0).toUpperCase() + p.slice(1)
                        ).join(', ');
                    }
                    
                    // Format and show times
                    if (data.time_in) {
                        document.getElementById('timeInDisplay').classList.remove('hidden');
                        document.getElementById('successTimeIn').textContent = new Date(data.time_in).toLocaleString();
                    }
                    
                    // Handle ongoing session warning
                    if (data.warning && data.warning.has_ongoing_session) {
                        const warningEl = document.getElementById('ongoingSessionWarning');
                        warningEl.classList.remove('hidden');
                        
                        document.getElementById('warningMessage').textContent = data.warning.message;
                        document.getElementById('warningLab').textContent = 'Lab ' + data.warning.ongoing_laboratory;
                        document.getElementById('warningTimeIn').textContent = new Date(data.warning.ongoing_time_in).toLocaleString();
                        document.getElementById('warningPurpose').textContent = data.warning.ongoing_purpose || 'N/A';
                    } else {
                        document.getElementById('ongoingSessionWarning').classList.add('hidden');
                    }
                    
                    if (data.time_out) {
                        document.getElementById('timeOutDisplay').classList.remove('hidden');
                        document.getElementById('successTimeOut').textContent = new Date(data.time_out).toLocaleString();
                    } else {
                        document.getElementById('timeOutDisplay').classList.add('hidden');
                    }
                } else {
                    // Show error state
                    showModalState('error');
                    document.getElementById('errorMessage').textContent = data.message;
                }
            } catch (error) {
                console.error('Error:', error);
                showModalState('error');
                document.getElementById('errorMessage').textContent = 'Connection error. Please try again.';
            }
            
            // Clear RFID input
            this.value = '';
        }
    });
});
</script>
@endsection
