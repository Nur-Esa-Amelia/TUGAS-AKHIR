@extends('adminsistem.layouts.app')

@section('title', 'Kelola Program Studi - Sistem Early Warning IKU/IKT')
@section('page_title', 'Kelola Program Studi')
@section('page_subtitle', 'Kelola data program studi aktif dalam sistem early warning IKU/IKT')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Action Header & Filters Card -->
    <div class="card" style="display: flex; flex-direction: column; gap: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div>
                <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary);">Daftar Program Studi</h3>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">Kelola data program studi aktif dalam sistem early warning IKU/IKT.</p>
            </div>
            <div>
                <button type="button" onclick="openAddModal()" class="btn btn-primary">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Prodi Baru
                </button>
            </div>
        </div>

        <!-- Filters Form -->
        <form action="{{ route('adminsistem.prodi.index') }}" method="GET" class="filter-row-custom">
            <!-- Search -->
            <div class="filter-item-custom" style="flex: 2;">
                <label for="search" class="form-label-custom">Cari Program Studi</label>
                <div class="search-wrapper">
                    <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Nama Prodi / Kode Prodi..." class="form-input-custom">
                    <button type="submit" class="btn-search" title="Cari">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    <a href="{{ route('adminsistem.prodi.index') }}" class="btn-reset" title="Reset Pencarian">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Kode Prodi</th>
                        <th>Nama Program Studi</th>
                        <th>Jumlah Pengguna</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prodis as $prodi)
                        <tr>
                            <!-- Kode Prodi -->
                            <td>
                                <span class="badge-custom badge-blue">
                                    {{ $prodi->kode_prodi }}
                                </span>
                            </td>
                            <!-- Nama Prodi -->
                            <td style="font-weight: 700; color: var(--text-primary);">
                                {{ $prodi->nama_prodi }}
                            </td>
                            <!-- Users Count -->
                            <td style="font-weight: 600; color: var(--text-secondary);">
                                {{ $prodi->users_count }} User
                            </td>
                            <!-- Actions -->
                            <td style="text-align: right;">
                                <div style="display: inline-flex; align-items: center; gap: 8px; justify-content: flex-end;">
                                    <!-- Edit Link -->
                                    <button type="button" onclick="openEditModal({{ $prodi->id }}, '{{ htmlspecialchars(addslashes($prodi->kode_prodi)) }}', '{{ htmlspecialchars(addslashes($prodi->nama_prodi)) }}')" class="btn-action-edit" title="Edit Prodi">
                                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('adminsistem.prodi.destroy', $prodi->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus prodi ini? Seluruh user di bawah prodi ini akan dilepas asosiasinya.')" style="display: inline-flex;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-delete" title="Hapus Prodi">
                                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 48px; color: var(--text-muted);">
                                <svg style="width: 32px; height: 32px; margin: 0 auto 12px; color: var(--text-muted); display: block;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Tidak ditemukan data program studi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($prodis->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid #1e293b; background-color: rgba(15, 23, 42, 0.2);">
                {{ $prodis->onEachSide(10)->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h2>Tambah Program Studi Baru</h2>
            <button class="btn-close" onclick="closeAddModal()">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <form action="{{ route('adminsistem.prodi.store') }}" method="POST" class="ajax-form">
                @csrf
                <div class="form-group-custom">
                    <label for="kode_prodi" class="form-label-custom">Kode Prodi</label>
                    <input type="text" class="form-input-custom" id="kode_prodi" name="kode_prodi" placeholder="Contoh: PRD-01" required>
                </div>

                <div class="form-group-custom" style="margin-top: 16px;">
                    <label for="nama_prodi" class="form-label-custom">Nama Program Studi</label>
                    <input type="text" class="form-input-custom" id="nama_prodi" name="nama_prodi" placeholder="Contoh: D3 Teknik Informatika" required>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Prodi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h2>Edit Program Studi</h2>
            <button class="btn-close" onclick="closeEditModal()">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <form id="editForm" method="POST" class="ajax-form">
                @csrf
                @method('PUT')
                
                <div class="form-group-custom">
                    <label for="edit_kode_prodi" class="form-label-custom">Kode Prodi</label>
                    <input type="text" class="form-input-custom" id="edit_kode_prodi" name="kode_prodi" required>
                </div>

                <div class="form-group-custom" style="margin-top: 16px;">
                    <label for="edit_nama_prodi" class="form-label-custom">Nama Program Studi</label>
                    <input type="text" class="form-input-custom" id="edit_nama_prodi" name="nama_prodi" required>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Perbarui Prodi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('addModal').classList.add('show');
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.remove('show');
    }

    function openEditModal(id, kode, nama) {
        document.getElementById('edit_kode_prodi').value = kode;
        document.getElementById('edit_nama_prodi').value = nama;
        
        const form = document.getElementById('editForm');
        form.action = `/adminsistem/prodi/${id}`;
        
        document.getElementById('editModal').classList.add('show');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.remove('show');
    }
</script>
@endsection
