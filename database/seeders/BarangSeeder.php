<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $barangs = [
            ['kode_barang' => 'ATK-001', 'nama_barang' => 'Bolpoin Hitam Standard', 'stok' => 500, 'satuan' => 'pcs'],
            ['kode_barang' => 'ATK-002', 'nama_barang' => 'Bolpoin Biru Standard', 'stok' => 450, 'satuan' => 'pcs'],
            ['kode_barang' => 'ATK-003', 'nama_barang' => 'Pensil 2B', 'stok' => 300, 'satuan' => 'pcs'],
            ['kode_barang' => 'ATK-004', 'nama_barang' => 'Penghapus Karet', 'stok' => 200, 'satuan' => 'pcs'],
            ['kode_barang' => 'ATK-005', 'nama_barang' => 'Penggaris 30 cm', 'stok' => 150, 'satuan' => 'pcs'],
            ['kode_barang' => 'ATK-006', 'nama_barang' => 'Kertas A4 80gsm', 'stok' => 50, 'satuan' => 'rim'],
            ['kode_barang' => 'ATK-007', 'nama_barang' => 'Kertas F4 80gsm', 'stok' => 40, 'satuan' => 'rim'],
            ['kode_barang' => 'ATK-008', 'nama_barang' => 'Map Folder Kuning', 'stok' => 100, 'satuan' => 'pcs'],
            ['kode_barang' => 'ATK-009', 'nama_barang' => 'Tipe-X / Correction Tape', 'stok' => 80, 'satuan' => 'pcs'],
            ['kode_barang' => 'ATK-010', 'nama_barang' => 'Stapler Besar', 'stok' => 25, 'satuan' => 'pcs'],
            ['kode_barang' => 'ATK-011', 'nama_barang' => 'Isi Staples No. 10', 'stok' => 60, 'satuan' => 'box'],
            ['kode_barang' => 'ATK-012', 'nama_barang' => 'Lem Kertas Stick', 'stok' => 70, 'satuan' => 'pcs'],
            ['kode_barang' => 'ATK-013', 'nama_barang' => 'Spidol Permanent Hitam', 'stok' => 40, 'satuan' => 'pcs'],
            ['kode_barang' => 'ATK-014', 'nama_barang' => 'Spidol Whiteboard Biru', 'stok' => 35, 'satuan' => 'pcs'],
            ['kode_barang' => 'ATK-015', 'nama_barang' => 'Clipboard A4', 'stok' => 30, 'satuan' => 'pcs'],
        ];

        foreach ($barangs as $barang) {
            Barang::create($barang);
        }
    }
}