@extends('host.layout')

@section('host-content')
    <h1 class="host-title">Rules & Calendar Availability</h1>
    <p class="host-subtitle">Establish standard guest rules and block any dates your property is unavailable.</p>

    <!-- Booking Type -->
    <div class="discount-card mb-4" style="border-radius: 20px; padding: 24px; border: 1.5px solid var(--border); background: var(--card-bg); margin: 10px;">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
            <div style="width: 42px; height: 42px; border-radius: 50%; background: rgba(37, 99, 235, 0.08); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <i class="fas fa-bolt"></i>
            </div>
            <div>
                <h4 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--text-main);">Booking Type</h4>
                <p class="text-muted small mb-0" style="margin-top: 2px;">Choose how guests book your space.</p>
            </div>
        </div>

        <div class="form-floating-airbnb" id="wrap_booking_type">
            <select class="form-control-airbnb" name="booking_type" id="bookingTypeSelect" style="height:60px;"
                onchange="updateField('booking_type', this.value); updateSummaryBookingType(this.value)">
                <option value="Instant Booking" {{ $room->booking_type === 'Instant Booking' ? 'selected' : '' }}>Instant Booking (Book automatically)</option>
                <option value="Request to Book" {{ $room->booking_type === 'Request to Book' ? 'selected' : '' }}>Request to Book (Requires approval)</option>
            </select>
            <label for="bookingTypeSelect">Select Booking Type</label>
        </div>
    </div>

    <!-- Cancellation Policy -->
    <div class="discount-card mb-4" style="border-radius: 20px; padding: 24px; border: 1.5px solid var(--border); background: var(--card-bg); margin: 10px;">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
            <div style="width: 42px; height: 42px; border-radius: 50%; background: rgba(37, 99, 235, 0.08); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <h4 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--text-main);">Cancellation Policy</h4>
                <p class="text-muted small mb-0" style="margin-top: 2px;">Protect your listing from last-minute cancellations.</p>
            </div>
        </div>

        <div class="form-floating-airbnb mb-4" id="wrap_cancellation_policy">
            <select class="form-control-airbnb" name="cancellation_policy" id="cancellationPolicySelect" style="height:60px;"
                onchange="updateField('cancellation_policy', this.value); updateSummaryCancellationPolicy(this.value)">
                <option value="Flexible" {{ $room->cancellation_policy === 'Flexible' ? 'selected' : '' }}>Flexible (Full refund 1 day prior)</option>
                <option value="Moderate" {{ $room->cancellation_policy === 'Moderate' ? 'selected' : '' }}>Moderate (Full refund 5 days prior)</option>
                <option value="Strict" {{ $room->cancellation_policy === 'Strict' ? 'selected' : '' }}>Strict (50% refund up to 1 week prior)</option>
            </select>
            <label for="cancellationPolicySelect">Select Default Policy</label>
        </div>

        <!-- Custom Cancellation toggle -->
        <div class="custom-cancellation-wrap" style="background: var(--bg-primary); border-radius: 16px; padding: 20px; border: 1px solid var(--border);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <div>
                    <h5 style="font-size: 0.95rem; font-weight: 700; margin: 0; color: var(--text-main);">Custom Cancellation Policy</h5>
                    <p class="text-muted small mb-0" style="margin-top: 2px;">Set custom penalty terms for your property.</p>
                </div>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" id="customCancellationToggle" style="width: 44px; height: 22px; cursor: pointer;"
                        {{ $room->custom_cancellation ? 'checked' : '' }}
                        onchange="toggleCustomCancellation(this.checked)">
                </div>
            </div>

            <div id="customCancellationFields" style="display: {{ $room->custom_cancellation ? 'block' : 'none' }};">
                <div style="height: 1px; background: var(--border); margin: 15px 0;"></div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating-airbnb">
                            <input type="number" class="form-control-airbnb" id="freeCancellationDaysInput" value="{{ $room->free_cancellation_days ?? 0 }}" placeholder=" "
                                oninput="updateField('free_cancellation_days', this.value); generateCancellationMessage();">
                            <label for="freeCancellationDaysInput">Free Cancellation Days</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating-airbnb">
                            <input type="number" class="form-control-airbnb" id="cancellationFeeInput" value="{{ $room->cancellation_fee ?? 0 }}" placeholder=" "
                                oninput="updateField('cancellation_fee', this.value); generateCancellationMessage();">
                            <label for="cancellationFeeInput">Cancellation Fee (%)</label>
                        </div>
                    </div>
                </div>

                <!-- Live Message Preview Box -->
                <div class="mt-4" style="background: rgba(37, 99, 235, 0.04); border-left: 4px solid var(--primary); padding: 14px 16px; border-radius: 0 12px 12px 0;">
                    <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--primary); display: block; margin-bottom: 4px;">Dynamic Refund Preview</span>
                    <p id="cancellationMessagePreview" style="margin: 0; font-size: 13px; font-weight: 600; color: var(--text-main); line-height: 1.4;">
                        Calculating terms...
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Policy -->
    <div class="discount-card mb-4" style="border-radius: 20px; padding: 24px; border: 1.5px solid var(--border); background: var(--card-bg); margin: 10px;">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
            <div style="width: 42px; height: 42px; border-radius: 50%; background: rgba(37, 99, 235, 0.08); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <i class="far fa-clock"></i>
            </div>
            <div>
                <h4 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--text-main);">Checkout Policy</h4>
                <p class="text-muted small mb-0" style="margin-top: 2px;">Set the standard checkout time for departing guests.</p>
            </div>
        </div>

        <div class="form-floating-airbnb" id="wrap_checkout_policy">
            <select class="form-control-airbnb" name="checkout_policy" id="checkoutPolicySelect" style="height:60px;"
                onchange="updateField('checkout_policy', this.value); updateSummaryCheckoutPolicy(this.value)">
                @php
                    $times = ['08:00 AM', '09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '01:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM', '06:00 PM', '07:00 PM', '08:00 PM'];
                    $currentCheckout = $room->checkout_policy ?: '11:00 AM';
                @endphp
                @foreach($times as $time)
                    <option value="{{ $time }}" {{ $currentCheckout === $time ? 'selected' : '' }}>{{ $time }}</option>
                @endforeach
            </select>
            <label for="checkoutPolicySelect">Select Checkout Time</label>
        </div>
    </div>

    <!-- House Rules Checkboxes -->
    <div class="discount-card mb-4" style="border-radius: 20px; padding: 24px; border: 1.5px solid var(--border); background: var(--card-bg); margin: 10px;">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
            <div style="width: 42px; height: 42px; border-radius: 50%; background: rgba(220, 38, 38, 0.08); color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div>
                <h4 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--text-main);">Official House Rules</h4>
                <p class="text-muted small mb-0" style="margin-top: 2px;">Guests must agree to your rules before checking in.</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 14px;">
            @php
                $selected = is_array($room->selected_rules) ? $room->selected_rules : [];
            @endphp
            @foreach($roomRules as $index => $rule)
                <label class="rule-checkbox-card {{ $index >= 4 ? 'rule-card-hidden' : '' }}" style="display: {{ $index >= 4 ? 'none' : 'flex' }}; align-items: center; gap: 12px; background: var(--bg-primary); border: 1.5px solid {{ in_array($rule->id, $selected) ? 'var(--primary)' : 'var(--border)' }}; border-radius: 16px; padding: 14px; cursor: pointer; transition: all 0.25s ease;">
                    <input type="checkbox" class="rule-raw-checkbox" style="display: none !important;" value="{{ $rule->id }}" {{ in_array($rule->id, $selected) ? 'checked' : '' }} onchange="toggleHouseRule(this)">
                    <div class="rule-checkbox-custom" style="width: 20px; height: 20px; border: 2px solid {{ in_array($rule->id, $selected) ? 'var(--primary)' : '#9ca3af' }}; background: {{ in_array($rule->id, $selected) ? 'var(--primary)' : 'transparent' }}; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #fff; transition: all 0.2s ease;">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <span style="font-size: 13.5px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                            @if($rule->icon)
                                <i class="{{ $rule->icon }}" style="color: var(--primary); font-size: 13px;"></i>
                            @endif
                            {{ $rule->rule_name }}
                        </span>
                        <span style="font-size: 11px; color: var(--text-muted); display: block; margin-top: 2px;">{{ $rule->rule_text }}</span>
                    </div>
                </label>
            @endforeach
        </div>

        <!-- Rules Expand Button -->
        <div style="text-align: center; margin-top: 24px;">
            <button type="button" id="btn_toggle_rules" onclick="toggleAllRules()" class="btn btn-outline-primary" style="border-radius: 20px; font-weight: 700; padding: 8px 24px; font-size: 13.5px; cursor: pointer; transition: all 0.25s ease; border: 1.5px solid var(--primary); color: var(--primary); background: transparent;">
                Show More <i class="fas fa-chevron-down ms-1" style="font-size: 11px;"></i>
            </button>
        </div>
    </div>

    <!-- Calendar Panel to block dates -->
    <div class="discount-card mb-5" style="border-radius: 20px; padding: 24px; border: 1.5px solid var(--border); background: var(--card-bg); margin: 10px;">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
            <div style="width: 42px; height: 42px; border-radius: 50%; background: rgba(16, 185, 129, 0.08); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div>
                <h4 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--text-main);">Availability Calendar</h4>
                <p class="text-muted small mb-0" style="margin-top: 2px;">Select specific dates to toggle availability blocks.</p>
            </div>
        </div>

        <div style="display: flex; gap: 24px; align-items: flex-start; flex-wrap: wrap;">
            <!-- Interactive Custom Calendar -->
            <div style="flex: 1; min-width: 300px; background: var(--bg-primary); border: 1.5px solid var(--border); border-radius: 20px; padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <button type="button" class="btn btn-outline-secondary btn-sm border-0" onclick="prevMonth()" style="border-radius: 50%; width: 36px; height: 36px;"><i class="fas fa-chevron-left"></i></button>
                    <h5 id="calendar_month_title" style="margin: 0; font-weight: 800; color: var(--text-main);">May 2026</h5>
                    <button type="button" class="btn btn-outline-secondary btn-sm border-0" onclick="nextMonth()" style="border-radius: 50%; width: 36px; height: 36px;"><i class="fas fa-chevron-right"></i></button>
                </div>

                <!-- Calendar Grid -->
                <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; text-align: center; font-weight: 700; font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">
                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                </div>
                <div id="calendar_days_grid" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px;">
                    <!-- Dynamically populated via JS -->
                </div>
            </div>

            <!-- Block actions panel -->
            <div id="calendar_action_panel" style="width: 250px; background: var(--bg-primary); border: 1.5px solid var(--border); border-radius: 20px; padding: 20px; display: flex; flex-direction: column; gap: 15px;">
                <h5 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: var(--text-main);">Selected Date</h5>
                <p id="selected_date_text" style="margin: 0; font-size: 13.5px; font-weight: 600; color: var(--text-muted);">Please select a date on the calendar...</p>
                
                <div style="height: 1px; background: var(--border);"></div>

                <div id="selected_date_actions" style="display: none;">
                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; margin-bottom: 15px;">
                        <span>Status:</span>
                        <strong id="selected_date_status" style="color: #10b981;">Available</strong>
                    </div>

                    <button type="button" id="btn_toggle_block" onclick="toggleSelectedDateBlock()" class="btn-modern-toggle w-100" style="height: 30px; width: 205px; border-radius: 14px; font-weight: 600; font-size: 13.5px; letter-spacing: 0.3px; border: none; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2); color: #fff; background: linear-gradient(135deg, #ef4444, #dc2626);">
                        <i class="fas fa-ban"></i> Block Date
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="host-actions">
        <a href="{{ route('host.step', ['room' => $room->id, 'step' => 5]) }}" class="btn-prev">Back</a>
        <form action="{{ route('host.finish', $room->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn-next" style="width: 160px; height: 50px; display: flex; align-items: center; justify-content: center;">
                <span class="btn-text">Publish Listing</span>
                <div class="btn-spinner"></div>
            </button>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    // Selected Rules Array
    let selectedRules = @json(is_array($room->selected_rules) ? $room->selected_rules : []);
    
    // Blocked Dates from Database
    let blockedDates = new Set(@json($room->calendars->pluck('date')->toArray()));

    // Active state date picking variables
    let currentDate = new Date(2026, 4, 23); // Default anchor: May 23, 2026
    let displayedYear = currentDate.getFullYear();
    let displayedMonth = currentDate.getMonth();
    let selectedDateStr = null;

    // Cancellation UI Sync
    function toggleCustomCancellation(isChecked) {
        document.getElementById('customCancellationFields').style.display = isChecked ? 'block' : 'none';
        document.getElementById('wrap_cancellation_policy').style.display = isChecked ? 'none' : 'block';
        document.getElementById('summary_custom_cancel_row').style.display = isChecked ? 'flex' : 'none';
        
        // Toggle standard row in summary
        const standardSummaryRow = document.getElementById('summary_cancellation_policy_row');
        if (standardSummaryRow) {
            standardSummaryRow.style.display = isChecked ? 'none' : 'flex';
        }

        updateField('custom_cancellation', isChecked ? 1 : 0);
        if (isChecked) {
            generateCancellationMessage();
        }
    }

    function generateCancellationMessage() {
        const days = parseInt(document.getElementById('freeCancellationDaysInput').value) || 0;
        const fee = parseInt(document.getElementById('cancellationFeeInput').value) || 0;

        let message = `Guests receive a 100% free cancellation until ${days} days before check-in. `;
        if (fee > 0) {
            message += `Afterwards, a fee of ${fee}% on the base price is applied for cancellation.`;
        } else {
            message += `No penalty fee applies for late cancellation.`;
        }

        document.getElementById('cancellationMessagePreview').innerText = message;
    }

    // Rules Expand/Collapse
    let rulesExpanded = false;
    function toggleAllRules() {
        const hiddenCards = document.querySelectorAll('.rule-card-hidden');
        const btn = document.getElementById('btn_toggle_rules');
        rulesExpanded = !rulesExpanded;

        hiddenCards.forEach(card => {
            card.style.display = rulesExpanded ? 'flex' : 'none';
        });

        if (rulesExpanded) {
            btn.innerHTML = 'Show Less <i class="fas fa-chevron-up ms-1" style="font-size: 11px;"></i>';
        } else {
            btn.innerHTML = 'Show More <i class="fas fa-chevron-down ms-1" style="font-size: 11px;"></i>';
        }
    }

    function toggleHouseRule(checkbox) {
        const val = parseInt(checkbox.value);
        const card = checkbox.closest('.rule-checkbox-card');
        const customCheck = card.querySelector('.rule-checkbox-custom');

        if (checkbox.checked) {
            if (!selectedRules.includes(val)) selectedRules.push(val);
            card.style.borderColor = 'var(--primary)';
            customCheck.style.background = 'var(--primary)';
            customCheck.style.borderColor = 'var(--primary)';
        } else {
            selectedRules = selectedRules.filter(id => id !== val);
            card.style.borderColor = 'var(--border)';
            customCheck.style.background = 'transparent';
            customCheck.style.borderColor = '#9ca3af';
        }

        // Live save rules
        autoSave('selected_rules', JSON.stringify(selectedRules));
        
        // Update summary label
        document.getElementById('summary_rules_count').innerText = `${selectedRules.length} Selected`;
    }

    // Dynamic Summary View Modifiers
    function updateSummaryBookingType(value) {
        document.getElementById('summary_booking_type').innerText = value;
    }

    function updateSummaryCancellationPolicy(value) {
        document.getElementById('summary_cancellation_policy').innerText = value;
    }

    function updateSummaryCheckoutPolicy(value) {
        const el = document.getElementById('summary_checkout_policy');
        if (el) {
            el.innerText = value;
        }
    }

    // Calendar Generation Scripting
    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    function renderCalendar() {
        const grid = document.getElementById('calendar_days_grid');
        const title = document.getElementById('calendar_month_title');
        grid.innerHTML = '';

        title.innerText = `${monthNames[displayedMonth]} ${displayedYear}`;

        const firstDay = new Date(displayedYear, displayedMonth, 1).getDay();
        const totalDays = new Date(displayedYear, displayedMonth + 1, 0).getDate();

        // Empty spacer cells for preceding days of the week
        for (let i = 0; i < firstDay; i++) {
            const emptyCell = document.createElement('div');
            grid.appendChild(emptyCell);
        }

        for (let d = 1; d <= totalDays; d++) {
            const cell = document.createElement('div');
            const dateObj = new Date(displayedYear, displayedMonth, d);
            const dateStr = formatDateString(dateObj);

            cell.className = 'calendar-day-cell';
            cell.innerText = d;
            
            // Inline CSS styling for calendar days
            cell.style.height = '42px';
            cell.style.display = 'flex';
            cell.style.alignItems = 'center';
            cell.style.justifyContent = 'center';
            cell.style.borderRadius = '10px';
            cell.style.cursor = 'pointer';
            cell.style.fontWeight = '700';
            cell.style.fontSize = '13px';
            cell.style.position = 'relative';
            cell.style.transition = 'all 0.2s ease';

            const isBlocked = blockedDates.has(dateStr);
            const isSelected = selectedDateStr === dateStr;

            // Apply style tokens based on date status
            if (isBlocked) {
                cell.style.background = 'rgba(239, 68, 68, 0.1)';
                cell.style.color = '#ef4444';
                cell.style.border = '1.5px solid rgba(239, 68, 68, 0.3)';
            } else {
                cell.style.background = 'transparent';
                cell.style.color = 'var(--text-main)';
                cell.style.border = '1px solid transparent';
            }

            if (isSelected) {
                cell.style.border = '2px solid var(--primary)';
                cell.style.boxShadow = '0 0 10px rgba(37, 99, 235, 0.2)';
            }

            // Cell click handler
            cell.onclick = () => selectCalendarDate(dateStr, isBlocked);

            grid.appendChild(cell);
        }
    }

    function selectCalendarDate(dateStr, isBlocked) {
        selectedDateStr = dateStr;
        renderCalendar();

        // Render actions panel info
        const parsedDate = new Date(dateStr + "T00:00:00");
        const formattedText = parsedDate.toLocaleDateString('en-US', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });

        document.getElementById('selected_date_text').innerText = formattedText;
        document.getElementById('selected_date_actions').style.display = 'block';

        const statusLabel = document.getElementById('selected_date_status');
        const btnToggle = document.getElementById('btn_toggle_block');

        if (isBlocked) {
            statusLabel.innerText = "Blocked";
            statusLabel.style.color = "#ef4444";
            btnToggle.className = "btn-modern-toggle w-100";
            btnToggle.style.background = "linear-gradient(135deg, #10b981, #059669)";
            btnToggle.style.boxShadow = "0 4px 14px rgba(16, 185, 129, 0.3)";
            btnToggle.innerHTML = '<i class="fas fa-check-circle"></i> Make Available';
        } else {
            statusLabel.innerText = "Available";
            statusLabel.style.color = "#10b981";
            btnToggle.className = "btn-modern-toggle w-100";
            btnToggle.style.background = "linear-gradient(135deg, #ef4444, #dc2626)";
            btnToggle.style.boxShadow = "0 4px 14px rgba(239, 68, 68, 0.3)";
            btnToggle.innerHTML = '<i class="fas fa-ban"></i> Block Date';
        }
    }

    function toggleSelectedDateBlock() {
        if (!selectedDateStr) return;

        const isCurrentlyBlocked = blockedDates.has(selectedDateStr);
        const block = !isCurrentlyBlocked;

        showToast('saving', 'Updating calendar...');

        fetch(`/host/{{ $room->id }}/calendar/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                date: selectedDateStr,
                block: block
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Calendar Updated');
                if (block) {
                    blockedDates.add(selectedDateStr);
                } else {
                    blockedDates.delete(selectedDateStr);
                }
                // Refresh views
                selectCalendarDate(selectedDateStr, block);
            } else {
                showToast('error', 'Failed to update calendar');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('error', 'Error toggling calendar!');
        });
    }

    function prevMonth() {
        displayedMonth--;
        if (displayedMonth < 0) {
            displayedMonth = 11;
            displayedYear--;
        }
        renderCalendar();
    }

    function nextMonth() {
        displayedMonth++;
        if (displayedMonth > 11) {
            displayedMonth = 0;
            displayedYear++;
        }
        renderCalendar();
    }

    function formatDateString(d) {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function updateField(field, value) {
        autoSave(field, value, 'rooms');
    }

    document.addEventListener('DOMContentLoaded', () => {
        generateCancellationMessage();
        renderCalendar();
        
        // Check initial custom cancellation state
        const initialCustom = document.getElementById('customCancellationToggle').checked;
        toggleCustomCancellation(initialCustom);
    });
</script>

<style>
    /* Rule Card Styles */
    .rule-checkbox-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.04);
        background: var(--card-bg) !important;
    }
    .rule-checkbox-card input:checked + .rule-checkbox-custom {
        transform: scale(1.05);
    }
    .calendar-day-cell:hover {
        background: rgba(37, 99, 235, 0.05) !important;
        transform: scale(1.08);
    }
    .btn-modern-toggle:hover {
        transform: translateY(-2px);
        filter: brightness(1.08);
    }
    .btn-modern-toggle:active {
        transform: translateY(1px);
        filter: brightness(0.95);
    }
</style>
@endsection
