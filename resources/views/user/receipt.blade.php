@extends('layouts.app')
@section('title', 'Receipt - ' . ($reservation->room->title ?? 'Reservation'))

@section('content')
    <style>
        .receipt-wrapper {
            background-color: #f8fafc;
            padding: 40px 20px;
            min-height: calc(100vh - 80px);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .receipt-container {
            max-width: 850px;
            margin: 0 auto;
        }

        .receipt-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-back {
            background: transparent;
            color: var(--body-text, #111827);
        }

        .btn-back:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        .btn-print {
            background: var(--body-text, #111827);
            color: white;
            border: none;
        }

        .btn-print:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .receipt-card {
            background: white;
            border: 1px solid var(--border, #e5e7eb);
            border-radius: 24px;
            padding: 50px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            position: relative;
            overflow: hidden;
        }

        /* Paid Stamp */
        .paid-stamp {
            position: absolute;
            top: 40px;
            right: 40px;
            font-size: 40px;
            font-weight: 900;
            color: rgba(34, 197, 94, 0.15);
            /* light green */
            text-transform: uppercase;
            letter-spacing: 4px;
            border: 4px solid rgba(34, 197, 94, 0.15);
            padding: 10px 20px;
            border-radius: 12px;
            transform: rotate(15deg);
            pointer-events: none;
            z-index: 1;
        }

        .receipt-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
            position: relative;
            z-index: 2;
        }

        .brand-logo {
            width: 48px;
            height: 48px;
            background: var(--primary, #2563eb);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .brand-text h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            color: var(--body-text, #111827);
        }

        .brand-text p {
            margin: 0;
            color: var(--body-muted, #6b7280);
            font-size: 14px;
        }

        .receipt-meta {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            background: #f8fafc;
            padding: 24px;
            border-radius: 16px;
            margin-bottom: 40px;
            position: relative;
            z-index: 2;
        }

        .meta-group h4 {
            margin: 0 0 6px 0;
            font-size: 11px;
            color: var(--body-muted, #64748b);
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .meta-group p {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: var(--body-text, #0f172a);
        }

        .receipt-details {
            margin-bottom: 40px;
            position: relative;
            z-index: 2;
        }

        .details-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--body-text, #0f172a);
            margin: 0 0 8px 0;
        }

        .details-subtitle {
            margin: 0;
            color: var(--body-muted, #64748b);
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }

        .invoice-table th {
            text-align: left;
            padding: 16px 0;
            border-bottom: 2px solid #e2e8f0;
            color: var(--body-muted, #64748b);
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .invoice-table td {
            padding: 20px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 15px;
            color: var(--body-text, #0f172a);
        }

        .invoice-table td.amount {
            text-align: right;
            font-weight: 600;
        }

        .invoice-table th.amount {
            text-align: right;
        }

        .total-section {
            display: flex;
            justify-content: flex-end;
            position: relative;
            z-index: 2;
        }

        .total-box {
            width: 300px;
            background: #f8fafc;
            padding: 24px;
            border-radius: 16px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 15px;
            color: var(--body-text, #334155);
        }

        .total-row.final {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 2px solid #e2e8f0;
            font-size: 20px;
            font-weight: 900;
            color: var(--primary, #2563eb);
        }

        .receipt-footer {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px dashed #cbd5e1;
            text-align: center;
            color: var(--body-muted, #64748b);
            font-size: 14px;
            position: relative;
            z-index: 2;
        }

        @media print {
            body {
                background: white !important;
            }

            header,
            footer,
            nav {
                display: none !important;
            }

            .receipt-wrapper {
                background: white;
                padding: 0;
                min-height: auto;
            }

            .btn-print,
            .btn-back,
            .receipt-header {
                display: none !important;
            }

            .receipt-container {
                margin: 0;
                padding: 0;
                max-width: 100%;
            }

            .receipt-card {
                box-shadow: none;
                border: none;
                padding: 0;
            }
        }

        @media (max-width: 768px) {
            .receipt-meta {
                grid-template-columns: 1fr 1fr;
            }

            .receipt-card {
                padding: 30px 20px;
            }

            .paid-stamp {
                font-size: 24px;
                right: 20px;
                top: 20px;
            }

            .total-box {
                width: 100%;
            }
        }
    </style>

    <div class="receipt-wrapper">
        <div class="receipt-container">

            <div class="receipt-header">
                <a href="{{ url()->previous() }}" class="btn-action btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <button class="btn-action btn-print" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Invoice
                </button>
            </div>

            <div class="receipt-card">

                @if($reservation->status === 'success' || $reservation->status === 'completed')
                    <div class="paid-stamp">PAID</div>
                @endif

                <div class="receipt-brand" style="justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="brand-logo">
                            <i class="fas fa-hotel"></i>
                        </div>
                        <div class="brand-text">
                            <h1>Payment Receipt</h1>
                            <p>Official Invoice & Confirmation</p>
                        </div>
                    </div>

                    <div style="text-align: right; display: flex; flex-direction: column;">
                        @if($siteSettings->get('site_logo'))
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($siteSettings->get('site_logo')) }}"
                                alt="{{ $siteSettings->get('site_name', 'Hotel Host') }}"
                                style="height: 40px; width: auto; object-fit: contain; margin-bottom: 6px;">
                            <h2 style="margin: 0; font-size: 15px; font-weight: 700; color: var(--body-text, #111827);">
                                {{ $siteSettings->get('site_name', 'Hotel Host') }}</h2>
                        @else
                            <h2 style="margin: 0; font-size: 24px; font-weight: 800; color: var(--body-text, #111827);">
                                {{ $siteSettings->get('site_name', 'Hotel Host') }}</h2>
                        @endif
                    </div>
                </div>

                <div class="receipt-meta">
                    <div class="meta-group">
                        <h4>Invoice No.</h4>
                        <p>{{ $reservation->transaction_id ?? 'INV-' . $reservation->id }}</p>
                    </div>
                    <div class="meta-group">
                        <h4>Date Issued</h4>
                        <p>{{ $reservation->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="meta-group">
                        <h4>Payment Method</h4>
                        <p>{{ $reservation->payment_type ?? 'N/A' }}</p>
                    </div>
                    <div class="meta-group">
                        <h4>Billed To</h4>
                        <p>{{ $reservation->user->name ?? 'Guest User' }}</p>
                    </div>
                </div>

                <div class="receipt-details">
                    <h3 class="details-title">Booking Details</h3>
                    <p class="details-subtitle">
                        <i class="fas fa-map-marker-alt"></i> {{ $reservation->room->title ?? 'Reservation' }}
                        <span style="margin: 0 8px;">|</span>
                        <i class="fas fa-calendar-alt"></i> {{ $reservation->checkin->format('M d, Y') }} -
                        {{ $reservation->checkout->format('M d, Y') }}
                        <span style="margin: 0 8px;">|</span>
                        <i class="fas fa-user"></i> {{ $reservation->guests }} Guest(s)
                    </p>
                </div>

                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="amount">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="font-weight: 600;">Base Accommodation</div>
                                <div style="font-size: 13px; color: #64748b; margin-top: 4px;">
                                    {{ $reservation->checkin->diffInDays($reservation->checkout) }} Nights Standard Rate
                                </div>
                            </td>
                            <td class="amount">
                                {{ $reservation->room->currency_symbol ?? '$' }}{{ number_format($reservation->base_amount, 2) }}
                            </td>
                        </tr>

                        @if($reservation->food_amount > 0)
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">Enhancements & Services</div>
                                    <div style="font-size: 13px; color: #64748b; margin-top: 4px;">Meals and additional add-ons
                                    </div>
                                </td>
                                <td class="amount">
                                    {{ $reservation->room->currency_symbol ?? '$' }}{{ number_format($reservation->food_amount, 2) }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <div class="total-section">
                    <div class="total-box">
                        <div class="total-row">
                            <span>Subtotal</span>
                            <span>{{ $reservation->room->currency_symbol ?? '$' }}{{ number_format($reservation->base_amount + $reservation->food_amount, 2) }}</span>
                        </div>

                        @if($reservation->service_fee > 0)
                            <div class="total-row">
                                <span>Service Fee</span>
                                <span>{{ $reservation->room->currency_symbol ?? '$' }}{{ number_format($reservation->service_fee, 2) }}</span>
                            </div>
                        @endif

                        @if($reservation->tax > 0)
                            <div class="total-row">
                                <span>Taxes</span>
                                <span>{{ $reservation->room->currency_symbol ?? '$' }}{{ number_format($reservation->tax, 2) }}</span>
                            </div>
                        @endif

                        @if($reservation->security_deposit > 0)
                            <div class="total-row">
                                <span>Security Deposit</span>
                                <span>{{ $reservation->room->currency_symbol ?? '$' }}{{ number_format($reservation->security_deposit, 2) }}</span>
                            </div>
                        @endif

                        <div class="total-row final">
                            <span>Total Paid</span>
                            <span>{{ $reservation->room->currency_symbol ?? '$' }}{{ number_format($reservation->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="receipt-footer">
                    <p style="margin: 0 0 4px 0;"><strong>Thank you for your business!</strong></p>
                    <p style="margin: 0;">If you have any questions regarding this invoice, please contact support.</p>
                </div>
            </div>
        </div>
    </div>
@endsection