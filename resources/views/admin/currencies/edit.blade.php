@extends('admin.layout')

@section('title', 'Edit Currency')
@section('page-title', 'Edit Currency')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <span class="card-title">Edit Currency: {{ $currency->currency_code }}</span>
        <a href="{{ route('admin.currencies.index') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.currencies.update', $currency) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 16px;">
                <label style="display:block; margin-bottom:8px; font-weight:600;">Currency Code (e.g. USD, EUR)</label>
                <input type="text" name="currency_code" value="{{ old('currency_code', $currency->currency_code) }}" required style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg); color:var(--text);">
                @error('currency_code') <span style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display:block; margin-bottom:8px; font-weight:600;">Currency Name (e.g. US Dollar)</label>
                <input type="text" name="currency_name" value="{{ old('currency_name', $currency->currency_name) }}" required style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg); color:var(--text);">
                @error('currency_name') <span style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
            </div>
            
            <div style="margin-bottom: 24px;">
                <label style="display:block; margin-bottom:8px; font-weight:600;">Symbol (e.g. $, €)</label>
                <input type="text" name="symbol" value="{{ old('symbol', $currency->symbol) }}" required style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg); color:var(--text);">
                @error('symbol') <span style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-warning" style="width:100%; justify-content:center; padding:12px; font-size:15px; color:#fff;">Update Currency</button>
        </form>
    </div>
</div>
@endsection
