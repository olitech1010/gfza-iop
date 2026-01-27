<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS ID Card</title>
    <style>
        @page {
            margin: 0;
            size: 85.6mm 53.98mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            width: 85.6mm;
            height: 53.98mm;
        }
        
        .card {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1e3a5f 0%, #0e7490 100%);
            color: white;
            display: flex;
            overflow: hidden;
            position: relative;
        }
        
        .card-left {
            width: 55%;
            padding: 8px 10px;
            display: flex;
            flex-direction: column;
        }
        
        .card-right {
            width: 45%;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 8px;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 6px;
        }
        
        .logo {
            width: 30px;
            height: 30px;
            object-fit: contain;
        }
        
        .org-name {
            font-size: 7px;
            font-weight: 600;
            line-height: 1.2;
        }
        
        .card-title {
            background: rgba(255,255,255,0.15);
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        
        .user-info {
            flex: 1;
        }
        
        .user-name {
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 4px;
            line-height: 1.2;
        }
        
        .user-detail {
            font-size: 7px;
            opacity: 0.9;
            margin-bottom: 2px;
        }
        
        .user-detail strong {
            display: inline-block;
            width: 45px;
        }
        
        .staff-id-badge {
            background: #fbbf24;
            color: #1e3a5f;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
            text-align: center;
            margin-top: auto;
        }
        
        .photo-container {
            width: 60px;
            height: 70px;
            background: #f1f5f9;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .photo-placeholder {
            color: #94a3b8;
            font-size: 24px;
        }
        
        .qr-container {
            width: 55px;
            height: 55px;
            background: white;
            padding: 3px;
            border-radius: 4px;
        }
        
        .qr-container img, .qr-container svg {
            width: 100%;
            height: 100%;
        }
        
        .validity {
            font-size: 6px;
            color: #64748b;
            margin-top: 4px;
            text-align: center;
        }
        
        .watermark {
            position: absolute;
            bottom: -20px;
            right: -20px;
            width: 80px;
            height: 80px;
            opacity: 0.05;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-left">
            <div class="logo-section">
                @if($logo)
                    <img src="{{ $logo }}" alt="GFZA Logo" class="logo">
                @endif
                <div class="org-name">
                    GHANA FREE ZONES<br>AUTHORITY
                </div>
            </div>
            
            <div class="card-title">NSS PERSONNEL</div>
            
            <div class="user-info">
                <div class="user-name">{{ strtoupper($user->first_name . ' ' . $user->last_name) }}</div>
                <div class="user-detail"><strong>Dept:</strong> {{ $user->department?->name ?? 'N/A' }}</div>
                <div class="user-detail"><strong>Title:</strong> {{ $user->job_title ?? 'NSS Personnel' }}</div>
            </div>
            
            <div class="staff-id-badge">{{ $user->staff_id }}</div>
        </div>
        
        <div class="card-right">
            <div class="photo-container">
                @if($photo)
                    <img src="{{ $photo }}" alt="Photo">
                @else
                    <span class="photo-placeholder">👤</span>
                @endif
            </div>
            
            <div class="qr-container">
                <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
            </div>
            
            <div class="validity">Valid: {{ now()->format('Y') }}</div>
        </div>
        
        @if($logo)
            <img src="{{ $logo }}" alt="" class="watermark">
        @endif
    </div>
</body>
</html>
