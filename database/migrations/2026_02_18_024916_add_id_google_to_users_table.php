<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            if (!Schema::hasColumn('users', 'id_google')) {
                $table->string('id_google', 256)->nullable();
            }

            if (!Schema::hasColumn('users', 'otp')) {
                $table->string('otp', 6)->nullable();
            }

            if (!Schema::hasColumn('users', 'otp_expires_at')) {
                $table->timestamp('otp_expires_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['id_google', 'otp', 'otp_expires_at']);
        });
    }
};
