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

    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
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

            return $this->redirectBasedOnRole();
        }

        // Try RFID login if username login fails
        $user = User::where('rfid_number', $credentials['username'])->first();
        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
            $user->forceFill([
                'last_login' => Carbon::now()
            ])->save();

            return $this->redirectBasedOnRole();
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }

    protected function redirectBasedOnRole()
    {
        $user = Auth::user();
        
        if ($user->group_id <= 2) {
            return redirect('/my-tasks')->with('auth_redirect', true);
        } elseif ($user->group_id === 4) { // Custodian
            return redirect('/custodian/assets')->with('auth_redirect', true);
        } elseif ($user->group_id === 3) { // Users
            return redirect('/lab-logging')->with('auth_redirect', true);
        } else {
            return redirect('/')->with('error', 'Unauthorized access.');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}