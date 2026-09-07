<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BuktiIku extends Model
{
    use HasFactory;

    protected $table = 'bukti_iku';

    protected $fillable = [
        'id_iku',
        'nama_bukti',
        'deskripsi',
    ];

    public function iku()
    {
        return $this->belongsTo(Iku::class, 'id_iku');
    }

    public function pengisianBukti()
    {
        return $this->hasMany(PengisianBukti::class, 'id_bukti_iku');
    }
}
