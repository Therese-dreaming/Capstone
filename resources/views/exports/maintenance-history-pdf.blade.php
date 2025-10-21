<!DOCTYPE html>
<html>
<head>
    <title>Maintenance History Report</title>
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
            color: #991b1b;
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
        
        .status-pending {
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
        
        .task-cell {
            max-width: 200px;
            word-wrap: break-word;
            color: #000000;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #000000;
            border-top: 1px solid #000000;
            padding-top: 15px;
        }
        
        .signature-footer {
            margin-top: 20px;
            text-align: right;
            padding-top: 20px;
        }
        
        .signature-entry {
            margin-bottom: 20px;
            display: inline-block;
            text-align: center;
            margin-right: 30px;
            position: relative;
        }
        
        .signature-label {
            font-size: 10px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 5px;
        }
        
        .signature-container {
            position: relative;
            width: 180px;
            height: 70px;
            display: inline-block;
        }
        
        .signature-name {
            font-size: 10px;
            color: #000000;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            border-bottom: 1px solid #000000;
            padding-bottom: 2px;
            padding-top: 35px;
        }
        
        .signature-image {
            max-width: 180px;
            max-height: 60px;
            border: none;
            background-color: transparent;
            position: absolute;
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
        }
        
        .signature-line {
            width: 180px;
            border-bottom: 1px solid #000000;
            margin: 5px auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Maintenance History Report</h1>
        <p class="subtitle">Comprehensive Maintenance Activity Overview</p>
    </div>
    
    <div class="report-info">
        <div class="generation-date">
            Generated on {{ date('F d, Y \a\t g:i A') }}
        </div>
        @if(request()->has('start_date') || request()->has('end_date') || request()->has('lab_filter') || request()->has('status_filter') || request()->has('issue_filter'))
            <div class="filters-applied">
                <strong>Applied Filters:</strong>
                @if(request('start_date')) From: {{ \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') }} @endif
                @if(request('end_date')) To: {{ \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') }} @endif
                @if(request('lab_filter')) | Lab: {{ request('lab_filter') }} @endif
                @if(request('status_filter')) | Status: {{ ucfirst(request('status_filter')) }} @endif
                @if(request('issue_filter')) | Issues: {{ ucfirst(str_replace('_', ' ', request('issue_filter'))) }} @endif
            </div>
        @endif
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Scheduled Date</th>
                <th>Location</th>
                <th>Maintenance Tasks</th>
                <th>Assigned Technician</th>
                <th>Status</th>
                <th>Action By</th>
                <th>Completion Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maintenances as $maintenance)
                <tr>
                    <td class="date-cell">
                        {{ $maintenance->scheduled_date ? \Carbon\Carbon::parse($maintenance->scheduled_date)->format('m/d/Y') : 'N/A' }}
                    </td>
                    <td class="location-cell">
                        @if($maintenance->location)
                            {{ $maintenance->location->building }} - {{ $maintenance->location->room_number }}
                        @else
                            {{ isset($maintenance->lab_number) ? 'Lab ' . $maintenance->lab_number : 'N/A' }}
                        @endif
                    </td>
                    <td class="task-cell">
                        {{ $maintenance->maintenance_task ?: 'N/A' }}
                    </td>
                    <td class="technician-cell">
                        {{ $maintenance->technician ? $maintenance->technician->name : 'Not Assigned' }}
                    </td>
                    <td>
                        <span class="status-badge status-{{ $maintenance->status }}">
                            {{ ucfirst($maintenance->status) }}
                        </span>
                    </td>
                    <td class="technician-cell">
                        {{ $maintenance->actionBy ? $maintenance->actionBy->name : 'System' }}
                    </td>
                    <td class="date-cell">
                        @if($maintenance->status === 'completed' && $maintenance->completed_at)
                            {{ \Carbon\Carbon::parse($maintenance->completed_at)->format('m/d/Y g:i A') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="no-data">
                        <strong>No maintenance records found</strong><br>
                        <small>Try adjusting your filter criteria to view maintenance activities</small>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        <p>This report was automatically generated by the Asset Management System</p>
        <p>© {{ date('Y') }} - Confidential and Proprietary Information</p>
    </div>
    
    @if(isset($signatures) && count($signatures) > 0)
    <div class="signature-footer">
        @foreach($signatures as $signature)
        <div class="signature-entry">
            <div class="signature-label">{{ $signature['label'] }}:</div>
            <div class="signature-container">
                @if(isset($signature['signature_base64']) && !empty($signature['signature_base64']))
                    <img src="{{ $signature['signature_base64'] }}" alt="Signature" class="signature-image">
                @endif
                <div class="signature-name">{{ $signature['name'] }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</body>
</html>