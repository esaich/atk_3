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
    /**
     * Display a listing of all item requests (pending, approved, rejected),
     * grouped by date.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Retrieve all item requests along with related user and item data
        $allPermintaans = PermintaanBarang::with('user', 'barang')
            ->orderBy('created_at', 'desc') // Order by latest date
            ->get();

        // Group requests by date (without time)
        $groupedPermintaans = $allPermintaans->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
        })->map(function($group) {
            // For each date group, get the date and its items
            return [
                'tanggal' => $group->first()->created_at, // Get the date from the first item in the group
                'items' => $group, // Collection of all requests within this date group
            ];
        });

        // Pass the grouped request data to the admin.permintaan.index view
        return view('admin.permintaan.index', compact('groupedPermintaans'));
    }

    /**
     * Display details of item requests for a specific date.
     *
     * @param string $tanggal Date in YYYY-MM-DD format.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showGroupedByDate(string $tanggal)
    {
        // Retrieve all item requests for the specific date, eager load user and barang
        $permintaanItems = PermintaanBarang::with('user', 'barang')
            ->whereDate('created_at', $tanggal)
            ->orderBy('created_at', 'asc')
            ->get();

        // If no items are found for the date, redirect back with an error message
        if ($permintaanItems->isEmpty()) {
            return redirect()->route('admin.permintaan.index')->with('error', 'Tidak ada permintaan ditemukan untuk tanggal tersebut.');
        }

        // Pass the items and date to the grouped_show view
        return view('admin.permintaan.grouped_show', compact('permintaanItems', 'tanggal'));
    }

    /**
     * Show the form for editing the specified item request.
     * Admin can only edit 'pending' requests.
     *
     * @param \App\Models\PermintaanBarang $permintaan_barang
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(PermintaanBarang $permintaan_barang)
    {
        // Admin hanya bisa mengedit permintaan yang statusnya 'pending'
        if ($permintaan_barang->status !== 'pending') {
            return redirect()->route('admin.permintaan.showGroupedByDate', [
                'tanggal' => $permintaan_barang->created_at->format('Y-m-d')
            ])->with('error', 'Permintaan ini tidak dapat diedit karena statusnya bukan pending.');
        }

        $barangs = Barang::all(); // Untuk dropdown pilihan barang (jika admin bisa mengubah barang juga)
        return view('admin.permintaan.edit', compact('permintaan_barang', 'barangs'));
    }

    /**
     * Update the specified item request in storage.
     * Admin can only update 'pending' requests.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PermintaanBarang $permintaan_barang
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, PermintaanBarang $permintaan_barang)
    {
        // Admin hanya bisa mengupdate permintaan yang statusnya 'pending'
        if ($permintaan_barang->status !== 'pending') {
            return redirect()->route('admin.permintaan.showGroupedByDate', [
                'tanggal' => $permintaan_barang->created_at->format('Y-m-d')
            ])->with('error', 'Permintaan ini tidak dapat diperbarui karena statusnya bukan pending.');
        }

        $request->validate([
            'barang_id' => 'required|exists:barang,id', // Admin bisa mengubah barang
            'jumlah' => 'required|integer|min:1',
            'alasan' => 'nullable|string',
        ]);

        // Perbarui record permintaan
        $permintaan_barang->update($request->only(['barang_id', 'jumlah', 'alasan']));

        // Redirect kembali ke halaman detail kelompok setelah update
        return redirect()->route('admin.permintaan.showGroupedByDate', [
            'tanggal' => $permintaan_barang->created_at->format('Y-m-d')
        ])->with('success', 'Permintaan barang berhasil diperbarui oleh Admin.');
    }

    /**
     * Approve an item request and reduce item stock.
     *
     * @param int $id ID of the item request.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        // Start a database transaction to ensure data consistency
        DB::beginTransaction();

        try {
            $permintaan = PermintaanBarang::findOrFail($id);

            // Ensure the request is still pending before approval
            if ($permintaan->status !== 'pending') {
                DB::rollBack();
                // Redirect to the grouped show page if possible, otherwise to index
                $redirectRoute = route('admin.permintaan.showGroupedByDate', ['tanggal' => $permintaan->created_at->format('Y-m-d')]);
                return redirect($redirectRoute)->with('error', 'Permintaan ini sudah tidak pending.');
            }

            $barang = $permintaan->barang; // Access the item relationship

            // Check if the item is found
            if (!$barang) {
                DB::rollBack();
                $redirectRoute = route('admin.permintaan.showGroupedByDate', ['tanggal' => $permintaan->created_at->format('Y-m-d')]);
                return redirect($redirectRoute)->with('error', 'Barang terkait tidak ditemukan.');
            }

            // Check available item stock
            if ($barang->stok < $permintaan->jumlah) {
                DB::rollBack();
                $redirectRoute = route('admin.permintaan.showGroupedByDate', ['tanggal' => $permintaan->created_at->format('Y-m-d')]);
                return redirect($redirectRoute)->with('error', 'Stok ' . $barang->nama_barang . ' tidak mencukupi. Hanya tersedia ' . $barang->stok . '.');
            }

            // Reduce item stock
            $barang->stok -= $permintaan->jumlah;
            $barang->save(); // Save item stock changes

            // Change request status to 'approved'
            $permintaan->status = 'disetujui';
            $permintaan->save(); // Save request status changes

            // Record in BarangKeluar
            BarangKeluar::create([
                'permintaan_id' => $permintaan->id,
                'barang_id' => $permintaan->barang_id,
                'jumlah_keluar' => $permintaan->jumlah,
                'tanggal_keluar' => Carbon::now(),
                'keterangan' => 'Disetujui dari permintaan ID: ' . $permintaan->id . ' oleh ' . (Auth::user()->name ?? 'Admin'), 
            ]);

            DB::commit(); // Commit the transaction if all operations are successful

            $redirectRoute = route('admin.permintaan.showGroupedByDate', ['tanggal' => $permintaan->created_at->format('Y-m-d')]);
            return redirect($redirectRoute)->with('success', 'Permintaan disetujui dan stok barang diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction if an error occurs
            Log::error("Error approving request ID {$id}: " . $e->getMessage()); // Log the error for debugging
            // Try to redirect back to the grouped view if possible, otherwise to index
            $permintaan = PermintaanBarang::find($id);
            if ($permintaan) {
                $redirectRoute = route('admin.permintaan.showGroupedByDate', ['tanggal' => $permintaan->created_at->format('Y-m-d')]);
            } else {
                $redirectRoute = route('admin.permintaan.index');
            }
            return redirect($redirectRoute)->with('error', 'Terjadi kesalahan saat menyetujui permintaan: ' . $e->getMessage());
        }
    }

    /**
     * Reject an item request with a reason.
     *
     * @param \Illuminate\Http\Request $request HTTP request object.
     * @param int $id ID of the item request.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string',
        ]);

        $permintaan = PermintaanBarang::findOrFail($id);

        // Ensure the request is still pending before rejection
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
