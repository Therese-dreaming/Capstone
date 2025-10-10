<!DOCTYPE html>
<html>
<head>
    <title>Lab Attendance History Report</title>
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
        
        .status-on-going {
            color: #000000;
        }
        
        .no-data {
            text-align: center;
            padding: 30px 20px;
            color: #000000;
            font-style: italic;
            background-color: #ffffff;
        }
        
        .faculty-cell {
            font-weight: 500;
            color: #000000;
        }
        
        .laboratory-cell {
            color: #000000;
            font-weight: 500;
        }
        
        .date-cell {
            font-family: 'Courier New', monospace;
            font-size: 9px;
            color: #000000;
        }
        
        .time-cell {
            font-family: 'Courier New', monospace;
            font-size: 9px;
            color: #000000;
        }
        
        .purpose-cell {
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
            margin-top: 40px;
            position: fixed;
            bottom: 20px;
            right: 20px;
            text-align: right;
        }
        
        .signature-entry {
            margin-bottom: 20px;
            display: inline-block;
            text-align: center;
            margin-right: 30px;
        }
        
        .signature-label {
            font-size: 10px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 5px;
        }
        
        .signature-name {
            font-size: 10px;
            color: #000000;
            margin-bottom: 5px;
        }
        
        .signature-image {
            max-width: 180px;
            max-height: 90px;
            border: none;
            background-color: transparent;
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
        <h1>Lab Attendance History Report</h1>
        <p class="subtitle">Comprehensive Laboratory Usage Overview</p>
    </div>
    
    <div class="report-info">
        <div class="generation-date">
            Generated on {{ date('F d, Y \a\t g:i A') }}
        </div>
        @if(request()->has('laboratory') || request()->has('purpose') || request()->has('status') || request()->has('time_in_start_date') || request()->has('time_in_end_date') || request()->has('time_out_start_date') || request()->has('time_out_end_date'))
            <div class="filters-applied">
                <strong>Applied Filters:</strong>
                @if(request('laboratory')) Laboratory: {{ request('laboratory') }} @endif
                @if(request('purpose')) | Purpose: {{ ucfirst(request('purpose')) }} @endif
                @if(request('status')) | Status: {{ ucfirst(request('status')) }} @endif
                @if(request('time_in_start_date')) | Time In From: {{ \Carbon\Carbon::parse(request('time_in_start_date'))->format('M d, Y') }} @endif
                @if(request('time_in_end_date')) | Time In To: {{ \Carbon\Carbon::parse(request('time_in_end_date'))->format('M d, Y') }} @endif
                @if(request('time_out_start_date')) | Time Out From: {{ \Carbon\Carbon::parse(request('time_out_start_date'))->format('M d, Y') }} @endif
                @if(request('time_out_end_date')) | Time Out To: {{ \Carbon\Carbon::parse(request('time_out_end_date'))->format('M d, Y') }} @endif
            </div>
        @endif
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Faculty</th>
                <th>Position</th>
                <th>Laboratory</th>
                <th>Purpose</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="date-cell">
                        {{ $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('m/d/Y') : ($log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('m/d/Y') : 'N/A') }}
                    </td>
                    <td class="faculty-cell">
                        {{ $log->user->name ?? 'N/A' }}
                    </td>
                    <td class="faculty-cell">
                        {{ $log->user->position ?? 'N/A' }}
                    </td>
                    <td class="laboratory-cell">
                        {{ $log->laboratory ?? 'N/A' }}
                    </td>
                    <td class="purpose-cell">
                        {{ $log->purpose ? ucfirst($log->purpose) : 'N/A' }}
                    </td>
                    <td class="time-cell">
                        {{ $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('g:i A') : '-' }}
                    </td>
                    <td class="time-cell">
                        {{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('g:i A') : '-' }}
                    </td>
                    <td>
                        <span class="status-badge status-{{ str_replace('-', '_', $log->status) }}">
                            {{ ucfirst(str_replace('-', ' ', $log->status ?? 'N/A')) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="no-data">
                        <strong>No attendance records found</strong><br>
                        <small>Try adjusting your filter criteria to view lab attendance activities</small>
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
            <div class="signature-name">{{ $signature['name'] }}</div>
            @if(isset($signature['signature_base64']) && !empty($signature['signature_base64']))
                <img src="{{ $signature['signature_base64'] }}" alt="Signature" class="signature-image">
            @else
                <div class="signature-line"></div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</body>
</html>
