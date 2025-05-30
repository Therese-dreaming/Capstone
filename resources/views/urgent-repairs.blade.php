@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8">
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
        <strong class="font-bold">Success!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4 md:gap-0">
        <h2 class="text-2xl font-semibold">Urgent Repairs</h2>
        <a href="{{ route('repair.status') }}" class="bg-[#960106] text-white px-4 py-2 rounded hover:bg-red-700 transition-colors w-full md:w-auto text-center">View All Requests</a>
    </div>

    <!-- Urgent Repairs Table -->
    @if($urgentRepairs->count() > 0)
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <div class="bg-[#960106] text-white p-4 rounded-t-lg">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="text-xl font-semibold">URGENT REPAIRS ({{ $urgentRepairs->count() }})</h3>
            </div>
        </div>

        <div class="p-2 md:p-4">
            <table class="min-w-full text-xs md:text-sm">
                <thead class="bg-[#960106] text-white">
                    <tr>
                        <th class="px-2 md:px-6 py-3 text-left text-sm font-medium">Request Date</th>
                        <th class="px-2 md:px-6 py-3 text-left text-sm font-medium">Ticket No.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($urgentRepairs as $request)
                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="navigateToRequest('{{ $request->id }}')">
                        <td class="px-2 md:px-6 py-4">{{ \Carbon\Carbon::parse($request->created_at)->format('M j, Y (g:i A)') }}</td>
                        <td class="px-2 md:px-6 py-4 font-medium">{{ $request->ticket_number ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <div class="flex flex-col items-center justify-center space-y-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900">All Clear!</h3>
            <p class="text-gray-600">There are no urgent repairs that need attention at the moment.</p>
        </div>
    </div>
    @endif
</div>

<!-- Include the modals -->
@include('partials.update-modal')
@include('partials.complete-modal')

<script>
    // Add this new function
    function navigateToRequest(requestId) {
        // Store the request ID in sessionStorage
        sessionStorage.setItem('highlightRequestId', requestId);
        // Navigate to repair status page
        window.location.href = '{{ route('repair.status') }}';
    }

    function openUpdateModal(requestId) {
        const modal = document.getElementById('updateModal');
        const form = document.getElementById('updateForm');
        form.action = `/repair-requests/${requestId}`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeUpdateModal() {
        const modal = document.getElementById('updateModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function openCompleteModal(requestId) {
        const modal = document.getElementById('completeModal');
        const form = document.getElementById('completeForm');
        form.action = `/repair-requests/${requestId}`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeCompleteModal() {
        const modal = document.getElementById('completeModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // Add CSRF token to all AJAX requests
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.getElementById('updateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        fetch(this.action, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    });

    document.getElementById('completeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        fetch(this.action, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    });
</script>

@endsection