<!DOCTYPE html>
<html>
<head>
    <title>Asset Category Report</title>
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
        
        .category-cell {
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
        <h1>Asset Category Report</h1>
        <p class="subtitle">Comprehensive Overview of Assets by Category</p>
    </div>
    
    <div class="report-info">
        <div class="generation-date">
            Generated on {{ date('F d, Y \a\t g:i A') }}
        </div>
    </div>
    
    <div class="summary-section">
        <div class="summary-text">
            This report contains a total of <strong>{{ $totalSummary['total_assets'] }} assets</strong> across all categories, with a combined total value of <strong>PHP {{ number_format($totalSummary['total_value'], 2) }}</strong>.
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Category Name</th>
                <th>Asset Count</th>
                <th>Total Value</th>
                <th>Average Value</th>
                <th>Percentage of Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                @php
                    $categoryCount = $category->assets->count();
                    $categoryValue = $category->assets->sum('purchase_price');
                    $averageValue = $categoryCount > 0 ? $categoryValue / $categoryCount : 0;
                    $percentage = $totalSummary['total_value'] > 0 ? ($categoryValue / $totalSummary['total_value']) * 100 : 0;
                @endphp
                <tr>
                    <td class="category-cell">{{ $category->name }}</td>
                    <td class="count-cell">{{ $categoryCount }}</td>
                    <td class="value-cell">PHP {{ number_format($categoryValue, 2) }}</td>
                    <td class="value-cell">PHP {{ number_format($averageValue, 2) }}</td>
                    <td class="count-cell">{{ number_format($percentage, 1) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="no-data">
                        <strong>No categories found</strong><br>
                        <small>No asset categories are currently available in the system</small>
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td class="category-cell"><strong>TOTAL</strong></td>
                <td class="count-cell"><strong>{{ $totalSummary['total_assets'] }}</strong></td>
                <td class="value-cell"><strong>PHP {{ number_format($totalSummary['total_value'], 2) }}</strong></td>
                <td class="value-cell"><strong>PHP {{ $totalSummary['total_assets'] > 0 ? number_format($totalSummary['total_value'] / $totalSummary['total_assets'], 2) : '0.00' }}</strong></td>
                <td class="count-cell"><strong>100.0%</strong></td>
            </tr>
        </tfoot>
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
