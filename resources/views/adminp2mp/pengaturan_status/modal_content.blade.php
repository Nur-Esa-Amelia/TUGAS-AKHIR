<div style="display: flex; flex-direction: column; gap: 20px;">
    <form action="{{ route('adminp2mp.pengaturan-status.store') }}" method="POST" id="pengaturan-status-form-modal">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <!-- Threshold Tercapai -->
            <div class="form-group-custom" style="margin-bottom: 0;">
                <label for="threshold_tercapai_modal" class="form-label-custom" style="display: flex; align-items: center; gap: 6px;">
                    <span style="width: 10px; height: 10px; border-radius: 50%; background-color: #10b981; display: inline-block;"></span>
                    Batas Status Tercapai (%)
                </label>
                <div style="position: relative;">
                    <input type="number" 
                           step="0.01"
                           min="0"
                           max="100"
                           id="threshold_tercapai_modal" 
                           name="threshold_tercapai" 
                           value="{{ old('threshold_tercapai', number_format($thresholdTercapai, 2, '.', '')) }}" 
                           placeholder="100.00" 
                           class="form-input-custom" 
                           required
                           oninput="updateThresholdModalPreview()">
                    <span style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #64748b; font-weight: 600; font-size: 0.9rem;">%</span>
                </div>
                <small style="color: #64748b; font-size: 0.75rem; display: block; margin-top: 4px;">Minimal % realisasi untuk status Tercapai.</small>
            </div>

            <!-- Threshold Perlu Perhatian -->
            <div class="form-group-custom" style="margin-bottom: 0;">
                <label for="threshold_perlu_perhatian_modal" class="form-label-custom" style="display: flex; align-items: center; gap: 6px;">
                    <span style="width: 10px; height: 10px; border-radius: 50%; background-color: #f59e0b; display: inline-block;"></span>
                    Batas Status Perlu Perhatian (%)
                </label>
                <div style="position: relative;">
                    <input type="number" 
                           step="0.01"
                           min="0"
                           max="100"
                           id="threshold_perlu_perhatian_modal" 
                           name="threshold_perlu_perhatian" 
                           value="{{ old('threshold_perlu_perhatian', number_format($thresholdPerluPerhatian, 2, '.', '')) }}" 
                           placeholder="60.00" 
                           class="form-input-custom" 
                           required
                           oninput="updateThresholdModalPreview()">
                    <span style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #64748b; font-weight: 600; font-size: 0.9rem;">%</span>
                </div>
                <small style="color: #64748b; font-size: 0.75rem; display: block; margin-top: 4px;">Minimal % realisasi untuk status Perlu Perhatian.</small>
            </div>
        </div>

        <!-- Submit actions -->
        <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--border); padding-top: 16px;">
            <button type="button" class="btn btn-secondary" onclick="closeModal('pengaturan-status-modal')">Batal</button>
            <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
                Simpan & Update Status IKU
            </button>
        </div>
    </form>
</div>

<script>
    function updateThresholdModalPreview() {
        const tercapaiInput = document.getElementById('threshold_tercapai_modal');
        const perhatianInput = document.getElementById('threshold_perlu_perhatian_modal');

        if (!tercapaiInput || !perhatianInput) return;

        const tercapaiVal = parseFloat(tercapaiInput.value) || 0;
        const perhatianVal = parseFloat(perhatianInput.value) || 0;

        const pTercapai = document.getElementById('modal_preview_tercapai');
        const pPerhatianMin = document.getElementById('modal_preview_perhatian_min');
        const pPerhatianMax = document.getElementById('modal_preview_perhatian_max');
        const pTidakTercapai = document.getElementById('modal_preview_tidak_tercapai');

        if (pTercapai) pTercapai.textContent = tercapaiVal.toFixed(2) + '%';
        if (pPerhatianMin) pPerhatianMin.textContent = perhatianVal.toFixed(2) + '%';
        if (pPerhatianMax) pPerhatianMax.textContent = tercapaiVal.toFixed(2) + '%';
        if (pTidakTercapai) pTidakTercapai.textContent = perhatianVal.toFixed(2) + '%';
    }
</script>
