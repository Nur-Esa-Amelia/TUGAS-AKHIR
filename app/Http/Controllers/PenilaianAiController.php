<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IkuPencapaian;
use App\Models\RekomendasiAi;
use App\Models\BuktiIku;
use App\Models\PengisianBukti;
use App\Models\PenilaianRekomendasiAi;
use App\Models\PenilaianKlaimAi;
use App\Models\PenilaianFnAi;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;

class PenilaianAiController extends Controller
{
    /**
     * Dapatkan data pengujian, klaim terpecah, data acuan sistem, dan penilaian tersimpan.
     *
     * @param int $pencapaianId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPenilaianData($pencapaianId)
    {
        try {
            $pencapaian = IkuPencapaian::with(['iku.kategori', 'prodi'])->findOrFail($pencapaianId);
            $rekomendasi = RekomendasiAi::where('id_iku_pencapaian', $pencapaian->id)->first();

            if (!$rekomendasi || empty($rekomendasi->rekomendasi)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Rekomendasi AI belum tersedia untuk indikator ini.'
                ], 404);
            }

            // 1. Data Acuan Sistem (Pembanding Faktual)
            $buktiIkuList = BuktiIku::where('id_iku', $pencapaian->id_iku)->get();
            $sudahDiunggah = [];
            $belumDiunggah = [];

            foreach ($buktiIkuList as $bukti) { //cek 11 bukti
                $pengisians = PengisianBukti::with(['files'])
                    ->where('id_bukti_iku', $bukti->id)
                    ->where('tahun', $pencapaian->tahun)
                    ->whereHas('user', function ($q) use ($pencapaian) {
                        $q->where('prodi_id', $pencapaian->id_prodi);
                    })
                    ->get();

                if ($pengisians->isEmpty()) {
                    $belumDiunggah[] = [
                        'nama_bukti' => $bukti->nama_bukti,
                        'deskripsi' => $bukti->deskripsi ?: '-'
                    ];
                } else {
                    $details = [];
                    foreach ($pengisians as $p) {
                        $details[] = [ //ngambil detail
                            'status' => ucfirst($p->status),
                            'jumlah_berkas' => $p->files->count(),
                            'catatan' => $p->catatan_validator ?: '-'
                        ];
                    }
                    $sudahDiunggah[] = [
                        'nama_bukti' => $bukti->nama_bukti,
                        'deskripsi' => $bukti->deskripsi ?: '-',
                        'details' => $details
                    ];
                }
            }

            $dataAcuan = [ //fakta dari database
                'nama_iku' => $pencapaian->iku ? $pencapaian->iku->nama_iku : 'Indikator Kinerja',
                'deskripsi' => $pencapaian->iku ? ($pencapaian->iku->deskripsi ?: '-') : '-',
                'kategori' => $pencapaian->iku && $pencapaian->iku->kategori ? $pencapaian->iku->kategori->nama_kategori : '-',
                'prodi' => $pencapaian->prodi ? $pencapaian->prodi->nama_prodi : '-',
                'tahun' => $pencapaian->tahun,
                'target' => $pencapaian->target . ($pencapaian->satuan === 'persen' ? '%' : '') . " (" . $pencapaian->objek . ")",
                'realisasi' => round($pencapaian->realisasi) . " bukti valid",
                'status' => $pencapaian->status,
                'bukti_wajib_count' => $buktiIkuList->count(),
                'sudah_diunggah' => $sudahDiunggah, //Memasukkan daftar bukti yang sudah diunggah.
                'belum_diunggah' => $belumDiunggah,
            ];

            // 2. Extrak & Pecah Klaim AI (1 Kalimat = 1 Klaim)
            $extractedClaims = $this->extractClaimsFromMarkdown($rekomendasi->rekomendasi);

            // 3. Ambil Penilaian Tersimpan (Jika ada)
            $savedPenilaian = PenilaianRekomendasiAi::with(['klaimList', 'fnList'])
                ->where('id_rekomendasi_ai', $rekomendasi->id)
                ->first();

            $claimsData = [];
            $savedFnList = [];
            $metrics = null;

            if ($savedPenilaian) {
                $savedKlaimMap = $savedPenilaian->klaimList->keyBy('nomor_klaim');
                foreach ($extractedClaims as $index => $claimText) {
                    $nomor = 'K' . ($index + 1);
                    $savedKlaim = $savedKlaimMap->get($nomor);
                    $claimsData[] = [
                        'nomor' => $nomor,
                        'teks' => $claimText,
                        'status' => $savedKlaim ? $savedKlaim->status_penilaian : 'faktual'
                    ];
                }
                $savedFnList = $savedPenilaian->fnList->pluck('fakta_terlewat')->toArray();
                $metrics = [
                    'total_klaim' => $savedPenilaian->total_klaim,
                    'tp' => $savedPenilaian->tp,
                    'fp' => $savedPenilaian->fp,
                    'fn' => $savedPenilaian->fn,
                    'precision' => $savedPenilaian->precision, //klaim AI yang dinilai benar
                    'recall' => $savedPenilaian->recall, //fakta yang berhasil dicakup AI
                    'f1_score' => $savedPenilaian->f1_score,
                    'has_hallucination' => $savedPenilaian->has_hallucination,
                ];
            } else {
                foreach ($extractedClaims as $index => $claimText) { //AMBIL SEMUA KLAIM AI
                    $claimsData[] = [
                        'nomor' => 'K' . ($index + 1),
                        'teks' => $claimText,
                        'status' => 'faktual'
                    ];
                }
            }

            // 4. Hitung Hallucination Rate Keseluruhan (Berbasis Klaim: FP / Total Klaim AI * 100)
            $totalTp = (int) PenilaianRekomendasiAi::sum('tp');
            $totalFp = (int) PenilaianRekomendasiAi::sum('fp');
            $totalClaimsAi = $totalTp + $totalFp;
            $systemHallucinationRate = $totalClaimsAi > 0 ? round(($totalFp / $totalClaimsAi) * 100, 2) : 0;
            $totalEvaluated = PenilaianRekomendasiAi::count();
            $totalHallucinated = PenilaianRekomendasiAi::where('has_hallucination', true)->count();

            return response()->json([
                'status' => 'success',
                'pencapaian_id' => $pencapaian->id,
                'rekomendasi_id' => $rekomendasi->id,
                'data_acuan' => $dataAcuan,
                'claims' => $claimsData,
                'saved_fn' => $savedFnList,
                'metrics' => $metrics,
                'system_metrics' => [
                    'total_evaluated' => $totalEvaluated,
                    'total_hallucinated' => $totalHallucinated,
                    'total_claims_ai' => $totalClaimsAi,
                    'total_fp' => $totalFp,
                    'hallucination_rate' => $systemHallucinationRate,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getPenilaianData: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Simpan penilaian pengujian rekomendasi AI
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storePenilaian(Request $request)
    {
        $request->validate([
            'pencapaian_id' => 'required|exists:iku_pencapaian,id',
            'claims' => 'required|array',
            'claims.*.nomor' => 'required|string',
            'claims.*.teks' => 'required|string',
            'claims.*.status' => 'required|in:faktual,halusinasi',
            'fn_list' => 'nullable|array',
            'fn_list.*' => 'nullable|string',
        ]);

        try {
            $pencapaian = IkuPencapaian::findOrFail($request->pencapaian_id);
            $rekomendasi = RekomendasiAi::where('id_iku_pencapaian', $pencapaian->id)->firstOrFail();

            $claims = $request->claims; //ambil daftar klaim AI
            $fnRaw = $request->fn_list ?? [];
            $fnList = array_filter(array_map('trim', $fnRaw), fn($val) => !empty($val));

            $tp = 0;
            $fp = 0;
            foreach ($claims as $claim) {
                if ($claim['status'] === 'faktual') {
                    $tp++;
                } elseif ($claim['status'] === 'halusinasi') {
                    $fp++;
                }
            }
            $fn = count($fnList);
            $totalKlaim = count($claims);

            // Hitung Metrik Evaluasi: Precision, Recall, F1-Score
            $precision = ($tp + $fp) > 0 ? round(($tp / ($tp + $fp)) * 100, 2) : 0;
            $recall = ($tp + $fn) > 0 ? round(($tp / ($tp + $fn)) * 100, 2) : 0;
            $f1Score = ($precision + $recall) > 0 ? round((2 * ($precision * $recall) / ($precision + $recall)), 2) : 0;
            $hasHallucination = ($fp > 0);

            // Simpan / update ke database
            $penilaian = PenilaianRekomendasiAi::updateOrCreate(
                ['id_rekomendasi_ai' => $rekomendasi->id],
                [
                    'id_user' => auth()->id() ?? 1,
                    'total_klaim' => $totalKlaim,
                    'tp' => $tp,
                    'fp' => $fp,
                    'fn' => $fn,
                    'precision' => $precision,
                    'recall' => $recall,
                    'f1_score' => $f1Score,
                    'has_hallucination' => $hasHallucination,
                ]
            );

            // Simpan detail klaim
            PenilaianKlaimAi::where('id_penilaian', $penilaian->id)->delete();
            foreach ($claims as $claim) {
                PenilaianKlaimAi::create([
                    'id_penilaian' => $penilaian->id,
                    'nomor_klaim' => $claim['nomor'],
                    'teks_klaim' => $claim['teks'],
                    'status_penilaian' => $claim['status'],
                ]);
            }

            // Simpan detail FN
            PenilaianFnAi::where('id_penilaian', $penilaian->id)->delete();
            foreach ($fnList as $fakta) {
                PenilaianFnAi::create([
                    'id_penilaian' => $penilaian->id,
                    'fakta_terlewat' => $fakta,
                ]);
            }

            // Hitung Ulang Hallucination Rate Keseluruhan (Berbasis Klaim: FP / Total Klaim AI * 100)
            $totalTp = (int) PenilaianRekomendasiAi::sum('tp');
            $totalFp = (int) PenilaianRekomendasiAi::sum('fp');
            $totalClaimsAi = $totalTp + $totalFp;
            $systemHallucinationRate = $totalClaimsAi > 0 ? round(($totalFp / $totalClaimsAi) * 100, 2) : 0;
            $totalEvaluated = PenilaianRekomendasiAi::count();
            $totalHallucinated = PenilaianRekomendasiAi::where('has_hallucination', true)->count();

            ActivityLog::log('Menilai rekomendasi AI', 'Pengujian AI', "Melakukan pengujian AI untuk IKU ID {$pencapaian->id_iku} (F1: {$f1Score}%, Precision: {$precision}%, Recall: {$recall}%)");

            return response()->json([
                'status' => 'success',
                'message' => 'Penilaian Rekomendasi AI berhasil disimpan.',
                'metrics' => [
                    'total_klaim' => $totalKlaim,
                    'tp' => $tp,
                    'fp' => $fp,
                    'fn' => $fn,
                    'precision' => $precision,
                    'recall' => $recall,
                    'f1_score' => $f1Score,
                    'has_hallucination' => $hasHallucination,
                ],
                'system_metrics' => [
                    'total_evaluated' => $totalEvaluated,
                    'total_hallucinated' => $totalHallucinated,
                    'total_claims_ai' => $totalClaimsAi,
                    'total_fp' => $totalFp,
                    'hallucination_rate' => $systemHallucinationRate,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error storePenilaian: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper untuk memecah teks Markdown rekomendasi menjadi kalimat-kalimat klaim.
     *
     * @param string $text
     * @return array
     */
    private function extractClaimsFromMarkdown($text) //memecah klaim
    {
        if (empty($text)) {
            return [];
        }

        // Hapus header Markdown bertanda ### atau ## atau #
        $lines = explode("\n", $text);
        $cleanLines = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) continue; //brs ksg 
            // Abaikan garis judul header utama
            if (preg_match('/^#+\s+/', $trimmed)) continue;

            // Bersihkan bullet point markdown (- **...**)
            $cleanLine = preg_replace('/^\s*[-*+]\s+/', '', $trimmed);
            // Bersihkan tag markdown bold / italic
            $cleanLine = str_replace(['**', '*', '__', '_', '`'], '', $cleanLine);
            $cleanLine = trim($cleanLine);

            if (!empty($cleanLine)) {
                $cleanLines[] = $cleanLine;
            }
        }

        // Gabungkan teks bersih dan pecah berdasarkan tanda titik / kalimat
        $fullText = implode(' ', $cleanLines);
        // Split berdasarkan tanda titik (.) yang diikuti spasi atau akhir baris, tetapi abaikan singkatan angka decimal (0.5)
        $rawSentences = preg_split('/(?<=[.!?])\s+/', $fullText, -1, PREG_SPLIT_NO_EMPTY);

        $sentences = [];
        foreach ($rawSentences as $sentence) {
            $s = trim($sentence);
            if (mb_strlen($s) > 8) { // abaikan potongan kalimat terlalu pendek
                $sentences[] = $s;
            }
        }

        return !empty($sentences) ? $sentences : [$text];
    }
}
