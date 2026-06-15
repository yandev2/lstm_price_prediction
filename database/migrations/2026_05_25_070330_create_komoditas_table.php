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
        Schema::create('komoditas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nama_komoditas')->unique();
            $table->string('kategori');
            $table->string('satuan');
            $table->index('nama_komoditas');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komoditas');
    }
};
