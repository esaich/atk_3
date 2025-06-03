<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'supplier';
    protected $fillable = ['nama_supplier', 'alamat', 'telepon', 'email'];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }


    public function barang()
    {
        return $this->hasMany(Barang::class);
    }
}
