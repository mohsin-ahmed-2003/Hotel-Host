<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Verify Your Email — {{ $siteName }}</title></head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:40px 20px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(30,58,138,0.10);">

      <tr>
        <td style="background:linear-gradient(135deg,#1E3A8A 0%,#2563eb 100%);padding:36px 40px;text-align:center;">
          @if($siteLogo)
            <img src="{{ $siteLogo }}" alt="{{ $siteName }}" style="height:52px;width:auto;max-width:180px;object-fit:contain;margin-bottom:12px;display:block;margin-left:auto;margin-right:auto;">
          @endif
          <div style="font-size:40px;margin-bottom:10px;">✉️</div>
          <h1 style="color:#ffffff;font-size:24px;font-weight:800;margin:0;">Verify Your Email Address</h1>
          <p style="color:#93afd4;font-size:13px;margin:6px 0 0;">{{ $siteName }}</p>
        </td>
      </tr>

      <tr>
        <td style="padding:40px 40px 32px;">
          <p style="color:#475569;font-size:15px;line-height:1.7;margin:0 0 20px;">
            Hi <strong>{{ $userName }}</strong>! Please verify your email address to unlock exclusive benefits:
          </p>

          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            <tr>
              <td style="background:#eff6ff;border-radius:12px;padding:18px 20px;border-left:4px solid #2563eb;">
                <p style="margin:0;font-size:14px;color:#1e3a8a;font-weight:700;">🏷️ First to grab hotel discounts</p>
                <p style="margin:6px 0 0;font-size:13px;color:#475569;line-height:1.6;">Get notified instantly when hotels set special discounts and limited-time offers — before anyone else.</p>
              </td>
            </tr>
          </table>

          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            <tr>
              <td style="background:#f0fdf4;border-radius:12px;padding:18px 20px;border-left:4px solid #16a34a;">
                <p style="margin:0;font-size:14px;color:#15803d;font-weight:700;">🔔 Updates & Notifications</p>
                <p style="margin:6px 0 0;font-size:13px;color:#475569;line-height:1.6;">Stay informed about new hotels, best locations, and exclusive deals worldwide.</p>
              </td>
            </tr>
          </table>

          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:32px;">
            <tr>
              <td style="background:#fefce8;border-radius:12px;padding:18px 20px;border-left:4px solid #d97706;">
                <p style="margin:0;font-size:14px;color:#92400e;font-weight:700;">✅ Verified Badge</p>
                <p style="margin:6px 0 0;font-size:13px;color:#475569;line-height:1.6;">Earn your green verified badge — a mark of trust on your profile.</p>
              </td>
            </tr>
          </table>

          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            <tr>
              <td align="center">
                <a href="{{ $verifyUrl }}" style="display:inline-block;background:linear-gradient(135deg,#1E3A8A,#2563eb);color:#ffffff;text-decoration:none;font-size:16px;font-weight:700;padding:16px 40px;border-radius:12px;letter-spacing:0.3px;">
                  ✅ Verify My Email →
                </a>
              </td>
            </tr>
          </table>

          <p style="color:#94a3b8;font-size:12px;text-align:center;margin:0;">This link expires in 24 hours. If you did not create an account, ignore this email.</p>
        </td>
      </tr>

      <tr>
        <td style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:20px 40px;text-align:center;">
          <p style="color:#94a3b8;font-size:12px;margin:0;">© {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
