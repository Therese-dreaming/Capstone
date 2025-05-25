@extends('layouts.app')

@section('content')
<div class="flex-1 ml-80">
    <div class="p-6">
        <!-- Main Container -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold">Location Details: {{ $location }}</h1>
                    <p class="text-gray-600">Total Assets: {{ $assets->count() }}</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('reports.location') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                        </svg>
                        Back to Locations
                    </a>
                    <button onclick="printReport()" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Report
                    </button>
                </div>
            </div>

            <!-- Assets Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Model</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serial Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purchase Price</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assets as $asset)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $asset->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->location }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
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
                            <td colspan="5" class="px-6 py-4 text-sm font-medium text-gray-900">Total</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">₱{{ number_format($assets->sum('purchase_price'), 2) }}</td>
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

        /* Update table styles for printing */
        thead th {
            background-color: #f3f4f6 !important;
            color: #374151 !important;
        }

        /* Remove status colors in print */
        .px-2.inline-flex {
            background: none !important;
            color: #374151 !important;
        }

        /* Show total assets count in print */
        .text-gray-600 {
            display: block !important;
            margin-bottom: 10px;
        }

        /* Hide action buttons */
        .flex.space-x-4 {
            display: none !important;
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

        /* Adjust table for printing */
        .overflow-x-auto {
            overflow: visible !important;
        }

        /* Add footer styles */
        tfoot {
            display: table-row-group !important;
        }
    }
</style>
@endsection