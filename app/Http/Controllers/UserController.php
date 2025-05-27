<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;  // Add this line

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('group')->get();
        return view('users', compact('users'));
    }

    public function create()
    {
        $groups = Group::all();
        return view('create-user', compact('groups'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->group->name !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validated = $request->validate([
                'name' => 'required',
                'username' => 'required|unique:users',
                'department' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'password' => 'required|confirmed|min:6',
                'group_id' => 'required|exists:groups,id',
            ]);

            // Validate RFID number for faculty/teacher positions
            if (in_array(strtolower($request->position), ['teacher', 'faculty'])) {
                $request->validate([
                    'rfid_number' => 'required|unique:users,rfid_number'
                ]);
                $validated['rfid_number'] = $request->rfid_number;
            }

            $validated['password'] = Hash::make($validated['password']);
            
            $user = User::create($validated);

            return redirect()->route('users.index')
                ->with('success', 'User "' . $validated['name'] . '" has been created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }

    public function edit(User $user)
    {
        if (Auth::user()->group->name !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }
        $groups = Group::all();
        return view('edit-user', compact('user', 'groups'));
    }

    public function update(Request $request, User $user)
    {
        if (Auth::user()->group->name !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validated = $request->validate([
                'name' => 'required',
                'username' => 'required|unique:users,username,' . $user->id,
                'department' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'group_id' => 'required|exists:groups,id',
            ]);

            // Validate RFID number for faculty/teacher positions
            if (in_array(strtolower($request->position), ['teacher', 'faculty'])) {
                $request->validate([
                    'rfid_number' => 'required|unique:users,rfid_number,' . $user->id
                ]);
                $validated['rfid_number'] = $request->rfid_number;
            } else {
                $validated['rfid_number'] = null; // Clear RFID if position is changed from faculty/teacher
            }

            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'required|confirmed|min:6'
                ]);
                $validated['password'] = Hash::make($request->password);
            }

            $user->update($validated);

            return redirect()->route('users.index')
                ->with('success', 'User "' . $user->name . '" has been updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()]);
        }
    }

    public function destroy(User $user)
    {
        if (Auth::user()->group->name !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        try {
            $userName = $user->name;
            $user->delete();
            return redirect()->route('users.index')
                ->with('success', 'User "' . $userName . '" has been deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete user. Please try again.');
        }
    }
}