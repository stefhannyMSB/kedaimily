<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('penjualans', function (Blueprint $table) {
        $table->id('id_penjualan');
        $table->date('tanggal');
        $table->unsignedBigInteger('id_menu');
        $table->foreign('id_menu')->references('id_menu')->on('menus');
        $table->integer('jumlah');
        $table->decimal('total', 15, 2);
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('penjualans');
}

};