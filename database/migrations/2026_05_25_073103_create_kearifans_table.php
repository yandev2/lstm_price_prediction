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
        Schema::create('kearifans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nama_kearifan')->unique();
            $table->text('deskripsi');
            $table->string('jenis');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kearifans');
    }
};
