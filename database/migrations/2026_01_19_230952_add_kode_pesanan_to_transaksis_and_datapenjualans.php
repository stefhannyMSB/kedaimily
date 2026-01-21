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
        Schema::table('transaksis', function (Blueprint $table) {
            $table->string('kode_pesanan', 50)->nullable()->after('id_transaksi');
            $table->index('kode_pesanan');
        });

        Schema::table('datapenjualans', function (Blueprint $table) {
            $table->string('kode_pesanan', 50)->nullable()->after('id_datapenjualan');
            $table->index('kode_pesanan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropIndex(['kode_pesanan']);
            $table->dropColumn('kode_pesanan');
        });

        Schema::table('datapenjualans', function (Blueprint $table) {
            $table->dropIndex(['kode_pesanan']);
            $table->dropColumn('kode_pesanan');
        });
    }
};
