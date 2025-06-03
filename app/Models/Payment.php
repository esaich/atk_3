<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['supplier_id', 'total_harga', 'tanggal_bayar', 'keterangan'];

    protected $casts = [
        'tanggal_bayar' => 'datetime',  // ini yang penting
    ];
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class);
    }
}
