<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Codes Package - {{ $package->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        
        .page {
            page-break-after: always;
            margin-bottom: 20px;
        }
        
        .page:last-child {
            page-break-after: avoid;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 18px;
            text-align: center;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 12px;
        }
        
        .grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .grid-row {
            display: table-row;
        }
        
        .grid-cell {
            display: table-cell;
            width: 25%;
            height: 170px;
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
            vertical-align: top;
            word-wrap: break-word;
        }
        
        .qr-code {
            width: 80px;
            height: 80px;
            margin: 0 auto 8px;
        }
        
        .code-text {
            font-weight: bold;
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
        }
        
        .subjects {
            font-size: 9px;
            color: #333;
            margin-bottom: 5px;
            line-height: 1.2;
            max-height: 45px;
            overflow: hidden;
            text-align: center;
            font-weight: 400;
            font-family: Arial, sans-serif;
        }
        
        .expires-at {
            font-size: 10px;
            color: #666;
            font-style: italic;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
            color: #999;
        }

        
    </style>
</head>
<body>
    @foreach($pages as $pageIndex => $page)
        <div class="page">
            <div class="header">
                <h1>{{ $package->name }}</h1>
                <p>Package ID: {{ $package->id }} | Generated: {{ date('Y-m-d H:i:s') }}</p>
                <p>Page {{ $pageIndex + 1 }} of {{ count($pages) }}</p>
            </div>
            
            <div class="grid">
                @for($row = 0; $row < 4; $row++)
                    <div class="grid-row">
                        @for($col = 0; $col < 4; $col++)
                            @php
                                $index = $row * 4 + $col;
                                $codeData = isset($page[$index]) ? $page[$index] : null;
                            @endphp
                            
                            <div class="grid-cell">
                                @if($codeData)
                                    <img src="data:image/svg+xml;base64,{{ $codeData['qr_code'] }}" class="qr-code" alt="QR Code">
                                    <div class="code-text">{{ $codeData['code'] }}</div>
                                    <div class="subjects">{{ $codeData['subjects'] }}</div>
                                    <div class="expires-at">Expires: {{ date('Y-m-d', strtotime($codeData['expires_at'])) }}</div>
                                @else
                                    <!-- Empty cell -->
                                @endif
                            </div>
                        @endfor
                    </div>
                @endfor
            </div>
            
            <div class="footer">
                <p>Total Codes: {{ $package->codes->count() }} | Expires: {{ date('Y-m-d', strtotime($package->expires_at)) }}</p>
            </div>
        </div>
    @endforeach
</body>
</html> 