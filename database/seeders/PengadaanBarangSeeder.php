<?php

namespace Database\Seeders;

use App\Models\PengadaanBarang;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PengadaanBarangSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Supplier::all();
        $items = [
            ['nama_barang' => 'Laptop ASUS VivoBook', 'satuan' => 'unit'],
            ['nama_barang' => 'Printer Canon PIXMA', 'satuan' => 'unit'],
            ['nama_barang' => 'Tinta Printer Hitam', 'satuan' => 'box'],
            ['nama_barang' => 'Kursi Kantor Ergonomis', 'satuan' => 'pcs'],
            ['nama_barang' => 'Meja Kerja Standard', 'satuan' => 'pcs'],
            ['nama_barang' => 'Lemari Arsip Besi', 'satuan' => 'pcs'],
            ['nama_barang' => 'Kalkulator Scientific', 'satuan' => 'pcs'],
            ['nama_barang' => 'Paper Clip Kotak', 'satuan' => 'box'],
            ['nama_barang' => 'Post-it Notes 3x3', 'satuan' => 'pad'],
            ['nama_barang' => 'Tinta Stempel Merah', 'satuan' => 'botol'],
        ];

        foreach ($items as $item) {
            PengadaanBarang::create([
                'nama_barang' => $item['nama_barang'],
                'satuan' => $item['satuan'],
                'jumlah_diajukan' => rand(5, 30),
                'tanggal_pengajuan' => Carbon::now()->subDays(rand(1, 45)),
                'keterangan' => 'Pengajuan pengadaan untuk kebutuhan kantor',
                'supplier_id' => $suppliers->random()->id,
            ]);
        }
    }
}