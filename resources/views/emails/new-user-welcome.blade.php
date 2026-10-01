<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $companyName }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 30px 15px;
            -webkit-font-smoothing: antialiased;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            padding: 36px 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 6px 0 0;
            font-size: 13px;
            color: #cbd5e1;
        }
        .content {
            padding: 36px 30px;
            line-height: 1.6;
        }
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            background-color: #e0e7ff;
            color: #4338ca;
            margin-bottom: 12px;
        }
        .credentials-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px 24px;
            margin: 24px 0;
        }
        .credentials-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            margin-bottom: 14px;
        }
        .cred-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #cbd5e1;
        }
        .cred-row:last-child {
            border-bottom: none;
        }
        .cred-label {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
        }
        .cred-value {
            font-size: 14px;
            color: #0f172a;
            font-weight: 700;
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        }
        .btn-container {
            text-align: center;
            margin: 30px 0 20px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            color: #ffffff !important;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            padding: 14px 34px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
        }
        .security-notice {
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 12px;
            color: #92400e;
            margin-top: 24px;
        }
        .projects-list {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 6px;
        }
        .project-tag {
            display: inline-block;
            background-color: #f1f5f9;
            color: #334155;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #e2e8f0;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="header">
            @if(!empty($companyLogo))
                <img src="{{ $companyLogo }}" alt="{{ $companyName }}" style="max-height: 44px; max-width: 180px; margin-bottom: 12px; background: #ffffff; padding: 4px 10px; border-radius: 8px;">
            @endif
            <h1>{{ $companyName }}</h1>
            <p>Team Workspace & CRM Portal</p>
        </div>

        <!-- Content -->
        <div class="content">
            <span class="role-badge">{{ $roleName }}</span>
            <h2 style="margin: 0 0 10px; font-size: 20px; font-weight: 800; color: #0f172a;">Welcome to the Team, {{ $userName }}!</h2>
            <p style="margin: 0 0 16px; font-size: 14px; color: #475569;">
                An account has been created for you on the <strong>{{ $companyName }}</strong> CRM portal. Below are your login credentials to access the system:
            </p>

            <!-- Credentials Box -->
            <div class="credentials-card">
                <div class="credentials-title">Your Login Credentials</div>
                
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 500; border-bottom: 1px dashed #cbd5e1;">Portal URL:</td>
                        <td style="padding: 8px 0; font-size: 13px; text-align: right; border-bottom: 1px dashed #cbd5e1;">
                            <a href="{{ $loginUrl }}" style="color: #4f46e5; text-decoration: none; font-weight: 600;">{{ $loginUrl }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 500; border-bottom: 1px dashed #cbd5e1;">Email / Username:</td>
                        <td style="padding: 8px 0; font-size: 14px; color: #0f172a; font-weight: 700; text-align: right; font-family: monospace; border-bottom: 1px dashed #cbd5e1;">
                            {{ $userEmail }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 500; border-bottom: 1px dashed #cbd5e1;">Password:</td>
                        <td style="padding: 8px 0; font-size: 14px; color: #4338ca; font-weight: 800; text-align: right; font-family: monospace; background: #e0e7ff; padding-right: 8px; border-radius: 6px;">
                            {{ $password }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 500;">Assigned Role:</td>
                        <td style="padding: 8px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right;">
                            {{ $roleName }}
                        </td>
                    </tr>
                    @if(!empty($assignedProjects))
                    <tr>
                        <td style="padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 500; vertical-align: top;">Assigned Projects:</td>
                        <td style="padding: 8px 0; font-size: 12px; color: #0f172a; text-align: right;">
                            @foreach($assignedProjects as $pName)
                                <span class="project-tag">{{ $pName }}</span>
                            @endforeach
                        </td>
                    </tr>
                    @endif
                </table>
            </div>

            <!-- Login Button -->
            <div class="btn-container">
                <a href="{{ $loginUrl }}" class="btn">Log In to Your Workspace &rarr;</a>
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <strong>🔒 Security Recommendation:</strong> Please do not share these credentials with anyone. We recommend updating your password from your account profile after your first login.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 4px;">&copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.</p>
            <p style="margin: 0; font-size: 11px;">This is an automated system email. Please do not reply directly to this message.</p>
        </div>
    </div>
</body>
</html>