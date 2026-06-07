<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;

use App\Models\SiteSetting;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price', 'asc')->get();
        $activeSubscription = auth()->check() ? \App\Models\UserSubscription::where('user_id', auth()->id())->where('status', 'active')->first() : null;
        
        return view('subscriptions.index', compact('plans', 'activeSubscription'));
    }

    public function checkout(SubscriptionPlan $plan)
    {
        $payment_stripe = SiteSetting::get('stripe_enabled') === '1';
        $payment_paypal = SiteSetting::get('paypal_enabled') === '1';
        $payment_razorpay = SiteSetting::get('razorpay_enabled') === '1';
        $payment_easebuzz = SiteSetting::get('easebuzz_enabled') === '1';

        return view('subscriptions.checkout', compact(
            'plan',
            'payment_stripe',
            'payment_paypal',
            'payment_razorpay',
            'payment_easebuzz'
        ));
    }

    public function history()
    {
        $subscriptions = \App\Models\UserSubscription::where('user_id', auth()->id())->latest()->get();
        return view('subscriptions.history', compact('subscriptions'));
    }

    public function unsubscribe($id)
    {
        $subscription = \App\Models\UserSubscription::where('user_id', auth()->id())->findOrFail($id);
        
        if ($subscription->status === 'active') {
            $subscription->update(['status' => 'cancelled']);
            return back()->with('success', 'Your subscription has been successfully cancelled.');
        }

        return back()->with('error', 'Only active subscriptions can be cancelled.');
    }
}
