@extends('admin.layout')

@section('title', 'Edit Subscription Plan')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-1"
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
            <h2 class="fw-bold m-0" style="font-weight: bold; margin: 0; font-size: 28px; color: #1e293b;">Edit Subscription
                Plan</h2>
            <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-outline-secondary btn-sm"
                style="padding: 7px 34px 10px; background-color: red; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 10px;">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
        </div>
        <p class="text-muted small mb-4" style="color: #6c757d; font-size: 15px; margin-bottom: 1.5rem;">Update limits and
            pricing.</p>

        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin:0; padding-left:20px;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.subscription-plans.update', $subscriptionPlan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label required">Plan Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $subscriptionPlan->name) }}" required>
                        </div>

                        <div class="form-group" style="grid-column: span 1;">
                            <div style="display: flex; gap: 20px;">
                                <div style="flex: 1;">
                                    <label class="form-label required">Currency</label>
                                    <select name="currency" class="form-control" required>
                                        @foreach($currencies as $cur)
                                            <option value="{{ $cur->currency_code }}" {{ $subscriptionPlan->currency === $cur->currency_code ? 'selected' : '' }}>
                                                {{ $cur->currency_code }} ({{ $cur->symbol }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div style="flex: 2;">
                                    <label class="form-label required">Price</label>
                                    <input type="number" step="0.01" name="price" class="form-control"
                                        value="{{ old('price', $subscriptionPlan->price) }}" required>
                                </div>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Duration (Days)</label>
                            <input type="number" name="duration_days" class="form-control"
                                value="{{ old('duration_days', $subscriptionPlan->duration_days) }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Hosting Allowed (Stays)</label>
                            <select name="hosting_allowed" class="form-control">
                                <option value="" {{ empty($subscriptionPlan->hosting_allowed) ? 'selected' : '' }}>
                                    Unlimited</option>
                                <option value="1" {{ $subscriptionPlan->hosting_allowed == 1 ? 'selected' : '' }}>1</option>
                                <option value="3" {{ $subscriptionPlan->hosting_allowed == 3 ? 'selected' : '' }}>3</option>
                                <option value="5" {{ $subscriptionPlan->hosting_allowed == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ $subscriptionPlan->hosting_allowed == 10 ? 'selected' : '' }}>10
                                </option>
                                <option value="20" {{ $subscriptionPlan->hosting_allowed == 20 ? 'selected' : '' }}>20
                                </option>
                                <option value="50" {{ $subscriptionPlan->hosting_allowed == 50 ? 'selected' : '' }}>50
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Cancellations Allowed</label>
                            <input type="number" name="cancellations_allowed" class="form-control"
                                value="{{ old('cancellations_allowed', $subscriptionPlan->cancellations_allowed) }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Service Fee Reduction (%)</label>
                            <input type="number" step="0.01" name="cancellation_fee_reduction" class="form-control"
                                value="{{ old('cancellation_fee_reduction', $subscriptionPlan->cancellation_fee_reduction) }}"
                                required>
                        </div>
                        
                        <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ $subscriptionPlan->is_active ? 'checked' : '' }} style="width: 18px; height: 18px;">
                            <label for="is_active" class="form-label" style="margin-bottom: 0;">Active Plan</label>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary">Update Plan</button>
                    </div>
                </form>
            </div>
        </div>
@endsection