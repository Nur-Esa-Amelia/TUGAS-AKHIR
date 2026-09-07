@extends('adminprodi.layouts.app')

@section('title', 'Perbaiki Bukti IKU/IKT - Kaprodi')
@section('page_title', 'Perbaiki Bukti IKU/IKT')
@section('page_subtitle', 'Perbarui berkas bukti pemenuhan Indikator Kinerja Utama Program Studi Anda berdasarkan catatan validator')

@section('content')
<div class="card" style="max-width: 650px; margin: 0 auto;">
    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 20px; border-bottom: 1px solid #1e293b; padding-bottom: 12px; color: #fb7185;">Form Perbaikan Bukti Kinerja - Tahun {{ $tahunAktif }}</h3>

    @if($pengisian->catatan_validator)
        <div style="margin-bottom: 20px; padding: 14px 16px; background-color: rgba(244,63,94,0.08); border-radius: 8px; border: 1px solid rgba(244,63,94,0.15); line-height: 1.5;">
            <div style="font-weight: 700; color: #fb7185; font-size: 0.9rem; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Catatan Validator (Perlu Perbaikan):
            </div>
            <p style="font-size: 0.85rem; color: var(--text-secondary);">{{ $pengisian->catatan_validator }}</p>
        </div>
    @endif

    <form action="{{ route('adminprodi.pengisian.update', $pengisian->id) }}" method="POST" enctype="multipart/form-data" class="form-layout-container">
        @csrf
        @method('PUT')

        <!-- IKU/IKT Select -->
        <div class="form-group-custom">
            <label for="id_iku" class="form-label-custom">Pilih Indikator IKU/IKT Program Studi</label>
            <select id="id_iku" name="id_iku" class="form-select-custom @error('id_iku') is-invalid @enderror" required>
                <option value="">-- Pilih Indikator --</option>
                @foreach($ikus as $item)
                    <option value="{{ $item->id }}" {{ old('id_iku', $pengisian->id_iku) == $item->id ? 'selected' : '' }}>
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
                    <option value="{{ $bukti->id }}" data-iku-id="{{ $bukti->id_iku }}" {{ old('id_bukti_iku', $pengisian->id_bukti_iku) == $bukti->id ? 'selected' : '' }}>
                        {{ $bukti->nama_bukti }}
                    </option>
                @endforeach
            </select>
            <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 2px;">Pilihan jenis bukti disesuaikan secara otomatis berdasarkan IKU/IKT yang dipilih di atas.</small>
            @error('id_bukti_iku')
                <span class="form-error-custom">{{ $message }}</span>
            @enderror
        </div>

        <!-- Berkas Bukti Saat Ini -->
        <div class="form-group-custom">
            <label class="form-label-custom">Berkas Bukti Saat Ini</label>
            <div id="existing-files-container" style="display: flex; flex-direction: column; gap: 16px;">
                @forelse($pengisian->files as $index => $file)
                    <div class="existing-file-card" id="existing-file-{{ $file->id }}" style="background-color: var(--bg-surface2); border: 1px solid var(--border); border-radius: 12px; padding: 16px; display: flex; flex-direction: column; gap: 12px; position: relative;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <a href="{{ asset($file->file_bukti) }}" target="_blank" style="color: #10b981; text-decoration: underline; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; font-weight: 600; max-width: 80%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <svg style="width: 14px; height: 14px; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                {{ $file->nama_file }}
                            </a>
                            <button type="button" class="btn-action-delete remove-existing-file-btn" data-file-id="{{ $file->id }}" style="padding: 12px; height: auto; border-radius: 10px;" title="Hapus Berkas Ini">
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div style="display: flex; flex-direction: column; gap: 6px;">
                            <label class="form-label-custom" style="font-size: 0.72rem; color: #94a3b8; font-weight: 600;">Keterangan Berkas</label>
                            <textarea name="existing_keterangan[{{ $file->id }}]" rows="2" placeholder="Tulis keterangan spesifik untuk berkas ini..." class="form-input-custom">{{ old('existing_keterangan.'.$file->id, $file->keterangan) }}</textarea>
                        </div>
                    </div>
                @empty
                    <span style="color: var(--text-muted); font-size: 0.85rem;">Tidak ada berkas bukti sebelumnya.</span>
                @endforelse
            </div>
            <div id="deleted-files-inputs"></div>
        </div>

        <!-- Berkas Bukti Baru (Dynamic Cards - Opsional) -->
        <div class="form-group-custom">
            <label class="form-label-custom">Unggah Berkas Bukti Baru (Opsional)</label>
            <div id="file-inputs-container" style="display: flex; flex-direction: column; gap: 16px;">
                <div class="file-input-card" style="background-color: var(--bg-surface2); border: 1px solid var(--border); border-radius: 12px; padding: 16px; display: flex; flex-direction: column; gap: 12px; position: relative;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="file-number-label" style="font-size: 0.8rem; font-weight: 700; color: #10b981;">Berkas Bukti Baru #1</span>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <label class="form-label-custom" style="font-size: 0.72rem; color: #94a3b8; font-weight: 600;">Pilih Berkas</label>
                        <input type="file" name="files[]" class="form-input-custom file-selector-input">
                    </div>
                    
                    <div class="keterangan-file-group" style="display: none; flex-direction: column; gap: 6px;">
                        <label class="form-label-custom" style="font-size: 0.72rem; color: #94a3b8; font-weight: 600;">Keterangan Berkas</label>
                        <textarea name="keterangan_files[]" rows="2" placeholder="Tulis keterangan spesifik untuk berkas ini (contoh: Link Google Drive, Judul Dokumen, dll)..." class="form-input-custom file-keterangan-input"></textarea>
                    </div>
                </div>
            </div>
            
            <button type="button" id="add-file-btn" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.75rem; align-self: flex-start; margin-top: 12px; display: none;">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                </svg>
                Tambah File Lainnya
            </button>
            
            <small style="color: var(--text-muted); font-size: 0.75rem; display: block; margin-top: 12px;">
                Format berkas yang didukung: <strong>PDF, JPG, JPEG, PNG, ZIP, DOC, DOCX</strong>. Maksimal ukuran per file: <strong>10 MB</strong>.<br>
                <span style="color: #fb7185; font-weight: 500;">Catatan: Berkas baru yang diunggah akan ditambahkan ke daftar bukti IKU/IKT Anda.</span>
            </small>
            @error('files')
                <span class="form-error-custom">{{ $message }}</span>
            @enderror
        </div>

        <!-- Action Buttons -->
        <div class="form-footer-actions">
            <a href="{{ route('adminprodi.bukti-dosen') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" style="background-color: #10b981; border-color: #10b981;">Simpan Perubahan</button>
        </div>
    </form>
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
                // Memfilter bukti yang sesuai dengan IKU yang dipilih
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
        
        // Mengatur pilihan awal IKU dan jenis bukti
        if (ikuSelect.value) {
            updateBuktiOptions();
            const oldBuktiId = "{{ old('id_bukti_iku', $pengisian->id_bukti_iku) }}";
            if (oldBuktiId) {
                buktiSelect.value = oldBuktiId;
            }
        }

        // Mengatur fitur upload file secara dinamis
        const fileInputsContainer = document.getElementById('file-inputs-container');
        const addFileBtn = document.getElementById('add-file-btn');
        const existingFilesContainer = document.getElementById('existing-files-container');
        const deletedFilesInputs = document.getElementById('deleted-files-inputs');

        // Memperbarui nomor urut setiap berkas baru
        function updateLabels() {
            const cards = fileInputsContainer.querySelectorAll('.file-input-card');
            cards.forEach((card, index) => {
                const label = card.querySelector('.file-number-label');
                if (label) {
                    label.textContent = `Berkas Bukti Baru #${index + 1}`;
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

        // Mengecek perubahan pada input file
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
                    <span class="file-number-label" style="font-size: 0.8rem; font-weight: 700; color: #10b981;">Berkas Bukti Baru #${nextIdx}</span>
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

        // Menghapus input file baru
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

       // Menangani penghapusan file yang sudah tersimpan
        if (existingFilesContainer) {
            existingFilesContainer.addEventListener('click', (e) => {
                const removeBtn = e.target.closest('.remove-existing-file-btn');
                if (removeBtn) {
                    const fileId = removeBtn.getAttribute('data-file-id');
                    const card = document.getElementById('existing-file-' + fileId);
                    if (card) {
                        // Menyembunyikan file yang akan dihapus
                        card.style.display = 'none';
                        // Menyimpan ID file yang akan dihapus untuk diproses server
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'deleted_files[]';
                        hiddenInput.value = fileId;
                        deletedFilesInputs.appendChild(hiddenInput);
                    }
                }
            });
        }

        // Run check initial visibility
        checkFileVisibility();
    });
</script>
@endsection
