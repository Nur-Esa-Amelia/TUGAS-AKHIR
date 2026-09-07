@extends('dosen.layouts.app')

@section('title', 'Target & Capaian IKU/IKT - Dosen')
@section('page_title', 'Target & Capaian IKU/IKT')
@section('page_subtitle', 'Target pemenuhan IKU/IKT program studi ' . $prodiName . ' dan status bukti yang Anda unggah')

@section('content')
<style>
    @keyframes shimmer {
        0% {
            background-position: -200% 0;
        }
        100% {
            background-position: 200% 0;
        }
    }
    .shimmer {
        background: linear-gradient(90deg, #1e293b 25%, #334155 50%, #1e293b 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }

    #ai-content::-webkit-scrollbar {
        width: 6px;
    }
    #ai-content::-webkit-scrollbar-track {
        background: #0f172a;
    }
    #ai-content::-webkit-scrollbar-thumb {
        background: #334155;
        border-radius: 3px;
    }
    #ai-content::-webkit-scrollbar-thumb:hover {
        background: #475569;
    }
</style>

<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Filter Year Card -->
    <div class="card" style="display: flex; flex-direction: column; gap: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px;">
            <div>
                <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary);">Pilih Tahun Akademik</h3>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">Tampilkan target IKU/IKT prodi dan bukti capaian Anda pada tahun akademik terpilih.</p>
            </div>
            
            <form action="{{ route('dosen.pencapaian.index') }}" method="GET" style="display: flex; align-items: flex-end; gap: 12px; width: 220px;">
                <div class="filter-item-custom" style="width: 100%;">
                    <select id="tahun" name="tahun" class="form-select-custom" onchange="this.form.submit()">
                        @foreach($tahunList as $y)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if($recommendations && $recommendations->isNotEmpty())
        <div class="alert-box alert-warning" style="background: rgba(168, 85, 247, 0.1); border: 1px solid rgba(168, 85, 247, 0.3); color: var(--text-secondary); display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 8px; margin-bottom: 8px;">
            <svg style="width: 18px; height: 18px; color: #c084fc; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21l8.982-11.795H13.62l1.378-6.059L6 15.004h3.813z"></path>
            </svg>
            <div style="font-size: 0.85rem;">
                Terdeteksi <strong>{{ $recommendations->count() }}</strong> indikator dengan status warning. Klik tombol <strong>💡 Rekomendasi</strong> di kolom status tabel untuk melihat saran perbaikan AI.
            </div>
        </div>
    @else
        <div class="alert-box alert-success" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: var(--text-secondary); display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 8px; margin-bottom: 8px;">
            <svg style="width: 18px; height: 18px; color: #10b981; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div style="font-size: 0.85rem;">
                Tidak ada rekomendasi karena seluruh indikator dalam kondisi aman.
            </div>
        </div>
    @endif

    <!-- Table Card -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="min-width: 200px;">Indikator Kinerja Utama</th>
                        <th style="text-align: center; width: 100px;">Target</th>
                        <th style="text-align: center; width: 120px;">Realisasi (Prodi)</th>
                        <th style="text-align: center; width: 130px;">Ketercapaian</th>
                        <th style="min-width: 250px;">Bukti Saya & Status Tugas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pencapaianList as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            
                            <!-- Indikator IKU/IKT -->
                            <td>
                                <div style="font-weight: 600; color: var(--text-primary); line-height: 1.4;">{{ $item->iku->nama_iku }}</div>
                                <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">Kategori: {{ $item->iku->kategori->nama_kategori }}</div>
                            </td>

                            <!-- Target -->
                            <td style="text-align: center; font-weight: 600; color: var(--text-secondary);">
                                {{ $item->target }}{{ $item->satuan === 'persen' ? '%' : '' }}
                                <span style="font-size: 0.65rem; color: var(--text-muted); display: block; font-weight: normal; margin-top: 2px;">({{ $item->objek }})</span>
                            </td>

                            <!-- Realisasi -->
                            <td style="text-align: center; font-weight: 700; color: #3b82f6;">
                                {{ round($item->realisasi) }} Bukti
                            </td>

                            <!-- Ketercapaian -->
                            <td style="text-align: center; vertical-align: middle;">
                                @if($item->status === 'Tercapai')
                                    <span class="badge-custom badge-green">Tercapai</span>
                                @elseif($item->status === 'Perlu Perhatian')
                                    <span class="badge-custom badge-yellow" style="background-color: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.2); color: #fbbf24; display: block; margin-bottom: 6px;">Perlu Perhatian</span>
                                    <button type="button" class="btn-show-ai-rec" data-pencapaian-id="{{ $item->id }}" style="padding: 3px 8px; font-size: 0.72rem; border-radius: 6px; background: rgba(168, 85, 247, 0.15); border: 1px solid rgba(168, 85, 247, 0.3); color: #c084fc; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s; outline: none;">
                                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21l8.982-11.795H13.62l1.378-6.059L6 15.004h3.813z"></path>
                                        </svg>
                                        Rekomendasi
                                    </button>
                                @else
                                    <span class="badge-custom badge-rose" style="display: block; margin-bottom: 6px;">Tidak Tercapai</span>
                                    <button type="button" class="btn-show-ai-rec" data-pencapaian-id="{{ $item->id }}" style="padding: 3px 8px; font-size: 0.72rem; border-radius: 6px; background: rgba(168, 85, 247, 0.15); border: 1px solid rgba(168, 85, 247, 0.3); color: #c084fc; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s; outline: none;">
                                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21l8.982-11.795H13.62l1.378-6.059L6 15.004h3.813z"></path>
                                        </svg>
                                        Rekomendasi
                                    </button>
                                @endif
                            </td>

                            <!-- Bukti Saya & Status Tugas -->
                            <td>
                                @php
                                    $isAssigned = in_array($item->id_iku, $assignedIkuIds);
                                @endphp

                                @if($item->my_proofs->isNotEmpty())
                                    @php
                                        $totalFiles = $item->my_proofs->sum(fn ($proof) => $proof->files->count());
                                    @endphp
                                    <details style="padding: 8px; background-color: var(--bg-surface); border: 1px solid var(--border); border-radius: 6px;">
                                        <summary style="font-size: 0.8rem; color: #38bdf8; cursor: pointer; user-select: none; display: inline-flex; align-items: center; gap: 6px; font-weight: 600; text-decoration: underline; outline: none;">
                                            <svg style="width: 12px; height: 12px; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"></path>
                                            </svg>
                                            Detail Berkas ({{ $totalFiles }})
                                        </summary>
                                        <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 8px; padding: 10px; background-color: var(--bg-surface2); border: 1px solid var(--border); border-radius: 6px;">
                                            @foreach($item->my_proofs as $proof)
                                                <div style="display: flex; flex-direction: column; gap: 6px;">
                                                    <div style="font-size: 0.72rem; color: var(--text-muted); font-weight: 600;">
                                                        Jenis Bukti: {{ $proof->buktiIku->nama_bukti }}
                                                    </div>
                                                    @foreach($proof->files as $file)
                                                        <div style="display: flex; flex-direction: column; gap: 4px; border-bottom: 1px solid var(--border); padding-bottom: 6px; margin-bottom: 4px;">
                                                            <a href="{{ asset($file->file_bukti) }}" target="_blank" style="color: #10b981; text-decoration: underline; font-size: 0.72rem; display: inline-flex; align-items: center; gap: 4px; word-break: break-all; font-weight: 500;">
                                                                <svg style="width: 12px; height: 12px; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                                </svg>
                                                                {{ $file->nama_file }}
                                                            </a>
                                                            @if($file->keterangan)
                                                                <div style="font-size: 0.68rem; color: var(--text-muted); padding-left: 16px; line-height: 1.4;">
                                                                    <strong>Keterangan:</strong> {!! preg_replace('~(https?://[^\s<]+)~i', '<a href="$1" target="_blank" style="color: #3b82f6; text-decoration: underline; word-break: break-all;">$1</a>', e($file->keterangan)) !!}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                    @if($proof->catatan_validator)
                                                        <div style="font-size: 0.68rem; color: #fb7185; padding: 4px; background-color: rgba(244,63,94,0.04); border-radius: 4px; border: 1px solid rgba(244,63,94,0.08);">
                                                            <strong>Catatan:</strong> {{ $proof->catatan_validator }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>

                                        @if($isAssigned && $item->status !== 'Tercapai')
                                            <button type="button" class="btn btn-primary btn-open-upload-modal" data-iku-id="{{ $item->id_iku }}" style="padding: 4px 8px; font-size: 0.7rem; border-radius: 6px; align-self: flex-start; justify-content: center; height: 26px;">
                                                + Tambah Bukti Baru
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    @if($isAssigned && $item->status !== 'Tercapai')
                                        <div style="display: flex; flex-direction: column; gap: 6px;">
                                            <span style="font-size: 0.75rem; color: #f59e0b; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;">
                                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                </svg>
                                                Ditugaskan ke Anda & Belum Diisi
                                            </span>
                                            <button type="button" class="btn btn-primary btn-open-upload-modal" data-iku-id="{{ $item->id_iku }}" style="padding: 6px 12px; font-size: 0.72rem; border-radius: 6px; align-self: flex-start; justify-content: center; height: 28px;">
                                                Unggah Bukti Sekarang
                                            </button>
                                        </div>
                                    @elseif($isAssigned && $item->status === 'Tercapai')
                                        <span style="font-size: 0.75rem; color: #10b981; font-weight: 500;">Target prodi sudah terpenuhi</span>
                                    @else
                                        <span style="font-size: 0.75rem; color: #64748b; font-style: italic;">Bukan Tugas Anda</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 48px; color: var(--text-muted);">
                                <svg style="width: 32px; height: 32px; margin: 0 auto 12px; color: #334155; display: block;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Belum ada data target IKU/IKT prodi untuk tahun akademik {{ $tahun }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('partials.ai_evaluation_modal')
@include('partials.ai_evaluation_script')

<script>
function initDosenPencapaianRecs() {
    const recommendationsData = {!! json_encode(($recommendations && $recommendations->isNotEmpty()) ? $recommendations->keyBy('id_iku_pencapaian') : (object)[]) !!};

    document.querySelectorAll('.btn-show-ai-rec').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const pencapaianId = btn.getAttribute('data-pencapaian-id');
            const data = recommendationsData ? recommendationsData[pencapaianId] : null;
            
            let textToShow = data ? data.rekomendasi : '';
            const metaData = (data && data.iku_pencapaian) ? {
                nama_iku: data.iku_pencapaian.iku ? data.iku_pencapaian.iku.nama_iku : 'Indikator Kinerja',
                status: data.iku_pencapaian.status,
                realisasi: data.iku_pencapaian.realisasi,
                target: data.iku_pencapaian.target
            } : null;

            if (!textToShow || textToShow.includes('Rekomendasi belum di-generate') || textToShow.includes('Layanan AI sedang tidak tersedia') || textToShow.includes('sedang diproses')) {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = `<svg style="width: 12px; height: 12px; animation: spin 1s linear infinite;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses AI...`;
                btn.style.opacity = '0.7';
                btn.style.pointerEvents = 'none';

                fetch('/rekomendasi/generate-ajax/' + pencapaianId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
                    }
                })
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success') {
                        if (!recommendationsData[pencapaianId]) {
                            recommendationsData[pencapaianId] = { rekomendasi: res.rekomendasi, iku_pencapaian: null };
                        } else {
                            recommendationsData[pencapaianId].rekomendasi = res.rekomendasi;
                        }
                        if (typeof openAiModal === 'function') {
                            openAiModal(res.rekomendasi, pencapaianId, metaData);
                        }
                    } else {
                        if (typeof openAiModal === 'function') {
                            openAiModal('**Terjadi kesalahan** saat memproses rekomendasi.', pencapaianId, metaData);
                        }
                    }
                })
                .catch(error => {
                    if (typeof openAiModal === 'function') {
                        openAiModal('**Koneksi gagal.** Silakan periksa jaringan Anda.', pencapaianId, metaData);
                    }
                })
                .finally(() => {
                    btn.innerHTML = originalHtml;
                    btn.style.opacity = '1';
                    btn.style.pointerEvents = 'auto';
                });
            } else {
                if (typeof openAiModal === 'function') {
                    openAiModal(textToShow, pencapaianId, metaData);
                }
            }
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDosenPencapaianRecs);
} else {
    initDosenPencapaianRecs();
}
</script>
@endsection
