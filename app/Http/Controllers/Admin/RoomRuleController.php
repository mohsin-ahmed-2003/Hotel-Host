<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomRule;
use Illuminate\Http\Request;

class RoomRuleController extends Controller
{
    public function index(Request $request)
    {
        $query = RoomRule::query();
        
        if ($request->has('search')) {
            $query->where('rule_name', 'like', '%' . $request->search . '%')
                  ->orWhere('rule_text', 'like', '%' . $request->search . '%')
                  ->orWhere('id', $request->search);
        }

        $roomRules = $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'))->paginate(10);
        return view('admin.room_rules.index', compact('roomRules'));
    }

    public function create()
    {
        return view('admin.room_rules.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'rule_name' => 'required|string|max:255',
            'rule_text' => 'nullable|string|max:1000',
            'icon'      => 'nullable|string|max:255'
        ]);

        RoomRule::create([
            'rule_name' => $request->rule_name,
            'rule_text' => $request->rule_text,
            'icon'      => $request->icon ?? 'fas fa-clipboard-list'
        ]);

        return redirect()->route('admin.room-rules.index')->with('success', 'Room Rule created successfully.');
    }

    public function edit(RoomRule $roomRule)
    {
        return view('admin.room_rules.edit', compact('roomRule'));
    }

    public function update(Request $request, RoomRule $roomRule)
    {
        $request->validate([
            'rule_name' => 'required|string|max:255',
            'rule_text' => 'nullable|string|max:1000',
            'icon'      => 'nullable|string|max:255'
        ]);

        $roomRule->update([
            'rule_name' => $request->rule_name,
            'rule_text' => $request->rule_text,
            'icon'      => $request->icon ?? 'fas fa-clipboard-list'
        ]);

        return redirect()->route('admin.room-rules.index')->with('success', 'Room Rule updated successfully.');
    }

    public function destroy(RoomRule $roomRule)
    {
        $roomRule->delete();
        return redirect()->route('admin.room-rules.index')->with('success', 'Room Rule deleted successfully.');
    }
}
