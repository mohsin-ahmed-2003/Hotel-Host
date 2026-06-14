@extends('admin.layout')

@section('title', 'Edit Currency Rate')
@section('page-title', 'Edit Currency Rate')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <span class="card-title">Edit Rate: {{ $currencyRate->target_currency }} (Base: USD)</span>
        <a href="{{ route('admin.currency-rates.index') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.currency-rates.update', $currencyRate) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 16px;">
                <label style="display:block; margin-bottom:8px; font-weight:600;">Target Currency</label>
                <select name="target_currency" required style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg); color:var(--text);">
                    <option value="">Select a currency</option>
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->currency_code }}" {{ old('target_currency', $currencyRate->target_currency) == $currency->currency_code ? 'selected' : '' }}>
                            {{ $currency->currency_code }} - {{ $currency->currency_name }} ({{ $currency->symbol }})
                        </option>
                    @endforeach
                </select>
                @error('target_currency') <span style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display:block; margin-bottom:8px; font-weight:600;">Rate (How much 1 USD equals)</label>
                <input type="number" step="0.00000001" name="rate" value="{{ old('rate', $currencyRate->rate) }}" required style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg); color:var(--text);">
                @error('rate') <span style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
            </div>
            
            <div style="margin-bottom: 24px;">
                <label style="display:block; margin-bottom:8px; font-weight:600;">Date</label>
                <input type="date" name="rate_date" value="{{ old('rate_date', $currencyRate->rate_date) }}" required style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg); color:var(--text);">
                @error('rate_date') <span style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-warning" style="width:100%; justify-content:center; padding:12px; font-size:15px; color:#fff;">Update Rate</button>
        </form>
    </div>
</div>
@endsection
