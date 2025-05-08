<!DOCTYPE html>
<html>
<head>
    <title>Lab Schedule History</title>
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
        .status-ongoing {
            background-color: #fef3c7;
            color: #d97706;
        }
        .status-scheduled {
            background-color: #e0f2fe;
            color: #0369a1;
        }
    </style>
</head>
<body>
    <h1>Lab Schedule History Report</h1>
    <table>
        <thead>
            <tr>
                <th>Laboratory</th>
                <th>Department</th>
                <th>Subject/Course</th>
                <th>Professor</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
                <th>Coordinator</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedules as $schedule)
                <tr>
                    <td>{{ $schedule->laboratory }}</td>
                    <td>{{ $schedule->department }}</td>
                    <td>{{ $schedule->subject_course }}</td>
                    <td>{{ $schedule->professor }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($schedule->start)->format('M j, Y g:i A') }}
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($schedule->end)->format('M j, Y g:i A') }}
                    </td>
                    <td>
                        <span class="status-badge status-{{ strtolower($schedule->status ?? 'scheduled') }}">
                            {{ $schedule->status ?? 'Scheduled' }}
                        </span>
                    </td>
                    <td>{{ $schedule->collaborator->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>