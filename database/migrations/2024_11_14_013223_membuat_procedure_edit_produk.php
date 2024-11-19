<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MembuatProcedureEditProduk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure ="
        CREATE PROCEDURE update_produk(
            IN p_id_produk INT,
            IN p_nama_produk VARCHAR(255),
            IN p_harga_jual DECIMAL(10,2),
            IN p_id_kategori INT,
            IN p_stok_produk INT,
            IN p_merk VARCHAR(255),
            IN p_harga_beli_produk DECIMAL(10,2)
        )
        BEGIN
            UPDATE produk
            SET 
                nama_produk = p_nama_produk,
                harga_jual = p_harga_jual,
                id_kategori = p_id_kategori,
                updated_at = NOW()
            WHERE id_produk = p_id_produk;

            UPDATE detail_produk
            SET 
                stok_produk = p_stok_produk,
                merk = p_merk,
                harga_beli_produk = p_harga_beli_produk,
                updated_at = NOW()
            WHERE id_produk = p_id_produk;
        END;
        ";

        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS update_produk');
    }
}
