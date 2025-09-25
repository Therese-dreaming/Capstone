<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Lab Attendance History</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 11px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 4px 6px; 
            text-align: left; 
        }
        th { 
            background-color: #960106;
            color: white;
            font-size: 10px;
            text-transform: uppercase;
        }
        h1 { 
            text-align: center; 
            color: #1f2937;
            font-size: 14px;
            margin: 10px 0;
        }
        .whitespace-nowrap {
            white-space: nowrap;
        }
        td {
            font-size: 10px;
            line-height: 1.3;
        }
        .filters {
            margin: 10px 0;
            font-size: 10px;
        }
        .filters p {
            margin: 3px 0;
        }
        .total-records {
            text-align: right;
            font-size: 10px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Lab Attendance History Report</h1>

    <div class="filters">
        <p><strong>Laboratory:</strong> {{ $filters['laboratory'] ?? 'All' }}</p>
        <p><strong>Purpose:</strong> {{ $filters['purpose'] ?? 'All' }}</p>
        <p><strong>Status:</strong> {{ $filters['status'] ?? 'All' }}</p>
        @if($filters['start_date'] || $filters['end_date'])
        <p><strong>Date Range:</strong> {{ $filters['start_date'] ?? 'Any' }} to {{ $filters['end_date'] ?? 'Any' }}</p>
        @endif
        <p><strong>Generated on:</strong> {{ now()->format('m/d/Y h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="whitespace-nowrap">Date</th>
                <th>Faculty</th>
                <th>Laboratory</th>
                <th>Purpose</th>
                <th class="whitespace-nowrap">Time In</th>
                <th class="whitespace-nowrap">Time Out</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="whitespace-nowrap">
                        {{ $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('m/d/Y') : ($log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('m/d/Y') : 'N/A') }}
                    </td>
                    <td>
                        {{ $log->user->name ?? 'N/A' }}
                        <br>
                        <small>{{ $log->user->position ?? 'N/A' }}</small>
                    </td>
                    <td>{{ $log->laboratory ?? 'N/A' }}</td>
                    <td>{{ $log->purpose ? ucfirst($log->purpose) : 'N/A' }}</td>
                    <td class="whitespace-nowrap">{{ $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('h:i A') : '-' }}</td>
                    <td class="whitespace-nowrap">{{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('h:i A') : '-' }}</td>
                    <td>{{ ucfirst($log->status ?? 'N/A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No records found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-records">
        <p>Total Records: {{ $logs->count() }}</p>
    </div>
</body>
</html>