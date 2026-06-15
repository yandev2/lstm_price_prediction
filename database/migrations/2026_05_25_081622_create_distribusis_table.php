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
        Schema::create('distribusis', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('komoditas_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pasar_asal_id')->constrained('pasars')->cascadeOnDelete();
            $table->foreignId('pasar_tujuan_id')->constrained('pasars')->cascadeOnDelete();
            $table->float('volume');
            $table->dateTime('tanggal');
            $table->string('transportasi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribusis');
    }
};
