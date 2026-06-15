<?php

namespace Database\Seeders;

use App\Models\Lookup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LookupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Lookup::insertOrIgnore([
            [
                'key' => 'transportasi_distribusi',
                'value' => json_encode(['Truk', 'Kapal', 'Pesawat']),
            ],
            [
                'key' => 'mapping_tingkat_pengaruh',
                'value' => json_encode(['Rendah', 'Sedang', 'Tinggi']),
            ],
            [
                'key' => 'jenis_faktor',
                'value' => json_encode(['Ramadhan', 'Hujan', 'Idul Fitri', 'Natal Tahun Baru', 'Operasi Pasar', 'Gangguan Distribusi', 'Banjir']),
            ],
            [
                'key' => 'jenis_kearifan_lokal',
                'value' => json_encode(['Sosial', 'Budaya', 'Distribusi', 'Perilaku Pasar', 'Sosial Ekonomi']),
            ],
            [
                'key' => 'satuan_komoditas',
                'value' => json_encode(['kg', 'liter']),
            ],
            [
                'key' => 'kategori_komoditas',
                'value' => json_encode(['Pangan Pokok', 'Hortikultura', 'Protein Hewani', 'Pangan Olahan']),
            ],
            [
                'key' => 'tipe_pasar',
                'value' => json_encode(['Tradisional', 'Modern']),
            ],
        ]);
    }
}
