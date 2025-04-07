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
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Lab</th>
                <th>Task</th>
                <th>Technician</th>
                <th>Status</th>
                <th>Action By</th>
                <th>Completion</th>
            </tr>
        </thead>
        <tbody>
            @foreach($maintenances as $maintenance)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('m/d/Y') }}</td>
                    <td>Lab {{ $maintenance->lab_number }}</td>
                    <td>{{ $maintenance->maintenance_task }}</td>
                    <td>{{ $maintenance->technician->name }}</td>
                    <td>
                        <span class="status-badge status-{{ $maintenance->status }}">
                            {{ ucfirst($maintenance->status) }}
                        </span>
                    </td>
                    <td>{{ $maintenance->actionBy ? $maintenance->actionBy->name : 'System' }}</td>
                    <td>{{ $maintenance->status === 'completed' ? \Carbon\Carbon::parse($maintenance->completed_at)->format('m/d/Y g:i A') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>