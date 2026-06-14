<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - Hotel Host</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --accent: #2563eb;
            --border-color: #e2e8f0;
            --success: #10b981;
            --warning: #f59e0b;
        }

        body.dark-mode {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --border-color: #334155;
            --accent: #3b82f6;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
        }

        .main-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 24px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: var(--text-primary);
            margin: 0 0 8px 0;
        }

        .page-subtitle {
            font-size: 16px;
            color: var(--text-secondary);
            margin: 0;
        }

        .tabs-container {
            display: flex;
            gap: 30px;
            border-bottom: 2px solid var(--border-color);
            margin-bottom: 30px;
        }

        .tab-btn {
            padding: 12px 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--text-secondary);
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: -2px;
        }

        .tab-btn:hover {
            color: var(--text-primary);
        }

        .tab-btn.active {
            color: var(--accent);
            border-bottom-color: var(--accent);
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .review-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            display: flex;
            gap: 24px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .review-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .reviewer-info {
            width: 200px;
            flex-shrink: 0;
            border-right: 1px solid var(--border-color);
            padding-right: 24px;
        }

        .reviewer-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--text-secondary);
            margin-bottom: 12px;
            object-fit: cover;
        }

        .reviewer-name {
            font-weight: 700;
            font-size: 16px;
            margin: 0 0 4px 0;
        }

        .review-date {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .review-body {
            flex-grow: 1;
        }

        .room-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--accent);
            margin: 0 0 12px 0;
        }

        .rating-stars {
            color: #fbbf24;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .review-text {
            font-size: 15px;
            line-height: 1.6;
            color: var(--text-primary);
            margin: 0 0 20px 0;
        }

        .review-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding-top: 16px;
            border-top: 1px dashed var(--border-color);
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
        }

        .btn-approve {
            background: var(--accent);
            color: white;
        }

        .btn-approve:hover {
            background: #1d4ed8;
            transform: scale(1.02);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .badge-approved {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px dashed var(--border-color);
        }

        .empty-icon {
            font-size: 48px;
            color: var(--text-secondary);
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
</head>
<body>

@include('partials.header')

<div class="main-container">
    <div class="page-header">
        <h1 class="page-title">Accounts</h1>
        <p class="page-subtitle">Manage your profile, settings, and guest reviews.</p>
    </div>

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid rgba(16,185,129,0.2);">
            <i class="fas fa-check-circle" style="margin-right: 8px;"></i> {{ session('success') }}
        </div>
    @endif

    <div class="tabs-container">
        <button class="tab-btn" onclick="switchTab('profile')">Profile Overview</button>
        <button class="tab-btn active" onclick="switchTab('reviews')">Guest Reviews</button>
        <button class="tab-btn" onclick="switchTab('settings')">Settings</button>
    </div>

    <!-- Profile Tab -->
    <div id="profile" class="tab-content">
        <div class="empty-state">
            <i class="fas fa-user-circle empty-icon"></i>
            <h3>Profile Settings</h3>
            <p>Your profile information will be displayed here.</p>
            <a href="/profile" class="btn btn-approve" style="margin-top: 20px;">Go to My Profile</a>
        </div>
    </div>

    <!-- Reviews Tab -->
    <div id="reviews" class="tab-content active">
        @if($hostReviews->count() > 0)
            @foreach($hostReviews as $review)
                <div class="review-card">
                    <div class="reviewer-info">
                        @if($review->user->profile_image)
                            <img src="{{ asset($review->user->profile_image) }}" alt="Avatar" class="reviewer-avatar">
                        @else
                            <div class="reviewer-avatar"><i class="fas fa-user"></i></div>
                        @endif
                        <h4 class="reviewer-name">{{ $review->user->name ?? 'Guest' }}</h4>
                        <div class="review-date">Left on {{ $review->created_at->format('M d, Y') }}</div>
                    </div>
                    
                    <div class="review-body">
                        <div class="room-title">
                            <i class="fas fa-home" style="margin-right: 6px;"></i> 
                            {{ $review->room->title ?? 'Unknown Property' }}
                        </div>
                        
                        <div class="rating-stars">
                            @for($i=1; $i<=5; $i++)
                                <i class="fa{{ $i <= ($review->rating ?? 5) ? 's' : 'r' }} fa-star"></i>
                            @endfor
                            <span style="color: var(--text-secondary); margin-left: 8px;">({{ $review->rating ?? 5 }}.0)</span>
                        </div>
                        
                        <p class="review-text">
                            {{ $review->review_text ?? $review->description ?? 'No detailed description provided.' }}
                        </p>
                        
                        <div class="review-actions">
                            @if($review->host_approved)
                                <div class="badge badge-approved">
                                    <i class="fas fa-check-circle"></i> Approved & Published
                                </div>
                            @else
                                <form action="{{ route('account.reviews.approve', $review->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="btn btn-approve">
                                        <i class="fas fa-check"></i> Approve Review
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="fas fa-star empty-icon"></i>
                <h3>No Reviews Yet</h3>
                <p>When guests leave reviews for your properties, they will appear here for your approval.</p>
            </div>
        @endif
    </div>

    <!-- Settings Tab -->
    <div id="settings" class="tab-content">
        <div class="empty-state">
            <i class="fas fa-cog empty-icon"></i>
            <h3>Account Settings</h3>
            <p>Your account preferences and security settings will be available here.</p>
        </div>
    </div>

</div>

<script>
    function switchTab(tabId) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Show selected tab
        document.getElementById(tabId).classList.add('active');
        event.currentTarget.classList.add('active');
    }
</script>

</body>
</html>
