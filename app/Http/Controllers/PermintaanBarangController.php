<?php

namespace App\Http\Controllers;

use App\Models\PermintaanBarang;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; // <-- BARIS INI DITAMBAHKAN
use Carbon\Carbon; // Import Carbon untuk penanganan tanggal

class PermintaanBarangController extends Controller
{
    /**
     * Menampilkan daftar permintaan barang milik user divisi yang login,
     * dikelompokkan berdasarkan tanggal.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $userId = Auth::id(); // Mendapatkan ID user yang sedang login

        // Mengambil semua permintaan barang user yang login, eager load barang,
        // dan mengelompokkannya berdasarkan tanggal dibuat.
        $allPermintaans = PermintaanBarang::with('barang')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Mengelompokkan permintaan berdasarkan tanggal (tanpa waktu)
        $groupedPermintaans = $allPermintaans->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
        })->map(function($group) {
            // Untuk setiap kelompok tanggal, ambil tanggal dan item-itemnya
            return [
                'tanggal' => $group->first()->created_at, // Ambil tanggal dari item pertama di grup
                'items' => $group, // Koleksi semua permintaan dalam kelompok tanggal ini
            ];
        });

        // Mengirim data yang sudah dikelompokkan ke view
        return view('divisi.permintaan-barang.index', compact('groupedPermintaans'));
    }

    /**
     * Menampilkan detail permintaan barang untuk tanggal tertentu dari user yang login.
     *
     * @param string $tanggal Tanggal dalam format YYYY-MM-DD.
     * @return \Illuminate\View\View
     */
    public function showGroupedByDate(string $tanggal)
    {
        $userId = Auth::id(); // Mendapatkan ID user yang sedang login

        // Ambil semua permintaan barang untuk user dan tanggal tertentu
        $permintaanItems = PermintaanBarang::with('barang')
            ->where('user_id', $userId)
            ->whereDate('created_at', $tanggal)
            ->orderBy('created_at', 'asc')
            ->get();

        // Jika tidak ada item ditemukan, mungkin arahkan kembali atau tampilkan pesan error
        if ($permintaanItems->isEmpty()) {
            return redirect()->route('divisi.permintaan-barang.index')->with('error', 'Tidak ada permintaan ditemukan untuk tanggal tersebut.');
        }

        return view('divisi.permintaan-barang.grouped_show', compact('permintaanItems', 'tanggal'));
    }

    /**
     * Menampilkan form untuk membuat permintaan barang baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $barangs = Barang::all(); // Mengambil semua barang untuk dropdown
        return view('divisi.permintaan-barang.create', compact('barangs'));
    }

    /**
     * Menyimpan data permintaan barang baru ke database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'items_data' => 'required|json', // Validasi bahwa ini adalah JSON string
        ]);

        $items = json_decode($request->items_data, true); // Decode JSON string menjadi array PHP

        if (empty($items)) {
            return redirect()->back()->withInput()->withErrors(['items_data' => 'Tidak ada item permintaan yang ditambahkan.']);
        }

        $userId = Auth::id();
        $errors = [];

        foreach ($items as $item) {
            // Validasi setiap item dalam array
            $validator = Validator::make($item, [ // <-- PERBAIKAN DI SINI
                'barang_id' => 'required|exists:barang,id',
                'jumlah' => 'required|integer|min:1',
                'alasan' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $errors[] = "Validasi gagal untuk item barang ID " . ($item['barang_id'] ?? 'tidak diketahui') . ": " . implode(', ', $validator->errors()->all());
                continue; // Lanjutkan ke item berikutnya jika validasi gagal
            }

            // Buat record permintaan baru untuk setiap item
            PermintaanBarang::create([
                'user_id' => $userId,
                'barang_id' => $item['barang_id'],
                'jumlah' => $item['jumlah'],
                'alasan' => $item['alasan'] ?? null, // Gunakan null jika alasan kosong
                'status' => 'pending', // Set status awal permintaan menjadi pending
            ]);
        }

        if (!empty($errors)) {
            return redirect()->route('divisi.permintaan-barang.index')->with('error', 'Beberapa permintaan gagal diajukan: ' . implode('; ', $errors));
        }

        return redirect()->route('divisi.permintaan-barang.index')->with('success', 'Semua permintaan barang berhasil diajukan.');
    }

    /**
     * Menampilkan form untuk mengedit permintaan barang.
     *
     * @param \App\Models\PermintaanBarang $permintaan_barang
     * @return \Illuminate\View\View
     */
    public function edit(PermintaanBarang $permintaan_barang)
    {
        // Pastikan user yang login adalah pemilik permintaan ini
        $this->authorizeRequestOwner($permintaan_barang);

        $barangs = Barang::all(); // Mengambil semua barang untuk dropdown
        return view('divisi.permintaan-barang.edit', compact('permintaan_barang', 'barangs'));
    }

    /**
     * Memperbarui permintaan barang yang sudah ada di database.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PermintaanBarang $permintaan_barang
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, PermintaanBarang $permintaan_barang)
    {
        // Pastikan user yang login adalah pemilik permintaan ini
        $this->authorizeRequestOwner($permintaan_barang);

        // Validasi input dari form
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'alasan' => 'nullable|string',
        ]);

        // Perbarui record permintaan
        $permintaan_barang->update($request->only(['barang_id', 'jumlah', 'alasan']));

        return redirect()->route('divisi.permintaan-barang.index')->with('success', 'Permintaan barang berhasil diperbarui.');
    }

    /**
     * Membatalkan atau menghapus permintaan barang.
     *
     * @param \App\Models\PermintaanBarang $permintaan_barang
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(PermintaanBarang $permintaan_barang)
    {
        // Pastikan user yang login adalah pemilik permintaan ini
        $this->authorizeRequestOwner($permintaan_barang);

        $permintaan_barang->delete(); // Hapus record permintaan

        return redirect()->route('divisi.permintaan-barang.index')->with('success', 'Permintaan barang berhasil dibatalkan.');
    }

    /**
     * Helper pribadi untuk memastikan user hanya bisa mengubah permintaannya sendiri.
     *
     * @param \App\Models\PermintaanBarang $permintaan_barang
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function authorizeRequestOwner(PermintaanBarang $permintaan_barang)
    {
        if ($permintaan_barang->user_id !== Auth::id()) {
            // Jika user_id permintaan tidak cocok dengan user yang login, lempar error 403
            abort(403, 'Unauthorized action.');
        }
    }
}
