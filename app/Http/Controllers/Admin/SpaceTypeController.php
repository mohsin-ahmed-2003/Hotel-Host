<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpaceType;
use Illuminate\Http\Request;

class SpaceTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = SpaceType::query();
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('id', $request->search);
        }

        $spaceTypes = $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'))->paginate(10);
        return view('admin.space_types.index', compact('spaceTypes'));
    }

    public function create()
    {
        return view('admin.space_types.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'status' => 'boolean']);
        SpaceType::create(['name' => $request->name, 'status' => $request->status ?? 1]);
        return redirect()->route('admin.space-types.index')->with('success', 'Space Type created.');
    }

    public function edit(SpaceType $spaceType)
    {
        return view('admin.space_types.edit', compact('spaceType'));
    }

    public function update(Request $request, SpaceType $spaceType)
    {
        $request->validate(['name' => 'required|string|max:255', 'status' => 'boolean']);
        $spaceType->update(['name' => $request->name, 'status' => $request->has('status') ? 1 : 0]);
        return redirect()->route('admin.space-types.index')->with('success', 'Space Type updated.');
    }

    public function destroy(SpaceType $spaceType)
    {
        $spaceType->delete();
        return redirect()->route('admin.space-types.index')->with('success', 'Space Type deleted.');
    }
}
