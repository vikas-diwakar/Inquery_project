<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to {{ $companyName }}</title>
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
            @if(!empty($companyLogo))
                <img src="{{ $companyLogo }}" alt="{{ $companyName }}" style="max-height: 48px; max-width: 200px; margin-bottom: 12px; background: #ffffff; padding: 4px 8px; border-radius: 8px;">
            @endif
            <h1>{{ $companyName }}</h1>
            <p style="margin: 4px 0 0; font-size: 13px; color: #94a3b8;">Real Estate & Sales Portal</p>
        </div>
        <div class="content">
            <h2>Welcome, {{ $userName }}!</h2>
            <p>Your workspace account for <strong>{{ $companyName }}</strong> is now ready. You can sign in to your dedicated portal to manage project inventory, capture customer inquiries, and coordinate sales follow-ups.</p>
            
            <p style="text-align: center;">
                <a href="{{ $loginUrl }}" class="btn">Sign In to {{ $companyName }} &rarr;</a>
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
