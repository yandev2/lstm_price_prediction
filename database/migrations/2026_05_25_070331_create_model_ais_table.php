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
        Schema::create('model_ais', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('komoditas_id')->nullable()->constrained()->nullOnDelete();
            $table->float('versi');
            $table->date('tanggal_training');
            $table->string('scaler_file');
            $table->string('model_file');
            $table->float('mape');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('models');
    }
};
