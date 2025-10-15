@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 transition-all duration-300" id="mainContent">
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center" role="alert">
        <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <div class="font-semibold">Success!</div>
            <div>{{ session('success') }}</div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-center" role="alert">
        <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
            <div class="font-semibold">Error!</div>
            <div>{{ session('error') }}</div>
        </div>
    </div>
    @endif

    <!-- Page Header with Background Design -->
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                        <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Repair Request Details</h1>
                        <p class="text-red-100 text-sm md:text-lg">View comprehensive information about this repair request</p>
                    </div>
                </div>
                <div class="space-x-3">
                    <a href="{{ route('repair.completed') }}" class="text-sm px-4 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 focus:ring-offset-2 focus:ring-offset-red-800 transition-all duration-200">
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Signature Reminder Alert (will be shown dynamically) -->
    <div id="signatureReminderAlert" class="hidden mb-6"></div>

    <!-- Main Container -->
    <div class="bg-white rounded-xl shadow-lg p-4 md:p-6">
        <!-- Tabs Navigation (will be populated dynamically) -->
        <div id="repairTabsNav" class="border-b border-gray-200 mb-6">
            <!-- Loading State -->
            <div class="text-center py-12">
                <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-t-4 border-red-600 mx-auto"></div>
                <p class="mt-6 text-gray-600 font-medium">Loading repair details...</p>
            </div>
        </div>
        
        <!-- Tab Content (will be populated dynamically) -->
        <div id="repairTabsContent" class="space-y-4">
        </div>
    </div>
</div>

<!-- Template for repair details -->
<template id="repairDetailsTemplate">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium status-badge">
                    <span class="status-icon"></span>
                    <span class="status-text"></span>
                </span>
                <!-- Urgency Level Badge -->
                <span class="urgency-level-badge ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold whitespace-nowrap">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="urgency-text">Level 3</span>
                </span>
            </div>
            <div class="text-xs text-gray-500">
                <span class="created-date block"></span>
                <span class="updated-date block"></span>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-centers">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                    </div>
                    Basic Information
                </h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Caller's Name</p>
                            <p class="caller-name text-gray-900 text-sm font-semibold mt-2"></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Location</p>
                            <p class="location text-gray-900 text-sm font-semibold mt-2">
                                <!-- Original caller's location will be populated by JavaScript -->
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Urgency Level</p>
                            <p class="urgency-level-info text-gray-900 text-sm font-semibold mt-2"></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ongoing Activity</p>
                            <p class="ongoing-activity text-gray-900 text-sm font-semibold mt-2"></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Equipment</p>
                            <p class="equipment text-gray-900 text-sm font-semibold mt-2"></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Serial Number</p>
                            <p class="serial-number text-gray-900 text-sm font-semibold mt-2"></p>
                        </div>
                    </div>
                    <!-- Current Location (only shown when asset is linked) -->
                    <div class="current-location-container hidden">
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <p class="text-xs font-medium text-green-600 uppercase tracking-wide">Current Location (Asset)</p>
                            <p class="current-location text-green-800 text-sm font-semibold mt-2">
                                <!-- Asset's current location will be populated by JavaScript -->
                            </p>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg register-asset-container hidden">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Asset Registration</p>
                        <a href="#" class="register-asset-btn inline-flex items-center px-4 py-2 bg-red-800 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Register & Link Asset
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reported Issue -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    Reported Issue
                </h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="issue text-gray-900 text-sm whitespace-pre-wrap"></p>
                </div>
            </div>

            <!-- Signatures -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    Signatures
                </h3>
                <!-- Signature meta information -->
                <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                    <div class="bg-gray-50 border border-gray-200 rounded-md p-3">
                        <p class="text-gray-500 font-medium">Verification Status</p>
                        <p class="verification-status font-semibold mt-1"></p>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 rounded-md p-3">
                        <p class="text-gray-500 font-medium">Signature Type</p>
                        <p class="signature-type font-semibold mt-1"></p>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 rounded-md p-3 signature-extra hidden">
                        <p class="text-gray-500 font-medium signature-extra-label"></p>
                        <p class="signature-extra-value font-semibold mt-1"></p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-3">Technician's Signature</p>
                        <div class="technician-signature-container">
                            <!-- Technician signature will be inserted here -->
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-3">Caller's Signature</p>
                        <div class="caller-signature-container">
                            <!-- Caller signature will be inserted here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technician Evaluation -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    Technician Evaluation
                </h3>
                <div class="evaluation-container">
                    <!-- Evaluation will be inserted here -->
                </div>
            </div>

            <!-- Repair History -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    Repair History
                </h3>
                <div class="repair-history-container">
                    <!-- Repair history will be inserted here -->
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Findings -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2m-9 4h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                    </div>
                    Findings
                </h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="findings text-gray-900 text-sm whitespace-pre-wrap"></p>
                </div>
            </div>

            <!-- Remarks -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                    </div>
                    Remarks
                </h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="remarks text-gray-900 text-sm whitespace-pre-wrap"></p>
                </div>
            </div>

            <!-- Repair Photo -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    Repair Photo
                </h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="photo-container">
                        <!-- Photo will be inserted here -->
                    </div>
                </div>
            </div>

            <!-- Photo Evidence (Before/After) -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        </svg>
                    </div>
                    Photo Evidence
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Before Photos</p>
                        <div class="before-photos-container grid grid-cols-2 gap-3"></div>
                        <p class="before-empty text-xs text-gray-500 hidden">No before photos uploaded</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">After Photos</p>
                        <div class="after-photos-container grid grid-cols-2 gap-3"></div>
                        <p class="after-empty text-xs text-gray-500 hidden">No after photos uploaded</p>
                    </div>
                </div>
            </div>

            <!-- Repair Timeline -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    Repair Timeline
                </h3>
                <div class="relative pl-8 space-y-6 before:absolute before:left-4 before:top-0 before:bottom-0 before:w-0.5 before:bg-gray-200">
                    <div class="timeline-container">
                        <!-- Timeline items will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Image Modal -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center">
    <div class="relative" onclick="event.stopPropagation();">
        <img id="modalImage" src="" alt="Enlarged Image" class="max-h-[80vh] max-w-[80vw] object-contain">
        <button onclick="closeImageModal()" class="absolute -top-4 -right-4 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 transition-colors">
            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<script>
    function formatStatus(status) {
        if (!status) return 'Unknown';
        const statusMap = {
            'pulled_out': 'Pulled Out',
            'in_progress': 'In Progress',
            'completed': 'Completed',
            'cancelled': 'Cancelled',
            'pending': 'Pending'
        };
        return statusMap[status] || status.split('_')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
            .join(' ');
    }

    function getStatusColor(status) {
        switch (status) {
            case 'cancelled':
                return 'bg-red-100 text-red-800 border border-red-200';
            case 'pulled_out':
                return 'bg-yellow-100 text-yellow-800 border border-yellow-200';
            case 'completed':
                return 'bg-green-100 text-green-800 border border-green-200';
            case 'in_progress':
                return 'bg-blue-100 text-blue-800 border border-blue-200';
            case 'pending':
                return 'bg-purple-100 text-purple-800 border border-purple-200';
            default:
                return 'bg-gray-100 text-gray-800 border border-gray-200';
        }
    }

    function getStatusIcon(status) {
        switch (status) {
            case 'cancelled':
                return '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>';
            case 'pulled_out':
                return '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
            case 'completed':
                return '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
            case 'in_progress':
                return '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>';
            case 'pending':
                return '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
            default:
                return '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        }
    }

    function getUrgencyLevelColor(urgencyLevel) {
        switch (urgencyLevel) {
            case 1:
                return 'bg-red-100 text-red-800 border border-red-200';
            case 2:
                return 'bg-orange-100 text-orange-800 border border-orange-200';
            case 3:
                return 'bg-blue-100 text-blue-800 border border-blue-200';
            default:
                return 'bg-gray-100 text-gray-800 border border-gray-200';
        }
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Function to switch between repair tabs
    function switchRepairTab(event, tabId) {
        // Remove active class from all tabs
        const tabs = document.querySelectorAll('.repair-tab-button');
        tabs.forEach(tab => {
            tab.classList.remove('border-red-600', 'text-red-600', 'bg-red-50');
            tab.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Add active class to clicked tab
        event.currentTarget.classList.remove('border-transparent', 'text-gray-500');
        event.currentTarget.classList.add('border-red-600', 'text-red-600', 'bg-red-50');
        
        // Hide all tab panels
        const panels = document.querySelectorAll('.repair-tab-panel');
        panels.forEach(panel => {
            panel.classList.add('hidden');
        });
        
        // Show selected tab panel
        const selectedPanel = document.getElementById(tabId);
        if (selectedPanel) {
            selectedPanel.classList.remove('hidden');
        }
    }

    // Helper to build image URL
    function buildImageUrl(path) {
        if (!path) return '';
        if (path.startsWith('http')) return path;
        if (path.startsWith('/')) return window.location.origin + path;
        return window.location.origin + '/storage/' + path;
    }

    // Build tabs for multiple repair attempts
    function buildRepairTabs(request, tabsNav, tabsContent) {
        let tabsHtml = '<nav class="flex flex-wrap gap-2 pb-4" aria-label="Repair Attempts">';
        
        // Create tab for each history + current
        request.histories.forEach((history, index) => {
            const isActive = index === request.histories.length - 1;
            const statusIcon = history.verification_status === 'approved' ? '✓' : 
                              history.verification_status === 'disputed' ? '↻' : '⋯';
            const statusColor = history.verification_status === 'approved' ? 'green' :
                               history.verification_status === 'disputed' ? 'red' : 'gray';
            
            tabsHtml += `
                <button onclick="switchRepairTab(event, 'repair-tab-${history.id}')" 
                    class="repair-tab-button ${isActive ? 'border-red-600 text-red-600 bg-red-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'} 
                    whitespace-nowrap py-3 px-5 border-b-2 font-medium text-sm flex items-center gap-2 transition-all">
                    <span>Repair ${history.attempt_number}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-${statusColor}-100 text-${statusColor}-800">
                        ${statusIcon}
                    </span>
                </button>
            `;
        });
        
        tabsHtml += '</nav>';
        tabsNav.innerHTML = tabsHtml;
        
        // Build content for each tab
        let contentHtml = '';
        request.histories.forEach((history, index) => {
            const isActive = index === request.histories.length - 1;
            contentHtml += buildRepairTabContent(request, history, isActive);
        });
        
        tabsContent.innerHTML = contentHtml;
    }

    // Build single repair view (no history yet)
    function buildSingleRepairView(request, tabsNav, tabsContent) {
        tabsNav.innerHTML = '<div class="pb-4"><h3 class="text-lg font-semibold text-gray-800">Current Repair Details</h3></div>';
        
        // Create a pseudo-history object from current request data
        const currentData = {
            id: 'current',
            attempt_number: 1,
            technician: request.technician,
            findings: request.findings,
            remarks: request.remarks,
            before_photos: request.before_photos,
            after_photos: request.after_photos,
            technician_signature: request.technician_signature,
            caller_signature: request.caller_signature,
            time_started: request.time_started,
            completed_at: request.completed_at,
            caller_signed_at: request.caller_signed_at,
            verification_status: request.verification_status
        };
        
        tabsContent.innerHTML = buildRepairTabContent(request, currentData, true);
    }

    // Build content for a single repair tab
    function buildRepairTabContent(request, history, isActive) {
        const technicianName = history.technician ? history.technician.name : 'Not assigned';
        const statusColor = history.verification_status === 'approved' ? 'green' :
                           history.verification_status === 'disputed' ? 'red' : 'gray';
        const statusText = history.verification_status === 'approved' ? 'Approved' :
                          history.verification_status === 'disputed' ? 'Rework Requested' : 'Pending';
        
        // Get caller name
        let callerName = 'N/A';
        if (request.creator && request.creator.name) {
            callerName = request.creator.name;
        } else if (request.caller_name) {
            callerName = request.caller_name;
        }
        
        // Get location
        let locationText = 'N/A';
        if (request.building && request.floor && request.room) {
            locationText = `${request.building} - ${request.floor} - ${request.room}`;
        } else if (request.location) {
            locationText = request.location;
        }
        
        return `
            <div id="repair-tab-${history.id}" class="repair-tab-panel ${isActive ? '' : 'hidden'}">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        ${buildBasicInfoSection(request, history, callerName, locationText, technicianName, statusText, statusColor)}
                        ${buildReportedIssueSection(request)}
                        ${buildFindingsSection(history)}
                        ${buildRemarksSection(history)}
                        ${buildTechnicianEvaluationSection(request)}
                    </div>
                    
                    <!-- Right Column -->
                    <div class="space-y-6">
                        ${buildPhotoEvidenceSection(request)}
                        ${buildRepairPhotosSection(history)}
                        ${buildTimelineSection(request, history)}
                        ${buildSignaturesSection(history, request)}
                    </div>
                </div>
            </div>
        `;
    }

    // Check signature reminder and show alert
    function checkSignatureReminder(request) {
        const alertContainer = document.getElementById('signatureReminderAlert');
        
        // Only check if user is the caller and request is completed
        const currentUserId = {{ auth()->id() }};
        const isCallerView = request.user_id === currentUserId;
        
        if (!isCallerView || !request.histories || request.histories.length === 0) {
            alertContainer.classList.add('hidden');
            return;
        }
        
        // Get the latest history
        const latestHistory = request.histories[request.histories.length - 1];
        
        // Check if completed but not signed
        if (latestHistory.completed_at && !latestHistory.caller_signed_at) {
            const completedDate = new Date(latestHistory.completed_at);
            const now = new Date();
            const hoursSinceCompletion = Math.floor((now - completedDate) / (1000 * 60 * 60));
            
            let alertHtml = '';
            
            // 72+ hours: Auto-approval warning
            if (hoursSinceCompletion >= 72) {
                alertHtml = `
                    <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-md">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-sm font-bold text-red-800 mb-1">⚠️ Auto-Approval Imminent</h3>
                                <p class="text-sm text-red-700">This repair has been completed for over 72 hours without your signature. It will be automatically approved soon. Please review and sign immediately if you have any concerns.</p>
                                <p class="text-xs text-red-600 mt-2">Completed: ${formatDate(latestHistory.completed_at)} (${hoursSinceCompletion} hours ago)</p>
                            </div>
                        </div>
                    </div>
                `;
            }
            // 48-72 hours: Supervisor notified
            else if (hoursSinceCompletion >= 48) {
                alertHtml = `
                    <div class="p-4 bg-orange-50 border-l-4 border-orange-500 rounded-lg shadow-md">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-orange-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-sm font-bold text-orange-800 mb-1">🔔 Supervisor Notified</h3>
                                <p class="text-sm text-orange-700">This repair has been unsigned for 48+ hours. Supervisors have been notified. Please review and sign within 24 hours or it will be auto-approved.</p>
                                <p class="text-xs text-orange-600 mt-2">Completed: ${formatDate(latestHistory.completed_at)} (${hoursSinceCompletion} hours ago)</p>
                            </div>
                        </div>
                    </div>
                `;
            }
            // 24-48 hours: Second reminder
            else if (hoursSinceCompletion >= 24) {
                alertHtml = `
                    <div class="p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg shadow-md">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-yellow-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-sm font-bold text-yellow-800 mb-1">⏰ Signature Reminder</h3>
                                <p class="text-sm text-yellow-700">This repair was completed over 24 hours ago and is still awaiting your signature. Please review and sign to confirm the repair quality.</p>
                                <p class="text-xs text-yellow-600 mt-2">Completed: ${formatDate(latestHistory.completed_at)} (${hoursSinceCompletion} hours ago)</p>
                            </div>
                        </div>
                    </div>
                `;
            }
            // 0-24 hours: First reminder
            else {
                alertHtml = `
                    <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg shadow-md">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-sm font-bold text-blue-800 mb-1">📝 Signature Required</h3>
                                <p class="text-sm text-blue-700">This repair has been completed and is awaiting your signature. Please review the repair details and sign to confirm.</p>
                                <p class="text-xs text-blue-600 mt-2">Completed: ${formatDate(latestHistory.completed_at)} (${hoursSinceCompletion} hours ago)</p>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            if (alertHtml) {
                alertContainer.innerHTML = alertHtml;
                alertContainer.classList.remove('hidden');
            }
        } else {
            alertContainer.classList.add('hidden');
        }
    }

    // Section builders
    function buildBasicInfoSection(request, history, callerName, locationText, technicianName, statusText, statusColor) {
        return `
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    Basic Information
                </h3>
                <div class="space-y-3">
                    <!-- Status -->
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="bg-${statusColor}-100 p-2 rounded-lg">
                                <svg class="w-4 h-4 text-${statusColor}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Status</span>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-${statusColor}-100 text-${statusColor}-800">${statusText}</span>
                    </div>
                    
                    <!-- Ticket Number -->
                    <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                        <div class="bg-blue-100 p-2 rounded-lg flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500">Ticket Number</p>
                            <p class="text-sm font-semibold text-gray-900 font-mono truncate">${request.ticket_number || 'N/A'}</p>
                        </div>
                    </div>
                    
                    <!-- Caller -->
                    <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                        <div class="bg-purple-100 p-2 rounded-lg flex-shrink-0">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500">Caller</p>
                            <p class="text-sm font-semibold text-gray-900 truncate">${callerName}</p>
                        </div>
                    </div>
                    
                    <!-- Location -->
                    <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                        <div class="bg-green-100 p-2 rounded-lg flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500">Location</p>
                            <p class="text-sm font-semibold text-gray-900 truncate">${locationText}</p>
                        </div>
                    </div>
                    
                    <!-- Equipment -->
                    <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                        <div class="bg-orange-100 p-2 rounded-lg flex-shrink-0">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500">Equipment</p>
                            <p class="text-sm font-semibold text-gray-900 truncate">${request.equipment || 'N/A'}</p>
                        </div>
                    </div>
                    
                    <!-- Serial Number -->
                    <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                        <div class="bg-indigo-100 p-2 rounded-lg flex-shrink-0">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500">Serial Number</p>
                            <p class="text-sm font-semibold text-gray-900 font-mono truncate">${request.serial_number || 'N/A'}</p>
                        </div>
                    </div>
                    
                    <!-- Register Asset Button (show if no serial number) -->
                    ${!request.serial_number || request.serial_number === 'N/A' ? `
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <p class="text-xs font-medium text-blue-600 uppercase tracking-wide mb-3">Asset Registration</p>
                        ${(() => {
                            // Determine status based on completion type
                            let assetStatus = 'IN USE'; // default for completed
                            if (request.remarks && request.remarks.includes('Original completion type: pulled_out')) {
                                assetStatus = 'PULLED OUT';
                            } else if (request.status === 'pulled_out') {
                                assetStatus = 'PULLED OUT';
                            }
                            return `<a href="/assets/create?repair_request_id=${request.id}&equipment=${encodeURIComponent(request.equipment || '')}&location=${encodeURIComponent(locationText)}&status=${assetStatus}&from_non_registered=1" 
                               class="inline-flex items-center px-4 py-2 bg-red-800 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 text-sm font-medium">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Register & Link Asset
                            </a>`;
                        })()}
                    </div>
                    ` : ''}
                    
                    <!-- Technician -->
                    <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                        <div class="bg-red-100 p-2 rounded-lg flex-shrink-0">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500">Technician</p>
                            <p class="text-sm font-semibold text-gray-900 truncate">${technicianName}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function buildFindingsSection(history) {
        return `
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    Findings
                </h3>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">${history.findings || 'No findings recorded'}</p>
            </div>
        `;
    }

    function buildRemarksSection(history) {
        return `
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                    </div>
                    Remarks
                </h3>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">${history.remarks || 'No remarks recorded'}</p>
                ${history.caller_feedback ? `
                    <div class="mt-4 pt-4 border-t border-red-200 bg-red-50 rounded-lg p-4">
                        <p class="text-sm font-semibold text-red-700 mb-2">Caller Feedback (Rework Reason):</p>
                        <p class="text-sm text-red-900 whitespace-pre-wrap">${history.caller_feedback}</p>
                    </div>
                ` : ''}
            </div>
        `;
    }

    function buildReportedIssueSection(request) {
        return `
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    Reported Issue
                </h3>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">${request.issue || 'No issue recorded'}</p>
            </div>
        `;
    }

    function buildPhotoEvidenceSection(request) {
        const photoPath = request.photo ? buildImageUrl(request.photo) : null;
        return `
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    Photo Evidence
                </h3>
                ${photoPath ? `
                    <div class="flex justify-center">
                        <img src="${photoPath}" alt="Issue Photo" class="max-w-xs h-auto rounded-lg border cursor-pointer hover:opacity-90 transition-opacity" onclick="showImageModal('${photoPath}')">
                    </div>
                ` : '<p class="text-sm text-gray-500 text-center py-8">No photo available</p>'}
            </div>
        `;
    }

    function buildRepairPhotosSection(history) {
        const beforePhotos = Array.isArray(history.before_photos) ? history.before_photos : [];
        const afterPhotos = Array.isArray(history.after_photos) ? history.after_photos : [];
        
        return `
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    Repair Photos
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Before Photos Column -->
                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-2 text-center">Before</p>
                        ${beforePhotos.length > 0 ? `
                            <div class="space-y-2">
                                ${beforePhotos.map(p => `<img src="${buildImageUrl(p)}" alt="Before" class="w-full h-32 object-cover rounded border cursor-pointer hover:opacity-90 transition-opacity" onclick="showImageModal('${buildImageUrl(p)}')">`).join('')}
                            </div>
                        ` : '<div class="h-32 flex items-center justify-center bg-gray-50 rounded border border-dashed border-gray-300"><p class="text-xs text-gray-500">No photos</p></div>'}
                    </div>
                    
                    <!-- After Photos Column -->
                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-2 text-center">After</p>
                        ${afterPhotos.length > 0 ? `
                            <div class="space-y-2">
                                ${afterPhotos.map(p => `<img src="${buildImageUrl(p)}" alt="After" class="w-full h-32 object-cover rounded border cursor-pointer hover:opacity-90 transition-opacity" onclick="showImageModal('${buildImageUrl(p)}')">`).join('')}
                            </div>
                        ` : '<div class="h-32 flex items-center justify-center bg-gray-50 rounded border border-dashed border-gray-300"><p class="text-xs text-gray-500">No photos</p></div>'}
                    </div>
                </div>
            </div>
        `;
    }

    function buildTimelineSection(request, history) {
        return `
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    Repair Timeline
                </h3>
                <div class="space-y-3">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs font-medium text-gray-500">CREATED</p>
                        <p class="text-sm text-gray-900">${formatDate(request.created_at)}</p>
                    </div>
                    ${history.time_started ? `
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs font-medium text-gray-500">STARTED</p>
                            <p class="text-sm text-gray-900">${formatDate(history.time_started)}</p>
                        </div>
                    ` : ''}
                    ${history.completed_at ? `
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs font-medium text-gray-500">COMPLETED</p>
                            <p class="text-sm text-gray-900">${formatDate(history.completed_at)}</p>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    function buildSignaturesSection(history, request) {
        return `
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    Signatures
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Technician Signature Column -->
                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-2 text-center">Technician</p>
                        ${history.technician_signature ? `
                            <img src="${history.technician_signature}" alt="Technician Signature" class="w-full h-32 object-contain border rounded bg-gray-50 cursor-pointer hover:opacity-90 transition-opacity" onclick="showImageModal('${history.technician_signature}')">
                        ` : '<div class="h-32 flex items-center justify-center bg-gray-50 rounded border border-dashed border-gray-300"><p class="text-xs text-gray-500">No signature</p></div>'}
                    </div>
                    
                    <!-- Caller Signature Column -->
                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-2 text-center">Caller</p>
                        ${history.caller_signature ? `
                            <img src="${history.caller_signature}" alt="Caller Signature" class="w-full h-32 object-contain border rounded bg-gray-50 cursor-pointer hover:opacity-90 transition-opacity" onclick="showImageModal('${history.caller_signature}')">
                            ${history.caller_signed_at ? `<p class="text-xs text-gray-500 mt-1 text-center">Signed: ${formatDate(history.caller_signed_at)}</p>` : ''}
                        ` : '<div class="h-32 flex items-center justify-center bg-gray-50 rounded border border-dashed border-gray-300"><p class="text-xs text-gray-500">No signature</p></div>'}
                    </div>
                </div>
            </div>
        `;
    }

    function buildTechnicianEvaluationSection(request) {
        if (!request.evaluation) {
            return `
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl shadow-sm p-6 border-2 border-dashed border-gray-300">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <div class="bg-gray-200 p-2 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                        Technician Evaluation
                    </h3>
                    <div class="flex flex-col items-center justify-center py-8">
                        <div class="bg-white rounded-full p-4 mb-4 shadow-sm">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                        <p class="text-gray-600 font-medium mb-1">No Evaluation Yet</p>
                        <p class="text-sm text-gray-500 text-center max-w-xs">The technician has not been evaluated for this repair work.</p>
                    </div>
                </div>
            `;
        }
        
        const rating = request.evaluation.rating || 0;
        let starsHtml = '';
        for (let i = 1; i <= 5; i++) {
            starsHtml += i <= rating ? 
                '<svg class="w-6 h-6 text-yellow-400 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>' :
                '<svg class="w-6 h-6 text-gray-300 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
        }
        
        return `
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="bg-red-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    Technician Evaluation
                </h3>
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex gap-1">${starsHtml}</div>
                        <span class="text-sm font-semibold text-yellow-800">${rating}/5</span>
                    </div>
                    ${request.evaluation.feedback ? `
                        <div class="mt-3 pt-3 border-t border-yellow-200">
                            <p class="text-sm font-medium text-gray-700 mb-1">Feedback:</p>
                            <p class="text-sm text-gray-900">${request.evaluation.feedback}</p>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    // Load repair details when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Extract ID from URL path
        const pathParts = window.location.pathname.split('/');
        const id = pathParts[pathParts.length - 1];
        console.log('Repair ID:', id);
        if (id) {
            loadRepairDetails(id);
        }
    });

    function loadRepairDetails(id) {
        const tabsNav = document.getElementById('repairTabsNav');
        const tabsContent = document.getElementById('repairTabsContent');
        const url = `{{ url('/repair-requests') }}/${id}/data`;
        console.log('Fetching from URL:', url);

        // Show loading state
        tabsNav.innerHTML =
            '<div class="text-center py-12">' +
            '<div class="animate-spin rounded-full h-16 w-16 border-b-4 border-t-4 border-red-600 mx-auto"></div>' +
            '<p class="mt-6 text-gray-600 font-medium">Loading repair details...</p>' +
            '</div>';

        // Fetch request details
        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data);
                console.log('Asset data:', data.asset);
                console.log('Creator data:', data.creator);
                console.log('Histories:', data.histories);
                
                if (data) {
                    const request = data;
                    
                    // Check for unsigned repairs and show reminder
                    checkSignatureReminder(request);
                    
                    // Build tabs based on repair history
                    if (request.histories && request.histories.length > 0) {
                        // Create tabs for each repair attempt
                        buildRepairTabs(request, tabsNav, tabsContent);
                    } else {
                        // No history yet - show current repair details only
                        buildSingleRepairView(request, tabsNav, tabsContent);
                    }
                } else {
                    tabsNav.innerHTML = `
                    <div class="text-center py-12">
                        <div class="text-red-600 mb-4">
                            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Error Loading Details</h3>
                        <p class="text-gray-600">We couldn't retrieve the repair request information. Please try again later.</p>
                    </div>
                `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tabsNav.innerHTML = `
                <div class="text-center py-12">
                    <div class="text-red-600 mb-4">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Network Error</h3>
                    <p class="text-gray-600">There was a problem connecting to the server. Please check your connection and try again.</p>
                </div>
            `;
            });
    }

    // Helper function for image modal (if not already defined)
    function showImageModal(imageSrc) {
        // Create modal
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4';
        modal.onclick = function() { document.body.removeChild(modal); };
        
        const img = document.createElement('img');
        img.src = imageSrc;
        img.className = 'max-w-full max-h-full object-contain';
        img.onclick = function(e) { e.stopPropagation(); };
        
        modal.appendChild(img);
        document.body.appendChild(modal);
    }

    // Status helper functions
    function getStatusColor(status) {
        const colors = {
            'completed': 'bg-green-100 text-green-800',
            'in_progress': 'bg-blue-100 text-blue-800',
            'pending': 'bg-yellow-100 text-yellow-800',
            'cancelled': 'bg-red-100 text-red-800',
            'pulled_out': 'bg-purple-100 text-purple-800'
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
    }

    function getStatusIcon(status) {
        // Return empty string or add icons if needed
        return '';
    }

    function formatStatus(status) {
        const statusMap = {
            'in_progress': 'In Progress',
            'pulled_out': 'Pulled Out'
        };
        return statusMap[status] || status.charAt(0).toUpperCase() + status.slice(1);
    }

    // Remove old template (not needed anymore)
    const oldTemplate = document.getElementById('repairDetailsTemplate');
    if (oldTemplate) {
        oldTemplate.remove();
    }
</script>
@endsection
