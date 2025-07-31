<?php

namespace App\Http\Controllers;

use App\Models\PengadaanBarang; // Import model PengadaanBarang
use App\Models\Supplier;       // Import model Supplier untuk dropdown
use Illuminate\Http\Request;
use Carbon\Carbon;             // Untuk penanganan tanggal
use Barryvdh\DomPDF\Facade\Pdf; // Untuk fungsionalitas PDF
use Illuminate\Support\Facades\DB; // Untuk transaksi database
use Illuminate\Support\Facades\Validator; // Import Validator untuk validasi item individual

class PengadaanBarangController extends Controller
{
    /**
     * Menampilkan daftar semua pengajuan pengadaan barang (per item).
     * Metode ini tetap ada untuk CRUD per item jika diperlukan.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Ambil semua catatan pengadaan barang, eager load supplier, diurutkan berdasarkan tanggal pengajuan terbaru
        $pengadaanBarangs = PengadaanBarang::with('supplier')->orderBy('created_at', 'desc')->get();

        // Kita akan menggunakan metode indexGrouped() untuk tampilan yang dikelompokkan
        // return view('pengadaan.index', compact('pengadaanBarangs'));
        // Untuk saat ini, kita akan langsung mengarahkan ke indexGrouped
        return $this->indexGrouped();
    }

    /**
     * Menampilkan daftar pengajuan pengadaan barang yang dikelompokkan
     * berdasarkan Supplier dan Tanggal Pengajuan.
     *
     * @return \Illuminate\View\View
     */
    public function indexGrouped()
    {
        // Mengambil pengadaan barang dan mengelompokkannya
        // Kita perlu mengambil semua data, lalu mengelompokkannya di PHP
        $allPengadaan = PengadaanBarang::with('supplier')
                                        ->orderBy('tanggal_pengajuan', 'desc')
                                        ->orderBy('supplier_id', 'asc')
                                        ->get();

        $groupedPengadaan = $allPengadaan->groupBy(function($item) {
            return $item->supplier_id . '_' . $item->tanggal_pengajuan->format('Y-m-d');
        })->map(function($group) {
            // Untuk setiap kelompok (supplier_id_tanggal), ambil supplier pertama dan tanggal
            $firstItem = $group->first();
            return [
                'supplier' => $firstItem->supplier,
                'tanggal_pengajuan' => $firstItem->tanggal_pengajuan,
                'items' => $group, // Koleksi semua item pengadaan dalam kelompok ini
            ];
        });

        // Mengirim data yang sudah dikelompokkan ke view
        return view('pengadaan.index', compact('groupedPengadaan'));
    }


    /**
     * Menampilkan form untuk membuat pengajuan pengadaan barang baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $suppliers = Supplier::all(); // Ambil semua supplier untuk dropdown
        return view('pengadaan.create', compact('suppliers'));
    }

    /**
     * Menyimpan pengajuan pengadaan barang baru ke database.
     * Sekarang mendukung pengajuan multi-item melalui JSON.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi utama untuk memastikan ada data JSON yang dikirim
        $request->validate([
            'items_data' => 'required|json', // Validasi bahwa ini adalah JSON string
        ]);

        $items = json_decode($request->items_data, true); // Decode JSON string menjadi array PHP

        // Jika array item kosong setelah decode
        if (empty($items)) {
            return redirect()->back()->withInput()->withErrors(['items_data' => 'Tidak ada item pengajuan yang ditambahkan.']);
        }

        $errors = []; // Array untuk mengumpulkan error validasi per item

        // Loop melalui setiap item dalam array dan simpan ke database
        foreach ($items as $item) {
            // Validasi setiap item dalam array
            $validator = Validator::make($item, [
                'nama_barang' => 'required|string|max:255',
                'satuan' => 'nullable|string|max:100',
                'jumlah_diajukan' => 'required|integer|min:1',
                'tanggal_pengajuan' => 'required|date',
                'keterangan' => 'nullable|string',
                'supplier_id' => 'required|exists:supplier,id',
            ]);

            if ($validator->fails()) {
                // Kumpulkan pesan error untuk item yang gagal
                $errors[] = "Validasi gagal untuk item '" . ($item['nama_barang'] ?? 'tidak diketahui') . "': " . implode(', ', $validator->errors()->all());
                continue; // Lanjutkan ke item berikutnya jika validasi gagal
            }

            // Buat record PengadaanBarang baru untuk setiap item
            PengadaanBarang::create([
                'nama_barang' => $item['nama_barang'],
                'satuan' => $item['satuan'] ?? null,
                'jumlah_diajukan' => $item['jumlah_diajukan'],
                'tanggal_pengajuan' => $item['tanggal_pengajuan'],
                'keterangan' => $item['keterangan'] ?? null,
                'supplier_id' => $item['supplier_id'],
            ]);
        }

        // Jika ada error dari beberapa item, kembalikan dengan pesan error gabungan
        if (!empty($errors)) {
            return redirect()->route('pengadaan.index')->with('error', 'Beberapa pengajuan gagal diajukan: ' . implode('; ', $errors));
        }

        // Jika semua item berhasil disimpan
        return redirect()->route('pengadaan.index')->with('success', 'Semua pengajuan pengadaan barang berhasil diajukan.');
    }

    /**
     * Menampilkan detail pengajuan pengadaan barang tertentu (per item).
     *
     * @param \App\Models\PengadaanBarang $pengadaanBarang
     * @return \Illuminate\View\View
     */
    public function show(PengadaanBarang $pengadaanBarang)
    {
        // Eager load supplier untuk detail
        $pengadaanBarang->load('supplier');
        return view('pengadaan.show', compact('pengadaanBarang'));
    }

    /**
     * Menampilkan form untuk mengedit pengajuan pengadaan barang (per item).
     *
     * @param \App\Models\PengadaanBarang $pengadaanBarang
     * @return \Illuminate\View\View
     */
    public function edit(PengadaanBarang $pengadaanBarang)
    {
        $suppliers = Supplier::all(); // Ambil semua supplier untuk dropdown
        return view('pengadaan.edit', compact('pengadaanBarang', 'suppliers'));
    }

    /**
     * Memperbarui pengajuan pengadaan barang yang sudah ada di database (per item).
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PengadaanBarang $pengadaanBarang
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, PengadaanBarang $pengadaanBarang)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'nullable|string|max:100',
            'jumlah_diajukan' => 'required|integer|min:1',
            'tanggal_pengajuan' => 'required|date',
            'keterangan' => 'nullable|string',
            'supplier_id' => 'required|exists:supplier,id', // Validasi supplier_id
        ]);

        $pengadaanBarang->update($request->all());

        return redirect()->route('pengadaan.index')->with('success', 'Pengajuan pengadaan barang berhasil diperbarui.');
    }

    /**
     * Menghapus pengajuan pengadaan barang dari database (per item).
     *
     * @param \App\Models\PengadaanBarang $pengadaanBarang
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(PengadaanBarang $pengadaanBarang)
    {
        $pengadaanBarang->delete();

        return redirect()->route('pengadaan.index')->with('success', 'Pengajuan pengadaan barang berhasil dihapus.');
    }

    /**
     * Mengunduh laporan PDF untuk detail satu pengajuan pengadaan barang (per item).
     * Metode ini tetap ada untuk PDF per item jika diperlukan.
     *
     * @param \App\Models\PengadaanBarang $pengadaanBarang Objek PengadaanBarang yang akan dibuat PDF-nya.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadPdf(PengadaanBarang $pengadaanBarang)
    {
        // Eager load supplier untuk PDF
        $pengadaanBarang->load('supplier');

        // Muat view yang akan dikonversi menjadi PDF.
        // Disarankan untuk membuat view khusus untuk PDF (misalnya 'pengadaan.pdf_template')
        // agar Anda memiliki kontrol penuh atas tata letak cetak tanpa elemen interaktif web.
        $pdf = Pdf::loadView('pengadaan.pdf_template', compact('pengadaanBarang'));

        // Opsional: Atur ukuran kertas dan orientasi (misal: A4, portrait atau landscape)
        // $pdf->setPaper('A4', 'portrait');

        // Mengunduh PDF dengan nama file yang deskriptif
        return $pdf->download('laporan-pengadaan-item-' . $pengadaanBarang->id . '.pdf');
    }

    /**
     * Menampilkan detail pengajuan pengadaan barang yang dikelompokkan
     * berdasarkan Supplier dan Tanggal Pengajuan.
     *
     * @param \App\Models\Supplier $supplier Objek Supplier.
     * @param string $tanggal_pengajuan Tanggal pengajuan dalam format YYYY-MM-DD.
     * @return \Illuminate\View\View
     */
    public function groupedShow(Supplier $supplier, string $tanggal_pengajuan)
    {
        // Ambil semua item pengadaan barang untuk supplier dan tanggal tertentu
        $pengadaanItems = PengadaanBarang::where('supplier_id', $supplier->id)
                                        ->whereDate('tanggal_pengajuan', $tanggal_pengajuan)
                                        ->orderBy('nama_barang', 'asc')
                                        ->get();

        // Mengirim data ke view baru untuk detail kelompok
        return view('pengadaan.grouped_show', compact('supplier', 'tanggal_pengajuan', 'pengadaanItems'));
    }

    /**
     * Menghapus seluruh pengajuan pengadaan barang untuk Supplier dan Tanggal tertentu.
     *
     * @param \App\Models\Supplier $supplier Objek Supplier.
     * @param string $tanggal_pengajuan Tanggal pengajuan dalam format YYYY-MM-DD.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function groupedDestroy(Supplier $supplier, string $tanggal_pengajuan)
    {
        DB::beginTransaction();
        try {
            // Hapus semua item pengadaan barang untuk supplier dan tanggal tertentu
            PengadaanBarang::where('supplier_id', $supplier->id)
                           ->whereDate('tanggal_pengajuan', $tanggal_pengajuan)
                           ->delete();

            DB::commit();
            return redirect()->route('pengadaan.index')->with('success', 'Seluruh pengajuan pengadaan dari supplier ' . $supplier->nama_supplier . ' pada tanggal ' . Carbon::parse($tanggal_pengajuan)->format('d-m-Y') . ' berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pengadaan.index')->with('error', 'Gagal menghapus pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Mengunduh laporan PDF untuk pengajuan pengadaan barang yang dikelompokkan
     * berdasarkan Supplier dan Tanggal Pengajuan.
     *
     * @param \App\Models\Supplier $supplier Objek Supplier.
     * @param string $tanggal_pengajuan Tanggal pengajuan dalam format YYYY-MM-DD.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadPdfGrouped(Supplier $supplier, string $tanggal_pengajuan)
    {
        // Ambil semua item pengadaan barang untuk supplier dan tanggal tertentu
        $pengadaanItems = PengadaanBarang::where('supplier_id', $supplier->id)
                                        ->whereDate('tanggal_pengajuan', $tanggal_pengajuan)
                                        ->orderBy('nama_barang', 'asc')
                                        ->get();

        // Muat view PDF dengan data supplier dan item-itemnya
        $pdf = Pdf::loadView('pengadaan.pdf_template', compact('supplier', 'pengadaanItems', 'tanggal_pengajuan'));

        // Mengunduh PDF dengan nama file yang deskriptif
        return $pdf->download('laporan-pengadaan-' . $supplier->nama_supplier . '-' . $tanggal_pengajuan . '.pdf');
    }
}
