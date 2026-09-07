<?php

namespace App\Http\Controllers\AdminSistem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PenilaianRekomendasiAi;
use App\Models\DetailPenilaianRekomendasi;
use App\Models\RekomendasiAi;
use App\Models\Iku;
use App\Models\Prodi;
use App\Models\IkuPencapaian;

class HasilEvaluasiController extends Controller
{
    /**
     * Tampilkan halaman Hasil Evaluasi AI Khusus Admin.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $filterTahun = $request->get('tahun');
        $filterIku = $request->get('iku_id');
        $filterProdi = $request->get('prodi_id');
        $filterRekomendasi = $request->get('rekomendasi_id');

        // Ambil data penilaians dengan relasi detail dan rekomendasi
        $query = PenilaianRekomendasiAi::with([
            'rekomendasiAi.ikuPencapaian.iku',
            'rekomendasiAi.ikuPencapaian.prodi',
            'details'
        ]);

        if ($filterRekomendasi) {
            $query->where('id_rekomendasi_ai', $filterRekomendasi);
        }

        if ($filterTahun || $filterIku || $filterProdi) {
            $query->whereHas('rekomendasiAi.ikuPencapaian', function ($q) use ($filterTahun, $filterIku, $filterProdi) {
                if ($filterTahun) {
                    $q->where('tahun', $filterTahun);
                }
                if ($filterIku) {
                    $q->where('id_iku', $filterIku);
                }
                if ($filterProdi) {
                    $q->where('id_prodi', $filterProdi);
                }
            });
        }

        $allPenilaian = $query->get();

        // ==================== KALKULASI 3 TAHAP ====================
        
        // Structure data per Expert & per IKU
        $expertData = []; // [expertKey => ['nama', 'jabatan', 'prodi', 'ikus' => [ikuId => ['fakta', 'halusinasi', 'claims_count']]]]
        $ikuData = [];    // [ikuId => ['nama_iku', 'kode_iku', 'prodi', 'tahun', 'experts_fakta' => []]]
        $claimDetails = []; // [rekomendasiId => ['rekomendasi_teks', 'iku_name', 'claims' => [claimText => ['ratings' => [expertRatings]]]]]

        foreach ($allPenilaian as $penilaian) {
            $expertKey = strtolower(trim($penilaian->nama_penilai)) . '_' . strtolower(trim($penilaian->jabatan));
            if (!isset($expertData[$expertKey])) {
                $expertData[$expertKey] = [
                    'nama_penilai' => $penilaian->nama_penilai,
                    'jabatan' => $penilaian->jabatan,
                    'prodi_unit' => $penilaian->prodi_unit,
                    'ikus' => [], // iku_id => rata-rata fakta IKU ini untuk expert ini
                ];
            }

            $rekomendasi = $penilaian->rekomendasiAi;
            $pencapaian = $rekomendasi ? $rekomendasi->ikuPencapaian : null;
            $iku = $pencapaian ? $pencapaian->iku : null;
            $ikuId = $iku ? $iku->id : 0;
            $ikuNama = $iku ? $iku->nama_iku : 'Indikator Kinerja';
            $ikuKode = $iku ? ($iku->kode_iku ?: 'IKU') : 'IKU';
            $prodiNama = $pencapaian && $pencapaian->prodi ? $pencapaian->prodi->nama_prodi : $penilaian->prodi_unit;
            $tahun = $pencapaian ? $pencapaian->tahun : '-';

            // TAHAP 1: Calculate average per IKU for this expert
            $details = $penilaian->details;
            $totalClaimsInPenilaian = $details->count();

            if ($totalClaimsInPenilaian > 0) {
                $sumFaktaIku = $details->sum('persentase_fakta');
                $avgFaktaIkuForExpert = round($sumFaktaIku / $totalClaimsInPenilaian, 2);
                $avgHaluIkuForExpert = round(100 - $avgFaktaIkuForExpert, 2);

                // Simpan nilai IKU untuk expert ini
                $expertData[$expertKey]['ikus'][$ikuId] = [
                    'fakta' => $avgFaktaIkuForExpert,
                    'halusinasi' => $avgHaluIkuForExpert,
                ];

                // Tambahkan ke data IKU global
                if (!isset($ikuData[$ikuId])) {
                    $ikuData[$ikuId] = [
                        'id_iku' => $ikuId,
                        'kode_iku' => $ikuKode,
                        'nama_iku' => $ikuNama,
                        'prodi' => $prodiNama,
                        'tahun' => $tahun,
                        'expert_scores' => [],
                    ];
                }
                $ikuData[$ikuId]['expert_scores'][] = $avgFaktaIkuForExpert;
            }

            // Detail Klaim Grouping
            $recId = $rekomendasi ? $rekomendasi->id : 0;
            if (!isset($claimDetails[$recId])) {
                $claimDetails[$recId] = [
                    'id_rekomendasi' => $recId,
                    'nama_iku' => $ikuNama,
                    'kode_iku' => $ikuKode,
                    'prodi' => $prodiNama,
                    'tahun' => $tahun,
                    'rekomendasi_teks' => $rekomendasi ? $rekomendasi->rekomendasi : '',
                    'claims_map' => [],
                ];
            }

            foreach ($details as $d) {
                $klaimText = trim($d->klaim);
                if (!isset($claimDetails[$recId]['claims_map'][$klaimText])) {
                    $claimDetails[$recId]['claims_map'][$klaimText] = [
                        'teks' => $klaimText,
                        'ratings' => [],
                    ];
                }
                $claimDetails[$recId]['claims_map'][$klaimText]['ratings'][] = [
                    'nama_penilai' => $penilaian->nama_penilai,
                    'jabatan' => $penilaian->jabatan,
                    'prodi_unit' => $penilaian->prodi_unit,
                    'persentase_fakta' => $d->persentase_fakta,
                    'persentase_halusinasi' => $d->persentase_halusinasi,
                    'catatan' => $d->catatan,
                ];
            }
        }

        // TAHAP 2: Calculate overall Expert score (Average of IKUs rated by that expert)
        $processedExperts = [];
        $sumExpertFakta = 0;

        foreach ($expertData as $key => $exp) {
            $countIkus = count($exp['ikus']);
            $expertFakta = 0;
            if ($countIkus > 0) {
                $sumFakta = array_sum(array_column($exp['ikus'], 'fakta'));
                $expertFakta = round($sumFakta / $countIkus, 2);
            }
            $expertHalu = round(100 - $expertFakta, 2);

            $processedExperts[] = [
                'nama_penilai' => $exp['nama_penilai'],
                'jabatan' => $exp['jabatan'],
                'prodi_unit' => $exp['prodi_unit'],
                'jumlah_iku' => $countIkus,
                'fakta' => $expertFakta,
                'halusinasi' => $expertHalu,
            ];

            $sumExpertFakta += $expertFakta;
        }

        // TAHAP 3: Calculate Overall system score across ALL experts
        $totalExpertsCount = count($processedExperts);
        $overallFakta = $totalExpertsCount > 0 ? round($sumExpertFakta / $totalExpertsCount, 2) : 0;
        $overallHalusinasi = round(100 - $overallFakta, 2);

        // Process IKU averages
        $processedIkus = [];
        foreach ($ikuData as $ikuId => $data) {
            $scores = $data['expert_scores'];
            $count = count($scores);
            $avgFakta = $count > 0 ? round(array_sum($scores) / $count, 2) : 0;
            $avgHalu = round(100 - $avgFakta, 2);

            $processedIkus[] = [
                'id_iku' => $ikuId,
                'kode_iku' => $data['kode_iku'],
                'nama_iku' => $data['nama_iku'],
                'prodi' => $data['prodi'],
                'tahun' => $data['tahun'],
                'fakta' => $avgFakta,
                'halusinasi' => $avgHalu,
                'jumlah_expert' => $count,
            ];
        }

        // Process claim averages
        foreach ($claimDetails as $recId => &$recInfo) {
            foreach ($recInfo['claims_map'] as $text => &$claimObj) {
                $ratings = $claimObj['ratings'];
                $rCount = count($ratings);
                $sumF = 0;
                foreach ($ratings as $r) {
                    $sumF += $r['persentase_fakta'];
                }
                $avgF = $rCount > 0 ? round($sumF / $rCount, 2) : 0;
                $avgH = round(100 - $avgF, 2);

                $claimObj['avg_fakta'] = $avgF;
                $claimObj['avg_halusinasi'] = $avgH;
            }
        }

        // Filter options for dropdowns
        $tahunList = IkuPencapaian::select('tahun')->distinct()->pluck('tahun');
        $ikuList = Iku::all();
        $prodiList = Prodi::all();
        $rekomendasiList = RekomendasiAi::with('ikuPencapaian.iku')->get();

        // Ringkasan Metrics
        $totalIkuCount = count($processedIkus);
        $totalRekomendasiCount = count($claimDetails);

        return view('adminsistem.hasil_evaluasi.index', compact(
            'totalExpertsCount',
            'totalIkuCount',
            'totalRekomendasiCount',
            'overallFakta',
            'overallHalusinasi',
            'processedIkus',
            'processedExperts',
            'claimDetails',
            'tahunList',
            'ikuList',
            'prodiList',
            'rekomendasiList',
            'filterTahun',
            'filterIku',
            'filterProdi',
            'filterRekomendasi'
        ));
    }
}
