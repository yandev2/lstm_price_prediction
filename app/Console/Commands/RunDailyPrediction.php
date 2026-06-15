<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HargaPangan;
use App\Http\Controllers\AiPredictionController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class RunDailyPrediction extends Command
{
    /**
     * Nama command yang akan dipanggil di terminal.
     */
    protected $signature = 'ai:predict-daily';

    /**
     * Deskripsi command.
     */
    protected $description = 'Menjalankan prediksi AI untuk data harga terbaru di setiap komoditas dan pasar';

    /**
     * Execute the console command.
     */
    public function handle(AiPredictionController $aiController)
    {
        $this->info('Memulai proses prediksi AI harian...');
        Log::info("CRON: ai:predict-daily mulai dijalankan.");

        // 1. Dapatkan ID HargaPangan terbaru untuk setiap kombinasi Komoditas dan Pasar
        // Menggunakan trik MAX(id) dengan GROUP BY agar query sangat cepat
        $latestIds = HargaPangan::select(DB::raw('MAX(id) as id'))
            ->groupBy('komoditas_id', 'pasar_id')
            ->pluck('id');

        // 2. Ambil data utuhnya beserta relasi untuk keperluan log/tampilan
        $hargaTerbaruList = HargaPangan::with(['komoditas', 'pasar'])
            ->whereIn('id', $latestIds)
            ->get();

        $total = $hargaTerbaruList->count();
        $berhasil = 0;
        $gagal = 0;

        if ($total === 0) {
            $this->warn('Tidak ada data harga pangan yang ditemukan di database.');
            return self::SUCCESS;
        }

        // Tampilkan progress bar di terminal
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        // 3. Looping dan Prediksi
        foreach ($hargaTerbaruList as $harga) {
            $namaKomoditas = $harga->komoditas->nama_komoditas ?? 'Unknown';
            $namaPasar = $harga->pasar->nama_pasar ?? 'Unknown';

            try {
                // Panggil fungsi prediksi dari Controller yang sudah kita buat
                $aiController->predictPrice($harga->id);
                $berhasil++;
            } catch (Exception $e) {
                $gagal++;
                // Log spesifik komoditas & pasar yang gagal agar mudah di-debug
                Log::error("CRON Prediksi Gagal [{$namaKomoditas} di {$namaPasar}]: " . $e->getMessage());

                // Hapus progress bar sejenak untuk print pesan error di terminal
                $bar->clear();
                $this->error("\n Gagal prediksi {$namaKomoditas} di {$namaPasar}: " . $e->getMessage());
                $bar->display();
            }

            // Lanjut ke progress bar berikutnya
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        // 4. Kesimpulan
        $this->info("\nProses selesai! Berhasil: {$berhasil}, Gagal: {$gagal}");
        Log::info("CRON: ai:predict-daily selesai. Berhasil: {$berhasil}, Gagal: {$gagal}");

        return self::SUCCESS;
    }
}