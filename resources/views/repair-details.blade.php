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

    <!-- Main Container -->
    <div class="bg-white rounded-xl shadow-lg p-4 md:p-6">
        <!-- Content -->
        <div id="detailsContent" class="space-y-4">
            <!-- Loading State -->
            <div class="text-center py-12">
                <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-t-4 border-red-600 mx-auto"></div>
                <p class="mt-6 text-gray-600 font-medium">Loading repair details...</p>
            </div>
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
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
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
                                <!-- Location will be populated by JavaScript -->
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
                    <div class="bg-gray-50 p-4 rounded-lg register-asset-container hidden">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Asset Registration</p>
                        <a href="#" class="register-asset-btn inline-flex items-center px-4 py-2 bg-red-800 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Register Asset
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
        const content = document.getElementById('detailsContent');
        const template = document.getElementById('repairDetailsTemplate');
        const url = `{{ url('/repair-requests') }}/${id}/data`;
        console.log('Fetching from URL:', url);

        // Show loading state
        content.innerHTML =
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
                if (data) {
                    const request = data;
                    const clone = template.content.cloneNode(true);

                    // Set status badge
                    const statusBadge = clone.querySelector('.status-badge');
                    statusBadge.className = `inline-flex items-center px-4 py-2 rounded-full text-sm font-medium ${getStatusColor(request.status)}`;
                    statusBadge.innerHTML = getStatusIcon(request.status) + formatStatus(request.status);

                    // Set urgency level badge
                    const urgencyLevelBadge = clone.querySelector('.urgency-level-badge');
                    const urgencyText = clone.querySelector('.urgency-text');
                    const urgencyLevel = request.urgency_level || 3;
                    urgencyLevelBadge.className = `urgency-level-badge ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold whitespace-nowrap ${getUrgencyLevelColor(urgencyLevel)}`;
                    urgencyText.textContent = `Level ${urgencyLevel}`;

                    // Set dates
                    clone.querySelector('.created-date').textContent = 'Created: ' + formatDate(request.created_at);
                    clone.querySelector('.updated-date').textContent = 'Last Updated: ' + formatDate(request.updated_at);

                    // Set basic information
                    clone.querySelector('.caller-name').textContent = request.caller_name || 'N/A';
                    
                    // Set location with building, floor, and room
                    const locationText = request.building && request.floor && request.room 
                        ? `${request.building} - ${request.floor} - ${request.room}`
                        : (request.location || 'N/A');
                    clone.querySelector('.location').textContent = locationText;
                    
                    clone.querySelector('.equipment').textContent = request.equipment || 'N/A';
                    clone.querySelector('.serial-number').textContent = request.serial_number || 'N/A';
                    
                    // Set urgency level info
                    const urgencyLevelInfo = clone.querySelector('.urgency-level-info');
                    let urgencyDescription = '';
                    switch (urgencyLevel) {
                        case 1:
                            urgencyDescription = 'Level 1 - Highest (Ongoing Class/Event)';
                            break;
                        case 2:
                            urgencyDescription = 'Level 2 - Medium (Over 1 Week Old)';
                            break;
                        case 3:
                            urgencyDescription = 'Level 3 - Low (New Request)';
                            break;
                        default:
                            urgencyDescription = `Level ${urgencyLevel} - Unknown`;
                    }
                    urgencyLevelInfo.textContent = urgencyDescription;
                    
                    // Set ongoing activity info
                    const ongoingActivity = clone.querySelector('.ongoing-activity');
                    ongoingActivity.textContent = request.ongoing_activity === 'yes' ? 'Yes' : 'No';

                    // Set issue and findings
                    clone.querySelector('.issue').textContent = request.issue || 'No issue recorded';
                    clone.querySelector('.findings').textContent = request.findings || 'No findings recorded';

                    // Set photo
                    const photoContainer = clone.querySelector('.photo-container');
                    if (request.photo) {
                        const baseUrl = window.location.origin + '/capstone/public';
                        const photoPath = request.photo.startsWith('http') ? request.photo : baseUrl + '/storage/' + request.photo;
                        photoContainer.innerHTML = `
                        <div class="relative group w-full bg-gray-100 p-4 rounded-lg">
                            <img src="${photoPath}"
                                alt="Repair Photo"
                                class="w-full h-auto max-h-[400px] object-contain rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                                onerror="this.onerror=null; this.src='${baseUrl}/storage/placeholder.jpg';"
                                onclick="showImageModal('${photoPath}')">
                        </div>
                    `;
                    } else {
                        photoContainer.innerHTML = `
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="mt-2 text-gray-500">No photo available</p>
                        </div>
                    `;
                    }

                    // Set timeline
                    const timelineContainer = clone.querySelector('.timeline-container');
                    timelineContainer.innerHTML = `
                    <div class="relative">
                        <div class="absolute left-[-30px] top-0 w-6 h-6 rounded-full bg-blue-100 border-2 border-blue-500 flex items-center justify-center hidden"></div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs font-medium text-gray-500">CREATED</p>
                            <p class="text-sm text-gray-900">${formatDate(request.created_at)}</p>
                        </div>
                    </div>
                    ${request.time_started ? `
                        <div class="relative">
                            <div class="absolute left-[-30px] top-0 w-6 h-6 rounded-full bg-yellow-100 border-2 border-yellow-500 flex items-center justify-center hidden"></div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-xs font-medium text-gray-500">STARTED</p>
                                <p class="text-sm text-gray-900">${formatDate(request.time_started)}</p>
                            </div>
                        </div>
                    ` : ''}
                    ${request.completed_at ? `
                        <div class="relative">
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-xs font-medium text-gray-500">COMPLETED</p>
                                <p class="text-sm text-gray-900">${formatDate(request.completed_at)}</p>
                            </div>
                        </div>
                    ` : ''}
                `;

                    // Set remarks
                    clone.querySelector('.remarks').textContent = request.remarks || 'No remarks recorded';

                    // Set signatures
                    const techSignatureContainer = clone.querySelector('.technician-signature-container');
                    const callerSignatureContainer = clone.querySelector('.caller-signature-container');

                    if (request.technician_signature) {
                        techSignatureContainer.innerHTML = `
                        <div class="border rounded-lg p-3 bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                            <img src="${request.technician_signature}" 
                                alt="Technician Signature" 
                                class="w-full h-32 object-contain cursor-pointer hover:opacity-90 transition-opacity"
                                onclick="showImageModal('${request.technician_signature}')">
                        </div>
                    `;
                    } else {
                        techSignatureContainer.innerHTML = `
                        <div class="border rounded-lg p-4 bg-gray-50 text-center">
                            <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-gray-500 italic mt-2">No signature provided</p>
                        </div>
                    `;
                    }

                    if (request.caller_signature) {
                        callerSignatureContainer.innerHTML = `
                        <div class="border rounded-lg p-3 bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                            <img src="${request.caller_signature}" 
                                alt="Caller Signature" 
                                class="w-full h-32 object-contain cursor-pointer hover:opacity-90 transition-opacity"
                                onclick="showImageModal('${request.caller_signature}')">
                        </div>
                    `;
                    } else {
                        callerSignatureContainer.innerHTML = `
                        <div class="border rounded-lg p-4 bg-gray-50 text-center">
                            <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-gray-500 italic mt-2">No signature provided</p>
                        </div>
                    `;
                    }

                    // Show register asset button for unregistered assets, or show asset info if registered
                    const registerAssetContainer = clone.querySelector('.register-asset-container');
                    const registerAssetBtn = clone.querySelector('.register-asset-btn');
                    
                    if (!request.serial_number) {
                        // Asset not registered yet - show register button
                        registerAssetContainer.classList.remove('hidden');
                        // Set up the register asset button with pre-filled data
                        const registerUrl = new URL('{{ route("assets.create") }}', window.location.origin);
                        registerUrl.searchParams.set('equipment', request.equipment || '');
                        registerUrl.searchParams.set('location', request.location || '');
                        registerUrl.searchParams.set('category', request.category_id || '');
                        registerUrl.searchParams.set('findings', request.findings || '');
                        registerUrl.searchParams.set('remarks', request.remarks || '');
                        // Add non-registered asset context
                        registerUrl.searchParams.set('from_non_registered', '1');
                        registerUrl.searchParams.set('status', 'PULLED OUT');
                        registerAssetBtn.href = registerUrl.toString();
                    } else {
                        // Asset is registered - show asset information
                        registerAssetContainer.classList.remove('hidden');
                        registerAssetContainer.innerHTML = `
                            <p class="text-xs font-medium text-green-600 uppercase tracking-wide mb-3">Asset Registered</p>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <div class="flex items-center text-green-800">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-medium">Asset has been registered in the system</span>
                                </div>
                                <div class="mt-2 text-sm text-green-700">
                                    <p><strong>Serial Number:</strong> ${request.serial_number}</p>
                                    <p><strong>Status:</strong> ${formatStatus(request.status)}</p>
                                </div>
                            </div>
                        `;
                    }

                    content.innerHTML = '';
                    content.appendChild(clone);
                } else {
                    content.innerHTML = `
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
                content.innerHTML = `
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

    function showImageModal(imageUrl) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        if (!modal || !modalImage) return;

        modalImage.src = imageUrl;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        const modalImage = document.getElementById('modalImage');
        if (modalImage) {
            modalImage.src = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const imageModal = document.getElementById('imageModal');
        if (imageModal) {
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !imageModal.classList.contains('hidden')) {
                    closeImageModal();
                }
            });

            imageModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeImageModal();
                }
            });
        }
    });

    window.showImageModal = showImageModal;
    window.closeImageModal = closeImageModal;
</script>
@endsection