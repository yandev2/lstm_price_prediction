<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessAiPrediction;
use Illuminate\Http\Request;

class AiPredictionController extends Controller
{
    public function predictPrice(int $idHarga)
    {
       
        ProcessAiPrediction::dispatch($idHarga);

        return response()->json([
            'success' => true,
            'message' => 'Proses prediksi AI untuk harga pangan ini telah masuk antrean dan sedang berjalan di latar belakang.'
        ]);
    }
}