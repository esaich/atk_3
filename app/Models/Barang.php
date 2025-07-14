<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    // Hapus 'supplier_id' dan 'keterangan' dari $fillable
    protected $fillable = ['kode_barang', 'nama_barang', 'stok', 'satuan']; 

    // Hapus relasi supplier() karena kolom supplier_id sudah tidak ada
    // public function supplier()
    // {
    //     return $this->belongsTo(Supplier::class);
    // }

    public function permintaanBarang()
    {
        return $this->hasMany(PermintaanBarang::class);
    }

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class);
    }

    public function barangKeluar()
    {
        return $this->hasMany(BarangKeluar::class);
    }
}