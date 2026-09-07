<!-- Custom AI Recommendation & Evaluation Modal -->
<div id="custom-ai-modal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 20px; transition: all 0.3s ease;">
    <div style="background: var(--bg-surface); border: 1px solid var(--border); box-shadow: 0 0 30px rgba(168, 85, 247, 0.12); border-radius: 12px; width: 100%; max-width: 850px; max-height: 90vh; display: flex; flex-direction: column; animation: modalSlideIn 0.25s ease-out; overflow: hidden;">
        
        <!-- Modal Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding: 16px 20px; background: var(--bg-surface);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(168, 85, 247, 0.15); display: flex; align-items: center; justify-content: center; color: #a855f7;">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21l8.982-11.795H13.62l1.378-6.059L6 15.004h3.813z"></path>
                    </svg>
                </div>
                <div>
                    <h3 id="modal-title" style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin: 0;">Rekomendasi Analisis AI</h3>
                    <p id="modal-subtitle" style="font-size: 0.75rem; color: var(--text-muted); margin: 2px 0 0 0;"></p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <button id="btn-toggle-eval-mode" type="button" class="btn" style="padding: 6px 12px; font-size: 0.75rem; font-weight: 600; border-radius: 8px; background: rgba(59, 130, 246, 0.15); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.3); cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span id="btn-eval-text">Penilaian AI</span>
                </button>
                <button id="btn-close-modal" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 6px; border-radius: 6px; transition: all 0.2s;" title="Tutup Modal">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body Content -->
        <div style="padding: 20px; overflow-y: auto; flex: 1; max-height: calc(88vh - 75px);">
            
            <!-- MODE 1: Reading Mode (Rekomendasi AI) -->
            <div id="modal-mode-reading">
                <div id="modal-body-content" style="color: var(--text-secondary); font-size: 0.875rem; line-height: 1.6;">
                    <!-- Rendered markdown recommendation goes here -->
                </div>
                <div id="eval-banner-reading" style="display: none; margin-top: 20px; padding: 12px 16px; border-radius: 10px; background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.2); font-size: 0.8rem; color: #60a5fa; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <svg style="width: 16px; height: 16px; color: #3b82f6;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span id="eval-banner-text">Rekomendasi ini telah diuji oleh penilai.</span>
                    </div>
                    <button type="button" onclick="switchToEvaluationMode()" style="background: transparent; border: none; color: #3b82f6; font-weight: 600; cursor: pointer; text-decoration: underline; font-size: 0.8rem; padding: 0;">
                        Lihat Detail Pengujian ➔
                    </button>
                </div>
            </div>

            <!-- MODE 2: Evaluation Mode (Penilaian AI & Metrik) -->
            <div id="modal-mode-eval" style="display: none;">
                
                <!-- Loading State -->
                <div id="eval-loading" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 0; gap: 12px;">
                    <svg style="width: 32px; height: 32px; color: #3b82f6; animation: spin 1s linear infinite;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Memuat data pengujian AI...</span>
                </div>

                <!-- Main Evaluation Form Container -->
                <div id="eval-container" style="display: none; flex-direction: column; gap: 20px;">
                    
                    <!-- 1. Metrics Overview Grid -->
                    <div style="background: var(--bg-surface2); border: 1px solid var(--border); border-radius: 10px; padding: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                            <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                                <svg style="width: 16px; height: 16px; color: #3b82f6;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Hasil Pengujian & Metrik AI
                            </h4>
                            <span id="eval-status-badge" class="badge-custom badge-purple" style="font-size: 0.7rem;">Belum Diuji</span>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(105px, 1fr)); gap: 10px;">
                            <div style="background: var(--bg-surface); padding: 10px; border-radius: 8px; border: 1px solid var(--border); text-align: center;">
                                <span style="font-size: 0.68rem; color: var(--text-muted); display: block;">Total Klaim</span>
                                <strong id="metric-total-claims" style="font-size: 1.1rem; color: var(--text-primary);">0</strong>
                            </div>
                            <div style="background: rgba(16, 185, 129, 0.08); padding: 10px; border-radius: 8px; border: 1px solid rgba(16, 185, 129, 0.2); text-align: center;">
                                <span style="font-size: 0.68rem; color: #10b981; display: block;">TP (Faktual)</span>
                                <strong id="metric-tp" style="font-size: 1.1rem; color: #10b981;">0</strong>
                            </div>
                            <div style="background: rgba(239, 68, 68, 0.08); padding: 10px; border-radius: 8px; border: 1px solid rgba(239, 68, 68, 0.2); text-align: center;">
                                <span style="font-size: 0.68rem; color: #ef4444; display: block;">FP (Halusinasi)</span>
                                <strong id="metric-fp" style="font-size: 1.1rem; color: #ef4444;">0</strong>
                            </div>
                            <div style="background: rgba(245, 158, 11, 0.08); padding: 10px; border-radius: 8px; border: 1px solid rgba(245, 158, 11, 0.2); text-align: center;">
                                <span style="font-size: 0.68rem; color: #f59e0b; display: block;">FN (Terlewat)</span>
                                <strong id="metric-fn" style="font-size: 1.1rem; color: #f59e0b;">0</strong>
                            </div>
                            <div style="background: var(--bg-surface); padding: 10px; border-radius: 8px; border: 1px solid var(--border); text-align: center;">
                                <span style="font-size: 0.68rem; color: var(--text-muted); display: block;">Precision</span>
                                <strong id="metric-precision" style="font-size: 1.1rem; color: #3b82f6;">0%</strong>
                            </div>
                            <div style="background: var(--bg-surface); padding: 10px; border-radius: 8px; border: 1px solid var(--border); text-align: center;">
                                <span style="font-size: 0.68rem; color: var(--text-muted); display: block;">Recall</span>
                                <strong id="metric-recall" style="font-size: 1.1rem; color: #8b5cf6;">0%</strong>
                            </div>
                            <div style="background: var(--bg-surface); padding: 10px; border-radius: 8px; border: 1px solid var(--border); text-align: center;">
                                <span style="font-size: 0.68rem; color: var(--text-muted); display: block;">F1-Score</span>
                                <strong id="metric-f1" style="font-size: 1.1rem; color: #ec4899;">0%</strong>
                            </div>
                            <div style="background: var(--bg-surface); padding: 10px; border-radius: 8px; border: 1px solid var(--border); text-align: center;" title="Persentase klaim halusinasi dari seluruh klaim AI yang dievaluasi (FP / Total Klaim AI)">
                                <span style="font-size: 0.65rem; color: var(--text-muted); display: block;">Hallucination Rate</span>
                                <strong id="metric-hr" style="font-size: 1.1rem; color: #f43f5e;">0%</strong>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Data Acuan Sistem (Pembanding) Accordion -->
                    <details style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 10px; overflow: hidden;" open>
                        <summary style="padding: 12px 16px; font-size: 0.85rem; font-weight: 700; color: var(--text-primary); cursor: pointer; display: flex; align-items: center; justify-content: space-between; background: var(--bg-surface2);">
                            <span style="display: flex; align-items: center; gap: 8px;">
                                <svg style="width: 16px; height: 16px; color: #8b5cf6;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                </svg>
                                Data Acuan Pembanding (Database Sistem)
                            </span>
                            <span style="font-size: 0.72rem; color: var(--text-muted); font-weight: normal;">Klik untuk buka/tutup</span>
                        </summary>
                        <div id="eval-data-acuan-body" style="padding: 14px 16px; font-size: 0.8rem; color: var(--text-secondary); display: flex; flex-direction: column; gap: 10px;">
                            <!-- Filled dynamically by JavaScript -->
                        </div>
                    </details>

                    <!-- 3. Daftar Klaim Rekomendasi AI -->
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                            <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                                <svg style="width: 16px; height: 16px; color: #10b981;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Penilaian Faktual / Halusinasi Klaim AI
                            </h4>
                            <span style="font-size: 0.72rem; color: var(--text-muted);">Pilih status Faktual (TP) atau Halusinasi (FP) untuk setiap klaim</span>
                        </div>
                        <div id="claims-list-container" style="display: flex; flex-direction: column; gap: 10px;">
                            <!-- Rendered claims list dynamically by JavaScript -->
                        </div>
                    </div>

                    <!-- 4. Fakta Terlewat (FN) -->
                    <div style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 10px; padding: 14px 16px; display: flex; flex-direction: column; gap: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                            <div>
                                <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                                    <svg style="width: 16px; height: 16px; color: #f59e0b;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    Fakta Penting yang Terlewat (False Negative / FN)
                                </h4>
                                <p style="font-size: 0.75rem; color: var(--text-muted); margin: 2px 0 0 0;">
                                    Informasi/fakta penting yang tersedia pada data sistem tetapi tidak disebutkan sama sekali oleh AI.
                                </p>
                            </div>
                            <button type="button" id="btn-add-fn" class="btn" style="padding: 4px 10px; font-size: 0.72rem; background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3); border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Tambah Fakta Terlewat
                            </button>
                        </div>
                        <div id="fn-list-container" style="display: flex; flex-direction: column; gap: 8px;">
                            <!-- Rendered FN list dynamically by JavaScript -->
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 10px; border-top: 1px solid var(--border); margin-top: 6px; flex-wrap: wrap; gap: 10px;">
                        <button type="button" id="btn-back-to-reading" class="btn btn-secondary" style="padding: 8px 16px; font-size: 0.8rem; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px;">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali ke Rekomendasi
                        </button>
                        <button type="button" id="btn-save-evaluation" class="btn btn-primary" style="padding: 8px 20px; font-size: 0.8rem; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px;">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            Simpan Penilaian AI
                        </button>
                    </div>
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
.claim-card-item {
    background: var(--bg-surface2);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 12px 14px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    transition: border-color 0.2s;
}
.claim-card-item:hover {
    border-color: rgba(168, 85, 247, 0.3);
}
.claim-option-btn {
    cursor: pointer;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid var(--border);
    background: var(--bg-surface);
    color: var(--text-muted);
    transition: all 0.2s;
    user-select: none;
}
.claim-option-btn.selected-faktual {
    background: rgba(16, 185, 129, 0.15) !important;
    border-color: rgba(16, 185, 129, 0.4) !important;
    color: #10b981 !important;
}
.claim-option-btn.selected-halusinasi {
    background: rgba(239, 68, 68, 0.15) !important;
    border-color: rgba(239, 68, 68, 0.4) !important;
    color: #ef4444 !important;
}
</style>
