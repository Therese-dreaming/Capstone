<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    public function __construct()
    {
        if (Auth::check()) {
            Redirect::to('/assets')->send();
        }
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/assets');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // Try username login first
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user->forceFill([
                'last_login' => Carbon::now()
            ])->save();

            return redirect()->intended('/assets');
        }

        // Try RFID login if username login fails
        $user = User::where('rfid_number', $credentials['username'])->first();
        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
            $user->forceFill([
                'last_login' => Carbon::now()
            ])->save();

            return redirect()->intended('/assets');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}