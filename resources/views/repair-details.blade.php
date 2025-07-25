@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 transition-all duration-300" id="mainContent">
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

    <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4 md:gap-0">
            <h1 class="text-2xl font-bold flex items-center">
                <svg class="w-6 h-6 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Repair Request Details
            </h1>
            <div class="space-x-3">
                <a href="{{ route('repair.completed') }}" class="text-sm px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    Back to List
                </a>
            </div>
        </div>

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
    <div class="mb-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium status-badge">
                    <span class="status-icon"></span>
                    <span class="status-text"></span>
                </span>
                <span class="urgent-badge ml-2 hidden inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Urgent
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
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    Basic Information
                </h3>
                <div class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @if(!in_array($request->creator->group_id ?? null, [1, 2]))
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase">Caller's Name</p>
                            <p class="caller-name text-gray-900 text-sm font-medium mt-1"></p>
                        </div>
                        @endif
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase">Location</p>
                            <p class="location text-gray-900 text-sm font-medium mt-1">
                                {{ $request->building }} - {{ $request->floor }} - {{ $request->room }}
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase">Equipment</p>
                            <p class="equipment text-gray-900 text-sm font-medium mt-1"></p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase">Serial Number</p>
                            <p class="serial-number text-gray-900 text-sm font-medium mt-1"></p>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg register-asset-container hidden">
                        <p class="text-xs font-medium text-gray-500 uppercase mb-2">Asset Registration</p>
                        <a href="#" class="register-asset-btn inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Register Asset
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reported Issue -->
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Reported Issue
                </h3>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="issue text-gray-900 text-sm whitespace-pre-wrap"></p>
                </div>
            </div>

            <!-- Findings -->
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2m-9 4h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    Findings
                </h3>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="findings text-gray-900 text-sm whitespace-pre-wrap"></p>
                </div>
            </div>

            <!-- Remarks -->
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    Remarks
                </h3>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="remarks text-gray-900 text-sm whitespace-pre-wrap"></p>
                </div>
            </div>

            <!-- Signatures -->
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Signatures
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Technician's Signature</p>
                        <div class="technician-signature-container">
                            <!-- Technician signature will be inserted here -->
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Caller's Signature</p>
                        <div class="caller-signature-container">
                            <!-- Caller signature will be inserted here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Repair Photo -->
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Repair Photo
                </h3>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <div class="photo-container">
                        <!-- Photo will be inserted here -->
                    </div>
                </div>
            </div>

            <!-- Repair Timeline -->
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Repair Timeline
                </h3>
                <div class="relative pl-6 space-y-4 before:absolute before:left-3 before:top-0 before:bottom-0 before:w-0.5 before:bg-gray-200">
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
                return '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>';
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

                    // Set urgent badge
                    if (request.is_urgent) {
                        clone.querySelector('.urgent-badge').classList.remove('hidden');
                    }

                    // Set dates
                    clone.querySelector('.created-date').textContent = 'Created: ' + formatDate(request.created_at);
                    clone.querySelector('.updated-date').textContent = 'Last Updated: ' + formatDate(request.updated_at);

                    // Set basic information
                    clone.querySelector('.caller-name').textContent = request.caller_name || 'N/A';
                    clone.querySelector('.location').textContent = request.location || 'N/A';
                    clone.querySelector('.equipment').textContent = request.equipment || 'N/A';
                    clone.querySelector('.serial-number').textContent = request.serial_number || 'N/A';

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

                    // Show register asset button for unregistered assets
                    const registerAssetContainer = clone.querySelector('.register-asset-container');
                    const registerAssetBtn = clone.querySelector('.register-asset-btn');
                    if (!request.serial_number) {
                        registerAssetContainer.classList.remove('hidden');
                        // Set up the register asset button with pre-filled data
                        const registerUrl = new URL('{{ route("assets.create") }}', window.location.origin);
                        registerUrl.searchParams.set('equipment', request.equipment || '');
                        registerUrl.searchParams.set('location', request.location || '');
                        registerUrl.searchParams.set('category', request.category_id || '');
                        registerUrl.searchParams.set('findings', request.findings || '');
                        registerUrl.searchParams.set('remarks', request.remarks || '');
                        registerAssetBtn.href = registerUrl.toString();
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