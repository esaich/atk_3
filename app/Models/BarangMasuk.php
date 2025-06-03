<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    protected $table = 'barang_masuk';
    protected $fillable = ['barang_id', 'payment_id', 'jumlah_masuk', 'harga_satuan', 'tanggal_masuk'];
    protected $casts = [
        'tanggal_masuk' => 'datetime',  // pastikan ini ada
    ];
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
