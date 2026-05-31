@extends('host.layout')

@section('styles')
    <style>
        .price-per-guest-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(16, 185, 129, 0.06);
            border: 1px solid rgba(16, 185, 129, 0.15);
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #10b981 !important;
            cursor: pointer;
            user-select: none;
            transition: all 0.25s ease;
        }
        .price-per-guest-toggle:hover {
            background: rgba(16, 185, 129, 0.12);
            border-color: rgba(16, 185, 129, 0.3);
            transform: translateY(-1px);
        }
        .price-per-guest-toggle input[type="checkbox"] {
            width: 16px !important;
            height: 16px !important;
            accent-color: #10b981 !important;
            cursor: pointer;
            margin: 0 !important;
        }

        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 12px;
            margin: 10px -2px;
        }

        .amenity-card-airbnb {
            border: 1.5px solid var(--border);
            border-radius: 16px;
            padding: 6px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.2s;
            background: var(--card-bg);
            user-select: none;
        }

        .amenity-card-airbnb:hover {
            border-color: var(--primary);
        }

        .amenity-card-airbnb.selected {
            border-color: var(--primary);
            background: rgb(64 56 255 / 5%);
            font-weight: 700;
            box-shadow: 0 0 0 1px var(--primary);
        }

        .amenity-card-airbnb input[type="checkbox"] {
            display: none;
        }

        .enhancement-row-card {
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 20px 24px;
            margin-bottom: 20px;
            transition: border-color 0.3s;
            box-sizing: border-box;
            width: 100%;
            overflow: hidden;
        }

        .enhancement-row-card:hover {
            border-color: var(--primary);
        }

        .enhancement-card-header {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            width: 100%;
            margin-bottom: 0;
        }

        .enhancement-card-header h3 {
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0;
            white-space: nowrap;
            flex-shrink: 1;
            min-width: 0;
        }

        .btn-menu-setup {
            background: var(--primary);
            color: white !important;
            border: none;
            padding: 7px 18px;
            font-weight: 700;
            font-size: 13px;
            border-radius: 50px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            flex-shrink: 0;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.25);
        }

        .btn-menu-setup:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.35);
        }

        .food-items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 16px;
            width: 100%;
        }

        .food-item-card {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 9px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            background: var(--card-bg);
            font-size: 13px;
            color: var(--text-main);
            transition: all 0.2s;
            min-width: 0;
            overflow: hidden;
            box-sizing: border-box;
        }

        .food-item-card:hover {
            border-color: var(--primary);
            background: var(--bg-primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .food-item-card .item-left {
            display: flex;
            align-items: center;
            gap: 7px;
            min-width: 0;
            flex: 1;
            overflow: hidden;
        }

        .food-item-card .item-name {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            min-width: 0;
            flex: 1;
        }

        .food-item-card .item-price {
            font-weight: 700;
            font-size: 12px;
            color: var(--primary);
            white-space: nowrap;
            flex-shrink: 0;
        }

        @media (max-width: 576px) {
            .enhancement-row-card {
                padding: 14px 16px;
                border-radius: 16px;
            }

            .enhancement-card-header h3 {
                font-size: 0.95rem;
            }

            .btn-menu-setup {
                padding: 6px 13px;
                font-size: 12px;
            }

            .food-items-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                gap: 8px;
            }
        }

        @media (max-width: 360px) {
            .food-items-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            backdrop-filter: blur(8px);
            transition: all 0.3s ease;
        }

        .modal-card {
            background: var(--card-bg);
            width: 90%;
            max-width: 520px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px var(--border);
            animation: modalSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: scale(0.96) translateY(12px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-header {
            padding: 24px 28px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--card-bg);
        }

        .modal-header h3 {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
            letter-spacing: -0.02em;
        }

        .modal-btn-close {
            background: rgba(0, 0, 0, 0.04);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 20px;
        }

        body.dark-mode .modal-btn-close {
            background: rgba(255, 255, 255, 0.08);
        }

        .modal-btn-close:hover {
            background: var(--error);
            color: white;
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 28px;
            max-height: 55vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--border) transparent;
        }

        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background-color: var(--border);
            border-radius: 20px;
        }

        .dynamic-item-row {
            display: flex;
            gap: 28px;
            margin-bottom: 16px;
            align-items: center;
            animation: rowFadeIn 0.3s ease;
        }

        @keyframes rowFadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-input-wrapper {
            position: relative;
            flex: 1;
        }

        .modal-input {
            width: 100%;
            height: 48px;
            padding: 0 16px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            font-size: 15px;
            background: var(--card-bg);
            color: var(--text-main);
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .modal-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
        }

        .modal-input-price-wrapper {
            position: relative;
            width: 120px;
            flex-shrink: 0;
        }

        .modal-input-price-wrapper::before {
            content: '₹';
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-weight: 600;
            font-size: 14px;
            pointer-events: none;
        }

        .modal-input-price-wrapper .modal-input {
            padding-left: 28px;
        }

        .btn-remove-item {
            color: var(--text-muted);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
            border: 1px solid var(--border);
            background: var(--card-bg);
        }

        .btn-remove-item:hover {
            color: var(--error);
            border-color: rgba(239, 68, 68, 0.2);
            background: rgba(239, 68, 68, 0.05);
            transform: scale(1.05);
        }

        .btn-add-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            height: 48px;
            background: transparent;
            border: 2px dashed var(--primary);
            color: var(--primary);
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 4px;
        }

        .btn-add-item:hover {
            background: rgba(59, 130, 246, 0.06);
            border-color: var(--primary-dark);
            color: var(--primary-dark);
            transform: translateY(-1px);
        }

        .modal-footer {
            padding: 20px 28px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            background: var(--bg-primary);
        }

        .btn-modal-cancel {
            background: var(--card-bg);
            color: var(--text-main);
            border: 1.5px solid var(--border);
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 700;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-modal-cancel:hover {
            background: var(--border);
            color: var(--text-main);
        }

        .btn-modal-save {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 28px;
            font-size: 14px;
            font-weight: 700;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }

        .btn-modal-save:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.35);
        }



        .show-more-btn {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            padding: 12px 0 0 0;
            margin-top: 5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .show-more-btn:hover {
            text-decoration: underline;
        }

        .hidden-item {
            display: none !important;
        }

        /* --- Section Divider --- */
        .section-divider {
            border: none;
            border-top: 2px solid #29538dff;
            margin: 20px 0;
            opacity: 0.8;
            width: 100%;
        }

        .btn-counter-bed {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1.5px solid var(--primary);
            background: transparent;
            color: var(--primary);
            font-size: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s;
            font-weight: 700;
        }

        .btn-counter-bed:hover {
            background: var(--primary);
            color: white;
        }

        .btn-counter-bed:disabled {
            border-color: var(--border);
            color: var(--text-muted);
            cursor: not-allowed;
            background: transparent;
        }

        .bedroom-row {
            border-bottom: 1px dashed var(--border);
            padding: 20px 0;
            transition: all 0.3s ease;
        }

        .bedroom-row:last-child {
            border-bottom: none;
        }

        .bedroom-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .bedroom-title-bold {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 0 0 4px 0;
        }

        .bedroom-summary-text {
            font-size: 0.88rem;
            color: var(--text-muted);
            margin: 0;
            font-weight: 500;
        }

        .btn-edit-beds {
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

        .btn-edit-beds:hover {
            background: var(--primary);
            color: white;
        }

        .edit-beds-panel {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            margin-top: 15px;
            display: none;
            flex-direction: column;
            gap: 12px;
        }

        .bed-counter-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
        }

        .bed-counter-row:last-child {
            border-bottom: none;
        }

        .bed-info-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .bed-icon-img {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }

        .bed-name-text {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
        }

        .bed-count-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .bed-count-value {
            font-size: 15px;
            font-weight: 700;
            width: 20px;
            text-align: center;
        }

        .dropdown-add-bed-wrap {
            margin-top: 15px;
            border-top: 1px solid var(--border);
            padding-top: 15px;
        }

        .select-add-bed {
            width: 100%;
            height: 48px;
            border-radius: 12px;
            border: 1.5px solid var(--border);
            background: var(--card-bg);
            color: var(--text-main);
            padding: 0 16px;
            font-size: 14px;
            font-weight: 600;
            outline: none;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .select-add-bed:focus {
            border-color: var(--primary);
        }
    </style>
@endsection

@section('host-content')
    <h1 class="host-title">What amenities & spaces do you offer?</h1>
    <p class="host-subtitle">Detail your property's amenities, sleeping arrangements, and extra services.</p>

    <div class="mb-5">
        <h3 class="fw-bold h5 mb-3" style="margin-top: -2px;;">Essentials & Features</h3>
        <div class="amenities-grid">
            @foreach($amenities as $amenity)
                @php $isSelected = $room->amenities->contains('id', $amenity->id); @endphp
                <label class="amenity-card-airbnb {{ $isSelected ? 'selected' : '' }}" id="card_amenity_{{ $amenity->id }}">
                    <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" {{ $isSelected ? 'checked' : '' }}
                        onchange="toggleAmenity(this)">
                    @if($amenity->image)
                        <img src="{{ Storage::url($amenity->image) }}" width="24" height="24" style="object-fit: contain;">
                    @else
                        <i class="fas fa-check text-muted"></i>
                    @endif
                    <span class="small">{{ $amenity->name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <hr class="section-divider">

    <!-- Sleeping Arrangements Section -->
    <div class="mb-5">
        <h3 class="fw-bold h5 mb-2">Sleeping Arrangements</h3>
        <p class="text-muted small mb-4">Specify bedrooms and the counts/types of beds in each.</p>

        <!-- Bedroom Counter Card (Aligned layout matching enhancement cards) -->
        <div class="enhancement-row-card"
            style="display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div class="bed-icon-wrapper"
                    style="width: 42px; height: 42px; border-radius: 50%; background: rgba(59, 130, 246, 0.08); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                    <i class="fas fa-bed" style="transform: translateY(-1.5px);"></i>
                </div>
                <div>
                    <h4 class="bedroom-title-bold"
                        style="font-size: 1.02rem; font-weight: 700; margin: 0; color: var(--text-main); line-height: 1.2;">
                        Number of Bedrooms
                    </h4>
                    <p class="text-muted small mb-0" style="margin-top: 4px; line-height: 1.2;">How many bedrooms does this
                        room have?</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 12px; margin-left: auto;">
                <button type="button" class="btn-counter-bed" onclick="adjustBedrooms(-1)">−</button>
                <span class="fw-bold" style="font-size: 16px; min-width: 20px; text-align: center; color: var(--text-main);"
                    id="bedrooms_count_display">{{ $room->bedrooms_count ?: 1 }}</span>
                <button type="button" class="btn-counter-bed" onclick="adjustBedrooms(1)">+</button>
            </div>
        </div>

        <div class="bedrooms-list" id="bedrooms_container">
            <!-- Populated dynamically via JavaScript -->
        </div>
    </div>

    <hr class="section-divider">

    <div class="mb-5">
        <h3 class="fw-bold h5 mb-2">Food & Services (Optional)</h3>
        <p class="text-muted small mb-4">Offer extra services like meals to your guests.</p>

        @foreach(['Breakfast', 'Lunch', 'Dinner'] as $type)
            @php 
                $items = $room->enhancements->where('type', $type)->values(); 
                $isPerGuest = $items->where('is_per_guest', true)->count() > 0;
                $perGuestPrice = $isPerGuest ? $items->where('is_per_guest', true)->first()->price : '';
                $nonPerGuestItems = $items->where('is_per_guest', false)->values();
            @endphp
            <div class="enhancement-row-card">
                <div class="enhancement-card-header">
                    <h3>{{ $type }} Service</h3>
                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                        <!-- Price per Guest checkbox toggle option -->
                        <label class="price-per-guest-toggle" style="display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 600; cursor: pointer; color: var(--text-main); user-select: none;">
                            <input type="checkbox" class="is-per-guest-checkbox" id="per_guest_chk_{{ $type }}" data-type="{{ $type }}" {{ $isPerGuest ? 'checked' : '' }} onchange="togglePricePerGuest('{{ $type }}', this)" style="width: 16px; height: 16px; accent-color: var(--primary); cursor: pointer;">
                            <span>Price per guest</span>
                        </label>

                        <!-- Input field for Price per Guest (shown when checked) -->
                        <div class="price-per-guest-input-wrap" id="per_guest_input_wrap_{{ $type }}" style="display: {{ $isPerGuest ? 'flex' : 'none' }}; align-items: center; gap: 8px;">
                            <div class="modal-input-price-wrapper" style="width: 130px; margin-bottom: 0; position: relative;">
                                <input type="number" class="modal-input per-guest-price-input" id="per_guest_price_{{ $type }}" value="{{ $perGuestPrice }}" placeholder="Price" min="0" onblur="savePricePerGuest('{{ $type }}')" style="height: 38px; padding-left: 24px; font-size: 13.5px; font-weight: 600; border-radius: 8px;">
                            </div>
                        </div>

                        <!-- Setup Menu Button (hidden when checked) -->
                        <button class="btn-menu-setup" id="btn_menu_setup_{{ $type }}" onclick="openMenuModal('{{ $type }}')" style="display: {{ $isPerGuest ? 'none' : 'inline-flex' }}; padding: 6px 16px; font-size: 12.5px;">
                            <i class="fas {{ ($nonPerGuestItems->count() > 0) ? 'fa-edit' : 'fa-plus' }}" style="font-size:11px;"></i>
                            <span>{{ ($nonPerGuestItems->count() > 0) ? 'Edit Menu' : 'Setup Menu' }}</span>
                        </button>
                    </div>
                </div>

                <div class="food-items-grid" id="grid_{{ $type }}" style="display: {{ ($nonPerGuestItems->count() > 0 && !$isPerGuest) ? 'grid' : 'none' }}">
                    @foreach($nonPerGuestItems as $index => $item)
                        <div class="food-item-card {{ $index >= 4 ? 'hidden-item hidden-item-' . $type : '' }}">
                            <div class="item-left">
                                <i class="fas fa-utensils text-muted" style="font-size:11px;flex-shrink:0;"></i>
                                <span class="item-name" title="{{ $item->item_name }}">{{ $item->item_name }}</span>
                            </div>
                            <span class="item-price">₹{{ number_format($item->price, 0) }}</span>
                        </div>
                    @endforeach
                </div>
                
                <button class="show-more-btn" id="btn_{{ $type }}" onclick="toggleShowMore('{{ $type }}')" style="display: {{ ($nonPerGuestItems->count() > 4 && !$isPerGuest) ? 'inline-flex' : 'none' }}">
                    <span>Show more ({{ $nonPerGuestItems->count() - 4 }} more)</span>
                    <i class="fas fa-chevron-down small"></i>
                </button>
            </div>
        @endforeach
    </div>

    <div class="host-actions">
        <a href="{{ route('host.step', ['room' => $room->id, 'step' => 3]) }}" class="btn-prev">Back</a>
        <a href="{{ route('host.step', ['room' => $room->id, 'step' => 5]) }}" class="btn-next">
            <span class="btn-text">Save & Next</span>
            <div class="btn-spinner"></div>
        </a>
    </div>

    <!-- Menu Management Modal -->
    <div class="modal-overlay" id="menuModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 id="modalTitle">Edit Menu</h3>
                <button class="modal-btn-close" onclick="closeMenuModal()" aria-label="Close modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="modalItemsContainer">
                    <!-- Dynamic rows here -->
                </div>
                <button type="button" class="btn-add-item" onclick="addModalItemRow()">
                    <i class="fas fa-plus"></i> Add Menu Item
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeMenuModal()">Cancel</button>
                <button type="button" class="btn-modal-save" onclick="saveModalMenu()">Save Menu</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let currentModalType = '';

        function toggleAmenity(checkbox) {
            const card = document.getElementById('card_amenity_' + checkbox.value);
            checkbox.checked ? card.classList.add('selected') : card.classList.remove('selected');
            saveAmenities();
        }

        function saveAmenities() {
            showToast('saving', 'Saving...');
            const amenities = Array.from(document.querySelectorAll('input[name="amenities[]"]:checked')).map(cb => cb.value);

            fetch(`/host/{{ $room->id }}/amenities`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({
                    amenities,
                    step: 4
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', 'Saved');
                        if (data.step_valid !== null) updateStepperStatus(data.step, data.step_valid);
                    }
                })
                .catch(() => showToast('error', 'Error saving amenities'));
        }

        function openMenuModal(type) {
            currentModalType = type;
            document.getElementById('modalTitle').innerText = `Manage ${type} Menu`;
            document.getElementById('modalItemsContainer').innerHTML = '';

            // Fetch existing items for this type
            const existingItems = @json($room->enhancements);
            const filtered = existingItems.filter(i => i.type === type);

            if (filtered.length === 0) {
                addModalItemRow();
            } else {
                filtered.forEach(item => {
                    addModalItemRow(item.item_name, item.price);
                });
            }

            document.getElementById('menuModal').style.display = 'flex';
        }

        function closeMenuModal() {
            document.getElementById('menuModal').style.display = 'none';
        }

        function addModalItemRow(name = '', price = '') {
            const container = document.getElementById('modalItemsContainer');
            const rowId = 'row_' + Date.now() + Math.random().toString(36).substr(2, 5);
            const html = `
                        <div class="dynamic-item-row" id="${rowId}">
                            <div class="modal-input-wrapper">
                                <input type="text" class="modal-input modal-input-name" value="${name}" placeholder="Item Name" required>
                            </div>
                            <div class="modal-input-price-wrapper">
                                <input type="number" class="modal-input modal-input-price" value="${price}" placeholder="Price" min="0">
                            </div>
                            <button type="button" class="btn-remove-item" onclick="document.getElementById('${rowId}').remove()" title="Remove item">
                                <i class="fas fa-trash" style="color:red;"></i>
                            </button>
                        </div>
                    `;
            container.insertAdjacentHTML('beforeend', html);
        }

        function saveModalMenu() {
            const rows = document.querySelectorAll('.dynamic-item-row');
            const items = [];
            rows.forEach(row => {
                const name = row.querySelector('.modal-input-name').value;
                const price = row.querySelector('.modal-input-price').value;
                if (name) {
                    items.push({ item_name: name, price: price });
                }
            });

            showToast('saving', 'Saving menu...');
            fetch(`/host/{{ $room->id }}/enhancements-bulk`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({
                    type: currentModalType,
                    items: items
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', 'Menu saved');
                        location.reload();
                    }
                })
                .catch(() => showToast('error', 'Error saving menu'));
        }

        function toggleShowMore(type) {
            const hiddenItems = document.querySelectorAll('.hidden-item-' + type);
            const btn = document.getElementById('btn_' + type);
            const span = btn.querySelector('span');
            const icon = btn.querySelector('i');
            const isShowing = span.innerText.includes('Show less');

            hiddenItems.forEach(item => {
                if (isShowing) {
                    item.classList.add('hidden-item');
                } else {
                    item.classList.remove('hidden-item');
                }
            });

            span.innerText = isShowing ? `Show more (${hiddenItems.length} more)` : 'Show less';
            icon.classList.toggle('fa-chevron-down', isShowing);
            icon.classList.toggle('fa-chevron-up', !isShowing);
        }

        // --- Sleeping Arrangements Scripting ---
        const allBedTypes = @json($roomBeds);
        let dbAllocations = @json($room->bedroomBeds);

        let allocations = {};
        dbAllocations.forEach(alloc => {
            if (!allocations[alloc.bedroom_index]) {
                allocations[alloc.bedroom_index] = {};
            }
            allocations[alloc.bedroom_index][alloc.room_bed_id] = alloc.count;
        });

        let bedroomsCount = {{ $room->bedrooms_count ?: 1 }};

        function renderBedrooms() {
            const container = document.getElementById('bedrooms_container');
            container.innerHTML = '';

            for (let i = 1; i <= bedroomsCount; i++) {
                const bedCounts = allocations[i] || {};

                let totalBeds = 0;
                let summaryParts = [];

                Object.keys(bedCounts).forEach(bedId => {
                    const count = bedCounts[bedId];
                    if (count > 0) {
                        totalBeds += count;
                        const bedType = allBedTypes.find(b => b.id == bedId);
                        if (bedType) {
                            summaryParts.push(`${count} ${bedType.name}`);
                        }
                    }
                });

                const summaryText = summaryParts.length > 0
                    ? summaryParts.join(', ')
                    : 'No beds added yet';

                const editButtonText = totalBeds > 0 ? 'Edit Beds' : 'Add Beds';

                let selectOptionsHtml = '<option value="" disabled selected>Select another type of bed...</option>';
                let hasOptions = false;
                allBedTypes.forEach(bedType => {
                    if (!bedCounts[bedType.id] || bedCounts[bedType.id] === 0) {
                        selectOptionsHtml += `<option value="${bedType.id}">${bedType.name}</option>`;
                        hasOptions = true;
                    }
                });

                let bedRowsHtml = '';
                allBedTypes.forEach(bedType => {
                    const count = bedCounts[bedType.id] || 0;
                    if (count > 0) {
                        const iconPath = bedType.image ? `/storage/${bedType.image}` : '';
                        const iconHtml = iconPath
                            ? `<img src="${iconPath}" class="bed-icon-img" alt="${bedType.name}">`
                            : `<i class="fas fa-bed text-muted" style="font-size:18px;"></i>`;

                        bedRowsHtml += `
                                                <div class="bed-counter-row">
                                                    <div class="bed-info-left">
                                                        ${iconHtml}
                                                        <span class="bed-name-text">${bedType.name}</span>
                                                    </div>
                                                    <div class="bed-count-actions">
                                                        <button type="button" class="btn-counter-bed" onclick="updateBedCount(${i}, ${bedType.id}, -1)">−</button>
                                                        <span class="bed-count-value">${count}</span>
                                                        <button type="button" class="btn-counter-bed" onclick="updateBedCount(${i}, ${bedType.id}, 1)">+</button>
                                                    </div>
                                                </div>
                                            `;
                    }
                });

                const bedroomHtml = `
                                        <div class="enhancement-row-card" id="bedroom_row_${i}" style="margin-bottom: 20px;">
                                            <div class="bedroom-header-row" style="display: flex; justify-content: space-between; align-items: center; gap: 12px; width: 100%;">
                                                <div>
                                                    <h4 class="bedroom-title-bold" style="font-size: 1.05rem; font-weight: 700; color: var(--text-main); margin: 0 0 4px 0;">Bedroom ${i}</h4>
                                                    <p class="bedroom-summary-text" id="bedroom_summary_text_${i}">
                                                        <strong class="text-primary">${totalBeds} Bed${totalBeds === 1 ? '' : 's'}</strong> &nbsp;•&nbsp; 
                                                        <span>${summaryText}</span>
                                                    </p>
                                                </div>
                                                <button type="button" class="btn-edit-beds" id="btn_edit_beds_${i}" onclick="toggleEditBedPanel(${i})" style="display: inline-flex; align-items: center; gap: 6px;">
                                                    <i class="fas fa-edit" style="font-size: 11px;"></i>
                                                    <span>${editButtonText}</span>
                                                </button>
                                            </div>

                                            <div class="edit-beds-panel" id="edit_beds_panel_${i}" style="background: var(--bg-primary); border: 1px solid var(--border); border-radius: 12px; padding: 16px; margin-top: 15px; display: none; flex-direction: column; gap: 12px;">
                                                <div class="active-bed-rows-container">
                                                    ${bedRowsHtml || '<p class="text-muted small text-center my-3">No bed types selected. Choose one below to start.</p>'}
                                                </div>

                                                ${hasOptions ? `
                                                    <div class="dropdown-add-bed-wrap">
                                                        <select class="select-add-bed" onchange="addBedToBedroom(${i}, this)">
                                                            ${selectOptionsHtml}
                                                        </select>
                                                    </div>
                                                ` : ''}
                                            </div>
                                        </div>
                                    `;

                container.insertAdjacentHTML('beforeend', bedroomHtml);
            }
        }

        function toggleEditBedPanel(bedroomIndex) {
            const panel = document.getElementById(`edit_beds_panel_${bedroomIndex}`);
            const btn = document.getElementById(`btn_edit_beds_${bedroomIndex}`);
            const span = btn.querySelector('span');

            if (panel.style.display === 'flex') {
                panel.style.display = 'none';
                const bedCounts = allocations[bedroomIndex] || {};
                let total = 0;
                Object.values(bedCounts).forEach(c => total += c);
                span.innerText = total > 0 ? 'Edit Beds' : 'Add Beds';
            } else {
                panel.style.display = 'flex';
                span.innerText = 'Done';
            }
        }

        function adjustBedrooms(delta) {
            let newCount = bedroomsCount + delta;
            if (newCount < 1) return;

            bedroomsCount = newCount;
            document.getElementById('bedrooms_count_display').innerText = bedroomsCount;

            Object.keys(allocations).forEach(idx => {
                if (parseInt(idx) > bedroomsCount) {
                    delete allocations[idx];
                }
            });

            renderBedrooms();
            saveBedroomsState();
        }

        function updateBedCount(bedroomIndex, roomBedId, delta) {
            if (!allocations[bedroomIndex]) {
                allocations[bedroomIndex] = {};
            }

            let current = allocations[bedroomIndex][roomBedId] || 0;
            let next = current + delta;
            if (next < 0) next = 0;

            allocations[bedroomIndex][roomBedId] = next;

            const panelOpen = document.getElementById(`edit_beds_panel_${bedroomIndex}`).style.display === 'flex';
            renderBedrooms();
            if (panelOpen) {
                document.getElementById(`edit_beds_panel_${bedroomIndex}`).style.display = 'flex';
                document.getElementById(`btn_edit_beds_${bedroomIndex}`).querySelector('span').innerText = 'Done';
            }

            saveBedroomsState();
        }

        function addBedToBedroom(bedroomIndex, selectElement) {
            const bedId = selectElement.value;
            if (!bedId) return;

            if (!allocations[bedroomIndex]) {
                allocations[bedroomIndex] = {};
            }

            allocations[bedroomIndex][bedId] = 1;

            renderBedrooms();
            document.getElementById(`edit_beds_panel_${bedroomIndex}`).style.display = 'flex';
            document.getElementById(`btn_edit_beds_${bedroomIndex}`).querySelector('span').innerText = 'Done';

            saveBedroomsState();
        }

        function saveBedroomsState() {
            showToast('saving', 'Saving arrangements...');

            let flatAllocations = [];
            Object.keys(allocations).forEach(bedroomIdx => {
                const bedCounts = allocations[bedroomIdx];
                Object.keys(bedCounts).forEach(bedId => {
                    const count = bedCounts[bedId];
                    if (count > 0) {
                        flatAllocations.push({
                            bedroom_index: parseInt(bedroomIdx),
                            room_bed_id: parseInt(bedId),
                            count: count
                        });
                    }
                });
            });

            fetch(`/host/{{ $room->id }}/bedrooms`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({
                    bedrooms_count: bedroomsCount,
                    allocations: flatAllocations,
                    step: 4
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', 'Saved arrangements');
                        if (data.step_valid !== null) updateStepperStatus(data.step, data.step_valid);
                    }
                })
                .catch(() => showToast('error', 'Error saving sleeping arrangements'));
        }

        function togglePricePerGuest(type, checkbox) {
            const wrap = document.getElementById('per_guest_input_wrap_' + type);
            const setupBtn = document.getElementById('btn_menu_setup_' + type);
            const grid = document.getElementById('grid_' + type);
            const btnMore = document.getElementById('btn_' + type);
            const input = document.getElementById('per_guest_price_' + type);

            if (checkbox.checked) {
                wrap.style.display = 'flex';
                setupBtn.style.display = 'none';
                if (grid) grid.style.display = 'none';
                if (btnMore) btnMore.style.display = 'none';
                input.focus();
            } else {
                wrap.style.display = 'none';
                setupBtn.style.display = 'inline-flex';
                
                if (grid && grid.children.length > 0) {
                    grid.style.display = 'grid';
                    if (btnMore) btnMore.style.display = 'inline-flex';
                }
                
                // Clear any stored per guest enhancement for this type on backend
                savePricePerGuest(type, true);
            }
        }

        function savePricePerGuest(type, clear = false) {
            const checkbox = document.getElementById('per_guest_chk_' + type);
            const priceInput = document.getElementById('per_guest_price_' + type);
            const priceVal = clear ? null : priceInput.value;

            // Only save if the checkbox is checked, or if we are explicitly clearing
            if (!checkbox.checked && !clear) return;

            showToast('saving', 'Saving options...');
            fetch(`/host/{{ $room->id }}/enhancements-bulk`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({
                    type: type,
                    is_per_guest: checkbox.checked,
                    price: priceVal
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', clear ? 'Removed price per guest' : 'Saved price per guest');
                    }
                })
                .catch(() => showToast('error', 'Error saving option'));
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderBedrooms();
        });
    </script>
@endsection