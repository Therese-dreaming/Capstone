<!DOCTYPE html>
<html>
<head>
    <title>Lab Usage Report</title>
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
        
        .header-table {
            margin: 0 auto;
            border: none;
            width: auto;
        }
        
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        
        .header-logo {
            width: 60px;
            height: 60px;
            padding-right: 8px;
        }
        
        .header-text {
            text-align: left;
        }
        
        h1 { 
            color: #991b1b;
            font-size: 18px;
            font-weight: 900;
            margin: 0 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .subtitle {
            color: #991b1b;
            font-size: 12px;
            font-weight: 700;
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
        
        .summary-section {
            margin: 20px 0;
            text-align: left;
        }
        
        .summary-text {
            font-size: 12px;
            color: #000000;
            line-height: 1.5;
        }
        
        .summary-text strong {
            color: #991b1b;
            font-weight: bold;
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
        
        .no-data {
            text-align: center;
            padding: 30px 20px;
            color: #000000;
            font-style: italic;
            background-color: #ffffff;
        }
        
        .section-title {
            color: #991b1b;
            font-size: 14px;
            font-weight: 700;
            margin: 25px 0 15px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #991b1b;
            color: white;
        }
        
        .total-row td {
            color: white;
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
        <table class="header-table">
            <tr>
                <td>
                    <img src="{{ public_path('images/logo-small.png') }}" alt="Logo" class="header-logo">
                </td>
                <td class="header-text">
                    <h1>Lab Usage Report</h1>
                    <p class="subtitle">Comprehensive Laboratory Usage Analytics</p>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="report-info">
        <div class="generation-date">
            Generated on {{ date('F d, Y \a\t g:i A') }}
        </div>
    </div>
    
    <div class="summary-section">
        <div class="summary-text">
            This report contains comprehensive laboratory usage data with <strong>{{ $summary->total_sessions ?? 0 }} total sessions</strong> across <strong>{{ $summary->labs_used ?? 0 }} laboratories</strong>, totaling <strong>{{ number_format($summary->total_hours ?? 0, 1) }} hours</strong> of usage by <strong>{{ $summary->unique_users ?? 0 }} unique users</strong>.
        </div>
    </div>

    <div class="section-title">Usage by Department</div>
    <table>
        <thead>
            <tr>
                <th>Department</th>
                <th>Total Sessions</th>
                <th>Total Hours</th>
            </tr>
        </thead>
        <tbody>
            @forelse($departmentUsage as $dept)
            <tr>
                <td>{{ $dept->department_name }}</td>
                <td>{{ number_format($dept->total_sessions) }}</td>
                <td>{{ number_format($dept->total_hours, 1) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="no-data">
                    <strong>No department usage data found</strong><br>
                    <small>No department usage records available for the selected period</small>
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td><strong>TOTAL</strong></td>
                <td><strong>{{ number_format($departmentUsage->sum('total_sessions')) }}</strong></td>
                <td><strong>{{ number_format($departmentUsage->sum('total_hours'), 1) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">Usage by Laboratory</div>
    <table>
        <thead>
            <tr>
                <th>Laboratory</th>
                <th>Total Sessions</th>
                <th>Total Hours</th>
            </tr>
        </thead>
        <tbody>
            @forelse($labUsage as $lab)
            <tr>
                <td>{{ $lab->lab_name }}</td>
                <td>{{ number_format($lab->total_sessions) }}</td>
                <td>{{ number_format($lab->total_hours, 1) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="no-data">
                    <strong>No laboratory usage data found</strong><br>
                    <small>No laboratory usage records available for the selected period</small>
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td><strong>TOTAL</strong></td>
                <td><strong>{{ number_format($labUsage->sum('total_sessions')) }}</strong></td>
                <td><strong>{{ number_format($labUsage->sum('total_hours'), 1) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">Detailed Usage Data</div>
    <table>
        <thead>
            <tr>
                <th>Period</th>
                <th>Department</th>
                <th>Laboratory</th>
                <th>Total Sessions</th>
                <th>Total Hours</th>
                <th>Avg. Duration</th>
                <th>Unique Users</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usageData as $data)
            <tr>
                <td>{{ $data->period }}</td>
                <td>{{ $data->department_name }}</td>
                <td>{{ $data->lab_name }}</td>
                <td>{{ number_format($data->total_sessions) }}</td>
                <td>{{ number_format($data->total_hours, 1) }}</td>
                <td>{{ number_format($data->avg_duration, 1) }}h</td>
                <td>{{ number_format($data->unique_users) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="no-data">
                    <strong>No detailed usage data found</strong><br>
                    <small>No detailed usage records available for the selected period</small>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        <p>This report was automatically generated by the IT Asset and Repair Management System</p>
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