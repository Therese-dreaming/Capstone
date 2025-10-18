<!DOCTYPE html>
<html>
<head>
    <title>Vendor Analysis Report</title>
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
        
        .vendor-cell {
            font-weight: 500;
            color: #000000;
        }
        
        .count-cell {
            text-align: center;
            font-weight: 600;
            color: #000000;
        }
        
        .value-cell {
            text-align: right;
            font-weight: 500;
            color: #000000;
        }
        
        .rate-cell {
            text-align: center;
            font-weight: 500;
            color: #000000;
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
                    <h1>Vendor Analysis Report</h1>
                    <p class="subtitle">Comprehensive Vendor Performance and Reliability Analysis</p>
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
            This report analyzes <strong>{{ $overallStats['total_vendors'] }} vendors</strong> managing a total of <strong>{{ number_format($overallStats['total_assets']) }} assets</strong> with a combined value of <strong>PHP {{ number_format($overallStats['total_value'], 2) }}</strong>.
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Vendor Name</th>
                <th>Total Assets</th>
                <th>Total Value</th>
                <th>Total Repairs</th>
                <th>Completion Rate</th>
                <th>Disposed Assets</th>
                <th>Average Age</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vendorAnalysis as $vendor)
                <tr>
                    <td class="vendor-cell">{{ $vendor['name'] }}</td>
                    <td class="count-cell">{{ $vendor['total_assets'] }}</td>
                    <td class="value-cell">PHP {{ number_format($vendor['total_value'], 2) }}</td>
                    <td class="count-cell">{{ $vendor['total_repairs'] }}</td>
                    <td class="rate-cell">{{ number_format($vendor['completion_rate'], 1) }}%</td>
                    <td class="count-cell">{{ $vendor['disposed_count'] }}</td>
                    <td class="rate-cell">{{ $vendor['average_age'] ? number_format($vendor['average_age'], 1) . ' years' : 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="no-data">
                        <strong>No vendor analysis data available</strong><br>
                        <small>No vendors with assets are currently available for analysis</small>
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($vendorAnalysis) > 0)
        <tfoot>
            <tr class="total-row">
                <td class="vendor-cell"><strong>TOTAL</strong></td>
                <td class="count-cell"><strong>{{ number_format($overallStats['total_assets']) }}</strong></td>
                <td class="value-cell"><strong>PHP {{ number_format($overallStats['total_value'], 2) }}</strong></td>
                <td class="count-cell"><strong>{{ array_sum(array_column($vendorAnalysis, 'total_repairs')) }}</strong></td>
                <td class="rate-cell"><strong>{{ count($vendorAnalysis) > 0 ? number_format(array_sum(array_column($vendorAnalysis, 'completion_rate')) / count($vendorAnalysis), 1) : '0.0' }}%</strong></td>
                <td class="count-cell"><strong>{{ array_sum(array_column($vendorAnalysis, 'disposed_count')) }}</strong></td>
                <td class="rate-cell"><strong>{{ count(array_filter(array_column($vendorAnalysis, 'average_age'))) > 0 ? number_format(array_sum(array_filter(array_column($vendorAnalysis, 'average_age'))) / count(array_filter(array_column($vendorAnalysis, 'average_age'))), 1) . ' years' : 'N/A' }}</strong></td>
            </tr>
        </tfoot>
        @endif
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
