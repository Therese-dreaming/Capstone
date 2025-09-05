@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Back navigation -->
    <div class="mb-4 md:mb-6">
        <a href="{{ route('locations.index') }}" class="inline-flex items-center text-gray-600 hover:text-red-800 transition-colors duration-200 text-sm md:text-base">
            <svg class="w-4 h-4 md:w-5 md:h-5 mr-1 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            <span>Back to Locations</span>
        </a>
    </div>

    <!-- Page Header with Background Design -->
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                        <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Edit Location</h1>
                        <p class="text-red-100 text-sm md:text-lg">Update location information</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main form card -->
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
            @if ($errors->any())
            <div class="mb-6 p-4 md:p-6 bg-red-50 border border-red-200 rounded-xl text-red-700">
                <div class="flex items-center mb-3">
                    <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-red-800 font-medium text-sm md:text-base">Please correct the following errors:</h3>
                </div>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('locations.update', $location) }}" method="POST" id="locationForm">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <!-- Building Dropdown -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Building</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none pl-3">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <select name="building" id="building" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" required>
                                <option value="">Select Building</option>
                                <option value="FR. Smits Building" {{ old('building', $location->building) == 'FR. Smits Building' ? 'selected' : '' }}>FR. Smits Building</option>
                                <option value="Msgr. Gabriel Building" {{ old('building', $location->building) == 'Msgr. Gabriel Building' ? 'selected' : '' }}>Msgr. Gabriel Building</option>
                                <option value="Msgr. Sunga Building" {{ old('building', $location->building) == 'Msgr. Sunga Building' ? 'selected' : '' }}>Msgr. Sunga Building</option>
                                <option value="Bishop San Diego Building" {{ old('building', $location->building) == 'Bishop San Diego Building' ? 'selected' : '' }}>Bishop San Diego Building</option>
                                <option value="Fr. Carlos Building" {{ old('building', $location->building) == 'Fr. Carlos Building' ? 'selected' : '' }}>Fr. Carlos Building</option>
                                <option value="Fr. Urbano Building" {{ old('building', $location->building) == 'Fr. Urbano Building' ? 'selected' : '' }}>Fr. Urbano Building</option>
                                <option value="Fr. Joseph Building" {{ old('building', $location->building) == 'Fr. Joseph Building' ? 'selected' : '' }}>Fr. Joseph Building</option>
                                <option value="Facade Building" {{ old('building', $location->building) == 'Facade Building' ? 'selected' : '' }}>Facade Building</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Floor Dropdown -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Floor</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                </svg>
                            </div>
                            <select name="floor" id="floor" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" required>
                                <option value="">Select Floor</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Room Dropdown -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Room/Office</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <select name="room_number" id="room_number" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" required>
                                <option value="">Select Room/Office</option>
                            </select>
                        </div>
                    </div>

                    <!-- Other Room Input (hidden by default) -->
                    <div id="otherRoomDiv" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Specify Room/Office</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="text" name="other_room" id="other_room" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" placeholder="Enter custom room/office name" value="{{ old('other_room') }}">
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-6">
                        <a href="{{ route('locations.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-red-800 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Update Location
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Location data from CSV (same as create form)
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

// Building change handler
document.getElementById('building').addEventListener('change', function() {
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
    } else {
        otherRoomDiv.classList.add('hidden');
        otherRoomInput.required = false;
        otherRoomInput.value = '';
    }
});

// Form submission handler
document.getElementById('locationForm').addEventListener('submit', function(e) {
    const roomSelect = document.getElementById('room_number');
    const otherRoomInput = document.getElementById('other_room');
    
    // If "Other" is selected, use the custom input value
    if (roomSelect.value === 'Other') {
        if (otherRoomInput.value.trim() === '') {
            e.preventDefault();
            alert('Please specify the room/office name.');
            otherRoomInput.focus();
            return;
        }
        // Replace the "Other" value with the custom input
        roomSelect.value = otherRoomInput.value.trim();
    }
});

// Initialize form with current values
document.addEventListener('DOMContentLoaded', function() {
    const building = '{{ $location->building }}';
    const floor = '{{ $location->floor }}';
    const room = '{{ $location->room_number }}';
    
    if (building) {
        // Set building and trigger change
        document.getElementById('building').value = building;
        document.getElementById('building').dispatchEvent(new Event('change'));
        
        if (floor) {
            // Set floor and trigger change
            document.getElementById('floor').value = floor;
            document.getElementById('floor').dispatchEvent(new Event('change'));
            
            if (room) {
                // Check if room exists in predefined options
                const roomSelect = document.getElementById('room_number');
                const roomExists = Array.from(roomSelect.options).some(option => option.value === room);
                
                if (roomExists) {
                    // This is a predefined room
                    roomSelect.value = room;
                } else {
                    // This is a custom room
                    roomSelect.value = 'Other';
                    roomSelect.dispatchEvent(new Event('change'));
                    document.getElementById('other_room').value = room;
                }
            }
        }
    }
});
</script>
@endsection