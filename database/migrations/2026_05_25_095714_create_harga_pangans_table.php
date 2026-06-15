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
        Schema::create('harga_pangans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('komoditas_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pasar_id')->constrained()->cascadeOnDelete();
            $table->json('faktor_eksternal')->nullable();
            $table->dateTime('tanggal');
            $table->bigInteger('harga');
            $table->string('sumber_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_pangans');
    }
};
