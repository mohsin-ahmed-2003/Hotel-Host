@extends('admin.layout')

@section('title', 'Edit Country')
@section('page-title', 'Edit Country')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <span class="card-title">Edit Country: {{ $country->country_name }}</span>
        <a href="{{ route('admin.countries.index') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.countries.update', $country) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 16px;">
                <label style="display:block; margin-bottom:8px; font-weight:600;">Country Name</label>
                <input type="text" name="country_name" value="{{ old('country_name', $country->country_name) }}" required style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg); color:var(--text);">
                @error('country_name') <span style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display:block; margin-bottom:8px; font-weight:600;">Short Name (e.g. IN, US)</label>
                <input type="text" name="short_name" value="{{ old('short_name', $country->short_name) }}" required style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg); color:var(--text);">
                @error('short_name') <span style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display:block; margin-bottom:8px; font-weight:600;">Phone Code (e.g. +91)</label>
                <input type="text" name="phone_code" value="{{ old('phone_code', $country->phone_code) }}" required style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg); color:var(--text);">
                @error('phone_code') <span style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
            </div>
            
            <div style="margin-bottom: 24px;">
                <label style="display:block; margin-bottom:8px; font-weight:600;">Currency (e.g. INR, USD)</label>
                <input type="text" name="currency" value="{{ old('currency', $country->currency) }}" required style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; background:var(--bg); color:var(--text);">
                @error('currency') <span style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-warning" style="width:100%; justify-content:center; padding:12px; font-size:15px; color:#fff;">Update Country</button>
        </form>
    </div>
</div>
@endsection
