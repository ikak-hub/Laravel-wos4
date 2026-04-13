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
        Schema::create('customers', function (Blueprint $table) {
            $table->id('idcustomer');
            $table->string('nama', 255);
            $table->string('email', 255)->nullable();
            $table->longText('foto_blob')->nullable();    // untuk SK Tambah Customer 1
            $table->string('foto_path', 500)->nullable(); // untuk SK Tambah Customer 2
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
