<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $query = Country::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('country_name', 'like', "%{$search}%")
                  ->orWhere('short_name', 'like', "%{$search}%")
                  ->orWhere('phone_code', 'like', "%{$search}%")
                  ->orWhere('currency', 'like', "%{$search}%");
        }

        $countries = $query->paginate(20);

        return view('admin.countries.index', compact('countries'));
    }

    public function create()
    {
        return view('admin.countries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'country_name' => 'required|string|max:255',
            'short_name' => 'required|string|max:10',
            'phone_code' => 'required|string|max:10',
            'currency' => 'required|string|max:10',
        ]);

        Country::create($request->all());

        return redirect()->route('countries.index')->with('success', 'Country created successfully.');
    }

    public function edit(Country $country)
    {
        return view('admin.countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $request->validate([
            'country_name' => 'required|string|max:255',
            'short_name' => 'required|string|max:10',
            'phone_code' => 'required|string|max:10',
            'currency' => 'required|string|max:10',
        ]);

        $country->update($request->all());

        return redirect()->route('countries.index')->with('success', 'Country updated successfully.');
    }

    public function destroy(Country $country)
    {
        $country->delete();
        return redirect()->route('countries.index')->with('success', 'Country deleted successfully.');
    }
}
