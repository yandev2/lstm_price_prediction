<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HargaPangan;
use App\Jobs\ProcessAiPrediction;
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
    protected $description = 'Menjalankan prediksi AI untuk data harga terbaru di setiap komoditas dan pasar melalui Job Queue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses prediksi AI harian (mengirim ke antrean)...');
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

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($hargaTerbaruList as $harga) {
            $namaKomoditas = $harga->komoditas->nama_komoditas ?? 'Unknown';
            $namaPasar = $harga->pasar->nama_pasar ?? 'Unknown';

            try {
                ProcessAiPrediction::dispatch($harga->id);
                $berhasil++;
            } catch (Exception $e) {
                $gagal++;
                Log::error("CRON Dispatch Gagal [{$namaKomoditas} di {$namaPasar}]: " . $e->getMessage());

                $bar->clear();
                $this->error("\n Gagal men-dispatch {$namaKomoditas} di {$namaPasar}: " . $e->getMessage());
                $bar->display();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        // 4. Kesimpulan
        $this->info("\nProses selesai! Berhasil dikirim ke antrean: {$berhasil}, Gagal: {$gagal}");
        Log::info("CRON: ai:predict-daily selesai. Berhasil dispatch: {$berhasil}, Gagal: {$gagal}");

        return self::SUCCESS;
    }
}