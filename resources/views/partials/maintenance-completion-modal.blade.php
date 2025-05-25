<!-- Completion Modal with Feedback -->
<div id="completionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-3 md:p-5 border w-full max-w-[95%] md:max-w-[600px] shadow-lg rounded-lg bg-white">
        <!-- Close button -->
        <button onclick="hideModal('completionModal')" class="absolute top-3 md:top-4 right-3 md:right-4 text-gray-400 hover:text-gray-600 focus:outline-none">
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <div class="mt-2 md:mt-3">
            <!-- Header with Icon -->
            <div class="flex items-center justify-center mb-4 md:mb-6">
                <svg class="w-6 h-6 md:w-8 md:h-8 text-red-800 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-lg md:text-xl font-bold text-gray-900">Complete Maintenance</h3>
            </div>

            <div class="mt-4">
                <form id="completeForm" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')
                    
                    <!-- Issues Radio Group with Icon -->
                     <div class="space-y-3 bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center space-x-2 mb-2">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <label class="text-sm font-medium text-gray-700">Did you notice any issues with assets?</label>
                        </div>
                        <div class="flex items-center space-x-6 ml-7">
                            <label class="inline-flex items-center hover:cursor-pointer">
                                <input type="radio" name="has_issues" value="0" class="form-radio text-red-800 focus:ring-red-800" checked onclick="toggleIssueDetails(false)">
                                <span class="ml-2 text-gray-700">No Issues</span>
                            </label>
                            <label class="inline-flex items-center hover:cursor-pointer">
                                <input type="radio" name="has_issues" value="1" class="form-radio text-red-800 focus:ring-red-800" onclick="toggleIssueDetails(true)">
                                <span class="ml-2 text-gray-700">Yes, Found Issues</span>
                            </label>
                        </div>
                    </div>

                    <div id="issueDetails" class="space-y-4 hidden">
                        <!-- Asset Search Section with Icon -->
                        <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                            <div class="flex items-center space-x-2 mb-2">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <label class="text-sm font-medium text-gray-700">Search Asset</label>
                            </div>
                            <div class="flex space-x-2">
                                <div class="flex-1">
                                    <input type="text" id="serial_number" name="serial_number" placeholder="Enter serial number" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 h-12 px-4">                                </div>
                                <button type="button" onclick="searchAsset()" class="px-4 py-2 bg-red-800 text-white rounded-md hover:bg-red-700 flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <span>Search</span>
                                </button>
                            </div>
                            
                            <!-- Asset Search Message -->
                            <div id="assetMessage" class="hidden rounded-md p-3">
                                <div class="message-title font-semibold"></div>
                                <div class="message-content mt-1"></div>
                            </div>
                        </div>

                        <!-- Asset Details Section with Icon -->
                        <div class="bg-gray-50 p-4 rounded-lg space-y-4">
                            <div class="flex items-center space-x-2 mb-2">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <label class="text-sm font-medium text-gray-700">Asset Details</label>
                            </div>
                            <div class="grid grid-cols-2 gap-4 ml-7">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Asset Name</label>
                                    <input type="text" id="asset_name" name="asset_name" readonly class="mt-1 w-full rounded-md border-gray-300 bg-gray-100 shadow-sm h-12 px-4">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Category</label>
                                    <input type="text" id="asset_category" name="asset_category" readonly class="mt-1 w-full rounded-md border-gray-300 bg-gray-100 shadow-sm h-12 px-4">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Location</label>
                                    <input type="text" id="asset_location" name="asset_location" readonly class="mt-1 w-full rounded-md border-gray-300 bg-gray-100 shadow-sm h-12 px-4">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">End of Life Date</label>
                                    <input type="text" id="asset_eol" name="asset_eol" readonly class="mt-1 w-full rounded-md border-gray-300 bg-gray-100 shadow-sm h-12 px-4">
                                </div>
                            </div>
                        </div>

                        <!-- Issue Description with Icon -->
                        <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <label class="text-sm font-medium text-gray-700">Issue Description</label>
                            </div>
                            <textarea name="issue_description" rows="4" placeholder="Describe the issue..." class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 px-4 py-3"></textarea>
                        </div>

                        <!-- Container for Additional Issues -->
                        <div id="additionalIssues" class="space-y-4"></div>

                        <!-- Add More Issues Button (moved outside) -->
                        <div class="flex justify-end mt-2">
                            <button type="button" onclick="addAnotherIssue()" class="px-4 py-2 bg-red-800 text-white rounded-md hover:bg-red-700 flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span>Add Another Issue</span>
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="hideModal('completionModal')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Cancel</span>
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Complete Maintenance</span>
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleIssueDetails(show) {
    const issueDetailsDiv = document.getElementById('issueDetails');
    if (show) {
        issueDetailsDiv.classList.remove('hidden');
    } else {
        issueDetailsDiv.classList.add('hidden');
    }
}

async function searchAsset() {
    const serialNumber = document.getElementById('serial_number').value;
    const labNumber = document.getElementById('completeForm').getAttribute('data-lab');
    const messageDiv = document.getElementById('assetMessage');
    const messageTitle = messageDiv.querySelector('.message-title');
    const messageContent = messageDiv.querySelector('.message-content');

    if (!serialNumber) {
        messageDiv.className = 'mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded';
        messageTitle.textContent = 'Error!';
        messageContent.textContent = 'Please enter a serial number';
        messageDiv.classList.remove('hidden');
        return;
    }

    try {
        const response = await fetch(`/assets/fetch/${serialNumber}`);
        const data = await response.json();

        if (!response.ok) {
            throw new Error('Asset not found');
        }

        // Validate that the asset belongs to the correct lab
        const expectedLocation = `Computer Lab ${labNumber}`;
        if (data.location !== expectedLocation) {
            throw new Error(`This asset does not belong to ${expectedLocation}`);
        }

        // Auto-fill all asset details
        document.getElementById('serial_number').value = serialNumber; // Ensure serial number is set
        document.getElementById('asset_name').value = data.name || '';
        document.getElementById('asset_category').value = data.category?.name || '';
        document.getElementById('asset_location').value = data.location || '';
        document.getElementById('asset_eol').value = data.end_of_life_date || 'Not specified';

        // Show success message
        messageDiv.className = 'mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded';
        messageTitle.textContent = 'Success!';
        messageContent.textContent = 'Asset found and details loaded';
        messageDiv.classList.remove('hidden');
    } catch (error) {
        messageDiv.className = 'mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded';
        messageTitle.textContent = 'Error!';
        messageContent.textContent = error.message;
        messageDiv.classList.remove('hidden');
    }
}

function addAnotherIssue() {
    const container = document.getElementById('additionalIssues');
    const issueCount = container.children.length + 1;

    const issueDiv = document.createElement('div');
    issueDiv.className = 'space-y-3 md:space-y-4 border-t pt-3 md:pt-4';
    issueDiv.innerHTML = `
        <!-- Asset Search Section with Icon -->
        <div class="bg-gray-50 p-3 md:p-4 rounded-lg space-y-2 md:space-y-3">
            <div class="flex items-center space-x-2 mb-1 md:mb-2">
                <svg class="w-4 h-4 md:w-5 md:h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <label class="text-xs md:text-sm font-medium text-gray-700">Search Additional Asset</label>
            </div>
            <div class="flex space-x-2">
                <div class="flex-1">
                    <input type="text" class="serial_number w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 h-10 md:h-12 px-3 md:px-4 text-xs md:text-sm" 
                           name="additional_serial_number[]" placeholder="Enter serial number">
                </div>
                <button type="button" onclick="searchAdditionalAsset(this)" class="px-3 py-1.5 md:px-4 md:py-2 bg-red-800 text-white rounded-md hover:bg-red-700 flex items-center space-x-1 text-xs md:text-sm">
                    <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Search</span>
                </button>
            </div>
            
            <!-- Asset Search Message -->
            <div class="assetMessage hidden rounded-md p-2 md:p-3">
                <div class="message-title font-semibold text-xs md:text-sm"></div>
                <div class="message-content mt-1 text-xs md:text-sm"></div>
            </div>
        </div>

        <!-- Asset Details Section with Icon -->
        <div class="bg-gray-50 p-3 md:p-4 rounded-lg space-y-3 md:space-y-4">
            <div class="flex items-center space-x-2 mb-1 md:mb-2">
                <svg class="w-4 h-4 md:w-5 md:h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <label class="text-xs md:text-sm font-medium text-gray-700">Additional Asset Details</label>
            </div>
            <div class="grid grid-cols-2 gap-2 md:gap-4 ml-5 md:ml-7">
                <div>
                    <label class="block text-xs font-medium text-gray-500">Asset Name</label>
                    <input type="text" class="asset_name mt-1 w-full rounded-md border-gray-300 bg-gray-100 shadow-sm h-10 md:h-12 px-3 md:px-4 text-xs md:text-sm" 
                           readonly name="additional_asset_name[]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500">Category</label>
                    <input type="text" class="asset_category mt-1 w-full rounded-md border-gray-300 bg-gray-100 shadow-sm h-10 md:h-12 px-3 md:px-4 text-xs md:text-sm" 
                           readonly name="additional_asset_category[]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500">Location</label>
                    <input type="text" class="asset_location mt-1 w-full rounded-md border-gray-300 bg-gray-100 shadow-sm h-10 md:h-12 px-3 md:px-4 text-xs md:text-sm" 
                           readonly name="additional_asset_location[]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500">End of Life Date</label>
                    <input type="text" class="asset_eol mt-1 w-full rounded-md border-gray-300 bg-gray-100 shadow-sm h-10 md:h-12 px-3 md:px-4 text-xs md:text-sm" 
                           readonly name="additional_asset_eol[]">
                </div>
            </div>
        </div>

        <!-- Issue Description with Icon -->
        <div class="bg-gray-50 p-3 md:p-4 rounded-lg space-y-2 md:space-y-3">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 md:w-5 md:h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <label class="text-xs md:text-sm font-medium text-gray-700">Additional Issue Description</label>
            </div>
            <textarea class="issue_description mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 px-3 md:px-4 py-2 md:py-3 text-xs md:text-sm" 
                      rows="3" md:rows="4" placeholder="Describe the issue..." name="additional_issue_description[]"></textarea>
        </div>

        <!-- Remove Button -->
        <div class="flex justify-end">
            <button type="button" onclick="removeIssue(this)" class="px-3 py-1.5 md:px-4 md:py-2 bg-red-600 text-white rounded-md hover:bg-red-700 flex items-center space-x-1 text-xs md:text-sm">
                <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span>Remove Issue</span>
            </button>
        </div>
    `;

    container.appendChild(issueDiv);
}

// Add this new function to remove an issue
function removeIssue(button) {
    // Only remove the closest .space-y-3 or .space-y-4 block (the single issue)
    const issueDiv = button.closest('.space-y-3, .space-y-4');
    if (issueDiv) {
        issueDiv.remove();
    }
}

async function searchAdditionalAsset(button) {
    const issueDiv = button.closest('.bg-gray-50');
    const serialInput = issueDiv.querySelector('.serial_number');
    const originalSerialNumber = serialInput.value;
    const messageDiv = issueDiv.querySelector('.assetMessage');
    const messageTitle = messageDiv.querySelector('.message-title');
    const messageContent = messageDiv.querySelector('.message-content');
    const labNumber = document.getElementById('completeForm').getAttribute('data-lab');

    if (!serialInput.value) {
        messageDiv.className = 'assetMessage mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded';
        messageTitle.textContent = 'Error!';
        messageContent.textContent = 'Please enter a serial number';
        messageDiv.classList.remove('hidden');
        return;
    }

    try {
        const response = await fetch(`/assets/fetch/${originalSerialNumber}`);
        const data = await response.json();

        if (!response.ok) {
            throw new Error('Asset not found');
        }

        // Validate that the asset belongs to the correct lab
        const expectedLocation = `Computer Lab ${labNumber}`;
        if (data.location !== expectedLocation) {
            throw new Error(`This asset does not belong to ${expectedLocation}`);
        }

        // Auto-fill all asset details
        serialInput.value = originalSerialNumber;
        const parentDiv = issueDiv.closest('.space-y-4');
        parentDiv.querySelector('.asset_name').value = data.name || '';
        parentDiv.querySelector('.asset_category').value = data.category?.name || '';
        parentDiv.querySelector('.asset_location').value = data.location || '';
        parentDiv.querySelector('.asset_eol').value = data.end_of_life_date || 'Not specified';

        // Show success message
        messageDiv.className = 'assetMessage mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded';
        messageTitle.textContent = 'Success!';
        messageContent.textContent = 'Asset found and details loaded';
        messageDiv.classList.remove('hidden');
    } catch (error) {
        messageDiv.className = 'assetMessage mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded';
        messageTitle.textContent = 'Error!';
        messageContent.textContent = error.message;
        messageDiv.classList.remove('hidden');

        // Clear asset details on error
        const parentDiv = issueDiv.closest('.space-y-4');
        parentDiv.querySelector('.asset_name').value = '';
        parentDiv.querySelector('.asset_category').value = '';
        parentDiv.querySelector('.asset_location').value = '';
        parentDiv.querySelector('.asset_eol').value = '';
    }
}

function hideModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function markAsComplete(maintenanceId, labNumber) {
    const modal = document.getElementById('completionModal');
    const form = document.getElementById('completeForm');
    form.setAttribute('action', `/maintenance/${maintenanceId}/complete`);
    form.setAttribute('data-lab', labNumber);

    // Reset form and hide issue details
    form.reset();
    document.getElementById('issueDetails').classList.add('hidden');
    document.getElementById('assetMessage').classList.add('hidden');
    
    // Clear any additional issues
    document.getElementById('additionalIssues').innerHTML = '';

    // Show modal
    modal.classList.remove('hidden');
}

// Single DOMContentLoaded event listener that handles everything
document.addEventListener('DOMContentLoaded', function() {
    // Radio button event listeners
    const hasIssuesRadios = document.querySelectorAll('input[name="has_issues"]');
    const issueDetailsDiv = document.getElementById('issueDetails');

    hasIssuesRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === '1') {
                issueDetailsDiv.classList.remove('hidden');
            } else {
                issueDetailsDiv.classList.add('hidden');
            }
        });
    });

    // Form submission handler
    const completeForm = document.getElementById('completeForm');
    if (completeForm) {
        completeForm.addEventListener('submit', function(e) {
            const hasIssues = document.querySelector('input[name="has_issues"]:checked').value === '1';
            
            if (hasIssues) {
                e.preventDefault();
            
                // Collect all asset issues
                const issues = [];
            
                // Get main issue
                const mainIssueDescription = document.querySelector('textarea[name="issue_description"]').value.trim();
                const mainSerialNumber = document.getElementById('serial_number').value.trim();
                const messageDiv = document.getElementById('assetMessage');
                const messageTitle = messageDiv.querySelector('.message-title');
                const messageContent = messageDiv.querySelector('.message-content');
            
                if (!mainSerialNumber || !mainIssueDescription) {
                    // Use the messageDiv pattern instead of alert
                    messageDiv.className = 'mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded';
                    messageTitle.textContent = 'Error!';
                    messageContent.textContent = 'Please fill in both serial number and issue description for the main issue';
                    messageDiv.classList.remove('hidden');
                    
                    // Scroll to the message
                    messageDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }
            
                issues.push({
                    serial_number: mainSerialNumber,
                    issue_description: mainIssueDescription
                });
            
                // Get additional issues
                const additionalIssues = document.getElementById('additionalIssues').children;
                let hasInvalidAdditionalIssue = false;
                
                Array.from(additionalIssues).forEach(issueDiv => {
                    const serialNumber = issueDiv.querySelector('.serial_number').value.trim();
                    const issueDescription = issueDiv.querySelector('.issue_description').value.trim();
                    const additionalMessageDiv = issueDiv.querySelector('.assetMessage');
                    
                    if (!additionalMessageDiv) {
                        console.error('Could not find message div in additional issue');
                        return;
                    }
                    
                    const additionalMessageTitle = additionalMessageDiv.querySelector('.message-title');
                    const additionalMessageContent = additionalMessageDiv.querySelector('.message-content');
            
                    if ((serialNumber && !issueDescription) || (!serialNumber && issueDescription)) {
                        // Use the messageDiv pattern for additional issues
                        additionalMessageDiv.className = 'assetMessage mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded';
                        additionalMessageTitle.textContent = 'Error!';
                        additionalMessageContent.textContent = 'Please fill in both serial number and issue description';
                        additionalMessageDiv.classList.remove('hidden');
                        hasInvalidAdditionalIssue = true;
                    } else if (serialNumber && issueDescription) {
                        issues.push({
                            serial_number: serialNumber,
                            issue_description: issueDescription
                        });
                    }
                });
                
                if (hasInvalidAdditionalIssue) {
                    return;
                }
            
                // Add issues to form data
                const issuesInput = document.createElement('input');
                issuesInput.type = 'hidden';
                issuesInput.name = 'asset_issues';
                issuesInput.value = JSON.stringify(issues);
                this.appendChild(issuesInput);
                
                // Submit the form
                this.submit();
            }
            // If no issues, the form will submit normally without additional fields
        });
    }
});
</script>