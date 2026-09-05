<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to PropDrip</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background-color: #0f172a; padding: 32px 24px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px; }
        .content { padding: 32px 24px; line-height: 1.6; }
        .btn { display: inline-block; background-color: #4f46e5; color: #ffffff; font-weight: 700; text-decoration: none; padding: 12px 28px; border-radius: 12px; margin-top: 20px; }
        .footer { background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PropDrip Real Estate CRM</h1>
        </div>
        <div class="content">
            <h2>Welcome to PropDrip, {{ $userName }}!</h2>
            <p>Thank you for registering <strong>{{ $companyName }}</strong> with PropDrip. We're excited to help you manage inquiries, automate lead drips, and capture property leads effortlessly.</p>
            <p>To get started, verify your email address and set up your first project QR code!</p>
            <p style="text-align: center;">
                <a href="{{ $loginUrl }}" class="btn">Go to Dashboard &rarr;</a>
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} PropDrip. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
