<script>
function initAiEvalScript() {
    const modal = document.getElementById('custom-ai-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalSubtitle = document.getElementById('modal-subtitle');
    const modalBody = document.getElementById('modal-body-content');
    const btnCloseModal = document.getElementById('btn-close-modal');

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
        const statusHtml = metaData ? 
            'Status: <span style="font-weight: 600; color: ' + 
            (metaData.status === 'Perlu Perhatian' ? '#fbbf24' : '#ef4444') + ';">' + 
            metaData.status + '</span> (Realisasi: ' + Math.round(metaData.realisasi) + ' dari Target: ' + metaData.target + ')'
            : 'Detail Rekomendasi AI';

        if (modalTitle) modalTitle.textContent = 'Rekomendasi Analisis AI: ' + ikuName;
        if (modalSubtitle) modalSubtitle.innerHTML = statusHtml;
        if (modalBody) modalBody.innerHTML = parseMarkdown(text);

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
