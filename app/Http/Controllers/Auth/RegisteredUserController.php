<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nim_nik' => ['required', 'string', 'min:8', 'max:255', Rule::unique('users', 'nim_nik')],
            'nik' => ['required', 'digits:16', Rule::unique('users', 'nik')],
            'jenis_kelamin' => ['required', Rule::in(['putra', 'putri'])],
            'no_telepon' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Rules\Password::min(8)],
        ]);

        $user = new User();
        $user->nama = $validated['nama'];
        $user->nim_nik = $validated['nim_nik'];
        $user->nik = $validated['nik'];
        $user->jenis_kelamin = $validated['jenis_kelamin'];
        $user->no_telepon = $validated['no_telepon'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role = 'penghuni';
        $user->is_active = true;
        $user->must_change_password = false;
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect(route('dashboard', absolute: false));
    }
}
