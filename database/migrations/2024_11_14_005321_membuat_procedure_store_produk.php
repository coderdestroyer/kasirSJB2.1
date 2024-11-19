<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MembuatProcedureStoreProduk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure ="
        CREATE PROCEDURE store_produk(
            IN p_nama_produk VARCHAR(255),
            IN p_harga_jual DECIMAL(10,2),
            IN p_id_kategori INT,
            IN p_stok_produk INT,
            IN p_merk VARCHAR(255),
            IN p_harga_beli_produk DECIMAL(10,2)
        )
        BEGIN
            DECLARE v_kode_produk VARCHAR(255);
            DECLARE v_last_id INT;

            SELECT MAX(id_produk) INTO v_last_id FROM produk;

            SET v_kode_produk = CONCAT('P', LPAD(v_last_id + 1, 6, '0'));

            INSERT INTO produk (kode_produk, nama_produk, harga_jual, id_kategori, created_at, updated_at)
            VALUES (v_kode_produk, p_nama_produk, p_harga_jual, p_id_kategori, NOW(), NOW());

            SET v_last_id = LAST_INSERT_ID();

            INSERT INTO detail_produk (id_produk, stok_produk, merk, harga_beli_produk, created_at, updated_at)
            VALUES (v_last_id, p_stok_produk, p_merk, p_harga_beli_produk, NOW(), NOW());
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
        DB::unprepared('DROP PROCEDURE IF EXISTS store_produk');

    }
}