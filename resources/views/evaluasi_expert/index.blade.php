<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Form Evaluasi Rekomendasi AI - Expert Judgment</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('images/LOGO POLTEKKKKK.jpg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root, html[data-theme="light"] {
            --bg-base: #f8fafc;
            --bg-surface: #ffffff;
            --bg-surface2: #f1f5f9;
            --bg-surface3: #e2e8f0;
            --border: #cbd5e1;
            --border-hover: rgba(79, 70, 229, 0.5);
            --text-primary: #0f172a;
            --text-secondary: #1e293b;
            --text-muted: #475569;
            --input-bg: #ffffff;
            --input-border: #94a3b8;
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --success: #059669;
            --danger: #dc2626;
            --warning: #d97706;
        }

        html[data-theme="dark"] {
            --bg-base: #090d16;
            --bg-surface: #0f172a;
            --bg-surface2: #1e293b;
            --bg-surface3: #334155;
            --border: #1e293b;
            --border-hover: rgba(99, 102, 241, 0.4);
            --text-primary: #ffffff;
            --text-secondary: #f8fafc;
            --text-muted: #94a3b8;
            --input-bg: #1e293b;
            --input-border: #334155;
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }


        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-base);
            color: var(--text-secondary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 16px;
        }

        .form-container {
            width: 100%;
            max-width: 820px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Banner Header Card */
        .form-header-card {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 16px;
            padding: 28px 32px;
            color: white;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.3);
            position: relative;
            overflow: hidden;
        }

        .form-header-card::after {
            content: '';
            position: absolute;
            right: -30px;
            bottom: -30px;
            width: 160px;
            height: 160px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            pointer-events: none;
        }

        .form-header-title {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }

        .form-header-desc {
            font-size: 0.875rem;
            opacity: 0.9;
            line-height: 1.5;
        }

        /* Section Card */
        .form-card {
            background-color: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 18px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: border-color 0.2s;
        }

        .form-card:hover {
            border-color: var(--border-hover);
        }

        .form-card-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 12px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-label {
            font-size: 0.825rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .form-label span.req {
            color: var(--danger);
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            background-color: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.875rem;
            color: var(--text-primary);
            outline: none;
            transition: all 0.2s ease;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .form-select option {
            background-color: var(--bg-surface);
            color: var(--text-primary);
        }

        /* Recommendation Display Box */
        .rec-display-box {
            background-color: var(--bg-surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 18px;
            font-size: 0.875rem;
            line-height: 1.6;
            color: var(--text-secondary);
            max-height: 380px;
            overflow-y: auto;
        }

        /* Claim Item Card */
        .claim-card {
            background-color: var(--bg-surface2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            transition: all 0.2s;
        }

        .claim-card:hover {
            border-color: rgba(99, 102, 241, 0.3);
        }

        .claim-text-header {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .claim-badge {
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .claim-text {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.4;
        }

        .percentage-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .perc-input-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .perc-slider-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .perc-slider {
            flex: 1;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .perc-val-box {
            width: 70px;
            text-align: center;
            font-weight: 700;
            font-size: 0.875rem;
            padding: 6px;
        }

        .catatan-required-hint {
            font-size: 0.75rem;
            color: var(--danger);
            display: none;
        }

        .btn-submit {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 14px 28px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }

        .btn-submit:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Success Screen */
        #success-container {
            display: none;
            text-align: center;
            padding: 40px 20px;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        .success-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 640px) {
            .percentage-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <div class="form-container">

        <!-- Form Banner Header -->
        <div class="form-header-card">
            <h1 class="form-header-title">Form Evaluasi Rekomendasi AI</h1>
            <p class="form-header-desc">
                Metode Expert Judgment — Evaluasi tingkat kebenaran fakta dan potensi halusinasi pada klaim-klaim hasil analisis rekomendasi Gemini AI Poltek Sukabumi.
            </p>
        </div>

        <!-- Success Screen (Hidden by default) -->
        <div id="success-container" class="form-card">
            <div class="success-icon">
                <svg style="width: 36px; height: 36px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary);">Penilaian Berhasil Disimpan!</h2>
            <p style="font-size: 0.875rem; color: var(--text-muted); max-width: 460px;">
                Terima kasih atas partisipasi dan penilaian berharga Anda. Hasil evaluasi ini akan digunakan oleh Manajemen untuk meningkatkan kualitas rekomendasi AI.
            </p>
            <button type="button" onclick="window.location.reload()" class="btn-submit" style="width: auto; padding: 10px 24px; font-size: 0.85rem; margin-top: 10px;">
                Isi Penilaian Lagi
            </button>
        </div>

        <!-- Main Evaluation Form -->
        <form id="expert-eval-form" onsubmit="return false;">
            <div style="display: flex; flex-direction: column; gap: 20px;">

                <!-- 1. Identitas Penilai -->
                <div class="form-card">
                    <h3 class="form-card-title">
                        <svg style="width: 20px; height: 20px; color: #818cf8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Identitas Penilai
                    </h3>

                    <div class="form-group">
                        <label class="form-label" for="nama_penilai">Nama Lengkap Penilai <span class="req">*</span></label>
                        <input type="text" id="nama_penilai" class="form-input" placeholder="Contoh: Dr. Ir. Ahmad Subagja, M.T." required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="jabatan">Jabatan <span class="req">*</span></label>
                        <input type="text" id="jabatan" class="form-input" placeholder="Contoh: Dosen Teknik Komputer" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="prodi_unit">Program Studi / Unit Kerja <span class="req">*</span></label>
                        <input type="text" id="prodi_unit" class="form-input" placeholder="Contoh: Teknik Komputer / P2MP" required>
                    </div>
                </div>

                <!-- 2. Pilih Program Studi & Indikator Kinerja Utama (IKU) -->
                <div class="form-card">
                    <h3 class="form-card-title">
                        <svg style="width: 20px; height: 20px; color: #818cf8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v12"></path>
                        </svg>
                        Pilih Program Studi & Indikator Kinerja Utama (IKU)
                    </h3>

                    <!-- Dropdown Program Studi yang Memiliki Rekomendasi AI -->
                    <div class="form-group">
                        <label class="form-label" for="select-prodi">Pilih Program Studi yang Ingin Dinilai <span class="req">*</span></label>
                        <select id="select-prodi" class="form-select" required>
                            <option value="">-- Pilih Program Studi --</option>
                            @if(isset($prodiList) && count($prodiList) > 0)
                                @foreach($prodiList as $prodi)
                                    <option value="{{ $prodi->id }}" {{ (strcasecmp($prodi->nama_prodi, 'Teknik Komputer') === 0 || $loop->first) ? 'selected' : '' }}>
                                        {{ $prodi->nama_prodi }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Dropdown IKU (Filtered based on Selected Prodi) -->
                    <div class="form-group">
                        <label class="form-label" for="select-iku">Pilih IKU yang Ingin Dinilai <span class="req">*</span></label>
                        <select id="select-iku" class="form-select" required>
                            <option value="">-- Pilih Indikator Kinerja --</option>
                        </select>
                    </div>
                </div>

                <!-- 3. Data Faktual Capaian IKU/IKT (Muncul otomatis setelah IKU dipilih) -->
                <div id="iku-detail-card" class="form-card" style="display: none;">
                    <h3 class="form-card-title">
                        <svg style="width: 20px; height: 20px; color: #818cf8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Data Faktual Capaian IKU/IKT
                    </h3>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; background-color: var(--bg-surface2); padding: 14px 16px; border-radius: 10px; border: 1px solid var(--border);">
                        <div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; display: block;">Kode IKU</span>
                            <strong id="detail-kode-iku" style="font-size: 0.9rem; color: var(--primary);"> - </strong>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; display: block;">Nama IKU</span>
                            <strong id="detail-nama-iku" style="font-size: 0.9rem; color: var(--text-primary);"> - </strong>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; display: block;">Program Studi</span>
                            <strong id="detail-prodi-iku" style="font-size: 0.9rem; color: var(--text-primary);"> - </strong>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; display: block;">Tahun Akademik</span>
                            <strong id="detail-tahun-iku" style="font-size: 0.9rem; color: var(--text-primary);"> - </strong>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; display: block;">Target</span>
                            <strong id="detail-target-iku" style="font-size: 0.9rem; color: var(--text-primary);"> - </strong>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; display: block;">Realisasi</span>
                            <strong id="detail-realisasi-iku" style="font-size: 0.9rem; color: #3b82f6;"> - </strong>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; display: block;">Status Capaian</span>
                            <strong id="detail-status-iku" style="font-size: 0.9rem;"> - </strong>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 4px;">
                        <label class="form-label">Deskripsi IKU/IKT:</label>
                        <div id="detail-deskripsi-iku" class="rec-display-box" style="max-height: 160px; background-color: var(--bg-surface2); font-style: normal; line-height: 1.6;">
                            <!-- Populated via AJAX -->
                        </div>
                    </div>
                </div>

                <!-- 4. Hasil Rekomendasi AI -->
                <div id="rec-preview-card" class="form-card" style="display: none;">
                    <h3 class="form-card-title">
                        <svg style="width: 20px; height: 20px; color: #a855f7;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21l8.982-11.795H13.62l1.378-6.059L6 15.004h3.813z"></path>
                        </svg>
                        Rekomendasi AI
                    </h3>
                    <div id="rec-body-text" class="rec-display-box"></div>
                </div>

                <!-- 5. Penilaian Per Klaim Individual -->
                <div id="claims-card-section" class="form-card" style="display: none;">
                    <h3 class="form-card-title">
                        <svg style="width: 20px; height: 20px; color: #10b981;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Penilaian Faktual & Halusinasi Per Klaim
                    </h3>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: -10px;">
                        Beri nilai persentase <strong>Fakta (0–100%)</strong> dan <strong>Halusinasi (0–100%)</strong> untuk setiap klaim. Total persentase harus bernilai 100%. Jika persentase Halusinasi > 0%, wajib menuliskan catatan/koreksi.
                    </p>

                    <div id="claims-list-wrapper" style="display: flex; flex-direction: column; gap: 14px;">
                        <!-- Rendered by JS -->
                    </div>
                </div>

                <!-- Submit Button Area -->
                <div id="submit-section" style="display: none;">
                    <button type="button" id="btn-submit-eval" class="btn-submit">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Kirim Penilaian Expert
                    </button>
                </div>

            </div>
        </form>

    </div>

    <!-- Script Markdown Parser & Form Handler -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectProdi = document.getElementById('select-prodi');
        const selectIku = document.getElementById('select-iku');
        
        const ikuDetailCard = document.getElementById('iku-detail-card');
        const detailKodeIku = document.getElementById('detail-kode-iku');
        const detailNamaIku = document.getElementById('detail-nama-iku');
        const detailProdiIku = document.getElementById('detail-prodi-iku');
        const detailTahunIku = document.getElementById('detail-tahun-iku');
        const detailTargetIku = document.getElementById('detail-target-iku');
        const detailRealisasiIku = document.getElementById('detail-realisasi-iku');
        const detailStatusIku = document.getElementById('detail-status-iku');
        const detailDeskripsiIku = document.getElementById('detail-deskripsi-iku');

        const recPreviewCard = document.getElementById('rec-preview-card');
        const recBodyText = document.getElementById('rec-body-text');

        const claimsCardSection = document.getElementById('claims-card-section');
        const claimsListWrapper = document.getElementById('claims-list-wrapper');
        const submitSection = document.getElementById('submit-section');
        const btnSubmitEval = document.getElementById('btn-submit-eval');
        const expertForm = document.getElementById('expert-eval-form');
        const successContainer = document.getElementById('success-container');

        const allIkuOptions = @json($ikuOptions ?? []);
        let currentClaimsData = [];

        function parseSimpleMarkdown(text) {
            if (!text) return '';
            let html = text
                .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
            html = html.replace(/^### (.*$)/gim, '<h5 style="color: var(--text-primary); font-weight: 700; margin-top: 12px; margin-bottom: 6px;">$1</h5>');
            html = html.replace(/^## (.*$)/gim, '<h4 style="color: var(--text-primary); font-weight: 700; margin-top: 16px; margin-bottom: 8px;">$1</h4>');
            html = html.replace(/\*\*(.*?)\*\*/g, '<strong style="color: var(--text-primary); font-weight: 700;">$1</strong>');
            html = html.replace(/^\s*[-*+]\s+(.*)$/gim, '<li style="margin-left: 20px; margin-bottom: 4px;">$1</li>');
            return html.replace(/\n/g, '<br>');
        }

        function hideDownstreamCards() {
            if (ikuDetailCard) ikuDetailCard.style.display = 'none';
            if (recPreviewCard) recPreviewCard.style.display = 'none';
            if (claimsCardSection) claimsCardSection.style.display = 'none';
            if (submitSection) submitSection.style.display = 'none';
        }

        // Filter IKU dropdown based on selected prodi
        function filterIkuByProdi(prodiId) {
            selectIku.innerHTML = '<option value="">-- Pilih Indikator Kinerja --</option>';
            hideDownstreamCards();

            if (!prodiId) return;

            const filtered = allIkuOptions.filter(opt => opt.id_prodi == prodiId);
            filtered.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.id_rekomendasi;
                option.textContent = `[${opt.kode_iku}] ${opt.nama_iku} (Tahun ${opt.tahun})`;
                selectIku.appendChild(option);
            });
        }

        // Handle Prodi Selection Change
        selectProdi.addEventListener('change', function () {
            filterIkuByProdi(this.value);
        });

        // Handle IKU Selection Change
        selectIku.addEventListener('change', function () {
            const recId = this.value;
            if (!recId) {
                hideDownstreamCards();
                return;
            }

            // Reset loading state
            detailKodeIku.textContent = '...';
            detailNamaIku.textContent = '...';
            detailProdiIku.textContent = '...';
            detailTahunIku.textContent = '...';
            if (detailTargetIku) detailTargetIku.textContent = '...';
            if (detailRealisasiIku) detailRealisasiIku.textContent = '...';
            if (detailStatusIku) detailStatusIku.textContent = '...';
            detailDeskripsiIku.textContent = 'Memuat deskripsi IKU...';
            recBodyText.innerHTML = '<span style="color: var(--text-muted);">Memuat rekomendasi AI...</span>';

            ikuDetailCard.style.display = 'flex';
            recPreviewCard.style.display = 'flex';
            claimsCardSection.style.display = 'none';
            submitSection.style.display = 'none';

            fetch('/evaluasi-expert/rekomendasi/' + recId)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Populate Data Faktual Capaian IKU/IKT Card
                        detailKodeIku.textContent = data.kode_iku || '-';
                        detailNamaIku.textContent = data.nama_iku || '-';
                        detailProdiIku.textContent = data.prodi || '-';
                        detailTahunIku.textContent = data.tahun || '-';
                        if (detailTargetIku) detailTargetIku.textContent = data.target || '-';
                        if (detailRealisasiIku) detailRealisasiIku.textContent = data.realisasi || '-';
                        if (detailStatusIku) {
                            const st = data.status_capaian || '-';
                            let badgeColor = '#10b981';
                            if (st === 'Perlu Perhatian') badgeColor = '#f59e0b';
                            else if (st === 'Tidak Tercapai') badgeColor = '#ef4444';
                            detailStatusIku.innerHTML = `<span style="color: ${badgeColor}; font-weight: 700;">${st}</span>`;
                        }
                        detailDeskripsiIku.innerHTML = data.deskripsi_iku ? parseSimpleMarkdown(data.deskripsi_iku) : 'Tidak ada deskripsi indikator kinerja.';

                        // Populate Rekomendasi AI Card
                        recBodyText.innerHTML = parseSimpleMarkdown(data.rekomendasi_teks);

                        // Render Claims & Show Evaluation Section
                        currentClaimsData = data.claims || [];
                        renderClaimsForm(currentClaimsData);

                        claimsCardSection.style.display = 'flex';
                        submitSection.style.display = 'block';
                    } else {
                        alert(data.message || 'Gagal memuat rekomendasi.');
                        hideDownstreamCards();
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Koneksi gagal saat mengambil data rekomendasi.');
                    hideDownstreamCards();
                });
        });

        // Auto-filter IKU dropdown on initial load if Prodi is pre-selected (e.g., Teknik Komputer)
        if (selectProdi.value) {
            filterIkuByProdi(selectProdi.value);
        }

        function renderClaimsForm(claims) {
            claimsListWrapper.innerHTML = '';
            if (claims.length === 0) {
                claimsListWrapper.innerHTML = '<div style="color: var(--text-muted);">Tidak ada klaim yang dapat di-extract.</div>';
                return;
            }

            claims.forEach((claimText, idx) => {
                const card = document.createElement('div');
                card.className = 'claim-card';
                card.dataset.index = idx;
                card.dataset.klaim = claimText;

                card.innerHTML = `
                    <div class="claim-text-header">
                        <span class="claim-badge">Klaim #${idx + 1}</span>
                        <div class="claim-text">${claimText}</div>
                    </div>
                    <div class="percentage-grid">
                        <div class="perc-input-group">
                            <label class="form-label" style="color: var(--success);">Persentase Fakta (%)</label>
                            <div class="perc-slider-wrap">
                                <input type="range" class="perc-slider slider-fakta" min="0" max="100" value="100" data-idx="${idx}">
                                <input type="number" class="form-input perc-val-box val-fakta" min="0" max="100" value="100" data-idx="${idx}">
                            </div>
                        </div>
                        <div class="perc-input-group">
                            <label class="form-label" style="color: var(--danger);">Persentase Halusinasi (%)</label>
                            <div class="perc-slider-wrap">
                                <input type="range" class="perc-slider slider-halusinasi" min="0" max="100" value="0" data-idx="${idx}">
                                <input type="number" class="form-input perc-val-box val-halusinasi" min="0" max="100" value="0" data-idx="${idx}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 4px;">
                        <label class="form-label">Catatan / Koreksi Expert <span class="catatan-hint-${idx} catatan-required-hint">* Wajib diisi karena halusinasi > 0%</span></label>
                        <textarea class="form-textarea input-catatan" rows="2" placeholder="Tuliskan catatan atau fakta yang benar jika ada kesalahan/halusinasi..."></textarea>
                    </div>
                `;

                claimsListWrapper.appendChild(card);
            });

            // Synchronize sliders & number inputs
            claimsListWrapper.querySelectorAll('.claim-card').forEach(card => {
                const sFakta = card.querySelector('.slider-fakta');
                const vFakta = card.querySelector('.val-fakta');
                const sHalu = card.querySelector('.slider-halusinasi');
                const vHalu = card.querySelector('.val-halusinasi');
                const hint = card.querySelector('.catatan-required-hint');

                function updateFromFakta(val) {
                    val = Math.max(0, Math.min(100, parseInt(val) || 0));
                    const halu = 100 - val;
                    sFakta.value = val;
                    vFakta.value = val;
                    sHalu.value = halu;
                    vHalu.value = halu;

                    if (halu > 0) {
                        if (hint) hint.style.display = 'inline';
                    } else {
                        if (hint) hint.style.display = 'none';
                    }
                }

                function updateFromHalusinasi(val) {
                    val = Math.max(0, Math.min(100, parseInt(val) || 0));
                    const fakta = 100 - val;
                    sHalu.value = val;
                    vHalu.value = val;
                    sFakta.value = fakta;
                    vFakta.value = fakta;

                    if (val > 0) {
                        if (hint) hint.style.display = 'inline';
                    } else {
                        if (hint) hint.style.display = 'none';
                    }
                }

                sFakta.addEventListener('input', e => updateFromFakta(e.target.value));
                vFakta.addEventListener('input', e => updateFromFakta(e.target.value));
                sHalu.addEventListener('input', e => updateFromHalusinasi(e.target.value));
                vHalu.addEventListener('input', e => updateFromHalusinasi(e.target.value));
            });
        }

        btnSubmitEval.addEventListener('click', function () {
            const namaPenilai = document.getElementById('nama_penilai').value.trim();
            const jabatan = document.getElementById('jabatan').value.trim();
            const prodiUnit = document.getElementById('prodi_unit').value.trim();
            const idRekomendasi = selectIku.value;

            if (!namaPenilai || !jabatan || !prodiUnit || !idRekomendasi) {
                alert('Mohon lengkapi Nama Penilai, Jabatan, Prodi/Unit, dan Pilih IKU terlebih dahulu.');
                return;
            }

            const claimCards = claimsListWrapper.querySelectorAll('.claim-card');
            if (claimCards.length === 0) {
                alert('Tidak ada klaim untuk dinilai.');
                return;
            }

            const payloadClaims = [];
            let isValid = true;
            let errorMsg = '';

            claimCards.forEach((card, idx) => {
                const klaimText = card.dataset.klaim;
                const vFakta = parseFloat(card.querySelector('.val-fakta').value) || 0;
                const vHalu = parseFloat(card.querySelector('.val-halusinasi').value) || 0;
                const catatan = card.querySelector('.input-catatan').value.trim();

                if (Math.abs((vFakta + vHalu) - 100) > 0.01) {
                    isValid = false;
                    errorMsg = `Klaim #${idx + 1}: Total persentase Fakta dan Halusinasi harus 100%.`;
                }

                if (vHalu > 0 && !catatan) {
                    isValid = false;
                    errorMsg = `Klaim #${idx + 1}: Catatan/Koreksi wajib diisi karena persentase Halusinasi > 0%.`;
                }

                payloadClaims.push({
                    klaim: klaimText,
                    persentase_fakta: vFakta,
                    persentase_halusinasi: vHalu,
                    catatan: catatan
                });
            });

            if (!isValid) {
                alert(errorMsg);
                return;
            }

            btnSubmitEval.disabled = true;
            btnSubmitEval.innerHTML = 'Mengirim Penilaian...';

            fetch('/evaluasi-expert/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    id_rekomendasi: idRekomendasi,
                    nama_penilai: namaPenilai,
                    jabatan: jabatan,
                    prodi_unit: prodiUnit,
                    claims: payloadClaims
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    expertForm.style.display = 'none';
                    successContainer.style.display = 'flex';
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    alert(data.message || 'Gagal menyimpan penilaian.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Koneksi gagal saat mengirim data.');
            })
            .finally(() => {
                btnSubmitEval.disabled = false;
                btnSubmitEval.innerHTML = `
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg> Kirim Penilaian Expert
                `;
            });
        });
    });
    </script>
</body>
</html>
