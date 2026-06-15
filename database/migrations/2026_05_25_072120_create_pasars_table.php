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
        Schema::create('pasars', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nama_pasar')->unique();
            $table->text('alamat');
            $table->string('tipe');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasars');
    }
};
