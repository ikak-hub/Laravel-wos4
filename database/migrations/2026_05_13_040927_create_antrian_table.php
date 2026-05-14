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
        Schema::create('antrian', function (Blueprint $table) {
            $table->id('idantrian');
            $table->string('nomor_antrian', 10);   // e.g. A001, A002
            $table->string('nama', 255);
            // 0=menunggu, 1=dipanggil/dilayani, 2=terlambat/tidak hadir
            $table->tinyInteger('status')->default(0);
            $table->integer('loket')->nullable();  // loket yang dituju saat dipanggil
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antrian');
    }
};
