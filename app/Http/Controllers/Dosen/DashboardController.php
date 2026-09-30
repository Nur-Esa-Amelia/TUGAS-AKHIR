<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\PenugasanDosen;
use App\Models\Pengaturan;
use App\Models\PengisianBukti;
use App\Models\IkuPencapaian;
use App\Models\FileIsiBukti;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard Dosen.
     */
    public function index()
    {
        $user = auth()->user(); 
        $prodiId = $user->prodi_id;
        $prodiName = $user->prodi ? $user->prodi->nama_prodi : 'Program Studi';
        
        $settings = Pengaturan::where('id_prodi', $prodiId)->first();
        $tahunAktif = $settings?->tahun_aktif ?? date('Y');

        // Ambil IKU/IKT yang ditugaskan untuk tahun ini
        $assignments = PenugasanDosen::with(['iku.kategori'])
            ->where('id_user', $user->id)
            ->where('tahun', $tahunAktif)
            ->get();

        // Hitung statistik untuk tahun akademik aktif
        $totalAssignments = $assignments->count();
        //menghitung jumlah file bukti yang sudah di-upload oleh user tertentu pada tahun aktif.
        $totalProofs = FileIsiBukti::whereHas('pengisianBukti', function ($query) use ($user, $tahunAktif) {
            $query->where('id_user', $user->id)->where('tahun', $tahunAktif);
        })->count();

        //menghitung jumlah file bukti yang sudah dinyatakan valid oleh user pada tahun aktif.
        $validProofs = FileIsiBukti::whereHas('pengisianBukti', function ($query) use ($user, $tahunAktif) {
            $query->where('id_user', $user->id)->where('tahun', $tahunAktif)->where('status', 'valid');
        })->count();

        //menghitung jumlah file bukti yang sudah dinyatakan pending oleh user pada tahun aktif.
        $pendingProofs = FileIsiBukti::whereHas('pengisianBukti', function ($query) use ($user, $tahunAktif) {
            $query->where('id_user', $user->id)->where('tahun', $tahunAktif)->where('status', 'pending');
        })->count();

        //menghitung jumlah file bukti yang sudah dinyatakan invalid oleh user pada tahun aktif.
        $invalidProofs = FileIsiBukti::whereHas('pengisianBukti', function ($query) use ($user, $tahunAktif) {
            $query->where('id_user', $user->id)->where('tahun', $tahunAktif)->where('status', 'invalid');
        })->count();

        // Hitung kemajuan individu dosen
        $totalPercentage = 0;
        $countWithTarget = 0;

        foreach ($assignments as $assignment) {
            $pencapaian = IkuPencapaian::where('id_iku', $assignment->id_iku)
                ->where('id_prodi', $prodiId)
                ->where('tahun', $tahunAktif)
                ->first();

            $targetNyata = 0;
            $realisasi = 0;
            $persentase = 0;

            if ($pencapaian) {
                // Gunakan fungsi targetNyata dari model
                $targetNyata = $pencapaian->targetNyata($settings);

                // 3. Realisasi per IKU/IKT = HITUNG berkas bukti yang valid untuk user ini
                $realisasi = FileIsiBukti::whereHas('pengisianBukti', function ($query) use ($assignment, $user, $tahunAktif) {
                    $query->where('id_iku', $assignment->id_iku)
                        ->where('id_user', $user->id)
                        ->where('tahun', $tahunAktif)
                        ->where('status', 'valid');
                })->count();

                // 4. Persentase per IKU/IKT = (realisasi / target_nyata) * 100, presisi 2 angka desimal, max 100
                $persentase = $targetNyata > 0 ? min(round(($realisasi / $targetNyata) * 100, 2), 100) : 0;

                $totalPercentage += $persentase; //total persentase
                $countWithTarget++; //iku punya target
            }

            // Lampirkan variabel ke model penugasan untuk dikirim ke view
            $assignment->target_nyata = $targetNyata;
            $assignment->realisasi = $realisasi;
            $assignment->persentase = $persentase;
            $assignment->satuan = $pencapaian ? $pencapaian->satuan : '';
            $assignment->objek = $pencapaian ? $pencapaian->objek : '';
            $assignment->target_tercapai = $targetNyata > 0 ? $realisasi >= $targetNyata : false;
        }

        // rata-rata persentase IKU/IKT yang ditugaskan
        $achievementPercentage = $countWithTarget > 0 
            ? min(round($totalPercentage / $countWithTarget, 2), 100)
            : 0;

        return view('dosen.dashboard', compact(
            'prodiName',
            'tahunAktif',
            'assignments',
            'totalAssignments',
            'totalProofs',
            'validProofs',
            'pendingProofs',
            'invalidProofs',
            'achievementPercentage',
            'settings'
        ));
    }

    /**
     * Tampilkan target IKU/IKT prodi, status pencapaian, dan berkas bukti yang diunggah oleh dosen itu sendiri.
     */
    public function pencapaian(Request $request)
    {
        $user = auth()->user();
        $prodiId = $user->prodi_id;
        $prodiName = $user->prodi ? $user->prodi->nama_prodi : 'Program Studi';
        
        $settings = Pengaturan::where('id_prodi', $prodiId)->first();
        $tahunAktif = $settings?->tahun_aktif ?? date('Y');
        
        if ($settings) {
            $tahunList = range($settings->tahun_mulai, $settings->tahun_selesai);
        } else {
            $tahunList = range(date('Y') - 2, date('Y') + 5);
        }

        $tahun = $request->query('tahun', $tahunAktif);

        // Sinkronisasikan dulu untuk memastikan data pencapaian di database terbaru
        IkuPencapaian::calculateAndSync($prodiId, $tahun);

        // Ambil semua target/pencapaian untuk program studi ini
        $pencapaianList = IkuPencapaian::with('iku.kategori')
            ->where('id_prodi', $prodiId)
            ->where('tahun', $tahun)
            ->orderBy('id', 'asc')
            ->get();

        // Lampirkan bukti unggahan milik dosen itu sendiri untuk masing-masing IKU/IKT
        foreach ($pencapaianList as $item) {
            $item->my_proofs = PengisianBukti::with(['buktiIku', 'files'])
                ->where('id_user', $user->id)
                ->where('id_iku', $item->id_iku)
                ->where('tahun', $tahun)
                ->get();
        }

        // Dapatkan daftar ID IKU/IKT yang ditugaskan ke dosen ini untuk tahun akademik berjalan
        $assignedIkuIds = PenugasanDosen::where('id_user', $user->id)
            ->where('tahun', $tahun)
            ->pluck('id_iku')
            ->toArray();

        // Filter indikator yang bermasalah (Perlu Perhatian atau Tidak Tercapai)
        $warnings = $pencapaianList->filter(function ($item) {
            return in_array($item->status, ['Perlu Perhatian', 'Tidak Tercapai']);
        });

        $rekomendasiController = new \App\Http\Controllers\RekomendasiAiController();
        $recommendations = $rekomendasiController->getOrGenerate($warnings);

        return view('dosen.pencapaian.index', compact(
            'pencapaianList',
            'assignedIkuIds',
            'tahunList',
            'tahun',
            'prodiName',
            'settings',
            'recommendations'
        ));
    }
}
