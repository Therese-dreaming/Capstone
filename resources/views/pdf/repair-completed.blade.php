<!DOCTYPE html>
<html>
<head>
    <title>Repair History Report</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 11px;
            color: #000000;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background-color: #ffffff;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
        }
        
        h1 { 
            color: #000000;
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .subtitle {
            color: #000000;
            font-size: 12px;
            font-weight: 500;
            margin: 0;
        }
        
        .report-info {
            text-align: center;
            margin: 15px 0;
            padding: 10px;
            background-color: #ffffff;
        }
        
        .generation-date {
            font-size: 10px;
            color: #000000;
            font-weight: 500;
        }
        
        .filters-applied {
            font-size: 10px;
            color: #000000;
            font-weight: 600;
            margin-top: 5px;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }
        
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px 10px; 
            text-align: left;
            vertical-align: top;
        }
        
        th { 
            background-color: #ffffff;
            color: #000000;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            border: 1px solid #000000;
        }
        
        tbody tr:nth-child(even) {
            background-color: #ffffff;
        }
        
        tbody tr:hover {
            background-color: #ffffff;
        }
        
        td {
            font-size: 10px;
            line-height: 1.3;
            color: #000000;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #000000;
        }
        
        .status-completed {
            color: #000000;
        }
        
        .status-cancelled {
            color: #000000;
        }
        
        .status-pulled_out {
            color: #000000;
        }
        
        .no-data {
            text-align: center;
            padding: 30px 20px;
            color: #000000;
            font-style: italic;
            background-color: #ffffff;
        }
        
        .location-cell {
            font-weight: 500;
            color: #000000;
        }
        
        .technician-cell {
            color: #000000;
            font-weight: 500;
        }
        
        .date-cell {
            font-family: 'Courier New', monospace;
            font-size: 9px;
            color: #000000;
        }
        
        .item-cell {
            max-width: 200px;
            word-wrap: break-word;
            color: #000000;
        }
        
        .unregistered-badge {
            color: #000000;
            padding: 1px 4px;
            font-size: 9px;
            margin-left: 4px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #000000;
            border-top: 1px solid #000000;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Repair History Report</h1>
        <p class="subtitle">Comprehensive Repair Activity Overview</p>
    </div>
    
    <div class="report-info">
        <div class="generation-date">
            Generated on {{ date('F d, Y \a\t g:i A') }}
        </div>
        @if(request()->has('status') || request()->has('location') || request()->has('request_start_date') || request()->has('request_end_date') || request()->has('completion_start_date') || request()->has('completion_end_date'))
            <div class="filters-applied">
                <strong>Applied Filters:</strong>
                @if(request('status')) Status: {{ ucfirst(str_replace('_', ' ', request('status'))) }} @endif
                @if(request('location')) | Location: {{ request('location') }} @endif
                @if(request('request_start_date')) | Request From: {{ \Carbon\Carbon::parse(request('request_start_date'))->format('M d, Y') }} @endif
                @if(request('request_end_date')) | Request To: {{ \Carbon\Carbon::parse(request('request_end_date'))->format('M d, Y') }} @endif
                @if(request('completion_start_date')) | Completion From: {{ \Carbon\Carbon::parse(request('completion_start_date'))->format('M d, Y') }} @endif
                @if(request('completion_end_date')) | Completion To: {{ \Carbon\Carbon::parse(request('completion_end_date'))->format('M d, Y') }} @endif
            </div>
        @endif
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Request Date</th>
                <th>Completion Date</th>
                <th>Status</th>
                <th>Item</th>
                <th>Ticket No.</th>
                <th>Location</th>
                <th>Assigned Technician</th>
                <th>Findings</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($completedRequests as $request)
                <tr>
                    <td class="date-cell">
                        {{ \Carbon\Carbon::parse($request->created_at)->format('m/d/Y g:i A') }}
                    </td>
                    <td class="date-cell">
                        {{ $request->completed_at ? \Carbon\Carbon::parse($request->completed_at)->format('m/d/Y g:i A') : 'N/A' }}
                    </td>
                    <td>
                        <span class="status-badge status-{{ $request->status }}">
                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                        </span>
                    </td>
                    <td class="item-cell">
                        @if($request->asset && $request->asset->serial_number)
                            {{ $request->asset->serial_number }}
                        @elseif(!empty($request->serial_number))
                            {{ $request->serial_number }}
                        @else
                            {{ $request->equipment }}
                            <span class="unregistered-badge">Unregistered</span>
                        @endif
                    </td>
                    <td>{{ $request->ticket_number }}</td>
                    <td class="location-cell">
                        @if($request->building && $request->floor && $request->room)
                            {{ $request->building }} - Floor {{ $request->floor }} - Room {{ $request->room }}
                        @elseif($request->building && $request->room)
                            {{ $request->building }} - Room {{ $request->room }}
                        @elseif($request->location)
                            {{ $request->location }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="technician-cell">
                        {{ $request->technician ? $request->technician->name : 'Not Assigned' }}
                    </td>
                    <td class="item-cell">{{ $request->findings ?: 'N/A' }}</td>
                    <td class="item-cell">{{ $request->remarks ?: 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="no-data">
                        <strong>No repair records found</strong><br>
                        <small>Try adjusting your filter criteria to view repair activities</small>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        <p>This report was automatically generated by the Asset Management System</p>
        <p>© {{ date('Y') }} - Confidential and Proprietary Information</p>
    </div>
</body>
</html>