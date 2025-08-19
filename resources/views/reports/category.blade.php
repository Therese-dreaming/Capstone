@extends('layouts.app')

@section('content')
<div class="flex-1 p-4 md:p-8 transition-all duration-300" id="mainContent">
    <div class="max-w-6xl mx-auto">
        <!-- Success and Error Messages -->
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-200 rounded-xl text-green-800 flex items-center">
            <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-200 rounded-xl text-red-800 flex items-center">
            <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
        @endif

        <!-- Main Container -->
        <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div class="flex items-center">
                    <div class="bg-red-100 p-3 rounded-full mr-4">
                        <svg class="w-8 h-8 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Asset Category Report</h1>
                        <p class="text-gray-600 text-sm md:text-base">Comprehensive overview of assets by category</p>
                    </div>
                </div>
                <button onclick="printReport()" class="inline-flex items-center px-6 py-3 bg-red-800 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 shadow-lg hover:shadow-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Report
                </button>
            </div>

            <!-- Total Summary Card -->
            <div class="mb-8">
                <div class="bg-red-800 text-white rounded-xl shadow-lg p-6 md:p-8">
                    <div class="flex items-center mb-4">
                        <div class="bg-white/20 p-3 rounded-full mr-4 backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold">Total Assets Summary</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
                            <div class="text-3xl md:text-4xl font-bold mb-2">{{ $totalSummary['total_assets'] }}</div>
                            <p class="text-red-100 text-sm md:text-base font-medium">Total Assets</p>
                        </div>
                        <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm sm:text-right">
                            <div class="text-3xl md:text-4xl font-bold mb-2">₱{{ number_format($totalSummary['total_value'], 2) }}</div>
                            <p class="text-red-100 text-sm md:text-base font-medium">Total Value</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                @foreach($categories as $category)
                <a href="{{ route('reports.category.details', $category->id) }}" class="group block bg-white rounded-xl shadow-md border border-gray-200 p-6 hover:shadow-lg hover:border-red-300 transition-all duration-300 no-underline text-gray-800 transform hover:-translate-y-1">
                    <div class="flex items-center mb-4">
                        <div class="bg-red-100 p-2 rounded-lg mr-3 group-hover:bg-red-200 transition-colors">
                            <svg class="w-5 h-5 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 group-hover:text-red-800 transition-colors">{{ $category->name }}</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-3xl font-bold text-red-800">{{ $category->assets->count() }}</span>
                            <div class="text-right">
                                <div class="text-sm text-gray-500 font-medium">Total Assets</div>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-gray-100">
                            <div class="text-sm text-gray-600">Total Value</div>
                            <div class="text-lg font-semibold text-gray-900">₱{{ number_format($category->assets->sum('purchase_price'), 2) }}</div>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-red-600 text-sm font-medium group-hover:text-red-700 transition-colors">
                        <span>View Details</span>
                        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- Category Details Table -->
            <div class="overflow-x-auto hidden md:block">
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2-2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Detailed Category Breakdown
                    </h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white rounded-lg">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Assets</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($categories as $category)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-red-100 p-2 rounded-lg mr-3">
                                            <svg class="w-4 h-4 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $category->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ $category->assets->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">₱{{ number_format($category->assets->sum('purchase_price'), 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-800">
                                    <a href="{{ route('reports.category.details', $category->id) }}" class="inline-flex items-center px-3 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-colors font-medium">
                                        <span>View Details</span>
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100 rounded-lg">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">Total</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-800">
                                        {{ $totalSummary['total_assets'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">₱{{ number_format($totalSummary['total_value'], 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap"></td>
                            </tr>
                        </tfoot>
                    </table>
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
        .flex.flex-col.sm\:flex-row.justify-between.items-start.sm\:items-center.mb-6.gap-4.sm\:gap-0 > .flex.flex-col.sm\:flex-row.space-y-3.sm\:space-y-0.sm\:space-x-3.w-full.sm\:w-auto { /* Hide the button container */
            display: none !important;
        }

        /* Hide category summary cards */
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

        /* Style the subtitle */
        .text-gray-600.text-sm.md\:text-base {
            text-align: center !important;
            margin-bottom: 20px;
            font-size: 10pt;
            display: block !important;
        }

         /* Ensure total summary card is visible and styled */
        .mb-8:has(.bg-red-800) { /* Target the parent div of the total summary card */
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

        .bg-gray-50.rounded-xl.p-6.mb-6 {
            background-color: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        table {
            width: 100% !important;
            table-layout: auto !important; /* Allow columns to size naturally */
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
        thead th,
        .bg-green-100,
        .bg-yellow-100,
        .bg-red-100 {
            -webkit-print-color-adjust: unset !important;
            print-color-adjust: unset !important;
        }
    }
</style>

@endsection