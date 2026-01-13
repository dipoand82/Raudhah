<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10); // Ambil data user, 10 per halaman
        return view('admin.users.index', compact('users'));
    }

    // 2. FORM TAMBAH (Jika tidak pakai Modal)
    public function create()
    {
        return view('admin.users.create');
    }

    // 3. SIMPAN DATA BARU
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        // Simpan ke database
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Password wajib di-hash
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan!');
    }

    // 4. FORM EDIT
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // 5. UPDATE DATA
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // Validasi email unik, tapi abaikan email milik user ini sendiri
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:8', // Password boleh kosong jika tidak ingin diganti
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Cek apakah password diisi? Jika ya, update password baru
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui!');
    }

    // 6. HAPUS DATA
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }
}
