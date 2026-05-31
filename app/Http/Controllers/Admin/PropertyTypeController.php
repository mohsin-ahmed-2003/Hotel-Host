<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use Illuminate\Http\Request;

class PropertyTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = PropertyType::query();
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('id', $request->search);
        }

        $propertyTypes = $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'))->paginate(10);
        return view('admin.property_types.index', compact('propertyTypes'));
    }

    public function create()
    {
        return view('admin.property_types.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'status' => 'boolean']);
        PropertyType::create(['name' => $request->name, 'status' => $request->status ?? 1]);
        return redirect()->route('admin.property-types.index')->with('success', 'Property Type created.');
    }

    public function edit(PropertyType $propertyType)
    {
        return view('admin.property_types.edit', compact('propertyType'));
    }

    public function update(Request $request, PropertyType $propertyType)
    {
        $request->validate(['name' => 'required|string|max:255', 'status' => 'boolean']);
        $propertyType->update(['name' => $request->name, 'status' => $request->has('status') ? 1 : 0]);
        return redirect()->route('admin.property-types.index')->with('success', 'Property Type updated.');
    }

    public function destroy(PropertyType $propertyType)
    {
        $propertyType->delete();
        return redirect()->route('admin.property-types.index')->with('success', 'Property Type deleted.');
    }
}
