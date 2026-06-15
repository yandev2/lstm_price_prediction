<?php

namespace App\Http\Controllers;

use App\Jobs\ExportDistribusiJob;
use App\Jobs\ExportHargaPanganJob;
use App\Jobs\ExportPrediksiHargaJob;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ExportController extends Controller
{
    public function export_harga_pangan(array $id, string $type)
    {
        $user = auth()->user();
        $fileName = 'export_data_harga_pangan_' . Carbon::now()->format('d-m-Y_H-i-s');

        if ($type == "exel") {
            $pathFile = "export/{$fileName}.xlsx";
            ExportHargaPanganJob::dispatch($id, $user, $pathFile, $type);
        }
        if ($type == 'pdf') {
            $pathFile = "export/{$fileName}.pdf";
            ExportHargaPanganJob::dispatch($id, $user, $pathFile, $type);
        }
    }

    public function export_distribusi(array $id, string $type)
    {
        $user = auth()->user();
        $fileName = 'export_data_distribusi_pangan_' . Carbon::now()->format('d-m-Y_H-i-s');

        if ($type == "exel") {
            $pathFile = "export/{$fileName}.xlsx";
            ExportDistribusiJob::dispatch($id, $user, $pathFile, $type);
        }
        if ($type == 'pdf') {
            $pathFile = "export/{$fileName}.pdf";
            ExportDistribusiJob::dispatch($id, $user, $pathFile, $type);
        }
    }

     public function export_prediksi_harga_pangan(array $id, string $type)
    {
        $user = auth()->user();
        $fileName = 'export_data_prediksi_harga_pangan_' . Carbon::now()->format('d-m-Y_H-i-s');

        if ($type == "exel") {
            $pathFile = "export/{$fileName}.xlsx";
            ExportPrediksiHargaJob::dispatch($id, $user, $pathFile, $type);
        }
        if ($type == 'pdf') {
            $pathFile = "export/{$fileName}.pdf";
            ExportPrediksiHargaJob::dispatch($id, $user, $pathFile, $type);
        }
    }
}
