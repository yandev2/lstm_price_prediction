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
        Schema::table('harga_pangans', function (Blueprint $table) {
            $table->index(['pasar_id', 'komoditas_id', 'id'], 'idx_harga_pangan_latest');
        });

        Schema::table('distribusis', function (Blueprint $table) {
            $table->index(['tanggal'], 'idx_distribusi_tanggal');
            $table->index(['komoditas_id', 'tanggal'], 'idx_distribusi_komoditas_tanggal');
        });

        Schema::table('history_harga_pangans', function (Blueprint $table) {
            $table->index(['tanggal_perubahan'], 'idx_history_tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('harga_pangans', function (Blueprint $table) {
            $table->dropIndex('idx_harga_pangan_latest');
        });

        Schema::table('distribusis', function (Blueprint $table) {
            $table->dropIndex('idx_distribusi_tanggal');
            $table->dropIndex('idx_distribusi_komoditas_tanggal');
        });

        Schema::table('history_harga_pangans', function (Blueprint $table) {
            $table->dropIndex('idx_history_tanggal');
        });
    }
};
