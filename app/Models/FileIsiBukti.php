<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FileIsiBukti extends Model
{
    use HasFactory;

    protected $table = 'file_isi_bukti';

    protected $fillable = [
        'id_pengisian_bukti',
        'file_bukti',
        'nama_file',
        'keterangan',
    ];

    public function pengisianBukti()
    {
        return $this->belongsTo(PengisianBukti::class, 'id_pengisian_bukti');
    }
}
