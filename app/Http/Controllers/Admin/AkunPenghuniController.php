<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AkunPenghuniController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->where('role', 'penghuni');
        if ($request->filled('search')) {
            $term = trim($request->string('search')->toString());
            $query->where(fn ($builder) => $builder->where('nama', 'like', "%{$term}%")
                ->orWhere('nim_nik', 'like', "%{$term}%")
                ->orWhere('nik', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%"));
        }
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->string('jenis_kelamin')->toString());
        }
        if ($request->filled('aktif') && in_array($request->input('aktif'), ['0', '1'], true)) {
            $query->where('is_active', (bool) $request->boolean('aktif'));
        }
        $users = $query->orderBy('nama')->paginate(15)->withQueryString();
        return view('admin.akun-penghuni.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'min:8', 'max:255', Rule::unique('users', 'nim_nik')],
            'nik' => ['required', 'digits:16', Rule::unique('users', 'nik')],
            'jenis_kelamin' => ['required', Rule::in(['putra', 'putri'])],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'no_telepon' => ['nullable', 'string', 'max:20'],
        ]);

        $initialPassword = $validated['nim'];
        $user = new User();
        $user->nama = $validated['nama'];
        $user->nik = $validated['nik'];
        $user->jenis_kelamin = $validated['jenis_kelamin'];
        $user->nim_nik = $validated['nim'];
        $user->email = $validated['email'];
        $user->no_telepon = $validated['no_telepon'] ?? null;
        $user->role = 'penghuni';
        $user->password = Hash::make($initialPassword);
        $user->is_active = true;
        $user->must_change_password = false;
        $user->save();

        return redirect()->route('admin.akun-penghuni.index')
            ->with('login_username', $validated['nim'])
            ->with('login_password', $initialPassword)
            ->with('success', 'Akun berhasil dibuat. Username dan password awal sama dengan NIM; penghuni dapat mengganti password melalui Profil kapan saja.');
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->role === 'penghuni', 404);
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'min:8', 'max:255', Rule::unique('users', 'nim_nik')->ignore($user->id)],
            'nik' => ['required', 'digits:16', Rule::unique('users', 'nik')->ignore($user->id)],
            'jenis_kelamin' => ['required', Rule::in(['putra', 'putri'])],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'no_telepon' => ['nullable', 'string', 'max:20'],
        ]);

        $genderConflict = false;
        DB::transaction(function () use ($user, $validated, &$genderConflict) {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            if ($lockedUser->jenis_kelamin !== $validated['jenis_kelamin']
                && $lockedUser->penempatans()->whereIn('status', ['scheduled', 'active', 'no_show'])->exists()) {
                $genderConflict = true;
                return;
            }

            $lockedUser->nama = $validated['nama'];
            $lockedUser->nik = $validated['nik'];
            $lockedUser->jenis_kelamin = $validated['jenis_kelamin'];
            $lockedUser->nim_nik = $validated['nim'];
            $lockedUser->email = $validated['email'];
            $lockedUser->no_telepon = $validated['no_telepon'] ?? null;
            $lockedUser->save();
        }, 3);
        if ($genderConflict) {
            return back()->withErrors(['jenis_kelamin' => 'Jenis kelamin tidak dapat diubah selama masih ada penempatan aktif, terjadwal, atau no-show. Selesaikan penempatan melalui administrasi terlebih dahulu.'])->withInput();
        }
        return back()->with('success', 'Identitas penghuni diperbarui.');
    }

    public function toggle(User $user)
    {
        abort_unless($user->role === 'penghuni', 404);
        $user->is_active = !$user->is_active;
        $user->save();
        return back()->with('success', $user->is_active ? 'Akun diaktifkan.' : 'Akun dinonaktifkan; histori tetap dipertahankan.');
    }

    public function resetPassword(User $user)
    {
        abort_unless($user->role === 'penghuni', 404);
        $nim = (string) $user->nim_nik;
        if (mb_strlen($nim) < 8) {
            return back()->withErrors(['reset_password' => 'Password reset menggunakan NIM, tetapi NIM akun ini kurang dari 8 karakter. Perbarui NIM melalui administrasi terlebih dahulu.']);
        }

        $user->password = Hash::make($nim);
        $user->must_change_password = false;
        $user->save();

        return back()
            ->with('login_username', $nim)
            ->with('login_password', $nim)
            ->with('success', 'Password akun direset kembali ke NIM. Penghuni dapat menggantinya melalui Profil kapan saja.');
    }
}
