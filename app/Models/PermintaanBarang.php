<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\User;
use App\Models\Barang;
use App\Models\BarangKeluar;

class PermintaanBarang extends Model
{
    protected $table = 'permintaan_barang';

    protected $fillable = [
        'user_id',
        'barang_id',
        'jumlah',
        'status',
        'alasan',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke User (pemohon divisi)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Barang yang diminta
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    // Relasi ke BarangKeluar (jika sudah diproses keluar)
    public function barangKeluar(): HasOne
    {
        return $this->hasOne(BarangKeluar::class, 'permintaan_id');
    }
}
