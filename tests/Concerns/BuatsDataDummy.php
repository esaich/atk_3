<?php

namespace Tests\Concerns;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\PengadaanBarang;
use App\Models\Supplier;
use App\Models\User;

trait BuatsDataDummy
{
    protected function buatAdmin(array $override = []): User
    {
        return User::factory()->create(array_merge(['role' => 'admin'], $override));
    }

    protected function buatDivisi(array $override = []): User
    {
        return User::factory()->create(array_merge(['role' => 'divisi'], $override));
    }

    protected function buatBendahara(array $override = []): User
    {
        return User::factory()->create(array_merge(['role' => 'bendahara'], $override));
    }

    protected function buatSupplier(array $override = []): Supplier
    {
        return Supplier::create(array_merge([
            'nama_supplier' => 'CV Sumber Makmur',
            'alamat' => 'Jl. Contoh No. 1',
            'telepon' => '081234567890',
            'email' => 'supplier'.uniqid().'@example.com',
        ], $override));
    }

    protected function buatBarang(array $override = []): Barang
    {
        return Barang::create(array_merge([
            'kode_barang' => 'BRG-'.uniqid(),
            'nama_barang' => 'Kertas A4',
            'stok' => 10,
            'satuan' => 'rim',
        ], $override));
    }

    protected function buatPengadaanBarang(array $override = []): PengadaanBarang
    {
        return PengadaanBarang::create(array_merge([
            'nama_barang' => 'Tinta Printer',
            'satuan' => 'box',
            'jumlah_diajukan' => 10,
            'tanggal_pengajuan' => now()->format('Y-m-d'),
            'keterangan' => null,
            'supplier_id' => $this->buatSupplier()->id,
            'status' => 'diajukan',
        ], $override));
    }

    protected function buatBarangMasuk(array $override = []): BarangMasuk
    {
        return BarangMasuk::create(array_merge([
            'barang_id' => $this->buatBarang()->id,
            'supplier_id' => $this->buatSupplier()->id,
            'jumlah_masuk' => 5,
            'harga_satuan' => 10000,
            'tanggal_masuk' => now()->format('Y-m-d'),
        ], $override));
    }
}