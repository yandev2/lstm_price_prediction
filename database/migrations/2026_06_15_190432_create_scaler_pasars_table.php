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
        Schema::create('scaler_pasars', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nama');
            $table->jsonb("daftar_pasar");
            $table->integer('versi');
            $table->string('file_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scaler_pasars');
    }
};
