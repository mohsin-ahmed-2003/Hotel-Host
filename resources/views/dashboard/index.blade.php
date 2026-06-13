@extends('layouts.app')

@section('title', 'Dashboard')

@section('styles')
<style>
    .dashboard-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 24px;
        min-height: 60vh;
    }
    .page-title {
        font-size: 32px;
        font-weight: 800;
        color: var(--body-text);
        margin-bottom: 24px;
        letter-spacing: -0.5px;
    }
</style>
@endsection

@section('content')
<div class="dashboard-container">
    @if($isHost)
        @include('dashboard.host')
    @else
        @include('dashboard.guest')
    @endif
</div>
@endsection
