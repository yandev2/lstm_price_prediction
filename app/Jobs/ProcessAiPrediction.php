<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\HargaPangan;
use App\Models\ModelAi;
use App\Models\PrediksiHarga;
use App\Models\ScalerPasar;
use App\Services\MappingAiInput;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class ProcessAiPrediction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $idHarga;

    // Set batas waktu job ke 120 detik (2 menit)
    public $timeout = 120;

    // Coba ulang 3 kali jika gagal (misalnya script python gagal / crash)
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(int $idHarga)
    {
        $this->idHarga = $idHarga;
    }

    /**
     * Execute the job.
     */
    public function handle(MappingAiInput $mappingService): void
    {
        try {
            $payload = $mappingService->map($this->idHarga);

            if (empty($payload)) {
                throw new Exception("Payload gagal dimuat. Data historis mungkin tidak mencukupi (minimal 60 hari).");
            }

            $hargaPangan = HargaPangan::select(['id', 'komoditas_id', 'pasar_id', 'tanggal'])->findOrFail($this->idHarga);
            $modelAi = ModelAi::where('komoditas_id', $hargaPangan->komoditas_id)->orderByDesc('versi')->first();
            $scalerPasar = ScalerPasar::latest('versi')->first();

            if (!$modelAi) {
                throw new Exception("Model AI dan Scaler untuk komoditas ini belum dikonfigurasi.");
            }

            $tempFileName = 'temp_payloads/payload_' . $this->idHarga . '_' . time() . '.json';
            Storage::disk('local')->put($tempFileName, json_encode($payload));
            $payloadPath = Storage::disk('local')->path($tempFileName);

            $pythonScript = base_path('scripts/predict.py');
            $modelPath = Storage::disk('local')->path($modelAi->model_file);
            $scalerPath = Storage::disk('local')->path($modelAi->scaler_file);
            $encoderPath = Storage::disk('local')->path($scalerPasar->file_url);

            if (!file_exists($modelPath) || !file_exists($scalerPath) || !file_exists($encoderPath)) {
                $errorMsg = "Salah satu file fisik AI (Model/Scaler/Encoder) tidak ditemukan di server.";

                Log::error("PredictPrice Error: " . $errorMsg, [
                    'model' => $modelPath,
                    'scaler' => $scalerPath,
                    'encoder' => $encoderPath
                ]);

                throw new Exception($errorMsg);
            }
            $python = config('services.python_binary');


            $process = new Process([
                $python,
                $pythonScript,
                '--payload',
                $payloadPath,
                '--model',
                $modelPath,
                '--scaler',
                $scalerPath,
                '--encoder',
                $encoderPath,
            ]);

            $process->setTimeout(60);
            $process->run();

            Storage::disk('local')->delete($tempFileName);

            if (!$process->isSuccessful()) {
                $stdErr = $process->getErrorOutput();
                $stdOut = $process->getOutput();
                Log::error("Python STDERR: " . $stdErr);
                Log::error("Python STDOUT: " . $stdOut);
                $pesanErrorAI = "Skrip AI gagal tereksekusi tanpa pesan.";
                $parsed = json_decode($stdOut, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($parsed['message'])) {
                    $pesanErrorAI = $parsed['message'];
                } elseif (!empty($stdErr)) {
                    $pesanErrorAI = $stdErr;
                }
                throw new Exception($pesanErrorAI);
            }

            $pythonOutput = $process->getOutput();
            $result = json_decode($pythonOutput, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("Gagal parse JSON dari Python. Output: " . $pythonOutput);
                throw new Exception("Format balikan dari AI rusak atau bukan JSON yang valid.");
            }

            if (isset($result['success']) && $result['success'] === false) {
                throw new Exception("AI Error: " . ($result['message'] ?? 'Kesalahan internal AI.'));
            }

            $dataPrediksi = $result['data'];

            $tanggalUntukPrediksi = Carbon::parse($hargaPangan->tanggal, config('app.timezone'))->addDay()->format('Y-m-d');
            PrediksiHarga::updateOrCreate(
                [
                    'komoditas_id' => $hargaPangan->komoditas_id,
                    'pasar_id' => $hargaPangan->pasar_id,
                    'prediksi_harga_untuk_tanggal' => $tanggalUntukPrediksi,
                ],
                [
                    'model_ai_id' => $modelAi->id,
                    'tanggal_prediksi' => now(config('app.timezone')),
                    'harga_prediksi' => (int) round($dataPrediksi['harga_prediksi']),
                    'selisih_persen' => $dataPrediksi['selisih_persen'],
                    'status_anomali' => $dataPrediksi['status_anomali'],
                    'alert_harga' => $dataPrediksi['alert_harga'],
                ]
            );

        } catch (Exception $e) {
            $harga = HargaPangan::with(['komoditas', 'pasar'])->find($this->idHarga);
            $namaKomoditas = $harga->komoditas->nama_komoditas ?? 'Unknown';
            $namaPasar = $harga->pasar->nama_pasar ?? 'Unknown';
            Log::error("Job Prediksi Gagal [{$namaKomoditas} di {$namaPasar}]: " . $e->getMessage());
            throw $e;
        }
    }
}
