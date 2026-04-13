<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules;

class ForcePasswordChangeController extends Controller
{
    /**
     * Tampilkan formulir ganti password.
     */
    public function index()
    {
        return view('auth.force-change-password');
    }

    /**
     * Proses simpan email dan password baru.
     */
    public function update(Request $request)
    {
        // 1. Validasi Input
        // Menambahkan validasi email, dan mengecualikan ID user saat ini dari pengecekan 'unique'
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            // EMAIL
            // 'email.required' => 'Email wajib diisi.',
            // 'email.email' => 'Format email tidak valid.',
            // 'email.unique' => 'Email sudah digunakan.',

            // PASSWORD
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi ulangi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        // 2. Ambil User yang sedang login
      $user = $request->user();

    // UPDATE HANYA PASSWORD
    $user->update([
        'password' => Hash::make($request->password),
        'must_change_password' => false,
    ]);

    // Refresh session biar tidak logout
    $request->session()->put('password_hash_web', $user->password);

    // REDIRECT
    if ($user->role === 'siswa') {
        return redirect()->route('siswa.dashboard')
            ->with('status', 'Password berhasil diperbarui!');
    }

    return redirect()->route('dashboard')
        ->with('status', 'Password berhasil diperbarui!');
}
}
