@extends('layouts.app')

@section('content')
<div class="flex-1 bg-gray-50">
    <div class="p-4 md:p-6 max-w-4xl mx-auto">
        <!-- Main Container -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-red-800 to-red-700 p-4 md:p-6">
                <h1 class="text-xl md:text-2xl font-bold text-white flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    ASSET SCANNER
                </h1>
                <p class="text-white text-opacity-80 mt-1">Scan QR code to view asset details</p>
            </div>

            <!-- Scanner Section -->
            <div class="p-4 md:p-6">
                <div class="flex flex-col items-center">
                    <!-- Custom styled scanner container -->
                    <div class="w-full max-w-md relative">
                        <!-- Scanner container with custom styling -->
                        <div id="reader" class="w-full aspect-square rounded-lg overflow-hidden shadow-inner"></div>
                        
                        <!-- Scanner overlay with scanning animation -->
                        <div id="scanner-overlay" class="absolute top-0 left-0 w-full h-full pointer-events-none flex items-center justify-center">
                            <div class="w-64 h-64 border-2 border-red-500 rounded-lg relative">
                                <!-- Animated scanning line -->
                                <div id="scan-line" class="absolute left-0 top-0 w-full h-1 bg-red-500 opacity-70 transform -translate-y-1/2"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Scanner status indicator -->
                    <div class="mt-4 text-center">
                        <div id="scanner-status" class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                            <span class="relative flex h-3 w-3 mr-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                            Scanner active
                        </div>
                    </div>
                    
                    <!-- File Upload Option -->
                    <div class="mt-6 w-full max-w-md">
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <h3 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                Or upload QR code image
                            </h3>
                            <div class="flex items-center justify-center w-full">
                                <label for="qr-file-selector" class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <p class="text-xs text-gray-500">Click to upload or drag and drop</p>
                                        <p class="text-xs text-gray-400">PNG, JPG, GIF up to 10MB</p>
                                    </div>
                                    <input id="qr-file-selector" type="file" class="hidden" accept="image/*" />
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Result Section -->
                <div id="result" class="mt-6 hidden">
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                        <div class="flex items-center mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-800 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="font-bold text-gray-900">Asset Found</div>
                        </div>
                        <div class="text-sm text-gray-700 mb-2">
                            <strong>Serial Number:</strong> 
                            <a id="serial-link" href="#" class="font-medium text-red-600 hover:text-red-800 hover:underline"></a>
                        </div>
                        <div class="mt-3 flex justify-end">
                            <button onclick="resetScanner()" class="text-sm text-gray-600 hover:text-gray-900 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Scan Another
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    let html5QrCode = null;
    let scannerActive = false;
    
    function initializeScanner() {
        try {
            // Clean up any existing scanner
            if (html5QrCode) {
                html5QrCode.stop();
                html5QrCode = null;
            }

            // Create a new instance of Html5Qrcode (not the scanner)
            html5QrCode = new Html5Qrcode("reader");
            
            // Custom styling for the scanner
            customizeScanner();
            
            // Start the scanner automatically
            startScanner();
            
            // Start the scanning animation
            startScanAnimation();
            
            // Setup file input listener
            setupFileInput();
        } catch (error) {
            console.error('Scanner initialization error:', error);
            updateScannerStatus('error', 'Scanner initialization failed');
        }
    }
    
    function customizeScanner() {
        // Get the reader element
        const readerElement = document.getElementById('reader');
        
        // Apply custom styles to hide default UI elements we don't want
        const style = document.createElement('style');
        style.textContent = `
            /* Hide the header section with title and buttons */
            #reader__header_message {
                display: none !important;
            }
            
            /* Hide the default scan region */
            #reader__scan_region {
                background: transparent !important;
                border: none !important;
            }
            
            /* Make the video fill the container */
            #reader__scan_region video {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                border-radius: 0.5rem !important;
            }
            
            /* Hide the default QR box */
            #reader__scan_region img {
                display: none !important;
            }
            
            /* Hide the default file selection section */
            #reader__dashboard_section_swaplink {
                display: none !important;
            }
            
            #reader__dashboard_section_csr {
                padding: 0 !important;
            }
            
            /* Style the camera selection dropdown */
            #reader__camera_selection {
                width: 100% !important;
                margin-top: 8px !important;
                padding: 8px 12px !important;
                border-radius: 0.375rem !important;
                border: 1px solid #d1d5db !important;
                background-color: #f9fafb !important;
                font-size: 0.875rem !important;
                color: #374151 !important;
            }
            
            /* Hide default file selection button */
            #reader__filescan_input {
                display: none !important;
            }
        `;
        document.head.appendChild(style);
    }
    
    function setupFileInput() {
        const fileInput = document.getElementById('qr-file-selector');
        fileInput.addEventListener('change', event => {
            if (event.target.files.length === 0) {
                return;
            }
            
            const imageFile = event.target.files[0];
            
            // Stop the camera scanner before processing file
            if (html5QrCode && scannerActive) {
                html5QrCode.stop();
                scannerActive = false;
                updateScannerStatus('processing', 'Processing image...');
                stopScanAnimation();
            }
            
            // Scan the file
            html5QrCode.scanFile(imageFile, true)
                .then(decodedText => {
                    // Handle the scanned code as if from camera
                    onScanSuccess(decodedText);
                })
                .catch(error => {
                    console.error('QR code parsing error:', error);
                    showToast('Could not find a valid QR code in the image', 'error');
                    updateScannerStatus('inactive');
                    // Restart camera scanner
                    startScanner();
                    startScanAnimation();
                });
        });
        
        // Setup drag and drop
        const dropZone = document.querySelector('label[for="qr-file-selector"]');
        
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('bg-gray-100');
        });
        
        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('bg-gray-100');
        });
        
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('bg-gray-100');
            
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                const changeEvent = new Event('change');
                fileInput.dispatchEvent(changeEvent);
            }
        });
    }
    
    function startScanner() {
        try {
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
                formatsToSupport: [ Html5QrcodeSupportedFormats.QR_CODE ],
                showTorchButtonIfSupported: true,
            };
            
            html5QrCode.start(
                { facingMode: "environment" }, // Use back camera by default
                config,
                onScanSuccess,
                onScanFailure
            ).then(() => {
                scannerActive = true;
                updateScannerStatus('active');
            }).catch(err => {
                console.error('Start scanner error:', err);
                updateScannerStatus('error', 'Failed to start camera');
            });
        } catch (error) {
            console.error('Start scanner error:', error);
            updateScannerStatus('error', 'Failed to start scanner');
        }
    }
    
    function stopScanner() {
        try {
            if (html5QrCode && scannerActive) {
                html5QrCode.stop().then(() => {
                    scannerActive = false;
                    updateScannerStatus('inactive');
                    stopScanAnimation();
                }).catch(err => {
                    console.error('Stop scanner error:', err);
                });
            }
        } catch (error) {
            console.error('Stop scanner error:', error);
        }
    }
    
    function resetScanner() {
        // Hide result
        document.getElementById('result').classList.add('hidden');
        
        // Reset file input
        document.getElementById('qr-file-selector').value = '';
        
        // Restart scanner
        startScanner();
        startScanAnimation();
    }

    function onScanSuccess(decodedText, decodedResult) {
        try {
            console.log('Decoded QR Text:', decodedText);
            const qrData = JSON.parse(decodedText);
            console.log('Parsed QR Data:', qrData);
            
            // Get the current protocol and host
            const protocol = window.location.protocol;
            const host = window.location.host;
            
            // Create the link to asset list with search, including /capstone/public
            const serialLink = document.getElementById('serial-link');
            serialLink.href = `${protocol}//${host}/capstone/public/assets?search=${qrData.serial_number}`;
            serialLink.textContent = qrData.serial_number;
            
            // Show results
            document.getElementById('result').classList.remove('hidden');

            // Stop scanner after successful scan
            stopScanner();
            
            // Play success sound
            playSuccessSound();
        } catch (error) {
            console.error('QR parsing error:', error);
            showToast('Invalid QR code format: ' + error.message, 'error');
        }
    }

    function onScanFailure(error) {
        // Don't show alerts for common scanning errors
        if (!error.includes("No MultiFormat Readers")) {
            console.log("Scanner error:", error);
        }
    }
    
    function updateScannerStatus(status, message = '') {
        const statusElement = document.getElementById('scanner-status');
        
        if (status === 'active') {
            statusElement.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800';
            statusElement.innerHTML = `
                <span class="relative flex h-3 w-3 mr-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                Scanner active
            `;
        } else if (status === 'inactive') {
            statusElement.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-600';
            statusElement.innerHTML = `
                <span class="relative flex h-3 w-3 mr-2">
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-gray-400"></span>
                </span>
                Scanner inactive
            `;
        } else if (status === 'processing') {
            statusElement.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800';
            statusElement.innerHTML = `
                <span class="relative flex h-3 w-3 mr-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                </span>
                ${message || 'Processing...'}
            `;
        } else if (status === 'error') {
            statusElement.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-100 text-red-800';
            statusElement.innerHTML = `
                <span class="relative flex h-3 w-3 mr-2">
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
                ${message || 'Scanner error'}
            `;
        }
    }
    
    function startScanAnimation() {
        const scanLine = document.getElementById('scan-line');
        scanLine.style.animation = 'scanAnimation 2s linear infinite';
    }
    
    function stopScanAnimation() {
        const scanLine = document.getElementById('scan-line');
        scanLine.style.animation = 'none';
    }
    
    function playSuccessSound() {
        // Create a simple beep sound
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();
            
            oscillator.type = 'sine';
            oscillator.frequency.value = 1000;
            gainNode.gain.value = 0.1;
            
            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            
            oscillator.start();
            setTimeout(() => oscillator.stop(), 200);
        } catch (e) {
            console.log('Audio not supported');
        }
    }
    
    function showToast(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 left-1/2 transform -translate-x-1/2 px-4 py-2 rounded-lg text-white text-sm shadow-lg z-50 ${type === 'error' ? 'bg-red-600' : 'bg-gray-800'}`;
        toast.textContent = message;
        
        // Add to body
        document.body.appendChild(toast);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            setTimeout(() => document.body.removeChild(toast), 300);
        }, 3000);
    }
    
    // Add the scanning animation keyframes
    const styleSheet = document.createElement('style');
    styleSheet.textContent = `
        @keyframes scanAnimation {
            0% { top: 0; }
            50% { top: 100%; }
            100% { top: 0; }
        }
    `;
    document.head.appendChild(styleSheet);
    
    // Initialize scanner when page loads
    window.addEventListener('load', function() {
        initializeScanner();
    });

    // Clean up scanner when page is unloaded
    window.addEventListener('beforeunload', function() {
        stopScanner();
    });
</script>
@endsection