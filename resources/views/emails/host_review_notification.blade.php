<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>You received a new review!</title>
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
            background-color: #10b981;
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
            font-size: 16px;
            line-height: 1.6;
        }
        .review-details {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 20px 0;
            background-color: #10b981;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
        }
        .center {
            text-align: center;
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
            <h1>New Review Received</h1>
        </div>
        <div class="content">
            <p>Hi {{ $reservation->room->user->name ?? 'Host' }},</p>
            <p>Guest <strong>{{ $reservation->user->name ?? 'A Guest' }}</strong> has just left a review for their stay at <strong>{{ $reservation->room->title ?? 'your property' }}</strong>.</p>
            
            <div class="review-details">
                <p><strong>Room Space:</strong> {{ $review->room_space }} / 5</p>
                <p><strong>Room Amenities:</strong> {{ $review->room_amenities }} / 5</p>
                <p><strong>Room Arrangement:</strong> {{ $review->room_arrangement }} / 5</p>
                @if($review->dining_services)
                <p><strong>Dining & Services:</strong> {{ $review->dining_services }} / 5</p>
                @endif
                <p><strong>Room Cleanness:</strong> {{ $review->room_cleanness }} / 5</p>
                <p><strong>Stay Location:</strong> {{ $review->stay_location }} / 5</p>
                
                @if($review->description)
                <p><strong>Comment:</strong></p>
                <p><em>"{{ $review->description }}"</em></p>
                @endif
            </div>

            <p>Please review and approve or reject this review for your property listing.</p>
            
            <div class="center">
                <a href="{{ route('host.reviews.show', $reservation->id) }}" class="btn">View & Approve</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Hotel Host') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
