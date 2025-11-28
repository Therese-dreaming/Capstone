@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 transition-all duration-300" id="mainContent">
    <div class="@auth max-w-4xl @else max-w-full @endauth mx-auto">
        <!-- Header Section -->
        <div class="mb-6 md:mb-8">
            <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                        <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2-2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Lab Login</h1>
                        <p class="text-red-100 text-sm md:text-lg">RFID-based laboratory attendance system</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- RFID Attendance Section -->
        <div class="mb-8 p-4 md:p-6 bg-white rounded-xl shadow-lg transform transition-all duration-300 hover:shadow-xl">
            <div class="flex flex-col sm:flex-row items-center mb-6 gap-2 sm:gap-4">
                <div class="bg-red-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800">RFID Attendance</h2>
                    <p class="text-gray-600 text-sm md:text-base">Select a laboratory and scan your RFID card</p>
                </div>
            </div>

            <!-- Laboratory Selection -->
            <div class="mb-6">
                <!-- Purpose Selection -->
                <div class="mb-6" id="purposeSection">
                    <label class="block text-sm font-medium text-gray-700 mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                        <div class="flex items-center">
                            <div class="bg-red-100 p-2 rounded-lg mr-3">
                                <svg class="w-4 h-4 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <span class="font-semibold">Purpose of Usage</span>
                        </div>
                        <span id="selectedPurposeText" class="text-sm font-medium text-red-600 bg-red-50 px-3 py-1 rounded-full">Selected: None</span>
                    </label>

                    <!-- Multi-selection controls for purpose -->
                    <div class="mb-4 flex flex-wrap gap-2 items-center">
                        <button onclick="selectAllPurposes()" class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded-full hover:bg-blue-200 transition-colors">
                            Select All
                        </button>
                        <button onclick="clearAllPurposes()" class="px-3 py-1 text-xs bg-gray-100 text-gray-800 rounded-full hover:bg-gray-200 transition-colors">
                            Clear All
                        </button>
                        <div id="selectedPurposesCount" class="text-xs text-gray-600 ml-2">
                            0 purposes selected
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                        <div class="purpose-card cursor-pointer transform transition-all duration-300 hover:scale-105 h-full" data-value="lecture" onclick="togglePurpose(this)">
                            <div class="relative bg-white rounded-xl border-2 border-gray-200 hover:border-red-500 shadow-md p-4 group transition-all duration-300 h-full flex flex-col">
                                <div class="absolute inset-0 bg-red-50 opacity-0 transition-opacity duration-300 rounded-xl"></div>
                                <div class="absolute top-3 right-3 transition-all duration-300 opacity-0 group-hover:opacity-100 active-checkmark">
                                    <div class="bg-red-600 p-1 rounded-full">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-lg font-bold text-gray-800 mb-2">Lecture</div>
                                <div class="text-sm text-gray-500 flex-grow">Regular classroom instruction</div>
                            </div>
                        </div>

                        <div class="purpose-card cursor-pointer transform transition-all duration-300 hover:scale-105 h-full" data-value="examination" onclick="togglePurpose(this)">
                            <div class="relative bg-white rounded-xl border-2 border-gray-200 hover:border-red-500 shadow-md p-4 group transition-all duration-300 h-full flex flex-col">
                                <div class="absolute inset-0 bg-red-50 opacity-0 transition-opacity duration-300 rounded-xl"></div>
                                <div class="absolute top-3 right-3 transition-all duration-300 opacity-0 group-hover:opacity-100 active-checkmark">
                                    <div class="bg-red-600 p-1 rounded-full">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-lg font-bold text-gray-800 mb-2">Examination</div>
                                <div class="text-sm text-gray-500 flex-grow">Tests and assessments</div>
                            </div>
                        </div>

                        <div class="purpose-card cursor-pointer transform transition-all duration-300 hover:scale-105 h-full" data-value="practical" onclick="togglePurpose(this)">
                            <div class="relative bg-white rounded-xl border-2 border-gray-200 hover:border-red-500 shadow-md p-4 group transition-all duration-300 h-full flex flex-col">
                                <div class="absolute inset-0 bg-red-50 opacity-0 transition-opacity duration-300 rounded-xl"></div>
                                <div class="absolute top-3 right-3 transition-all duration-300 opacity-0 group-hover:opacity-100 active-checkmark">
                                    <div class="bg-red-600 p-1 rounded-full">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-lg font-bold text-gray-800 mb-2">Practical</div>
                                <div class="text-sm text-gray-500 flex-grow">Hands-on laboratory work</div>
                            </div>
                        </div>

                        <div class="purpose-card cursor-pointer transform transition-all duration-300 hover:scale-105 h-full" data-value="research" onclick="togglePurpose(this)">
                            <div class="relative bg-white rounded-xl border-2 border-gray-200 hover:border-red-500 shadow-md p-4 group transition-all duration-300 h-full flex flex-col">
                                <div class="absolute inset-0 bg-red-50 opacity-0 transition-opacity duration-300 rounded-xl"></div>
                                <div class="absolute top-3 right-3 transition-all duration-300 opacity-0 group-hover:opacity-100 active-checkmark">
                                    <div class="bg-red-600 p-1 rounded-full">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-lg font-bold text-gray-800 mb-2">Research</div>
                                <div class="text-sm text-gray-500 flex-grow">Academic research activities</div>
                            </div>
                        </div>

                        <div class="purpose-card cursor-pointer transform transition-all duration-300 hover:scale-105 h-full" data-value="training" onclick="togglePurpose(this)">
                            <div class="relative bg-white rounded-xl border-2 border-gray-200 hover:border-red-500 shadow-md p-4 group transition-all duration-300 h-full flex flex-col">
                                <div class="absolute inset-0 bg-red-50 opacity-0 transition-opacity duration-300 rounded-xl"></div>
                                <div class="absolute top-3 right-3 transition-all duration-300 opacity-0 group-hover:opacity-100 active-checkmark">
                                    <div class="bg-red-600 p-1 rounded-full">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-lg font-bold text-gray-800 mb-2">Training</div>
                                <div class="text-sm text-gray-500 flex-grow">Workshops and skill development</div>
                            </div>
                        </div>

                        <div class="purpose-card cursor-pointer transform transition-all duration-300 hover:scale-105 h-full" data-value="other" onclick="togglePurpose(this)">
                            <div class="relative bg-white rounded-xl border-2 border-gray-200 hover:border-red-500 shadow-md p-4 group transition-all duration-300 h-full flex flex-col">
                                <div class="absolute inset-0 bg-red-50 opacity-0 transition-opacity duration-300 rounded-xl"></div>
                                <div class="absolute top-3 right-3 transition-all duration-300 opacity-0 group-hover:opacity-100 active-checkmark">
                                    <div class="bg-red-600 p-1 rounded-full">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-lg font-bold text-gray-800 mb-2">Other</div>
                                <div class="text-sm text-gray-500 flex-grow">Please specify below</div>
                            </div>
                        </div>
                    </div>

                    <!-- Other Purpose Input (Hidden by default) -->
                    <div id="otherPurposeInput" class="hidden mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Please specify the purpose:</label>
                        <input type="text" id="otherPurposeText" placeholder="Enter specific purpose..." 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                    </div>
                </div>

                <label class="block text-sm font-medium text-gray-700 mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                    <div class="flex items-center">
                        <div class="bg-red-100 p-2 rounded-lg mr-3">
                            <svg class="w-4 h-4 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <span class="font-semibold">Select Laboratory</span>
                    </div>
                    <span id="selectedLabText" class="text-sm font-medium text-red-600 bg-red-50 px-3 py-1 rounded-full">Selected: None</span>
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 items-stretch" id="labCards">
                    @forelse($laboratories as $lab)
                    <div class="lab-card cursor-pointer transform transition-all duration-300 hover:scale-105 h-full" data-value="{{ $lab->number }}" onclick="selectLab(this)">
                        <div class="relative bg-white rounded-xl border-2 border-gray-200 hover:border-green-500 shadow-md p-4 group transition-all duration-300 h-full flex flex-col">
                            <!-- Active State Indicator -->
                            <div class="absolute inset-0 bg-red-50 opacity-0 transition-opacity duration-300 rounded-xl"></div>

                            <!-- Checkmark Icon (Visible when active) -->
                            <div class="absolute top-3 right-3 transition-all duration-300 opacity-0 group-hover:opacity-100 active-checkmark">
                                <div class="bg-green-600 p-1 rounded-full">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Lab Number / Name -->
                            <div class="text-xl sm:text-2xl font-bold text-gray-800 mb-1 relative">Laboratory {{ $lab->number }}</div>
                            @if($lab->building || $lab->floor || $lab->room_number)
                                <div class="text-sm text-gray-500 mb-2">
                                    {{ $lab->building ? $lab->building . ' • ' : '' }}
                                    {{ $lab->floor ? 'Floor ' . $lab->floor . ' • ' : '' }}
                                    {{ $lab->room_number ?? '' }}
                                </div>
                            @endif

                            <!-- Status Indicator -->
                            <div class="flex items-center gap-2 text-sm text-gray-500 relative">
                                <div class="flex items-center">
                                    <span class="status-icon mr-2">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </span>
                                    <span class="status-text font-medium">Available</span>
                                </div>
                            </div>

                            <!-- Current User Information -->
                            <div class="current-user-info mt-3 pt-3 border-t" style="min-height: 60px; visibility: hidden; border-color: transparent;">
                                <div class="text-xs text-gray-600 mb-1">
                                    <span class="font-semibold">Currently in use by:</span>
                                </div>
                                <div class="text-sm font-semibold text-gray-800 current-user-name">-</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span class="font-medium">Time In:</span> <span class="current-user-time-in">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2-2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="font-medium">No laboratories found</p>
                        <p class="text-sm">Please add laboratories in the system settings</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Attendance Information Display -->
            <div id="attendanceInfo" class="hidden mt-6 border border-gray-200 rounded-xl p-4 md:p-6 transform transition-all duration-300 opacity-0 bg-gray-50 shadow-lg">
                <div class="mb-6 pb-4 border-b border-gray-200 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4">
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-lg md:text-xl font-bold text-gray-800">Attendance Details</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Faculty Information -->
                    <div class="p-4 md:p-5 bg-white rounded-xl transition-all duration-300 hover:bg-gray-50 border border-gray-200 shadow-sm hover:shadow">
                        <div class="flex items-center mb-3">
                            <div class="p-2 bg-red-100 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Faculty Name</p>
                        </div>
                        <p id="facultyName" class="text-base md:text-lg font-bold mt-1 text-gray-900 pl-0 sm:pl-12">-</p>
                    </div>

                    <!-- Date -->
                    <div class="p-4 md:p-5 bg-white rounded-xl transition-all duration-300 hover:bg-gray-50 border border-gray-200 shadow-sm hover:shadow">
                        <div class="flex items-center mb-3">
                            <div class="p-2 bg-red-100 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Date</p>
                        </div>
                        <p id="currentDate" class="text-base md:text-lg font-bold mt-1 text-gray-900 pl-0 sm:pl-12">-</p>
                    </div>

                    <!-- Time In -->
                    <div class="p-4 md:p-5 bg-white rounded-xl transition-all duration-300 hover:bg-gray-50 border border-gray-200 shadow-sm hover:shadow">
                        <div class="flex items-center mb-3">
                            <div class="p-2 bg-green-100 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Time In</p>
                        </div>
                        <p id="timeIn" class="text-base md:text-lg font-bold mt-1 text-green-600 pl-0 sm:pl-12">-</p>
                    </div>

                    <!-- Time Out -->
                    <div class="p-4 md:p-5 bg-white rounded-xl transition-all duration-300 hover:bg-gray-50 border border-gray-200 shadow-sm hover:shadow">
                        <div class="flex items-center mb-3">
                            <div class="p-2 bg-red-100 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Time Out</p>
                        </div>
                        <p id="timeOut" class="text-base md:text-lg font-bold mt-1 text-red-600 pl-0 sm:pl-12">-</p>
                    </div>

                    <!-- Status Section with Color Legend -->
                    <div class="col-span-1 md:col-span-2 p-4 md:p-5 bg-white rounded-xl transition-all duration-300 hover:bg-gray-50 border border-gray-200 shadow-sm hover:shadow">
                        <div class="flex items-center mb-3">
                            <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Status</p>
                        </div>
                        <p id="logStatus" class="text-base md:text-lg font-bold mt-1 pl-0 sm:pl-12">-</p>

                        <!-- Status Legend -->
                        <div class="mt-4 flex flex-col sm:flex-row gap-2 sm:gap-6 text-sm pl-0 sm:pl-12">
                            <div class="flex items-center px-3 py-2 bg-red-100 rounded-lg">
                                <span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                                <span class="text-gray-700 font-medium">On-going</span>
                            </div>
                            <div class="flex items-center px-3 py-2 bg-green-100 rounded-lg">
                                <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                                <span class="text-gray-700 font-medium">Completed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Indicator -->
            <div id="statusIndicator" class="hidden mt-4 p-4 rounded-xl text-center transform transition-all duration-300 scale-95 opacity-0"></div>
        </div>

        <!-- Instructions Card -->
        <div class="bg-white rounded-xl shadow-lg p-4 md:p-8 transform transition-all duration-300 hover:shadow-xl">
            <div class="flex flex-col sm:flex-row items-center mb-4 gap-2 sm:gap-4">
                <div class="bg-red-100 p-3 rounded-full">
                    <svg class="w-5 h-5 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg md:text-xl font-bold text-gray-800">Instructions</h3>
            </div>
            <ul class="list-none space-y-3 text-gray-600">
                <li class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="bg-green-100 p-2 rounded-full mr-3">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="font-medium">Select the purpose of your laboratory usage</span>
                </li>
                <li class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="bg-green-100 p-2 rounded-full mr-3">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="font-medium">Select the laboratory where you will conduct your class</span>
                </li>
                <li class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="bg-green-100 p-2 rounded-full mr-3">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="font-medium">Tap your RFID card when entering the laboratory (Time In)</span>
                </li>
                <li class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="bg-green-100 p-2 rounded-full mr-3">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="font-medium">Tap your RFID card again when leaving the laboratory (Time Out)</span>
                </li>
                <li class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="bg-green-100 p-2 rounded-full mr-3">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="font-medium">Ensure your card is properly scanned and wait for the confirmation</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Tap Card Modal -->
<div id="tapCardModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50">
    <div class="relative bg-white rounded-xl shadow-xl mx-auto p-8 max-w-md w-full transform transition-all duration-300 scale-95">
        <!-- Exit Button -->
        <button id="tapCloseBtn" onclick="closeTapCardModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <h3 id="tapModalTitle" class="text-xl font-bold text-gray-900 mb-3">Ready to Scan</h3>
            <p id="tapModalText" class="text-gray-600 mb-6">Please tap your RFID card to record your attendance.</p>
            <!-- Waiting animation -->
            <div id="tapWaiting" class="animate-pulse flex justify-center">
                <div class="h-3 w-3 bg-red-600 rounded-full mx-1"></div>
                <div class="h-3 w-3 bg-red-600 rounded-full mx-1 animation-delay-200"></div>
                <div class="h-3 w-3 bg-red-600 rounded-full mx-1 animation-delay-400"></div>
            </div>
            <!-- Loading (Verifying) state -->
            <div id="tapLoading" class="hidden mt-4 flex items-center justify-center">
                <div class="h-6 w-6 border-2 border-red-600 border-t-transparent rounded-full animate-spin"></div>
                <span class="ml-3 text-sm text-gray-700">Verifying your RFID...</span>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    let selectedLab = null;
    let selectedPurposes = [];
    let isProcessing = false;

    // Function to toggle purpose selection (multi-select)
    function togglePurpose(element) {
        const purposeValue = element.dataset.value;
        
        // Check if purpose is already selected
        const isSelected = selectedPurposes.includes(purposeValue);
        
        if (isSelected) {
            // Remove from selection
            selectedPurposes = selectedPurposes.filter(purpose => purpose !== purposeValue);
            element.querySelector('.absolute').classList.remove('opacity-50');
            element.querySelector('.active-checkmark').classList.remove('opacity-100');
            element.querySelector('.border-2').classList.remove('border-red-500');
        } else {
            // Add to selection
            selectedPurposes.push(purposeValue);
            element.querySelector('.absolute').classList.add('opacity-50');
            element.querySelector('.active-checkmark').classList.add('opacity-100');
            element.querySelector('.border-2').classList.add('border-red-500');
        }
        
        updateSelectedPurposesDisplay();
        
        // Show/hide other purpose input if "other" is selected
        const otherPurposeInput = document.getElementById('otherPurposeInput');
        if (selectedPurposes.includes('other')) {
            otherPurposeInput.classList.remove('hidden');
        } else {
            otherPurposeInput.classList.add('hidden');
            document.getElementById('otherPurposeText').value = '';
        }
    }

    // Function to select all purposes
    function selectAllPurposes() {
        document.querySelectorAll('.purpose-card').forEach(card => {
            const purposeValue = card.dataset.value;
            if (!selectedPurposes.includes(purposeValue)) {
                selectedPurposes.push(purposeValue);
                card.querySelector('.absolute').classList.add('opacity-50');
                card.querySelector('.active-checkmark').classList.add('opacity-100');
                card.querySelector('.border-2').classList.add('border-red-500');
            }
        });
        updateSelectedPurposesDisplay();
        
        // Show other input if "other" is selected
        if (selectedPurposes.includes('other')) {
            document.getElementById('otherPurposeInput').classList.remove('hidden');
        }
    }

    // Function to clear all purpose selections
    function clearAllPurposes() {
        selectedPurposes = [];
        document.querySelectorAll('.purpose-card').forEach(card => {
            card.querySelector('.absolute').classList.remove('opacity-50');
            card.querySelector('.active-checkmark').classList.remove('opacity-100');
            card.querySelector('.border-2').classList.remove('border-red-500');
        });
        updateSelectedPurposesDisplay();
        
        // Hide other input
        document.getElementById('otherPurposeInput').classList.add('hidden');
        document.getElementById('otherPurposeText').value = '';
    }

    // Function to update the selected purposes display
    function updateSelectedPurposesDisplay() {
        const selectedPurposeTextEl = document.getElementById('selectedPurposeText');
        const selectedPurposesCountEl = document.getElementById('selectedPurposesCount');
        
        if (selectedPurposes.length === 0) {
            selectedPurposeTextEl.textContent = 'Selected: None';
            selectedPurposeTextEl.className = 'text-sm font-medium text-red-600 bg-red-50 px-3 py-1 rounded-full';
        } else if (selectedPurposes.length === 1) {
            const purposeText = document.querySelector(`[data-value="${selectedPurposes[0]}"] .text-lg`).textContent;
            selectedPurposeTextEl.textContent = `Selected: ${purposeText}`;
            selectedPurposeTextEl.className = 'text-sm font-medium text-green-700 bg-green-50 px-3 py-1 rounded-full';
        } else {
            selectedPurposeTextEl.textContent = `Selected: ${selectedPurposes.length} purposes`;
            selectedPurposeTextEl.className = 'text-sm font-medium text-green-700 bg-green-50 px-3 py-1 rounded-full';
        }
        
        selectedPurposesCountEl.textContent = `${selectedPurposes.length} purpose${selectedPurposes.length !== 1 ? 's' : ''} selected`;
    }

    // Function to handle lab selection (single select)
    async function selectLab(element) {
        // Check if user has an ongoing lab session
        const hasOngoingSession = await checkUserOngoingSession();
        if (hasOngoingSession) {
            showStatus('error', 'You have an ongoing lab session. Please complete it before selecting another laboratory.');
            return;
        }

        // Reset purpose section visual state on each lab selection
        const purposeSection = document.getElementById('purposeSection');
        if (purposeSection) purposeSection.classList.remove('opacity-50');

        const labNumber = element.dataset.value;
        const availability = await getLabAvailabilityDetail(labNumber);
        let canOpenModal = true;

        // If lab is available, proceed with purpose requirement (tap-in)
        if (availability.status !== 'ongoing') {
            if (selectedPurposes.length === 0) {
                showStatus('error', 'Please select at least one purpose first before choosing a laboratory');
                canOpenModal = false;
            } else if (selectedPurposes.includes('other') && !document.getElementById('otherPurposeText').value.trim()) {
                showStatus('error', 'Please specify the purpose before proceeding');
                canOpenModal = false;
            }
        } else {
            // Lab is unavailable: allow tap-out attempt without purpose
            // Hide purpose section visually
            if (purposeSection) purposeSection.classList.add('opacity-50');
        }

        // Remove active state from all cards
        document.querySelectorAll('.lab-card').forEach(card => {
            card.querySelector('.absolute').classList.remove('opacity-50');
            card.querySelector('.active-checkmark').classList.remove('opacity-100');
            const borderEl = card.querySelector('.border-2');
            borderEl.classList.remove('border-red-500');
            borderEl.classList.remove('border-green-500');
        });

        // Add active state to selected card
        element.querySelector('.absolute').classList.add('opacity-50');
        element.querySelector('.active-checkmark').classList.add('opacity-100');
        element.querySelector('.border-2').classList.add('border-green-500');

        // Update selected lab
        selectedLab = element.dataset.value;
        const selectedLabTextEl = document.getElementById('selectedLabText');
        selectedLabTextEl.textContent = `Selected: ${selectedLab}`;
        selectedLabTextEl.className = 'text-sm font-medium text-green-700 bg-green-50 px-3 py-1 rounded-full';

        // Show tap card modal only if allowed
        if (canOpenModal) {
            const modalEl = document.getElementById('tapCardModal');
            modalEl.classList.remove('hidden');
            modalEl.style.display = 'flex';
            // Ensure modal starts in non-loading state
            if (typeof setTapModalLoading === 'function') {
                setTapModalLoading(false);
            }
            startRFIDListener();
        }
    }

    // Function to close tap card modal and reset selection
    function closeTapCardModal(force = false) {
        if (isProcessing && !force) return; // Prevent closing while verifying unless forced
        const modalEl = document.getElementById('tapCardModal');
        if (modalEl) {
            modalEl.classList.add('hidden');
            modalEl.style.display = 'none';
        }
        stopRFIDListener();
        // Reset modal visual state
        if (typeof setTapModalLoading === 'function') {
            setTapModalLoading(false);
        }
        
        // Reset lab selection
        resetLabSelection();
    }

    // Function to reset lab selection
    function resetLabSelection() {
        // Remove active state from all cards
        document.querySelectorAll('.lab-card').forEach(card => {
            card.querySelector('.absolute').classList.remove('opacity-50');
            card.querySelector('.active-checkmark').classList.remove('opacity-100');
            card.querySelector('.border-2').classList.remove('border-red-500');
        });

        // Reset selected lab
        selectedLab = null;
        document.getElementById('selectedLabText').textContent = 'Selected: None';
        document.getElementById('selectedLabText').className = 'text-sm font-medium text-red-600 bg-red-50 px-3 py-1 rounded-full';
    }

    // Function to reset purpose selection
    function resetPurposeSelection() {
        clearAllPurposes();
    }

    // Function to start RFID listener
    function startRFIDListener() {
        if (window.RFIDListener) return; // Prevent multiple listeners

        let rfidNumber = '';
        let lastKeyTime = Date.now();
        let collectingRFID = false;

        // Keep reference to the inner collector so we can remove it on stop
        window.RFIDCollectHandler = function collectKeys(e) {
            const currentTime = Date.now();
            if (currentTime - lastKeyTime > 100) { // Reset if too much time between keys
                rfidNumber = e.key;
            } else {
                rfidNumber += e.key;
            }
            lastKeyTime = currentTime;

            // Check if we have a complete RFID number (usually 10 digits)
            if (rfidNumber.length >= 10) {
                // Switch modal to verifying state immediately
                if (typeof setTapModalLoading === 'function') {
                    setTapModalLoading(true);
                }
                document.removeEventListener('keypress', window.RFIDCollectHandler);
                handleRFIDScan(rfidNumber);
                collectingRFID = false;
            }
        };

        window.RFIDListener = function handleFirstKey(e) {
            if (isProcessing) return;
            if (!collectingRFID) {
                collectingRFID = true;
                rfidNumber = e.key;
                lastKeyTime = Date.now();
                document.addEventListener('keypress', window.RFIDCollectHandler);
            }
        };

        document.addEventListener('keypress', window.RFIDListener);
    }

    // Function to stop RFID listener
    function stopRFIDListener() {
        if (window.RFIDCollectHandler) {
            document.removeEventListener('keypress', window.RFIDCollectHandler);
            window.RFIDCollectHandler = null;
        }
        if (window.RFIDListener) {
            document.removeEventListener('keypress', window.RFIDListener);
            window.RFIDListener = null;
        }
    }

    // Function to handle RFID scan
    async function handleRFIDScan(rfidNumber) {
        // Only require a lab selection; purposes may be omitted for tap-out when lab is ongoing
        if (!selectedLab || isProcessing) return;
        isProcessing = true;

        // Determine if lab is currently ongoing to treat as tap-out (skip purpose)
        const availability = await getLabAvailabilityDetail(selectedLab);
        let purposesText = null;
        if (availability.status !== 'ongoing') {
            if (selectedPurposes.length > 0) {
                // Handle multiple purposes
                purposesText = selectedPurposes.map(purpose => {
                    if (purpose === 'other') {
                        const otherText = document.getElementById('otherPurposeText').value.trim();
                        if (!otherText) {
                            showStatus('error', 'Please specify the purpose before proceeding');
                            isProcessing = false;
                            if (typeof setTapModalLoading === 'function') {
                                setTapModalLoading(false);
                            }
                            return null;
                        }
                        return otherText;
                    }
                    return purpose;
                }).filter(p => p !== null);
                
                if (purposesText.length === 0) return;
            }
        }

        try {
            if (typeof setTapModalLoading === 'function') {
                setTapModalLoading(true);
            }
            const response = await fetch('/lab-schedule/rfid-attendance', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    rfid_number: rfidNumber,
                    laboratory: selectedLab,
                    ...(purposesText ? { purposes: purposesText } : {})
                })
            });

            // Attempt to parse JSON regardless of status; fall back to text
            const ct = response.headers.get('content-type') || '';
            let data;
            if (ct.includes('application/json')) {
                data = await response.json();
            } else {
                const text = await response.text();
                data = { success: false, message: text || 'Unexpected response from server' };
            }

            if (response.ok && data.success) {
                // Update attendance information
                document.getElementById('facultyName').textContent = data.faculty_name;
                document.getElementById('currentDate').textContent = new Date().toLocaleDateString();

                // Format time in
                const timeInDate = data.time_in ? new Date(data.time_in) : null;
                document.getElementById('timeIn').textContent = timeInDate ? 
                    timeInDate.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : '-';

                // Format time out
                const timeOutDate = data.time_out ? new Date(data.time_out) : null;
                document.getElementById('timeOut').textContent = timeOutDate ? 
                    timeOutDate.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : '-';

                const logStatus = document.getElementById('logStatus');
                logStatus.textContent = data.status;
                // Update status color based on status
                if (data.status === 'on-going') {
                    logStatus.className = 'text-lg font-semibold mt-1 text-red-600 pl-0 sm:pl-12';
                } else if (data.status === 'completed') {
                    logStatus.className = 'text-lg font-semibold mt-1 text-green-600 pl-0 sm:pl-12';
                }

                // Show attendance info
                const attendanceInfo = document.getElementById('attendanceInfo');
                attendanceInfo.classList.remove('hidden', 'opacity-0');

                // Check for ongoing session warning
                if (data.warning && data.warning.has_ongoing_session) {
                    // Show warning message about ongoing session
                    showOngoingSessionWarning(data.warning);
                    showStatus('success', data.message);
                } else {
                    // Show normal success message
                    showStatus('success', data.message);
                }
                
                closeTapCardModal(true);
                stopRFIDListener();
            } else {
                // Show error message first, then close modal after a delay
                const statusMsg = (data && data.message) ? data.message :
                    (!response.ok ? `Request failed (${response.status})` : 'Failed to process RFID');
                showStatus('error', statusMsg);
                stopRFIDListener();
                
                // Close modal after 2 seconds to allow user to see the error message
                setTimeout(() => {
                    closeTapCardModal(true);
                }, 2000);
            }
        } catch (error) {
            // Show error message first, then close modal after a delay
            showStatus('error', 'Error processing RFID scan');
            console.error('Error:', error);
            stopRFIDListener();
            
            // Close modal after 2 seconds to allow user to see the error message
            setTimeout(() => {
                closeTapCardModal(true);
            }, 2000);
        } finally {
            isProcessing = false;
        }
    }

    // Function to show status message
    function showStatus(type, message) {
        const statusIndicator = document.getElementById('statusIndicator');
        statusIndicator.className = `mt-4 p-4 rounded-xl text-center transform transition-all duration-300 ${type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'}`;
        statusIndicator.textContent = message;
        statusIndicator.classList.remove('hidden', 'opacity-0', 'scale-95');

        // For error messages, show them longer (5 seconds) to ensure visibility
        const hideDelay = type === 'error' ? 5000 : 3000;
        
        // Hide status after specified delay
        setTimeout(() => {
            statusIndicator.classList.add('opacity-0', 'scale-95');
            setTimeout(() => statusIndicator.classList.add('hidden'), 300);
        }, hideDelay);
    }

    // Clean up event listeners when the page is unloaded
    window.addEventListener('unload', () => {
        stopRFIDListener();
    });

    // Toggle loading state in the Tap modal
    function setTapModalLoading(loading) {
        const closeBtn = document.getElementById('tapCloseBtn');
        const waiting = document.getElementById('tapWaiting');
        const loadingDiv = document.getElementById('tapLoading');
        const title = document.getElementById('tapModalTitle');
        const text = document.getElementById('tapModalText');

        if (loading) {
            if (closeBtn) {
                closeBtn.setAttribute('disabled', '');
                closeBtn.classList.add('opacity-50', 'pointer-events-none');
            }
            if (waiting) waiting.classList.add('hidden');
            if (loadingDiv) loadingDiv.classList.remove('hidden');
            if (title) title.textContent = 'Verifying';
            if (text) text.textContent = 'Please wait while we verify your RFID...';
        } else {
            if (closeBtn) {
                closeBtn.removeAttribute('disabled');
                closeBtn.classList.remove('opacity-50', 'pointer-events-none');
            }
            if (waiting) waiting.classList.remove('hidden');
            if (loadingDiv) loadingDiv.classList.add('hidden');
            if (title) title.textContent = 'Ready to Scan';
            if (text) text.textContent = 'Please tap your RFID card to record your attendance.';
        }
    }

    // Function to check if user has an ongoing lab session
    async function checkUserOngoingSession() {
        try {
            const response = await fetch('/lab-schedule/check-user-ongoing-session', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });
            // If the route does not exist (404) or any non-OK status, treat as no ongoing session
            if (!response.ok) {
                return false;
            }
            const data = await response.json().catch(() => ({ hasOngoingSession: false }));
            return Boolean(data.hasOngoingSession);
        } catch (error) {
            // Fail-safe: do not block UI if the endpoint is unavailable
            return false;
        }
    }

    // Function to check specific lab availability
    async function checkSpecificLabAvailability(labNumber) {
        try {
            const response = await fetch(`/lab-schedule/check-availability/${labNumber}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) return true; // assume available on error
            const ct = response.headers.get('content-type') || '';
            if (!ct.includes('application/json')) return true;
            const data = await response.json();
            return (data && data.status) ? data.status !== 'ongoing' : true;
        } catch (error) {
            console.error('Error checking specific lab availability:', error);
            return true; // Default to available if error occurs
        }
    }

            // Simplified lab availability check - always shows available
    async function checkLabAvailability(labNumber) {
        // With improved system, all labs show as available
        // Warning modals handle ongoing session logic
        const statusIconWrapper = document.querySelector(`[data-value="${labNumber}"] .status-icon`);
        const statusText = document.querySelector(`[data-value="${labNumber}"] .status-text`);
        const container = document.querySelector(`[data-value="${labNumber}"] .relative`);

        if (!statusIconWrapper) return;

        // Always show as available
        statusIconWrapper.innerHTML = `
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>`;
        statusText.textContent = 'Available';
        if (container) {
            container.classList.add('bg-white', 'border-gray-200');
            container.classList.remove('bg-red-100', 'border-red-600', 'opacity-80', 'bg-orange-50', 'border-orange-300');
        }
    }

    // Helper to get current lab availability
    async function getLabAvailabilityDetail(labNumber) {
        try {
            const response = await fetch(`/lab-schedule/check-availability/${labNumber}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) return { status: 'available' };
            const ct = response.headers.get('content-type') || '';
            if (!ct.includes('application/json')) return { status: 'available' };
            const data = await response.json();
            return data && data.status ? data : { status: 'available' };
        } catch (e) {
            return { status: 'available' };
        }
    }

    // Function to update lab card with current user info when lab is selected
    async function updateLabCardWithUserInfo(labNumber) {
        const availability = await getLabAvailabilityDetail(labNumber);
        if (availability.status === 'ongoing' && availability.current_user) {
            const labCard = document.querySelector(`[data-value="${labNumber}"]`);
            if (labCard) {
                const currentUserInfo = labCard.querySelector('.current-user-info');
                const currentUserName = labCard.querySelector('.current-user-name');
                const currentUserTimeIn = labCard.querySelector('.current-user-time-in');
                
                if (currentUserInfo && currentUserName && currentUserTimeIn) {
                    currentUserInfo.style.visibility = 'visible';
                    currentUserInfo.style.borderColor = '#e5e7eb'; // border-gray-200
                    currentUserName.textContent = availability.current_user.name || '-';
                    if (availability.current_user.time_in) {
                        const timeInDate = new Date(availability.current_user.time_in);
                        currentUserTimeIn.textContent = timeInDate.toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }
                }
            }
        }
    }

    // Removed automatic lab availability checking on page load
    // Labs now show as "Available" by default in HTML template
    // Warning modals handle ongoing session logic

    function updateLabStatus(labNumber, status, sessionDate = null, currentUser = null) {
        const labCard = document.querySelector(`[data-value="${labNumber}"]`);
        if (!labCard) return;

        const statusIconWrapper = labCard.querySelector('.status-icon');
        const statusText = labCard.querySelector('.status-text');
        const container = labCard.querySelector('.relative');
        const currentUserInfo = labCard.querySelector('.current-user-info');
        const currentUserName = labCard.querySelector('.current-user-name');
        const currentUserTimeIn = labCard.querySelector('.current-user-time-in');

        if (!statusIconWrapper) return;

        // Check if ongoing session is from today (use local date, not UTC)
        const now = new Date();
        const today = now.getFullYear() + '-' + 
                     String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                     String(now.getDate()).padStart(2, '0');
        const isFromToday = sessionDate === today;
        
        // Debug logging
        console.log(`Lab ${labNumber}: status=${status}, sessionDate=${sessionDate}, today=${today}, isFromToday=${isFromToday}`);

        if (status === 'on-going' && isFromToday) {
            // Unavailable if ongoing session is from today
            statusIconWrapper.innerHTML = `
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            `;
            statusText.textContent = 'Unavailable';
            if (container) {
                container.classList.add('bg-red-100', 'border-red-600', 'opacity-80');
                container.classList.remove('bg-white', 'border-gray-200', 'bg-orange-50', 'border-orange-300');
            }
        } else if (status === 'on-going' && !isFromToday) {
            // Ongoing session from past day - still show as available but indicate past session
            statusIconWrapper.innerHTML = `
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            statusText.textContent = 'Available';
            if (container) {
                container.classList.add('bg-white', 'border-gray-200');
                container.classList.remove('bg-red-100', 'border-red-600', 'opacity-80', 'bg-orange-50', 'border-orange-300');
            }
            addPastSessionIndicator(labCard);
        } else {
            // Available (either no ongoing session or ongoing session from past days)
            statusIconWrapper.innerHTML = `
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            statusText.textContent = 'Available';
            if (container) {
                container.classList.add('bg-white', 'border-gray-200');
                container.classList.remove('bg-red-100', 'border-red-600', 'opacity-80', 'bg-orange-50', 'border-orange-300');
            }

            // Hide current user information when available (but keep space reserved)
            if (currentUserInfo) {
                currentUserInfo.style.visibility = 'hidden';
                currentUserInfo.style.borderColor = 'transparent';
            }
            removePastSessionIndicator(labCard);
        }

        // Show current user information if there's an ongoing session (regardless of date)
        if (status === 'on-going' && currentUserInfo && currentUser) {
            currentUserInfo.style.visibility = 'visible';
            currentUserInfo.style.borderColor = '#e5e7eb'; // border-gray-200
            if (currentUserName) {
                currentUserName.textContent = currentUser.name || '-';
            }
            if (currentUserTimeIn && currentUser.time_in) {
                const timeInDate = new Date(currentUser.time_in);
                currentUserTimeIn.textContent = timeInDate.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        } else if (currentUserInfo && status !== 'on-going') {
            // Hide but keep space reserved for consistent card heights
            currentUserInfo.style.visibility = 'hidden';
            currentUserInfo.style.borderColor = 'transparent';
        }
    }

    // Function to add past session indicator
    function addPastSessionIndicator(labCard) {
        // Remove existing indicator first
        removePastSessionIndicator(labCard);
        
        // Add indicator badge
        const indicator = document.createElement('div');
        indicator.className = 'past-session-indicator absolute -top-1 -right-1 w-3 h-3 bg-yellow-500 rounded-full border-2 border-white';
        indicator.title = 'Has ongoing session from previous day(s)';
        
        const container = labCard.querySelector('.relative');
        if (container) {
            container.appendChild(indicator);
        }
    }

    // Function to remove past session indicator
    function removePastSessionIndicator(labCard) {
        const indicator = labCard.querySelector('.past-session-indicator');
        if (indicator) {
            indicator.remove();
        }
    }

    // Function to fetch all labs status (now re-enabled with proper logic)
    async function fetchLabsStatus() {
        try {
            const response = await fetch('/lab-schedule/all-labs-status');
            const data = await response.json();
            console.log('Fetched lab status data:', data); // Debug logging
            data.forEach(lab => {
                // Pass session date and current user info to determine if it's from today or past
                updateLabStatus(lab.laboratory, lab.status, lab.session_date, lab.current_user || null);
            });
        } catch (error) {
            console.error('Error fetching lab status:', error);
        }
    }

    // Function to ensure lab cards are always enabled (improved system allows cross-lab usage)
    async function updateLabCardsBasedOnUserSession() {
        // With the improved login system, users can always select labs
        // They'll get warnings about ongoing sessions, but won't be blocked
        const labCards = document.querySelectorAll('.lab-card');
        
        labCards.forEach(card => {
            // Always enable lab selection
            card.style.pointerEvents = 'auto';
            card.style.opacity = '1';
            card.querySelector('.relative').classList.add('cursor-pointer');
            card.querySelector('.relative').classList.remove('cursor-not-allowed');
            
            // Remove any old disabled indicators
            const disabledIndicator = card.querySelector('.disabled-indicator');
            if (disabledIndicator) {
                disabledIndicator.remove();
            }
        });
    }

    // Function to show ongoing session warning
    function showOngoingSessionWarning(warningData) {
        // Create warning modal
        const warningModal = document.createElement('div');
        warningModal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50';
        warningModal.innerHTML = `
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-yellow-100 p-3 rounded-full mr-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Ongoing Session Detected</h3>
                </div>
                
                <div class="mb-6">
                    <p class="text-gray-700 mb-4">${warningData.message}</p>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="text-sm text-gray-700">
                            <div class="font-medium mb-2">Previous Session Details:</div>
                            <div class="space-y-1">
                                <div><span class="font-medium">Laboratory:</span> ${warningData.ongoing_laboratory}</div>
                                <div><span class="font-medium">Purpose:</span> ${warningData.ongoing_purpose}</div>
                                <div><span class="font-medium">Started:</span> ${new Date(warningData.ongoing_time_in).toLocaleString()}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="text-sm text-blue-800">
                            <div class="font-medium mb-1">📋 Action Required:</div>
                            <p>Please notify the admin about the actual time you logged out from Laboratory ${warningData.ongoing_laboratory} so it can be recorded properly in the manual logout system.</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        I Understand
                    </button>
                    <a href="/lab-schedule/manual-logout" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        View Manual Logout
                    </a>
                </div>
            </div>
        `;
        
        document.body.appendChild(warningModal);
        
        // Auto-remove after 15 seconds
        setTimeout(() => {
            if (warningModal.parentNode) {
                warningModal.remove();
            }
        }, 15000);
    }

    // Initial load
    document.addEventListener('DOMContentLoaded', () => {
        // Fetch lab status to show proper availability and indicators
        fetchLabsStatus();
        updateLabCardsBasedOnUserSession(); // Ensure cards are enabled
        
        // Regular updates to keep status current
        setInterval(() => {
            fetchLabsStatus();
            updateLabCardsBasedOnUserSession();
        }, 5000); // Check every 5 seconds
    });
</script>
