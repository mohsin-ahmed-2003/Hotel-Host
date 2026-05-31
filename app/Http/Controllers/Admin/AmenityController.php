<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AmenityController extends Controller
{
    public function index(Request $request)
    {
        $query = Amenity::query();
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('id', $request->search);
        }

        $amenities = $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'))->paginate(10);
        return view('admin.amenities.index', compact('amenities'));
    }

    public function create()
    {
        return view('admin.amenities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->only(['name', 'description']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('amenities', 'public');
        }

        Amenity::create($data);
        return redirect()->route('admin.amenities.index')->with('success', 'Amenity created.');
    }

    public function edit(Amenity $amenity)
    {
        return view('admin.amenities.edit', compact('amenity'));
    }

    public function update(Request $request, Amenity $amenity)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->only(['name', 'description']);

        if ($request->hasFile('image')) {
            if ($amenity->image) Storage::disk('public')->delete($amenity->image);
            $data['image'] = $request->file('image')->store('amenities', 'public');
        }

        $amenity->update($data);
        return redirect()->route('admin.amenities.index')->with('success', 'Amenity updated.');
    }

    public function destroy(Amenity $amenity)
    {
        if ($amenity->image) Storage::disk('public')->delete($amenity->image);
        $amenity->delete();
        return redirect()->route('admin.amenities.index')->with('success', 'Amenity deleted.');
    }
}
