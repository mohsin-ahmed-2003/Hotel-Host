<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {
        $query = Currency::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('currency_code', 'like', "%{$search}%")
                  ->orWhere('currency_name', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%");
        }

        $currencies = $query->paginate(20);

        return view('admin.currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('admin.currencies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'currency_code' => 'required|string|max:10|unique:currencies,currency_code',
            'currency_name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
        ]);

        Currency::create($request->all());

        return redirect()->route('currencies.index')->with('success', 'Currency created successfully.');
    }

    public function edit(Currency $currency)
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'currency_code' => 'required|string|max:10|unique:currencies,currency_code,' . $currency->id,
            'currency_name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
        ]);

        $currency->update($request->all());

        return redirect()->route('currencies.index')->with('success', 'Currency updated successfully.');
    }

    public function destroy(Currency $currency)
    {
        $currency->delete();
        return redirect()->route('currencies.index')->with('success', 'Currency deleted successfully.');
    }
}
