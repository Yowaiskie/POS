<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->position === 'Kitchen') {
                return redirect()->intended('/kitchen');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string',
        ]);

        // Find any admin with this PIN
        $admin = \App\Models\User::where('position', 'Admin')
            ->where('admin_pin', $request->pin)
            ->first();

        if ($admin) {
            return response()->json([
                'success' => true,
                'admin_id' => $admin->id,
                'admin_name' => $admin->name
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid Security PIN.'
        ], 422);
    }
}
