@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-6 max-w-6xl md:max-w-full">
    <div class="max-w-6xl mx-auto">
        <!-- Main Container -->
        <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold">Disposal History</h1>
                    <p class="text-gray-600 text-sm">Total Assets Disposed: {{ $disposedAssets->count() }}</p>
                </div>
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 w-full sm:w-auto items-start sm:items-center">
                    <!-- Date Filter Form -->
                    <form action="{{ route('reports.disposal-history') }}" method="GET" class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 w-full sm:w-auto" id="dateFilterForm">
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
                            <a href="{{ route('reports.disposal-history') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center justify-center w-full sm:w-auto text-sm">
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

            <!-- Date Range Info (if filtered) -->
            @if(request('start_date') || request('end_date'))
            <div class="mb-4 text-sm text-gray-600 text-center sm:text-left">
                Showing results from 
                {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') : 'the beginning' }}
                to
                {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') : 'present' }}
            </div>
            @endif

            <!-- Assets Table (Desktop View) -->
            <div class="overflow-x-auto hidden md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Asset Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Serial Number</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Disposal Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Disposal Reason</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Purchase Price</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($disposedAssets as $asset)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ $asset->name }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                <a href="{{ route('assets.index', ['search' => $asset->serial_number]) }}" class="font-bold text-red-600 hover:underline">{{ $asset->serial_number }}</a>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $asset->category->name }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($asset->disposal_date)->format('M d, Y') }}
                                <br>
                                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($asset->disposal_date)->format('h:iA') }}</span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $asset->disposal_reason }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($asset->purchase_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-sm font-medium text-gray-900">Total Value of Disposed Assets</td>
                            <td class="px-4 py-2 text-sm font-medium text-gray-900">₱{{ number_format($disposedAssets->sum('purchase_price'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Assets List (Mobile View) -->
            <div class="grid grid-cols-1 gap-4 md:hidden">
                @foreach($disposedAssets as $asset)
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-800">
                    <div class="font-bold text-gray-900 mb-2">{{ $asset->name }}</div>
                    <div class="text-sm text-gray-900 mb-1"><strong>Serial Number:</strong> <a href="{{ route('assets.index', ['search' => $asset->serial_number]) }}" class="font-bold text-red-600 hover:underline">{{ $asset->serial_number }}</a></div>
                    <div class="text-sm text-gray-600 mb-1"><strong>Category:</strong> {{ $asset->category->name }}</div>
                    <div class="text-sm text-gray-600 mb-1">
                        <strong>Disposal Date:</strong> {{ \Carbon\Carbon::parse($asset->disposal_date)->format('M d, Y') }}
                        <br>
                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($asset->disposal_date)->format('h:iA') }}</span>
                    </div>
                    <div class="text-sm text-gray-600 mb-2"><strong>Disposal Reason:</strong> {{ $asset->disposal_reason }}</div>
                    <div class="text-sm text-gray-900"><strong>Purchase Price:</strong> ₱{{ number_format($asset->purchase_price, 2) }}</div>
                </div>
                @endforeach

                <!-- Total Purchase Price Summary for Mobile -->
                 <div class="bg-gray-50 rounded-lg shadow p-4 flex justify-between items-center mt-2">
                    <div class="text-sm font-medium text-gray-900">Total Value of Disposed Assets</div>
                    <div class="text-sm font-medium text-gray-900">₱{{ number_format($disposedAssets->sum('purchase_price'), 2) }}</div>
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
        .flex.flex-col.sm\:flex-row.space-y-3.sm\:space-y-0.sm\:space-x-4.w-full.sm\:w-auto.items-start.sm\:items-center, /* Hide button group container */
        .flex.flex-col.sm\:flex-row.justify-between.items-start.sm\:items-center.mb-6.gap-4, /* Hide header section */
        .grid.grid-cols-1.gap-4.md\:hidden, /* Hide mobile cards */
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

         /* Show total assets count in print */
        .text-gray-600.text-sm {
            text-align: center !important;
            margin-bottom: 20px;
            font-size: 10pt;
            display: block !important;
        }

         /* Show date range in print if filtered */
        .mb-4.text-sm.text-gray-600.text-center.sm\:text-left {
            display: block !important;
            text-align: center !important;
            margin-bottom: 15px;
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

        /* Remove status colors in print */
        .px-2.inline-flex {
            background-color: transparent !important;
            color: black !important;
        }
    }
</style>
@endsection