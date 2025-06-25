<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Pastikan ini ada

class BarangMasuk extends Model
{
    protected $table = 'barang_masuk';

    protected $fillable = [
        'barang_id',
        'supplier_id', // <--- HARUS supplier_id
        'jumlah_masuk',
        'harga_satuan',
        'tanggal_masuk',
    ];

    // Tambahkan properti $casts untuk mengonversi tanggal_masuk ke objek Carbon
    protected $casts = [
        'tanggal_masuk' => 'date', // Mengonversi ke objek Carbon yang merepresentasikan tanggal saja
    ];

    // Relasi ke barang
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    // Relasi ke supplier
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // Jika Anda masih memiliki relasi payment() di BarangMasuk dan payment_id sudah dihapus dari DB,
    // maka Anda harus menghapus fungsi ini. Jika tidak, ia akan menyebabkan error.
    // public function payment()
    // {
    //     return $this->belongsTo(Payment::class, 'payment_id');
    // }
}
