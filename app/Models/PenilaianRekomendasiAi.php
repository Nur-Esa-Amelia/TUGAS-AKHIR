<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PenilaianRekomendasiAi extends Model
{
    use HasFactory;

    protected $table = 'penilaian_rekomendasi_ai';

    protected $fillable = [
        'id_rekomendasi_ai',
        'nama_penilai',
        'jabatan',
        'prodi_unit',
    ];

    public function rekomendasiAi()
    {
        return $this->belongsTo(RekomendasiAi::class, 'id_rekomendasi_ai');
    }

    public function details()
    {
        return $this->hasMany(DetailPenilaianRekomendasi::class, 'id_penilaian');
    }
}

