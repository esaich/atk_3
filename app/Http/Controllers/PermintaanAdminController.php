<?php

namespace App\Http\Controllers;

use App\Models\PermintaanBarang;
use App\Models\Barang;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PermintaanAdminController extends Controller
{
    public function index()
    {
        $allPermintaans = PermintaanBarang::with('user', 'barang')
            ->orderBy('created_at', 'desc')
            ->get();

        $groupedPermintaans = $allPermintaans->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
        })->map(function($group) {
            return [
                'tanggal' => $group->first()->created_at,
                'items' => $group,
            ];
        });

        return view('admin.permintaan.index', compact('groupedPermintaans'));
    }

    public function showGroupedByDate(string $tanggal)
    {
        $permintaanItems = PermintaanBarang::with('user', 'barang')
            ->whereDate('created_at', $tanggal)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($permintaanItems->isEmpty()) {
            return redirect()->route('admin.permintaan.index')->with('error', 'Tidak ada permintaan ditemukan untuk tanggal tersebut.');
        }

        return view('admin.permintaan.grouped_show', compact('permintaanItems', 'tanggal'));
    }

    public function edit(PermintaanBarang $permintaan_barang)
    {
        if ($permintaan_barang->status !== 'pending') {
            return redirect()->route('admin.permintaan.showGroupedByDate', [
                'tanggal' => $permintaan_barang->created_at->format('Y-m-d')
            ])->with('error', 'Permintaan ini tidak dapat diedit karena statusnya bukan pending.');
        }

        $barangs = Barang::all();
        return view('admin.permintaan.edit', compact('permintaan_barang', 'barangs'));
    }

    public function update(Request $request, PermintaanBarang $permintaan_barang)
    {
        if ($permintaan_barang->status !== 'pending') {
            return redirect()->route('admin.permintaan.showGroupedByDate', [
                'tanggal' => $permintaan_barang->created_at->format('Y-m-d')
            ])->with('error', 'Permintaan ini tidak dapat diperbarui karena statusnya bukan pending.');
        }

        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'alasan' => 'nullable|string',
        ]);

        $permintaan_barang->update($request->only(['barang_id', 'jumlah', 'alasan']));

        return redirect()->route('admin.permintaan.showGroupedByDate', [
            'tanggal' => $permintaan_barang->created_at->format('Y-m-d')
        ])->with('success', 'Permintaan barang berhasil diperbarui oleh Admin.');
    }

    /**
     * Approve an item request and reduce item stock.
     * Menggunakan lockForUpdate() supaya dua approval bersamaan untuk barang
     * yang sama tidak bisa membuat stok jadi negatif (race condition).
     */
    public function approve($id)
    {
        DB::beginTransaction();

        try {
            $permintaan = PermintaanBarang::findOrFail($id);

            if ($permintaan->status !== 'pending') {
                DB::rollBack();
                $redirectRoute = route('admin.permintaan.showGroupedByDate', ['tanggal' => $permintaan->created_at->format('Y-m-d')]);
                return redirect($redirectRoute)->with('error', 'Permintaan ini sudah tidak pending.');
            }

            // Kunci row barang ini sampai transaksi selesai, supaya request approve
            // lain untuk barang yang sama harus menunggu, bukan berjalan paralel.
            $barang = Barang::where('id', $permintaan->barang_id)->lockForUpdate()->first();

            if (!$barang) {
                DB::rollBack();
                $redirectRoute = route('admin.permintaan.showGroupedByDate', ['tanggal' => $permintaan->created_at->format('Y-m-d')]);
                return redirect($redirectRoute)->with('error', 'Barang terkait tidak ditemukan.');
            }

            if ($barang->stok < $permintaan->jumlah) {
                DB::rollBack();
                $redirectRoute = route('admin.permintaan.showGroupedByDate', ['tanggal' => $permintaan->created_at->format('Y-m-d')]);
                return redirect($redirectRoute)->with('error', 'Stok ' . $barang->nama_barang . ' tidak mencukupi. Hanya tersedia ' . $barang->stok . '.');
            }

            $barang->stok -= $permintaan->jumlah;
            $barang->save();

            $permintaan->status = 'disetujui';
            $permintaan->save();

            BarangKeluar::create([
                'permintaan_id' => $permintaan->id,
                'barang_id' => $permintaan->barang_id,
                'jumlah_keluar' => $permintaan->jumlah,
                'tanggal_keluar' => Carbon::now(),
                'keterangan' => 'Disetujui dari permintaan ID: ' . $permintaan->id . ' oleh ' . (Auth::user()->name ?? 'Admin'),
            ]);

            DB::commit();

            $redirectRoute = route('admin.permintaan.showGroupedByDate', ['tanggal' => $permintaan->created_at->format('Y-m-d')]);
            return redirect($redirectRoute)->with('success', 'Permintaan disetujui dan stok barang diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error approving request ID {$id}: " . $e->getMessage());
            $permintaan = PermintaanBarang::find($id);
            if ($permintaan) {
                $redirectRoute = route('admin.permintaan.showGroupedByDate', ['tanggal' => $permintaan->created_at->format('Y-m-d')]);
            } else {
                $redirectRoute = route('admin.permintaan.index');
            }
            return redirect($redirectRoute)->with('error', 'Terjadi kesalahan saat menyetujui permintaan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string',
        ]);

        $permintaan = PermintaanBarang::findOrFail($id);

        if ($permintaan->status !== 'pending') {
            $redirectRoute = route('admin.permintaan.showGroupedByDate', ['tanggal' => $permintaan->created_at->format('Y-m-d')]);
            return redirect($redirectRoute)->with('error', 'Permintaan ini sudah tidak pending.');
        }

        $permintaan->status = 'ditolak';
        $permintaan->alasan = $request->alasan;
        $permintaan->save();

        $redirectRoute = route('admin.permintaan.showGroupedByDate', ['tanggal' => $permintaan->created_at->format('Y-m-d')]);
        return redirect($redirectRoute)->with('success', 'Permintaan berhasil ditolak.');
    }
}