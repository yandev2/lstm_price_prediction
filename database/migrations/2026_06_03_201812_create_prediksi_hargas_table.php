<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prediksi_hargas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('komoditas_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pasar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('model_ai_id')->constrained('model_ais')->cascadeOnDelete();
            $table->date('tanggal_prediksi');
            $table->date('prediksi_harga_untuk_tanggal');
            $table->bigInteger('harga_prediksi');
            $table->float('selisih_persen')->nullable();
            $table->string('status_anomali')->nullable();
            $table->boolean('alert_harga')->nullable();

            $table->unique(
                ['komoditas_id', 'pasar_id', 'prediksi_harga_untuk_tanggal'],
                'unique_prediksi_harian'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prediksi_hargas');
    }
};
