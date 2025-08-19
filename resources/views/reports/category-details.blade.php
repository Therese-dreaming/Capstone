@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 bg-gray-50">
	<!-- Back navigation -->
	<div class="mb-4 md:mb-6 print-hide">
		<a href="{{ route('reports.category') }}" class="inline-flex items-center text-gray-600 hover:text-red-800 transition-colors duration-200 text-sm md:text-base">
			<svg class="w-4 h-4 md:w-5 md:h-5 mr-1 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
			</svg>
			<span>Back to Categories</span>
		</a>
	</div>

	<!-- Page Header with Background Design -->
	<div class="mb-6 md:mb-8 print-hide">
		<div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
			<div class="flex items-center justify-between">
				<div class="flex items-center">
					<div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
						<svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
						</svg>
					</div>
					<div>
						<h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">{{ $category->name }} Assets</h1>
						<p class="text-red-100 text-sm md:text-lg">Detailed Assets in this Category</p>
					</div>
				</div>
				<button onclick="printReport()" 
					class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-4 py-2 md:px-5 md:py-3 rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105 inline-flex items-center text-sm md:text-base">
					<svg class="w-4 h-4 md:w-5 md:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
					</svg>
					Print Report
				</button>
			</div>
		</div>
	</div>

	<!-- Summary cards -->
	<div class="bg-white rounded-xl shadow-md p-4 md:p-6 mb-6 print-hide">
		<h2 class="text-lg md:text-xl font-semibold text-gray-900 mb-4 md:mb-6 flex items-center">
			<svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
			</svg>
			Category Overview
		</h2>
		<div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
			<div class="bg-gradient-to-r from-red-50 to-red-100 p-4 md:p-6 rounded-xl border border-red-200">
				<p class="text-xs md:text-sm font-medium text-red-700 mb-1">Total Assets</p>
				<p class="text-lg md:text-2xl font-bold text-red-900">{{ number_format($assets->count()) }}</p>
			</div>
			<div class="bg-gradient-to-r from-green-50 to-green-100 p-4 md:p-6 rounded-xl border border-green-200">
				<p class="text-xs md:text-sm font-medium text-green-700 mb-1">Total Investment</p>
				<p class="text-lg md:text-2xl font-bold text-green-900">₱{{ number_format($assets->sum('purchase_price'), 2) }}</p>
			</div>
			<div class="bg-gradient-to-r from-purple-50 to-purple-100 p-4 md:p-6 rounded-xl border border-purple-200">
				<p class="text-xs md:text-sm font-medium text-purple-700 mb-1">Unique Models</p>
				<p class="text-lg md:text-2xl font-bold text-purple-900">{{ number_format($assets->pluck('model')->filter()->unique()->count()) }}</p>
			</div>
		</div>
	</div>

	<h1 class="print-only text-center text-xl font-bold">{{ $category->name }} Assets</h1>

	<!-- Assets Table (Desktop View) -->
	<div class="hidden md:block overflow-x-auto rounded-lg border border-gray-200 bg-white">
		<table class="min-w-full divide-y divide-gray-200">
			<thead class="bg-gray-50">
				<tr>
					<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
					<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
					<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
					<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
					<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
					<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Price</th>
				</tr>
			</thead>
			<tbody class="bg-white divide-y divide-gray-200">
				@foreach($assets as $asset)
				<tr class="hover:bg-gray-50 transition-colors duration-200">
					<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $asset->name }}</td>
					@php
						$loc = $asset->location;
						if (is_string($loc)) {
							$decoded = json_decode($loc, true);
							if (json_last_error() === JSON_ERROR_NONE) {
								$loc = $decoded;
							}
						}
						$building = is_array($loc) ? ($loc['building'] ?? null) : (is_object($loc) ? ($loc->building ?? null) : null);
						$floor = is_array($loc) ? ($loc['floor'] ?? null) : (is_object($loc) ? ($loc->floor ?? null) : null);
						$room = is_array($loc) ? ($loc['room_number'] ?? null) : (is_object($loc) ? ($loc->room_number ?? null) : null);
						$parts = [];
						if ($building) { $parts[] = $building; }
						if ($floor) { $parts[] = 'Floor ' . $floor; }
						if ($room) { $parts[] = $room; }
						$locationDisplay = $parts ? implode(' • ', $parts) : 'N/A';
					@endphp
					<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $locationDisplay }}</td>
					<td class="px-6 py-4 whitespace-nowrap text-sm">
						<span class="px-3 inline-flex text-xs leading-5 font-semibold rounded-full 
							@if($asset->status == 'IN USE') bg-green-100 text-green-800
							@elseif($asset->status == 'UNDER REPAIR') bg-yellow-100 text-yellow-800
							@else bg-red-100 text-red-800
							@endif">
							{{ $asset->status }}
						</span>
					</td>
					<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->model }}</td>
					<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
						<a href="{{ route('assets.index', ['search' => $asset->serial_number]) }}" class="font-bold text-red-600 hover:underline">{{ $asset->serial_number }}</a>
					</td>
					<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($asset->purchase_price, 2) }}</td>
				</tr>
				@endforeach
			</tbody>
			<tfoot class="bg-gray-50">
				<tr>
					<td colspan="5" class="px-6 py-3 text-sm font-medium text-gray-900 text-right">Total</td>
					<td class="px-6 py-3 text-sm font-medium text-gray-900">₱{{ number_format($assets->sum('purchase_price'), 2) }}</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<!-- Assets List (Mobile View) -->
	<div class="grid grid-cols-1 gap-4 md:hidden">
		@foreach($assets as $asset)
		<div class="bg-white rounded-xl shadow p-4 border border-gray-200">
			<div class="flex justify-between items-start mb-2">
				<div class="font-bold text-gray-900">{{ $asset->name }}</div>
				<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
					@if($asset->status == 'IN USE') bg-green-100 text-green-800
					@elseif($asset->status == 'UNDER REPAIR') bg-yellow-100 text-yellow-800
					@else bg-red-100 text-red-800
					@endif">
					{{ $asset->status }}
				</span>
			</div>
			@php
				$loc = $asset->location;
				if (is_string($loc)) {
					$decoded = json_decode($loc, true);
					if (json_last_error() === JSON_ERROR_NONE) {
						$loc = $decoded;
					}
				}
				$building = is_array($loc) ? ($loc['building'] ?? null) : (is_object($loc) ? ($loc->building ?? null) : null);
				$floor = is_array($loc) ? ($loc['floor'] ?? null) : (is_object($loc) ? ($loc->floor ?? null) : null);
				$room = is_array($loc) ? ($loc['room_number'] ?? null) : (is_object($loc) ? ($loc->room_number ?? null) : null);
				$parts = [];
				if ($building) { $parts[] = $building; }
				if ($floor) { $parts[] = 'Floor ' . $floor; }
				if ($room) { $parts[] = $room; }
				$locationDisplay = $parts ? implode(' • ', $parts) : 'N/A';
			@endphp
			<div class="text-sm text-gray-600 mb-1"><strong>Location:</strong> {{ $locationDisplay }}</div>
			<div class="text-sm text-gray-600 mb-1"><strong>Model:</strong> {{ $asset->model }}</div>
			<div class="text-sm text-gray-900 mb-1"><strong>Serial Number:</strong> <a href="{{ route('assets.index', ['search' => $asset->serial_number]) }}" class="font-bold text-red-600 hover:underline">{{ $asset->serial_number }}</a></div>
			<div class="text-sm text-gray-900"><strong>Purchase Price:</strong> ₱{{ number_format($asset->purchase_price, 2) }}</div>
		</div>
		@endforeach

		<!-- Total Purchase Price Summary for Mobile -->
		<div class="bg-gray-50 rounded-lg shadow p-4 flex justify-between items-center border border-gray-200">
			<div class="text-sm font-medium text-gray-900">Total Purchase Price</div>
			<div class="text-sm font-medium text-gray-900">₱{{ number_format($assets->sum('purchase_price'), 2) }}</div>
		</div>
	</div>
</div>

<script>
	function printReport() {
		window.print();
	}
</script>

<style>
	.print-only { display: none; }
	@media print {
		/* Hide navigation elements and buttons */
		aside.fixed,
		nav.bg-white,
		.sidebar-nav,
		header,
		.header,
		#header,
		[x-data],
		button,
		.print-hide,
		.grid.grid-cols-1.gap-4.md\:hidden { /* Hide mobile cards */
			display: none !important;
		}

		.print-only { display: block !important; }

		/* Ensure main content area is visible and uses full width */
		.flex-1.p-4,
		.flex-1.p-8 {
			 padding: 0 !important;
		}

		/* Remove left margin from main content added for sidebar */
		.md\:ml-80 {
			margin-left: 0 !important;
		}

		/* Ensure container is visible and uses full width */
		.max-w-6xl, .max-w-full {
			max-width: 100% !important;
			width: 100% !important;
		}

		/* Reset layout */
		body, html {
			margin: 0 !important;
			padding: 0 !important;
			width: 100% !important;
			background: white !important;
		}

		/* Style the title */
		h1 {
			text-align: center !important;
			margin-bottom: 20px;
			font-size: 18pt;
			width: 100% !important;
			display: block !important;
		}

		/* Ensure the table is visible and styled for print */
		.hidden.md\:block {
			display: block !important;
		}

		.overflow-x-auto {
			overflow-x: visible !important; /* Ensure table is not scrollable in print */
		}

		table {
			width: 100% !important;
			border-collapse: collapse;
			margin-top: 20px;
			table-layout: auto !important; /* Allow columns to size naturally */
		}

		th {
			background-color: #f3f4f6;
			color: #6b7280;
			font-size: 9pt !important;
			font-weight: 600;
			text-transform: uppercase;
			padding: 8px !important;
			text-align: left;
			white-space: normal !important;
		}

		td {
			padding: 8px !important;
			font-size: 9pt !important;
			border-bottom: 1px solid #e5e7eb;
			white-space: normal !important;
		}
	}
</style>
@endsection
