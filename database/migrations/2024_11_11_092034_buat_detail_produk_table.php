<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BuatDetailProdukTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_produk', function (Blueprint $table) {
            $table->increments('id_detail_produk');
            $table->unsignedInteger('id_produk');
            $table->integer('stok_produk');
            $table->string('merk')->nullable();
            $table->integer('harga_beli_produk');
            $table->timestamps();

            // Tambahkan foreign key jika diperlukan
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_produk');
    }
}
