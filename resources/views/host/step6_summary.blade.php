@php
    $discounts = $room->roomPrice->discounts ?? [];
    $totalDiscountPercent = 0;
    $appliedDiscounts = [];

    if (!empty($discounts)) {
        // Last minute
        if (!empty($discounts['last_minute']) && ($discounts['last_minute']['active'] ?? false)) {
            $pct = (float) ($discounts['last_minute']['percentage'] ?? 0);
            if ($pct > 0) {
                $totalDiscountPercent += $pct;
                $appliedDiscounts[] = "Last-Minute: {$pct}%";
            }
        }
        // Early bird
        if (!empty($discounts['early_bird']) && ($discounts['early_bird']['active'] ?? false) && !empty($discounts['early_bird']['rules'])) {
            $bestPct = 0;
            foreach ($discounts['early_bird']['rules'] as $rule) {
                if (($rule['percentage'] ?? 0) > $bestPct) {
                    $bestPct = (float) $rule['percentage'];
                }
            }
            if ($bestPct > 0) {
                $totalDiscountPercent += $bestPct;
                $appliedDiscounts[] = "Early-Bird: {$bestPct}%";
            }
        }
        // Length of stay
        if (!empty($discounts['length_of_stay']) && ($discounts['length_of_stay']['active'] ?? false) && !empty($discounts['length_of_stay']['rules'])) {
            $bestPct = 0;
            foreach ($discounts['length_of_stay']['rules'] as $rule) {
                if (($rule['percentage'] ?? 0) > $bestPct) {
                    $bestPct = (float) $rule['percentage'];
                }
            }
            if ($bestPct > 0) {
                $totalDiscountPercent += $bestPct;
                $appliedDiscounts[] = "Length-of-Stay: {$bestPct}%";
            }
        }
        // Custom
        if (!empty($discounts['custom']) && ($discounts['custom']['active'] ?? false) && !empty($discounts['custom']['rules'])) {
            $bestPct = 0;
            foreach ($discounts['custom']['rules'] as $rule) {
                if (($rule['percentage'] ?? 0) > $bestPct) {
                    $bestPct = (float) $rule['percentage'];
                }
            }
            if ($bestPct > 0) {
                $totalDiscountPercent += $bestPct;
                $appliedDiscounts[] = "Custom: {$bestPct}%";
            }
        }
    }

    $discountAmount = ($room->price * $totalDiscountPercent) / 100;

    // Service fee (from site settings)
    $serviceFeePct = (float) \App\Models\SiteSetting::get('service_fee', 5);
    $serviceFeeAmount = ($room->price * $serviceFeePct) / 100;

    // Tax (12% standard GST calculation)
    $taxAmount = $room->price * 0.12;

    // Grand total estimate
    $totalEstimate = $room->price + $serviceFeeAmount + $taxAmount;
@endphp

<div class="summary-card-premium"
    style="background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1.5px solid rgba(255, 255, 255, 0.45); border-radius: 28px; padding: 24px; box-shadow: 0 12px 30px rgba(0, 0, 0, 0.05); position: sticky; top: 40px; overflow: hidden; transition: all 0.3s ease;">
    <!-- Ambient glowing circle inside card -->
    <div
        style="position: absolute; top: -50px; right: -50px; width: 140px; height: 140px; border-radius: 50%; background: rgba(37, 99, 235, 0.08); filter: blur(30px); pointer-events: none; z-index: 0;">
    </div>

    <!-- Media preview with fallback -->
    @php
        $coverPhoto = $room->photos->first();
        $coverPath = $coverPhoto ? asset('storage/' . $coverPhoto->photo_path) : 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=800&q=80';
    @endphp
    <div class="summary-media-wrapper"
        style="position: relative; width: 100%; height: 180px; border-radius: 20px; overflow: hidden; margin-bottom: 20px; box-shadow: 0 8px 20px rgba(0,0,0,0.06); z-index: 1;">
        <img src="{{ $coverPath }}" alt="Property Cover"
            style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
        <div
            style="position: absolute; bottom: 12px; left: 12px; background: rgba(15, 23, 42, 0.75); color: #fff; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; backdrop-filter: blur(6px); letter-spacing: 0.3px; border: 1px solid rgba(255,255,255,0.15);">
            Preview Listing
        </div>
    </div>

    <!-- Details -->
    <div class="summary-details" style="position: relative; z-index: 1;">
        <span class="property-tag"
            style="display: inline-block; font-size: 10.5px; font-weight: 800; text-transform: uppercase; color: var(--primary); letter-spacing: 1px; margin-bottom: 6px;">
            {{ $room->spaceType->name ?? 'Entire Space' }} &nbsp;•&nbsp; {{ $room->propertyType->name ?? 'Home' }}
        </span>
        <h3 class="property-title"
            style="font-size: 1.15rem; font-weight: 800; color: var(--text-main); margin: 0 0 10px 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
            {{ $room->title ?: 'Unnamed Listing' }}
        </h3>

        <!-- Metadata pills -->
        <div
            style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; border-bottom: 1.5px solid var(--border); padding-bottom: 16px;">
            <div
                style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-muted); background: var(--bg-primary); padding: 5px 12px; border-radius: 12px; border: 1px solid var(--border);">
                <i class="fas fa-map-marker-alt text-primary" style="font-size: 11px;"></i>
                <span style="font-weight: 600;">{{ $room->roomLocation->city ?? 'No Location Set' }}</span>
            </div>
            <div
                style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-muted); background: var(--bg-primary); padding: 5px 12px; border-radius: 12px; border: 1px solid var(--border);">
                <i class="fas fa-bed text-primary" style="font-size: 11px;"></i>
                <span style="font-weight: 600;">{{ $room->bedrooms_count ?? 1 }} Bedrooms</span>
            </div>
            <div
                style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-muted); background: var(--bg-primary); padding: 5px 12px; border-radius: 12px; border: 1px solid var(--border);">
                <i class="fas fa-users text-primary" style="font-size: 11px;"></i>
                <span style="font-weight: 600;">{{ $room->accommodation ?? 1 }} Guests</span>
            </div>
        </div>

        <style>
            .summary-tooltip-container:hover .summary-tooltip {
                visibility: visible !important;
                opacity: 1 !important;
            }
        </style>

        <!-- Selected Amenities badges (Only Icons/Images with modern tooltip) -->
        @if($room->amenities->count() > 0)
            <div
                style="margin-bottom: 16px; border-bottom: 1.5px solid var(--border); padding-bottom: 16px; display: none;">
                <span
                    style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 10px; letter-spacing: 0.5px;">Amenities</span>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    @foreach($room->amenities as $amenity)
                        <div class="summary-tooltip-container"
                            style="position: relative; display: inline-block; cursor: pointer;">
                            <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(37, 99, 235, 0.06); border: 1px solid rgba(37, 99, 235, 0.15); display: flex; align-items: center; justify-content: center; color: var(--primary); transition: all 0.25s ease; overflow: hidden;"
                                onmouseover="this.style.transform='scale(1.1)'; this.style.borderColor='var(--primary)';"
                                onmouseout="this.style.transform='scale(1)'; this.style.borderColor='rgba(37, 99, 235, 0.15)';">
                                @if($amenity->image)
                                    <img src="{{ Storage::url($amenity->image) }}" alt="{{ $amenity->name }}"
                                        style="width: 100%; height: 100%; object-fit: contain; padding: 4px;">
                                @else
                                    <i class="fas fa-spa" style="font-size: 13px;"></i>
                                @endif
                            </div>
                            <!-- Modern tooltip -->
                            <div class="summary-tooltip"
                                style="visibility: hidden; background-color: #0f172a; color: #fff; text-align: center; border-radius: 6px; padding: 5px 10px; position: absolute; z-index: 1000; bottom: 125%; left: 50%; transform: translateX(-50%); opacity: 0; transition: opacity 0.2s, visibility 0.2s; white-space: nowrap; font-size: 11px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                {{ $amenity->name }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Pricing breakdown panel -->
        <div style="margin-bottom: 18px;">
            <span
                style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 10px; letter-spacing: 0.5px;">Price
                Details</span>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px;">
                    <span style="color: var(--text-muted); font-weight: 500;">Base Rate</span>
                    <strong
                        style="color: var(--text-main); font-weight: 700;">{!! $room->currency_symbol !!}{{ number_format($room->price, 2) }}
                        / Night</strong>
                </div>

                <div style="display: flex; justify-content: space-between; font-size: 13px;">
                    <span style="color: var(--text-muted); font-weight: 500;">Service Fee ({{ $serviceFeePct }}% on
                        Base)</span>
                    <strong
                        style="color: var(--text-main); font-weight: 700;">{!! $room->currency_symbol !!}{{ number_format($serviceFeeAmount, 2) }}</strong>
                </div>

                <div style="display: flex; justify-content: space-between; font-size: 13px;">
                    <span style="color: var(--text-muted); font-weight: 500;">Est. Tax (12% standard GST)</span>
                    <strong
                        style="color: var(--text-main); font-weight: 700;">{!! $room->currency_symbol !!}{{ number_format($taxAmount, 2) }}</strong>
                </div>

                @if($discountAmount > 0)
                    <div style="display: flex; justify-content: space-between; font-size: 13px; align-items: center;">
                        <span
                            style="color: var(--text-muted); font-weight: 500; display: flex; align-items: center; gap: 6px;">
                            Discounts
                            <div class="summary-tooltip-container"
                                style="position: relative; display: inline-block; cursor: help;">
                                <i class="fas fa-info-circle" style="font-size: 12px; color: var(--text-muted);"></i>
                                <div class="summary-tooltip"
                                    style="visibility: hidden; width: 200px; background-color: #0f172a; color: #fff; text-align: left; border-radius: 12px; padding: 12px; position: absolute; z-index: 1000; bottom: 125%; left: 0; opacity: 0; transition: opacity 0.2s, visibility 0.2s; box-shadow: 0 10px 25px rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.1); font-size: 12px; font-family: sans-serif;">
                                    <div
                                        style="font-weight: 700; font-size: 10px; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 4px; letter-spacing: 0.5px;">
                                        Active Discounts</div>
                                    @foreach($appliedDiscounts as $dName)
                                        @php
                                            $parts = explode(': ', $dName);
                                            $name = $parts[0] ?? 'Discount';
                                            $pct = $parts[1] ?? '0%';
                                        @endphp
                                        <div
                                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; font-weight: 600;">
                                            <span>{{ $name }}</span>
                                            <span style="color: #10b981;">-{{ $pct }}</span>
                                        </div>
                                    @endforeach
                                    <div style="height: 1px; background: rgba(255,255,255,0.1); margin: 6px 0;"></div>
                                    <div style="display: flex; justify-content: space-between; font-weight: 700;">
                                        <span>Total Saved</span>
                                        <span
                                            style="color: #10b981;">-{!! $room->currency_symbol !!}{{ number_format($discountAmount, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </span>
                        <!-- <strong style="color: #16a34a; font-weight: 700;">-{!! $totalDiscountPercent !!}%</strong> -->
                    </div>
                @endif

                <div style="height: 1px; background: var(--border); margin: 4px 0;"></div>

                <div style="display: flex; justify-content: space-between; font-size: 14.5px;">
                    <span style="color: var(--text-main); font-weight: 800;">Estimated Total</span>
                    <strong
                        style="color: var(--primary); font-weight: 800;">{!! $room->currency_symbol !!}{{ number_format($totalEstimate, 2) }}</strong>
                </div>
            </div>
        </div>

        <!-- Cancellation & Rules Info -->
        <div
            style="background: var(--bg-primary); border: 1.5px solid var(--border); border-radius: 20px; padding: 16px; display: flex; flex-direction: column; gap: 10px;">
            <div id="summary_cancellation_policy_row"
                style="display: {{ $room->custom_cancellation ? 'none' : 'flex' }}; justify-content: space-between; align-items: center; font-size: 12.5px;">
                <span style="color: var(--text-muted); font-weight: 600;">Cancellation Policy</span>
                <span id="summary_cancellation_policy" style="font-weight: 800; color: var(--text-main);">
                    {{ $room->cancellation_policy ?: 'Flexible' }}
                </span>
            </div>

            <div id="summary_custom_cancel_row"
                style="display: {{ $room->custom_cancellation ? 'flex' : 'none' }}; justify-content: space-between; align-items: center; font-size: 12.5px;">
                <span style="color: var(--text-muted); font-weight: 600;">Custom Policy</span>
                <span style="font-weight: 800; color: var(--primary);">
                    {{ $room->free_cancellation_days }}d Free ({{ number_format($room->cancellation_fee) }}%)
                </span>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12.5px;">
                <span style="color: var(--text-muted); font-weight: 600;">Checkout Time</span>
                <span id="summary_checkout_policy" style="font-weight: 800; color: var(--text-main);">
                    {{ $room->checkout_policy ?: '11:00 AM' }}
                </span>
            </div>

            <div
                style="display: flex; justify-content: space-between; align-items: center; font-size: 12.5px; border-top: 1px dashed var(--border); padding-top: 8px;">
                <span style="color: var(--text-muted); font-weight: 600;">Official House Rules</span>
                <span id="summary_rules_count" style="font-weight: 800; color: var(--text-main);">
                    {{ is_array($room->selected_rules) ? count($room->selected_rules) : 0 }} Rules Selected
                </span>
            </div>
        </div>
    </div>
</div>