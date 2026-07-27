<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo untuk relasi
use App\Models\User;
use App\Models\BarangMasuk;

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
        'status',
        'approved_by',
        'approved_at',
        'catatan_approval',
    ];

    // Mengonversi atribut tanggal_pengajuan ke objek Carbon (tanggal saja)
    // dan timestamps ke objek Carbon (tanggal dan waktu)
    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'approved_at' => 'datetime',
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

    /**
     * User (role bendahara) yang menyetujui/menolak pengajuan ini.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Semua barang masuk yang dicatat berdasarkan pengajuan pengadaan ini.
     */
    public function barangMasuks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BarangMasuk::class, 'pengadaan_barang_id');
    }

    /**
     * Total barang yang sudah benar-benar diterima (dicatat lewat barang masuk)
     * berdasarkan pengajuan ini.
     */
    public function getJumlahDiterimaAttribute(): int
    {
        return (int) $this->barangMasuks()->sum('jumlah_masuk');
    }

    /**
     * Sisa jumlah yang masih boleh diterima sebelum melebihi jumlah yang diajukan.
     */
    public function getSisaJumlahAttribute(): int
    {
        return max(0, $this->jumlah_diajukan - $this->jumlah_diterima);
    }
}