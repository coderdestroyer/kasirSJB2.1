<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HapusSubtotalGantiIdPembelianDiPembelianDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pembelian_detail', function (Blueprint $table) {
            // Menghapus kolom subtotal
            $table->dropColumn('subtotal');

            // Menambahkan kolom nama_produk
            $table->string('nama_produk')->after('id_pembelian');

            // Menghapus kolom id_produk
            $table->dropColumn('id_produk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pembelian_detail', function (Blueprint $table) {
            // Mengembalikan kolom id_produk
            $table->unsignedBigInteger('id_produk')->after('id_pembelian');

            // Menghapus kolom nama_produk
            $table->dropColumn('nama_produk');

            // Menambahkan kembali kolom subtotal
            $table->decimal('subtotal', 15, 2)->after('status');
        });
    }
}
