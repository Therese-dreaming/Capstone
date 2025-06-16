@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-6 max-w-6xl md:max-w-full">
    <div class="max-w-6xl mx-auto">
        <!-- Main Container -->
        <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold">Employee Performance Analysis</h1>
                    <p class="text-gray-600 text-sm">Period: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') : 'All Time' }} to {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') : 'Present' }}</p>
                </div>
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 w-full sm:w-auto items-start sm:items-center">
                    <!-- Date Filter Form -->
                    <form action="{{ route('reports.employee-performance') }}" method="GET" class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 w-full sm:w-auto" id="dateFilterForm">
                        <div class="flex items-center space-x-2 w-full sm:w-auto">
                            <label for="start_date" class="text-sm font-medium text-gray-600 flex-shrink-0">From:</label>
                            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" 
                                onchange="this.form.submit()"
                                class="form-input h-9 w-full md:w-auto px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div class="flex items-center space-x-2 w-full sm:w-auto">
                            <label for="end_date" class="text-sm font-medium text-gray-600 flex-shrink-0">To:</label>
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                                onchange="this.form.submit()"
                                class="form-input h-9 w-full md:w-auto px-3 py-0 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500">
                        </div>
                        @if(request('start_date') || request('end_date'))
                            <a href="{{ route('reports.employee-performance') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center justify-center w-full sm:w-auto text-sm">
                                Reset
                            </a>
                        @endif
                    </form>
                    <button onclick="printReport()" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center justify-center sm:justify-start w-full sm:w-auto text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Report
                    </button>
                </div>
            </div>

            <!-- Overall Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                    <h3 class="text-sm font-medium text-gray-500">Total Repairs Completed</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $overallStats['total_repairs'] }}</p>
                    <p class="text-sm text-gray-600">Average: {{ number_format($overallStats['avg_repairs_per_employee'], 1) }} per employee</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                    <h3 class="text-sm font-medium text-gray-500">Total Maintenance Tasks</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $overallStats['total_maintenance'] }}</p>
                    <p class="text-sm text-gray-600">Average: {{ number_format($overallStats['avg_maintenance_per_employee'], 1) }} per employee</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                    <h3 class="text-sm font-medium text-gray-500">Average Response Time</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $overallStats['avg_response_time'] }} hours</p>
                    <p class="text-sm text-gray-600">Best: {{ $overallStats['best_response_time'] }} hours</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
                    <h3 class="text-sm font-medium text-gray-500">Completion Rate</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($overallStats['completion_rate'], 1) }}%</p>
                    <p class="text-sm text-gray-600">Total Tasks: {{ $overallStats['total_tasks'] }}</p>
                </div>
            </div>

            <!-- Employee Performance Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Individual Performance Metrics</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Repairs Completed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Maintenance Tasks</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Response Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Rate</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance Score</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($employeeStats as $employee)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $employee->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $employee->role }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $employee->repairs_completed }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $employee->maintenance_tasks }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $employee->avg_response_time }} hours
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($employee->completion_rate, 1) }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-24 bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-red-600 h-2.5 rounded-full" style="width: {{ $employee->performance_score }}%"></div>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-600">{{ number_format($employee->performance_score, 1) }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Performance Analysis -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Performance Analysis</h2>
                
                <!-- Key Findings -->
                <div class="mb-6">
                    <h3 class="text-md font-medium text-gray-700 mb-2">Key Findings</h3>
                    <ul class="list-disc list-inside space-y-2 text-gray-600">
                        @foreach($analysis['key_findings'] as $finding)
                            <li>{{ $finding }}</li>
                        @endforeach
                    </ul>
                </div>

                <!-- Recommendations -->
                <div>
                    <h3 class="text-md font-medium text-gray-700 mb-2">Recommendations</h3>
                    <ul class="list-disc list-inside space-y-2 text-gray-600">
                        @foreach($analysis['recommendations'] as $recommendation)
                            <li>{{ $recommendation }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function printReport() {
        window.print();
    }
</script>

<style>
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
        form { /* Hide filter form */
            display: none !important;
        }

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

        /* Show period in print */
        .text-gray-600.text-sm {
            text-align: center !important;
            margin-bottom: 20px;
            font-size: 10pt;
            display: block !important;
        }

        /* Ensure tables are visible and styled for print */
        table {
            width: 100% !important;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #f3f4f6 !important;
            color: #6b7280 !important;
            font-size: 9pt !important;
            font-weight: 600;
            text-transform: uppercase;
            padding: 8px !important;
            text-align: left;
        }

        td {
            padding: 8px !important;
            font-size: 9pt !important;
            border-bottom: 1px solid #e5e7eb;
        }

        /* Ensure proper page breaks */
        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        /* Print colors */
        .bg-red-600 {
            background-color: #dc2626 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .bg-gray-50 {
            background-color: #f9fafb !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
@endsection 