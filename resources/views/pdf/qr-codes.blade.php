<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            size: A4 landscape;
            margin: 15px;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .page {
            display: block;
            margin-bottom: 15px;
        }
        .qr-container {
            display: inline-block;
            width: 100%;
        }
        .qr-item {
            display: inline-block;
            width: 120px;
            text-align: center;
            padding: 8px;
            border: 1px dashed #999;
            margin: 3px;
            vertical-align: top;
        }
        .qr-code {
            width: 100px;
            height: 100px;
            margin-bottom: 8px;
        }
        .asset-info {
            font-size: 10px;
            line-height: 1.1;
        }
        .asset-name {
            font-weight: bold;
            margin-bottom: 3px;
            word-wrap: break-word;
            font-size: 9px;
        }
        .serial-number {
            font-size: 8px;
            color: #666;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 1px 3px;
            border-radius: 2px;
            margin-top: 2px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }
        .header p {
            margin: 3px 0 0 0;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Asset QR Codes</h1>
        <p>Generated on {{ \Carbon\Carbon::now()->format('M d, Y H:i') }}</p>
        @if(request('date_from') || request('date_to'))
            <p>
                @if(request('date_from'))
                    From {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}
                @endif
                @if(request('date_from') && request('date_to'))
                    to
                @endif
                @if(request('date_to'))
                    {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                @endif
            </p>
        @endif
    </div>

    @foreach($assets->chunk(7) as $rowAssets)
        <div class="page">
            <div class="qr-container">
                @foreach($rowAssets as $asset)
                    <div class="qr-item">
                        @if($asset->qr_code)
                            <img src="{{ public_path('storage/' . $asset->qr_code) }}" class="qr-code">
                        @else
                            <div class="qr-code" style="background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #999; font-size: 8px;">
                                No QR Code
                            </div>
                        @endif
                        <div class="asset-info">
                            <div class="asset-name">{{ $asset->name }}</div>
                            <div class="serial-number">{{ $asset->serial_number }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</body>
</html>