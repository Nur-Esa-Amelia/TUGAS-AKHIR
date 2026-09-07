<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Mail\EwsWarningMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class IkuPencapaian extends Model
{
    use HasFactory;

    protected $table = 'iku_pencapaian';

    protected $fillable = [
        'id_iku',
        'id_prodi',
        'id_user',
        'tahun',
        'target',
        'satuan',
        'realisasi',
        'objek',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'realisasi' => 'float',
        'target' => 'float',
    ];

    public function iku()
    {
        return $this->belongsTo(Iku::class, 'id_iku');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function rekomendasiAi()
    {
        return $this->hasOne(RekomendasiAi::class, 'id_iku_pencapaian');
    }

    public function targetNyata(?Pengaturan $settings = null): float
    {
        $settings ??= Pengaturan::where('id_prodi', $this->id_prodi)->first();
        $target = (float) $this->target;

        if ($this->satuan !== 'persen') {
            return $target;
        }

        if (!in_array($this->objek, ['mahasiswa', 'dosen'], true)) {
            return $target;
        }

        $jumlah = $this->objek === 'mahasiswa'
            ? ($settings?->jml_mahasiswa ?? 0)
            : ($settings?->jml_dosen ?? 0);

        return round(($target / 100) * $jumlah);
    }

    public function batasBerkas(?Pengaturan $settings = null): int
    {
        return max(0, (int) ceil($this->targetNyata($settings)));
    }

    public static function sisaBerkas($prodiId, $ikuId, $tahun): ?int
    {
        $pencapaian = self::where('id_prodi', $prodiId)
            ->where('id_iku', $ikuId)
            ->where('tahun', $tahun)
            ->first();

        if (!$pencapaian) {
            return null;
        }

        $terpakai = FileIsiBukti::whereHas('pengisianBukti', function ($query) use ($ikuId, $tahun, $prodiId) {
            $query->where('id_iku', $ikuId)
                ->where('tahun', $tahun)
                ->whereIn('status', ['pending', 'valid'])
                ->whereHas('user', function ($userQuery) use ($prodiId) {
                    $userQuery->where('prodi_id', $prodiId);
                });
        })->count();

        return max(0, $pencapaian->batasBerkas() - $terpakai);
    }

    /**
     * Hitung realisasi dan sinkronisasikan status berdasarkan bukti yang divalidasi P2MP.
     */
    public static function calculateAndSync($prodiId, $tahun)
    {
        $settings = Pengaturan::where('id_prodi', $prodiId)->first();
        $jml_mahasiswa = $settings ? $settings->jml_mahasiswa : 0;
        $jml_dosen = $settings ? $settings->jml_dosen : 0;

        $pencapaians = self::where('id_prodi', $prodiId)->where('tahun', $tahun)->get(); 

        foreach ($pencapaians as $pencapaian) {
            // Realisasi adalah jumlah berkas bukti yang diunggah oleh dosen dari prodi ini, di tahun ini, dan berstatus 'valid'
            $realisasi = FileIsiBukti::whereHas('pengisianBukti', function ($query) use ($pencapaian, $tahun, $prodiId) {
                $query->where('id_iku', $pencapaian->id_iku)
                    ->where('tahun', $tahun)
                    ->where('status', 'valid')
                    ->whereHas('user', function ($q) use ($prodiId) {
                        $q->where('prodi_id', $prodiId);
                    });
            })->count();

            $target_nyata = $pencapaian->targetNyata($settings);

            // Tentukan status ketercapaian target
            if ($target_nyata > 0) {
                $persentase = min(($realisasi / $target_nyata) * 100, 100);
            } else {
                $persentase = $realisasi > 0 ? 100 : 0;
            }

            if ($persentase >= 100) {
                $status = 'Tercapai';
                // Hapus rekomendasi jika ada karena status sudah tercapai/aman
                \App\Models\RekomendasiAi::where('id_iku_pencapaian', $pencapaian->id)->delete();
            } elseif ($persentase >= 60) {
                $status = 'Perlu Perhatian';
            } else {
                $status = 'Tidak Tercapai';
            }

            $oldStatus = $pencapaian->status;

            if ($pencapaian->realisasi != $realisasi || $pencapaian->status != $status) {
                $pencapaian->update([
                    'realisasi' => $realisasi,
                    'status' => $status
                ]);
            }

            // Deteksi transisi status dari non-warning (Tercapai, Baru, null) ke warning (Perlu Perhatian, Tidak Tercapai)
            $isWarning = in_array($status, ['Perlu Perhatian', 'Tidak Tercapai']);
            $wasWarning = in_array($oldStatus, ['Perlu Perhatian', 'Tidak Tercapai']);

            if ($isWarning && !$wasWarning) {
                // Ambil Kaprodi dan Admin Prodi terkait
                $recipients = User::where('prodi_id', $pencapaian->id_prodi)
                    ->whereIn('role', ['kaprodi', 'admin_prodi'])
                    ->whereNotNull('email')
                    ->pluck('email')
                    ->toArray();

                if (!empty($recipients)) {
                    try {
                        Mail::to($recipients)->send(new EwsWarningMail($pencapaian));
                        Log::info("EWS warning email sent successfully to: " . implode(', ', $recipients) . " for IkuPencapaian ID: " . $pencapaian->id);
                    } catch (\Exception $e) {
                        Log::error("Failed to send EWS warning email: " . $e->getMessage() . " for IkuPencapaian ID: " . $pencapaian->id);
                    }
                }
            }
        }
    }
}
