@extends('adminprodi.layouts.app')

@section('title', 'Unggah Bukti IKU/IKT - Kaprodi')
@section('page_title', 'Unggah Bukti IKU/IKT')
@section('page_subtitle', 'Kirim berkas bukti pemenuhan Indikator Kinerja Utama Program Studi Anda')

@section('content')
<div style="position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-surface); border: 1px solid var(--border); box-shadow: 0 0 30px rgba(37, 99, 235, 0.15); border-radius: 12px; width: 100%; max-width: 650px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; animation: modalSlideIn 0.25s ease-out;">
        <!-- Modal Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding: 16px 20px; background: var(--bg-surface);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(37, 99, 235, 0.15); display: flex; align-items: center; justify-content: center; color: #3b82f6;">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin: 0;">Form Unggah Bukti Kinerja - Tahun {{ $tahunAktif }}</h3>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin: 2px 0 0 0;">Kirim berkas bukti pemenuhan IKU/IKT Program Studi</p>
                </div>
            </div>
            <a href="{{ route('adminprodi.bukti-dosen') }}" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 6px; border-radius: 6px; text-decoration: none;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>

        <!-- Modal Body -->
        <form action="{{ route('adminprodi.pengisian.store') }}" method="POST" enctype="multipart/form-data" style="padding: 20px; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 16px;">
            @csrf

            <!-- IKU/IKT Select -->
            <div class="form-group-custom">
                <label for="id_iku" class="form-label-custom">Pilih Indikator IKU/IKT Program Studi</label>
                <select id="id_iku" name="id_iku" class="form-select-custom @error('id_iku') is-invalid @enderror" required>
                    <option value="">-- Pilih Indikator --</option>
                    @foreach($ikus as $item)
                        <option value="{{ $item->id }}" {{ old('id_iku', request('id_iku')) == $item->id ? 'selected' : '' }}>
                            {{ $item->nama_iku }}
                        </option>
                    @endforeach
                </select>
                @error('id_iku')
                    <span class="form-error-custom">{{ $message }}</span>
                @enderror
            </div>

            <!-- Jenis Bukti Select -->
            <div class="form-group-custom">
                <label for="id_bukti_iku" class="form-label-custom">Pilih Jenis Bukti Dokumen</label>
                <select id="id_bukti_iku" name="id_bukti_iku" class="form-select-custom @error('id_bukti_iku') is-invalid @enderror" required>
                    <option value="">-- Pilih Jenis Bukti --</option>
                    @foreach($buktiIku as $bukti)
                        <option value="{{ $bukti->id }}" data-iku-id="{{ $bukti->id_iku }}" {{ old('id_bukti_iku') == $bukti->id ? 'selected' : '' }}>
                            {{ $bukti->nama_bukti }}
                        </option>
                    @endforeach
                </select>
                <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 2px;">Pilihan jenis bukti disesuaikan secara otomatis berdasarkan IKU/IKT yang dipilih di atas.</small>
                @error('id_bukti_iku')
                    <span class="form-error-custom">{{ $message }}</span>
                @enderror
            </div>

            <!-- Berkas Bukti (Dynamic Cards) -->
            <div class="form-group-custom">
                <label class="form-label-custom">Unggah Berkas Bukti</label>
                <div id="file-inputs-container" style="display: flex; flex-direction: column; gap: 12px;">
                    <div class="file-input-card" style="background-color: var(--bg-surface2); border: 1px solid var(--border); border-radius: 10px; padding: 14px; display: flex; flex-direction: column; gap: 10px; position: relative;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="file-number-label" style="font-size: 0.78rem; font-weight: 700; color: #10b981;">Berkas Bukti #1</span>
                        </div>
                        
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <label class="form-label-custom" style="font-size: 0.72rem; color: var(--text-muted); font-weight: 600;">Pilih Berkas</label>
                            <input type="file" name="files[]" class="form-input-custom file-selector-input" required>
                        </div>
                        
                        <div class="keterangan-file-group" style="display: none; flex-direction: column; gap: 4px;">
                            <label class="form-label-custom" style="font-size: 0.72rem; color: var(--text-muted); font-weight: 600;">Keterangan Berkas (Opsional)</label>
                            <textarea name="keterangan_files[]" rows="2" placeholder="Tulis keterangan spesifik untuk berkas ini (contoh: Link Google Drive, Judul Dokumen, dll)..." class="form-input-custom file-keterangan-input"></textarea>
                        </div>
                    </div>
                </div>
                
                <button type="button" id="add-file-btn" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.75rem; align-self: flex-start; margin-top: 8px; display: none;">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    Tambah File Lainnya
                </button>
                
                <small style="color: var(--text-muted); font-size: 0.72rem; display: block; margin-top: 8px;">Format berkas: <strong>PDF, JPG, JPEG, PNG, ZIP, DOC, DOCX</strong> (Maks 10 MB per file).</small>
                @error('files')
                    <span class="form-error-custom">{{ $message }}</span>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; justify-content: flex-end; gap: 10px; padding-top: 14px; border-top: 1px solid var(--border); margin-top: 8px;">
                <a href="{{ route('adminprodi.bukti-dosen') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Unggah Berkas Bukti</button>
            </div>
        </form>
    </div>
</div>

<!-- Dynamic Select & File Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ikuSelect = document.getElementById('id_iku');
        const buktiSelect = document.getElementById('id_bukti_iku');
        
        // Menyimpan semua pilihan bukti yang tersedia dari server
        const originalBuktiOptions = Array.from(buktiSelect.querySelectorAll('option')).filter(opt => opt.value !== '');
        
         // Memperbarui pilihan bukti berdasarkan IKU yang dipilih
        function updateBuktiOptions() {
            const selectedIkuId = ikuSelect.value;
            
            // Mengosongkan pilihan bukti sebelumnya
            buktiSelect.innerHTML = '<option value="">-- Pilih Jenis Bukti --</option>';
            
            if (selectedIkuId) {
                // Filter matching options
                const filtered = originalBuktiOptions.filter(opt => opt.getAttribute('data-iku-id') === selectedIkuId);
                
                if (filtered.length > 0) {
                    filtered.forEach(opt => buktiSelect.appendChild(opt.cloneNode(true)));
                } else {
                    const noOpt = document.createElement('option');
                    noOpt.value = "";
                    noOpt.disabled = true;
                    noOpt.textContent = "Belum ada jenis bukti yang dikonfigurasi untuk IKU/IKT ini";
                    buktiSelect.appendChild(noOpt);
                }
            }
        }
        
        ikuSelect.addEventListener('change', updateBuktiOptions);
        
        // Mengatur nilai awal IKU dan jenis bukti
        if (ikuSelect.value) {
            updateBuktiOptions();
            const oldBuktiId = "{{ old('id_bukti_iku') }}";
            if (oldBuktiId) {
                buktiSelect.value = oldBuktiId;
            }
        }

        // Mengatur tampilan upload file secara dinamis
        const fileInputsContainer = document.getElementById('file-inputs-container');
        const addFileBtn = document.getElementById('add-file-btn');

        // Memperbarui nomor urut setiap berkas
        function updateLabels() {
            const cards = fileInputsContainer.querySelectorAll('.file-input-card');
            cards.forEach((card, index) => {
                const label = card.querySelector('.file-number-label');
                if (label) {
                    label.textContent = `Berkas Bukti #${index + 1}`;
                }
            });
        }

        // Mengatur tampilan keterangan file dan tombol tambah file
        function checkFileVisibility() {
            let hasFile = false;
            const cards = fileInputsContainer.querySelectorAll('.file-input-card');
            
            cards.forEach(card => {
                const fileInput = card.querySelector('.file-selector-input');
                const ketGroup = card.querySelector('.keterangan-file-group');
                
                if (fileInput.files && fileInput.files.length > 0) {
                    hasFile = true;
                    if (ketGroup) {
                        ketGroup.style.display = 'flex';
                    }
                } else {
                    if (ketGroup) {
                        ketGroup.style.display = 'none';
                    }
                }
            });

            if (hasFile) {
                addFileBtn.style.display = 'inline-flex';
            } else {
                addFileBtn.style.display = 'none';
            }
        }

        // Menangani perubahan pada input file
        fileInputsContainer.addEventListener('change', (e) => {
            if (e.target.classList.contains('file-selector-input')) {
                checkFileVisibility();
            }
        });

        // Menambahkan input file baru
        addFileBtn.addEventListener('click', () => {
            const nextIdx = fileInputsContainer.querySelectorAll('.file-input-card').length + 1;
            const newCard = document.createElement('div');
            newCard.className = 'file-input-card';
            newCard.style.cssText = 'background-color: var(--bg-surface2); border: 1px solid var(--border); border-radius: 12px; padding: 16px; display: flex; flex-direction: column; gap: 12px; position: relative; margin-top: 12px;';

            newCard.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="file-number-label" style="font-size: 0.8rem; font-weight: 700; color: #10b981;">Berkas Bukti #${nextIdx}</span>
                    <button type="button" class="btn-action-delete remove-file-btn" style="padding: 12px; height: auto; border-radius: 10px;" title="Hapus File">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label class="form-label-custom" style="font-size: 0.72rem; color: #94a3b8; font-weight: 600;">Pilih Berkas</label>
                    <input type="file" name="files[]" class="form-input-custom file-selector-input" required>
                </div>
                
                <div class="keterangan-file-group" style="display: none; flex-direction: column; gap: 6px;">
                    <label class="form-label-custom" style="font-size: 0.72rem; color: #94a3b8; font-weight: 600;">Keterangan Berkas</label>
                    <textarea name="keterangan_files[]" rows="2" placeholder="Tulis keterangan spesifik untuk berkas ini (contoh: Link Google Drive, Judul Dokumen, dll)..." class="form-input-custom file-keterangan-input"></textarea>
                </div>
            `;

            fileInputsContainer.appendChild(newCard);
            updateLabels();
        });

        // Menghapus input file
        fileInputsContainer.addEventListener('click', (e) => {
            const removeBtn = e.target.closest('.remove-file-btn');
            if (removeBtn) {
                const card = removeBtn.closest('.file-input-card');
                if (card) {
                    card.remove();
                    updateLabels();
                    checkFileVisibility();
                }
            }
        });

       // Mengecek kondisi awal tampilan file
        checkFileVisibility();
    });
</script>
@endsection
