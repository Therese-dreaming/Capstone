@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-6 mx-auto max-w-6xlmd:max-w-full">
    <div class="max-w-6xl mx-auto">
        <!-- Success and Error Messages -->
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
        <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 sm:gap-0">
                <h1 class="text-xl sm:text-2xl font-bold">Asset Location Report</h1>
                <button onclick="printReport()" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center justify-center sm:justify-start w-full sm:w-auto text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Report
                </button>
            </div>

            <!-- Total Summary Card -->
            <div class="mb-6">
                <div class="bg-red-800 text-white rounded-lg shadow p-4">
                    <h3 class="text-lg sm:text-xl font-semibold">Total Assets Summary</h3>
                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="text-2xl sm:text-3xl font-bold">{{ $totalSummary['total_assets'] }}</span>
                            <p class="text-sm opacity-80">Total Assets</p>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl sm:text-3xl font-bold">₱{{ number_format($totalSummary['total_value'], 2) }}</span>
                            <p class="text-sm opacity-80">Total Value</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
                @foreach($locationStats as $stat)
                <a href="{{ route('reports.location.details', $stat['location']) }}" class="block bg-white rounded-lg shadow p-4 border-l-4 border-red-800 hover:shadow-md transition-shadow duration-200 no-underline text-gray-800">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $stat['location'] }}</h3>
                    <div class="mt-2 flex justify-between items-center">
                        <span class="text-2xl font-bold text-red-800">{{ $stat['count'] }}</span>
                        <div class="flex flex-col text-right">
                            <span class="text-sm text-gray-500">Total Assets</span>
                            <span class="text-xs text-gray-400">Value: ₱{{ number_format($stat['total_value'], 2) }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- Location Details Table (Desktop View) -->
            <div class="overflow-x-auto hidden md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Assets</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Value</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($locationStats as $stat)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $stat['location'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $stat['count'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($stat['total_value'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-800">
                                <a href="{{ route('reports.location.details', $stat['location']) }}" class="hover:text-red-600">View Details</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Total</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $totalSummary['total_assets'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($totalSummary['total_value'], 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
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
        .flex.flex-col.sm\:flex-row.justify-between.items-start.sm\:items-center.mb-6.gap-4.sm\:gap-0 > .flex.flex-col.sm\:flex-row.space-y-3.sm\:space-y-0.sm\:space-x-4.w-full.sm\:w-auto { /* Hide the button group container */
            display: none !important;
        }

        /* Hide location summary cards */
        .grid.grid-cols-1.md\:grid-cols-2.lg\:grid-cols-3.xl\:grid-cols-4 {
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

        /* Ensure total summary card is visible and styled */
        .mb-6:has(.bg-red-800) { /* Target the parent div of the total summary card */
             display: block !important;
             margin-bottom: 20px !important;
        }

        .bg-red-800 {
            background-color: white !important;
            color: black !important;
            border: 1px solid #000;
            padding: 15px !important;
        }

        .bg-red-800 * {
            color: black !important;
        }

        /* Ensure the table is visible and styled for print */
        .overflow-x-auto.hidden.md\:block {
            display: block !important;
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
            color: #6b7280;\
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

        /* Hide Actions column in print */
        table th:last-child,
        table td:last-child {
            display: none !important;
        }

        /* Ensure proper page breaks */
        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        tfoot {
            display: table-row-group !important;
        }

         /* Remove color-adjust for backgrounds/colors */
        thead th {
            -webkit-print-color-adjust: unset !important;
            print-color-adjust: unset !important;
        }
    }
</style>
@endsection