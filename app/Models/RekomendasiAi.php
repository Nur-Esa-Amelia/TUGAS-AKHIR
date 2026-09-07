<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RekomendasiAi extends Model
{
    use HasFactory;

    protected $table = 'rekomendasi_ai';

    protected $fillable = [
        'id_iku_pencapaian',
        'rekomendasi',
    ];

    public function ikuPencapaian()
    {
        return $this->belongsTo(IkuPencapaian::class, 'id_iku_pencapaian');
    }

    public function penilaian()
    {
        return $this->hasMany(PenilaianRekomendasiAi::class, 'id_rekomendasi_ai');
    }

}
