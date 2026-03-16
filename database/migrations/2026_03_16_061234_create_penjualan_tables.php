<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Tabel penjualan (header transaksi)
        Schema::create('penjualan', function (Blueprint $table) {
            $table->increments('id_penjualan');     // PK auto-increment
            $table->timestamp('timestamp')->useCurrent(); // waktu transaksi
            $table->integer('total');               // total harga
        });
 
        // ── Tabel penjualan_detail (baris item transaksi)
        Schema::create('penjualan_detail', function (Blueprint $table) {
            $table->increments('idpenjualan_detail'); // PK
            $table->integer('id_penjualan');          // FK → penjualan
            $table->string('id_barang', 8);           // FK → barang
            $table->smallInteger('jumlah');           // qty
            $table->integer('subtotal');              // harga × jumlah
 
            // Foreign keys
            $table->foreign('id_penjualan')
                  ->references('id_penjualan')
                  ->on('penjualan')
                  ->onDelete('cascade');
 
            $table->foreign('id_barang')
                  ->references('id_barang')
                  ->on('barang')
                  ->onDelete('restrict');
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('penjualan_detail');
        Schema::dropIfExists('penjualan');
    }
};
