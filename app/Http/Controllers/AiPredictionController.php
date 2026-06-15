<?php

namespace App\Http\Controllers;

use App\Models\HargaPangan;
use App\Models\ModelAi;
use App\Models\PrediksiHarga;
use App\Services\MappingAiInput;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class AiPredictionController extends Controller
{
    protected MappingAiInput $mappingService;

    // Inject service MappingAiInput
    public function __construct(MappingAiInput $mappingService)
    {
        $this->mappingService = $mappingService;
    }

    public function predictPrice(int $idHarga)
    {
        try {
            $payload = $this->mappingService->map($idHarga);

            if (empty($payload)) {
                throw new Exception("Payload gagal dimuat. Data historis mungkin tidak mencukupi (minimal 60 hari).");
            }

            $hargaPangan = HargaPangan::select(['id', 'komoditas_id', 'pasar_id', 'tanggal'])->findOrFail($idHarga);
            $modelAi = ModelAi::where('komoditas_id', $hargaPangan->komoditas_id)->orderByDesc('versi')->first();

            if (!$modelAi) {
                throw new Exception("Model AI dan Scaler untuk komoditas ini belum dikonfigurasi.");
            }

            $tempFileName = 'temp_payloads/payload_' . $idHarga . '_' . time() . '.json';
            Storage::disk('local')->put($tempFileName, json_encode($payload));
            $payloadPath = Storage::disk('local')->path($tempFileName);

            $pythonScript = base_path('scripts/predict.py');            // GUNAKAN STORAGE::PATH() - Ini otomatis menyesuaikan OS (Windows/Linux) 
            $modelPath = Storage::disk('local')->path($modelAi->model_file);
            $scalerPath = Storage::disk('local')->path($modelAi->scaler_file);
            $encoderPath = Storage::disk('local')->path('ai_models/encoder_pasar.pkl');

            if (!file_exists($modelPath) || !file_exists($scalerPath) || !file_exists($encoderPath)) {
                $errorMsg = "Salah satu file fisik AI (Model/Scaler/Encoder) tidak ditemukan di server.";

                Log::error("PredictPrice Error: " . $errorMsg, [
                    'model' => $modelPath,
                    'scaler' => $scalerPath,
                    'encoder' => $encoderPath
                ]);

                // UBAH DARI `return;` MENJADI `throw new Exception`
                // Agar Command bisa menangkapnya dan menghitungnya sebagai "Gagal"
                throw new Exception($errorMsg);
            }

            $process = new Process([
                'python', // ganti 'python3' jika server Linux Anda menggunakan python3
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
            // Asumsi: Prediksi harga selalu untuk keesokan harinya (H+1) dari data harga saat ini
            $tanggalUntukPrediksi = Carbon::parse($hargaPangan->tanggal)->addDay()->format('Y-m-d');
            $prediksi = PrediksiHarga::updateOrCreate(
                [
                    'komoditas_id' => $hargaPangan->komoditas_id,
                    'pasar_id' => $hargaPangan->pasar_id,
                    'prediksi_harga_untuk_tanggal' => $tanggalUntukPrediksi,
                ],
                [
                    'model_ai_id' => $modelAi->id,
                    'tanggal_prediksi' => now(),
                    'harga_prediksi' => (int) round($dataPrediksi['harga_prediksi']),
                    'selisih_persen' => $dataPrediksi['selisih_persen'],
                    'status_anomali' => $dataPrediksi['status_anomali'],
                    'alert_harga' => $dataPrediksi['alert_harga'],
                ]
            );

            return $prediksi;
        } catch (Exception $e) {
            Log::error("PredictPrice Error [ID Harga: {$idHarga}]: " . $e->getMessage());
            throw $e;
        }
    }
}