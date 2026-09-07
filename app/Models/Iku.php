<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Iku extends Model
{
    use HasFactory;

    protected $table = 'iku';

    protected $fillable = [
        'id_kategori',
        'kode_iku',
        'nama_iku',
        'deskripsi',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function buktiIku()
    {
        return $this->hasMany(BuktiIku::class, 'id_iku');
    }

    public function penugasanDosen()
    {
        return $this->hasMany(PenugasanDosen::class, 'id_iku');
    }

    public function pengisianBukti()
    {
        return $this->hasMany(PengisianBukti::class, 'id_iku');
    }

    public function ikuPencapaian()
    {
        return $this->hasMany(IkuPencapaian::class, 'id_iku');
    }
}
