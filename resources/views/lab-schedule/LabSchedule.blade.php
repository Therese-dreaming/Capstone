@extends('layouts.app')

@section('content')
<div class="flex-1 ml-80">
    <div class="p-6">
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

        <!-- Main Container -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold">Laboratory Schedule</h1>
                    <p class="text-gray-600">Manage and view laboratory reservations across all departments</p>
                </div>
                <button id="newScheduleBtn" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    New Schedule
                </button>
            </div>

            <!-- Replace the Laboratory Filters section -->
            <div class="mb-6">
                <div class="max-w-xs">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Select Laboratory</label>
                    <select id="labFilter" class="w-full border rounded p-2 text-sm bg-white">
                        @foreach($laboratories as $lab)
                        <option value="{{ $lab }}" {{ $lab == '401' ? 'selected' : '' }}> {{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Calendar -->
            <div id="calendar" class="fc-container"></div>
        </div>
    </div>
</div>

<!-- Schedule Modal -->
<div id="scheduleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto">
        <div class="bg-red-800 text-white px-4 py-3 rounded-t-lg flex justify-between items-center">
            <h3 class="text-lg font-bold">New Laboratory Schedule</h3>
            <button type="button" id="closeModalX" class="text-white hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="scheduleForm" class="p-6">
            <div class="space-y-4">
                <!-- Time Selection (Moved to top) -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium">Start Time</label>
                        <input type="datetime-local" name="start" class="w-full border rounded p-2 text-sm" required step="1800">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium">End Time</label>
                        <input type="datetime-local" name="end" class="w-full border rounded p-2 text-sm" required step="1800">
                    </div>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium">Department</label>
                    <select name="department" class="w-full border rounded p-2 text-sm" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium">Laboratory</label>
                    <select name="laboratory" class="w-full border rounded p-2 text-sm" required>
                        @foreach($laboratories as $lab)
                        <option value="{{ $lab }}">{{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium">Subject/Course</label>
                    <input type="text" name="subject_course" class="w-full border rounded p-2 text-sm" required>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium">Professor</label>
                    <select name="professor_id" class="w-full border rounded p-2 text-sm" required>
                        <option value="">Select Professor</option>
                        @foreach($faculty as $professor)
                        <option value="{{ $professor->id }}">{{ $professor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium">Collaborator</label>
                    <select name="collaborator_id" class="w-full border rounded p-2 text-sm" required>
                        <option value="">Select Coordinator</option>
                        @foreach($coordinators as $coordinator)
                        <option value="{{ $coordinator->id }}">{{ $coordinator->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" id="closeModal" class="px-4 py-2 border rounded text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-800 text-white rounded text-sm">Save Schedule</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Fix calendar display issues */
    .fc-container {
        height: 700px;
        width: 100%;
        margin: 0 auto;
        overflow-y: auto;
        /* Enable vertical scrolling */
    }

    .fc-view-harness {
        height: 100% !important;
        /* Ensure the view takes full height */
    }

    .fc-scroller {
        overflow: hidden !important;
        /* Prevent double scrollbars */
    }

    .fc-timegrid.fc-timeGridWeek-view {
        overflow: visible !important;
        /* Allow content to be visible within container */
    }

    .fc-timegrid-slot {
        height: 40px !important;
    }

    .fc-scrollgrid-sync-inner {
        text-align: center;
    }

    .fc-col-header-cell {
        background-color: #f3f4f6;
        padding: 8px 0;
    }

    .fc-timegrid-event {
        border-radius: 4px;
        padding: 2px 4px;
        min-height: 40px !important;
        overflow: visible !important;
        background-color: #991b1b !important;
        border-color: #7f1d1d !important;
        color: white !important;
        padding: 2px 4px !important;
    }

    .fc-daygrid-event {
        background-color: #991b1b !important;
        border-color: #7f1d1d !important;
        color: white !important;
        padding: 2px 4px !important;
        border-radius: 4px !important;
    }

    .fc-daygrid-dot-event {
        background-color: #991b1b !important;
        border-color: #7f1d1d !important;
        color: white !important;
    }

    .fc-daygrid-event .fc-event-title {
        color: white !important;
        font-weight: normal !important;
    }

    .event-content {
        overflow: visible;
        white-space: normal;
        line-height: 1.2;
        color: white !important;
    }

    /* Toolbar styling */
    .fc-toolbar {
        margin-bottom: 1rem !important;
    }

    .fc-toolbar-title {
        font-size: 1.25rem !important;
        font-weight: 600 !important;
    }

    .fc-button-primary {
        background-color: #991b1b !important;
        border-color: #991b1b !important;
    }

    .fc-button-primary:hover {
        background-color: #7f1d1d !important;
        border-color: #7f1d1d !important;
    }

    .fc-button-active {
        background-color: #7f1d1d !important;
        border-color: #7f1d1d !important;
    }

    /* Ensure the calendar is responsive */
    @media (max-width: 768px) {
        .fc-container {
            height: 500px;
        }

        .fc-toolbar {
            flex-direction: column;
        }

        .fc-toolbar-chunk {
            margin-bottom: 10px;
        }
    }

</style>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const modal = document.getElementById('scheduleModal');
        const form = document.getElementById('scheduleForm');
        const closeBtn = document.getElementById('closeModal');
        const closeXBtn = document.getElementById('closeModalX');
        const newScheduleBtn = document.getElementById('newScheduleBtn');
        const labFilter = document.getElementById('labFilter');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek'
            , headerToolbar: {
                left: 'prev,next today'
                , center: 'title'
                , right: 'dayGridMonth,timeGridWeek,timeGridDay'
            }
            , events: function(info, successCallback, failureCallback) {
                fetch('/lab-schedule/events')
                    .then(response => response.json())
                    .then(events => {
                        const selectedLab = labFilter.value;
                        // If no laboratory is selected (empty select), show no events
                        if (!selectedLab) {
                            successCallback([]);
                            return;
                        }
                        
                        // Filter events based on laboratory
                        let filteredEvents = events.filter(event => {
                            return event.laboratory === selectedLab;
                        });
                        
                        successCallback(filteredEvents);
                    })
                    .catch(error => {
                        console.error('Error fetching events:', error);
                        failureCallback(error);
                    });
            }
            , selectable: true
            , selectMirror: false, // Changed from true to false to remove the highlight effect
            slotEventOverlap: false
            , eventDisplay: 'block'
            , select: function(info) {
                // Only show modal if not in month view
                if (calendar.view.type !== 'dayGridMonth') {
                    form.elements.start.value = info.startStr.slice(0, 16);
                    form.elements.end.value = info.endStr.slice(0, 16);
                    modal.classList.remove('hidden');
                }
            }
            , dateClick: function(info) {
                if (calendar.view.type === 'dayGridMonth') {
                    // Only change view in month view, no modal
                    calendar.changeView('timeGridDay', info.date);
                } else if (calendar.view.type === 'timeGridWeek' || calendar.view.type === 'timeGridDay') {
                    // Show modal only in week or day view
                    const clickedDate = new Date(info.dateStr);

                    // Ensure we're using the clicked time, not defaulting to any specific time
                    const hours = clickedDate.getHours();
                    const minutes = clickedDate.getMinutes();

                    // Create end date 1 hour later while preserving the exact clicked time
                    const endDate = new Date(clickedDate);
                    endDate.setHours(hours + 1);
                    endDate.setMinutes(minutes);

                    // Format for datetime-local input while preserving the exact time
                    const formatDateForInput = (date) => {
                        const pad = (num) => String(num).padStart(2, '0');
                        return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
                    };

                    const startStr = formatDateForInput(clickedDate);
                    const endStr = formatDateForInput(endDate);

                    form.elements.start.value = startStr;
                    form.elements.end.value = endStr;
                    modal.classList.remove('hidden');
                }
            }
            , eventContent: function(arg) {
                // For month view, only show time
                if (calendar.view.type === 'dayGridMonth') {
                    const startTime = arg.event.start.toLocaleTimeString('en-US', {
                        hour: '2-digit'
                        , minute: '2-digit'
                        , hour12: false
                    });
                    const endTime = arg.event.end.toLocaleTimeString('en-US', {
                        hour: '2-digit'
                        , minute: '2-digit'
                        , hour12: false
                    });
                    return {
                        html: `<div class="event-content p-1">
                            <div>${startTime} - ${endTime}</div>
                            <div class="text-xs">${arg.event.extendedProps.subject_course || 'N/A'}</div>
                        </div>`
                    };
                }

                // For week and day view, show full details
                return {
                    html: `
                        <div class="event-content p-1">
                            <div class="text-xs font-medium mb-1">
                                ${arg.event.extendedProps.subject_course || 'N/A'}
                            </div>
                            <div class="text-xs">
                                Professor: ${arg.event.extendedProps.professor || 'N/A'}
                            </div>
                        </div>
                    `
                };
            }
            , eventDidMount: function(info) {
                // Ensure proper time display and spanning
                info.el.style.width = '100%';
                info.el.style.maxHeight = 'none'; // Allow the event to expand
            }
            , height: 'auto'
            , allDaySlot: false
            , slotDuration: '00:30:00'
            , slotMinTime: '05:00:00'
            , slotMaxTime: '24:00:00'
            , expandRows: true
            , nowIndicator: true
            , dayMaxEvents: true
            , eventTimeFormat: {
                hour: '2-digit'
                , minute: '2-digit'
                , meridiem: false
                , hour12: false
            }
            , themeSystem: 'standard'
        });

        calendar.render();

        // Filter events when selection changes
        labFilter.addEventListener('change', function() {
            calendar.refetchEvents();
        });

        // Update form's laboratory select when adding new schedule
        newScheduleBtn.addEventListener('click', () => {
            const now = new Date();
            const start = new Date(now);
            start.setHours(start.getHours() + 1, 0, 0, 0);
            const end = new Date(start);
            end.setHours(end.getHours() + 1);
            setModalTimes(start.toISOString(), end.toISOString());
            modal.classList.remove('hidden');
        });

        closeBtn.addEventListener('click', closeModal);
        closeXBtn.addEventListener('click', closeModal);

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            submitSchedule(data);
        });

        function setModalTimes(startStr, endStr) {
            form.elements.start.value = startStr.slice(0, 16);
            form.elements.end.value = endStr.slice(0, 16);
        }

        function closeModal() {
            modal.classList.add('hidden');
            form.reset();
        }

        function showMessage(message, type) {
            const messageContainer = document.createElement('div');
            messageContainer.className = `mb-4 p-4 ${type === 'success' ? 'bg-green-100 border-l-4 border-green-500 text-green-700' : 'bg-red-100 border-l-4 border-red-500 text-red-700'}`;
            messageContainer.innerText = message;
            document.querySelector('.p-6').prepend(messageContainer);
            setTimeout(() => {
                messageContainer.remove();
            }, 5000);
        }

        function submitSchedule(data) {
            fetch('/lab-schedule', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data) return; // If redirected, data will be undefined
                    if (data.message) {
                        closeModal();
                        showMessage(data.message, 'error');
                        return;
                    }
                    closeModal();
                    calendar.refetchEvents();
                    showMessage('Schedule created successfully!', 'success');
                })
                .catch(error => {
                    console.error('Error:', error);
                    closeModal();
                    showMessage('An error occurred while creating the schedule', 'error');
                });
        }
    });

</script>
@endsection
