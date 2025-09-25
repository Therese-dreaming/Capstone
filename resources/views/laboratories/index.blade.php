@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8">
	@if(session('success'))
	<div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
		{{ session('success') }}
	</div>
	@endif

	<div class="mb-6 md:mb-8">
		<div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
			<div class="flex items-center justify-between">
				<div class="flex items-center">
					<div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
						<svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
						</svg>
					</div>
					<div>
						<h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Laboratories</h1>
						<p class="text-red-100 text-sm md:text-lg">Manage laboratory rooms for lab logging</p>
					</div>
				</div>
				<a href="{{ route('laboratories.create') }}" class="inline-flex items-center px-4 py-2 bg-white/20 text-white font-medium rounded-lg hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 focus:ring-offset-2 focus:ring-offset-red-800 transition-colors duration-200">
					<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
					</svg>
					Add Laboratory
				</a>
			</div>
		</div>
	</div>

	<!-- Delete Laboratory Modal -->
	<div id="deleteLabModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
		<div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
			<div class="mt-1">
				<div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
					<svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
					</svg>
				</div>
				<h3 class="text-lg leading-6 font-medium text-gray-900 mt-4 text-center" id="deleteLabTitle">Delete Laboratory</h3>
				<p class="text-sm text-gray-500 text-center mt-2">Are you sure you want to delete this laboratory? This action cannot be undone.</p>
				<div class="mt-5 flex justify-center space-x-4">
					<button type="button" onclick="closeLabDeleteModal()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">Cancel</button>
					<form id="deleteLabForm" method="POST" class="inline">
						@csrf
						@method('DELETE')
						<button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">Delete</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Toolbar -->
	<div class="bg-white rounded-xl shadow-lg p-4 md:p-6 mb-6 md:mb-8">
		<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
			<div class="flex items-center gap-3">
				<div class="bg-red-50 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
					Total Laboratories: {{ number_format($labs->total()) }}
				</div>
			</div>
			<div class="flex items-center gap-3 w-full md:w-auto">
				<div class="relative flex-1 md:flex-initial">
					<span class="absolute p-2 inset-y-0 left-3 flex items-center text-gray-400">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
					</span>
					<input id="labSearch" type="text" placeholder="Search" class="pl-9 p-6 w-full h-10 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm" />
				</div>
			</div>
		</div>
	</div>

	<!-- Desktop Table -->
	<div class="hidden md:block bg-white rounded-xl shadow-lg p-4 md:p-6">
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-gray-200" id="labsTable">
				<thead class="bg-gray-50">
					<tr>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Number</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
					</tr>
				</thead>
				<tbody class="bg-white divide-y divide-gray-200">
					@forelse($labs as $lab)
					@php
						$parts = [];
						if ($lab->building) { $parts[] = $lab->building; }
						if ($lab->floor) { $parts[] = 'Floor ' . $lab->floor; }
						if ($lab->room_number) { $parts[] = 'Room ' . $lab->room_number; }
						$locationDisplay = $parts ? implode(' • ', $parts) : '—';
					@endphp
					<tr class="hover:bg-gray-50 transition-colors">
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
							<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">{{ $lab->number }}</span>
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
							<div class="flex items-center gap-2">
								<svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292" />
								</svg>
								<span>{{ $lab->name ?? '—' }}</span>
							</div>
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
							<div class="flex items-center gap-2">
								<svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
								</svg>
								<span>{{ $locationDisplay }}</span>
							</div>
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
							<div class="flex items-center space-x-1.5">
								<a href="{{ route('laboratories.edit', $lab) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors" title="Edit">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
									</svg>
								</a>
								<button type="button" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition-colors" title="Delete" onclick="openLabDeleteModal('{{ route('laboratories.destroy', $lab) }}', 'Laboratory {{ $lab->number }}')">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
									</svg>
								</button>
							</div>
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="4" class="px-6 py-12 text-center text-gray-500">
							<div class="flex flex-col items-center">
								<svg class="w-14 h-14 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2-2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
								</svg>
								<p class="text-lg font-medium">No laboratories found</p>
								<p class="text-sm">Create your first laboratory to get started</p>
								<a href="{{ route('laboratories.create') }}" class="mt-3 inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700">Add Laboratory</a>
							</div>
						</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
		<div class="mt-4">
			{{ $labs->links() }}
		</div>
	</div>

	<!-- Mobile Cards -->
	<div class="md:hidden space-y-4">
		@forelse($labs as $lab)
		@php
			$parts = [];
			if ($lab->building) { $parts[] = $lab->building; }
			if ($lab->floor) { $parts[] = 'Floor ' . $lab->floor; }
			if ($lab->room_number) { $parts[] = 'Room ' . $lab->room_number; }
			$locationDisplay = $parts ? implode(' • ', $parts) : '—';
		@endphp
		<div class="bg-white rounded-xl shadow p-4 border border-gray-200" data-searchable>
			<div class="flex items-center justify-between mb-2">
				<div class="flex items-center gap-2">
					<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">{{ $lab->number }}</span>
					<span class="text-sm font-medium text-gray-900">{{ $lab->name ?? '—' }}</span>
				</div>
				<div class="flex items-center gap-2">
					<a href="{{ route('laboratories.edit', $lab) }}" class="p-2 rounded-md bg-blue-50 text-blue-700 hover:bg-blue-100" title="Edit">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
						</svg>
					</a>
					<button type="button" class="p-2 rounded-md bg-red-50 text-red-700 hover:bg-red-100" title="Delete" onclick="openLabDeleteModal('{{ route('laboratories.destroy', $lab) }}', 'Laboratory {{ $lab->number }}')">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
						</svg>
					</button>
				</div>
			</div>
			<div class="text-sm text-gray-600">{{ $locationDisplay }}</div>
		</div>
		@empty
		<div class="text-center text-gray-500 bg-white rounded-xl border border-gray-200 p-6">
			<p class="font-medium">No laboratories found</p>
			<p class="text-sm">Create your first laboratory to get started</p>
			<a href="{{ route('laboratories.create') }}" class="mt-3 inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700">Add Laboratory</a>
		</div>
		@endforelse
	</div>
</div>

@section('scripts')
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const searchInput = document.getElementById('labSearch');
		if (!searchInput) return;

		function matches(text, term) {
			return text.toLowerCase().includes(term);
		}

		function filter() {
			const term = searchInput.value.trim().toLowerCase();
			// Table rows
			const rows = document.querySelectorAll('#labsTable tbody tr');
			rows.forEach(row => {
				if (!term) { row.classList.remove('hidden'); return; }
				const cellsText = Array.from(row.querySelectorAll('td')).map(td => td.innerText || '').join(' ');
				row.classList.toggle('hidden', !matches(cellsText, term));
			});

			// Mobile cards
			const cards = document.querySelectorAll('[data-searchable]');
			cards.forEach(card => {
				if (!term) { card.classList.remove('hidden'); return; }
				card.classList.toggle('hidden', !matches(card.innerText || '', term));
			});
		}

		searchInput.addEventListener('input', filter);
	});

	// Delete Modal Helpers
	function openLabDeleteModal(actionUrl, label) {
		const modal = document.getElementById('deleteLabModal');
		const form = document.getElementById('deleteLabForm');
		const title = document.getElementById('deleteLabTitle');
		if (form) form.action = actionUrl;
		if (title) title.textContent = `Delete ${label}`;
		if (modal) modal.classList.remove('hidden');
	}

	function closeLabDeleteModal() {
		const modal = document.getElementById('deleteLabModal');
		if (modal) modal.classList.add('hidden');
	}
</script>
@endsection
@endsection