<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            size: A4 landscape;
            margin: 20px;
        }
        body {
            margin: 0;
            padding: 0;
        }
        .page {
            display: block;
            margin-bottom: 20px;
        }
        .qr-container {
            display: inline-block;
            width: 100%;
        }
        .qr-item {
            display: inline-block;
            width: 180px;
            text-align: center;
            padding: 10px;
            border: 1px dashed #999;
            margin: 5px;
            vertical-align: top;
        }
        .qr-code {
            width: 150px;
            height: 150px;
            margin-bottom: 10px;
        }
        .asset-info {
            font-size: 12px;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    @foreach($assets->chunk(5) as $rowAssets)
        <div class="page">
            <div class="qr-container">
                @foreach($rowAssets as $asset)
                    <div class="qr-item">
                        <img src="{{ public_path('storage/' . $asset->qr_code) }}" class="qr-code">
                        <div class="asset-info">
                            <strong>{{ $asset->name }}</strong><br>
                            {{ $asset->serial_number }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</body>
</html>