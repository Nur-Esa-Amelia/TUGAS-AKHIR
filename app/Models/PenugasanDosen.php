<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PenugasanDosen extends Model
{
    use HasFactory;

    protected $table = 'penugasan_dosen';

    protected $fillable = [
        'id_iku',
        'id_user',
        'tahun',
    ];

    public function iku()
    {
        return $this->belongsTo(Iku::class, 'id_iku');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
