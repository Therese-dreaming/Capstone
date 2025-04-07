@extends('layouts.app')

@section('content')
<div class="flex-1 ml-80">
    <div class="p-6">
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
        <div class="bg-white rounded-lg shadow-lg p-6">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Asset Category Report</h1>
                <button onclick="printReport()" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Report
                </button>
            </div>

            <!-- Total Summary Card -->
            <div class="mb-6">
                <div class="bg-red-800 text-white rounded-lg shadow p-4">
                    <h3 class="text-xl font-semibold">Total Assets Summary</h3>
                    <div class="mt-2 grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-3xl font-bold">{{ $totalSummary['total_assets'] }}</span>
                            <p class="text-sm opacity-80">Total Assets</p>
                        </div>
                        <div class="text-right">
                            <span class="text-3xl font-bold">₱{{ number_format($totalSummary['total_value'], 2) }}</span>
                            <p class="text-sm opacity-80">Total Value</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
                @foreach($categoryStats as $stat)
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-800">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $stat['name'] }}</h3>
                    <div class="mt-2 flex justify-between items-center">
                        <span class="text-2xl font-bold text-red-800">{{ $stat['count'] }}</span>
                        <div class="flex flex-col text-right">
                            <span class="text-sm text-gray-500">Total Assets</span>
                            <span class="text-xs text-gray-400">Value: ₱{{ number_format($stat['total_value'], 2) }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Category Details Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Assets</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Value</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $category->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->assets->count() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($category->assets->sum('purchase_price'), 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-800">
                                <a href="{{ route('reports.category.details', $category->id) }}" class="hover:text-red-600">View Details</a>
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
        /* Hide navigation elements */
        aside.fixed,
        nav.bg-white,
        .sidebar-nav,
        header,
        .header,
        #header,
        [x-data],
        button,
        .print-hide,
        .grid.grid-cols-1 {
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
            text-align: center !important;
            margin-bottom: 20px;
            font-size: 18pt;
            width: 100% !important;
            display: block !important;
        }

        /* Style the summary card */
        .bg-red-800 {
            background-color: white !important;
            color: black !important;
            border: 1px solid #000;
            margin-bottom: 20px;
            padding: 15px !important;
        }

        .bg-red-800 * {
            color: black !important;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #f3f4f6;
            color: #6b7280;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            padding: 0.75rem 1.5rem;
            text-align: left;
        }

        td {
            padding: 1rem 1.5rem;
            font-size: 0.875rem;
            border-bottom: 1px solid #e5e7eb;
        }

        /* Hide action column */
        th:last-child, 
        td:last-child {
            display: none;
        }

        /* Ensure proper page breaks */
        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        /* Show footer in print */
        tfoot {
            display: table-row-group;
        }
    }
</style>

@endsection