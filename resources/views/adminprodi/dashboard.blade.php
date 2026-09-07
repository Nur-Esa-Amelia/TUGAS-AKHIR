@extends('adminprodi.layouts.app')

@section('title', 'Dashboard Admin Prodi - Sistem Early Warning IKU/IKT')
@section('page_title', 'Selamat Datang di Sistem Monitoring Pencapaian Indikator Kinerja Politeknik Sukabumi')
@section('page_subtitle', '')

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

<!-- Check configuration warning -->
@if(!$settings || $settings->jml_mahasiswa == 0 || $settings->jml_dosen == 0)
<div class="alert-box alert-danger" style="margin-bottom: 20px;">
    <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
    </svg>
    <div>
        <strong>Perhatian!</strong> Pengaturan jumlah mahasiswa atau dosen prodi Anda belum diset atau masih bernilai 0. Silakan isi terlebih dahulu di menu 
        <a href="{{ route('adminprodi.pengaturan.index') }}" style="color: inherit; text-decoration: underline; font-weight: bold;">Pengaturan Sistem</a> 
        agar perhitungan target persentase berjalan dengan akurat.
    </div>
</div>
@endif

<!-- Welcome Announcement Card -->
<div class="card welcome-card">
    <div class="welcome-text">
        <span class="welcome-badge">Program Studi {{ $prodiName }}</span>
        <h3 class="welcome-title">Halo, {{ auth()->user()->name }}!</h3>
        <p class="welcome-desc">
            Anda login sebagai <strong>{{ auth()->user()->role === 'kaprodi' ? 'Ketua Program Studi (Kaprodi)' : 'Admin Program Studi' }}</strong>. 
        </p>
    </div>
    <div style="display: flex; flex-direction: column; gap: 12px; flex-shrink: 0; min-width: 280px; max-width: 480px; width: 100%;">
        <!-- Tahun Akademik Aktif -->
        <div class="system-time-card" style="align-self: center; box-sizing: border-box; margin: 0; min-width: 220px;">
            <div class="time-icon time-icon-1">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="time-text">
                <span class="time-label">Tahun Akademik Aktif</span>
                <span class="time-value time-value-1">{{ $tahunAktif }}</span>
            </div>
        </div>

        <div style="display: flex; gap: 12px; width: 100%;">
            <!-- Jumlah Mahasiswa -->
            <div class="system-time-card" style="flex: 1; box-sizing: border-box; margin: 0; padding: 10px 14px;">
                <div class="time-icon time-icon-2" style="width: 36px; height: 36px;">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="time-text">
                    <span class="time-label" style="font-size: 0.6rem;">Jumlah Mahasiswa</span>
                    <span class="time-value time-value-2" style="font-size: 0.8rem;">{{ $settings?->jml_mahasiswa ?? 0 }} Mhs</span>
                </div>
            </div>

            <!-- Jumlah Dosen -->
            <div class="system-time-card" style="flex: 1; box-sizing: border-box; margin: 0; padding: 10px 14px;">
                <div class="time-icon time-icon-3" style="width: 36px; height: 36px;">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="time-text">
                    <span class="time-label" style="font-size: 0.6rem;">Jumlah Dosen</span>
                    <span class="time-value time-value-3" style="font-size: 0.8rem;">{{ $settings?->jml_dosen ?? 0 }} Dosen</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Metrics Cards Section -->
<div class="dashboard-grid">
    <!-- Total Target IKU/IKT -->
    <div class="stat-card">
        <div>
            <div class="stat-header">
                <div class="stat-info">
                    <span class="stat-label">Total Target IKU/IKT</span>
                    <h4 class="stat-value">{{ $totalTargets }}</h4>
                </div>
                <div class="stat-icon target">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-footer">
            <span class="stat-desc">Target diatur tahun {{ $tahunAktif }}</span>
            <a href="{{ route('adminprodi.pencapaian.index') }}" class="stat-link">
                Detail
                <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>

    <!-- Total Bukti Terbaca (Valid) -->
    <div class="stat-card">
        <div>
            <div class="stat-header">
                <div class="stat-info">
                    <span class="stat-label">Realisasi (Bukti Valid)</span>
                    <h4 class="stat-value">{{ $totalValidProofs }}</h4>
                </div>
                <div class="stat-icon realisasi">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-footer">
            <span class="stat-desc">Telah divalidasi P2MP</span>
            <span class="badge-custom badge-green" style="font-size: 0.6rem;">Valid</span>
        </div>
    </div>

    <!-- IKU/IKT Tercapai -->
    <div class="stat-card">
        <div>
            <div class="stat-header">
                <div class="stat-info">
                    <span class="stat-label">IKU/IKT Tercapai</span>
                    <h4 class="stat-value" style="color: #10b981;">{{ $achievedCount }}</h4>
                </div>
                <div class="stat-icon tercapai">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-footer">
            <span class="stat-desc">Target terpenuhi</span>
            <span class="badge-custom badge-blue">
                {{ $totalTargets > 0 ? round(($achievedCount / $totalTargets) * 100) : 0 }}%
            </span>
        </div>
    </div>

    <!-- IKU/IKT Belum Tercapai -->
    <div class="stat-card">
        <div>
            <div class="stat-header">
                <div class="stat-info">
                    <span class="stat-label">Belum Tercapai</span>
                    <h4 class="stat-value" style="color: #ef4444;">{{ $unachievedCount }}</h4>
                </div>
                <div class="stat-icon belum-tercapai">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-footer">
            <span class="stat-desc">Perlu didorong pengisian</span>
            <span class="badge-custom badge-rose">Warning</span>
        </div>
    </div>
</div>

<!-- Balanced Scorecard (BSC) Averages -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 24px;">
    <!-- Perspektif Mahasiswa -->
    <div class="card" style="display: flex; align-items: center; gap: 20px; padding: 20px; position: relative; overflow: hidden; border-color: rgba(56, 189, 248, 0.15);">
        <div style="position: relative; width: 76px; height: 76px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: conic-gradient({{ $avgMahasiswa >= 100 ? '#10b981' : ($avgMahasiswa >= 60 ? '#f59e0b' : '#ef4444') }} {{ $avgMahasiswa * 3.6 }}deg, var(--bg-surface2) 0deg); flex-shrink: 0; box-shadow: inset 0 0 8px rgba(0,0,0,0.12);">
            <div style="content: ''; position: absolute; width: 60px; height: 60px; border-radius: 50%; background-color: var(--bg-surface);"></div>
            <span style="position: relative; font-size: 1.1rem; font-weight: 800; color: var(--text-primary);">{{ $avgMahasiswa }}%</span>
        </div>
        <div style="display: flex; flex-direction: column; gap: 4px;">
            <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary); margin: 0;">Perspektif Mahasiswa</h4>
            <span class="badge-custom {{ $avgMahasiswa >= 100 ? 'badge-green' : ($avgMahasiswa >= 60 ? 'badge-blue' : 'badge-rose') }}" style="align-self: flex-start; font-size: 0.6rem; margin-top: 2px;">
                {{ $avgMahasiswa >= 100 ? 'Sangat Baik' : ($avgMahasiswa >= 60 ? 'Cukup Baik' : 'Kurang/Risiko') }}
            </span>
        </div>
    </div>

    <!-- Perspektif Dosen -->
    <div class="card" style="display: flex; align-items: center; gap: 20px; padding: 20px; position: relative; overflow: hidden; border-color: rgba(168, 85, 247, 0.15);">
        <div style="position: relative; width: 76px; height: 76px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: conic-gradient({{ $avgDosen >= 100 ? '#10b981' : ($avgDosen >= 60 ? '#f59e0b' : '#ef4444') }} {{ $avgDosen * 3.6 }}deg, var(--bg-surface2) 0deg); flex-shrink: 0; box-shadow: inset 0 0 8px rgba(0,0,0,0.12);">
            <div style="content: ''; position: absolute; width: 60px; height: 60px; border-radius: 50%; background-color: var(--bg-surface);"></div>
            <span style="position: relative; font-size: 1.1rem; font-weight: 800; color: var(--text-primary);">{{ $avgDosen }}%</span>
        </div>
        <div style="display: flex; flex-direction: column; gap: 4px;">
            <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary); margin: 0;">Perspektif Dosen</h4>
            <span class="badge-custom {{ $avgDosen >= 100 ? 'badge-green' : ($avgDosen >= 60 ? 'badge-purple' : 'badge-rose') }}" style="align-self: flex-start; font-size: 0.6rem; margin-top: 2px;">
                {{ $avgDosen >= 100 ? 'Sangat Baik' : ($avgDosen >= 60 ? 'Cukup Baik' : 'Kurang/Risiko') }}
            </span>
        </div>
    </div>
</div>

    @if($recommendations && $recommendations->isNotEmpty())
        <div class="alert-box alert-warning" style="background: rgba(168, 85, 247, 0.1); border: 1px solid rgba(168, 85, 247, 0.3); color: var(--text-secondary); display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px;">
            <svg style="width: 18px; height: 18px; color: #c084fc; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21l8.982-11.795H13.62l1.378-6.059L6 15.004h3.813z"></path>
            </svg>
            <div style="font-size: 0.85rem; color: var(--text-secondary);">
                Terdeteksi <strong>{{ $recommendations->count() }}</strong> indikator dengan status warning. Klik tombol <strong>💡 Rekomendasi</strong> di kolom status tabel untuk melihat saran perbaikan AI.
            </div>
        </div>
    @else
        <div class="alert-box alert-success" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: var(--text-secondary); display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px;">
            <svg style="width: 18px; height: 18px; color: #10b981; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div style="font-size: 0.85rem; color: var(--text-secondary);">
                Tidak ada rekomendasi karena seluruh indikator dalam kondisi aman.
            </div>
        </div>
    @endif

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- IKU/IKT Capaian Table -->
    <div class="card" style="display: flex; flex-direction: column; gap: 18px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.1rem; font-weight: 700;">Monitoring Capaian IKU/IKT ({{ $tahunAktif }})</h3>
            <a href="{{ route('adminprodi.laporan.index') }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.75rem; border-radius: 8px;">Lihat Laporan Lengkap</a>
        </div>

        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Indikator Kinerja Utama</th>
                        <th style="text-align: center;">Target</th>
                        <th style="text-align: center;">Realisasi (Valid)</th>
                        <th style="text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pencapaians as $item)
                        <tr>
                            <td>
                                <div style="font-weight: 600; color: var(--text-primary);">{{ $item->iku->nama_iku }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">Kategori: {{ $item->iku->kategori->nama_kategori }}</div>
                            </td>
                            <td style="text-align: center; font-weight: 600; color: var(--text-primary);">
                                {{ $item->target }}{{ $item->satuan === 'persen' ? '%' : '' }} 
                                <span style="font-size: 0.7rem; color: var(--text-muted); display: block; font-weight: normal;">({{ $item->objek }})</span>
                            </td>
                            <td style="text-align: center; font-weight: 700; color: var(--text-primary);">
                                {{ round($item->realisasi) }} Bukti
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
                            <td colspan="4" style="text-align: center; color: #64748b; padding: 30px;">
                                Belum ada target IKU/IKT yang diatur untuk tahun akademik aktif ({{ $tahunAktif }}). Silakan tambahkan target di menu Target IKU/IKT Tahunan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Assignments -->
    <div class="card" style="display: flex; flex-direction: column; gap: 18px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.1rem; font-weight: 700;">Penugasan Dosen Terbaru</h3>
            <a href="{{ route('adminprodi.penugasan.index') }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.75rem; border-radius: 8px;">Kelola</a>
        </div>

        <div style="display: flex; flex-direction: column; gap: 12px;">
            @forelse($recentAssignments as $assign)
                <div style="padding: 12px; background-color: var(--bg-surface2); border: 1px solid var(--border); border-radius: 8px; display: flex; flex-direction: column; gap: 4px;">
                    <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-primary);">{{ $assign->user->name }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary);">IKU/IKT: {{ $assign->iku->nama_iku }}</div>
                    <div style="font-size: 0.7rem; color: var(--text-muted); align-self: flex-end; margin-top: 4px;">Tahun: {{ $assign->tahun }}</div>
                </div>
            @empty
                <div style="text-align: center; color: #64748b; padding: 20px; font-size: 0.85rem;">
                    Belum ada penugasan dosen untuk tahun {{ $tahunAktif }}.
                </div>
            @endforelse
        </div>
    </div>
</div>

@include('partials.ai_evaluation_modal')
@include('partials.ai_evaluation_script')

<script>
function initAdminProdiDashboardRecs() {
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
    document.addEventListener('DOMContentLoaded', initAdminProdiDashboardRecs);
} else {
    initAdminProdiDashboardRecs();
}
</script>
@endsection
