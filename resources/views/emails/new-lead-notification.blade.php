<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Lead Captured - {{ $customerName }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background-color: #0f172a; padding: 24px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 800; }
        .badge { display: inline-block; background-color: #10b981; color: #ffffff; font-size: 11px; font-weight: 800; text-transform: uppercase; padding: 4px 10px; border-radius: 9999px; margin-bottom: 12px; }
        .content { padding: 28px 24px; line-height: 1.6; }
        .lead-card { background: #f8fafc; border-left: 4px solid #4f46e5; border-radius: 8px; padding: 16px; margin: 16px 0; }
        .btn { display: inline-block; background-color: #4f46e5; color: #ffffff; font-weight: 700; text-decoration: none; padding: 12px 24px; border-radius: 10px; }
        .footer { background-color: #f1f5f9; padding: 16px; text-align: center; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="badge">🔥 New Lead</span>
            <h1>New Lead Received for {{ $projectName }}</h1>
        </div>
        <div class="content">
            <p>Hello Team,</p>
            <p>A new lead has just been submitted via QR / Web form for <strong>{{ $projectName }}</strong>.</p>
            
            <div class="lead-card">
                <p style="margin: 4px 0;"><strong>Name:</strong> {{ $customerName }}</p>
                <p style="margin: 4px 0;"><strong>Phone:</strong> {{ $phone }}</p>
                @if($email)
                <p style="margin: 4px 0;"><strong>Email:</strong> {{ $email }}</p>
                @endif
                @if($budget)
                <p style="margin: 4px 0;"><strong>Budget:</strong> ${{ number_format($budget, 2) }}</p>
                @endif
                @if($preferredType)
                <p style="margin: 4px 0;"><strong>Preferred Option:</strong> {{ $preferredType }}</p>
                @endif
                @if($userMessage)
                <p style="margin: 4px 0;"><strong>Message:</strong> {{ $userMessage }}</p>
                @endif
            </div>

            <p style="text-align: center;">
                <a href="{{ $viewUrl }}" class="btn">View Lead in CRM &rarr;</a>
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} PropDrip Lead Automation System.</p>
        </div>
    </div>
</body>
</html>
