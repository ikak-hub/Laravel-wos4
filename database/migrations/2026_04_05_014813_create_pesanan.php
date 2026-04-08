<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('pesanan', function (Blueprint $table) {
            $table->id('idpesanan');
            $table->string('nama', 255);                          // Guest_0000001
            $table->integer('total');
            $table->string('metode_bayar', 100)->nullable();      // qris, bank_transfer, dst
            $table->smallInteger('status_bayar')->default(0);     // 0=pending, 1=lunas, 2=batal
            $table->string('snap_token', 1000)->nullable();
            $table->string('midtrans_order_id', 100)->nullable()->unique();
            $table->unsignedBigInteger('idvendor');
            $table->foreign('idvendor')->references('idvendor')->on('vendors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
