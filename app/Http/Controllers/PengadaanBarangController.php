<?php

namespace App\Http\Controllers;

use App\Models\PengadaanBarang;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PengadaanBarangController extends Controller
{
    public function index()
    {
        $pengadaanBarangs = PengadaanBarang::with('supplier')->orderBy('created_at', 'desc')->get();
        return $this->indexGrouped();
    }

    public function indexGrouped()
    {
        $allPengadaan = PengadaanBarang::with('supplier')
                                        ->orderBy('tanggal_pengajuan', 'desc')
                                        ->orderBy('supplier_id', 'asc')
                                        ->get();

        $groupedPengadaan = $allPengadaan->groupBy(function($item) {
            return $item->supplier_id . '_' . $item->tanggal_pengajuan->format('Y-m-d');
        })->map(function($group) {
            $firstItem = $group->first();
            return [
                'supplier' => $firstItem->supplier,
                'tanggal_pengajuan' => $firstItem->tanggal_pengajuan,
                'items' => $group,
            ];
        });

        return view('pengadaan.index', compact('groupedPengadaan'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('pengadaan.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items_data' => 'required|json',
        ]);

        $items = json_decode($request->items_data, true);

        if (empty($items)) {
            return redirect()->back()->withInput()->withErrors(['items_data' => 'Tidak ada item pengajuan yang ditambahkan.']);
        }

        $errors = [];

        foreach ($items as $item) {
            $validator = Validator::make($item, [
                'nama_barang' => 'required|string|max:255',
                'satuan' => 'nullable|string|max:100',
                'jumlah_diajukan' => 'required|integer|min:1',
                'tanggal_pengajuan' => 'required|date',
                'keterangan' => 'nullable|string',
                'supplier_id' => 'required|exists:supplier,id',
            ]);

            if ($validator->fails()) {
                $errors[] = "Validasi gagal untuk item '" . ($item['nama_barang'] ?? 'tidak diketahui') . "': " . implode(', ', $validator->errors()->all());
                continue;
            }

            PengadaanBarang::create([
                'nama_barang' => $item['nama_barang'],
                'satuan' => $item['satuan'] ?? null,
                'jumlah_diajukan' => $item['jumlah_diajukan'],
                'tanggal_pengajuan' => $item['tanggal_pengajuan'],
                'keterangan' => $item['keterangan'] ?? null,
                'supplier_id' => $item['supplier_id'],
            ]);
        }

        if (!empty($errors)) {
            return redirect()->route('pengadaan.index')->with('error', 'Beberapa pengajuan gagal diajukan: ' . implode('; ', $errors));
        }

        return redirect()->route('pengadaan.index')->with('success', 'Semua pengajuan pengadaan barang berhasil diajukan.');
    }

    public function show(PengadaanBarang $pengadaanBarang)
    {
        $pengadaanBarang->load('supplier');
        return view('pengadaan.show', compact('pengadaanBarang'));
    }

    public function edit(PengadaanBarang $pengadaanBarang)
    {
        $suppliers = Supplier::all();
        return view('pengadaan.edit', compact('pengadaanBarang', 'suppliers'));
    }

    public function update(Request $request, PengadaanBarang $pengadaanBarang)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'nullable|string|max:100',
            'jumlah_diajukan' => 'required|integer|min:1',
            'tanggal_pengajuan' => 'required|date',
            'keterangan' => 'nullable|string',
            'supplier_id' => 'required|exists:supplier,id',
        ]);

        $pengadaanBarang->update($validated);

        return redirect()->route('pengadaan.index')->with('success', 'Pengajuan pengadaan barang berhasil diperbarui.');
    }

    public function destroy(PengadaanBarang $pengadaanBarang)
    {
        $pengadaanBarang->delete();

        return redirect()->route('pengadaan.index')->with('success', 'Pengajuan pengadaan barang berhasil dihapus.');
    }

    public function downloadPdf(PengadaanBarang $pengadaanBarang)
    {
        $pengadaanBarang->load('supplier');
        $pdf = Pdf::loadView('pengadaan.pdf_template', compact('pengadaanBarang'));
        return $pdf->download('laporan-pengadaan-item-' . $pengadaanBarang->id . '.pdf');
    }

    public function groupedShow(Supplier $supplier, string $tanggal_pengajuan)
    {
        $pengadaanItems = PengadaanBarang::where('supplier_id', $supplier->id)
                                        ->whereDate('tanggal_pengajuan', $tanggal_pengajuan)
                                        ->orderBy('nama_barang', 'asc')
                                        ->get();

        return view('pengadaan.grouped_show', compact('supplier', 'tanggal_pengajuan', 'pengadaanItems'));
    }

    public function groupedDestroy(Supplier $supplier, string $tanggal_pengajuan)
    {
        DB::beginTransaction();
        try {
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

    public function downloadPdfGrouped(Supplier $supplier, string $tanggal_pengajuan)
    {
        $pengadaanItems = PengadaanBarang::where('supplier_id', $supplier->id)
                                        ->whereDate('tanggal_pengajuan', $tanggal_pengajuan)
                                        ->orderBy('nama_barang', 'asc')
                                        ->get();

        $pdf = Pdf::loadView('pengadaan.pdf_template', compact('supplier', 'pengadaanItems', 'tanggal_pengajuan'));
        return $pdf->download('laporan-pengadaan-' . $supplier->nama_supplier . '-' . $tanggal_pengajuan . '.pdf');
    }
}