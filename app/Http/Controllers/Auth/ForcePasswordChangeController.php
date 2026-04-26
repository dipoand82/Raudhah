<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules;

class ForcePasswordChangeController extends Controller
{
    public function index()
    {
        return view('auth.force-change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi ulangi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

      $user = $request->user();

    $user->update([
        'password' => Hash::make($request->password),
        'must_change_password' => false,
    ]);

    $request->session()->put('password_hash_web', $user->password);

    if ($user->role === 'siswa') {
        return redirect()->route('siswa.dashboard')
            ->with('status', 'Password berhasil diperbarui!');
    }

    return redirect()->route('dashboard')
        ->with('status', 'Password berhasil diperbarui!');
}
}
