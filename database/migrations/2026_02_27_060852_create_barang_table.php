<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->string('id_barang', 8)->primary()->default('00000000');
            $table->string('nama', 50);
            $table->integer('harga');
            $table->timestamp('timestamp')->useCurrent();
        });
        DB::unprepared("
            CREATE OR REPLACE FUNCTION fn_generate_id_barang()
            RETURNS TRIGGER AS \$\$
            DECLARE
                nr INTEGER;
                today DATE := CURRENT_DATE;
            BEGIN
                SELECT COUNT(id_barang) + 1
                INTO nr
                FROM barang
                WHERE DATE(\"timestamp\") = today;

                NEW.id_barang := CONCAT(
                    TO_CHAR(today, 'YY'),
                    TO_CHAR(today, 'MM'),
                    TO_CHAR(today, 'DD'),
                    LPAD(nr::TEXT, 2, '0')
                );

                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        // 2. Buat trigger yang memanggil function di atas
        DB::unprepared("
            CREATE TRIGGER trigger_id_barang
            BEFORE INSERT ON barang
            FOR EACH ROW
            EXECUTE FUNCTION fn_generate_id_barang();
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trigger_id_barang ON barang');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_generate_id_barang()');
        Schema::dropIfExists('barang');
    }
};
