<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('barang', function (Blueprint $table) {
        $table->id();
        $table->string('kode_barang')->unique();
        $table->string('nama_barang');
        $table->integer('stok')->default(0); // stok default 0
        $table->string('satuan');
        $table->text('keterangan')->nullable();
        $table->foreignId('supplier_id')->nullable()->constrained('supplier')->onDelete('set null');
        $table->timestamps();
});

    }

    public function down()
    {
        Schema::dropIfExists('barang');
    }
};
