<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PrediksiHarga extends Model
{
    use LogsActivity;
    protected $fillable = [
        'komoditas_id',
        'pasar_id',
        'model_ai_id',
        'tanggal_prediksi',
        'prediksi_harga_untuk_tanggal',
        'harga_prediksi',
        'selisih_persen',
        'status_anomali',
        'alert_harga',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'komoditas.nama_komoditas',
                'pasar.nama_pasar',
                'modelAi.versi',
                'tanggal_prediksi',
                'prediksi_harga_untuk_tanggal',
                'harga_prediksi',
                'selisih_persen',
                'status_anomali',
                'alert_harga',
            ])
            ->logOnlyDirty()
            ->useLogName('prediksi-harga')
            ->dontLogEmptyChanges();
    }


    public function komoditas()
    {
        return $this->belongsTo(Komoditas::class, 'komoditas_id');
    }

    public function pasar()
    {
        return $this->belongsTo(Pasar::class, 'pasar_id');
    }

    public function modelAi()
    {
        return $this->belongsTo(ModelAi::class, 'model_ai_id');
    }
}
