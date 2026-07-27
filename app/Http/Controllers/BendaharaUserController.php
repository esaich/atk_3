<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class BendaharaUserController extends Controller
{
    // Tampilkan daftar user bendahara
    public function index()
    {
        $bendaharas = User::where('role', 'bendahara')->get();
        return view('tambah-user-bendahara.index', compact('bendaharas'));
    }

    // Tampilkan form tambah user bendahara
    public function create()
    {
        return view('tambah-user-bendahara.create');
    }

    // Simpan user bendahara baru ke database
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
            'role' => 'bendahara',
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.bendahara.index')->with('success', 'User bendahara berhasil ditambah.');
    }

    // Tampilkan form edit user bendahara
    public function edit(User $bendahara)
    {
        if ($bendahara->role !== 'bendahara') {
            abort(404);
        }

        return view('tambah-user-bendahara.edit', compact('bendahara'));
    }

    // Update data user bendahara
    public function update(Request $request, User $bendahara)
    {
        if ($bendahara->role !== 'bendahara') {
            abort(404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($bendahara->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $bendahara->name = $request->name;
        $bendahara->email = $request->email;

        if ($request->filled('password')) {
            $bendahara->password = Hash::make($request->password);
        }

        $bendahara->save();

        return redirect()->route('admin.bendahara.index')->with('success', 'User bendahara berhasil diupdate.');
    }

    // Hapus user bendahara
    public function destroy(User $bendahara)
    {
        if ($bendahara->role !== 'bendahara') {
            abort(404);
        }

        $bendahara->delete();

        return redirect()->route('admin.bendahara.index')->with('success', 'User bendahara berhasil dihapus.');
    }
}