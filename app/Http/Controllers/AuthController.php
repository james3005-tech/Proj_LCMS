<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Lawyer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users'],
            'password'              => ['required', 'confirmed', 'min:8'],
            'role'                  => ['required', 'in:admin,lawyer,client'],
            'phone'                 => ['nullable', 'string', 'max:20'],
            'address'               => ['nullable', 'string'],
            'bar_number'            => ['nullable', 'string'],
            'specialization'        => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
            'phone'    => $data['phone'] ?? null,
        ]);

        if ($data['role'] === 'client') {
            Client::create([
                'user_id' => $user->id,
                'address' => $data['address'] ?? null,
            ]);
        } elseif ($data['role'] === 'lawyer') {
            Lawyer::create([
                'user_id'        => $user->id,
                'bar_number'     => $data['bar_number'] ?? null,
                'specialization' => $data['specialization'] ?? null,
            ]);
        }

        Auth::login($user);
        return redirect()->route('dashboard')->with('success', 'Welcome to LCMS, ' . $user->name . '!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}