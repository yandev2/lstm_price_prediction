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
        Schema::create('history_harga_pangans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('harga_pangan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('komoditas_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pasar_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('harga_lama');
            $table->bigInteger('harga_baru');
            $table->dateTime('tanggal_perubahan');
            $table->text('deskripsi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_harga_pangans');
    }
};
