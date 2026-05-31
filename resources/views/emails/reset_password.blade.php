<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Password Reset Code — {{ $siteName }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:40px 20px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(30,58,138,0.10);">

      <!-- Header -->
      <tr>
        <td style="background:linear-gradient(135deg,#1E3A8A 0%,#2563eb 100%);padding:36px 40px;text-align:center;">
          @if($siteLogo)
            <img src="{{ $siteLogo }}" alt="{{ $siteName }}" style="height:52px;width:auto;max-width:180px;object-fit:contain;margin-bottom:12px;display:block;margin-left:auto;margin-right:auto;">
          @endif
          <h1 style="color:#ffffff;font-size:24px;font-weight:800;margin:0;">Password Reset Request</h1>
          <p style="color:#93afd4;font-size:13px;margin:6px 0 0;">{{ $siteName }}</p>
        </td>
      </tr>

      <!-- Body -->
      <tr>
        <td style="padding:40px 40px 32px;">
          <p style="color:#475569;font-size:15px;line-height:1.7;margin:0 0 24px;">
            Hi <strong>{{ $userName }}</strong>, we received a request to reset your password. Use the code below on the password reset page.
          </p>

          <!-- Code box -->
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
            <tr>
              <td align="center" style="background:#eff6ff;border-radius:14px;padding:28px 20px;border:2px dashed #2563eb;">
                <p style="margin:0 0 8px;font-size:13px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:1px;">Your Reset Code</p>
                <p style="margin:0;font-size:42px;font-weight:900;color:#1E3A8A;letter-spacing:12px;font-family:'Courier New',monospace;">{{ $resetCode }}</p>
                <p style="margin:12px 0 0;font-size:12px;color:#94a3b8;">This code expires in <strong>15 minutes</strong></p>
              </td>
            </tr>
          </table>

          <!-- Warning -->
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
            <tr>
              <td style="background:#fef2f2;border-radius:10px;padding:14px 18px;border-left:4px solid #ef4444;">
                <p style="margin:0;font-size:13px;color:#b91c1c;line-height:1.6;">
                  ⚠️ If you did not request a password reset, please ignore this email. Your account remains secure.
                </p>
              </td>
            </tr>
          </table>

          <a href="{{ url('/forgot-password') }}" style="display:inline-block;background:linear-gradient(135deg,#1E3A8A,#2563eb);color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;padding:13px 32px;border-radius:10px;">
            Go to Reset Page →
          </a>
        </td>
      </tr>

      <!-- Footer -->
      <tr>
        <td style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:24px 40px;text-align:center;">
          <p style="color:#94a3b8;font-size:12px;margin:0 0 4px;">© {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
          <p style="color:#cbd5e1;font-size:11px;margin:0;">This is an automated message. Please do not reply.</p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
