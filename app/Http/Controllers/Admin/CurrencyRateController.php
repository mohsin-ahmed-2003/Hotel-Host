<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CurrencyRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CurrencyRateController extends Controller
{
    public function index(Request $request)
    {
        $query = CurrencyRate::with('currency');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('target_currency', 'like', "%{$search}%")
                  ->orWhereHas('currency', function($q) use ($search) {
                      $q->where('currency_name', 'like', "%{$search}%");
                  });
        }

        // We only want to show the latest rates mostly, or just order by date descending
        $query->orderBy('rate_date', 'desc')->orderBy('target_currency', 'asc');

        $rates = $query->paginate(30);

        return view('admin.currency_rates.index', compact('rates'));
    }

    public function sync()
    {
        try {
            Artisan::call('currency:sync');
            return redirect()->route('admin.currency-rates.index')->with('success', 'Currency rates synced successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.currency-rates.index')->with('error', 'Failed to sync currency rates: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $currencies = \App\Models\Currency::all();
        return view('admin.currency_rates.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'target_currency' => 'required|string|max:10',
            'rate' => 'required|numeric|min:0',
            'rate_date' => 'required|date',
        ]);

        CurrencyRate::updateOrCreate(
            [
                'base_currency' => 'USD',
                'target_currency' => strtoupper($request->target_currency),
                'rate_date' => $request->rate_date,
            ],
            [
                'rate' => $request->rate,
            ]
        );

        return redirect()->route('admin.currency-rates.index')->with('success', 'Currency rate saved successfully.');
    }

    public function edit(CurrencyRate $currencyRate)
    {
        $currencies = \App\Models\Currency::all();
        return view('admin.currency_rates.edit', compact('currencyRate', 'currencies'));
    }

    public function update(Request $request, CurrencyRate $currencyRate)
    {
        $request->validate([
            'target_currency' => 'required|string|max:10',
            'rate' => 'required|numeric|min:0',
            'rate_date' => 'required|date',
        ]);

        $currencyRate->update([
            'target_currency' => strtoupper($request->target_currency),
            'rate' => $request->rate,
            'rate_date' => $request->rate_date,
        ]);

        return redirect()->route('admin.currency-rates.index')->with('success', 'Currency rate updated successfully.');
    }

    public function destroy(CurrencyRate $currencyRate)
    {
        $currencyRate->delete();
        return redirect()->route('admin.currency-rates.index')->with('success', 'Currency rate deleted successfully.');
    }
}
