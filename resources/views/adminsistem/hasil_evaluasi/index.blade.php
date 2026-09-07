@extends(auth()->user() && auth()->user()->role === 'admin_p2mp' ? 'adminp2mp.layouts.app' : 'adminsistem.layouts.app')

@section('title', 'Hasil Evaluasi AI - Expert Judgment')
@section('page_title', 'Hasil Evaluasi Rekomendasi AI (Expert Judgment)')
@section('page_subtitle', 'Analisis Tingkat Fakta & Halusinasi Berdasarkan Evaluasi Pakar/Expert')

@section('content')
<style>
    .eval-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .eval-stat-card {
        background-color: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .eval-stat-card:hover {
        border-color: rgba(99, 102, 241, 0.4);
        transform: translateY(-2px);
    }

    .eval-stat-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--text-faint);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .eval-stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.1;
    }

    .eval-filter-card {
        background-color: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 18px 20px;
        margin-bottom: 24px;
    }

    .eval-tab-nav {
        display: flex;
        gap: 10px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 20px;
    }

    .eval-tab-btn {
        padding: 10px 18px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
    }

    .eval-tab-btn.active {
        color: #3b82f6;
        border-bottom-color: #3b82f6;
    }

    .claim-item-row {
        background-color: var(--bg-surface2);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 12px;
    }
</style>

<!-- 1. Ringkasan Metrics Overview -->
<div class="eval-summary-grid">
    <!-- Total Expert -->
    <div class="eval-stat-card">
        <span class="eval-stat-label">Total Expert Penilai</span>
        <div class="eval-stat-value" style="color: #6366f1;">{{ $totalExpertsCount }} Pakar</div>
        <span style="font-size: 0.72rem; color: var(--text-muted);">Individu Penilai</span>
    </div>

    <!-- Total IKU Dinilai -->
    <div class="eval-stat-card">
        <span class="eval-stat-label">Jumlah IKU Dinilai</span>
        <div class="eval-stat-value" style="color: #38bdf8;">{{ $totalIkuCount }} IKU</div>
        <span style="font-size: 0.72rem; color: var(--text-muted);">Terdaftar di sistem</span>
    </div>

    <!-- Total Rekomendasi -->
    <div class="eval-stat-card">
        <span class="eval-stat-label">Total Rekomendasi</span>
        <div class="eval-stat-value" style="color: #a855f7;">{{ $totalRekomendasiCount }} Rekomendasi</div>
        <span style="font-size: 0.72rem; color: var(--text-muted);">Telah Dievaluasi</span>
    </div>

    <!-- Rata-rata Fakta Keseluruhan -->
    <div class="eval-stat-card" style="background: rgba(16, 185, 129, 0.08); border-color: rgba(16, 185, 129, 0.25);">
        <span class="eval-stat-label" style="color: #10b981;">Rata-rata Fakta (%)</span>
        <div class="eval-stat-value" style="color: #10b981;">{{ $overallFakta }}%</div>
        <span style="font-size: 0.72rem; color: #34d399;">Tahap 3 Keseluruhan Expert</span>
    </div>

    <!-- Rata-rata Halusinasi Keseluruhan -->
    <div class="eval-stat-card" style="background: rgba(239, 68, 68, 0.08); border-color: rgba(239, 68, 68, 0.25);">
        <span class="eval-stat-label" style="color: #ef4444;">Rata-rata Halusinasi (%)</span>
        <div class="eval-stat-value" style="color: #ef4444;">{{ $overallHalusinasi }}%</div>
        <span style="font-size: 0.72rem; color: #f87171;">Tahap 3 Keseluruhan Expert</span>
    </div>
</div>

<!-- 2. Panel Filter -->
<div class="eval-filter-card">
    <form method="GET" action="{{ url()->current() }}">
        <div class="filter-row-custom">
            <!-- Filter Tahun -->
            <div class="filter-item-custom">
                <label class="form-label-custom">Tahun</label>
                <select name="tahun" class="form-select-custom">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $filterTahun == $t ? 'selected' : '' }}>Tahun {{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter IKU -->
            <div class="filter-item-custom">
                <label class="form-label-custom">Indikator Kinerja (IKU)</label>
                <select name="iku_id" class="form-select-custom">
                    <option value="">Semua IKU</option>
                    @foreach($ikuList as $iku)
                        <option value="{{ $iku->id }}" {{ $filterIku == $iku->id ? 'selected' : '' }}>
                            [{{ $iku->kode_iku ?: 'IKU' }}] {{ $iku->nama_iku }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Prodi -->
            <div class="filter-item-custom">
                <label class="form-label-custom">Program Studi</label>
                <select name="prodi_id" class="form-select-custom">
                    <option value="">Semua Prodi</option>
                    @foreach($prodiList as $p)
                        <option value="{{ $p->id }}" {{ $filterProdi == $p->id ? 'selected' : '' }}>{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Action Buttons -->
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="height: 42px; padding: 0 16px;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filter
                </button>
                <a href="{{ url()->current() }}" class="btn btn-secondary" style="height: 42px; padding: 0 16px;">
                    Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- 3. Navigation Tabs Section -->
<div class="eval-tab-nav">
    <button type="button" class="eval-tab-btn active" onclick="switchEvalTab('tab-iku', this)">
        Hasil Per IKU
    </button>
    <button type="button" class="eval-tab-btn" onclick="switchEvalTab('tab-expert', this)">
        Hasil Per Expert
    </button>
    <button type="button" class="eval-tab-btn" onclick="switchEvalTab('tab-detail-klaim', this)">
        Detail Penilaian Klaim
    </button>
</div>

<!-- TAB 1: HASIL PER IKU -->
<div id="tab-iku" class="eval-tab-content card">
    <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 16px;">Ringkasan Hasil Evaluasi Per IKU</h3>
    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Kode & Nama IKU</th>
                    <th>Program Studi / Unit</th>
                    <th style="text-align: center;">Tahun</th>
                    <th style="text-align: center;">Jumlah Expert</th>
                    <th style="text-align: center;">Rata-rata Fakta (%)</th>
                    <th style="text-align: center;">Rata-rata Halusinasi (%)</th>
                    <th style="text-align: center;">Status evaluasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($processedIkus as $iku)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--text-primary);">[{{ $iku['kode_iku'] }}] {{ $iku['nama_iku'] }}</div>
                        </td>
                        <td>{{ $iku['prodi'] }}</td>
                        <td style="text-align: center;">{{ $iku['tahun'] }}</td>
                        <td style="text-align: center; font-weight: 600;">{{ $iku['jumlah_expert'] }} Penilai</td>
                        <td style="text-align: center; font-weight: 800; color: #10b981;">
                            {{ $iku['fakta'] }}%
                        </td>
                        <td style="text-align: center; font-weight: 800; color: #ef4444;">
                            {{ $iku['halusinasi'] }}%
                        </td>
                        <td style="text-align: center;">
                            @if($iku['fakta'] >= 85)
                                <span class="badge-custom badge-green">✓ Akurat & Faktual</span>
                            @elseif($iku['fakta'] >= 60)
                                <span class="badge-custom badge-blue">Cukup Baik</span>
                            @else
                                <span class="badge-custom badge-rose">⚠ Potensi Halusinasi Tinggi</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            Belum ada data penilaian expert yang tersimpan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TAB 2: HASIL PER EXPERT -->
<div id="tab-expert" class="eval-tab-content card" style="display: none;">
    <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 16px;">Ringkasan Nilai Per Expert Penilai (Tahap 2)</h3>
    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Nama Penilai / Expert</th>
                    <th>Jabatan</th>
                    <th>Prodi / Unit</th>
                    <th style="text-align: center;">Jumlah IKU Dinilai</th>
                    <th style="text-align: center;">Fakta Expert (%)</th>
                    <th style="text-align: center;">Halusinasi Expert (%)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($processedExperts as $exp)
                    <tr>
                        <td style="font-weight: 700; color: var(--text-primary);">
                            {{ $exp['nama_penilai'] }}
                        </td>
                        <td>{{ $exp['jabatan'] }}</td>
                        <td>{{ $exp['prodi_unit'] }}</td>
                        <td style="text-align: center; font-weight: 600;">{{ $exp['jumlah_iku'] }} IKU</td>
                        <td style="text-align: center; font-weight: 800; color: #10b981;">
                            {{ $exp['fakta'] }}%
                        </td>
                        <td style="text-align: center; font-weight: 800; color: #ef4444;">
                            {{ $exp['halusinasi'] }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            Belum ada penilai yang melakukan evaluasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TAB 3: DETAIL PENILAIAN KLAIM -->
<div id="tab-detail-klaim" class="eval-tab-content card" style="display: none;">
    <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 16px;">Detail Penilaian Per Klaim oleh Seluruh Expert</h3>

    @forelse($claimDetails as $recId => $recItem)
        <div style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; flex-wrap: wrap; gap: 8px;">
                <div>
                    <span class="badge-custom badge-purple" style="font-size: 0.7rem; margin-bottom: 4px; display: inline-block;">
                        [{{ $recItem['kode_iku'] }}] {{ $recItem['nama_iku'] }}
                    </span>
                    <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin: 0;">
                        Prodi: {{ $recItem['prodi'] }} (Tahun {{ $recItem['tahun'] }})
                    </h4>
                </div>
            </div>

            <!-- List Klaim -->
            <div style="display: flex; flex-direction: column; gap: 14px;">
                @foreach($recItem['claims_map'] as $text => $cObj)
                    <div class="claim-item-row">
                        <div style="font-size: 0.875rem; font-weight: 700; color: var(--text-primary); margin-bottom: 10px;">
                            Klaim: "{{ $cObj['teks'] }}"
                        </div>

                        <!-- Expert Ratings for this Claim -->
                        <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 12px;">
                            @foreach($cObj['ratings'] as $rIdx => $r)
                                <div style="font-size: 0.8rem; background: var(--bg-surface); border: 1px solid var(--border); border-radius: 8px; padding: 10px 12px; display: flex; flex-direction: column; gap: 4px;">
                                    <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 6px;">
                                        <strong>Expert {{ $rIdx + 1 }}: {{ $r['nama_penilai'] }} ({{ $r['jabatan'] }})</strong>
                                        <div>
                                            <span style="color: #10b981; font-weight: 700;">Fakta: {{ $r['persentase_fakta'] }}%</span> | 
                                            <span style="color: #ef4444; font-weight: 700;">Halusinasi: {{ $r['persentase_halusinasi'] }}%</span>
                                        </div>
                                    </div>
                                    @if($r['catatan'])
                                        <div style="color: var(--text-muted); font-size: 0.78rem; border-top: 1px solid var(--border); padding-top: 4px; margin-top: 2px;">
                                            <em>Catatan: "{{ $r['catatan'] }}"</em>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Average score for this Claim -->
                        <div style="font-size: 0.8rem; font-weight: 700; background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.2); padding: 8px 12px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #818cf8;">Rata-rata Klaim Ini:</span>
                            <div>
                                <span style="color: #10b981;">Fakta {{ $cObj['avg_fakta'] }}%</span> | 
                                <span style="color: #ef4444;">Halusinasi {{ $cObj['avg_halusinasi'] }}%</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div style="text-align: center; color: var(--text-muted); padding: 30px;">
            Belum ada detail penilaian klaim yang tersedia.
        </div>
    @endforelse
</div>

<script>
function switchEvalTab(tabId, btnEl) {
    document.querySelectorAll('.eval-tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.eval-tab-btn').forEach(btn => btn.classList.remove('active'));

    const targetTab = document.getElementById(tabId);
    if (targetTab) targetTab.style.display = 'block';
    if (btnEl) btnEl.classList.add('active');
}
</script>
@endsection
