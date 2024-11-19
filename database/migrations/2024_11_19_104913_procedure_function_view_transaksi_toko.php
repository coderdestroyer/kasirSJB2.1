<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProcedureFunctionViewTransaksiToko extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Procedure: save_transaction
        DB::unprepared('
            CREATE PROCEDURE save_transaction(
                IN user_id INT,
                IN invoice_number VARCHAR(255),
                IN cashier_id INT,
                IN transaction_date DATE,
                IN transaction_details JSON
            )
            BEGIN
                DECLARE last_transaction_id INT;
                DECLARE json_length INT;
                DECLARE i INT DEFAULT 0;

                -- Insert into penjualan
                INSERT INTO penjualan (id_user, nomor_invoice, id_kasir, tanggal_penjualan)
                VALUES (user_id, invoice_number, cashier_id, transaction_date);

                SET last_transaction_id = LAST_INSERT_ID();

                -- Calculate JSON array length
                SET json_length = JSON_LENGTH(transaction_details);

                -- Loop through JSON array
                WHILE i < json_length DO
                    INSERT INTO penjualan_detail (id_penjualan, id_produk, harga_jual_produk, jumlah)
                    VALUES (
                        last_transaction_id,
                        JSON_UNQUOTE(JSON_EXTRACT(transaction_details, CONCAT(\'$[\', i, \'].product_id\'))),
                        JSON_UNQUOTE(JSON_EXTRACT(transaction_details, CONCAT(\'$[\', i, \'].price\'))),
                        JSON_UNQUOTE(JSON_EXTRACT(transaction_details, CONCAT(\'$[\', i, \'].quantity\')))
                    );
                    SET i = i + 1;
                END WHILE;

                -- Update stock in detail_produk
                UPDATE detail_produk dp
                JOIN penjualan_detail pd ON dp.id_produk = pd.id_produk
                SET dp.stok_produk = dp.stok_produk - pd.jumlah
                WHERE pd.id_penjualan = last_transaction_id;
            END;
        ');

        // Function: calculate_subtotal
        DB::unprepared('
            CREATE FUNCTION calculate_subtotal(price DECIMAL(10, 2), quantity INT)
            RETURNS DECIMAL(10, 2)
            DETERMINISTIC
            BEGIN
                RETURN price * quantity;
            END;
        ');

        // View: view_transaction_summary
        DB::unprepared('
            CREATE VIEW view_transaction_summary AS
            SELECT 
                p.id_penjualan AS transaction_id,
                p.nomor_invoice AS invoice_number,
                u.name AS user_name,
                k.name AS cashier_name,
                p.tanggal_penjualan AS transaction_date,
                SUM(pd.harga_jual_produk * pd.jumlah) AS total_amount
            FROM penjualan p
            JOIN penjualan_detail pd ON p.id_penjualan = pd.id_penjualan
            JOIN users u ON p.id_user = u.id
            JOIN users k ON p.id_kasir = k.id
            GROUP BY p.id_penjualan, p.nomor_invoice, u.name, k.name, p.tanggal_penjualan;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the view
        DB::unprepared('DROP VIEW IF EXISTS view_transaction_summary');

        // Drop the function
        DB::unprepared('DROP FUNCTION IF EXISTS calculate_subtotal');

        // Drop the procedure
        DB::unprepared('DROP PROCEDURE IF EXISTS save_transaction');
    }
}
