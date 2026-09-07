@extends('adminsistem.layouts.app')

@section('title', 'Kelola User - Sistem Early Warning IKU/IKT')
@section('page_title', 'Kelola Pengguna')
@section('page_subtitle', 'Kelola hak akses dan asosiasi program studi pengguna')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Action Header & Filters Card -->
    <div class="card" style="display: flex; flex-direction: column; gap: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div>
                <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary);">Daftar Pengguna</h3>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">Kelola hak akses dan asosiasi program studi pengguna aplikasi.</p>
            </div>
            <div>
                <button type="button" onclick="openAddModal()" class="btn btn-primary">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Tambah User Baru
                </button>
            </div>
        </div>

        <!-- Filters Form -->
        <form action="{{ route('adminsistem.users.index') }}" method="GET" class="filter-row-custom">
            <!-- Search -->
            <div class="filter-item-custom" style="flex: 2;">
                <label for="search" class="form-label-custom">Cari Pengguna</label>
                <div class="search-wrapper">
                    <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Nama / Email..." class="form-input-custom">
                    <button type="submit" class="btn-search" title="Cari">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    <a href="{{ route('adminsistem.users.index') }}" class="btn-reset" title="Reset Pencarian">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Role Filter -->
            <div class="filter-item-custom">
                <label for="role" class="form-label-custom">Filter Role</label>
                <select name="role" id="role" onchange="this.form.submit()" class="form-select-custom">
                    <option value="">Semua Role</option>
                    @foreach($roles as $key => $value)
                        <option value="{{ $key }}" {{ $role == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Prodi Filter -->
            <div class="filter-item-custom">
                <label for="prodi_id" class="form-label-custom">Filter Program Studi</label>
                <select name="prodi_id" id="prodi_id" onchange="this.form.submit()" class="form-select-custom">
                    <option value="">Semua Prodi / Tanpa Prodi</option>
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}" {{ $prodiId == $p->id ? 'selected' : '' }}>{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
            </div>
            

        </form>
    </div>

    <!-- Table Card -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Email</th>
                        <th>Hak Akses / Role</th>
                        <th>Program Studi</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <!-- Name -->
                            <td style="font-weight: 700; color: var(--text-primary);">
                                {{ $user->name }}
                            </td>
                            <!-- Email -->
                            <td>
                                {{ $user->email }}
                            </td>
                            <!-- Role Badge -->
                            <td>
                                @if($user->role === 'admin_sistem')
                                    <span class="badge-custom badge-rose">Admin Sistem</span>
                                @elseif($user->role === 'admin_p2mp')
                                    <span class="badge-custom badge-purple">Admin P2MP</span>
                                @elseif($user->role === 'admin_prodi')
                                    <span class="badge-custom badge-blue">Admin Prodi</span>
                                @elseif($user->role === 'kaprodi')
                                    <span class="badge-custom badge-cyan">Kaprodi</span>
                                @else
                                    <span class="badge-custom badge-green">Dosen</span>
                                @endif
                            </td>
                            <!-- Program Studi -->
                            <td>
                                @if($user->prodi)
                                    <span style="font-weight: 600; color: #38bdf8; display: block;">{{ $user->prodi->nama_prodi }}</span>
                                    <span style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">{{ $user->prodi->kode_prodi }}</span>
                                @else
                                    <span style="font-size: 0.75rem; color: var(--text-muted); font-style: italic;">Tidak Terkait Prodi</span>
                                @endif
                            </td>
                            <!-- Actions -->
                            <td style="text-align: right;">
                                <div style="display: inline-flex; align-items: center; gap: 8px; justify-content: flex-end;">
                                    <!-- Edit Link -->
                                    <button type="button" onclick="openEditModal({{ $user->id }}, '{{ htmlspecialchars(addslashes($user->name)) }}', '{{ htmlspecialchars(addslashes($user->email)) }}', '{{ $user->role }}', '{{ $user->prodi_id }}')" class="btn-action-edit" title="Edit User">
                                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </button>

                                    <!-- Delete Button -->
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('adminsistem.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')" style="display: inline-flex;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-delete" title="Hapus User">
                                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="btn-action-delete" style="opacity: 0.35; cursor: not-allowed;" title="Anda tidak dapat menghapus akun sendiri">
                                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 48px; color: var(--text-muted);">
                                <svg style="width: 32px; height: 32px; margin: 0 auto 12px; color: var(--text-muted); display: block;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Tidak ditemukan data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid #1e293b; background-color: rgba(15, 23, 42, 0.2);">
                {{ $users->onEachSide(10)->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h2>Tambah Pengguna Baru</h2>
            <button class="btn-close" onclick="closeAddModal()">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <form action="{{ route('adminsistem.users.store') }}" method="POST" class="ajax-form">
                @csrf
                <div class="form-group-custom">
                    <label for="name" class="form-label-custom">Nama Lengkap</label>
                    <input type="text" class="form-input-custom" id="name" name="name" required>
                </div>

                <div class="form-group-custom" style="margin-top: 16px;">
                    <label for="email" class="form-label-custom">Email</label>
                    <input type="email" class="form-input-custom" id="email" name="email" required>
                </div>

                <div class="form-group-custom" style="margin-top: 16px;">
                    <label for="password" class="form-label-custom">Password (Default: password)</label>
                    <input type="password" class="form-input-custom" id="password" name="password" placeholder="Kosongkan untuk default password">
                </div>

                <div class="form-group-custom" style="margin-top: 16px;">
                    <label for="role_select" class="form-label-custom">Role</label>
                    <select name="role" id="role_select" class="form-select-custom" required onchange="toggleProdi(this.value, 'prodi_group')">
                        <option value="">Pilih Role...</option>
                        @foreach($roles as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom" id="prodi_group" style="margin-top: 16px; display: none;">
                    <label for="prodi_select" class="form-label-custom">Program Studi</label>
                    <select name="prodi_id" id="prodi_select" class="form-select-custom">
                        <option value="">Pilih Program Studi...</option>
                        @foreach($prodis as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h2>Edit Pengguna</h2>
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
                    <label for="edit_name" class="form-label-custom">Nama Lengkap</label>
                    <input type="text" class="form-input-custom" id="edit_name" name="name" required>
                </div>

                <div class="form-group-custom" style="margin-top: 16px;">
                    <label for="edit_email" class="form-label-custom">Email</label>
                    <input type="email" class="form-input-custom" id="edit_email" name="email" required>
                </div>

                <div class="form-group-custom" style="margin-top: 16px;">
                    <label for="edit_password" class="form-label-custom">Password Baru (Opsional)</label>
                    <input type="password" class="form-input-custom" id="edit_password" name="password" placeholder="Isi untuk mengganti password">
                </div>

                <div class="form-group-custom" style="margin-top: 16px;">
                    <label for="edit_role_select" class="form-label-custom">Role</label>
                    <select name="role" id="edit_role_select" class="form-select-custom" required onchange="toggleProdi(this.value, 'edit_prodi_group')">
                        <option value="">Pilih Role...</option>
                        @foreach($roles as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom" id="edit_prodi_group" style="margin-top: 16px; display: none;">
                    <label for="edit_prodi_select" class="form-label-custom">Program Studi</label>
                    <select name="prodi_id" id="edit_prodi_select" class="form-select-custom">
                        <option value="">Pilih Program Studi...</option>
                        @foreach($prodis as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Perbarui User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleProdi(role, groupId) {
        //Program Studi disembunyikan dari p2mp
        const group = document.getElementById(groupId);
        if (role === 'admin_p2mp' || role === 'admin_sistem') {
            group.style.display = 'none';
        } else {
            group.style.display = 'block';
        }
    }

    function openAddModal() {
        document.getElementById('addModal').classList.add('show');
        toggleProdi(document.getElementById('role_select').value, 'prodi_group');
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.remove('show');
    }

    function openEditModal(id, name, email, role, prodi_id) {
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_role_select').value = role;
        document.getElementById('edit_prodi_select').value = prodi_id || '';
        
        toggleProdi(role, 'edit_prodi_group');
        
        const form = document.getElementById('editForm');
        form.action = `/adminsistem/users/${id}`;
        
        document.getElementById('editModal').classList.add('show');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.remove('show');
    }
</script>
@endsection
