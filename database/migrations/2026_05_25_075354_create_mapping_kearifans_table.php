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
        Schema::create('mapping_kearifans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('kearifan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pasar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('komoditas_id')->constrained()->cascadeOnDelete();
            $table->float('skor_harga')->nullable();
            $table->float('skor_distribusi')->nullable();
            $table->float('skor_frekuensi')->nullable();
            $table->float('kearifan_score')->nullable();
            $table->text('pengaruh')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapping_kearifans');
    }
};
