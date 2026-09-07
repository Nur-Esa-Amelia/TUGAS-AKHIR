<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengisianBukti extends Model
{
    use HasFactory;

    protected $table = 'pengisian_bukti';

    protected $fillable = [
        'id_iku',
        'id_bukti_iku',
        'id_user',
        'tahun',
        'keterangan',
        'status',
        'catatan_validator',
    ];

    public function iku()
    {
        return $this->belongsTo(Iku::class, 'id_iku');
    }

    public function buktiIku()
    {
        return $this->belongsTo(BuktiIku::class, 'id_bukti_iku');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function files()
    {
        return $this->hasMany(FileIsiBukti::class, 'id_pengisian_bukti');
    }
}
