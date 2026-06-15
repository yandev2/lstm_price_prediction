<?php

namespace Database\Seeders;

use App\Models\Distribusi;
use App\Models\HargaPangan;
use App\Models\Komoditas;
use App\Models\Pasar;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class HargaPanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startDate = Carbon::now()->subMonths(2)->startOfMonth();
        $endDate = Carbon::now();

        $opsiFaktor = [
            'Ramadhan',
            'Hujan',
            'Idul Fitri',
            'Natal Tahun Baru',
            'Operasi Pasar',
            'Gangguan Distribusi',
            'Banjir'
        ];

        $transportasi = ['Truk', 'Kapal', 'Pesawat'];

        while ($startDate->lte($endDate)) {
            $jumlahFaktor = rand(0, 2);
            $faktorSelected = [];

            if ($jumlahFaktor > 0) {
                $randomKeys = (array) array_rand($opsiFaktor, $jumlahFaktor);
                foreach ($randomKeys as $key) {
                    $faktorSelected[] = $opsiFaktor[$key];
                }
            }

            $hargaAcak = rand(25000, 35000);
            $volumeAcak = rand(200, 700);
            HargaPangan::create([
                'created_by'       => 1,
                'komoditas_id'     => 2,
                'pasar_id'         => 2,
                'faktor_eksternal' => $faktorSelected,
                'tanggal'          => $startDate->format('Y-m-d'),
                'harga'            => $hargaAcak,
                'sumber_data'      => 'E-Pangkal DATA SISTEM',
            ]);

            Distribusi::create([
                'komoditas_id'    => 2,
                'pasar_asal_id'   => 1,
                'pasar_tujuan_id' => 2,
                'volume'          => $volumeAcak,
                'tanggal'         => $startDate->format('Y-m-d'),
                'transportasi'    => Arr::random($transportasi),
            ]);
            $startDate->addDay();
        }
    }
}
