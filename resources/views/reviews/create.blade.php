@extends('layouts.app')

@section('styles')
<style>
/* Scoped CSS for the review page to avoid depending on Bootstrap */
.review-page-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 100px);
    padding: 40px 20px;
    background-color: #f8fafc;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

.review-card {
    background: #ffffff;
    width: 100%;
    max-width: 600px;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.review-header {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    padding: 35px 25px;
    text-align: center;
    color: #ffffff;
}

.review-header h3 {
    margin: 0 0 10px 0;
    font-size: 24px;
    font-weight: 700;
}

.review-header p {
    margin: 0;
    font-size: 15px;
    opacity: 0.85;
}

.review-body {
    padding: 40px;
}

.rating-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0;
    border-bottom: 1px solid #f1f5f9;
}

.rating-label {
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.star-group {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.star-rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 6px;
}

.star-rating input[type="radio"] {
    display: none;
}

.star-rating label {
    font-size: 30px;
    color: #e2e8f0;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    margin: 0;
    line-height: 1;
}

/* Aggressive reset to prevent any global CSS from adding red asterisks to the stars */
.star-rating label::before,
.star-rating label::after,
.star-rating input::before,
.star-rating input::after,
.star-rating i::before,
.star-rating i::after {
    content: none !important;
    display: none !important;
}

/* Ensure the FontAwesome icon itself still works */
.star-rating label i::before {
    content: "\f005" !important;
    display: inline-block !important;
}

.star-rating label:hover {
    transform: scale(1.15);
}

.star-rating input[type="radio"]:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label {
    color: #fbbf24;
}

.description-box {
    margin-top: 30px;
}

.description-box label {
    display: block;
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 12px;
}

.custom-textarea {
    width: 100%;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 16px;
    padding: 16px;
    background-color: #f8fafc;
    color: #334155;
    resize: none;
    transition: all 0.2s ease;
    box-sizing: border-box;
    font-family: inherit;
}

.custom-textarea:focus {
    border-color: #6366f1;
    background-color: #ffffff;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    outline: none;
}

.submit-btn {
    width: 100%;
    background: #6366f1;
    color: #ffffff;
    border: none;
    border-radius: 50px;
    padding: 16px;
    font-size: 18px;
    font-weight: 600;
    margin-top: 35px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
}

.submit-btn:hover {
    background: #4f46e5;
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.5);
}

.error-text {
    color: #ef4444;
    font-size: 13px;
    margin-top: 6px;
    display: block;
}

/* Hide any global required asterisks */
.review-card *::after {
    content: none !important;
}

/* Mobile Responsiveness */
@media (max-width: 576px) {
    .review-page-wrapper {
        padding: 20px 10px;
    }
    
    .review-card {
        border-radius: 16px;
    }
    
    .review-header {
        padding: 25px 20px;
    }
    
    .review-header h3 {
        font-size: 20px;
    }
    
    .review-body {
        padding: 25px 20px;
    }
    
    .rating-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        padding: 12px 0;
    }
    
    .rating-label {
        font-size: 15px;
    }
    
    .star-group {
        width: 100%;
        align-items: flex-start;
    }
    
    .star-rating label {
        font-size: 35px; /* Larger tap target for mobile */
        margin-right: 2px;
    }
    
    .submit-btn {
        padding: 14px;
        font-size: 16px;
    }
}
</style>
@endsection

@section('content')
<div class="review-page-wrapper">
    <div class="review-card">
        <div class="review-header">
            <h3>How was your stay at {{ $reservation->room->title ?? 'our property' }}?</h3>
            <p>Your feedback helps future guests and allows hosts to improve.</p>
        </div>
        
        <div class="review-body">
            <form action="{{ route('user.reservations.review.store', $reservation->id) }}" method="POST">
                @csrf

                @php
                    $fields = [
                        'room_space' => 'Room Space',
                        'room_amenities' => 'Room Amenities',
                        'room_arrangement' => 'Room Arrangement',
                        'room_cleanness' => 'Room Cleanness',
                        'stay_location' => 'Stay Location'
                    ];
                @endphp

                @foreach($fields as $field => $label)
                <div class="rating-row">
                    <div class="rating-label">{{ $label }}</div>
                    <div class="star-group">
                        <div class="star-rating">
                            @for($i = 5; $i >= 1; $i--)
                            <input type="radio" id="{{ $field }}-{{ $i }}" name="{{ $field }}" value="{{ $i }}" {{ old($field) == $i ? 'checked' : '' }} required>
                            <label for="{{ $field }}-{{ $i }}" title="{{ $i }} stars"><i class="fas fa-star"></i></label>
                            @endfor
                        </div>
                        @error($field)
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                @endforeach

                @if($reservation->food_amount > 0)
                <div class="rating-row">
                    <div class="rating-label">Dining & Services</div>
                    <div class="star-group">
                        <div class="star-rating">
                            @for($i = 5; $i >= 1; $i--)
                            <input type="radio" id="dining_services-{{ $i }}" name="dining_services" value="{{ $i }}" {{ old('dining_services') == $i ? 'checked' : '' }} required>
                            <label for="dining_services-{{ $i }}" title="{{ $i }} stars"><i class="fas fa-star"></i></label>
                            @endfor
                        </div>
                        @error('dining_services')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                @endif

                <div class="description-box">
                    <label for="description">Description (Optional)</label>
                    <textarea class="custom-textarea" id="description" name="description" rows="4" placeholder="Tell us more about your experience... what did you love?">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="submit-btn">
                    Submit Review <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
