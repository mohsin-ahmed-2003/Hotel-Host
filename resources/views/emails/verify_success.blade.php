<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Email Verified — {{ $siteName }}</title></head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:40px 20px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(30,58,138,0.10);">

      <tr>
        <td style="background:linear-gradient(135deg,#15803d 0%,#16a34a 100%);padding:36px 40px;text-align:center;">
          @if($siteLogo)
            <img src="{{ $siteLogo }}" alt="{{ $siteName }}" style="height:52px;width:auto;max-width:180px;object-fit:contain;margin-bottom:12px;display:block;margin-left:auto;margin-right:auto;">
          @endif
          <div style="font-size:48px;margin-bottom:10px;">✅</div>
          <h1 style="color:#ffffff;font-size:24px;font-weight:800;margin:0;">You're Verified!</h1>
          <p style="color:#bbf7d0;font-size:13px;margin:6px 0 0;">{{ $siteName }}</p>
        </td>
      </tr>

      <tr>
        <td style="padding:40px 40px 32px;">
          <p style="color:#475569;font-size:15px;line-height:1.7;margin:0 0 20px;">
            Congratulations <strong>{{ $userName }}</strong>! 🎉 Your email has been verified. You now have full access to all verified member benefits:
          </p>

          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px;">
            <tr>
              <td style="background:#f0fdf4;border-radius:10px;padding:14px 18px;border-left:4px solid #16a34a;">
                <p style="margin:0;font-size:13px;color:#15803d;line-height:1.7;">
                  🏷️ <strong>Discount Alerts</strong> — You'll be the first to know when hotels set special discounts.<br>
                  🔔 <strong>Updates</strong> — Get notified about new hotels and best locations worldwide.<br>
                  ✅ <strong>Verified Badge</strong> — Your profile now shows a green verified badge.<br>
                  🎁 <strong>Exclusive Offers</strong> — Take advantage of member-only booking deals.
                </p>
              </td>
            </tr>
          </table>

          <p style="color:#475569;font-size:14px;line-height:1.7;margin:20px 0 28px;">
            Start exploring and take advantage of your verified status today!
          </p>

          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td align="center">
                <a href="{{ url('/') }}" style="display:inline-block;background:linear-gradient(135deg,#15803d,#16a34a);color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;padding:13px 32px;border-radius:10px;">
                  Explore Hotels Now →
                </a>
              </td>
            </tr>
          </table>
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
