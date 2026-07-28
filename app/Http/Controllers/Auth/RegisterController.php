<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:30',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'accept_terms' => 'accepted',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'cliente',
            'accepted_terms_at' => now(),
            'accepted_terms_version' => '2026-07-28',
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('home')->with('success', '¡Bienvenido a Negocios RaR!');
    }
}
