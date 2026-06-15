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
        Schema::create('faktor_eksternals', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('harga_pangan_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('jenis');
            $table->string('nilai');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faktor_eksternals');
    }
};
