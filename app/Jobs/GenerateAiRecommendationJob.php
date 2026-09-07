<?php

namespace App\Jobs;

use App\Models\IkuPencapaian;
use App\Models\RekomendasiAi;
use App\Models\BuktiIku;
use App\Models\PengisianBukti;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateAiRecommendationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; //minimal 2 mnt
    protected $ikuPencapaianId;

    //Create a new job instance.
    public function __construct($ikuPencapaianId)
    {
        $this->ikuPencapaianId = $ikuPencapaianId; //ID ini disimpan agar bisa digunakan saat Job menjalankan rekomendasi
    }

    //Execute the job.
    public function handle(): void
    {
        $item = IkuPencapaian::with(['iku', 'prodi'])->find($this->ikuPencapaianId);
        if (!$item) {
            return;
        }

        // Cek apakah ada model aktif di database
        $hasActiveKeys = \App\Models\GeminiModel::where('status', 'aktif')->exists();

        if (!$hasActiveKeys) {
            RekomendasiAi::updateOrCreate(
                ['id_iku_pencapaian' => $item->id],
                ['rekomendasi' => 'Rekomendasi AI belum tersedia karena tidak ada Konfigurasi Model Gemini yang aktif atau siap digunakan saat ini.']
            );
            return;
        }

        // Catat aktivitas jika ini dipicu (Opsional)

        // ambil data acuan
        $prodiName = $item->prodi ? $item->prodi->nama_prodi : 'Program Studi'; 
        $tahun = $item->tahun; 
        $namaIku = $item->iku ? $item->iku->nama_iku : 'Indikator'; 
        $deskripsi = $item->iku ? ($item->iku->deskripsi ?: 'Tidak ada deskripsi') : 'Tidak ada deskripsi'; 
        $target = $item->target . ($item->satuan === 'persen' ? '%' : '') . " (" . $item->objek . ")"; 
        $realisasi = round($item->realisasi) . " bukti valid"; 
        $status = $item->status;

        // Ambil semua jenis bukti yang wajib dilaporkan untuk IKU/IKT ini
        $buktiIkuList = BuktiIku::where('id_iku', $item->id_iku)->get();

        $sudahDiunggah = [];
        $belumDiunggah = [];

        foreach ($buktiIkuList as $bukti) {
            // Cari pengisian bukti untuk bukti ini, di tahun ini, oleh prodi ini
            $pengisians = PengisianBukti::with(['files'])
                ->where('id_bukti_iku', $bukti->id)
                ->where('tahun', $tahun)
                ->whereHas('user', function ($q) use ($item) {
                    $q->where('prodi_id', $item->id_prodi);
                })
                ->get();

            if ($pengisians->isEmpty()) {
                $belumDiunggah[] = "- **" . $bukti->nama_bukti . "**" . ($bukti->deskripsi ? " ({$bukti->deskripsi})" : "");
            } else {
                $details = [];
                foreach ($pengisians as $p) {
                    $fileCount = $p->files->count();
                    $statusPengisian = ucfirst($p->status);
                    $catatan = $p->catatan_validator ? ", Catatan Validator: \"{$p->catatan_validator}\"" : "";
                    $details[] = "Status: {$statusPengisian} ({$fileCount} berkas){$catatan}";
                }
                $sudahDiunggah[] = "- **" . $bukti->nama_bukti . "**" . ($bukti->deskripsi ? " ({$bukti->deskripsi})" : "") . " [" . implode('; ', $details) . "]";
            }
        }

        $prompt = "Anda adalah Asisten AI Sistem Early Warning IKU/IKT (Indikator Kinerja Utama) Perguruan Tinggi.\n";
        $prompt .= "Berikan analisis risiko dan rekomendasi perbaikan untuk indikator yang tidak tercapai berikut:\n\n";
        $prompt .= "### 1. Data IKU/IKT & Deskripsi\n";
        $prompt .= "- Nama IKU/IKT: " . $namaIku . "\n";
        $prompt .= "- Deskripsi: " . $deskripsi . "\n";
        $prompt .= "- Program Studi: " . $prodiName . "\n";
        $prompt .= "- Tahun Akademik: " . $tahun . "\n";
        $prompt .= "- Target: " . $target . "\n";
        $prompt .= "- Realisasi: " . $realisasi . "\n";
        $prompt .= "- Status: " . $status . " (Perlu Perhatian / Tidak Tercapai)\n\n";

        $prompt .= "### 2. Jenis Bukti yang Wajib Dilaporkan\n";
        if ($buktiIkuList->isEmpty()) {
            $prompt .= "(Belum didefinisikan untuk IKU/IKT ini)\n\n";
        } else {
            foreach ($buktiIkuList as $bukti) {
                $prompt .= "- **" . $bukti->nama_bukti . "**" . ($bukti->deskripsi ? ": " . $bukti->deskripsi : "") . "\n";
            }
            $prompt .= "\n";
        }

        $prompt .= "### 3. Perbandingan Bukti yang Sudah dan Belum Diunggah\n";
        if ($buktiIkuList->isEmpty()) {
            $prompt .= "(Tidak dapat dibandingkan karena jenis bukti belum didefinisikan)\n\n";
        } else {
            $prompt .= "**Bukti yang Sudah Diunggah:**\n";
            if (empty($sudahDiunggah)) {
                $prompt .= "- (Belum ada bukti yang diunggah)\n";
            } else {
                foreach ($sudahDiunggah as $s) {
                    $prompt .= $s . "\n"; //masukkan data ygdi unggah ke prompt
                }
            }
            $prompt .= "\n**Bukti yang Belum Diunggah:**\n";
            if (empty($belumDiunggah)) {
                $prompt .= "- (Semua jenis bukti wajib sudah memiliki unggahan)\n";
            } else {
                foreach ($belumDiunggah as $b) {
                    $prompt .= $b . "\n"; //Memasukkan nama dan informasi bukti yang belum diunggah ke prompt.
                }
            }
            $prompt .= "\n";
        }

        $prompt .= "Tugas Anda:\n";
        $prompt .= "Berikan analisis terperinci yang mencakup tiga bagian berikut dengan sub-heading yang jelas:\n";
        $prompt .= "1. Prioritas Penanganan: Berikan prioritas penanganan (Tinggi / Sedang / Rendah) beserta alasan taktisnya.\n";
        $prompt .= "2. Analisis Risiko: Uraikan dampak buruk jika indikator ini terus-menerus tidak tercapai.\n";
        $prompt .= "3. Rekomendasi Perbaikan: Uraikan langkah-langkah konkret, strategis, dan realistis untuk meningkatkan capaian IKU/IKT tersebut.\n\n";
        $prompt .= "PENTING:\n";
        $prompt .= "- Analisislah perbandingan bukti yang sudah dan belum diunggah di atas secara mendalam. Rekomendasi perbaikan harus didasarkan pada kondisi nyata tersebut (misal: menyuruh mengunggah bukti yang belum ada, menindaklanjuti bukti yang ditolak/pending, dll.), sehingga rekomendasi yang dihasilkan sangat spesifik sesuai dengan kondisi nyata pada indikator tersebut dan tidak bersifat general/umum.\n";
        $prompt .= "- Jangan ulangi lagi bagian informasi data IKU/IKT, jenis bukti wajib, atau perbandingan bukti di jawaban Anda. Mulailah respon Anda langsung dengan heading/sub-heading untuk 3 poin analisis di atas.\n\n";
        $prompt .= "Sajikan jawaban Anda dalam Bahasa Indonesia yang formal, ringkas, terstruktur menggunakan format markdown (gunakan bullet points, sub-heading, dan cetak tebal).";

        // Mempersiapkan teks detail informasi untuk disimpan ke database dan ditampilkan di modal
        $headerText = "### Analisis Risiko dan Rekomendasi Perbaikan IKU/IKT {$namaIku}\n\n";
        $headerText .= "- **Nama IKU/IKT**: " . $namaIku . " (" . $deskripsi . ")\n";
        $headerText .= "- **Program Studi**: " . $prodiName . "\n";
        $headerText .= "- **Tahun Akademik**: " . $tahun . "\n";
        $headerText .= "- **Target**: " . $target . "\n";
        $headerText .= "- **Realisasi**: " . $realisasi . "\n";
        $headerText .= "- **Status**: " . $status . "\n\n";

        $recommendationText = 'Layanan AI sedang tidak tersedia atau seluruh kuota API Key telah habis. Silakan coba beberapa saat lagi.';

        $activeModels = \App\Models\GeminiModel::where('status', 'aktif')
            ->where(function($q) {
                $q->whereNull('cooldown_until')->orWhere('cooldown_until', '<', now());
            })
            // Mengurutkan model yang belum pernah digunakan
            ->orderByRaw('last_used_at IS NULL DESC, last_used_at ASC')
            ->get();

        if ($activeModels->isEmpty()) { //apakah model ksg
            $activeModels = \App\Models\GeminiModel::where('status', 'aktif')
                ->orderByRaw('last_used_at IS NULL DESC, last_used_at ASC')
                ->get();
        }

        $success = false;

        foreach ($activeModels as $activeModel) {
            $apiKey = $activeModel->api_key; //smbil api key
            $model = $activeModel->model_id;

            try {
                $response = Http::timeout(30)->post(
                    //alamat endpoint Gemini API.
                    'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $apiKey,
                    [
                        'contents' => [ //strktr dt yg di kirim
                            [
                                 'parts' => [
                                     [
                                         'text' => $prompt
                                     ]
                                 ]
                            ]
                        ]
                    ]
                );

                if ($response->successful()) {
                    $result = $response->json();
                    $aiText = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Gagal memproses rekomendasi AI.';
                    $recommendationText = $headerText . $aiText;
                    
                    // Berhasil, perbarui last_used_at dan hapus cooldown
                    $activeModel->update([
                        'last_used_at' => now(),
                        'cooldown_until' => null
                    ]);
                    
                    Log::info('Gemini API Success', [
                        'config_id' => $activeModel->id,
                        'model_id' => $model,
                        'key_prefix' => substr($apiKey, 0, 4) . '***',
                    ]);

                    $success = true;
                    break; // Berhenti karena sudah berhasil
                } else {
                    $status = $response->status();
                    $errorBody = $response->body();
                    
                    Log::warning('Gemini API Failed', [
                        'config_id' => $activeModel->id,
                        'model_id' => $model,
                        'key_prefix' => substr($apiKey, 0, 4) . '***',
                        'status' => $status,
                        'response' => $errorBody
                    ]);

                    // Berikan cooldown agar key/model yang error/overload sementara dilewati untuk request selanjutnya
                    $cooldownMinutes = in_array($status, [500, 502, 503, 504]) ? 2 : 5;
                    $activeModel->update([
                        'cooldown_until' => now()->addMinutes($cooldownMinutes)
                    ]);
                }
            } catch (\Exception $e) {
                // Connection error atau timeout, beri cooldown 2 menit agar request berikutnya langsung memakai key lain yang sehat
                Log::error('Gemini API Connection Error', [
                    'config_id' => $activeModel->id,
                    'model_id' => $model,
                    'key_prefix' => substr($apiKey, 0, 4) . '***',
                    'message' => $e->getMessage()
                ]);
                
                $activeModel->update([
                    'cooldown_until' => now()->addMinutes(2)
                ]);
            }
        }

        // Jika gagal, log message
        if (!$success) {
            $recommendationText = 'Layanan AI sedang tidak tersedia atau seluruh kuota API Key telah habis. Silakan coba beberapa saat lagi.';
        }

        RekomendasiAi::updateOrCreate(
            ['id_iku_pencapaian' => $this->ikuPencapaianId],
            ['rekomendasi' => $recommendationText]
        );
    }
}
