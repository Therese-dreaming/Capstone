<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            margin: 0;
            padding: 20px;
        }
        .qr-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
        }
        .qr-item {
            width: 180px;
            height: 240px;
            text-align: center;
            padding: 15px;
            border-right: 1px dashed #999;
            border-bottom: 1px dashed #999;
            page-break-inside: avoid;
        }
        .qr-item:nth-child(3n) {
            border-right: none;
        }
        .qr-code {
            width: 150px;
            height: auto;
            margin-bottom: 10px;
        }
        .asset-info {
            font-size: 12px;
            line-height: 1.2;
            margin-top: 10px;
        }
        @page {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="qr-grid">
        @foreach($assets as $asset)
            <div class="qr-item">
                <img src="{{ public_path('storage/' . $asset->qr_code) }}" class="qr-code">
                <div class="asset-info">
                    <strong>{{ $asset->name }}</strong><br>
                    {{ $asset->serial_number }}
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>