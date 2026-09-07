<!-- Custom AI Recommendation Modal (Reading Mode) -->
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
                <button id="btn-close-modal" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 6px; border-radius: 6px; transition: all 0.2s;" title="Tutup Modal">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body Content -->
        <div style="padding: 20px; overflow-y: auto; flex: 1; max-height: calc(88vh - 75px);">
            <div id="modal-body-content" style="color: var(--text-secondary); font-size: 0.875rem; line-height: 1.6;">
                <!-- Rendered markdown recommendation content goes here -->
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
