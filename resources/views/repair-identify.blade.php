@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-xl mx-auto">
        <div class="mb-6">
            <h2 class="text-2xl font-bold">Identify Asset for Repair Request</h2>
            <p class="text-sm text-gray-600 mt-1">Scan or upload the asset's QR code to link it to this repair request.</p>
        </div>
        <div id="message-container"></div>
        <form id="identifyForm" method="POST" action="{{ route('repair.save-serial-number', $repairRequest->id) }}" class="space-y-6">
            @csrf
            <input type="hidden" name="repair_request_id" id="repairRequestId" value="{{ $repairRequest->id }}">
            <input type="hidden" name="serial_number" id="serialNumber" value="{{ $serialNumber ?? '' }}">

            <!-- QR Code Scanner Section -->
            <div class="space-y-2">
                <label class="block text-gray-700 text-sm font-semibold">
                    Scan Asset QR Code
                </label>
                <div class="border border-gray-300 rounded-md p-4">
                    <div class="space-y-4">
                        <!-- Camera Scanner -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Camera Scanner</h4>
                            <div id="reader" class="w-full"></div>
                            <div id="scanResult" class="mt-2 text-sm text-gray-600"></div>
                        </div>
                        <!-- OR Divider -->
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">OR</span>
                            </div>
                        </div>
                        <!-- File Upload for QR Code -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                Upload QR Code Image
                            </h4>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <div class="mb-4">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                    </div>
                                    <div class="mb-4">
                                        <p class="text-sm font-medium text-gray-700 mb-1">
                                            Click to upload or drag and drop
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            PNG, JPG, GIF, BMP up to 5MB
                                        </p>
                                    </div>
                                    <label for="qrCodeFile" class="cursor-pointer">
                                        <div class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                                            Choose File
                                        </div>
                                        <input type="file" id="qrCodeFile" name="qr_code_file" 
                                            accept=".jpg,.jpeg,.png,.gif,.bmp"
                                            class="hidden"
                                            onchange="handleQRCodeFileUpload(this)">
                                    </label>
                                </div>
                            </div>
                            <div id="fileInfo" class="hidden mt-3 p-3 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm text-green-800 font-medium" id="fileName"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-500">Scan the QR code on the asset or upload a QR code image to automatically fill in the serial number.</p>
            </div>

            <!-- Current Serial Number Display -->
            <div class="mt-4">
                <label class="block text-gray-700 text-sm font-semibold">Current Linked Serial Number</label>
                <div id="currentSerial" class="p-3 bg-gray-50 border border-gray-200 rounded-md text-gray-800 font-mono">
                    <span id="currentSerialValue">{{ $serialNumber ?? 'None' }}</span>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end pt-4 border-t">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#960106] rounded-md hover:bg-[#7d0105] focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Save Serial Number
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add HTML5-QRCode library -->
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCode;
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize QR Code Scanner
        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        html5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanFailure
        ).catch((err) => {
            console.error("Error starting QR scanner:", err);
            showMessage("Failed to start QR scanner. Please check camera permissions.", "error");
        });
        // Handle form submission via AJAX
        document.getElementById('identifyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    // Optionally disable the form or update UI
                    form.querySelector('button[type="submit"]').disabled = true;
                } else {
                    showMessage(data.message || 'Failed to save serial number', 'error');
                }
            })
            .catch(error => {
                showMessage('An error occurred while saving the serial number', 'error');
            });
        });
    });
    function onScanSuccess(decodedText, decodedResult) {
        try {
            let serial = '';
            try {
                const qrData = JSON.parse(decodedText);
                if (!qrData.serial_number) throw new Error('Invalid QR code format: serial number not found');
                serial = qrData.serial_number;
            } catch (jsonError) {
                if (typeof decodedText === 'string' && decodedText.trim() && /^[A-Z0-9-]+$/i.test(decodedText.trim())) {
                    serial = decodedText.trim();
                } else {
                    throw jsonError;
                }
            }
            document.getElementById('serialNumber').value = serial;
            document.getElementById('scanResult').innerHTML = `Scanned Serial Number: ${serial}`;
            document.getElementById('currentSerialValue').textContent = serial;
            showMessage('QR code scanned successfully!', 'success');
            html5QrCode.stop();
        } catch (error) {
            showMessage('Invalid QR code format. Please scan a valid asset QR code.', 'error');
        }
    }
    function onScanFailure(error) {
        // Silent
    }
    function handleQRCodeFileUpload(input) {
        const file = input.files[0];
        if (!file) return;
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp'];
        if (!allowedTypes.includes(file.type)) {
            showMessage('Please upload a valid image file (JPG, PNG, GIF, BMP)', 'error');
            input.value = '';
            return;
        }
        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
            showMessage('File size too large. Maximum size is 5MB.', 'error');
            input.value = '';
            return;
        }
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        fileName.textContent = file.name;
        fileInfo.classList.remove('hidden');
        html5QrCode.scanFile(file, true)
            .then(decodedText => {
                let serial = '';
                try {
                    const qrData = JSON.parse(decodedText);
                    if (!qrData.serial_number) throw new Error('Invalid QR code format: serial number not found');
                    serial = qrData.serial_number;
                } catch (error) {
                    if (typeof decodedText === 'string' && decodedText.trim() && /^[A-Z0-9-]+$/i.test(decodedText.trim())) {
                        serial = decodedText.trim();
                    } else {
                        showMessage('Invalid QR code format. Please upload a valid asset QR code image.', 'error');
                        input.value = '';
                        fileInfo.classList.add('hidden');
                        return;
                    }
                }
                document.getElementById('serialNumber').value = serial;
                document.getElementById('scanResult').innerHTML = `Uploaded QR Code - Serial Number: ${serial}`;
                document.getElementById('currentSerialValue').textContent = serial;
                showMessage('QR code uploaded and decoded successfully!', 'success');
                input.value = '';
            })
            .catch(error => {
                showMessage('Could not decode QR code from the uploaded image. Please check if the image contains a valid QR code.', 'error');
                input.value = '';
                fileInfo.classList.add('hidden');
            });
    }
    function showMessage(message, type = 'success') {
        const messageContainer = document.getElementById('message-container');
        const messageDiv = document.createElement('div');
        messageDiv.className = type === 'success' 
            ? 'mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700' 
            : 'mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700';
        messageDiv.innerHTML = `<strong class="font-bold">${type === 'success' ? 'Success!' : 'Error!'}</strong> <span class="block sm:inline">${message}</span>`;
        messageContainer.innerHTML = '';
        messageContainer.appendChild(messageDiv);
        setTimeout(() => { messageDiv.remove(); }, 5000);
    }
</script>
<style>
    #reader {
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
    }
    #reader video {
        width: 100%;
        border-radius: 0.375rem;
    }
</style>
@endsection 