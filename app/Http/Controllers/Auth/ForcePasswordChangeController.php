<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Redirect; // Tambahan

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
     * Proses simpan password baru.
     */
    public function update(Request $request)
    {
        // 1. Validasi Input (Password wajib diisi, dikonfirmasi, dan sesuai standar keamanan)
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Ambil User yang sedang login
        $user = $request->user();

        // 3. Update Password & Matikan Status 'Wajib Ganti'
        $user->update([
            'password' => Hash::make($request->password), // Enkripsi password baru
            'must_change_password' => false,              // <--- PENTING: Cabut tanda wajib ganti
        ]);

        // 4. Kembalikan ke Dashboard dengan pesan sukses
        return Redirect::route('dashboard')->with('status', 'Password berhasil diperbarui! Akun Anda sekarang aman.');
    }
}