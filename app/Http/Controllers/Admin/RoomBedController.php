<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomBed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomBedController extends Controller
{
    public function index(Request $request)
    {
        $query = RoomBed::query();
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('id', $request->search);
        }

        $roomBeds = $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'))->paginate(10);
        return view('admin.room_beds.index', compact('roomBeds'));
    }

    public function create()
    {
        return view('admin.room_beds.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->only(['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('room_beds', 'public');
        }

        RoomBed::create($data);
        return redirect()->route('admin.room-beds.index')->with('success', 'Bed Arrangement created successfully.');
    }

    public function edit($id)
    {
        $roomBed = RoomBed::findOrFail($id);
        return view('admin.room_beds.edit', compact('roomBed'));
    }

    public function update(Request $request, $id)
    {
        $roomBed = RoomBed::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->only(['name']);

        if ($request->hasFile('image')) {
            if ($roomBed->image) Storage::disk('public')->delete($roomBed->image);
            $data['image'] = $request->file('image')->store('room_beds', 'public');
        }

        $roomBed->update($data);
        return redirect()->route('admin.room-beds.index')->with('success', 'Bed Arrangement updated successfully.');
    }

    public function destroy($id)
    {
        $roomBed = RoomBed::findOrFail($id);
        if ($roomBed->image) Storage::disk('public')->delete($roomBed->image);
        $roomBed->delete();
        return redirect()->route('admin.room-beds.index')->with('success', 'Bed Arrangement deleted successfully.');
    }
}
