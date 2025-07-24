<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo untuk relasi

class PengadaanBarang extends Model
{
    // Menentukan nama tabel yang terkait dengan model ini
    protected $table = 'pengadaan_barang';

    // Atribut yang dapat diisi secara massal
    protected $fillable = [
        'nama_barang',
        'satuan',
        'jumlah_diajukan',
        'tanggal_pengajuan',
        'keterangan',
        'supplier_id', // Menambahkan supplier_id ke fillable
    ];

    // Mengonversi atribut tanggal_pengajuan ke objek Carbon (tanggal saja)
    // dan timestamps ke objek Carbon (tanggal dan waktu)
    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Mendefinisikan relasi "belongs to" ke model Supplier.
     * Setiap PengadaanBarang adalah milik satu Supplier.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
