@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/search.css') }}">
@endsection

@section('content')
<div class="search-page-wrapper">
    <!-- Property Types Horizontal Scroll -->
    <div class="property-types-scroll-wrap">
        <div class="pt-bar-container">
            <div class="property-types-container">
                @foreach($propertyTypes as $type)
                    <a href="{{ request()->fullUrlWithQuery(['property_type' => $type->id]) }}" class="property-type-item {{ request('property_type') == $type->id ? 'active' : '' }}">
                        @if($type->image)
                            <img src="{{ asset('storage/' . $type->image) }}" alt="{{ $type->name }}" class="pt-icon">
                        @else
                            <i class="fas fa-home pt-icon"></i>
                        @endif
                        <span class="pt-name">{{ $type->name }}</span>
                    </a>
                @endforeach
            </div>
            <div class="pt-filters-btn-wrap">
                <button type="button" class="filters-modal-btn" data-bs-toggle="offcanvas" data-bs-target="#searchFiltersOffcanvas" aria-controls="searchFiltersOffcanvas">
                    <i class="fas fa-sliders-h"></i> Filters
                </button>
            </div>
        </div>
    </div>

    <div class="search-main-layout container">
        <!-- LEFT: Results -->
        <div class="search-results-col">
            <h1 class="results-title">
                @if(request('city'))
                    Stays in {{ request('city') }}
                @else
                    Discover your perfect stay
                @endif
            </h1>
            <p class="results-subtitle">{{ $rooms->total() }} properties available</p>

            <div class="rooms-grid">
                @forelse($rooms as $room)
                    @include('home.partials.room_card', ['room' => $room])
                @empty
                    <div class="no-results">
                        <i class="fas fa-search-minus"></i>
                        <h3>No properties found</h3>
                        <p>Try adjusting your search filters to find what you're looking for.</p>
                        <a href="{{ route('search') }}" class="btn btn-outline-primary mt-3">Clear all filters</a>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $rooms->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Filters Offcanvas (Right Side) -->
<div class="offcanvas offcanvas-end custom-filters-offcanvas" tabindex="-1" id="searchFiltersOffcanvas" aria-labelledby="searchFiltersOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="searchFiltersOffcanvasLabel">Filters</h5>
        <button type="button" class="btn-close-custom"><i class="fas fa-times"></i></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('search') }}" method="GET" id="searchFiltersForm">
                    <input type="hidden" name="city" value="{{ request('city') }}">
                    <input type="hidden" name="checkin" value="{{ request('checkin') }}">
                    <input type="hidden" name="checkout" value="{{ request('checkout') }}">
                    <input type="hidden" name="guests" value="{{ request('guests') }}">
                    <input type="hidden" name="property_type" value="{{ request('property_type') }}">

                    <div class="filter-header d-none">
                        <!-- Hidden because we have a modal header -->
                    </div>

                    <!-- Price Range -->
                    <div class="filter-section">
                        <h4>Price Range</h4>
                        <div class="price-inputs">
                            <div class="price-input-group">
                                <span>$</span>
                                <input type="number" name="min_price" id="min_price" placeholder="Min" value="{{ request('min_price') }}">
                            </div>
                            <span class="price-separator">-</span>
                            <div class="price-input-group">
                                <span>$</span>
                                <input type="number" name="max_price" id="max_price" placeholder="Max" value="{{ request('max_price') }}">
                            </div>
                        </div>
                    </div>

                    <hr class="filter-divider">

                    <!-- Booking Type -->
                    <div class="filter-section">
                        <h4>Booking Option</h4>
                        <div class="toggle-group">
                            <label class="toggle-switch">
                                <input type="radio" name="booking_type" value="" {{ request('booking_type') == '' ? 'checked' : '' }}>
                                <span class="toggle-slider">Any</span>
                            </label>
                            <label class="toggle-switch">
                                <input type="radio" name="booking_type" value="instant" {{ request('booking_type') == 'instant' ? 'checked' : '' }}>
                                <span class="toggle-slider">Instant Book</span>
                            </label>
                            <label class="toggle-switch">
                                <input type="radio" name="booking_type" value="request" {{ request('booking_type') == 'request' ? 'checked' : '' }}>
                                <span class="toggle-slider">Request Book</span>
                            </label>
                        </div>
                    </div>

                    <hr class="filter-divider">

                    <!-- Space Types -->
                    <div class="filter-section">
                        <h4>Space Type</h4>
                        <div class="checkbox-list">
                            @foreach($spaceTypes as $type)
                                <label class="custom-checkbox">
                                    <input type="checkbox" name="space_types[]" value="{{ $type->id }}" {{ in_array($type->id, (array)request('space_types')) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    {{ $type->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <hr class="filter-divider">

                    <!-- Rooms & Beds -->
                    <div class="filter-section">
                        <h4>Rooms & Beds</h4>
                        <div class="rooms-beds-group">
                            <div class="rb-row">
                                <span>Bedrooms</span>
                                <div class="pills-group">
                                    <label class="pill"><input type="radio" name="bedrooms" value="any" {{ request('bedrooms', 'any') == 'any' ? 'checked' : '' }}><span>Any</span></label>
                                    @for($i=1; $i<=8; $i++)
                                        <label class="pill"><input type="radio" name="bedrooms" value="{{ $i }}" {{ request('bedrooms') == $i ? 'checked' : '' }}><span>{{ $i }}</span></label>
                                    @endfor
                                    <label class="pill"><input type="radio" name="bedrooms" value="10+" {{ request('bedrooms') == '10+' ? 'checked' : '' }}><span>10+</span></label>
                                </div>
                            </div>
                            <div class="rb-row mt-3">
                                <span>Beds</span>
                                <div class="pills-group">
                                    <label class="pill"><input type="radio" name="beds" value="any" {{ request('beds', 'any') == 'any' ? 'checked' : '' }}><span>Any</span></label>
                                    @for($i=1; $i<=8; $i++)
                                        <label class="pill"><input type="radio" name="beds" value="{{ $i }}" {{ request('beds') == $i ? 'checked' : '' }}><span>{{ $i }}</span></label>
                                    @endfor
                                    <label class="pill"><input type="radio" name="beds" value="10+" {{ request('beds') == '10+' ? 'checked' : '' }}><span>10+</span></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="filter-divider">

                    <!-- Amenities -->
                    <div class="filter-section">
                        <h4>Amenities</h4>
                        <div class="checkbox-list amenities-list">
                            @foreach($amenitiesList as $index => $amenity)
                                <label class="custom-checkbox {{ $index >= 6 ? 'hidden-amenity d-none' : '' }}">
                                    <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" {{ in_array($amenity->id, (array)request('amenities')) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    @if($amenity->image)
                                        <img src="{{ asset('storage/' . $amenity->image) }}" width="16" class="me-1">
                                    @endif
                                    {{ $amenity->name }}
                                </label>
                            @endforeach
                        </div>
                        @if($amenitiesList->count() > 6)
                            <button type="button" class="show-more-btn" id="showMoreAmenities">Show more <i class="fas fa-chevron-down ms-1"></i></button>
                        @endif
                    </div>
            </div>
            <div class="offcanvas-footer d-flex justify-content-between p-3 border-top">
                <a href="{{ route('search', ['city' => request('city'), 'checkin' => request('checkin'), 'checkout' => request('checkout'), 'guests' => request('guests')]) }}" class="clear-filters mt-2">Clear all</a>
                <button type="button" class="apply-filters-btn" onclick="document.getElementById('searchFiltersForm').submit();">Show properties</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/search.js') }}"></script>
@endsection
