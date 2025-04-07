@extends('layouts.app')

@section('content')
<div class="flex-1 ml-80">
    <div class="p-6">
        <!-- Main Container -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold">Disposal History</h1>
                    <p class="text-gray-600">Total Assets Disposed: {{ $disposedAssets->count() }}</p>
                </div>
                <div class="flex space-x-4">
                    <!-- Date Filter Form -->
                    <form action="{{ route('reports.disposal-history') }}" method="GET" class="flex space-x-4" id="dateFilterForm">
                        <div class="flex items-center space-x-2">
                            <label for="start_date" class="text-sm font-medium text-gray-600">From:</label>
                            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" 
                                onchange="this.form.submit()"
                                class="border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div class="flex items-center space-x-2">
                            <label for="end_date" class="text-sm font-medium text-gray-600">To:</label>
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                                onchange="this.form.submit()"
                                class="border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        @if(request('start_date') || request('end_date'))
                            <a href="{{ route('reports.disposal-history') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                                Reset
                            </a>
                        @endif
                    </form>
                    <button onclick="printReport()" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Report
                    </button>
                </div>
            </div>

            <!-- Date Range Info (if filtered) -->
            @if(request('start_date') || request('end_date'))
            <div class="mb-4 text-sm text-gray-600">
                Showing results from 
                {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') : 'the beginning' }}
                to
                {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') : 'present' }}
            </div>
            @endif

            <!-- Assets Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serial Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Disposal Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Disposal Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purchase Price</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($disposedAssets as $asset)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $asset->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->serial_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->category->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->disposal_date }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->disposal_reason }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($asset->purchase_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-sm font-medium text-gray-900">Total Value of Disposed Assets</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">₱{{ number_format($disposedAssets->sum('purchase_price'), 2) }}</td>
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
        /* Hide navigation elements */
        aside.fixed,
        nav.bg-white,
        .sidebar-nav,
        header,
        .header,
        #header,
        [x-data],
        button,
        .print-hide {
            display: none !important;
        }

        /* Reset layout */
        body, html {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background: white !important;
        }

        .flex-1.ml-80 {
            margin-left: 0 !important;
            width: 100% !important;
            padding: 20px !important;
        }

        /* Style the title */
        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 18pt;
        }

        /* Show total assets count in print */
        .text-gray-600 {
            display: block !important;
            margin-bottom: 10px;
        }

        /* Table styles */
        table {
            width: 100% !important;
            table-layout: fixed !important;
            border-collapse: collapse;
            margin-top: 20px;
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

        /* Hide filter form */
        form {
            display: none !important;
        }

        /* Show date range in print if filtered */
        .mb-4.text-sm.text-gray-600 {
            display: block !important;
            text-align: center;
            margin-bottom: 15px;
        }

        /* Remove status colors in print */
        .px-2.inline-flex {
            background: none !important;
            color: #374151 !important;
        }
    }
</style>
@endsection