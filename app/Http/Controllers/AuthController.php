<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('leads.index');
        }
        \Log::info('Login Attempt', [
            'username' => 'request->username',
            'password' => 'request->password',
        ]);
        return view('welcome');
    }

    public function login(Request $request)
    {
        \Log::info('Login Attempt', [
            'username' => $request->username,
            'password' => $request->password,
        ]);
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        \Log::info('Login Attempt', [
            'username' => $request->username,
            'password' => $request->password,
        ]);
        $user = User::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->route('leads.index');
        }

        return back()->withErrors([
            'username' => 'Incorrect username or password.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}