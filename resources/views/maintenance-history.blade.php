@extends('layouts.app')

@section('content')
<div class="flex-1 p-8 ml-72">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Maintenance History</h2>
        <a href="{{ route('maintenance.schedule') }}" class="bg-[#960106] text-white px-4 py-2 rounded hover:bg-red-800">
            Back to Schedule
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Completed Maintenance History</h3>
            <input type="text" 
                   id="completed-search" 
                   placeholder="Search by serial number..." 
                   class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Completed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technician</th>
                    </tr>
                </thead>
                <tbody id="completed-maintenance-list" class="bg-white divide-y divide-gray-200">
                    <!-- Will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function loadCompletedMaintenance() {
            fetch('/maintenance/completed')
                .then(response => response.json())
                .then(completedMaintenance => {
                    renderCompletedMaintenance(completedMaintenance);
                });
        }

        function renderCompletedMaintenance(maintenance) {
            const list = document.getElementById('completed-maintenance-list');
            list.innerHTML = maintenance.map(item => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${new Date(item.completion_date).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.asset.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.asset.serial_number}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.task}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.technician.name}</td>
                </tr>
            `).join('');
        }

        document.getElementById('completed-search').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            fetch(`/maintenance/completed?search=${searchTerm}`)
                .then(response => response.json())
                .then(filteredMaintenance => {
                    renderCompletedMaintenance(filteredMaintenance);
                });
        });

        document.addEventListener('DOMContentLoaded', loadCompletedMaintenance);
    </script>
</div>
@endsection