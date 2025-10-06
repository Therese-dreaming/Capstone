<!DOCTYPE html>
<html>
<head>
    <title>Maintenance History</title>
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
            background-color: #f3f4f6;
            font-size: 10px;
            text-transform: uppercase;
        }
        .completed { color: #059669; }
        .cancelled { color: #dc2626; }
        h1 { 
            text-align: center; 
            color: #1f2937;
            font-size: 14px;
            margin: 10px 0;
        }
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .status-completed {
            background-color: #dcfce7;
            color: #059669;
        }
        .status-cancelled {
            background-color: #fee2e2;
            color: #dc2626;
        }
    </style>
</head>
<body>
    <h1>Maintenance History Report</h1>
    <p style="text-align: center; margin: 5px 0; font-size: 10px; color: #6b7280;">
        Generated on {{ date('F d, Y \a\t g:i A') }}
        @if(request()->has('start_date') || request()->has('end_date') || request()->has('lab_filter') || request()->has('status_filter') || request()->has('issue_filter'))
            <br>Filters Applied: 
            @if(request('start_date')) From: {{ \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') }} @endif
            @if(request('end_date')) To: {{ \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') }} @endif
            @if(request('lab_filter')) Lab: {{ request('lab_filter') }} @endif
            @if(request('status_filter')) Status: {{ ucfirst(request('status_filter')) }} @endif
            @if(request('issue_filter')) Issues: {{ ucfirst(str_replace('_', ' ', request('issue_filter'))) }} @endif
        @endif
    </p>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Location</th>
                <th>Task</th>
                <th>Technician</th>
                <th>Status</th>
                <th>Action By</th>
                <th>Completion</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maintenances as $maintenance)
                <tr>
                    <td>{{ $maintenance->scheduled_date ? \Carbon\Carbon::parse($maintenance->scheduled_date)->format('m/d/Y') : 'N/A' }}</td>
                    <td>
                        @if($maintenance->location)
                            {{ $maintenance->location->building }} - {{ $maintenance->location->room_number }}
                        @else
                            {{ isset($maintenance->lab_number) ? 'Lab ' . $maintenance->lab_number : 'N/A' }}
                        @endif
                    </td>
                    <td>
                        @if($maintenance->maintenance_task)
                            {{ $maintenance->maintenance_task }}
                        @elseif(is_array($maintenance->maintenance_tasks))
                            {{ implode(', ', $maintenance->maintenance_tasks) }}
                        @elseif($maintenance->maintenance_tasks)
                            {{ $maintenance->maintenance_tasks }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $maintenance->technician ? $maintenance->technician->name : 'N/A' }}</td>
                    <td>
                        <span class="status-badge status-{{ $maintenance->status }}">
                            {{ ucfirst($maintenance->status) }}
                        </span>
                    </td>
                    <td>{{ $maintenance->actionBy ? $maintenance->actionBy->name : 'System' }}</td>
                    <td>
                        @if($maintenance->status === 'completed' && $maintenance->completed_at)
                            {{ \Carbon\Carbon::parse($maintenance->completed_at)->format('m/d/Y g:i A') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #6b7280;">
                        No maintenance records found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>