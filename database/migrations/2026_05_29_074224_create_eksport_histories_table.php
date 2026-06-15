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
        Schema::create('eksport_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('filename'); // Contoh: "laporan_kehadiran_mei_2026.xlsx"
            $table->string('file_path'); // Contoh: "client/14/exports/file_name.xlsx"
            $table->string('disk')->default('public'); // local, public, atau s3
            $table->string('mime_type')->nullable(); // Contoh: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            $table->bigInteger('file_size')->nullable(); // Dalam bytes, untuk info di UI frontend
            $table->string('module')->nullable(); // Contoh: "payroll", "presensi", "karyawan"

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eksport_histories');
    }
};
