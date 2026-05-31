<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome to {{ $siteName }}</title>
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
          <h1 style="color:#ffffff;font-size:26px;font-weight:800;margin:0;letter-spacing:-0.5px;">{{ $siteName }}</h1>
          <p style="color:#93afd4;font-size:14px;margin:6px 0 0;">Your Premium Hotel Booking Platform</p>
        </td>
      </tr>

      <!-- Body -->
      <tr>
        <td style="padding:40px 40px 32px;">
          <h2 style="color:#1E3A8A;font-size:22px;font-weight:800;margin:0 0 8px;">Welcome, {{ $userName }}! 🎉</h2>
          <p style="color:#475569;font-size:15px;line-height:1.7;margin:0 0 20px;">
            We're thrilled to have you join <strong>{{ $siteName }}</strong> — your gateway to premium hotel experiences around the world.
          </p>

          <!-- Feature cards -->
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            <tr>
              <td style="background:#eff6ff;border-radius:12px;padding:18px 20px;border-left:4px solid #2563eb;">
                <p style="margin:0;font-size:14px;color:#1e3a8a;font-weight:700;">🏨 Hotels Worldwide</p>
                <p style="margin:6px 0 0;font-size:13px;color:#475569;line-height:1.6;">Discover thousands of premium hotels, resorts, and boutique stays across every continent — from city centres to hidden retreats.</p>
              </td>
            </tr>
          </table>

          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            <tr>
              <td style="background:#f0fdf4;border-radius:12px;padding:18px 20px;border-left:4px solid #16a34a;">
                <p style="margin:0;font-size:14px;color:#15803d;font-weight:700;">🏷️ Exclusive Discounts</p>
                <p style="margin:6px 0 0;font-size:13px;color:#475569;line-height:1.6;">We'll notify you whenever hotels set special discounts and limited-time offers — so you never miss a great deal on your next stay.</p>
              </td>
            </tr>
          </table>

          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:32px;">
            <tr>
              <td style="background:#fefce8;border-radius:12px;padding:18px 20px;border-left:4px solid #d97706;">
                <p style="margin:0;font-size:14px;color:#92400e;font-weight:700;">📍 Best Locations</p>
                <p style="margin:6px 0 0;font-size:13px;color:#475569;line-height:1.6;">Find hotels in the best locations — near airports, city landmarks, beaches, and business districts — almost everywhere in the world.</p>
              </td>
            </tr>
          </table>

          <!-- CTA -->
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td align="center">
                <a href="{{ url('/') }}" style="display:inline-block;background:linear-gradient(135deg,#1E3A8A,#2563eb);color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;padding:14px 36px;border-radius:10px;letter-spacing:0.3px;">
                  Start Exploring Hotels →
                </a>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <!-- Footer -->
      <tr>
        <td style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:24px 40px;text-align:center;">
          <p style="color:#94a3b8;font-size:12px;margin:0 0 4px;">© {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
          <p style="color:#cbd5e1;font-size:11px;margin:0;">You received this email because you signed up at {{ $siteName }}.</p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
