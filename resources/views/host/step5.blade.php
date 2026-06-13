@extends('host.layout')

@section('host-content')
    <!-- Flatpickr Datepicker Stylesheet -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <h1 class="host-title">Now, set your price</h1>
    <p class="host-subtitle">You can change it anytime.</p>

    @php
        $savedCurrency = $room->roomPrice->currency ?? \App\Models\SiteSetting::get('default_currency', 'USD');
        $isTaxIncluded = $room->roomPrice->is_tax_included ?? true;
    @endphp

    <div class="mb-5">
        <div class="row align-items-center g-3">
            <div class="col-md-7">
                <div class="form-floating-airbnb" id="wrap_price">
                    <input type="number" class="form-control-airbnb" style="font-size:20px;font-weight:700;height:64px;"
                        name="price" id="priceInput" value="{{ $room->roomPrice->price ?? '' }}" placeholder=" "
                        oninput="calculateTotal(); autoSave('price', this.value, 'room_prices', 'priceInput')">
                    <label for="priceInput">Price per night</label>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-floating-airbnb" id="wrap_currency">
                    <select class="form-control-airbnb" name="currency" id="currencySelect"
                        style="height:64px;font-weight:700;"
                        onchange="calculateTotal(); autoSave('currency', this.value, 'room_prices', 'currencySelect')">
                        @foreach($currencies as $cur)
                            <option value="{{ $cur->currency_code }}" {{ $savedCurrency === $cur->currency_code ? 'selected' : '' }} data-symbol="{{ $cur->symbol }}">
                                {{ $cur->currency_code }} ({{ $cur->symbol }}) - {{ $cur->currency_name }}
                            </option>
                        @endforeach
                    </select>
                    <label for="currencySelect">Currency</label>
                </div>
            </div>
        </div>
    </div>

    {{-- Tax settings wrapper in discount-card style --}}
    <div class="discount-card" id="tax_card" style="margin-bottom: 24px;">
        <div class="discount-header" onclick="toggleTaxCard()">
            <input type="checkbox" class="discount-checkbox" id="taxIncluded" {{ $isTaxIncluded ? 'checked' : '' }}
                onclick="event.stopPropagation(); toggleTaxFields(this.checked); autoSave('is_tax_included', this.checked ? 1 : 0, 'room_prices')">
            <h4 class="discount-label">Taxes included in price</h4>
        </div>
        <div id="taxFields" class="additional-pricing-content" style="display:{{ $isTaxIncluded ? 'none' : 'block' }};" onclick="event.stopPropagation();">
            <div style="height:1px;background:var(--border);margin:16px 0;"></div>
            <p class="fw-bold small mb-3 text-uppercase" style="color:var(--text-muted);letter-spacing:.5px;">Tax Settings</p>
            <div class="row g-3">
                <div class="col-8">
                    <div class="form-floating-airbnb mb-0" id="wrap_taxAmount">
                        <input type="number" class="form-control-airbnb" name="tax_amount" id="taxAmount"
                            value="{{ $room->roomPrice->tax_amount ?? '' }}" placeholder=" " style="height:60px;"
                            oninput="calculateTotal(); autoSave('tax_amount', this.value, 'room_prices', 'taxAmount')">
                        <label for="taxAmount">Tax Amount</label>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-floating-airbnb mb-0" id="wrap_taxType">
                        <select class="form-control-airbnb" name="tax_type" id="taxType" style="height:60px;"
                            onchange="calculateTotal(); autoSave('tax_type', this.value, 'room_prices', 'taxType')">
                            <option value="percentage" {{ ($room->roomPrice->tax_type ?? 'percentage') === 'percentage' ? 'selected' : '' }}>% Percentage</option>
                            <option value="fixed" {{ ($room->roomPrice->tax_type ?? '') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        </select>
                        <label for="taxType">Tax Type</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Security Deposit --}}
    <div class="form-floating-airbnb mb-5" id="wrap_secDeposit">
        <input type="number" class="form-control-airbnb" name="security_deposit" id="secDepositInput"
            value="{{ $room->roomPrice->security_deposit ?? '' }}" placeholder=" " style="height:60px;"
            oninput="autoSave('security_deposit', this.value, 'room_prices', 'secDepositInput')">
        <label for="secDepositInput">Security Deposit (Optional)</label>
    </div>

    {{-- Additional Pricing Options --}}
    <div class="mb-5" id="additionalPricingSection">
        <h3 class="fw-bold h5 mb-2">Additional Pricing Options</h3>
        <p class="text-muted small mb-4">Set fees that apply to specific situations.</p>

        {{-- Cleaning Fee --}}
        <div class="discount-card" id="card_cleaning_fee">
            <div class="discount-header" onclick="toggleAdditionalOption('cleaning_fee')">
                <input type="checkbox" class="discount-checkbox" id="chk_cleaning_fee"
                    onclick="event.stopPropagation(); toggleAdditionalOption('cleaning_fee')">
                <h4 class="discount-label">Cleaning fee</h4>
            </div>
            <div id="content_cleaning_fee" class="additional-pricing-content" style="display:none;">
                <div class="discount-input-group mt-3" style="max-width:240px;">
                    <span class="discount-input-prefix additional-currency-symbol">&#8377;</span>
                    <input type="number" class="discount-input-field" id="input_cleaning_fee"
                        placeholder="0.00" style="width:180px;"
                        oninput="updateAdditionalPricing('cleaning_fee','amount',this.value)">
                </div>
                <p class="text-muted small mt-2 mb-0">This fee will apply to every reservation at your listing.</p>
            </div>
        </div>

        {{-- Additional Guests --}}
        <div class="discount-card" id="card_additional_guests">
            <div class="discount-header" onclick="toggleAdditionalOption('additional_guests')">
                <input type="checkbox" class="discount-checkbox" id="chk_additional_guests"
                    onclick="event.stopPropagation(); toggleAdditionalOption('additional_guests')">
                <h4 class="discount-label">Additional guests</h4>
            </div>
            <div id="content_additional_guests" class="additional-pricing-content" style="display:none;">
                <div class="discount-input-group mt-3" style="max-width:240px;">
                    <span class="discount-input-prefix additional-currency-symbol">&#8377;</span>
                    <input type="number" class="discount-input-field" id="input_additional_guests"
                        placeholder="0.00" style="width:180px;"
                        oninput="updateAdditionalPricing('additional_guests','amount',this.value)">
                </div>
                <p class="text-muted small mt-2 mb-1">For each guest after</p>
                <select class="discount-select" id="select_after_guests"
                    onchange="updateAdditionalPricing('additional_guests','after_guests',this.value)">
                    @for($g = 1; $g <= 10; $g++)
                        <option value="{{ $g }}" {{ $g == 2 ? 'selected' : '' }}>{{ $g }}</option>
                    @endfor
                </select>
                <p class="text-muted small mt-2 mb-0">This fee will apply for each additional guest, for each night of the reservation.</p>
            </div>
        </div>

        {{-- Weekend Pricing --}}
        <div class="discount-card" id="card_weekend_pricing">
            <div class="discount-header" onclick="toggleAdditionalOption('weekend_pricing')">
                <input type="checkbox" class="discount-checkbox" id="chk_weekend_pricing"
                    onclick="event.stopPropagation(); toggleAdditionalOption('weekend_pricing')">
                <h4 class="discount-label">Weekend pricing</h4>
            </div>
            <div id="content_weekend_pricing" class="additional-pricing-content" style="display:none;">
                <div class="discount-input-group mt-3" style="max-width:240px;">
                    <span class="discount-input-prefix additional-currency-symbol">&#8377;</span>
                    <input type="number" class="discount-input-field" id="input_weekend_pricing"
                        placeholder="0.00" style="width:180px;"
                        oninput="updateAdditionalPricing('weekend_pricing','amount',this.value)">
                </div>
                <p class="text-muted small mt-2 mb-0">This is a nightly price. It will replace your base price for every Friday and Saturday.</p>
            </div>
        </div>
    </div>

    <!-- Discount Section -->
    <div class="mb-5">
        <h3 class="fw-bold h5 mb-2">Discounts</h3>
        <p class="text-muted small mb-4">Offer promotional rates to increase bookings.</p>

        <div id="discounts_wrapper">
            <!-- Dynamically populated via JavaScript -->
        </div>
    </div>

    <style>
        .pricing-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            margin-bottom: 32px;
            max-width: 100%;
        }

        .pricing-card-body {
            padding: 20px;
        }

        .pricing-header-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            /* padding-bottom: 16px; */
            border-bottom: 1px solid var(--border);
        }

        .pricing-icon-box {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }

        .icon-box-blue {
            background: #eff6ff;
            color: #2563eb;
        }

        .icon-box-green {
            background: #f0fdf4;
            color: #16a34a;
        }

        .icon-box-primary {
            background: var(--accent-light);
            color: var(--accent);
        }

        .pricing-item-row {
            display: flex;
            align-items: center;
            padding: 10px 0;
        }

        .pricing-item-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-left: 12px;
        }

        .pricing-item-label {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--body-text);
        }

        .pricing-item-value {
            font-size: 14px;
            font-weight: 700;
            color: var(--body-text);
            font-family: 'Inter', sans-serif;
        }

        .pricing-card-footer {
            background: var(--bg-2);
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--border);
        }

        .total-label-text {
            font-size: 15px;
            font-weight: 800;
            color: var(--body-text);
        }

        .total-amount-text {
            font-size: 18px;
            font-weight: 800;
            color: var(--accent);
        }

        /* Dark mode adjustments */
        body.dark-mode .icon-box-blue {
            background: rgba(37, 99, 235, 0.12);
            color: #60a5fa;
        }

        body.dark-mode .icon-box-green {
            background: rgba(22, 163, 74, 0.12);
            color: #4ade80;
        }

        /* --- Discounts Section Styles --- */
        .discount-card {
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.25s ease;
        }
        .discount-card:hover {
            border-color: var(--primary);
        }
        .discount-header {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            user-select: none;
        }
        .discount-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--primary);
            margin: 0;
        }
        .discount-label {
            font-weight: 700;
            font-size: 15px;
            color: var(--text-main);
            margin: 0;
            cursor: pointer;
        }
        .discount-content {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
        }
        .discount-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            flex-wrap: nowrap; /* strictly keep on a single straight horizontal line */
        }
        .discount-select {
            height: 46px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            background: var(--card-bg) url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") no-repeat right 12px center/12px auto;
            padding: 0 32px 0 12px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
            outline: none;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            width: 180px; /* Uniform width for all dropdowns */
            transition: border-color 0.2s;
        }
        .discount-select:focus {
            border-color: var(--primary);
        }

        /* Custom Flatpickr alignment and size styling matching rooms/show.blade.php */
        .flatpickr-calendar, 
        .flatpickr-calendar.open, 
        .flatpickr-calendar.animate.open {
            transform: scale(0.85) !important; 
            transform-origin: top left !important;
            border-radius: 16px !important;
            border: 1px solid rgba(37, 99, 235, 0.4) !important;
            box-shadow: 0 15px 40px rgba(37, 99, 235, 0.15) !important;
            padding: 10px 15px 10px 10px !important;
            width: max-content !important;
            box-sizing: border-box !important;
        }
        .flatpickr-day.is-sunday:not(.flatpickr-disabled):not(.disabled) {
            color: #ef4444 !important; 
            font-weight: bold;
        }
        .flatpickr-day.today {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            color: white !important;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
        }
        .flatpickr-day.start-date-highlight {
            background: rgba(37, 99, 235, 0.15) !important;
            border-color: #2563eb !important;
            color: #2563eb !important;
            font-weight: 700;
            border-radius: 50%;
        }
        .discount-input-group {
            display: flex;
            align-items: center;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            background: var(--card-bg);
            overflow: hidden;
            height: 46px;
        }
        .discount-input-prefix {
            background: var(--bg-2);
            padding: 0 12px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            border-right: 1.5px solid var(--border);
        }
        .discount-input-field {
            border: none;
            outline: none;
            padding: 0 12px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
            background: transparent;
            height: 100%;
            width: 130px;
        }
        .btn-remove-discount {
            width: 38px;
            height: 38px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            color: #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background: var(--card-bg);
            transition: all 0.2s;
        }
        .btn-remove-discount:hover {
            background: #fef2f2;
            border-color: #fca5a5;
        }
        .btn-add-discount {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: transparent;
            border: 1.5px solid var(--primary);
            color: var(--primary);
            padding: 6px 16px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-add-discount:hover {
            background: var(--primary);
            color: white;
        }

        /* --- Additional Pricing Options --- */
        .additional-pricing-content {
            margin-top: 0;
            padding-top: 12px;
            border-top: 1px solid var(--border);
            animation: apFadeIn 0.22s ease;
        }
        @keyframes apFadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        /* Discount preview rows inside pricing card */
        .discount-preview-section {
            border-top: 2px dashed var(--border);
            margin: 8px 0 0;
            padding-top: 8px;
        }
        .discount-preview-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--text-muted);
            padding: 4px 0 8px 0;
        }
        .pricing-item-value-strike {
            font-size: 12px;
            color: var(--text-muted);
            text-decoration: line-through;
            margin-right: 6px;
        }
        .pricing-item-value-green {
            font-size: 14px;
            font-weight: 700;
            color: #16a34a;
            font-family: 'Inter', sans-serif;
        }
    </style>

    {{-- Price Breakdown --}}
    <div class="pricing-card">
        <div class="pricing-card-body">
            <div class="pricing-header-row">
                <div class="pricing-icon-box icon-box-primary">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h5 class="mb-0 fw-bold" style="font-size: 15px;">Price Breakdown</h5>
            </div>

            <div class="pricing-item-row">
                <div class="pricing-icon-box icon-box-blue">
                    <i class="fas fa-tag"></i>
                </div>
                <div class="pricing-item-content">
                    <span class="pricing-item-label">Base price</span>
                    <span id="calcBasePrice" class="pricing-item-value">0.00</span>
                </div>
            </div>

            <div style="height: 1px; background: var(--border); margin: 4px 0 4px 46px;"></div>

            <div class="pricing-item-row">
                <div class="pricing-icon-box icon-box-green">
                    <i class="fas fa-percent"></i>
                </div>
                <div class="pricing-item-content">
                    <span class="pricing-item-label">Taxes</span>
                    <span id="calcTax" class="pricing-item-value">0.00</span>
                </div>
            </div>
            <div id="additionalFeeRows"></div>
            <div id="discountPreviewRows"></div>
        </div>

        <div class="pricing-card-footer">
            <span class="total-label-text">Total per night</span>
            <span id="calcTotal" class="total-amount-text">0.00</span>
        </div>
    </div>

    <div class="host-actions">
        <a href="{{ route('host.step', ['room' => $room->id, 'step' => 4]) }}" class="btn-prev">Back</a>
        <a href="{{ route('host.step', ['room' => $room->id, 'step' => 6]) }}" class="btn-next">
            <span class="btn-text">Next Step</span>
            <div class="btn-spinner"></div>
        </a>
    </div>
@endsection

@section('scripts')
    <!-- Flatpickr Datepicker Script -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        function toggleTaxCard() {
            const chk = document.getElementById('taxIncluded');
            chk.checked = !chk.checked;
            toggleTaxFields(chk.checked);
            autoSave('is_tax_included', chk.checked ? 1 : 0, 'room_prices');
        }

        function toggleTaxFields(isChecked) {
            document.getElementById('taxFields').style.display = isChecked ? 'none' : 'block';
            calculateTotal();
        }

        function getSymbol() {
            const s = document.getElementById('currencySelect');
            return s.options[s.selectedIndex].getAttribute('data-symbol') || s.value;
        }

        function calculateTotal() {
            const basePrice = parseFloat(document.getElementById('priceInput').value) || 0;
            const isTaxIncluded = document.getElementById('taxIncluded').checked;
            const symbol = getSymbol();
            let taxAmount = 0;

            if (!isTaxIncluded) {
                const taxInput = parseFloat(document.getElementById('taxAmount').value) || 0;
                const taxType  = document.getElementById('taxType').value;
                taxAmount = taxType === 'percentage' ? basePrice * (taxInput / 100) : taxInput;
            }

            const wkActive = additionalPricingState.weekend_pricing.active;
            const wkAmt    = parseFloat(additionalPricingState.weekend_pricing.amount) || 0;

            const cfActive = additionalPricingState.cleaning_fee.active;
            const cfAmt    = cfActive ? (parseFloat(additionalPricingState.cleaning_fee.amount) || 0) : 0;

            const agActive = additionalPricingState.additional_guests.active;
            const agAmt    = agActive ? (parseFloat(additionalPricingState.additional_guests.amount) || 0) : 0;

            const serviceFeeAmt = basePrice * (serviceFeePercentage / 100);
            const total = basePrice + taxAmount + cfAmt + agAmt + serviceFeeAmt;

            document.getElementById('calcBasePrice').textContent = symbol + ' ' + basePrice.toFixed(2);
            document.getElementById('calcTax').textContent       = symbol + ' ' + taxAmount.toFixed(2);
            document.getElementById('calcTotal').textContent     = symbol + ' ' + total.toFixed(2);

            updateAdditionalCurrencySymbols();
            renderAdditionalFeeRows(symbol, cfActive, cfAmt, agActive, agAmt, wkActive, wkAmt, basePrice);
            renderDiscountPreviewRows(symbol, total, basePrice, taxAmount, cfAmt, agAmt);
        }

        const serviceFeePercentage = parseFloat(@json(\App\Models\SiteSetting::get('service_fee', 0))) || 0;

        // --- Discounts Scripting ---
        let savedDiscounts = @json($room->roomPrice->discounts ?? null);
        let defaultDiscounts = {
            last_minute: { active: false, days: 14, percentage: 10 },
            early_bird: { active: false, rules: [] },
            length_of_stay: { active: false, rules: [] },
            custom: { active: false, rules: [] }
        };
        
        let discountsState = savedDiscounts || {};
        discountsState.last_minute = Object.assign({}, defaultDiscounts.last_minute, discountsState.last_minute || {});
        discountsState.early_bird = Object.assign({}, defaultDiscounts.early_bird, discountsState.early_bird || {});
        if (!discountsState.early_bird.rules) discountsState.early_bird.rules = [];
        
        discountsState.length_of_stay = Object.assign({}, defaultDiscounts.length_of_stay, discountsState.length_of_stay || {});
        if (!discountsState.length_of_stay.rules) discountsState.length_of_stay.rules = [];
        
        discountsState.custom = Object.assign({}, defaultDiscounts.custom, discountsState.custom || {});
        if (!discountsState.custom.rules) discountsState.custom.rules = [];

        // --- Additional Pricing Scripting ---
        let savedAdditionalPricing = @json($room->roomPrice->additional_pricing ?? null);
        let _apDefaults = {
            cleaning_fee:      { active: false, amount: 0 },
            additional_guests: { active: false, amount: 0, after_guests: 2 },
            weekend_pricing:   { active: false, amount: 0 }
        };
        let additionalPricingState = {
            cleaning_fee:      Object.assign({}, _apDefaults.cleaning_fee,      (savedAdditionalPricing || {}).cleaning_fee      || {}),
            additional_guests: Object.assign({}, _apDefaults.additional_guests, (savedAdditionalPricing || {}).additional_guests || {}),
            weekend_pricing:   Object.assign({}, _apDefaults.weekend_pricing,   (savedAdditionalPricing || {}).weekend_pricing   || {})
        };

        function toggleAdditionalOption(type) {
            additionalPricingState[type].active = !additionalPricingState[type].active;
            syncAdditionalPricingUI();
            saveAdditionalPricingState();
            calculateTotal();
        }

        function updateAdditionalPricing(type, field, value) {
            additionalPricingState[type][field] = (field === 'after_guests') ? parseInt(value) : (parseFloat(value) || 0);
            saveAdditionalPricingState();
            calculateTotal();
        }

        function saveAdditionalPricingState() {
            autoSave('additional_pricing', JSON.stringify(additionalPricingState), 'room_prices');
        }

        function syncAdditionalPricingUI() {
            ['cleaning_fee', 'additional_guests', 'weekend_pricing'].forEach(function(type) {
                if (!additionalPricingState[type]) return; // safety guard
                const chk     = document.getElementById('chk_' + type);
                const content = document.getElementById('content_' + type);
                const active  = additionalPricingState[type].active;
                if (chk)     chk.checked = active;
                if (content) content.style.display = active ? 'block' : 'none';
                if (active) {
                    const inp = document.getElementById('input_' + type);
                    if (inp && additionalPricingState[type].amount) inp.value = additionalPricingState[type].amount;
                    if (type === 'additional_guests') {
                        const sel = document.getElementById('select_after_guests');
                        if (sel) sel.value = additionalPricingState[type].after_guests || 2;
                    }
                }
            });
            updateAdditionalCurrencySymbols();
        }

        function updateAdditionalCurrencySymbols() {
            const sym = getSymbol();
            document.querySelectorAll('.additional-currency-symbol').forEach(function(el) { el.textContent = sym; });
        }

        function renderAdditionalFeeRows(symbol, cfActive, cfAmt, agActive, agAmt, wkActive, wkAmt, basePrice) {
            const el = document.getElementById('additionalFeeRows');
            if (!el) return;
            let html = '';
            if (cfActive && cfAmt > 0) {
                html += `<div style="height:1px;background:var(--border);margin:4px 0 4px 46px;"></div>
                <div class="pricing-item-row">
                    <div class="pricing-icon-box" style="width:34px;height:34px;border-radius:8px;background:#fef3c7;color:#d97706;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;"><i class="fas fa-broom"></i></div>
                    <div class="pricing-item-content"><span class="pricing-item-label">Cleaning fee</span><span class="pricing-item-value">${symbol} ${cfAmt.toFixed(2)}</span></div>
                </div>`;
            }
            if (agActive && agAmt > 0) {
                html += `<div style="height:1px;background:var(--border);margin:4px 0 4px 46px;"></div>
                <div class="pricing-item-row">
                    <div class="pricing-icon-box" style="width:34px;height:34px;border-radius:8px;background:#f0f9ff;color:#0284c7;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;"><i class="fas fa-users"></i></div>
                    <div class="pricing-item-content"><span class="pricing-item-label">Additional guests</span><span class="pricing-item-value">${symbol} ${agAmt.toFixed(2)}/night</span></div>
                </div>`;
            }
            if (wkActive && wkAmt > 0 && wkAmt !== basePrice) {
                html += `<div style="height:1px;background:var(--border);margin:4px 0 4px 46px;"></div>
                <div class="pricing-item-row">
                    <div class="pricing-icon-box" style="width:34px;height:34px;border-radius:8px;background:#fdf4ff;color:#9333ea;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;"><i class="fas fa-calendar-week"></i></div>
                    <div class="pricing-item-content"><span class="pricing-item-label">Weekend price <small class="fw-normal text-muted">(Fri &amp; Sat)</small></span><span class="pricing-item-value">${symbol} ${wkAmt.toFixed(2)}</span></div>
                </div>`;
            }
            if (serviceFeePercentage > 0) {
                const serviceFeeAmt = basePrice * (serviceFeePercentage / 100);
                html += `<div style="height:1px;background:var(--border);margin:4px 0 4px 46px;"></div>
                <div class="pricing-item-row">
                    <div class="pricing-icon-box" style="width:34px;height:34px;border-radius:8px;background:#e0e7ff;color:#4f46e5;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;"><i class="fas fa-percent"></i></div>
                    <div class="pricing-item-content"><span class="pricing-item-label">Service fee <small class="fw-normal text-muted">(${serviceFeePercentage}%)</small></span><span class="pricing-item-value">${symbol} ${serviceFeeAmt.toFixed(2)}</span></div>
                </div>`;
            }
            el.innerHTML = html;
        }

        function buildDiscountRow(label, pct, origBase, discBase, finalTotal, symbol, icon) {
            return `<div class="pricing-item-row" style="flex-direction:column;align-items:flex-start;gap:4px;padding:10px 0;">
                <div style="display:flex;align-items:center;gap:10px;width:100%;">
                     <div class="pricing-icon-box icon-box-green" style="width:34px;height:34px;border-radius:8px;flex-shrink:0;"><i class="fas ${icon}"></i></div>
                    <div style="display:flex;justify-content:space-between;align-items:center;width:100%;margin-left:0;">
                        <span class="pricing-item-label">${label} <span style="font-size:11px;color:var(--text-muted);font-weight:500;">(-${pct}%)</span></span>
                        <span><span class="pricing-item-value-strike">${symbol} ${origBase.toFixed(2)}</span>&nbsp;<span class="pricing-item-value-green">${symbol} ${discBase.toFixed(2)}</span></span>
                    </div>
                </div>
                <div style="margin-left:44px;font-size:12px;color:var(--text-muted);">
                    Total per night: <strong style="color:var(--body-text);">${symbol} ${finalTotal.toFixed(2)}</strong>
                </div>
            </div>`;
        }

        function renderDiscountPreviewRows(symbol, baseTotal, effectiveBase, taxAmount, cfAmt, agAmt) {
            const el = document.getElementById('discountPreviewRows');
            if (!el) return;
            let rows = '';
            // Discount is applied to base price ONLY; tax + additional fees added on top unchanged
            const serviceFeeAmt = effectiveBase * (serviceFeePercentage / 100);
            const discBase  = (pct) => effectiveBase * (1 - pct / 100);
            const finalTot  = (pct) => discBase(pct) + taxAmount + cfAmt + agAmt + serviceFeeAmt;
            if (discountsState.last_minute && discountsState.last_minute.active) {
                const pct = parseFloat(discountsState.last_minute.percentage) || 0;
                if (pct > 0) rows += buildDiscountRow('Last-minute', pct, effectiveBase, discBase(pct), finalTot(pct), symbol, 'fa-bolt');
            }
            if (discountsState.early_bird && discountsState.early_bird.active && discountsState.early_bird.rules.length > 0) {
                const best = discountsState.early_bird.rules.reduce((a, b) => b.percentage > a.percentage ? b : a);
                const pct  = parseFloat(best.percentage) || 0;
                if (pct > 0) rows += buildDiscountRow('Early-bird', pct, effectiveBase, discBase(pct), finalTot(pct), symbol, 'fa-clock');
            }
            if (discountsState.length_of_stay && discountsState.length_of_stay.active && discountsState.length_of_stay.rules.length > 0) {
                const best = discountsState.length_of_stay.rules.reduce((a, b) => b.percentage > a.percentage ? b : a);
                const pct  = parseFloat(best.percentage) || 0;
                if (pct > 0) rows += buildDiscountRow('Length-of-stay', pct, effectiveBase, discBase(pct), finalTot(pct), symbol, 'fa-moon');
            }
            el.innerHTML = rows ? `<div class="discount-preview-section">
                <div class="discount-preview-label"><i class="fas fa-tags me-1"></i> Price After Discounts</div>${rows}</div>` : '';
        }

        function renderDiscounts() {
            const container = document.getElementById('discounts_wrapper');
            if (!container) return;
            container.innerHTML = '';

            // 1. Last-Minute Card
            container.appendChild(createLastMinuteCard());

            // 2. Early-Bird Card
            container.appendChild(createEarlyBirdCard());

            // 3. Length-of-Stay Card
            container.appendChild(createLengthOfStayCard());

            // 4. Custom Discount Card
            container.appendChild(createCustomDiscountCard());

            // Initialize Flatpickr dynamic custom date fields
            initFlatpickrOnCustomFields();
        }

        window.flatpickrInstances = window.flatpickrInstances || {};

        function initFlatpickrOnCustomFields() {
            document.querySelectorAll('.custom-date-picker').forEach(el => {
                const type = el.dataset.type; // 'start' or 'end'
                const idx = parseInt(el.dataset.idx);
                const key = `custom_${type}_${idx}`;

                // Set dynamic minDate for end picker based on selected start date
                let minDateVal = "today";
                if (type === 'end') {
                    const rule = discountsState.custom.rules[idx];
                    if (rule && rule.start_date) {
                        minDateVal = rule.start_date;
                    }
                }

                const fpInstance = flatpickr(el, {
                    minDate: minDateVal,
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d-m-Y",
                    defaultDate: el.value || null,
                    disableMobile: "true",
                    onDayCreate: function(dObj, dStr, fp, dayElem) {
                        if (dayElem.dateObj.getDay() === 0) {
                            dayElem.classList.add("is-sunday");
                        }
                        // Highlight the start date in the end date picker!
                        if (type === 'end') {
                            const rule = discountsState.custom.rules[idx];
                            if (rule && rule.start_date) {
                                const startD = new Date(rule.start_date + "T00:00:00");
                                if (dayElem.dateObj.getFullYear() === startD.getFullYear() &&
                                    dayElem.dateObj.getMonth() === startD.getMonth() &&
                                    dayElem.dateObj.getDate() === startD.getDate()) {
                                    dayElem.classList.add("start-date-highlight");
                                }
                            }
                        }
                    },
                    onChange: function(selectedDates, dateStr) {
                        updateRule('custom', idx, type === 'start' ? 'start_date' : 'end_date', dateStr);

                        if (type === 'start') {
                            const endPickerKey = `custom_end_${idx}`;
                            if (window.flatpickrInstances[endPickerKey]) {
                                window.flatpickrInstances[endPickerKey].set('minDate', dateStr);
                                
                                // If end date is now before the start date, clear it
                                const rule = discountsState.custom.rules[idx];
                                if (rule && rule.end_date && new Date(rule.end_date) < new Date(dateStr)) {
                                    rule.end_date = '';
                                    window.flatpickrInstances[endPickerKey].clear();
                                    updateRule('custom', idx, 'end_date', '');
                                }
                                
                                setTimeout(() => {
                                    window.flatpickrInstances[endPickerKey].open();
                                }, 10);
                            }
                        }
                    }
                });

                window.flatpickrInstances[key] = fpInstance;
            });
        }

        function saveDiscountsState() {
            autoSave('discounts', JSON.stringify(discountsState), 'room_prices');
        }

        function toggleDiscountActive(type) {
            discountsState[type].active = !discountsState[type].active;
            renderDiscounts();
            saveDiscountsState();
            calculateTotal();
        }

        function updateLastMinute(field, val) {
            discountsState.last_minute[field] = parseFloat(val) || 0;
            saveDiscountsState();
            calculateTotal();
        }

        function updateRule(type, idx, field, val) {
            if (discountsState[type] && discountsState[type].rules[idx]) {
                discountsState[type].rules[idx][field] = val;
                saveDiscountsState();
                calculateTotal();
            }
        }

        function removeRule(type, idx) {
            discountsState[type].rules.splice(idx, 1);
            renderDiscounts();
            saveDiscountsState();
            calculateTotal();
        }

        function addRule(type) {
            if (type === 'early_bird') {
                discountsState.early_bird.rules.push({ days: 30, percentage: 10 });
            } else if (type === 'custom') {
                discountsState.custom.rules.push({ start_date: '', end_date: '', percentage: 10 });
            }
            renderDiscounts();
            saveDiscountsState();
            calculateTotal();
        }

        function selectNightsToAdd(selectElement) {
            const val = selectElement.value;
            if (!val) return;

            const exists = discountsState.length_of_stay.rules.some(r => r.nights == val);
            if (!exists) {
                discountsState.length_of_stay.rules.push({ nights: parseInt(val), percentage: 10 });
                renderDiscounts();
                saveDiscountsState();
                calculateTotal();
            } else {
                showToast('info', 'This stay discount is already added.');
            }
            selectElement.value = '';
        }

        function createLastMinuteCard() {
            const div = document.createElement('div');
            div.className = 'discount-card';
            const active = discountsState.last_minute.active;
            div.innerHTML = `
                <div class="discount-header" onclick="toggleDiscountActive('last_minute')">
                    <input type="checkbox" class="discount-checkbox" ${active ? 'checked' : ''} onclick="event.stopPropagation(); toggleDiscountActive('last_minute')">
                    <h4 class="discount-label">Last-Minute Discounts</h4>
                </div>
                <div class="discount-content" style="display: ${active ? 'block' : 'none'};">
                    <div class="discount-row" style="display: flex; align-items: center; gap: 10px; flex-wrap: nowrap;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div class="discount-input-group">
                                <span class="discount-input-prefix">days</span>
                                <input type="number" class="discount-input-field" value="${discountsState.last_minute.days}" oninput="updateLastMinute('days', this.value)" placeholder="Days" style="width: 60px;">
                            </div>
                            <span class="fw-bold small text-muted" style="white-space: nowrap;">days before check-in</span>
                        </div>

                        <div style="display: flex; align-items: center; gap: 8px; margin-left: 16px;">
                            <div class="discount-input-group">
                                <span class="discount-input-prefix">%</span>
                                <input type="number" class="discount-input-field" value="${discountsState.last_minute.percentage}" oninput="updateLastMinute('percentage', this.value)" placeholder="Discount" style="width: 60px;">
                            </div>
                            <span class="fw-bold small text-muted" style="white-space: nowrap;">percentage of discount</span>
                        </div>
                    </div>
                </div>
            `;
            return div;
        }

        function createEarlyBirdCard() {
            const div = document.createElement('div');
            div.className = 'discount-card';
            const active = discountsState.early_bird.active;
            let rulesHtml = '';

            discountsState.early_bird.rules.forEach((rule, idx) => {
                rulesHtml += `
                    <div class="discount-row">
                        <div class="discount-input-group">
                            <span class="discount-input-prefix">days</span>
                            <input type="number" class="discount-input-field" value="${rule.days}" oninput="updateRule('early_bird', ${idx}, 'days', this.value)" placeholder="Days ahead" style="width: 100px;">
                        </div>

                        <div class="discount-input-group">
                            <span class="discount-input-prefix">%</span>
                            <input type="number" class="discount-input-field" value="${rule.percentage}" oninput="updateRule('early_bird', ${idx}, 'percentage', this.value)" placeholder="Discount %" style="width: 100px;">
                        </div>

                        <button type="button" class="btn-remove-discount" onclick="removeRule('early_bird', ${idx})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
            });

            div.innerHTML = `
                <div class="discount-header" onclick="toggleDiscountActive('early_bird')">
                    <input type="checkbox" class="discount-checkbox" ${active ? 'checked' : ''} onclick="event.stopPropagation(); toggleDiscountActive('early_bird')">
                    <h4 class="discount-label">Early-Bird Discounts</h4>
                </div>
                <div class="discount-content" style="display: ${active ? 'block' : 'none'};">
                    <div class="early-bird-rules-container">
                        ${rulesHtml || '<p class="text-muted small my-2">No early bird rules configured. Click Add to get started.</p>'}
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn-add-discount" onclick="addRule('early_bird')">
                            <i class="fas fa-plus" style="font-size:11px;"></i>
                            <span>Add</span>
                        </button>
                    </div>
                </div>
            `;
            return div;
        }

        function createLengthOfStayCard() {
            const div = document.createElement('div');
            div.className = 'discount-card';
            const active = discountsState.length_of_stay.active;
            let rulesHtml = '';
            const options = [2, 3, 4, 5, 6, 7, 14, 28];

            discountsState.length_of_stay.rules.forEach((rule, idx) => {
                let selectOptions = '';
                options.forEach(opt => {
                    const label = opt >= 7 ? `${opt / 7} week${opt >= 14 ? 's' : ''} (${opt} nights)` : `${opt} nights`;
                    selectOptions += `<option value="${opt}" ${rule.nights == opt ? 'selected' : ''}>${label}</option>`;
                });

                rulesHtml += `
                    <div class="discount-row">
                        <select class="discount-select" onchange="updateRule('length_of_stay', ${idx}, 'nights', this.value)">
                            ${selectOptions}
                        </select>

                        <div class="discount-input-group">
                            <span class="discount-input-prefix">%</span>
                            <input type="number" class="discount-input-field" value="${rule.percentage}" oninput="updateRule('length_of_stay', ${idx}, 'percentage', this.value)" placeholder="Discount %" style="width: 100px;">
                        </div>

                        <button type="button" class="btn-remove-discount" onclick="removeRule('length_of_stay', ${idx})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
            });

            div.innerHTML = `
                <div class="discount-header" onclick="toggleDiscountActive('length_of_stay')">
                    <input type="checkbox" class="discount-checkbox" ${active ? 'checked' : ''} onclick="event.stopPropagation(); toggleDiscountActive('length_of_stay')">
                    <h4 class="discount-label">Length-of-Stay Discounts</h4>
                </div>
                <div class="discount-content" style="display: ${active ? 'block' : 'none'};">
                    <div class="length-of-stay-rules-container">
                        ${rulesHtml || '<p class="text-muted small my-2">No length of stay rules configured.</p>'}
                    </div>
                    <div class="mt-3">
                        <select class="discount-select" onchange="selectNightsToAdd(this)">
                            <option value="" disabled selected>Select nights...</option>
                            <option value="2">2 nights</option>
                            <option value="3">3 nights</option>
                            <option value="4">4 nights</option>
                            <option value="5">5 nights</option>
                            <option value="6">6 nights</option>
                            <option value="7">1 week (7 nights)</option>
                            <option value="14">2 weeks (14 nights)</option>
                            <option value="28">1 month (28 nights)</option>
                        </select>
                    </div>
                </div>
            `;
            return div;
        }

        function createCustomDiscountCard() {
            const div = document.createElement('div');
            div.className = 'discount-card';
            const active = discountsState.custom.active;
            let rulesHtml = '';

            discountsState.custom.rules.forEach((rule, idx) => {
                rulesHtml += `
                    <div class="discount-row">
                        <div class="discount-input-group">
                            <span class="discount-input-prefix">start</span>
                            <input type="text" class="discount-input-field custom-date-picker" data-type="start" data-idx="${idx}" value="${rule.start_date || ''}" placeholder="Select Date" style="width: 110px; cursor: pointer;">
                        </div>

                        <div class="discount-input-group">
                            <span class="discount-input-prefix">end</span>
                            <input type="text" class="discount-input-field custom-date-picker" data-type="end" data-idx="${idx}" value="${rule.end_date || ''}" placeholder="Select Date" style="width: 110px; cursor: pointer;">
                        </div>

                        <div class="discount-input-group">
                            <span class="discount-input-prefix">%</span>
                            <input type="number" class="discount-input-field" value="${rule.percentage}" oninput="updateRule('custom', ${idx}, 'percentage', this.value)" placeholder="Discount" style="width: 80px;">
                        </div>

                        <button type="button" class="btn-remove-discount" onclick="removeRule('custom', ${idx})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
            });

            div.innerHTML = `
                <div class="discount-header" onclick="toggleDiscountActive('custom')">
                    <input type="checkbox" class="discount-checkbox" ${active ? 'checked' : ''} onclick="event.stopPropagation(); toggleDiscountActive('custom')">
                    <h4 class="discount-label">Custom Discount</h4>
                </div>
                <div class="discount-content" style="display: ${active ? 'block' : 'none'};">
                    <div class="custom-rules-container">
                        ${rulesHtml || '<p class="text-muted small my-2">No custom discounts configured. Click Add below to start.</p>'}
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn-add-discount" onclick="addRule('custom')">
                            <i class="fas fa-plus" style="font-size:11px;"></i>
                            <span>Add</span>
                        </button>
                    </div>
                </div>
            `;
            return div;
        }

        document.addEventListener('DOMContentLoaded', function () {
            syncAdditionalPricingUI();
            calculateTotal();
            renderDiscounts();

            // Auto-save currency on page load if not yet saved
            const currency = document.getElementById('currencySelect').value;
            @if(!$room->roomPrice || !$room->roomPrice->currency)
                autoSave('currency', currency, 'room_prices', 'currencySelect');
            @endif
        });
    </script>
@endsection