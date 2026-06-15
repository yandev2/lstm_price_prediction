<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KearifanSeeder extends Seeder
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
        $this->command->info('⏳ Sedang memproses data CSV dan memasukkannya ke tabel kearifan_lokals...');

        $insertBatch = [];
        $batchSize = 200;
        $totalTerinput = 0;

        while (($row = fgetcsv($fileHandle, 1000, ';')) !== false) {
            if (empty($row) || count($header) !== count($row)) {
                \Log::warning('Baris dilewati karena kolom tidak sinkron', ['row' => $row]);
                continue;
            }
            $data = array_combine($header, $row);

            $namaKearifan = trim($data['nama_kearifan']);
            $jenis =  trim($data['jenis_kearifan']);
            $insertBatch[] = [
                'nama_kearifan' => $namaKearifan,
                'deskripsi' =>  '-',
                'jenis' => $jenis
            ];
            
            $totalTerinput++;
            if (count($insertBatch) >= $batchSize) {
                DB::table('kearifans')->insertOrIgnore($insertBatch);
                $insertBatch = [];
            }
        }

        if (count($insertBatch) > 0) {
            DB::table('kearifans')->insertOrIgnore($insertBatch);
        }
        fclose($fileHandle);
        $this->command->info("✅ SUKSES! Total {$totalTerinput} data dari CSV berhasil dimasukkan ke tabel 'kearifan_lokals'.");
    }
}
