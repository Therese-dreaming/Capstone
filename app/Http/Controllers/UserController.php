<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        try {
            $validated = $request->validate([
                'name' => 'required',
                'username' => 'required|unique:users',
                'password' => 'required|confirmed|min:6',
                'group_id' => 'required|exists:groups,id',
                'status' => 'required|in:Active,Inactive'
            ]);

            $validated['password'] = Hash::make($validated['password']);
            
            // Debug information
            \Log::info('Validation passed', $validated);
            
            $user = User::create($validated);
            
            \Log::info('User created', ['user_id' => $user->id]);

            return redirect()->route('users.index')
                ->with('success', 'User created successfully');
        } catch (\Exception $e) {
            \Log::error('User creation failed: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }
}