<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekomendasiAi;
use App\Models\IkuPencapaian;
use App\Models\Iku;
use App\Models\PenilaianRekomendasiAi;
use App\Models\DetailPenilaianRekomendasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenilaianExpertController extends Controller
{
    /**
     * Tampilkan halaman publik penilaian expert (Google Form style).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Ambil rekomendasi AI yang valid (bukan placeholder / error)
        $rekomendasiList = RekomendasiAi::with(['ikuPencapaian.iku', 'ikuPencapaian.prodi'])
            ->where('rekomendasi', 'NOT LIKE', '%Rekomendasi belum di-generate%')
            ->where('rekomendasi', 'NOT LIKE', '%sementara tidak dapat dibuat%')
            ->where('rekomendasi', 'NOT LIKE', '%Gagal menghubungi server%')
            ->get();

        // Ambil daftar Program Studi unik yang memang memiliki rekomendasi AI / IKU tersedia
        $prodiList = $rekomendasiList->map(function ($item) {
            return $item->ikuPencapaian->prodi ?? null;
        })->filter()->unique('id')->values();

        // Opsi IKU untuk dropdown
        $ikuOptions = $rekomendasiList->map(function ($item) {
            $iku = $item->ikuPencapaian->iku ?? null;
            $prodi = $item->ikuPencapaian->prodi ?? null;
            return [
                'id_rekomendasi' => $item->id,
                'id_iku' => $iku ? $iku->id : null,
                'kode_iku' => $iku ? ($iku->kode_iku ?: 'IKU') : 'IKU',
                'nama_iku' => $iku ? $iku->nama_iku : 'Indikator Kinerja',
                'id_prodi' => $prodi ? $prodi->id : null,
                'nama_prodi' => $prodi ? $prodi->nama_prodi : 'Umum',
                'tahun' => $item->ikuPencapaian->tahun ?? date('Y'),
            ];
        });

        return view('evaluasi_expert.index', compact('ikuOptions', 'prodiList'));
    }

    /**
     * Helper untuk membersihkan header faktual legacy dari teks rekomendasi AI.
     *
     * @param string $text
     * @return string
     */
    public static function stripFactualHeader($text)
    {
        if (empty($text)) {
            return '';
        }
        $pattern = '/^(?:###\s+[^\n]*\n+)?(?:-\s+\*\*(?:Nama IKU\/IKT|Program Studi|Tahun Akademik|Target|Realisasi|Status)\*\*:[^\n]*\n+)+/i';
        $cleaned = preg_replace($pattern, '', trim($text));
        return trim($cleaned);
    }

    /**
     * Ambil detail rekomendasi AI & klaim-klaim untuk IKU yang dipilih (via AJAX).
     *
     * @param int $rekomendasiId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRekomendasiData($rekomendasiId)
    {
        try {
            $rekomendasi = RekomendasiAi::with(['ikuPencapaian.iku.kategori', 'ikuPencapaian.prodi'])
                ->findOrFail($rekomendasiId);

            $pencapaian = $rekomendasi->ikuPencapaian;
            $rekomendasiTeks = self::stripFactualHeader($rekomendasi->rekomendasi);
            $claims = $this->extractClaimsFromMarkdown($rekomendasiTeks);

            $targetFormatted = $pencapaian ? ($pencapaian->target . ($pencapaian->satuan === 'persen' ? '%' : '') . ($pencapaian->objek ? " ({$pencapaian->objek})" : '')) : '-';
            $realisasiFormatted = $pencapaian ? (round($pencapaian->realisasi) . ' Bukti') : '-';

            return response()->json([
                'status' => 'success',
                'rekomendasi_id' => $rekomendasi->id,
                'iku_id' => $pencapaian && $pencapaian->iku ? $pencapaian->iku->id : null,
                'nama_iku' => $pencapaian && $pencapaian->iku ? $pencapaian->iku->nama_iku : 'Indikator Kinerja',
                'kode_iku' => $pencapaian && $pencapaian->iku ? ($pencapaian->iku->kode_iku ?: '-') : '-',
                'deskripsi_iku' => $pencapaian && $pencapaian->iku ? ($pencapaian->iku->deskripsi ?: 'Tidak ada deskripsi indikator kinerja.') : '-',
                'prodi' => $pencapaian && $pencapaian->prodi ? $pencapaian->prodi->nama_prodi : '-',
                'tahun' => $pencapaian ? $pencapaian->tahun : '-',
                'target' => $targetFormatted,
                'realisasi' => $realisasiFormatted,
                'status_capaian' => $pencapaian ? $pencapaian->status : '-',
                'rekomendasi_teks' => $rekomendasiTeks,
                'claims' => $claims,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getRekomendasiData Expert: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal mengambil data rekomendasi.'], 500);
        }
    }

    /**
     * Simpan hasil penilaian dari Expert.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_rekomendasi' => 'required|exists:rekomendasi_ai,id',
            'nama_penilai' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'prodi_unit' => 'required|string|max:255',
            'claims' => 'required|array|min:1',
            'claims.*.klaim' => 'required|string',
            'claims.*.persentase_fakta' => 'required|numeric|min:0|max:100',
            'claims.*.persentase_halusinasi' => 'required|numeric|min:0|max:100',
            'claims.*.catatan' => 'nullable|string',
        ]);

        // Validasi tambahan: fakta + halusinasi = 100%, catatan wajib jika halusinasi > 0
        foreach ($request->claims as $index => $claim) {
            $fakta = (float) $claim['persentase_fakta'];
            $halusinasi = (float) $claim['persentase_halusinasi'];
            $catatan = trim($claim['catatan'] ?? '');

            if (abs(($fakta + $halusinasi) - 100) > 0.01) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Klaim #" . ($index + 1) . ": Persentase Fakta ({$fakta}%) + Halusinasi ({$halusinasi}%) harus bernilai total 100%."
                ], 422);
            }

            if ($halusinasi > 0 && empty($catatan)) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Klaim #" . ($index + 1) . ": Catatan/Koreksi wajib diisi karena persentase halusinasi lebih dari 0%."
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            $rekomendasi = RekomendasiAi::findOrFail($request->id_rekomendasi);
            $pencapaian = $rekomendasi->ikuPencapaian;

            // 1. Simpan Header Penilaian Expert
            $penilaian = PenilaianRekomendasiAi::create([
                'id_rekomendasi_ai' => $rekomendasi->id,
                'nama_penilai' => trim($request->nama_penilai),
                'jabatan' => trim($request->jabatan),
                'prodi_unit' => trim($request->prodi_unit),
            ]);

            // 2. Simpan Detail Klaim
            foreach ($request->claims as $claim) {
                DetailPenilaianRekomendasi::create([
                    'id_penilaian' => $penilaian->id,
                    'id_iku' => $pencapaian ? $pencapaian->id_iku : null,
                    'klaim' => $claim['klaim'],
                    'persentase_fakta' => (float) $claim['persentase_fakta'],
                    'persentase_halusinasi' => (float) $claim['persentase_halusinasi'],
                    'catatan' => !empty($claim['catatan']) ? trim($claim['catatan']) : null,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Penilaian rekomendasi AI berhasil disimpan. Terima kasih atas partisipasi dan evaluasi Anda!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error store Penilaian Expert: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan penilaian: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper untuk memecah teks Markdown rekomendasi menjadi klaim-klaim individual.
     *
     * @param string $text
     * @return array
     */
    private function extractClaimsFromMarkdown($text)
    {
        if (empty($text)) {
            return [];
        }

        $lines = explode("\n", $text);
        $cleanLines = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) continue;
            if (preg_match('/^#+\s+/', $trimmed)) continue;

            $cleanLine = preg_replace('/^\s*[-*+]\s+/', '', $trimmed);
            $cleanLine = str_replace(['**', '*', '__', '_', '`'], '', $cleanLine);
            $cleanLine = trim($cleanLine);

            if (!empty($cleanLine)) {
                $cleanLines[] = $cleanLine;
            }
        }

        $fullText = implode(' ', $cleanLines);
        $rawSentences = preg_split('/(?<=[.!?])\s+/', $fullText, -1, PREG_SPLIT_NO_EMPTY);

        $sentences = [];
        foreach ($rawSentences as $sentence) {
            $s = trim($sentence);
            if (mb_strlen($s) > 10) {
                $sentences[] = $s;
            }
        }

        return !empty($sentences) ? $sentences : [$text];
    }
}
