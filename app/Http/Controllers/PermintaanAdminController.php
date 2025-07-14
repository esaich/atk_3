<?php

namespace App\Http\Controllers;

use App\Models\PermintaanBarang;
use App\Models\Barang;
use App\Models\BarangKeluar; // Import model BarangKeluar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; // <-- BARIS INI DITAMBAHKAN
use Carbon\Carbon; // Import Carbon for date/time handling

class PermintaanAdminController extends Controller
{
    /**
     * Display a listing of all item requests (pending, approved, rejected).
     * This method now serves as the main index to view all requests.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Retrieve all item requests along with related user and item data
        $permintaans = PermintaanBarang::with('user', 'barang')
            ->orderBy('created_at', 'desc') // Order by latest date
            ->get();

        // Pass the request data to the admin.permintaan.index view
        return view('admin.permintaan.index', compact('permintaans'));
    }

    // The previous history() method might no longer be needed if index() already displays all.
    // If you still want a separate page for "only pending" and "full history",
    // you can keep this history() method and the index() method would filter pending.
    // However, based on your request, the index() method will now display all.
    /*
    public function riwayat()
    {
        $permintaans = PermintaanBarang::with('user', 'barang')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.permintaan.hasil', compact('permintaans'));
    }
    */

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
                // After changing index() to display all, we might want to redirect to index()
                return redirect()->route('admin.permintaan.index')->with('error', 'This request is no longer pending.');
            }

            $barang = $permintaan->barang; // Access the item relationship

            // Check if the item is found
            if (!$barang) {
                DB::rollBack();
                return redirect()->route('admin.permintaan.index')->with('error', 'Related item not found.');
            }

            // Check available item stock
            if ($barang->stok < $permintaan->jumlah) {
                DB::rollBack();
                return redirect()->route('admin.permintaan.index')->with('error', 'Stock of ' . $barang->nama_barang . ' is insufficient. Only ' . $barang->stok . ' available.');
            }

            // Reduce item stock
            $barang->stok -= $permintaan->jumlah;
            $barang->save(); // Save item stock changes

            // Change request status to 'approved'
            $permintaan->status = 'disetujui';
            $permintaan->save(); // Save request status changes

            // --- NEW PART: Record in BarangKeluar ---
            BarangKeluar::create([
                'permintaan_id' => $permintaan->id,
                'barang_id' => $permintaan->barang_id,
                'jumlah_keluar' => $permintaan->jumlah,
                'tanggal_keluar' => Carbon::now(), // Current date and time
                // Menggunakan Auth::user()->name yang lebih eksplisit
                // Operator null coalescing (??) akan menangani kasus jika Auth::user() adalah null
                'keterangan' => 'Approved from request ID: ' . $permintaan->id . ' by ' . (Auth::user()->name ?? 'Admin'), 
            ]);
            // --- End NEW PART ---

            DB::commit(); // Commit the transaction if all operations are successful

            return redirect()->route('admin.permintaan.index')->with('success', 'Request approved and item stock updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction if an error occurs
            Log::error("Error approving request ID {$id}: " . $e->getMessage()); // Log the error for debugging
            return redirect()->route('admin.permintaan.index')->with('error', 'An error occurred while approving the request: ' . $e->getMessage());
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
            return redirect()->route('admin.permintaan.index')->with('error', 'This request is no longer pending.');
        }

        $permintaan->status = 'ditolak';
        $permintaan->alasan = $request->alasan;
        $permintaan->save();

        return redirect()->route('admin.permintaan.index')->with('success', 'Request rejected successfully.');
    }
}
