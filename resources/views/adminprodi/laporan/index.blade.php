@extends('adminprodi.layouts.app')

@section('title', 'Laporan Capaian IKU/IKT - Admin Prodi')
@section('page_title', 'Laporan Capaian IKU/IKT')
@section('page_subtitle', 'Rekapitulasi pencapaian target Indikator Kinerja Utama Program Studi')

@section('content')
<style>
    /* Styling khusus cetak laporan */
    @media print {
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            font-size: 11pt !important;
        }
        .sidebar, .top-header, .btn, .filter-row-custom, form, #ai-recommendation-card {
            display: none !important;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        .card {
            background-color: transparent !important;
            border: none !important;
            padding: 0 !important;
        }
        .table-custom {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        .table-custom th {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border: 1px solid #cbd5e1 !important;
            font-weight: bold !important;
        }
        .table-custom td {
            color: #0f172a !important;
            border: 1px solid #cbd5e1 !important;
        }
        .badge-custom {
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            font-weight: bold !important;
        }
        .badge-green {
            color: #15803d !important;
        }
        .badge-rose {
            color: #b91c1c !important;
        }
        .badge-yellow {
            color: #d97706 !important;
        }
        .badge-purple {
            color: #6b21a8 !important;
        }
        .print-header {
            display: block !important;
            text-align: center !important;
            margin-bottom: 30px !important;
        }
        .print-title {
            font-size: 18pt !important;
            font-weight: 700 !important;
            margin-bottom: 5px !important;
        }
        .print-subtitle {
            font-size: 11pt !important;
            color: #475569 !important;
        }
    }

    .print-header {
        display: none;
    }

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

<!-- Printable Header -->
<div class="print-header">
    <div class="print-title">LAPORAN CAPAIAN INDIKATOR KINERJA UTAMA (IKU/IKT)</div>
    <div class="print-subtitle">PROGRAM STUDI: {{ strtoupper($prodiName) }} - TAHUN AKADEMIK {{ $tahun }}</div>
    <hr style="border: 0; border-top: 2px solid #000; margin-top: 15px;">
</div>

<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Filter Year & Print Button Card -->
    <div class="card" style="display: flex; flex-direction: column; gap: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px;">
            <form action="{{ route('adminprodi.laporan.index') }}" method="GET" style="display: flex; align-items: flex-end; gap: 12px; flex: 1;">
                @if(auth()->user()->role === 'admin_p2mp' && isset($prodis))
                    <div class="filter-item-custom" style="max-width: 250px;">
                        <label for="prodi_id" class="form-label-custom">Pilih Program Studi</label>
                        <select id="prodi_id" name="prodi_id" class="form-select-custom" onchange="this.form.submit()">
                            @foreach($prodis as $p)
                                <option value="{{ $p->id }}" {{ $selectedProdiId == $p->id ? 'selected' : '' }}>{{ $p->nama_prodi }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="filter-item-custom" style="max-width: 200px;">
                    <label for="tahun" class="form-label-custom">Pilih Tahun Akademik</label>
                    <select id="tahun" name="tahun" class="form-select-custom" onchange="this.form.submit()">
                        @foreach($tahunList as $y)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <div style="position: relative; display: inline-block;" class="dropdown-export">
                <button type="button" class="btn btn-primary" style="padding: 10px 18px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 8px;" onclick="document.getElementById('export-menu').style.display = document.getElementById('export-menu').style.display === 'none' ? 'block' : 'none'; event.stopPropagation();">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.82l-.24-2.07A1.99 1.99 0 004.5 9.75h-.5m16 0h-.5a1.99 1.99 0 00-1.98 2.01l-.24 2.07M4 9.75V7.5a3 3 0 013-3h10a3 3 0 013 3v2.25m-14 0h14m-12 9h10a2 2 0 002-2v-3.75a2 2 0 00-2-2H7a2 2 0 00-2 2V17a2 2 0 002 2z"></path>
                    </svg>
                    Cetak Laporan
                    <svg style="width: 12px; height: 12px; margin-left: 2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="export-menu" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 5px; background: white; border: 1px solid var(--border); border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); width: 240px; z-index: 50; overflow: hidden;">
                    <button type="button" onclick="window.print(); document.getElementById('export-menu').style.display = 'none';" style="width: 100%; text-align: left; padding: 12px 16px; background: transparent; border: none; font-size: 0.85rem; color: var(--text-primary); cursor: pointer; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid var(--border);" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'">
                        <svg style="width: 18px; height: 18px; color: #ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        PDF
                    </button>
                    <a href="{{ route('adminprodi.laporan.export-excel', ['tahun' => $tahun, 'prodi_id' => $selectedProdiId ?? null]) }}" style="width: 100%; text-align: left; padding: 12px 16px; background: transparent; border: none; font-size: 0.85rem; color: var(--text-primary); cursor: pointer; display: flex; align-items: center; gap: 10px; text-decoration: none;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'" onclick="document.getElementById('export-menu').style.display = 'none';">
                        <svg style="width: 18px; height: 18px; color: #10b981;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Excel (.xlsx)
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($recommendations && $recommendations->isNotEmpty())
        <div class="alert-box alert-warning" style="background: rgba(168, 85, 247, 0.1); border: 1px solid rgba(168, 85, 247, 0.3); color: var(--text-secondary); display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 8px; margin-bottom: 8px;">
            <svg style="width: 18px; height: 18px; color: #c084fc; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21l8.982-11.795H13.62l1.378-6.059L6 15.004h3.813z"></path>
            </svg>
            <div style="font-size: 0.85rem; color: var(--text-secondary);">
                Terdeteksi <strong>{{ $recommendations->count() }}</strong> indikator dengan status warning. Klik tombol <strong>💡 Rekomendasi</strong> di kolom status tabel untuk melihat saran perbaikan AI.
            </div>
        </div>
    @else
        <div class="alert-box alert-success" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: var(--text-secondary); display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 8px; margin-bottom: 8px;">
            <svg style="width: 18px; height: 18px; color: #10b981; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div style="font-size: 0.85rem; color: var(--text-secondary);">
                Tidak ada rekomendasi karena seluruh indikator dalam kondisi aman.
            </div>
        </div>
    @endif

    <!-- Table Card -->
    <div class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
        <div style="padding: 20px 24px; border-bottom: 1px solid var(--border);">
            <h3 class="text-base font-bold" style="font-size: 0.95rem; margin-bottom: 2px; color: var(--text-primary);">Capaian IKU/IKT Program Studi {{ $prodiName }}</h3>
            <p style="font-size: 0.75rem; color: var(--text-muted);">Daftar target, realisasi, dan status pencapaian IKU/IKT untuk tahun akademik {{ $tahun }}.</p>
        </div>

        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Kategori IKU/IKT</th>
                        <th>Nama Indikator IKU/IKT</th>
                        <th style="text-align: center;">Target Sasaran</th>
                        <th style="text-align: center;">Realisasi (Valid)</th>
                        <th style="text-align: center;">Capaian (%)</th>
                        <th style="text-align: center; width: 140px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $index => $item)
                        @php
                            // Hitung target riil/nyata untuk display capaian persen
                            $targetVal = floatval($item->target);
                            $jml_mahasiswa = $settings ? $settings->jml_mahasiswa : 0;
                            $jml_dosen = $settings ? $settings->jml_dosen : 0;
                            
                            if ($item->satuan === 'persen') {
                                if ($item->objek === 'mahasiswa') {
                                    $targetNyata = ($targetVal / 100) * $jml_mahasiswa;
                                } elseif ($item->objek === 'dosen') {
                                    $targetNyata = ($targetVal / 100) * $jml_dosen;
                                } else {
                                    $targetNyata = $targetVal;
                                }
                            } else {
                                $targetNyata = $targetVal;
                            }

                            // Persentase pencapaian terhadap target nyata
                            if ($targetNyata > 0) {
                                $persentase = min(round(($item->realisasi / $targetNyata) * 100), 100);
                            } else {
                                $persentase = $item->realisasi > 0 ? 100 : 0;
                            }
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="badge-custom badge-purple">{{ $item->iku->kategori->nama_kategori }}</span>
                            </td>
                            <td style="font-weight: 600; color: var(--text-primary);">{{ $item->iku->nama_iku }}</td>
                            <td style="text-align: center; font-weight: 700; color: var(--text-secondary);">
                                {{ $item->target }}{{ $item->satuan === 'persen' ? '%' : '' }}
                                <span style="font-size: 0.7rem; color: var(--text-muted); display: block; font-weight: normal;">({{ $item->objek }})</span>
                            </td>
                            <td style="text-align: center; font-weight: 700; color: #3b82f6;">
                                {{ round($item->realisasi) }} Bukti
                            </td>
                            <td style="text-align: center; font-weight: 700; color: {{ $persentase >= 100 ? '#10b981' : ($persentase >= 60 ? '#fbbf24' : '#ef4444') }};">
                                {{ $persentase }}%
                            </td>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                Belum ada target dan realisasi IKU/IKT yang tercatat untuk tahun akademik {{ $tahun }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tanda Tangan Cetak (khusus print) -->
        <div class="print-header" style="margin-top: 50px;">
            <div style="display: flex; justify-content: space-between; padding: 0 50px;">
                <div style="text-align: center;">
                    <p>Mengetahui,</p>
                    <p style="font-weight: bold; margin-top: 50px;">Ketua Program Studi</p>
                    <p style="color: var(--text-muted); font-size: 9pt;">(Tanda Tangan & Nama Terang)</p>
                </div>
                <div style="text-align: center;">
                    <p>Dibuat Oleh,</p>
                    <p style="font-weight: bold; margin-top: 50px;">{{ auth()->user()->role === 'kaprodi' ? 'Ketua Program Studi' : 'Admin Program Studi' }}</p>
                    <p style="color: var(--text-muted); font-size: 9pt;">{{ auth()->user()->name }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.ai_evaluation_modal')
@include('partials.ai_evaluation_script')

<script>
function initAdminProdiLaporanRecs() {
    const recommendationsData = {!! json_encode(($recommendations && $recommendations->isNotEmpty()) ? $recommendations->keyBy('id_iku_pencapaian') : (object)[]) !!};

    ////menentukan tombol rekomendasi AI mana yang diklik
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

                ////membuat/generate rekomendasi AI
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

    // menutup dropdown export mengklik di luar area
    window.addEventListener('click', function(e) {
        const menu = document.getElementById('export-menu');
        if (menu && menu.style.display === 'block' && !e.target.closest('.dropdown-export')) {
            menu.style.display = 'none';
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAdminProdiLaporanRecs);
} else {
    initAdminProdiLaporanRecs();
}
</script>
@endsection
