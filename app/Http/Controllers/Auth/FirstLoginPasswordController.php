<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FirstLoginPasswordController extends Controller
{
    public function edit()
    {
        abort_unless(auth()->user()?->role === 'penghuni', 403);
        return view('auth.first-login-password');
    }

    public function update(Request $request)
    {
        abort_unless($request->user()?->role === 'penghuni', 403);
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();
        $user->password = Hash::make($validated['password']);
        $user->must_change_password = false;
        $user->save();
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
