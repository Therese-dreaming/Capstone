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
        return back()->with('error', 'Groups cannot be modified.');
    }

    public function update(Request $request, Group $group)
    {
        return back()->with('error', 'Groups cannot be modified.');
    }

    public function destroy(Group $group)
    {
        return back()->with('error', 'Groups cannot be deleted.');
    }
}