<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KomoditasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {

        $csvFile = database_path('seeders/dataset_final_ai_epangkal.csv');

        if (!file_exists($csvFile)) {
            $this->command->error("❌ File CSV tidak ditemukan di: {$csvFile}");
            return;
        }

        $fileHandle = fopen($csvFile, 'r');
        $header = fgetcsv($fileHandle, 1000, ';');
        $this->command->info('⏳ Sedang memproses data CSV dan memasukkannya ke tabel komoditas...');

        $insertBatch = [];
        $batchSize = 200;
        $totalTerinput = 0;

        while (($row = fgetcsv($fileHandle, 1000, ';')) !== false) {
            if (empty($row) || count($header) !== count($row)) {
                \Log::warning('Baris dilewati karena kolom tidak sinkron', ['row' => $row]);
                continue;
            }
            $data = array_combine($header, $row);

            $namaKomoditas = trim($data['komoditas']);
            $kategori =  trim($data['kategori_komoditas']);
            $satuan =  trim($data['satuan']);

            $insertBatch[] = [
                'nama_komoditas' => $namaKomoditas,
                'kategori' => $kategori,
                'satuan' => $satuan,
            ];

            $totalTerinput++;
            if (count($insertBatch) >= $batchSize) {
                DB::table('komoditas')->insertOrIgnore($insertBatch);
                $insertBatch = [];
            }
        }

        if (count($insertBatch) > 0) {
            DB::table('komoditas')->insertOrIgnore($insertBatch);
        }
        fclose($fileHandle);
        $this->command->info("✅ SUKSES! Total {$totalTerinput} data dari CSV berhasil dimasukkan ke tabel 'komoditas'.");
    }
}
