<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailPenilaianRekomendasi extends Model
{
    use HasFactory;

    protected $table = 'detail_penilaian_rekomendasi';

    protected $fillable = [
        'id_penilaian',
        'id_iku',
        'klaim',
        'persentase_fakta',
        'persentase_halusinasi',
        'catatan',
    ];

    protected $casts = [
        'persentase_fakta' => 'float',
        'persentase_halusinasi' => 'float',
    ];

    public function penilaian()
    {
        return $this->belongsTo(PenilaianRekomendasiAi::class, 'id_penilaian');
    }

    public function iku()
    {
        return $this->belongsTo(Iku::class, 'id_iku');
    }
}
