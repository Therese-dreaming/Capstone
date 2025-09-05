@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6">
    <div class="mb-6 flex items-center">
        <a href="{{ route('locations.index') }}" class="flex items-center text-gray-600 hover:text-red-800 transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            Back to Locations
        </a>
    </div>

    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg">
        <!-- Header with icon -->
        <div class="flex items-center mb-6">
            <div class="bg-red-800 p-3 rounded-full mr-4 shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Create New Location</h1>
        </div>
        
        @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-md">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-red-800 font-medium">Please correct the following errors:</h3>
            </div>
            <ul class="list-disc list-inside text-sm text-red-700">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <form action="{{ route('locations.store') }}" method="POST" id="locationForm">
            @csrf
            <div class="space-y-5">
                <!-- Building Dropdown -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Building</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <select name="building" id="building" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" required>
                            <option value="">Select Building</option>
                            <option value="FR. Smits Building" {{ old('building') == 'FR. Smits Building' ? 'selected' : '' }}>FR. Smits Building</option>
                            <option value="Msgr. Gabriel Building" {{ old('building') == 'Msgr. Gabriel Building' ? 'selected' : '' }}>Msgr. Gabriel Building</option>
                            <option value="Msgr. Sunga Building" {{ old('building') == 'Msgr. Sunga Building' ? 'selected' : '' }}>Msgr. Sunga Building</option>
                            <option value="Bishop San Diego Building" {{ old('building') == 'Bishop San Diego Building' ? 'selected' : '' }}>Bishop San Diego Building</option>
                            <option value="Fr. Carlos Building" {{ old('building') == 'Fr. Carlos Building' ? 'selected' : '' }}>Fr. Carlos Building</option>
                            <option value="Fr. Urbano Building" {{ old('building') == 'Fr. Urbano Building' ? 'selected' : '' }}>Fr. Urbano Building</option>
                            <option value="Fr. Joseph Building" {{ old('building') == 'Fr. Joseph Building' ? 'selected' : '' }}>Fr. Joseph Building</option>
                            <option value="Facade Building" {{ old('building') == 'Facade Building' ? 'selected' : '' }}>Facade Building</option>
                        </select>
                    </div>
                </div>
                
                <!-- Floor Dropdown -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Floor</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                            </svg>
                        </div>
                        <select name="floor" id="floor" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" required>
                            <option value="">Select Floor</option>
                        </select>
                    </div>
                </div>
                
                <!-- Room Dropdown -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room/Office</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <select name="room_number" id="room_number" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            <option value="">Select Room/Office</option>
                        </select>
                    </div>
                </div>

                <!-- Other Room Input (hidden by default) -->
                <div id="otherRoomDiv" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Specify Room/Office</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input type="text" name="other_room" id="other_room" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" placeholder="Enter custom room/office name" value="{{ old('other_room') }}">
                    </div>
                </div>
                
                <!-- Add this hidden input after the room_number select -->
                <input type="hidden" name="final_room_number" id="final_room_number" value="">
                
                <div class="pt-4">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('locations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-800 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Create Location
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Help Card -->
    <div class="max-w-md mx-auto mt-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Location Information</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Locations are used to track where assets are physically located. Select from the predefined options or choose "Other" to specify a custom room.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
console.log('JavaScript is loading...');

// Location data from CSV
const locationData = {
    "FR. Smits Building": {
        "Ground Floor": [
            "Concessionaire",
            "Electrician Quarters",
            "Janitor's Quarter",
            "HS Electrical/Electronic Drafting Room",
            "HS Sewing Room",
            "HS Woodworking Room",
            "Stock Issuance Section",
            "Purchasing Office",
            "GSU Working Area",
            "Kios",
            "ABCI",
            "GSU",
            "Bookstore Stockroom",
            "Bookstore"
        ],
        "2nd Floor": [
            "College Faculty",
            "HS Rooms 52–55",
            "Speech Lab Rooms 56–57",
            "Mini Hotel",
            "SHS Classroom",
            "Security Guard Barracks"
        ],
        "3rd Floor": [
            "HS Rooms 59–62",
            "HS Laboratory Rooms 63–64",
            "President's Hall",
            "Caregiver Lecture Room"
        ],
        "4th Floor": [
            "HS Rooms 65–69, 74",
            "HS Laboratory Rooms 70–71",
            "Apicius Rooms 72–73",
            "HS Laboratory Stockroom"
        ]
    },
    "Msgr. Gabriel Building": {
        "Ground Floor": [
            "ECE Multimedia",
            "ECE Rooms 1–7",
            "ACU Canteen"
        ],
        "2nd Floor": [
            "College Dean's Office",
            "SHS Principal's Office",
            "SHS Faculty Room",
            "SHS OSA",
            "College Guidance Office",
            "Conference Room",
            "Office of the President",
            "Office of Student Affairs and Services",
            "Testing Area",
            "Institutional Office of Student Services and Affairs",
            "Physical Plant Office & PCO",
            "Veranda",
            "Control Booth",
            "SHS Rooms 211–214",
            "SHS Classroom Caceres"
        ],
        "3rd Floor": [
            "HS Library",
            "SHS Library",
            "College Library",
            "Grade School Library",
            "Caregiver Lecture Room 3",
            "SGS Conference Room",
            "SGS Office",
            "VPRPDEO Headquarters",
            "VPRPDEO Office"
        ],
        "4th Floor": [
            "Computer Labs 401–409",
            "ICTC"
        ],
        "5th Floor": [
            "SHS Rooms 510–520",
            "Science Labs 1–2"
        ],
        "6th Floor": [
            "St. Joseph Gymnasium",
            "Aula Minor"
        ]
    },
    "Msgr. Sunga Building": {
        "Ground Floor": [
            "Security Office",
            "Mini Stage",
            "Pastoral",
            "JHS OSA",
            "JHS Principal's Office",
            "Registrar's Office",
            "EVP",
            "CFO",
            "Sister's Convention",
            "IOSA",
            "CPARTS"
        ],
        "2nd Floor": [
            "JHS Faculty",
            "HS Guidance",
            "Testing Room",
            "SHS Rooms 206–208",
            "ACADE"
        ],
        "3rd Floor": [
            "JHS Rooms 401–408"
        ],
        "4th Floor": [
            "JHS Rooms 401–408"
        ],
        "5th Floor": [
            "JHS Rooms 501–508"
        ],
        "Mezzanine Floor": [
            "HS OSA",
            "Pastoral Storage",
            "JHS Academic Office",
            "Registrar Files Room",
            "EVP Storage",
            "EVP Board Room",
            "CCF Board Room",
            "Sister's Convent"
        ]
    },
    "Bishop San Diego Building": {
        "Ground Floor": [
            "Pump Room",
            "Main Canteen",
            "Kitchen",
            "Sto. Niño Chapel"
        ],
        "2nd Floor": [
            "Seminar Room",
            "San Pedro Calungsod",
            "ROTC Room"
        ],
        "3rd Floor": [
            "VPAA Conference",
            "VPAA",
            "HS Reading Center"
        ],
        "4th Floor": [
            "GS Home Economics Lab"
        ],
        "5th Floor": [
            "SHS Rooms SD3–SD5"
        ],
        "Mezzanine Floor": [
            "Canteen Eating Area"
        ]
    },
    "Fr. Carlos Building": {
        "Ground Floor": [
            "ERP Room 1",
            "Grade School Rooms 2–5"
        ],
        "2nd Floor": [
            "GS Rooms 11–15"
        ],
        "3rd Floor": [
            "GS Rooms 29–33"
        ]
    },
    "Fr. Urbano Building": {
        "Ground Floor": [
            "Medical Dental Clinic",
            "GS Guidance",
            "Grade School Principal's Office",
            "GS Faculty"
        ],
        "2nd Floor": [
            "GS Rooms 23–28",
            "GS Reading Center Rooms 21–22"
        ],
        "3rd Floor": [
            "GS Rooms 30–45"
        ],
        "4th Floor": [
            "GS Rooms 46–53"
        ]
    },
    "Fr. Joseph Building": {
        "Ground Floor": [
            "GS Rooms 6–10"
        ],
        "2nd Floor": [
            "Stock Room",
            "GS ACA",
            "GS Rooms 16–20"
        ],
        "3rd Floor": [
            "GS MAPE Faculty",
            "GS Rooms 34–38"
        ]
    },
    "Facade Building": {
        "Ground Floor": [
            "Finance",
            "Treasury",
            "Accounting",
            "Budget",
            "CHRMD (HR)",
            "ACU Canteen"
        ],
        "2nd Floor": [
            "Alumni Conference Room",
            "Alumni Office",
            "Archive Room",
            "CEA Room"
        ]
    }
};

console.log('Location data loaded');

// Building change handler
document.getElementById('building').addEventListener('change', function() {
    console.log('Building changed to:', this.value);
    const building = this.value;
    const floorSelect = document.getElementById('floor');
    const roomSelect = document.getElementById('room_number');
    const otherRoomDiv = document.getElementById('otherRoomDiv');
    const otherRoomInput = document.getElementById('other_room');
    
    // Clear floor and room options
    floorSelect.innerHTML = '<option value="">Select Floor</option>';
    roomSelect.innerHTML = '<option value="">Select Room/Office</option>';
    otherRoomDiv.classList.add('hidden');
    otherRoomInput.value = '';
    
    if (building && locationData[building]) {
        // Populate floor options
        Object.keys(locationData[building]).forEach(floor => {
            const option = document.createElement('option');
            option.value = floor;
            option.textContent = floor;
            floorSelect.appendChild(option);
        });
    }
});

// Floor change handler
document.getElementById('floor').addEventListener('change', function() {
    const building = document.getElementById('building').value;
    const floor = this.value;
    const roomSelect = document.getElementById('room_number');
    const otherRoomDiv = document.getElementById('otherRoomDiv');
    const otherRoomInput = document.getElementById('other_room');
    
    // Clear room options
    roomSelect.innerHTML = '<option value="">Select Room/Office</option>';
    otherRoomDiv.classList.add('hidden');
    otherRoomInput.value = '';
    
    if (building && floor && locationData[building] && locationData[building][floor]) {
        // Populate room options
        locationData[building][floor].forEach(room => {
            const option = document.createElement('option');
            option.value = room;
            option.textContent = room;
            roomSelect.appendChild(option);
        });
        
        // Add "Other" option
        const otherOption = document.createElement('option');
        otherOption.value = 'Other';
        otherOption.textContent = 'Other';
        roomSelect.appendChild(otherOption);
    }
});

// Room change handler
document.getElementById('room_number').addEventListener('change', function() {
    const otherRoomDiv = document.getElementById('otherRoomDiv');
    const otherRoomInput = document.getElementById('other_room');
    
    if (this.value === 'Other') {
        otherRoomDiv.classList.remove('hidden');
        otherRoomInput.required = true;
        // Don't set required on room_number when Other is selected
    } else {
        otherRoomDiv.classList.add('hidden');
        otherRoomInput.required = false;
        otherRoomInput.value = '';
        // Don't set required on room_number for predefined rooms either
    }
});

// Form submission handler
document.getElementById('locationForm').addEventListener('submit', function(e) {
    try {
        console.log('Form submit event fired!');
        
        const roomSelect = document.getElementById('room_number');
        const otherRoomInput = document.getElementById('other_room');
        const finalRoomInput = document.getElementById('final_room_number');
        
        console.log('=== FORM SUBMISSION DEBUG ===');
        console.log('roomSelect.value before:', roomSelect.value);
        console.log('otherRoomInput.value before:', otherRoomInput.value);
        
        // Check if any room is selected
        if (!roomSelect.value || roomSelect.value === '') {
            e.preventDefault();
            alert('Please select a room/office.');
            roomSelect.focus();
            return;
        }
        
        let finalRoomValue = roomSelect.value;
        
        // If "Other" is selected, validate and prepare the data
        if (roomSelect.value === 'Other') {
            console.log('Other selected, validating...');
            if (otherRoomInput.value.trim() === '') {
                e.preventDefault();
                alert('Please specify the room/office name.');
                otherRoomInput.focus();
                return;
            }
            finalRoomValue = otherRoomInput.value.trim();
            console.log('Updated finalRoomValue to:', finalRoomValue);
        }
        
        // Set the final room value in the hidden field
        finalRoomInput.value = finalRoomValue;
        
        console.log('roomSelect.value after:', roomSelect.value);
        console.log('finalRoomValue:', finalRoomValue);
        console.log('=== END DEBUG ===');
        
        // Let the form submit normally with the updated data
        
    } catch (error) {
        console.error('Error in form submission handler:', error);
    }
});

// Initialize form with old values if any
document.addEventListener('DOMContentLoaded', function() {
    const building = document.getElementById('building').value;
    const floor = '{{ old("floor") }}';
    const room = '{{ old("room_number") }}';
    
    if (building) {
        // Trigger building change to populate floors
        document.getElementById('building').dispatchEvent(new Event('change'));
        
        if (floor) {
            // Set floor and trigger change
            document.getElementById('floor').value = floor;
            document.getElementById('floor').dispatchEvent(new Event('change'));
            
            if (room) {
                if (room === '{{ old("other_room") }}') {
                    // This was a custom room
                    document.getElementById('room_number').value = 'Other';
                    document.getElementById('room_number').dispatchEvent(new Event('change'));
                    document.getElementById('other_room').value = room;
                } else {
                    // This was a predefined room
                    document.getElementById('room_number').value = room;
                }
            }
        }
    }
});
</script>
@endsection