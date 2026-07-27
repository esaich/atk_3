<?php

namespace App\Http\Controllers;

use App\Models\PengadaanBarang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class BendaharaController extends Controller
{
    /**
     * Dashboard bendahara: ringkasan pengajuan pengadaan yang perlu ditindak.
     */
    public function index()
    {
        $totalDiajukan = PengadaanBarang::where('status', 'diajukan')->count();
        $totalDisetujui = PengadaanBarang::where('status', 'disetujui')->count();
        $totalDitolak = PengadaanBarang::where('status', 'ditolak')->count();

        $pengadaanMenunggu = PengadaanBarang::with('supplier')
            ->where('status', 'diajukan')
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        return view('bendahara.dashboard', compact(
            'totalDiajukan',
            'totalDisetujui',
            'totalDitolak',
            'pengadaanMenunggu'
        ));
    }

    /**
     * Daftar seluruh pengajuan pengadaan (semua status) untuk ditinjau bendahara.
     */
    public function pengadaanIndex(Request $request)
    {
        $query = PengadaanBarang::with(['supplier', 'approver']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pengadaanBarangs = $query->orderBy('tanggal_pengajuan', 'desc')->get();
        $filterStatus = $request->input('status');

        return view('bendahara.pengadaan.index', compact('pengadaanBarangs', 'filterStatus'));
    }

    /**
     * Setujui satu pengajuan pengadaan.
     */
    public function approve(PengadaanBarang $pengadaanBarang)
    {
        if ($pengadaanBarang->status !== 'diajukan') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah pernah diproses sebelumnya.');
        }

        $pengadaanBarang->update([
            'status' => 'disetujui',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'catatan_approval' => null,
        ]);

        return redirect()->back()->with('success', 'Pengajuan pengadaan berhasil disetujui.');
    }

    /**
     * Tolak satu pengajuan pengadaan, wajib menyertakan alasan.
     */
    public function reject(Request $request, PengadaanBarang $pengadaanBarang)
    {
        if ($pengadaanBarang->status !== 'diajukan') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah pernah diproses sebelumnya.');
        }

        $request->validate([
            'catatan_approval' => 'required|string|max:1000',
        ], [
            'catatan_approval.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $pengadaanBarang->update([
            'status' => 'ditolak',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'catatan_approval' => $request->catatan_approval,
        ]);

        return redirect()->back()->with('success', 'Pengajuan pengadaan berhasil ditolak.');
    }

    /**
     * Form pengaturan akun bendahara.
     */
    public function showSettingsForm()
    {
        /** @var User $bendahara */
        $bendahara = Auth::user();

        return view('bendahara.settings', compact('bendahara'));
    }

    /**
     * Perbarui pengaturan akun bendahara.
     */
    public function updateSettings(Request $request)
    {
        /** @var User $bendahara */
        $bendahara = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($bendahara->id),
            ],
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ];

        $request->validate($rules);

        $bendahara->name = $request->name;
        $bendahara->email = $request->email;

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $bendahara->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini salah.']);
            }
            $bendahara->password = Hash::make($request->password);
        }

        $bendahara->save();

        return redirect()->route('bendahara.settings.index')->with('success', 'Pengaturan akun berhasil diperbarui.');
    }
}