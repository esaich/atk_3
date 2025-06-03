<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DivisiUserController extends Controller
{
    // Tampilkan daftar user divisi dengan pagination
    public function index()
    {
        $divisis = User::where('role', 'divisi')->paginate(10);
        return view('tambah-user-divisi.index', compact('divisis'));
    }

    // Tampilkan form tambah user divisi
    public function create()
    {
        return view('tambah-user-divisi.create');
    }

    // Simpan user divisi baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'divisi',
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.divisi.index')->with('success', 'User divisi berhasil ditambah.');
    }

    // Tampilkan form edit user divisi
    public function edit(User $divisi)
    {
        // Pastikan user yang diedit adalah role divisi (optional, bisa dihapus jika yakin)
        if ($divisi->role !== 'divisi') {
            abort(404);
        }

        return view('tambah-user-divisi.edit', compact('divisi'));
    }

    // Update data user divisi
    public function update(Request $request, User $divisi)
    {
        // Pastikan user yang diedit adalah role divisi (optional)
        if ($divisi->role !== 'divisi') {
            abort(404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($divisi->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $divisi->name = $request->name;
        $divisi->email = $request->email;

        if ($request->filled('password')) {
            $divisi->password = Hash::make($request->password);
        }

        $divisi->save();

        return redirect()->route('admin.divisi.index')->with('success', 'User divisi berhasil diupdate.');
    }

    // Hapus user divisi
    public function destroy(User $divisi)
    {
        // Pastikan user yang dihapus adalah role divisi (optional)
        if ($divisi->role !== 'divisi') {
            abort(404);
        }

        $divisi->delete();

        return redirect()->route('admin.divisi.index')->with('success', 'User divisi berhasil dihapus.');
    }
}
