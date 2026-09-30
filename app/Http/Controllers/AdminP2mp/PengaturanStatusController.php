<?php

namespace App\Http\Controllers\AdminP2mp;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\Prodi;
use App\Models\IkuPencapaian;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PengaturanStatusController extends Controller
{
    /**
     * Tampilkan halaman pengaturan status ketercapaian.
     */
    public function index(Request $request)
    {
        $setting = Pengaturan::whereNotNull('threshold_tercapai')->first();

        $thresholdTercapai = $setting?->threshold_tercapai ?? 100.00;
        $thresholdPerluPerhatian = $setting?->threshold_perlu_perhatian ?? 60.00;

        if ($request->ajax()) {
            return view('adminp2mp.pengaturan_status.modal_content', compact('thresholdTercapai', 'thresholdPerluPerhatian'));
        }

        return view('adminp2mp.pengaturan_status.index', compact('thresholdTercapai', 'thresholdPerluPerhatian'));
    }

    /**
     * Simpan perubahan threshold status.
     */
    public function store(Request $request)
    {
        $request->validate([
            'threshold_tercapai' => 'required|numeric|min:0|max:100',
            'threshold_perlu_perhatian' => 'required|numeric|min:0|lte:threshold_tercapai',
        ], [
            'threshold_tercapai.required' => 'Batas status Tercapai wajib diisi.',
            'threshold_tercapai.numeric' => 'Batas status Tercapai harus berupa angka.',
            'threshold_tercapai.min' => 'Batas status Tercapai minimal 0%.',
            'threshold_tercapai.max' => 'Batas status Tercapai maksimal 100%.',
            'threshold_perlu_perhatian.required' => 'Batas status Perlu Perhatian wajib diisi.',
            'threshold_perlu_perhatian.numeric' => 'Batas status Perlu Perhatian harus berupa angka.',
            'threshold_perlu_perhatian.min' => 'Batas status Perlu Perhatian minimal 0%.',
            'threshold_perlu_perhatian.lte' => 'Batas status Perlu Perhatian tidak boleh melebihi Batas status Tercapai.',
        ]);

        $thresholdTercapai = (float) $request->threshold_tercapai;
        $thresholdPerluPerhatian = (float) $request->threshold_perlu_perhatian;

        $prodis = Prodi::all();
        $currentYear = (int) date('Y');

        foreach ($prodis as $prodi) {
            $pengaturan = Pengaturan::where('id_prodi', $prodi->id)->first();

            if ($pengaturan) {
                $pengaturan->update([
                    'threshold_tercapai' => $thresholdTercapai,
                    'threshold_perlu_perhatian' => $thresholdPerluPerhatian,
                ]);
            } else {
                Pengaturan::create([
                    'id_prodi' => $prodi->id,
                    'id_user' => auth()->id(),
                    'tahun_mulai' => $currentYear,
                    'tahun_selesai' => $currentYear + 4,
                    'tahun_aktif' => $currentYear,
                    'jml_mahasiswa' => 0,
                    'jml_dosen' => 0,
                    'threshold_tercapai' => $thresholdTercapai,
                    'threshold_perlu_perhatian' => $thresholdPerluPerhatian,
                ]);
            }

            // Re-sync status pencapaian IKU untuk prodi ini sesuai threshold baru
            IkuPencapaian::calculateAndSync($prodi->id);
        }

        ActivityLog::log(
            'Mengubah threshold status ketercapaian',
            'Pengaturan Status',
            "Tercapai: ≥ {$thresholdTercapai}%, Perlu Perhatian: ≥ {$thresholdPerluPerhatian}%, Tidak Tercapai: < {$thresholdPerluPerhatian}%"
        );

        return back()->with('success', 'Threshold status ketercapaian berhasil diperbarui dan status IKU/IKT telah di-sync.');
    }
}
