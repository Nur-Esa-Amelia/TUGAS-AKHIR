<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiPrompt extends Model
{
    protected $fillable = [
        'name',
        'prompt_template',
        'status',
        'keterangan',
    ];

    /**
     * Ambil prompt yang sedang aktif.
     * Hanya boleh ada satu prompt aktif pada satu waktu.
     */
    public static function getActive(): ?self
    {
        return static::where('status', 'aktif')->latest()->first();
    }
}
