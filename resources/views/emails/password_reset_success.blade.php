<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Password Reset Successful — {{ $siteName }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:40px 20px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(30,58,138,0.10);">

      <tr>
        <td style="background:linear-gradient(135deg,#15803d 0%,#16a34a 100%);padding:36px 40px;text-align:center;">
          @if($siteLogo)
            <img src="{{ $siteLogo }}" alt="{{ $siteName }}" style="height:52px;width:auto;max-width:180px;object-fit:contain;margin-bottom:12px;display:block;margin-left:auto;margin-right:auto;">
          @endif
          <div style="width:56px;height:56px;background:rgba(255,255,255,0.2);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;font-size:28px;">✅</div>
          <h1 style="color:#ffffff;font-size:22px;font-weight:800;margin:0;">Password Reset Successful</h1>
        </td>
      </tr>

      <tr>
        <td style="padding:40px 40px 32px;">
          <p style="color:#475569;font-size:15px;line-height:1.7;margin:0 0 20px;">
            Hi <strong>{{ $userName }}</strong>, your password has been successfully reset. You can now log in with your new password.
          </p>
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
            <tr>
              <td style="background:#f0fdf4;border-radius:10px;padding:14px 18px;border-left:4px solid #16a34a;">
                <p style="margin:0;font-size:13px;color:#15803d;line-height:1.6;">
                  🔒 If you did not make this change, please contact support immediately.
                </p>
              </td>
            </tr>
          </table>
          <a href="{{ url('/auth') }}" style="display:inline-block;background:linear-gradient(135deg,#15803d,#16a34a);color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;padding:13px 32px;border-radius:10px;">
            Login to Your Account →
          </a>
        </td>
      </tr>

      <tr>
        <td style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:24px 40px;text-align:center;">
          <p style="color:#94a3b8;font-size:12px;margin:0;">© {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
