<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $subjectTitle }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background-color: #0f172a; padding: 24px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 800; }
        .content { padding: 28px 24px; line-height: 1.6; }
        .footer { background-color: #f1f5f9; padding: 16px; text-align: center; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(!empty($companyLogo))
                <img src="{{ $companyLogo }}" alt="{{ $companyName }}" style="max-height: 40px; max-width: 180px; margin-bottom: 10px; background: #ffffff; padding: 4px 8px; border-radius: 8px;">
            @endif
            <h1>{{ $companyName }}</h1>
            <p style="margin: 4px 0 0; font-size: 13px; color: #94a3b8;">{{ $projectName }}</p>
        </div>
        <div class="content">
            <p>Hi {{ $customerName }},</p>
            <div style="white-space: pre-line;">{!! nl2br(e($bodyMessage)) !!}</div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
