<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inquiry Received - {{ $projectName }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background-color: #4f46e5; padding: 32px 24px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 800; }
        .content { padding: 32px 24px; line-height: 1.6; }
        .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin: 20px 0; }
        .btn { display: inline-block; background-color: #10b981; color: #ffffff; font-weight: 700; text-decoration: none; padding: 12px 28px; border-radius: 12px; }
        .footer { background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thank You for Your Inquiry!</h1>
        </div>
        <div class="content">
            <h2>Dear {{ $customerName }},</h2>
            <p>Thank you for expressing interest in <strong>{{ $projectName }}</strong> by {{ $companyName }}.</p>
            <p>We have received your inquiry and our sales team will reach out to you shortly on WhatsApp / Phone with full pricing and floor plan details.</p>
            
            <div class="info-box">
                <p style="margin: 0 0 8px 0;"><strong>Inquiry Summary:</strong></p>
                <p style="margin: 4px 0;"><strong>Project:</strong> {{ $projectName }}</p>
                @if($location)
                <p style="margin: 4px 0;"><strong>Location:</strong> {{ $location }}</p>
                @endif
                <p style="margin: 4px 0;"><strong>Phone:</strong> {{ $phone }}</p>
            </div>

            <p>If you'd like to explore project brochures or schedule a site visit immediately, feel free to reply to this email.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
