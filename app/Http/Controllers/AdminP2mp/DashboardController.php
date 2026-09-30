<?php

namespace App\Http\Controllers\AdminP2mp;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Prodi;
use App\Models\PengisianBukti;
use App\Models\IkuPencapaian;
use App\Models\Pengaturan;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard Admin P2MP.
     */
    public function index(Request $request)
    {
        $totalUsers = User::count(); 
        $totalProdi = Prodi::count();
        $totalIku = \App\Models\Iku::count();
        $pendingValidationCount = PengisianBukti::where('status', 'pending')->count(); 
        
        $prodis = Prodi::all();  
        $prodiId = $request->query('prodi_id'); 

        if ($prodiId) {
            $settings = Pengaturan::where('id_prodi', $prodiId)->first();
        } else {
            $minStart = Pengaturan::min('tahun_mulai');
            $maxEnd = Pengaturan::max('tahun_selesai');
            $activeYear = Pengaturan::value('tahun_aktif');
            if ($minStart && $maxEnd) {
                $settings = (object)[
                    'tahun_mulai' => $minStart,
                    'tahun_selesai' => $maxEnd,
                    'tahun_aktif' => $activeYear ?: date('Y'),
                ];
            } else {
                $settings = null;
            }
        }

        //menentukan daftar tahun
        if ($settings) {
            $tahunList = range($settings->tahun_mulai, $settings->tahun_selesai);
        } else {
            $dbYears = IkuPencapaian::select('tahun')->distinct()->pluck('tahun')->toArray(); 
            $tahunList = array_unique(array_merge($dbYears, [date('Y') - 2, date('Y') - 1, date('Y'), date('Y') + 1]));
            sort($tahunList);
        }

        // menentukan tahun yang akan digunakan
        $tahunAktif = $settings?->tahun_aktif ?? date('Y');
        $tahun = $request->query('tahun', $tahunAktif);

        foreach ($prodis as $prodi) {
            IkuPencapaian::calculateAndSync($prodi->id, $tahun);
        }

        $query = IkuPencapaian::with(['prodi', 'iku.kategori'])
            ->where('tahun', $tahun);

        // filter berdasarkan id prodi
        if ($request->filled('prodi_id')) {
            $query->where('id_prodi', $prodiId);
        }

        $laporan = $query->orderBy('id_prodi')
            ->orderBy('id', 'asc')
            ->get();

        $settingsMap = Pengaturan::all()->keyBy('id_prodi');
        $totalReports = IkuPencapaian::where('tahun', $tahun)->count();

        // Hitung rata-rata Balanced Scorecard (BSC) untuk P2MP
        $mahasiswaIkus = collect();
        $dosenIkus = collect();

        foreach ($laporan as $item) {
            $settings = $settingsMap->get($item->id_prodi);
            $jml_mahasiswa = $settings ? $settings->jml_mahasiswa : 0;
            $jml_dosen = $settings ? $settings->jml_dosen : 0;
            
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

            // menghitung persentase
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

        // rata rata bsc
        $avgMahasiswa = $mahasiswaIkus->count() > 0 ? round($mahasiswaIkus->avg('persentase_capped'), 2) : 0;
        $avgDosen = $dosenIkus->count() > 0 ? round($dosenIkus->avg('persentase_capped'), 2) : 0;

        // Hitung IKU/IKT Tercapai & Belum Tercapai untuk kartu metrik
        $achievedCount = $laporan->where('status', 'Tercapai')->count();
        $unachievedCount = $laporan->where('status', '!=', 'Tercapai')->count();

        $globalSetting = Pengaturan::whereNotNull('threshold_tercapai')->first();
        $thresholdTercapai = (float) ($globalSetting?->threshold_tercapai ?? 100.00);
        $thresholdPerluPerhatian = (float) ($globalSetting?->threshold_perlu_perhatian ?? 60.00);

        return view('adminp2mp.dashboard', compact(
            'totalUsers',
            'totalProdi',
            'totalIku',
            'pendingValidationCount',
            'totalReports',
            'laporan',
            'settingsMap',
            'tahun',
            'prodis',
            'tahunList',
            'prodiId',
            'avgMahasiswa',
            'avgDosen',
            'achievedCount',
            'unachievedCount',
            'thresholdTercapai',
            'thresholdPerluPerhatian'
        ));
    }

    /**
     * Tampilkan halaman Validasi Bukti IKU/IKT.
     */
    public function validasi(Request $request)
    {
        $prodis = Prodi::all();
        $prodiId = $request->query('prodi_id');
        $status = $request->query('status');
        $tahun = $request->query('tahun');

        if ($prodiId) {
            $settings = Pengaturan::where('id_prodi', $prodiId)->first();
        } else {
            $minStart = Pengaturan::min('tahun_mulai');
            $maxEnd = Pengaturan::max('tahun_selesai');
            $activeYear = Pengaturan::value('tahun_aktif');
            if ($minStart && $maxEnd) {
                $settings = (object)[
                    'tahun_mulai' => $minStart,
                    'tahun_selesai' => $maxEnd,
                    'tahun_aktif' => $activeYear ?: date('Y'),
                ];
            } else {
                $settings = null;
            }
        }

        if ($settings) {
            $tahunList = range($settings->tahun_mulai, $settings->tahun_selesai);
        } else {
            $tahunList = PengisianBukti::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
            if (empty($tahunList)) {
                $tahunList = [date('Y')];
            }
        }

        $query = PengisianBukti::with(['user.prodi', 'iku.kategori', 'buktiIku', 'files']);

        //menampilkan data pengisian bukti hanya dari dosen yang berasal dari prodi yang dipilih
        if ($request->filled('prodi_id')) {
            $query->whereHas('user', function ($q) use ($prodiId) {
                $q->where('prodi_id', $prodiId);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $status);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $tahun);
        }

        $riwayat = $query->latest()->paginate(request('per_page', 10));

        return view('adminp2mp.validasi.index', compact(
            'riwayat',
            'prodis',
            'tahunList',
            'prodiId',
            'status',
            'tahun'
        ));
    }

    /**
     * Perbarui status validasi pengajuan bukti.
     */
    public function updateValidasi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:valid,invalid',
            'catatan_validator' => 'nullable|string|max:1000',
        ]);

        $pengisian = PengisianBukti::findOrFail($id);
        
        $pengisian->update([
            'status' => $request->status,
            'catatan_validator' => $request->status === 'invalid' ? $request->catatan_validator : null,
        ]);

        $statusText = $request->status === 'valid' ? 'disetujui (Valid)' : 'ditolak (Perlu Perbaikan)';
        
        $iku = \App\Models\Iku::find($pengisian->id_iku);
        ActivityLog::log('Validasi Bukti', 'Validasi Bukti IKU/IKT', "Status bukti IKU/IKT '{$iku->nama_iku}' diubah menjadi {$statusText}");

        return redirect()->back()->with('success', "Bukti IKU/IKT berhasil {$statusText}.");
    }

    /**
     * Tampilkan halaman Monitoring & Laporan.
     */
    public function monitoring(Request $request)
    {
        $prodis = Prodi::all();
        if ($prodis->isEmpty()) {
            return view('adminp2mp.monitoring.index', [
                'laporan' => collect(),
                'prodis' => collect(),
                'tahunList' => [date('Y')],
                'prodiId' => null,
                'prodiName' => '',
                'tahun' => date('Y'),
                'settings' => null
            ]);
        }

        $prodiId = $request->query('prodi_id', $prodis->first()->id);
        $selectedProdi = Prodi::find($prodiId) ?: $prodis->first();
        $prodiId = $selectedProdi->id;
        $prodiName = $selectedProdi->nama_prodi;

        $settings = Pengaturan::where('id_prodi', $prodiId)->first();
        $tahunAktif = $settings?->tahun_aktif ?? date('Y');

        if ($settings) {
            $tahunList = range($settings->tahun_mulai, $settings->tahun_selesai);
        } else {
            $dbYears = IkuPencapaian::select('tahun')->distinct()->pluck('tahun')->toArray();
            $tahunList = array_unique(array_merge($dbYears, [date('Y') - 1, date('Y'), date('Y') + 1]));
            sort($tahunList);
        }

        $tahun = $request->query('tahun', $tahunAktif);

        // Sinkronisasikan dulu untuk memastikan data di database sudah terbaru
        IkuPencapaian::calculateAndSync($prodiId, $tahun);

        $laporan = IkuPencapaian::with('iku.kategori')
            ->where('id_prodi', $prodiId)
            ->where('tahun', $tahun)
            ->orderBy('id', 'asc') 
            ->get();

        // Filter indikator yang bermasalah (Perlu Perhatian atau Tidak Tercapai)
        $warnings = $laporan->filter(function ($item) {
            return in_array($item->status, ['Perlu Perhatian', 'Tidak Tercapai']);
        });

        $rekomendasiController = new \App\Http\Controllers\RekomendasiAiController();
        $recommendations = $rekomendasiController->getOrGenerate($warnings);

        return view('adminp2mp.monitoring.index', compact(
            'laporan',
            'prodis',
            'tahunList',
            'prodiId',
            'prodiName',
            'tahun',
            'settings',
            'recommendations'
        ));
    }

    /**
     * Export laporan monitoring IKU/IKT ke format Excel.
     */
    public function exportExcel(Request $request)
    {
        $prodis = Prodi::all();
        $prodiId = $request->query('prodi_id');
        
        if ($prodiId) {
            $selectedProdi = Prodi::find($prodiId);
            $prodiName = $selectedProdi ? $selectedProdi->nama_prodi : 'Program Studi';
            $settings = Pengaturan::where('id_prodi', $prodiId)->first();
        } else {
            $prodiName = 'Semua Program Studi';
            $settings = null;
        }

        $tahunAktif = $settings?->tahun_aktif ?? date('Y');
        $tahun = $request->query('tahun', $tahunAktif);

        $sanitizedProdiName = str_replace(' ', '_', preg_replace('/[^\w\s-]/', '', $prodiName));
        $filename = 'Laporan_Capaian_IKU_' . $sanitizedProdiName . '_Tahun_' . $tahun . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LaporanExport($prodiId, $tahun, $prodiName),
            $filename
        );
    }
}
