<script>
function initAiEvalScript() {
    let currentPencapaianId = null;
    let currentEvaluationData = null;

    const modal = document.getElementById('custom-ai-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalSubtitle = document.getElementById('modal-subtitle');
    const modalBody = document.getElementById('modal-body-content');
    const btnCloseModal = document.getElementById('btn-close-modal');
    const btnToggleEvalMode = document.getElementById('btn-toggle-eval-mode');
    const btnEvalText = document.getElementById('btn-eval-text');

    const modalModeReading = document.getElementById('modal-mode-reading');
    const modalModeEval = document.getElementById('modal-mode-eval');
    const evalLoading = document.getElementById('eval-loading');
    const evalContainer = document.getElementById('eval-container');

    const btnBackToReading = document.getElementById('btn-back-to-reading');
    const btnSaveEvaluation = document.getElementById('btn-save-evaluation');
    const btnAddFn = document.getElementById('btn-add-fn');

    // Helper Markdown Parser
    function parseMarkdown(text) {
        if (!text) return '';
        let html = text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");
            
        html = html.replace(/^### (.*$)/gim, '<h5 style="color: var(--text-primary); font-weight: 700; margin-top: 14px; margin-bottom: 6px; font-size: 0.9rem;">$1</h5>');
        html = html.replace(/^## (.*$)/gim, '<h4 style="color: var(--text-primary); font-weight: 700; margin-top: 18px; margin-bottom: 8px; font-size: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 4px;">$1</h4>');
        html = html.replace(/^# (.*$)/gim, '<h3 style="color: var(--text-primary); font-weight: 800; margin-top: 22px; margin-bottom: 10px; font-size: 1.15rem;">$1</h3>');
        
        html = html.replace(/\*\*(.*?)\*\*/g, '<strong style="color: var(--text-primary); font-weight: 600;">$1</strong>');
        html = html.replace(/^\s*[-*+]\s+(.*)$/gim, '<li style="margin-left: 20px; margin-bottom: 6px; list-style-type: disc; padding-left: 4px;">$1</li>');
        
        const lines = html.split('\n');
        let processedLines = [];
        let inList = false;

        for (let i = 0; i < lines.length; i++) {
            let line = lines[i].trim();
            if (line.startsWith('<li')) {
                if (!inList) {
                    processedLines.push('<ul style="margin-bottom: 12px; display: flex; flex-direction: column; gap: 4px;">');
                    inList = true;
                }
                processedLines.push(line);
            } else {
                if (inList) {
                    processedLines.push('</ul>');
                    inList = false;
                }
                if (line === '') {
                    // skip
                } else if (line.startsWith('<h')) {
                    processedLines.push(line);
                } else {
                    processedLines.push(`<p style="margin-bottom: 12px; text-align: justify; color: var(--text-secondary);">${line}</p>`);
                }
            }
        }
        if (inList) {
            processedLines.push('</ul>');
        }

        return processedLines.join('\n');
    }

    // Global Modal Trigger Function
    window.openAiModal = function (text, pencapaianId, metaData) {
        currentPencapaianId = pencapaianId;
        currentEvaluationData = null;

        const ikuName = (metaData && metaData.nama_iku) ? metaData.nama_iku : 'Indikator Kinerja';
        const statusHtml = metaData ? 
            'Status: <span style="font-weight: 600; color: ' + 
            (metaData.status === 'Perlu Perhatian' ? '#fbbf24' : '#ef4444') + ';">' + 
            metaData.status + '</span> (Realisasi: ' + Math.round(metaData.realisasi) + ' dari Target: ' + metaData.target + ')'
            : 'Detail Rekomendasi AI';

        if (modalTitle) modalTitle.textContent = 'Rekomendasi Analisis AI: ' + ikuName;
        if (modalSubtitle) modalSubtitle.innerHTML = statusHtml;
        if (modalBody) modalBody.innerHTML = parseMarkdown(text);

        // Reset Modes
        if (modalModeReading) modalModeReading.style.display = 'block';
        if (modalModeEval) modalModeEval.style.display = 'none';
        if (btnEvalText) btnEvalText.textContent = 'Penilaian AI';
        if (btnToggleEvalMode) {
            btnToggleEvalMode.style.background = 'rgba(59, 130, 246, 0.15)';
            btnToggleEvalMode.style.color = '#3b82f6';
            btnToggleEvalMode.style.borderColor = 'rgba(59, 130, 246, 0.3)';
        }

        // Hide evaluation reading banner until fetched
        const evalBannerReading = document.getElementById('eval-banner-reading');
        if (evalBannerReading) evalBannerReading.style.display = 'none';

        if (modal) modal.style.display = 'flex';

        // Silently fetch existing evaluation info to populate banner
        fetch('/rekomendasi/penilaian/' + pencapaianId)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    currentEvaluationData = data;
                    if (data.metrics && evalBannerReading) {
                        const m = data.metrics;
                        const bannerText = document.getElementById('eval-banner-text');
                        if (bannerText) {
                            bannerText.innerHTML = 
                                `✓ <strong>Rekomendasi ini telah diuji.</strong> F1-Score: <strong>${m.f1_score}%</strong> | Precision: <strong>${m.precision}%</strong> | Recall: <strong>${m.recall}%</strong> (Hallucination Rate: <strong>${data.system_metrics.hallucination_rate}%</strong>)`;
                        }
                        evalBannerReading.style.display = 'flex';
                    }
                }
            })
            .catch(err => console.log('Silent eval fetch error:', err));
    };

    // Toggle Evaluation Mode vs Reading Mode
    function toggleEvalMode() {
        if (modalModeEval && modalModeEval.style.display === 'none') {
            switchToEvaluationMode();
        } else {
            switchToReadingMode();
        }
    }

    function switchToReadingMode() {
        if (modalModeReading) modalModeReading.style.display = 'block';
        if (modalModeEval) modalModeEval.style.display = 'none';
        if (btnEvalText) btnEvalText.textContent = 'Penilaian AI';
        if (btnToggleEvalMode) {
            btnToggleEvalMode.style.background = 'rgba(59, 130, 246, 0.15)';
            btnToggleEvalMode.style.color = '#3b82f6';
            btnToggleEvalMode.style.borderColor = 'rgba(59, 130, 246, 0.3)';
        }
    }

    window.switchToEvaluationMode = function () {
        if (modalModeReading) modalModeReading.style.display = 'none';
        if (modalModeEval) modalModeEval.style.display = 'block';
        if (btnEvalText) btnEvalText.textContent = 'Lihat Rekomendasi';
        if (btnToggleEvalMode) {
            btnToggleEvalMode.style.background = 'rgba(168, 85, 247, 0.15)';
            btnToggleEvalMode.style.color = '#c084fc';
            btnToggleEvalMode.style.borderColor = 'rgba(168, 85, 247, 0.3)';
        }

        if (evalLoading) evalLoading.style.display = 'flex';
        if (evalContainer) evalContainer.style.display = 'none';

        if (currentEvaluationData) {
            renderEvaluationForm(currentEvaluationData);
        } else {
            fetch('/rekomendasi/penilaian/' + currentPencapaianId)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        currentEvaluationData = data;
                        renderEvaluationForm(data);
                    } else {
                        alert('Gagal memuat data pengujian AI: ' + data.message);
                        switchToReadingMode();
                    }
                })
                .catch(err => {
                    alert('Koneksi gagal saat mengambil data pengujian.');
                    switchToReadingMode();
                });
        }
    };

    // Render Form Pengujian AI
    function renderEvaluationForm(data) {
        // 1. Data Acuan Pembanding Body
        const acuan = data.data_acuan;
        let acuanHtml = `
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 8px; margin-bottom: 8px;">
                <div><strong>Nama IKU:</strong> ${acuan.nama_iku}</div>
                <div><strong>Program Studi:</strong> ${acuan.prodi} (${acuan.tahun})</div>
                <div><strong>Target Sasaran:</strong> ${acuan.target}</div>
                <div><strong>Realisasi (Valid):</strong> ${acuan.realisasi}</div>
            </div>
            <div style="border-top: 1px solid var(--border); padding-top: 8px; margin-top: 4px;">
                <strong style="color: var(--text-primary); display: block; margin-bottom: 4px;">Syarat Jenis Bukti Wajib (${acuan.bukti_wajib_count} Jenis):</strong>
        `;

        if (acuan.sudah_diunggah && acuan.sudah_diunggah.length > 0) {
            acuanHtml += `<div style="margin-bottom: 6px;"><span style="color: #10b981; font-weight: 600;">✓ Jenis Bukti yang Memiliki Unggahan:</span><ul style="margin: 4px 0 0 16px; padding: 0;">`;
            acuan.sudah_diunggah.forEach(b => {
                let detStr = b.details.map(d => `Status: ${d.status} (${d.jumlah_berkas} file)`).join('; ');
                acuanHtml += `<li><strong>${b.nama_bukti}</strong> [${detStr}]</li>`;
            });
            acuanHtml += `</ul></div>`;
        }

        if (acuan.belum_diunggah && acuan.belum_diunggah.length > 0) {
            acuanHtml += `<div><span style="color: #ef4444; font-weight: 600;">✕ Jenis Bukti yang BELUM Diunggah:</span><ul style="margin: 4px 0 0 16px; padding: 0;">`;
            acuan.belum_diunggah.forEach(b => {
                acuanHtml += `<li><strong>${b.nama_bukti}</strong> (${b.deskripsi})</li>`;
            });
            acuanHtml += `</ul></div>`;
        }

        acuanHtml += `</div>`;
        const acuanBody = document.getElementById('eval-data-acuan-body');
        if (acuanBody) acuanBody.innerHTML = acuanHtml;

        // 2. Claims List
        const claimsContainer = document.getElementById('claims-list-container');
        if (claimsContainer) {
            claimsContainer.innerHTML = '';

            data.claims.forEach((claim, idx) => {
                const card = document.createElement('div');
                card.className = 'claim-card-item';
                card.dataset.nomor = claim.nomor;
                card.dataset.teks = claim.teks;

                const isFaktual = (claim.status === 'faktual');
                const isHalusinasi = (claim.status === 'halusinasi');

                card.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                        <div style="display: flex; gap: 8px; align-items: flex-start;">
                            <span class="badge-custom badge-purple" style="font-size: 0.7rem; flex-shrink: 0; margin-top: 2px;">${claim.nomor}</span>
                            <p style="font-size: 0.85rem; color: var(--text-primary); margin: 0; line-height: 1.4;">${claim.teks}</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 8px; justify-content: flex-end; margin-top: 4px;">
                        <div class="claim-option-btn ${isFaktual ? 'selected-faktual' : ''}" data-val="faktual">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Faktual (TP)
                        </div>
                        <div class="claim-option-btn ${isHalusinasi ? 'selected-halusinasi' : ''}" data-val="halusinasi">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Halusinasi (FP)
                        </div>
                    </div>
                `;

                // Attach Option Toggle Click Handler
                const optBtns = card.querySelectorAll('.claim-option-btn');
                optBtns.forEach(btn => {
                    btn.addEventListener('click', function () {
                        optBtns.forEach(b => {
                            b.classList.remove('selected-faktual', 'selected-halusinasi');
                        });
                        const val = this.getAttribute('data-val');
                        if (val === 'faktual') {
                            this.classList.add('selected-faktual');
                        } else {
                            this.classList.add('selected-halusinasi');
                        }
                        card.dataset.status = val;
                        recalculateMetrics();
                    });
                });

                card.dataset.status = claim.status || 'faktual';
                claimsContainer.appendChild(card);
            });
        }

        // 3. FN List
        const fnContainer = document.getElementById('fn-list-container');
        if (fnContainer) {
            fnContainer.innerHTML = '';

            const fnItems = data.saved_fn && data.saved_fn.length > 0 ? data.saved_fn : [];
            if (fnItems.length === 0) {
                renderEmptyFnRow();
            } else {
                fnItems.forEach(text => renderFnRow(text));
            }
        }

        // 4. Initial Metrics Update
        updateMetricsUI(data.metrics, data.system_metrics);

        if (evalLoading) evalLoading.style.display = 'none';
        if (evalContainer) evalContainer.style.display = 'flex';

        recalculateMetrics();
    }

    function renderEmptyFnRow() {
        const fnContainer = document.getElementById('fn-list-container');
        if (fnContainer && fnContainer.children.length === 0) {
            renderFnRow('');
        }
    }

    function renderFnRow(textValue) {
        const fnContainer = document.getElementById('fn-list-container');
        if (!fnContainer) return;
        
        const row = document.createElement('div');
        row.className = 'fn-row-item';
        row.style.display = 'flex';
        row.style.gap = '8px';
        row.style.alignItems = 'center';

        row.innerHTML = `
            <input type="text" class="form-input-custom fn-input" value="${textValue.replace(/"/g, '&quot;')}" placeholder="Ketik fakta penting dari sistem yang terlewat oleh AI..." style="padding: 8px 12px; font-size: 0.8rem;">
            <button type="button" class="btn btn-rose btn-remove-fn" style="padding: 6px 10px; flex-shrink: 0;" title="Hapus">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        `;

        const input = row.querySelector('.fn-input');
        if (input) input.addEventListener('input', recalculateMetrics);

        const btnRemove = row.querySelector('.btn-remove-fn');
        if (btnRemove) {
            btnRemove.addEventListener('click', function () {
                row.remove();
                renderEmptyFnRow();
                recalculateMetrics();
            });
        }

        fnContainer.appendChild(row);
    }

    if (btnAddFn) {
        btnAddFn.addEventListener('click', function () {
            renderFnRow('');
        });
    }

    // Live Metrics Calculation
    function recalculateMetrics() {
        const claimCards = document.querySelectorAll('#claims-list-container .claim-card-item');
        let tp = 0;
        let fp = 0;

        claimCards.forEach(card => {
            const status = card.dataset.status || 'faktual';
            if (status === 'faktual') {
                tp++;
            } else if (status === 'halusinasi') {
                fp++;
            }
        });

        const fnInputs = document.querySelectorAll('#fn-list-container .fn-input');
        let fn = 0;
        fnInputs.forEach(input => {
            if (input.value.trim() !== '') {
                fn++;
            }
        });

        const totalClaims = claimCards.length;

        const precision = (tp + fp) > 0 ? (tp / (tp + fp)) * 100 : 0;
        const recall = (tp + fn) > 0 ? (tp / (tp + fn)) * 100 : 0;
        const f1 = (precision + recall) > 0 ? (2 * (precision * recall) / (precision + recall)) : 0;
        const hasHallucination = (fp > 0);

        // Update DOM
        const elTotalClaims = document.getElementById('metric-total-claims');
        if (elTotalClaims) elTotalClaims.textContent = totalClaims;
        const elTp = document.getElementById('metric-tp');
        if (elTp) elTp.textContent = tp;
        const elFp = document.getElementById('metric-fp');
        if (elFp) elFp.textContent = fp;
        const elFn = document.getElementById('metric-fn');
        if (elFn) elFn.textContent = fn;

        const totalClaimsAi = tp + fp;
        const liveHr = totalClaimsAi > 0 ? (fp / totalClaimsAi) * 100 : 0;

        const elPrec = document.getElementById('metric-precision');
        if (elPrec) elPrec.textContent = precision.toFixed(1) + '%';
        const elRecall = document.getElementById('metric-recall');
        if (elRecall) elRecall.textContent = recall.toFixed(1) + '%';
        const elF1 = document.getElementById('metric-f1');
        if (elF1) elF1.textContent = f1.toFixed(1) + '%';
        const elHr = document.getElementById('metric-hr');
        if (elHr) elHr.textContent = (Math.round(liveHr * 100) / 100) + '%';

        const statusBadge = document.getElementById('eval-status-badge');
        if (statusBadge) {
            if (hasHallucination) {
                statusBadge.className = 'badge-custom badge-rose';
                statusBadge.textContent = 'Terdeteksi Halusinasi';
            } else {
                statusBadge.className = 'badge-custom badge-green';
                statusBadge.textContent = '✓ Faktual & Akurat';
            }
        }
    }

    function updateMetricsUI(metrics, systemMetrics) {
        if (metrics) {
            const elTotalClaims = document.getElementById('metric-total-claims');
            if (elTotalClaims) elTotalClaims.textContent = metrics.total_klaim;
            const elTp = document.getElementById('metric-tp');
            if (elTp) elTp.textContent = metrics.tp;
            const elFp = document.getElementById('metric-fp');
            if (elFp) elFp.textContent = metrics.fp;
            const elFn = document.getElementById('metric-fn');
            if (elFn) elFn.textContent = metrics.fn;
            const elPrec = document.getElementById('metric-precision');
            if (elPrec) elPrec.textContent = metrics.precision + '%';
            const elRecall = document.getElementById('metric-recall');
            if (elRecall) elRecall.textContent = metrics.recall + '%';
            const elF1 = document.getElementById('metric-f1');
            if (elF1) elF1.textContent = metrics.f1_score + '%';
        }
        if (systemMetrics) {
            const elHr = document.getElementById('metric-hr');
            if (elHr) elHr.textContent = systemMetrics.hallucination_rate + '%';
        }
    }

    // Save Evaluation via AJAX
    function saveEvaluation() {
        const claimCards = document.querySelectorAll('#claims-list-container .claim-card-item');
        const claims = [];

        claimCards.forEach(card => {
            claims.push({
                nomor: card.dataset.nomor,
                teks: card.dataset.teks,
                status: card.dataset.status || 'faktual'
            });
        });

        const fnInputs = document.querySelectorAll('#fn-list-container .fn-input');
        const fnList = [];
        fnInputs.forEach(input => {
            const val = input.value.trim();
            if (val !== '') fnList.push(val);
        });

        const originalText = btnSaveEvaluation ? btnSaveEvaluation.innerHTML : '';
        if (btnSaveEvaluation) {
            btnSaveEvaluation.innerHTML = 'Menyimpan...';
            btnSaveEvaluation.disabled = true;
        }

        fetch('/rekomendasi/penilaian/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
            },
            body: JSON.stringify({
                pencapaian_id: currentPencapaianId,
                claims: claims,
                fn_list: fnList
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                if (currentEvaluationData) {
                    currentEvaluationData.metrics = data.metrics;
                    currentEvaluationData.system_metrics = data.system_metrics;
                }
                updateMetricsUI(data.metrics, data.system_metrics);
                
                // Show floating toast
                showToastNotification('Berhasil!', data.message, 'success');

                // Update Reading Banner
                const evalBannerReading = document.getElementById('eval-banner-reading');
                if (evalBannerReading) {
                    const m = data.metrics;
                    const bannerText = document.getElementById('eval-banner-text');
                    if (bannerText) {
                        bannerText.innerHTML = 
                            `✓ <strong>Rekomendasi ini telah diuji.</strong> F1-Score: <strong>${m.f1_score}%</strong> | Precision: <strong>${m.precision}%</strong> | Recall: <strong>${m.recall}%</strong> (Hallucination Rate: <strong>${data.system_metrics.hallucination_rate}%</strong>)`;
                    }
                    evalBannerReading.style.display = 'flex';
                }
            } else {
                alert('Gagal menyimpan penilaian: ' + data.message);
            }
        })
        .catch(err => {
            alert('Koneksi gagal saat menyimpan penilaian.');
        })
        .finally(() => {
            if (btnSaveEvaluation) {
                btnSaveEvaluation.innerHTML = originalText;
                btnSaveEvaluation.disabled = false;
            }
        });
    }

    // Floating Toast Helper
    function showToastNotification(title, message, type) {
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) return;

        const isSuccess = (type === 'success');
        const borderColor = isSuccess ? '#10b981' : '#ef4444';
        const iconBg = isSuccess ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)';
        const iconColor = isSuccess ? '#10b981' : '#ef4444';

        const toast = document.createElement('div');
        toast.className = `toast-popup toast-${type}`;
        toast.style.cssText = `pointer-events: auto; background: var(--bg-surface); border-left: 4px solid ${borderColor}; border-top: 1px solid var(--border); border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15); border-radius: 10px; padding: 14px 16px; display: flex; align-items: flex-start; gap: 12px; animation: toastSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1); transition: all 0.3s ease;`;

        toast.innerHTML = `
            <div style="width: 24px; height: 24px; border-radius: 50%; background: ${iconBg}; color: ${iconColor}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px;">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="${isSuccess ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'}"></path>
                </svg>
            </div>
            <div style="flex: 1;">
                <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary); margin: 0 0 2px 0;">${title}</h4>
                <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0; line-height: 1.4;">${message}</p>
            </div>
            <button type="button" onclick="this.closest('.toast-popup').remove()" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; padding: 2px; border-radius: 4px; display: flex; align-items: center; justify-content: center;" title="Tutup">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;

        toastContainer.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // Attach Event Listeners
    if (btnToggleEvalMode) btnToggleEvalMode.addEventListener('click', toggleEvalMode);
    if (btnBackToReading) btnBackToReading.addEventListener('click', switchToReadingMode);
    if (btnSaveEvaluation) btnSaveEvaluation.addEventListener('click', saveEvaluation);

    if (btnCloseModal) {
        btnCloseModal.addEventListener('click', function () {
            if (modal) modal.style.display = 'none';
        });
    }

    window.addEventListener('click', function (e) {
        if (modal && e.target === modal) {
            modal.style.display = 'none';
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAiEvalScript);
} else {
    initAiEvalScript();
}
</script>
