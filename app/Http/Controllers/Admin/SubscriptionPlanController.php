<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::latest()->get();
        return view('admin.subscription_plans.index', compact('plans'));
    }

    public function create()
    {
        $currencies = \App\Models\Currency::all();
        return view('admin.subscription_plans.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'duration_days' => 'required|integer|min:1',
            'hosting_allowed' => 'nullable',
            'cancellations_allowed' => 'required|integer|min:0',
            'cancellation_fee_reduction' => 'required|numeric|min:0|max:100',
        ]);

        if (empty($data['hosting_allowed'])) {
            $data['hosting_allowed'] = null;
        }
        
        $data['is_active'] = $request->has('is_active');

        SubscriptionPlan::create($data);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan created successfully.');
    }

    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        $currencies = \App\Models\Currency::all();
        return view('admin.subscription_plans.edit', compact('subscriptionPlan', 'currencies'));
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'duration_days' => 'required|integer|min:1',
            'hosting_allowed' => 'nullable',
            'cancellations_allowed' => 'required|integer|min:0',
            'cancellation_fee_reduction' => 'required|numeric|min:0|max:100',
        ]);

        if (empty($data['hosting_allowed'])) {
            $data['hosting_allowed'] = null;
        }

        $data['is_active'] = $request->has('is_active');

        $subscriptionPlan->update($data);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->delete();
        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan deleted successfully.');
    }
}
