<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>How was your stay?</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #6366f1;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
            text-align: center;
            font-size: 16px;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 20px 0;
            background-color: #6366f1;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
        }
        .footer {
            background-color: #f4f4f4;
            color: #888;
            padding: 20px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>How was your stay?</h1>
        </div>
        <div class="content">
            <p>Hi {{ $reservation->user->name ?? 'Guest' }},</p>
            <p>We hope you had a wonderful stay at <strong>{{ $reservation->room->title ?? 'our property' }}</strong>.</p>
            <p>Your feedback is incredibly valuable to us and to future guests. Could you take a moment to share your experience?</p>
            <a href="{{ route('user.reservations.review', $reservation->id) }}" class="btn">Give Review</a>
            <p>If the button doesn't work, copy and paste this link into your browser:</p>
            <p><a href="{{ route('user.reservations.review', $reservation->id) }}">{{ route('user.reservations.review', $reservation->id) }}</a></p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Hotel Host') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
