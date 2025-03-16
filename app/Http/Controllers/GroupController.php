<?php

namespace App\Http\Controllers;

use App\Models\Group; // Make sure this points to the correct namespace
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::all();
        return view('groups', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:groups',
            'level' => 'required|integer',
            'status' => 'required|in:Active,Inactive'
        ]);

        Group::create($validated);
        return redirect()->route('groups.index')->with('success', 'Group created successfully');
    }

    public function edit(Group $group)
    {
        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|unique:groups,name,' . $group->id,
            'level' => 'required|integer',
            'status' => 'required|in:Active,Inactive'
        ]);

        $group->update($validated);
        return redirect()->route('groups.index')->with('success', 'Group updated successfully');
    }

    public function destroy(Group $group)
    {
        $group->delete();
        return redirect()->route('groups.index')->with('success', 'Group deleted successfully');
    }
}