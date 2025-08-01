<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class DivisiController extends Controller
{
    /**
     * Menampilkan dashboard untuk user divisi.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('divisi.dashboard');
    }

    /**
     * Menampilkan formulir pengaturan akun (khususnya untuk ubah password).
     *
     * @return \Illuminate\View\View
     */
    public function showSettingsForm()
    {
        return view('divisi.settings');
    }

    /**
     * Memproses permintaan untuk memperbarui kata sandi user divisi.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Kata sandi saat ini tidak cocok.'],
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('divisi.settings.password')->with('success', 'Kata sandi berhasil diperbarui.');
    }

    /**
     * Menampilkan formulir untuk mengubah email user divisi.
     *
     * @return \Illuminate\View\View
     */
    public function showEmailForm()
    {
        return view('divisi.email_settings');
    }

    /**
     * Memproses permintaan untuk memperbarui email user divisi.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateEmail(Request $request)
    {
        $user = Auth::user();

        // Validasi input email
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);
        
        $user->email = $request->email;
        $user->save();

        return redirect()->route('divisi.settings.email')->with('success', 'Email berhasil diperbarui.');
    }
}
