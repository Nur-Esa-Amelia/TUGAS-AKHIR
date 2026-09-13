<script>
function initAiEvalScript() {
    const modal = document.getElementById('custom-ai-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalSubtitle = document.getElementById('modal-subtitle');
    const btnCloseModal = document.getElementById('btn-close-modal');

    // Helper to strip legacy factual header text from recommendation text if present
    function stripFactualHeaderJS(text) {
        if (!text) return '';
        return text.replace(/^(?:###\s+[^\n]*\n+)?(?:-\s+\*\*(?:Nama IKU\/IKT|Program Studi|Tahun Akademik|Target|Realisasi|Status)\*\*:[^\n]*\n+)+/gi, '').trim();
    }

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
        const ikuName = (metaData && metaData.nama_iku) ? metaData.nama_iku : 'Indikator Kinerja';

        if (modalTitle) modalTitle.textContent = 'Detail Rekomendasi AI: ' + ikuName;
        if (modalSubtitle) modalSubtitle.textContent = 'Analisis Risiko & Saran Perbaikan Capaian IKU/IKT';

        // Populate Section 1: Data Faktual Capaian IKU/IKT
        if (metaData) {
            const elNama = document.getElementById('modal-factual-nama');
            const elProdi = document.getElementById('modal-factual-prodi');
            const elTahun = document.getElementById('modal-factual-tahun');
            const elTarget = document.getElementById('modal-factual-target');
            const elRealisasi = document.getElementById('modal-factual-realisasi');
            const elStatus = document.getElementById('modal-factual-status');
            const elDeskripsi = document.getElementById('modal-factual-deskripsi');

            if (elNama) elNama.textContent = metaData.nama_iku || '-';
            if (elProdi) elProdi.textContent = metaData.prodi || '-';
            if (elTahun) elTahun.textContent = metaData.tahun || '-';
            if (elTarget) elTarget.textContent = metaData.target_formatted || (metaData.target != null ? metaData.target : '-');
            if (elRealisasi) elRealisasi.textContent = metaData.realisasi_formatted || (metaData.realisasi != null ? (Math.round(metaData.realisasi) + ' Bukti') : '-');
            if (elStatus) {
                const st = metaData.status || '-';
                let color = '#10b981';
                if (st === 'Perlu Perhatian') color = '#fbbf24';
                else if (st === 'Tidak Tercapai') color = '#ef4444';
                elStatus.innerHTML = `<span style="font-weight: 700; color: ${color};">${st}</span>`;
            }
            if (elDeskripsi) elDeskripsi.textContent = metaData.deskripsi_iku || 'Tidak ada deskripsi indikator kinerja.';
        }

        // Populate Section 2: Rekomendasi AI (murni AI recommendation text)
        const cleanText = stripFactualHeaderJS(text);
        const elAiRec = document.getElementById('modal-ai-recommendation');
        if (elAiRec) elAiRec.innerHTML = parseMarkdown(cleanText);

        if (modal) modal.style.display = 'flex';
    };

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
