<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Connection - {{ config('app.name', 'PropDrip') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f8fafc;
            color: #0f172a;
        }
        .card {
            background: white;
            padding: 32px;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            text-align: center;
            max-width: 400px;
            width: 90%;
            border: 1px solid #e2e8f0;
        }
        .icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        .icon.success {
            background-color: #ecfdf5;
            color: #059669;
        }
        .icon.error {
            background-color: #fef2f2;
            color: #dc2626;
        }
        h2 {
            margin: 0 0 8px 0;
            font-size: 20px;
            font-weight: 700;
        }
        p {
            margin: 0 0 20px 0;
            font-size: 14px;
            color: #64748b;
            line-height: 1.5;
        }
        .btn {
            background-color: #0f172a;
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            display: inline-block;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="card">
        @if($success)
            <div class="icon success">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2>Connected Successfully!</h2>
            <p>{{ $message }}<br><span style="font-weight:600; color:#059669;">{{ $phone ?? '' }}</span></p>
            <p style="font-size:12px; color:#94a3b8;">Closing this window and updating your dashboard...</p>
        @else
            <div class="icon error">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <h2>Connection Failed</h2>
            <p>{{ $message }}</p>
            <button onclick="window.close()" class="btn">Close Window</button>
        @endif
    </div>

    <script>
        // Auto-close and refresh parent window
        setTimeout(function() {
            if (window.opener && !window.opener.closed) {
                try {
                    window.opener.location.reload();
                } catch(e) {}
                window.close();
            } else {
                @if($success)
                    window.location.href = "{{ route('settings.whatsapp') }}";
                @endif
            }
        }, 1200);
    </script>
</body>
</html>
