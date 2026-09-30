@extends('dosen.layouts.app')

@section('title', 'Dashboard Dosen - Sistem Early Warning IKU/IKT')
@section('page_title', 'Selamat Datang di Sistem Monitoring Pencapaian Indikator Kinerja Politeknik Sukabumi')
@section('page_subtitle', '')

@section('content')
<!-- Check configuration warning -->
@if(!$settings)
<div class="alert-box alert-danger" style="margin-bottom: 20px;">
    <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
    </svg>
    <div>
        <strong>Perhatian!</strong> Admin Program Studi Anda belum mengatur konfigurasi system tahun akademik. Harap hubungi Admin Prodi Anda untuk mengatur parameter profil IKU/IKT program studi.
    </div>
</div>
@endif

<!-- Welcome Announcement Card -->
<div class="card welcome-card">
    <div class="welcome-text">
        <span class="welcome-badge">Dosen {{ $prodiName }}</span>
        <h3 class="welcome-title">Halo, {{ auth()->user()->name }}!</h3>
        <p class="welcome-desc">
            Selamat datang di panel pengisian bukti kinerja. Di sini Anda dapat memantau indikator kinerja utama (IKU/IKT) yang telah ditugaskan oleh program studi Anda, mengunggah berkas-berkas bukti penunjang, serta memantau status validasi berkas oleh P2MP.
        </p>
    </div>
    <div style="display: flex; flex-direction: column; gap: 12px; flex-shrink: 0; min-width: 220px;">
        <!-- System Time Card -->
        <div class="system-time-card" style="margin: 0; width: 100%;">
            <div class="time-icon time-icon-1">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="time-text">
                <span class="time-label">Tahun Akademik Berjalan</span>
                <span class="time-value time-value-1">{{ $tahunAktif }}</span>
            </div>
        </div>

        <!-- Achievement Progress Card -->
        @if($totalAssignments > 0)
            <div class="system-time-card" style="margin: 0; width: 100%;">
                <div class="time-icon time-icon-1">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="time-text" style="flex: 1;">
                    <span class="time-label">Rata-Rata Capaian Tugas Anda</span>
                    <span class="time-value" style="display: flex; align-items: center; justify-content: space-between; gap: 8px; color: #ffffff;">
                        <span style="color: #ffffff;">{{ $achievementPercentage }}%</span>
                    </span>
                    <!-- Progress Bar -->
                    <div style="width: 100%; height: 4px; background-color: var(--bg-surface3); border-radius: 9999px; margin-top: 6px; overflow: hidden;">
                        <div style="width: {{ min(100, $achievementPercentage) }}%; height: 100%; background-color: #10b981; border-radius: 9999px;"></div>
                    </div>
                </div>
            </div>
        @else
            <div class="system-time-card" style="margin: 0; width: 100%;">
                <div class="time-icon time-icon-2">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="time-text">
                    <span class="time-label">Capaian Tugas Anda</span>
                    <span class="time-value time-value-2" style="font-size: 0.75rem;">Belum ada tugas ditugaskan</span>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Metrics Cards Section -->
<div class="dashboard-grid">
    <!-- Total Penugasan IKU/IKT -->
    <div class="stat-card">
        <div>
            <div class="stat-header">
                <div class="stat-info">
                    <span class="stat-label">IKU/IKT Ditugaskan</span>
                    <h4 class="stat-value">{{ $totalAssignments }}</h4>
                </div>
                <div class="stat-icon assign">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-footer">
            <span class="stat-desc">Wajib diisi bukti tahun {{ $tahunAktif }}</span>
        </div>
    </div>

    <!-- Total Bukti Diunggah -->
    <div class="stat-card">
        <div>
            <div class="stat-header">
                <div class="stat-info">
                    <span class="stat-label">Total Bukti Diunggah</span>
                    <h4 class="stat-value">{{ $totalProofs }}</h4>
                </div>
                <div class="stat-icon upload">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-footer">
            <span class="stat-desc">Semua pengunggahan tahun ini</span>
            <a href="{{ route('dosen.pengisian.index') }}" class="stat-link">
                Riwayat
                <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>

    <!-- Bukti Valid (P2MP) -->
    <div class="stat-card">
        <div>
            <div class="stat-header">
                <div class="stat-info">
                    <span class="stat-label">Bukti Valid</span>
                    <h4 class="stat-value" style="color: #10b981;">{{ $validProofs }}</h4>
                </div>
                <div class="stat-icon valid">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-footer">
            <span class="stat-desc">Divalidasi oleh P2MP</span>
        </div>
    </div>

    <!-- Pending Validation -->
    <div class="stat-card">
        <div>
            <div class="stat-header">
                <div class="stat-info">
                    <span class="stat-label">Menunggu Validasi</span>
                    <h4 class="stat-value" style="color: #f59e0b;">{{ $pendingProofs }}</h4>
                </div>
                <div class="stat-icon pending">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-footer">
            <span class="stat-desc">Menunggu tinjauan P2MP</span>
        </div>
    </div>
</div>

<!-- Assigned IKUs Table list -->
<div class="card" style="display: flex; flex-direction: column; gap: 18px;">
    <h3 style="font-size: 1.1rem; font-weight: 700;">Daftar Tugas Pengisian IKU/IKT Anda (Tahun {{ $tahunAktif }})</h3>

    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama Indikator IKU/IKT</th>
                    <th>Kategori</th>
                    <th style="text-align: center;">Target Nyata</th>
                    <th style="text-align: center;">Realisasi (Valid)</th>
                    <th style="text-align: center;">Persentase</th>
                    <th style="width: 180px; text-align: center;">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="font-weight: 600; color: var(--text-primary);">{{ $item->iku->nama_iku }}</td>
                        <td>
                            <span class="badge-custom badge-purple">{{ $item->iku->kategori->nama_kategori }}</span>
                        </td>
                        <td style="text-align: center; color: var(--text-secondary); font-size: 0.85rem;">
                            {{ round($item->target_nyata) }} {{ $item->satuan === 'persen' ? $item->objek : ($item->satuan ?: 'Bukti') }}
                        </td>
                        <td style="text-align: center; color: var(--text-secondary); font-weight: 600;">
                            {{ $item->realisasi }} Bukti
                        </td>
                        <td style="text-align: center; font-weight: 700; color: #10b981;">
                            {{ number_format($item->persentase, 2) }}%
                        </td>
                        <td style="text-align: center;">
                            @if(!empty($item->target_tercapai) && $item->target_tercapai)
                                <span class="badge-custom badge-green" style="font-size: 0.7rem; padding: 6px 10px;">Tercapai</span>
                            @else
                                <button type="button" class="btn btn-primary btn-open-upload-modal" data-iku-id="{{ $item->id_iku }}" style="padding: 6px 12px; font-size: 0.75rem; border-radius: 6px;">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    Unggah Bukti
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 40px;">
                            Anda tidak memiliki penugasan pengisian IKU/IKT untuk tahun akademik aktif ({{ $tahunAktif }}). Silakan hubungi Admin Prodi Anda jika ini adalah kesalahan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
