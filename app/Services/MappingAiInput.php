<?php

namespace App\Services;

use App\Models\Distribusi;
use App\Models\HargaPangan;
use App\Models\MappingKearifan;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MappingAiInput
{

    public function map(int $idHarga)
    {

        $hargaPasarTerbaru = HargaPangan::with(['pasar', 'faktorEksternal'])->where('id', $idHarga)->first();
        if (!$hargaPasarTerbaru)
            return null;

        $historyRecords = HargaPangan::with('faktorEksternal')->where('komoditas_id', $hargaPasarTerbaru->komoditas_id)
            ->where('pasar_id', $hargaPasarTerbaru->pasar_id)
            ->where('tanggal', '<', $hargaPasarTerbaru->tanggal)
            ->orderBy('tanggal', 'desc')
            ->take(60)
            ->get()
            ->reverse()
            ->values();

        $historyRecords->push($hargaPasarTerbaru);

        if ($historyRecords->count() < 60) {
            Log::warning("Gagal Prediksi: Data historis untuk Pasar ID {$hargaPasarTerbaru->pasar_id} kurang dari 60 baris.");
            throw new \Exception('Data historis tidak mencukupi untuk melakukan prediksi (minimal dibutuhkan 60 hari riwayat data).');
        }

        $kearifanScoreRow = MappingKearifan::select(['kearifan_score'])
            ->where('komoditas_id', $hargaPasarTerbaru->komoditas_id)
            ->where('pasar_id', $hargaPasarTerbaru->pasar_id)
            ->first();

        $fixedKearifanScore = (float) ($kearifanScoreRow->kearifan_score ?? 0);

        // 6. MAPPING DATA HISTORIS (Menyusun Baris Fitur per Tanggal)
        $historicalDataMapped = $historyRecords->map(function ($item) use ($fixedKearifanScore) {

            // Fix Bug: Ambil hasil SUM menggunakan alias 'total_volume'
            $totalVolume = Distribusi::where('komoditas_id', $item->komoditas_id)
                ->where('pasar_tujuan_id', $item->pasar_id)
                ->where('tanggal', $item->tanggal)
                ->select(DB::raw('SUM(CAST(volume AS INTEGER)) as total_volume'))
                ->first()
                ->total_volume ?? 0;

            $faktorAktif = $item->faktorEksternal->pluck('jenis')->map(function ($jenis) {
                return strtolower(trim($jenis));
            })->toArray();

            return [
                'tanggal' => $item->tanggal,
                'harga' => (float) $item->harga,
                'volume_distribusi' => (float) $totalVolume,
                'ramadhan' => in_array('ramadhan', $faktorAktif) ? 1 : 0,
                'idul_fitri' => in_array('idul fitri', $faktorAktif) ? 1 : 0,
                'natal_tahun_baru' => in_array('natal tahun baru', $faktorAktif) ? 1 : 0,
                'hujan' => in_array('hujan', $faktorAktif) ? 1 : 0,
                'operasi_pasar' => in_array('operasi pasar', $faktorAktif) ? 1 : 0,
                'gangguan_distribusi' => in_array('gangguan distribusi', $faktorAktif) ? 1 : 0,
                'banjir' => in_array('banjir', $faktorAktif) ? 1 : 0,
                'kearifan_score' => $fixedKearifanScore,
            ];
        })->toArray();

        $faktorHariIni = $hargaPasarTerbaru->faktorEksternal->pluck('jenis')->map(function ($jenis) {
            return strtolower(trim($jenis));
        })->toArray();

        // 8. KONSTRUKSI PAYLOAD UTAMA
        $payload = [
            'komoditas' => strtolower($hargaPasarTerbaru->komoditas->nama_komoditas ?? ""),
            'pasar' => $hargaPasarTerbaru->pasar->nama_pasar ?? 'Pasar Tidak Diketahui',
            'kearifan_score' => $fixedKearifanScore,
            'event' => [
                'ramadhan' => in_array('ramadhan', $faktorHariIni) ? 1 : 0,
                'idul_fitri' => in_array('idul_fitri', $faktorHariIni) ? 1 : 0,
                'natal_tahun_baru' => in_array('natal_tahun_baru', $faktorHariIni) ? 1 : 0,
                'hujan' => in_array('hujan', $faktorHariIni) ? 1 : 0,
                'operasi_pasar' => in_array('operasi_pasar', $faktorHariIni) ? 1 : 0,
                'gangguan_distribusi' => in_array('gangguan_distribusi', $faktorHariIni) ? 1 : 0,
                'banjir' => in_array('banjir', $faktorHariIni) ? 1 : 0,
            ],
            'historical_data' => $historicalDataMapped
        ];


        Log::info("=======================================================");
        Log::info("AI PREDICTION PAYLOAD READY FOR PRICE ID: {$idHarga}");
        Log::info("=======================================================");
        //Log::info(json_encode($payload, JSON_PRETTY_PRINT));
        Log::info("=======================================================");

        return $payload;
    }
}
