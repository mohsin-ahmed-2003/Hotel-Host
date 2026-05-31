@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8" style="max-width: 1200px; margin: 40px auto; min-height: 50vh;">
    <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 20px;">Search Results</h1>
    
    @if(request('location'))
        <p style="margin-bottom: 20px; color: #64748b; font-size: 16px;">
            Showing results for <strong>{{ request('location') }}</strong> 
            @if(request('checkin') && request('checkout'))
                from <strong>{{ request('checkin') }}</strong> to <strong>{{ request('checkout') }}</strong>
            @endif
            for <strong>{{ request('guests') }} guest(s)</strong>.
        </p>
    @endif

    <div style="background: var(--card-bg, #fff); padding: 40px; border-radius: 12px; border: 1px solid var(--border, #e2e8f0); text-align: center;">
        <h3 style="font-size: 20px; color: var(--body-text); margin-bottom: 10px;">Search Implementation Pending</h3>
        <p style="color: var(--body-muted);">The search filters and advanced results layout will be built out shortly.</p>
    </div>
</div>
@endsection
