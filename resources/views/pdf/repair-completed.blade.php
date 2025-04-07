<!DOCTYPE html>
<html>
<head>
    <title>Completed Repairs</title>
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
    </style>
</head>
<body>
    <h1>Completed Repairs Report</h1>

    <table>
        <thead>
            <tr>
                <th class="whitespace-nowrap">Request Date</th>
                <th class="whitespace-nowrap">Completion Date</th>
                <th>Item</th>
                <th>Ticket No.</th>
                <th>Department</th>
                <th>Lab Room</th>
                <th>Technician</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($completedRequests as $request)
            <tr>
                <td class="whitespace-nowrap">{{ \Carbon\Carbon::parse($request->created_at)->format('m/d/Y') }}</td>
                <td class="whitespace-nowrap">{{ \Carbon\Carbon::parse($request->completed_at)->format('m/d/Y') }}</td>
                <td>{{ $request->equipment }}</td>
                <td>{{ $request->ticket_number }}</td>
                <td>{{ $request->department }}</td>
                <td>{{ $request->office_room }}</td>
                <td>{{ $request->technician ? $request->technician->name : 'Not Assigned' }}</td>
                <td>{{ $request->remarks }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>