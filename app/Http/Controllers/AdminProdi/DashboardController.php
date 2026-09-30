<?php

namespace App\Http\Controllers\AdminProdi;

use App\Http\Controllers\Controller;
use App\Models\IkuPencapaian;
use App\Models\Pengaturan;
use App\Models\PenugasanDosen;
use App\Models\PengisianBukti;
use App\Models\FileIsiBukti;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard Admin Prodi.
     */
    public function index()
    {
        $prodiId = auth()->user()->prodi_id;
        $prodiName = auth()->user()->prodi ? auth()->user()->prodi->nama_prodi : 'Program Studi';
        
        $settings = Pengaturan::where('id_prodi', $prodiId)->first();
        $tahunAktif = $settings?->tahun_aktif ?? date('Y');
        
        // Sinkronisasikan database terlebih dahulu untuk mencerminkan validasi atau unggahan baru
        IkuPencapaian::calculateAndSync($prodiId, $tahunAktif);

        // Metrik Utama
        $totalTargets = IkuPencapaian::where('id_prodi', $prodiId)->where('tahun', $tahunAktif)->count();
        $achievedCount = IkuPencapaian::where('id_prodi', $prodiId)->where('tahun', $tahunAktif)->where('status', 'Tercapai')->count();
        $unachievedCount = IkuPencapaian::where('id_prodi', $prodiId)->where('tahun', $tahunAktif)->where('status', '!=', 'Tercapai')->count();
        
        // Hitung total bukti yang valid
        $totalValidProofs = FileIsiBukti::whereHas('pengisianBukti', function ($query) use ($prodiId, $tahunAktif) {
            $query->where('tahun', $tahunAktif)
                ->where('status', 'valid')
                ->whereHas('user', function ($q) use ($prodiId) {
                    $q->where('prodi_id', $prodiId);
                });
        })->count();

        // Ambil penugasan terbaru
        $recentAssignments = PenugasanDosen::with(['iku', 'user'])
            ->where('tahun', $tahunAktif)
            ->whereHas('user', function ($query) use ($prodiId) {
                $query->where('prodi_id', $prodiId);
            })
            ->latest()
            ->take(5)
            ->get();

        // Daftar Iku Pencapaian untuk tabel ringkasan cepat
        $pencapaians = IkuPencapaian::with('iku.kategori')
            ->where('id_prodi', $prodiId)
            ->where('tahun', $tahunAktif)
            ->get();

        // Hitung rata-rata Balanced Scorecard (BSC)
        $jml_mahasiswa = $settings ? $settings->jml_mahasiswa : 0;
        $jml_dosen = $settings ? $settings->jml_dosen : 0;

        $mahasiswaIkus = collect();
        $dosenIkus = collect();

        foreach ($pencapaians as $item) {
            $targetVal = floatval($item->target);
            if ($item->satuan === 'persen') {
                if ($item->objek === 'mahasiswa') {
                    $targetNyata = round(($targetVal / 100) * $jml_mahasiswa);
                } elseif ($item->objek === 'dosen') {
                    $targetNyata = round(($targetVal / 100) * $jml_dosen);
                } else {
                    $targetNyata = $targetVal;
                }
            } else {
                $targetNyata = $targetVal;
            }

            if ($targetNyata > 0) {
                $persentase = min(round(($item->realisasi / $targetNyata) * 100, 2), 100);
            } else {
                $persentase = $item->realisasi > 0 ? 100 : 0;
            }

            $item->persentase_capped = min($persentase, 100);

            if ($item->objek === 'mahasiswa') {
                $mahasiswaIkus->push($item);
            } elseif ($item->objek === 'dosen') {
                $dosenIkus->push($item);
            }
        }

        $avgMahasiswa = $mahasiswaIkus->count() > 0 ? round($mahasiswaIkus->avg('persentase_capped'), 2) : 0;
        $avgDosen = $dosenIkus->count() > 0 ? round($dosenIkus->avg('persentase_capped'), 2) : 0;

        // Filter indikator yang bermasalah (Perlu Perhatian atau Tidak Tercapai)
        $warnings = $pencapaians->filter(function ($item) {
            return in_array($item->status, ['Perlu Perhatian', 'Tidak Tercapai']);
        });

        $rekomendasiController = new \App\Http\Controllers\RekomendasiAiController();
        $recommendations = $rekomendasiController->getOrGenerate($warnings);

        //thrsold
        $thresholdTercapai = (float) ($settings?->threshold_tercapai ?? 100.00);
        $thresholdPerluPerhatian = (float) ($settings?->threshold_perlu_perhatian ?? 60.00);

        return view('adminprodi.dashboard', compact(
            'prodiName',
            'tahunAktif',
            'totalTargets',
            'achievedCount',
            'unachievedCount',
            'totalValidProofs',
            'recentAssignments',
            'pencapaians',
            'settings',
            'recommendations',
            'avgMahasiswa',
            'avgDosen',
            'thresholdTercapai',
            'thresholdPerluPerhatian'
        ));
    }

    /**
     * Tampilkan daftar bukti yang diunggah oleh dosen dari prodi ini.
     */
    public function buktiDosen(Request $request)
    {
        $prodiId = auth()->user()->prodi_id;
        $prodiName = auth()->user()->prodi ? auth()->user()->prodi->nama_prodi : 'Program Studi';
        
        $settings = Pengaturan::where('id_prodi', $prodiId)->first();
        $tahunAktif = $settings?->tahun_aktif ?? date('Y');
        
        if ($settings) {
            $tahunList = range($settings->tahun_mulai, $settings->tahun_selesai);
        } else {
            $tahunList = range(date('Y') - 2, date('Y') + 5);
        }

        // thn yg ingin di tampilkan
        $tahun = $request->query('tahun', $tahunAktif);
        $status = $request->query('status');

        $query = PengisianBukti::with(['user', 'iku.kategori', 'buktiIku', 'files'])
            ->whereHas('user', function ($q) use ($prodiId) {
                $q->where('prodi_id', $prodiId);
            });

        if ($request->filled('tahun')) {
            $query->where('tahun', $tahun);
        }

        if ($request->filled('status')) {
            $query->where('status', $status);
        }

        $riwayat = $query->latest()->paginate(request('per_page', 10));

        // Ambil seluruh IKU/IKT yang memiliki target di program studi ini untuk tahun akademik aktif
        $ikus = \App\Models\Iku::whereIn('id', function ($query) use ($prodiId, $tahunAktif) {
            $query->select('id_iku')
                ->from('iku_pencapaian')
                ->where('id_prodi', $prodiId)
                ->where('tahun', $tahunAktif);
        })->get();
        
        // Ambil semua dokumen bukti yang memungkinkan untuk IKU/IKT tersebut
        $buktiIku = \App\Models\BuktiIku::whereIn('id_iku', $ikus->pluck('id'))->get();

        return view('adminprodi.bukti_dosen.index', compact(
            'riwayat',
            'tahunList',
            'tahun',
            'status',
            'prodiName',
            'settings',
            'ikus',
            'buktiIku'
        ));
    }

    /**
     * Tampilkan daftar dosen di prodi ini beserta tugas indikator IKU/IKT mereka.
     */
    public function dosen(Request $request)
    {
        $prodiId = auth()->user()->prodi_id;
        $prodiName = auth()->user()->prodi ? auth()->user()->prodi->nama_prodi : 'Program Studi';
        
        $settings = Pengaturan::where('id_prodi', $prodiId)->first();
        $tahunAktif = $settings?->tahun_aktif ?? date('Y');
        
        if ($settings) {
            $tahunList = range($settings->tahun_mulai, $settings->tahun_selesai);
        } else {
            $tahunList = range(date('Y') - 2, date('Y') + 5);
        }

        $tahun = $request->query('tahun', $tahunAktif);

        // Muat dosen dari program studi yang sama
        $dosenList = User::where('role', 'dosen')
            ->where('prodi_id', $prodiId)
            ->orderBy('name')
            ->get();

        foreach ($dosenList as $dosenItem) {
            // Muat tugas dosen ini untuk tahun akademik terpilih
            $dosenItem->assignments = PenugasanDosen::with('iku.kategori')
                ->where('id_user', $dosenItem->id)
                ->where('tahun', $tahun)
                ->get();

            // mengecek iku sudah di isi/blm dan status bukti
            foreach ($dosenItem->assignments as $assign) {
                $proof = PengisianBukti::where('id_user', $dosenItem->id)
                    ->where('id_iku', $assign->id_iku)
                    ->where('tahun', $tahun)
                    ->first();
                
                $assign->proof_status = $proof ? $proof->status : 'belum_isi';
            }
        }

        return view('adminprodi.dosen.index', compact(
            'dosenList',
            'tahunList',
            'tahun',
            'prodiName',
            'settings'
        ));
    }
}

