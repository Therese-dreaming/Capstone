@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8">
	<div class="mb-6 md:mb-8">
		<div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
			<div class="flex items-center justify-between">
				<div class="flex items-center">
					<div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
						<svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
						</svg>
					</div>
					<div>
						<h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Edit Laboratory</h1>
						<p class="text-red-100 text-sm md:text-lg">Update laboratory details</p>
					</div>
				</div>
				<a href="{{ route('laboratories.index') }}" class="inline-flex items-center px-4 py-2 bg-white/20 text-white font-medium rounded-lg hover:bg-white/30">Back</a>
			</div>
		</div>
	</div>

	<div class="bg-white rounded-xl shadow-lg p-4 md:p-6">
		<form action="{{ route('laboratories.update', $laboratory) }}" method="POST" class="space-y-6">
			@csrf
			@method('PUT')
			<!-- Identification Section -->
			<div>
				<h2 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
					<svg class="w-5 h-5 text-red-700 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
					</svg>
					Identification
				</h2>
				<div class="bg-gray-50 border border-gray-200 rounded-lg p-4 md:p-5">
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-1">Number <span class="text-red-600">*</span></label>
							<input type="text" name="number" value="{{ old('number', $laboratory->number) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 px-4 py-2" placeholder="e.g., 401">
							@error('number')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
							<p class="text-xs text-gray-500 mt-1">Unique lab number used across logging and history.</p>
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-1">Name (optional)</label>
							<input type="text" name="name" value="{{ old('name', $laboratory->name) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 px-4 py-2" placeholder="e.g., Laboratory 401">
							@error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
						</div>
					</div>
				</div>
			</div>

			<!-- Location Section -->
			<div>
				<h2 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
					<svg class="w-5 h-5 text-red-700 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
					</svg>
					Location
				</h2>
				<div class="bg-gray-50 border border-gray-200 rounded-lg p-4 md:p-5">
					<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-1">Building</label>
							<input type="text" name="building" value="{{ old('building', $laboratory->building) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 px-4 py-2" placeholder="e.g., Main Building">
							@error('building')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-1">Floor</label>
							<input type="text" name="floor" value="{{ old('floor', $laboratory->floor) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 px-4 py-2" placeholder="e.g., 4">
							@error('floor')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-1">Room Number</label>
							<input type="text" name="room_number" value="{{ old('room_number', $laboratory->room_number) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 px-4 py-2" placeholder="e.g., 401">
							@error('room_number')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
						</div>
					</div>
					<p class="text-xs text-gray-500 mt-1">These fields are optional and help provide context in reports.</p>
				</div>
			</div>

			<div class="flex justify-end space-x-2 pt-2">
				<a href="{{ route('laboratories.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Cancel</a>
				<button type="submit" class="px-4 py-2 bg-red-800 text-white rounded-md hover:bg-red-700">Save Changes</button>
			</div>
		</form>
	</div>
</div>
@endsection 