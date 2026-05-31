<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Property Listing Received - {{ $siteName }}</title>
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
          <p style="color:#93afd4;font-size:14px;margin:6px 0 0;">Hosting Done Right</p>
        </td>
      </tr>

      <!-- Body -->
      <tr>
        <td style="padding:40px 40px 32px;">
          <h2 style="color:#1E3A8A;font-size:22px;font-weight:800;margin:0 0 8px;">Success, {{ $userName }}! 🏠</h2>
          <p style="color:#475569;font-size:15px;line-height:1.7;margin:0 0 20px;">
            Your property listing <strong>"{{ $room->display_name }}"</strong> has been received and is now being reviewed by our team.
          </p>

          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            <tr>
              <td style="background:#eff6ff;border-radius:12px;padding:18px 20px;border-left:4px solid #2563eb;">
                <p style="margin:0;font-size:14px;color:#1e3a8a;font-weight:700;">🔍 What happens next?</p>
                <p style="margin:6px 0 0;font-size:13px;color:#475569;line-height:1.6;">Our admins usually review listings within 24-48 hours. You'll receive another email as soon as your property is approved and live.</p>
              </td>
            </tr>
          </table>

          <h3 style="color:#1e3a8a;font-size:18px;font-weight:700;margin:32px 0 16px;">Boost Your Bookings 🚀</h3>
          
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px;">
            <tr>
              <td style="padding-bottom:12px;">
                <p style="margin:0;font-size:14px;color:#1e3a8a;font-weight:700;">✅ Professional Photos</p>
                <p style="margin:4px 0 0;font-size:13px;color:#475569;">High-quality images are the #1 factor in guest decision-making.</p>
              </td>
            </tr>
            <tr>
              <td style="padding-bottom:12px;">
                <p style="margin:0;font-size:14px;color:#1e3a8a;font-weight:700;">✅ Multiple Listings</p>
                <p style="margin:4px 0 0;font-size:13px;color:#475569;">Hosts with 3+ rooms see 50% more inquiries on average.</p>
              </td>
            </tr>
          </table>

          <!-- CTA -->
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px;">
            <tr>
              <td align="center">
                <a href="{{ url('/host/properties') }}" style="display:inline-block;background:linear-gradient(135deg,#1E3A8A,#2563eb);color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;padding:14px 36px;border-radius:10px;letter-spacing:0.3px;">
                  Go to Dashboard →
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
          <p style="color:#cbd5e1;font-size:11px;margin:0;">You're part of our growing host community.</p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
