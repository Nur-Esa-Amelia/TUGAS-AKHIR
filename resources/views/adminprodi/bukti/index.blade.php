@extends('adminprodi.layouts.app')

@section('title', 'Jenis Bukti - Admin Prodi')
@section('page_title', 'Kelola Jenis Bukti')
@section('page_subtitle', 'Tentukan jenis berkas/dokumen bukti yang wajib diunggah untuk setiap IKU/IKT')

@section('content')
<div class="card" style="display: flex; flex-direction: column; gap: 20px; width: 100%;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Daftar Jenis Bukti IKU/IKT</h3>

        @if(auth()->user()->role === 'admin_p2mp')
            <button type="button" onclick="openModal('modalCreate')" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.8rem;">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                </svg>
                Tambah Jenis Bukti
            </button>
        @endif
    </div>

    <div style="display: flex; justify-content: flex-end; align-items: center; width: 100%;">
        <form method="GET" action="{{ route('adminprodi.bukti.index') }}" id="filterBuktiForm" style="display: flex; align-items: center; gap: 10px; margin: 0; flex-wrap: nowrap; justify-content: flex-end;">
            <select id="id_iku" name="id_iku" class="form-select-custom" style="width: max-content; min-width: 200px; max-width: 400px; padding-right: 32px;" onchange="document.getElementById('filterBuktiForm').submit();">
                <option value="">Semua IKU/IKT</option>
                @foreach($ikuList as $iku)
                    <option value="{{ $iku->id }}" {{ (string)$selectedIku === (string)$iku->id ? 'selected' : '' }}>
                        {{ $iku->nama_iku }}
                    </option>
                @endforeach
            </select>
            <div class="search-wrapper">
                <input type="text" name="search" class="form-input-custom" placeholder="Cari Jenis Bukti..." value="{{ request('search') }}">
                <button type="submit" class="btn-search" title="Cari">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
                <a href="{{ route('adminprodi.bukti.index') }}" class="btn-reset" title="Reset Pencarian">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama Bukti</th>
                    <th>Terkait IKU/IKT</th>
                    <th>Keterangan / Deskripsi</th>
                    @if(auth()->user()->role === 'admin_p2mp')
                        <th style="width: 180px; text-align: center;">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($bukti as $index => $item)
                    <tr>
                        <td>{{ $bukti->firstItem() + $index }}</td>
                        <td style="font-weight: 600; color: var(--text-primary);">{{ $item->nama_bukti }}</td>
                        <td>
                            <div style="font-weight: 600; color: #3b82f6; max-width: 250px; white-space: normal; overflow-wrap: anywhere; line-height: 1.4;">
                                {{ $item->iku->nama_iku }}
                            </div>
                        </td>
                        <td style="color: var(--text-secondary); font-size: 0.8rem; max-width: 300px; white-space: normal; overflow-wrap: anywhere; text-align: justify; vertical-align: top; line-height: 1.5;">
                            {{ $item->deskripsi ?? '-' }}
                        </td>
                        @if(auth()->user()->role === 'admin_p2mp')
                            <td style="text-align: center;">
                                <div style="display: inline-flex; gap: 8px; justify-content: center; align-items: center;">
                                    <button type="button" onclick="openEditModal({{ $item->id }}, '{{ $item->id_iku }}', '{{ htmlspecialchars(addslashes($item->nama_bukti)) }}', '{{ htmlspecialchars(addslashes($item->deskripsi)) }}')" class="btn-action-edit" title="Edit">
                                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </button>
                                    <form action="{{ route('adminprodi.bukti.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis bukti ini? Seluruh file bukti yang telah diupload dosen untuk tipe bukti ini juga akan ikut terhapus.');" style="display: inline-flex;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-delete" title="Hapus">
                                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->role === 'admin_p2mp' ? 5 : 4 }}" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            Belum ada data jenis bukti.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div>
        {{ $bukti->appends(['search' => request('search'), 'id_iku' => request('id_iku')])->onEachSide(10)->links() }}
    </div>
</div>

@if(auth()->user()->role === 'admin_p2mp')
<!-- Modal Tambah -->
<div id="modalCreate" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Tambah Jenis Bukti IKU/IKT Baru</h5>
            <button type="button" class="btn-close" onclick="closeModal('modalCreate')">
                <svg style="width:20px;height:20px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('adminprodi.bukti.store') }}" method="POST" class="ajax-form">
            @csrf
            <div class="modal-body">
                <div style="margin-bottom: 16px;">
                    <label for="id_iku_create" class="form-label-custom">Terkait IKU/IKT <span style="color: #ef4444;">*</span></label>
                    <select id="id_iku_create" name="id_iku" class="form-select-custom" required>
                        <option value="">-- Pilih IKU/IKT --</option>
                        @foreach($ikuList as $iku)
                            <option value="{{ $iku->id }}">{{ $iku->nama_iku }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 16px;">
                    <label for="nama_bukti" class="form-label-custom">Nama Jenis Bukti <span style="color: #ef4444;">*</span></label>
                    <input type="text" class="form-input-custom" id="nama_bukti" name="nama_bukti" required placeholder="Contoh: SK Rektor, Laporan Keuangan">
                </div>
                <div style="margin-bottom: 16px;">
                    <label for="deskripsi" class="form-label-custom">Keterangan / Instruksi</label>
                    <textarea class="form-input-custom" id="deskripsi" name="deskripsi" rows="3" placeholder="Contoh: Bukti harus berupa PDF dan sudah ditandatangani..."></textarea>
                </div>
            </div>
            <div class="modal-header" style="justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalCreate')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Jenis Bukti</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Edit Jenis Bukti</h5>
            <button type="button" class="btn-close" onclick="closeModal('modalEdit')">
                <svg style="width:20px;height:20px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="formEdit" method="POST" class="ajax-form">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div style="margin-bottom: 16px;">
                    <label for="edit_id_iku" class="form-label-custom">Terkait IKU/IKT <span style="color: #ef4444;">*</span></label>
                    <select id="edit_id_iku" name="id_iku" class="form-select-custom" required>
                        <option value="">-- Pilih IKU/IKT --</option>
                        @foreach($ikuList as $iku)
                            <option value="{{ $iku->id }}">{{ $iku->nama_iku }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 16px;">
                    <label for="edit_nama_bukti" class="form-label-custom">Nama Jenis Bukti <span style="color: #ef4444;">*</span></label>
                    <input type="text" class="form-input-custom" id="edit_nama_bukti" name="nama_bukti" required>
                </div>
                <div style="margin-bottom: 16px;">
                    <label for="edit_deskripsi" class="form-label-custom">Keterangan / Instruksi</label>
                    <textarea class="form-input-custom" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-header" style="justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, id_iku, nama_bukti, deskripsi) {
        // Set form action
        document.getElementById('formEdit').action = `/adminprodi/bukti/${id}`;
        
        // inputan
        document.getElementById('edit_id_iku').value = id_iku;
        document.getElementById('edit_nama_bukti').value = nama_bukti;
        document.getElementById('edit_deskripsi').value = deskripsi;
        
        // Open modal
        openModal('modalEdit');
    }
</script>
@endif
@endsection
