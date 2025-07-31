<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\PermintaanBarang; // Tetap di sini jika data untuk dashboard diambil dari PermintaanBarang
use App\Models\User; // Import model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Untuk hashing password
use Illuminate\Validation\Rule; // Untuk validasi unik email
use Illuminate\Support\Facades\Auth; // Pastikan baris ini ada

class AdminController extends Controller
{
    /**
     * Menampilkan dashboard admin dengan statistik ringkas.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $totalSuppliers = Supplier::count();
        $totalBarang = Barang::count();
        $totalBarangMasuk = BarangMasuk::count();
        $totalPermintaan = PermintaanBarang::where('status', 'pending')->count(); 

        return view('admin.dashboard', compact(
            'totalSuppliers',
            'totalBarang',
            'totalBarangMasuk',
            'totalPermintaan'
        ));
    }

    /**
     * Menampilkan form pengaturan akun admin.
     *
     * @return \Illuminate\View\View
     */
    public function showSettingsForm()
    {
        // Mengambil data user admin yang sedang login
        $admin = auth()->user();
        return view('admin.settings.index', compact('admin')); // Meneruskan sebagai 'admin'
    }

    /**
     * Memperbarui pengaturan akun admin (email dan/atau password).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request)
    {
        $admin = auth()->user();

        // Aturan validasi
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                // Pastikan email unik, kecuali untuk email admin yang sedang login
                Rule::unique('users')->ignore($admin->id),
            ],
            'current_password' => 'nullable|string', // Password saat ini opsional jika tidak mengubah password
            'password' => 'nullable|string|min:8|confirmed', // Password baru opsional
        ];

        // Validasi permintaan
        $request->validate($rules);

        // Update nama dan email
        $admin->name = $request->name;
        $admin->email = $request->email;

        // Jika password baru diisi, validasi password lama dan update password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $admin->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini salah.']);
            }
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan akun berhasil diperbarui.');
    }
}
