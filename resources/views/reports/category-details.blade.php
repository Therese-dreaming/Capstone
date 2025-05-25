@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-6mx-auto max-w-6xl md:max-w-full">
    <div class="max-w-6xl mx-auto">
        <!-- Main Container -->
        <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 sm:gap-0">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold">{{ $category->name }} Assets</h1>
                    <p class="text-gray-600 text-sm">Total Assets: {{ $assets->count() }}</p>
                </div>
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 w-full sm:w-auto">
                    <a href="{{ route('reports.category') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center justify-center sm:justify-start w-full sm:w-auto text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                        </svg>
                        Back to Categories
                    </a>
                    <button onclick="printReport()" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center justify-center sm:justify-start w-full sm:w-auto text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Report
                    </button>
                </div>
            </div>

            <!-- Assets Table (Desktop View) -->
            <div class="overflow-x-auto hidden md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Model</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Serial Number</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Purchase Price</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assets as $asset)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ $asset->name }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $asset->location }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($asset->status == 'IN USE') bg-green-100 text-green-800
                                    @elseif($asset->status == 'UNDER REPAIR') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $asset->status }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $asset->model }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                <a href="{{ route('assets.index', ['search' => $asset->serial_number]) }}" class="font-bold text-red-600 hover:underline">{{ $asset->serial_number }}</a>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($asset->purchase_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-sm font-medium text-gray-900">Total</td>
                            <td class="px-4 py-2 text-sm font-medium text-gray-900">₱{{ number_format($assets->sum('purchase_price'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Assets List (Mobile View) -->
            <div class="grid grid-cols-1 gap-4 md:hidden">
                @foreach($assets as $asset)
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-800">
                    <div class="flex justify-between items-center mb-2">
                        <div class="font-bold text-gray-900">{{ $asset->name }}</div>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($asset->status == 'IN USE') bg-green-100 text-green-800
                            @elseif($asset->status == 'UNDER REPAIR') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ $asset->status }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-600 mb-1"><strong>Location:</strong> {{ $asset->location }}</div>
                    <div class="text-sm text-gray-600 mb-1"><strong>Model:</strong> {{ $asset->model }}</div>
                    <div class="text-sm text-gray-900 mb-1"><strong>Serial Number:</strong> <a href="{{ route('assets.index', ['search' => $asset->serial_number]) }}" class="font-bold text-red-600 hover:underline">{{ $asset->serial_number }}</a></div>
                    <div class="text-sm text-gray-900"><strong>Purchase Price:</strong> ₱{{ number_format($asset->purchase_price, 2) }}</div>
                </div>
                @endforeach

                <!-- Total Purchase Price Summary for Mobile -->
                <div class="bg-gray-50 rounded-lg shadow p-4 flex justify-between items-center mt-2">
                    <div class="text-sm font-medium text-gray-900">Total Purchase Price</div>
                    <div class="text-sm font-medium text-gray-900">₱{{ number_format($assets->sum('purchase_price'), 2) }}</div>
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
        .flex.flex-col.sm\:flex-row.space-y-3.sm\:space-y-0.sm\:space-x-3.w-full.sm\:w-auto, /* Hide button group container */
        .flex.flex-col.sm\:flex-row.justify-between.items-start.sm\:items-center.mb-6.gap-4.sm\:gap-0, /* Hide header section */
        .grid.grid-cols-1.gap-4.md\:hidden { /* Hide mobile cards */
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

         /* Style the total assets count below title */
        .text-gray-600.text-sm {
            text-align: center !important;
            margin-bottom: 20px;
            font-size: 10pt;
            display: block !important;
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

        /* Remove the color-adjust properties */
        thead th, .bg-green-100, .bg-yellow-100, .bg-red-100 {
            -webkit-print-color-adjust: unset !important;
            print-color-adjust: unset !important;
        }

        tfoot {
            display: table-row-group !important;
        }

        /* Ensure status colors print properly */
        .bg-green-100, .bg-yellow-100, .bg-red-100 {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Remove status colors in print */
        .px-2.inline-flex {
            background-color: transparent !important;
            color: black !important;
        }

        /* Ensure proper page breaks */
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

         /* Style the total assets count below title */
        .text-gray-600.text-sm {
            text-align: center !important;
            margin-bottom: 20px;
            font-size: 10pt;
            display: block !important;
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

        /* Remove the color-adjust properties */
        thead th, .bg-green-100, .bg-yellow-100, .bg-red-100 {
            -webkit-print-color-adjust: unset !important;
            print-color-adjust: unset !important;
        }

        tfoot {
            display: table-row-group !important;
        }

        /* Ensure status colors print properly */
        .bg-green-100, .bg-yellow-100, .bg-red-100 {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>

@endsection
