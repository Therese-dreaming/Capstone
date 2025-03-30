<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::all();
        return view('groups', compact('groups'));
    }

    public function edit(Group $group)
    {
        if (Auth::user()->group->name !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }
        
        if ($group->name === 'Admin') {
            return back()->with('error', 'The Admin group cannot be modified.');
        }
        
        return view('edit-group', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        if (Auth::user()->group->name !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        if ($group->name === 'Admin') {
            return back()->with('error', 'The Admin group cannot be modified.');
        }

        try {
            $validated = $request->validate([
                'name' => 'required|unique:groups,name,' . $group->id,
                'level' => 'required|integer|min:1|max:3',
                'status' => 'required|in:Active,Inactive'
            ]);

            $group->update($validated);

            return redirect()->route('groups.index')
                ->with('success', 'Group "' . $group->name . '" has been updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update group: ' . $e->getMessage()]);
        }
    }

    public function destroy(Group $group)
    {
        if (Auth::user()->group->name !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        if ($group->name === 'Admin') {
            return back()->with('error', 'The Admin group cannot be deleted.');
        }

        try {
            if ($group->users()->count() > 0) {
                return back()->with('error', 'Cannot delete group: It still has users assigned to it.');
            }

            $groupName = $group->name;
            $group->delete();

            return redirect()->route('groups.index')
                ->with('success', 'Group "' . $groupName . '" has been deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete group. Please try again.');
        }
    }
}