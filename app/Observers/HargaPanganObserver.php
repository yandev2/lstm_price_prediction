<?php

namespace App\Observers;

use App\Models\FaktorEksternal;
use App\Models\HargaPangan;
use App\Models\HistoryHargaPangan;

class HargaPanganObserver
{
    /**
     * Handle the HargaPangan "created" event.
     */
    public function created(HargaPangan $hargaPangan): void
    {
        $history = HargaPangan::where('komoditas_id', $hargaPangan->komoditas_id)
            ->where('pasar_id', $hargaPangan->pasar_id)
            ->where('id', '!=', $hargaPangan->id)
            ->latest('tanggal')
            ->first();

        $hargaLama = $history?->harga ?? null;
        $faktorEksternal = $hargaPangan->faktor_eksternal ?? [];

        foreach ($faktorEksternal as $faktor) {
            FaktorEksternal::createQuietly([
                'harga_pangan_id' => $hargaPangan->id,
                'tanggal'         => $hargaPangan->tanggal,
                'jenis'           => $faktor,
                'nilai'           => 1
            ]);
        }

        if ($hargaLama === null || $hargaLama != $hargaPangan->harga) {
            $daftarFaktor = implode(', ', $faktorEksternal);

            if ($hargaLama === null) {
                $hargaLamaLog = 0;
                $deskripsi = "Pendataan harga awal komoditas sebesar Rp " . number_format($hargaPangan->harga) . ".";
            }
            else {
                $hargaLamaLog = $hargaLama;
                $deskripsi = empty($faktorEksternal)
                    ? "Terjadi perubahan harga dari Rp " . number_format($hargaLama) . " menjadi Rp " . number_format($hargaPangan->harga) . "."
                    : "Terjadi perubahan harga karena faktor eksternal: {$daftarFaktor}.";
            }

            HistoryHargaPangan::createQuietly([
                'harga_pangan_id'   => $hargaPangan->id,
                'pasar_id'          => $hargaPangan->pasar_id,
                'komoditas_id'      => $hargaPangan->komoditas_id,
                'harga_lama'        => $hargaLamaLog,
                'harga_baru'        => $hargaPangan->harga,
                'tanggal_perubahan' => now(),
                'deskripsi'         => $deskripsi
            ]);
        }
    }

    /**
     * Handle the HargaPangan "updated" event.
     */
    public function updating(HargaPangan $hargaPangan): void
    {
        if ($hargaPangan->isDirty('harga')) {
            // HargaPanganHistory::createQuietly([
            //     'faktor_eksternal_id' => $hargaPangan->faktor_eksternal_id,
            //     'harga_pangan_id' => $hargaPangan->id,
            //     'pasar_id' => $hargaPangan->pasar_id,
            //     'komoditas_id' => $hargaPangan->komoditas_id,
            //     'harga_lama'        => $hargaPangan->getOriginal('harga'),
            //     'harga_baru'        => $hargaPangan->harga,
            //     'tanggal_perubahan' => now(),
            // ]);
        }
    }

    /**
     * Handle the HargaPangan "deleted" event.
     */
    public function deleted(HargaPangan $hargaPangan): void
    {
        //
    }

    /**
     * Handle the HargaPangan "restored" event.
     */
    public function restored(HargaPangan $hargaPangan): void
    {
        //
    }

    /**
     * Handle the HargaPangan "force deleted" event.
     */
    public function forceDeleted(HargaPangan $hargaPangan): void
    {
        //
    }
}
