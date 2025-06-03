<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    protected $table = 'barang_keluar';

    protected $fillable = [
        'permintaan_id',
        'barang_id',
        'jumlah_keluar',
        'tanggal_keluar',
        'keterangan',
    ];

    // Relasi ke permintaan barang
    public function permintaan()
    {
        return $this->belongsTo(PermintaanBarang::class, 'permintaan_id');
    }

    // Relasi ke barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
