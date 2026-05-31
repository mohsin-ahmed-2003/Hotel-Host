<?php

namespace App\Helpers;

use App\Models\Room;
use App\Models\SiteSetting;
use Carbon\Carbon;

class price_calculation
{
    /**
     * Calculate room booking price based on base price, weekends, discounts, fees, and taxes.
     */
    public static function calculate(Room $room, $checkin, $checkout, $guestsCount = 2, $enhancementIds = [], $enhancementDates = [])
    {
        $checkinDate = Carbon::parse($checkin);
        $checkoutDate = Carbon::parse($checkout);
        
        if ($checkoutDate->lte($checkinDate)) {
            return [
                'success' => false,
                'message' => 'Checkout date must be after checkin date.'
            ];
        }

        $totalNights = $checkinDate->diffInDays($checkoutDate);
        if ($totalNights <= 0) {
            return [
                'success' => false,
                'message' => 'Stay must be at least 1 night.'
            ];
        }

        $roomPriceModel = $room->roomPrice;
        $basePrice = (float) ($roomPriceModel->price ?? $room->price);
        $currencySymbol = $room->currency_symbol;
        
        // 1. Calculate nightly base rate with weekend check
        $totalRawBasePrice = 0;
        $nightlyRates = [];
        
        $additionalPricing = $roomPriceModel->additional_pricing ?? [];
        $hasWeekend = !empty($additionalPricing['weekend_pricing']['active']) && !empty($additionalPricing['weekend_pricing']['amount']);
        $weekendPrice = $hasWeekend ? (float) $additionalPricing['weekend_pricing']['amount'] : 0;

        $tempDate = $checkinDate->copy();
        $hasWeekendNightsBooked = false;
        for ($i = 0; $i < $totalNights; $i++) {
            $dayOfWeek = $tempDate->dayOfWeek; // 5 = Friday, 6 = Saturday
            $isWeekend = ($dayOfWeek === Carbon::FRIDAY || $dayOfWeek === Carbon::SATURDAY);
            if ($isWeekend && $weekendPrice > 0) {
                $hasWeekendNightsBooked = true;
            }
            $rate = ($isWeekend && $weekendPrice > 0) ? $weekendPrice : $basePrice;
            
            $totalRawBasePrice += $rate;
            $tempDate->addDay();
        }

        $avgBaseRate = $totalRawBasePrice / $totalNights;

        // 2. Prioritized Discount Engine
        $discountPct = 0;
        $discountAppliedName = '';
        $discounts = $roomPriceModel->discounts ?? [];

        $today = Carbon::today();
        $daysAhead = $today->diffInDays($checkinDate, false);

        // Priority 1: Custom/Seasonal Discount
        if (!empty($discounts['custom']['active']) && !empty($discounts['custom']['rules'])) {
            $bestCustom = null;
            $currentDay = Carbon::today();
            foreach ($discounts['custom']['rules'] as $rule) {
                if (!empty($rule['start_date']) && !empty($rule['end_date'])) {
                    $start = Carbon::parse($rule['start_date']);
                    $end = Carbon::parse($rule['end_date']);
                    
                    // Skip if the custom promo window has already passed/expired today
                    if ($end->lt($currentDay)) {
                        continue;
                    }
                    
                    if ($checkinDate->between($start, $end)) {
                        if ($bestCustom === null || (float) $rule['percentage'] > (float) $bestCustom['percentage']) {
                            $bestCustom = $rule;
                        }
                    }
                }
            }
            if ($bestCustom !== null) {
                $discountPct = (float) $bestCustom['percentage'];
                $discountAppliedName = 'Seasonal Deal';
            }
        }

        // Priority 2: Last-Minute Discount (only if no Custom Discount applied)
        if ($discountPct === 0 && !empty($discounts['last_minute']['active'])) {
            $limitDays = (int) ($discounts['last_minute']['days'] ?? 14);
            if ($daysAhead >= 0 && $daysAhead <= $limitDays) {
                $discountPct = (float) ($discounts['last_minute']['percentage'] ?? 0);
                $discountAppliedName = 'Last-Minute';
            }
        }

        // Priority 3: Early-Bird Discount (only if no Custom or Last-Minute applied)
        if ($discountPct === 0 && !empty($discounts['early_bird']['active']) && !empty($discounts['early_bird']['rules'])) {
            $bestEarly = null;
            foreach ($discounts['early_bird']['rules'] as $rule) {
                $ruleDays = (int) ($rule['days_ahead'] ?? $rule['days'] ?? 30);
                if ($daysAhead >= $ruleDays) {
                    if ($bestEarly === null || $ruleDays > (int) ($bestEarly['days_ahead'] ?? $bestEarly['days'])) {
                        $bestEarly = $rule;
                    }
                }
            }
            if ($bestEarly !== null) {
                $discountPct = (float) $bestEarly['percentage'];
                $discountAppliedName = "Early Bird ({$daysAhead} days ahead)";
            }
        }

        // Independent Stackable Discount: Length of Stay
        $losDiscountPct = 0;
        $losDiscountName = '';
        if (!empty($discounts['length_of_stay']['active']) && !empty($discounts['length_of_stay']['rules'])) {
            $bestLength = null;
            foreach ($discounts['length_of_stay']['rules'] as $rule) {
                $ruleNights = (int) ($rule['nights'] ?? 0);
                if ($totalNights >= $ruleNights) {
                    if ($bestLength === null || $ruleNights > (int) $bestLength['nights']) {
                        $bestLength = $rule;
                    }
                }
            }
            if ($bestLength !== null) {
                $losDiscountPct = (float) $bestLength['percentage'];
                $losDiscountName = "Length of Stay ({$totalNights} nights)";
            }
        }

        // Combine Primary Discount and Length of Stay Discount
        if ($losDiscountPct > 0) {
            $discountPct += $losDiscountPct;
            if ($discountAppliedName !== '') {
                $discountAppliedName .= ' + ' . $losDiscountName;
            } else {
                $discountAppliedName = $losDiscountName;
            }
        }

        $discountSavings = $totalRawBasePrice * ($discountPct / 100);
        $discountedBasePrice = $totalRawBasePrice - $discountSavings;

        // 3. Surcharges & Fees
        $cleaningFee = 0;
        if (!empty($additionalPricing['cleaning_fee']['active'])) {
            $cleaningFee = (float) ($additionalPricing['cleaning_fee']['amount'] ?? 0);
        }

        $extraGuestsFee = 0;
        if (!empty($additionalPricing['additional_guests']['active'])) {
            $threshold = (int) ($additionalPricing['additional_guests']['after_guests'] ?? 2);
            if ($guestsCount > $threshold) {
                $extraGuests = $guestsCount - $threshold;
                $perNightAmt = (float) ($additionalPricing['additional_guests']['amount'] ?? 0);
                $extraGuestsFee = $extraGuests * $perNightAmt * $totalNights;
            }
        }

        // 4. Site Service Fee (percentage on raw base rate)
        $serviceFeePct = (float) SiteSetting::get('service_fee', 0);
        $serviceFeeAmt = $totalRawBasePrice * ($serviceFeePct / 100);

        // 5. Taxes (applied if not included in price)
        $taxAmt = 0;
        $isTaxIncluded = (bool) ($roomPriceModel->is_tax_included ?? true);
        $taxType = $roomPriceModel->tax_type ?? 'percentage';
        $taxRate = (float) ($roomPriceModel->tax_amount ?? 0);
        
        if (!$isTaxIncluded && $taxRate > 0) {
            if ($taxType === 'percentage') {
                $taxAmt = $totalRawBasePrice * ($taxRate / 100);
            } else {
                $taxAmt = $taxRate * $totalNights;
            }
        }

        // 6. Selected Enhancements (Dining & Services)
        $totalEnhancementFee = 0;
        $selectedEnhancements = [];
        if (!empty($enhancementIds)) {
            $enhancements = \App\Models\RoomEnhancement::whereIn('id', $enhancementIds)
                ->where('room_id', $room->id)
                ->where('is_active', true)
                ->get();
                
            foreach ($enhancements as $enhancement) {
                // Determine selected days for this enhancement
                $daysCount = 1;
                $chosenDates = [];
                if (!empty($enhancementDates) && isset($enhancementDates[$enhancement->id])) {
                    $chosenDates = $enhancementDates[$enhancement->id];
                    $daysCount = max(1, count($chosenDates));
                }
                
                // Calculation: item price * guest count * number of selected days
                $itemTotal = (float) $enhancement->price * $guestsCount * $daysCount;
                $totalEnhancementFee += $itemTotal;
                
                $selectedEnhancements[] = [
                    'id' => $enhancement->id,
                    'item_name' => $enhancement->item_name,
                    'price' => (float) $enhancement->price,
                    'days_count' => $daysCount,
                    'selected_dates' => $chosenDates,
                    'item_total' => $itemTotal
                ];
            }
        }

        $finalTotal = $discountedBasePrice + $cleaningFee + $extraGuestsFee + $serviceFeeAmt + $taxAmt + $totalEnhancementFee;

        return [
            'success' => true,
            'totalNights' => $totalNights,
            'avgBaseRate' => $avgBaseRate,
            'totalRawBasePrice' => $totalRawBasePrice,
            'discountPct' => $discountPct,
            'discountAppliedName' => $discountAppliedName,
            'discountSavings' => $discountSavings,
            'discountedBasePrice' => $discountedBasePrice,
            'cleaningFee' => $cleaningFee,
            'extraGuestsFee' => $extraGuestsFee,
            'serviceFeePct' => $serviceFeePct,
            'serviceFeeAmt' => $serviceFeeAmt,
            'taxType' => $taxType,
            'taxRate' => $taxRate,
            'taxAmt' => $taxAmt,
            'totalEnhancementFee' => $totalEnhancementFee,
            'selectedEnhancements' => $selectedEnhancements,
            'finalTotal' => $finalTotal,
            'currencySymbol' => $currencySymbol,
            'standardBasePrice' => $basePrice,
            'weekendPrice' => $weekendPrice,
            'hasWeekendNightsBooked' => $hasWeekendNightsBooked
        ];
    }
}
