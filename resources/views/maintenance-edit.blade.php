@extends('layouts.app')

@section('content')
<div class="flex-1 p-8 ml-72">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Edit Maintenance Schedule</h2>
            <a href="{{ route('maintenance.upcoming') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 transition-colors duration-200">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </span>
            </a>
        </div>

        <form action="{{ route('maintenance.update', $maintenance->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Asset Information</label>
                <div class="text-gray-800 font-medium">
                    {{ $maintenance->asset->name }}
                    <span class="text-gray-500 text-sm ml-2">(SN: {{ $maintenance->serial_number }})</span>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="task" class="block text-sm font-medium text-gray-700 mb-1">Maintenance Task</label>
                    <textarea name="task" id="task" rows="3" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 resize-none py-3">{{ $maintenance->task }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-1">Scheduled Date</label>
                        <input type="date" name="scheduled_date" id="scheduled_date" 
                            min="{{ date('Y-m-d') }}" 
                            value="{{ $maintenance->scheduled_date }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 h-12">
                    </div>

                    <div>
                        <label for="technician_id" class="block text-sm font-medium text-gray-700 mb-1">Assigned Technician</label>
                        <select name="technician_id" id="technician_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 h-12">
                            @foreach($technicians as $technician)
                                <option value="{{ $technician->id }}" {{ $maintenance->technician_id == $technician->id ? 'selected' : '' }}>
                                    {{ $technician->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-8 pt-4 border-t">
                <a href="{{ route('maintenance.upcoming') }}" 
                    class="px-6 py-3 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" 
                    class="px-6 py-3 bg-red-800 text-white rounded-md hover:bg-red-700 transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection