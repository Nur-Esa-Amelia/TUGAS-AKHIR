<!-- Custom AI Recommendation Modal (Reading Mode) -->
<div id="custom-ai-modal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 20px; transition: all 0.3s ease;">
    <div style="background: var(--bg-surface); border: 1px solid var(--border); box-shadow: 0 0 30px rgba(168, 85, 247, 0.12); border-radius: 14px; width: 100%; max-width: 850px; max-height: 90vh; display: flex; flex-direction: column; animation: modalSlideIn 0.25s ease-out; overflow: hidden;">
        
        <!-- Modal Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding: 16px 20px; background: var(--bg-surface);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(168, 85, 247, 0.15); display: flex; align-items: center; justify-content: center; color: #a855f7;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21l8.982-11.795H13.62l1.378-6.059L6 15.004h3.813z"></path>
                    </svg>
                </div>
                <div>
                    <h3 id="modal-title" style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin: 0;">Detail Rekomendasi AI</h3>
                    <p id="modal-subtitle" style="font-size: 0.75rem; color: var(--text-muted); margin: 2px 0 0 0;">Analisis Risiko & Saran Perbaikan Indikator Kinerja</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <button id="btn-close-modal" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 6px; border-radius: 6px; transition: all 0.2s;" title="Tutup Modal">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body Content -->
        <div style="padding: 20px; overflow-y: auto; flex: 1; max-height: calc(88vh - 75px); display: flex; flex-direction: column; gap: 20px;">
            
            <!-- SECTION 1: Data Faktual Capaian IKU/IKT -->
            <div style="background: var(--bg-surface2); border: 1px solid var(--border); border-radius: 12px; padding: 18px; display: flex; flex-direction: column; gap: 14px;">
                <div style="display: flex; align-items: center; gap: 8px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
                    <svg style="width: 18px; height: 18px; color: #4f46e5;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--text-primary); margin: 0;">Data Faktual Capaian IKU/IKT</h4>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px;">
                    <div>
                        <span style="font-size: 0.72rem; color: var(--text-muted); font-weight: 600; display: block;">Nama IKU/IKT</span>
                        <strong id="modal-factual-nama" style="font-size: 0.85rem; color: var(--text-primary); line-height: 1.3; display: block;">-</strong>
                    </div>
                    <div>
                        <span style="font-size: 0.72rem; color: var(--text-muted); font-weight: 600; display: block;">Program Studi</span>
                        <strong id="modal-factual-prodi" style="font-size: 0.85rem; color: var(--text-primary); display: block;">-</strong>
                    </div>
                    <div>
                        <span style="font-size: 0.72rem; color: var(--text-muted); font-weight: 600; display: block;">Tahun Akademik</span>
                        <strong id="modal-factual-tahun" style="font-size: 0.85rem; color: var(--text-primary); display: block;">-</strong>
                    </div>
                    <div>
                        <span style="font-size: 0.72rem; color: var(--text-muted); font-weight: 600; display: block;">Target Sasaran</span>
                        <strong id="modal-factual-target" style="font-size: 0.85rem; color: var(--text-primary); display: block;">-</strong>
                    </div>
                    <div>
                        <span style="font-size: 0.72rem; color: var(--text-muted); font-weight: 600; display: block;">Realisasi</span>
                        <strong id="modal-factual-realisasi" style="font-size: 0.85rem; color: #3b82f6; display: block;">-</strong>
                    </div>
                    <div>
                        <span style="font-size: 0.72rem; color: var(--text-muted); font-weight: 600; display: block;">Status Capaian</span>
                        <div id="modal-factual-status" style="font-size: 0.85rem; display: block;">-</div>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 4px; border-top: 1px solid var(--border); padding-top: 10px;">
                    <span style="font-size: 0.72rem; color: var(--text-muted); font-weight: 600;">Deskripsi IKU/IKT:</span>
                    <div id="modal-factual-deskripsi" style="font-size: 0.825rem; color: var(--text-secondary); line-height: 1.5; background: var(--bg-surface); padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border);">
                        -
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Rekomendasi AI -->
            <div style="background: var(--bg-surface2); border: 1px solid var(--border); border-radius: 12px; padding: 18px; display: flex; flex-direction: column; gap: 14px;">
                <div style="display: flex; align-items: center; gap: 8px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
                    <svg style="width: 18px; height: 18px; color: #a855f7;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21l8.982-11.795H13.62l1.378-6.059L6 15.004h3.813z"></path>
                    </svg>
                    <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--text-primary); margin: 0;">Rekomendasi AI</h4>
                </div>

                <div id="modal-ai-recommendation" style="color: var(--text-secondary); font-size: 0.875rem; line-height: 1.6;">
                    <!-- Rendered markdown recommendation content goes here -->
                </div>
            </div>

        </div>

    </div>
</div>

<style>
@keyframes modalSlideIn {
    from {
        transform: scale(0.96) translateY(8px);
        opacity: 0;
    }
    to {
        transform: scale(1) translateY(0);
        opacity: 1;
    }
}
</style>
