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

        $terpakai = FileIsiBukti::join('pengisian_bukti', 'file_isi_bukti.id_pengisian_bukti', '=', 'pengisian_bukti.id')
            ->join('users', 'pengisian_bukti.id_user', '=', 'users.id')
            ->where('pengisian_bukti.id_iku', $ikuId)
            ->where('pengisian_bukti.tahun', $tahun)
            ->whereIn('pengisian_bukti.status', ['pending', 'valid'])
            ->where('users.prodi_id', $prodiId)
            ->count();

        return max(0, $pencapaian->batasBerkas() - $terpakai);
    }

    /**
     * Hitung realisasi dan sinkronisasikan status berdasarkan bukti yang divalidasi P2MP.
     */
    public static function calculateAndSync($prodiId, $tahun)
    {
        $settings = Pengaturan::where('id_prodi', $prodiId)->first();
        $pencapaians = self::where('id_prodi', $prodiId)->where('tahun', $tahun)->get();

        if ($pencapaians->isEmpty()) {
            return;
        }

        // Ambil seluruh jumlah realisasi per id_iku sekaligus dalam 1 query gabungan yang sangat cepat
        $realisasiMap = FileIsiBukti::join('pengisian_bukti', 'file_isi_bukti.id_pengisian_bukti', '=', 'pengisian_bukti.id')
            ->join('users', 'pengisian_bukti.id_user', '=', 'users.id')
            ->where('pengisian_bukti.tahun', $tahun)
            ->where('pengisian_bukti.status', 'valid')
            ->where('users.prodi_id', $prodiId)
            ->selectRaw('pengisian_bukti.id_iku, COUNT(file_isi_bukti.id) as total')
            ->groupBy('pengisian_bukti.id_iku')
            ->pluck('total', 'id_iku');

        foreach ($pencapaians as $pencapaian) {
            $realisasi = (int) ($realisasiMap[$pencapaian->id_iku] ?? 0);

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
                        Mail::to($recipients)->queue(new EwsWarningMail($pencapaian));
                        Log::info("EWS warning email queued successfully to: " . implode(', ', $recipients) . " for IkuPencapaian ID: " . $pencapaian->id);
                    } catch (\Exception $e) {
                        Log::error("Failed to queue EWS warning email: " . $e->getMessage() . " for IkuPencapaian ID: " . $pencapaian->id);
                    }
                }
            }
        }
    }
}
