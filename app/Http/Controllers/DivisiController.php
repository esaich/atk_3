<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class DivisiController extends Controller
{
    /**
     * Tampilkan dashboard divisi.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mendapatkan data user yang sedang login
        $user = Auth::user();

        // Mengirim data user ke view
        return view('divisi.dashboard', compact('user'));
    }

    /**
     * Tampilkan formulir pengaturan akun untuk divisi.
     *
     * @return \Illuminate\View\View
     */
    public function showSettingsForm()
    {
        return view('divisi.settings');
    }

    /**
     * Perbarui kata sandi dan/atau email pengguna divisi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $rules = [];
        $messages = [];

        // Validasi kondisional untuk kata sandi
        if ($request->filled('new_password') || $request->filled('current_password') || $request->filled('new_password_confirmation')) {
            $rules = array_merge($rules, [
                'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('Kata sandi yang Anda masukkan tidak cocok dengan kata sandi saat ini.');
                    }
                }],
                'new_password' => 'required|min:8|confirmed',
            ]);
            $messages = array_merge($messages, [
                'current_password.required' => 'Kata sandi saat ini harus diisi.',
                'new_password.required' => 'Kata sandi baru harus diisi.',
                'new_password.min' => 'Kata sandi baru minimal 8 karakter.',
                'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            ]);
        }

        // Validasi kondisional untuk email
        if ($request->filled('email') && $request->email !== $user->email) {
            $rules = array_merge($rules, [
                'email' => 'required|email|unique:users,email,' . $user->id,
            ]);
            $messages = array_merge($messages, [
                'email.required' => 'Alamat email harus diisi.',
                'email.email' => 'Format alamat email tidak valid.',
                'email.unique' => 'Alamat email ini sudah digunakan.',
            ]);
        }

        // Jika tidak ada data yang akan diperbarui
        if (empty($rules)) {
            return redirect()->back()->with('success', 'Tidak ada perubahan yang dilakukan.');
        }

        $request->validate($rules, $messages);
        
        // Memperbarui kata sandi jika diisi
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        // Memperbarui email jika diisi
        if ($request->filled('email') && $request->email !== $user->email) {
            $user->email = $request->email;
        }

        $user->save();

        return redirect()->back()->with('success', 'Pengaturan akun berhasil diperbarui.');
    }
}
