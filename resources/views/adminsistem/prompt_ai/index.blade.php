@extends('adminsistem.layouts.app')

@section('title', 'Kelola Prompt AI - Sistem Early Warning IKU/IKT')
@section('page_title', 'Kelola Prompt AI')
@section('page_subtitle', 'Konfigurasi template prompt yang dikirimkan ke model Gemini AI')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- {{-- PANDUAN PLACEHOLDER --}} -->
    <!-- <div class="card" style="background: linear-gradient(135deg, rgba(99,102,241,0.08) 0%, rgba(139,92,246,0.05) 100%); border: 1px solid rgba(99,102,241,0.2);">
        <div style="display: flex; gap: 14px; align-items: flex-start;">
            <div style="flex-shrink: 0; width: 36px; height: 36px; background: rgba(99,102,241,0.15); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 20px; height: 20px; color: #6366f1;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin: 0 0 8px;">Panduan Penggunaan Placeholder</h3>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0 0 12px;">
                    Gunakan placeholder berikut di dalam template prompt. Saat generate AI dijalankan, placeholder akan otomatis digantikan dengan data nyata dari sistem.
                    Jika tidak ada prompt yang diaktifkan, sistem akan menggunakan prompt bawaan (statis).
                </p>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    @foreach([
                        '{nama_iku}'             => 'Nama IKU/IKT',
                        '{deskripsi}'            => 'Deskripsi IKU/IKT',
                        '{prodi}'                => 'Nama Program Studi',
                        '{tahun}'                => 'Tahun Akademik',
                        '{target}'               => 'Target Capaian',
                        '{realisasi}'            => 'Realisasi Capaian',
                        '{status}'               => 'Status IKU',
                        '{daftar_bukti_wajib}'   => 'Daftar Jenis Bukti Wajib',
                        '{bukti_sudah_diunggah}' => 'Bukti yang Sudah Diunggah',
                        '{bukti_belum_diunggah}' => 'Bukti yang Belum Diunggah',
                    ] as $ph => $label)
                        <span title="{{ $label }}" style="display: inline-flex; align-items: center; gap: 5px; background: rgba(99,102,241,0.1); color: #6366f1; border: 1px solid rgba(99,102,241,0.25); padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-family: monospace; cursor: default;">
                            {{ $ph }}
                            <span style="font-family: sans-serif; font-size: 0.65rem; color: var(--text-muted);">→ {{ $label }}</span>
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div> -->

    {{-- DAFTAR PROMPT --}}
    <div class="card" style="display: flex; flex-direction: column; gap: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin: 0;">Daftar Template Prompt</h3>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">Hanya satu prompt yang dapat aktif pada satu waktu.</p>
            </div>
            <button type="button" onclick="openAddModal()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Prompt
            </button>
        </div>

        @if(session('success'))
            <div style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); color: #10b981; padding: 12px 16px; border-radius: 8px; font-size: 0.85rem;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #ef4444; padding: 12px 16px; border-radius: 8px; font-size: 0.85rem;">
                <ul style="margin: 0; padding-left: 16px;">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="display: flex; flex-direction: column; gap: 16px;">
            @forelse($prompts as $prompt)
                <div style="border: 1px solid var(--border); border-radius: 10px; padding: 18px 20px; display: flex; flex-direction: column; gap: 12px; {{ $prompt->status === 'aktif' ? 'border-color: rgba(16,185,129,0.4); background: rgba(16,185,129,0.03);' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                            <span style="font-size: 1rem; font-weight: 700; color: var(--text-primary);">{{ $prompt->name }}</span>
                            @if($prompt->status === 'aktif')
                                <span style="display: inline-flex; align-items: center; padding: 3px 10px; background-color: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 0.72rem; font-weight: 600; border-radius: 20px;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #10b981; margin-right: 6px;"></span>
                                    Aktif (Digunakan)
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; padding: 3px 10px; background-color: rgba(107, 114, 128, 0.1); color: #6b7280; font-size: 0.72rem; font-weight: 600; border-radius: 20px;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #6b7280; margin-right: 6px;"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                        <div style="display: flex; gap: 8px; flex-shrink: 0;">
                            @if($prompt->status !== 'aktif')
                                <form action="{{ route('adminsistem.prompt_ai.activate', $prompt->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary" style="padding: 6px 14px; font-size: 0.78rem;" title="Aktifkan prompt ini">
                                        Aktifkan
                                    </button>
                                </form>
                            @endif
                            <button type="button" class="btn" style="background-color: #f3f4f6; color: #4b5563; padding: 6px 10px;"
                                onclick="openEditModal(
                                    {{ $prompt->id }},
                                    '{{ addslashes($prompt->name) }}',
                                    '{{ addslashes($prompt->prompt_template) }}',
                                    '{{ $prompt->status }}',
                                    '{{ addslashes($prompt->keterangan ?? '') }}'
                                )" title="Edit Prompt">
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <form action="{{ route('adminsistem.prompt_ai.destroy', $prompt->id) }}" method="POST" style="margin:0;"
                                  onsubmit="return confirm('Hapus prompt \'{{ addslashes($prompt->name) }}\'?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" style="background-color: #fee2e2; color: #ef4444; padding: 6px 10px;" title="Hapus">
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($prompt->keterangan)
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0;">{{ $prompt->keterangan }}</p>
                    @endif

                    {{-- Preview prompt (collapsed) --}}
                    <div>
                        <button type="button" onclick="togglePreview('preview-{{ $prompt->id }}')"
                                style="font-size: 0.78rem; color: #6366f1; background: none; border: none; cursor: pointer; padding: 0; display: flex; align-items: center; gap: 4px;">
                            <svg id="icon-{{ $prompt->id }}" style="width: 14px; height: 14px; transition: transform 0.2s;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Lihat / Sembunyikan Template Prompt
                        </button>
                        <div id="preview-{{ $prompt->id }}" style="display: none; margin-top: 10px;">
                            <pre style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 8px; padding: 14px; font-size: 0.75rem; color: var(--text-secondary); white-space: pre-wrap; word-break: break-word; max-height: 300px; overflow-y: auto; font-family: monospace; line-height: 1.6;">{{ $prompt->prompt_template }}</pre>
                        </div>
                    </div>

                    <div style="font-size: 0.72rem; color: var(--text-muted);">
                        Diperbarui: {{ $prompt->updated_at->format('d M Y H:i') }}
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 40px 24px; color: var(--text-muted); font-size: 0.85rem;">
                    <svg style="width: 40px; height: 40px; margin: 0 auto 12px; display: block; opacity: 0.4;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"></path>
                    </svg>
                    Belum ada template prompt. Klik <strong>Tambah Prompt</strong> untuk membuat.
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Modal Tambah Prompt --}}
<div id="addModal" class="custom-modal">
    <div class="custom-modal-content" style="max-width: 720px; width: 95%;">
        <div class="custom-modal-header">
            <h2 class="custom-modal-title">Tambah Template Prompt AI</h2>
            <button type="button" class="custom-modal-close" onclick="closeAddModal()">&times;</button>
        </div>
        <div class="custom-modal-body" style="max-height: 80vh; overflow-y: auto;">
            <form action="{{ route('adminsistem.prompt_ai.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div class="form-group-custom">
                        <label for="add_name" class="form-label-custom">Nama Template</label>
                        <input type="text" id="add_name" name="name" class="form-input-custom" placeholder="Misal: Prompt Standar, Prompt Ringkas" required>
                    </div>

                    <div class="form-group-custom">
                        <label for="add_keterangan" class="form-label-custom">Keterangan <span style="color: var(--text-muted); font-weight: 400;">(opsional)</span></label>
                        <input type="text" id="add_keterangan" name="keterangan" class="form-input-custom" placeholder="Catatan singkat tentang template ini">
                    </div>

                    <div class="form-group-custom">
                        <label for="add_status" class="form-label-custom">Status</label>
                        <select id="add_status" name="status" class="form-select-custom" required>
                            <option value="nonaktif">Nonaktif</option>
                            <option value="aktif">Aktif (nonaktifkan prompt lain)</option>
                        </select>
                    </div>

                    <div class="form-group-custom">
                        <label for="add_prompt_template" class="form-label-custom">
                            Template Prompt
                            <span style="font-size: 0.72rem; color: var(--text-muted); font-weight: 400; margin-left: 6px;">Gunakan placeholder seperti {nama_iku}, {prodi}, dll.</span>
                        </label>
                        <textarea id="add_prompt_template" name="prompt_template" class="form-input-custom"
                                  rows="16" required
                                  style="font-family: monospace; font-size: 0.8rem; resize: vertical;"
                                  placeholder="Tulis template prompt di sini. Contoh:&#10;Anda adalah Asisten AI...&#10;&#10;Data IKU: {nama_iku} ({deskripsi})&#10;Prodi: {prodi}, Tahun: {tahun}&#10;..."></textarea>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Template</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Prompt --}}
<div id="editModal" class="custom-modal">
    <div class="custom-modal-content" style="max-width: 720px; width: 95%;">
        <div class="custom-modal-header">
            <h2 class="custom-modal-title">Edit Template Prompt AI</h2>
            <button type="button" class="custom-modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <div class="custom-modal-body" style="max-height: 80vh; overflow-y: auto;">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div class="form-group-custom">
                        <label for="edit_name" class="form-label-custom">Nama Template</label>
                        <input type="text" id="edit_name" name="name" class="form-input-custom" required>
                    </div>

                    <div class="form-group-custom">
                        <label for="edit_keterangan" class="form-label-custom">Keterangan <span style="color: var(--text-muted); font-weight: 400;">(opsional)</span></label>
                        <input type="text" id="edit_keterangan" name="keterangan" class="form-input-custom">
                    </div>

                    <div class="form-group-custom">
                        <label for="edit_status" class="form-label-custom">Status</label>
                        <select id="edit_status" name="status" class="form-select-custom" required>
                            <option value="nonaktif">Nonaktif</option>
                            <option value="aktif">Aktif (nonaktifkan prompt lain)</option>
                        </select>
                    </div>

                    <div class="form-group-custom">
                        <label for="edit_prompt_template" class="form-label-custom">
                            Template Prompt
                            <span style="font-size: 0.72rem; color: var(--text-muted); font-weight: 400; margin-left: 6px;">Gunakan placeholder seperti {nama_iku}, {prodi}, dll.</span>
                        </label>
                        <textarea id="edit_prompt_template" name="prompt_template" class="form-input-custom"
                                  rows="16" required
                                  style="font-family: monospace; font-size: 0.8rem; resize: vertical;"></textarea>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('addModal').style.display = 'flex';
    }
    function closeAddModal() {
        document.getElementById('addModal').style.display = 'none';
    }

    function openEditModal(id, name, promptTemplate, status, keterangan) {
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_prompt_template').value = promptTemplate;
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_keterangan').value = keterangan;
        document.getElementById('editForm').action = '/adminsistem/prompt-ai/' + id;
        document.getElementById('editModal').style.display = 'flex';
    }
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function togglePreview(id) {
        const el = document.getElementById(id);
        const promptId = id.replace('preview-', '');
        const icon = document.getElementById('icon-' + promptId);
        if (el.style.display === 'none') {
            el.style.display = 'block';
            icon.style.transform = 'rotate(90deg)';
        } else {
            el.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
        }
    }

    window.onclick = function(event) {
        if (event.target === document.getElementById('addModal')) closeAddModal();
        if (event.target === document.getElementById('editModal')) closeEditModal();
    }
</script>

<style>
    .custom-modal {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
        z-index: 10000;
        backdrop-filter: blur(4px);
    }
    .custom-modal-content {
        background-color: var(--bg-surface);
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        border: 1px solid var(--border);
    }
    .custom-modal-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .custom-modal-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }
    .custom-modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-muted);
        cursor: pointer;
        line-height: 1;
        padding: 0;
    }
    .custom-modal-close:hover { color: var(--text-primary); }
    .custom-modal-body { padding: 24px; }
</style>
@endsection
