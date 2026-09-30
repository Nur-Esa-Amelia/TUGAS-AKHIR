@extends('adminprodi.layouts.app')

@section('title', 'Target IKU/IKT Tahunan - Admin Prodi')
@section('page_title', 'Kelola Target IKU/IKT Tahunan')
@section('page_subtitle', 'Tentukan nilai target indikator kinerja utama per tahun akademik')

@section('content')
<!-- Check configuration warning -->
@if(!$settings || $settings->jml_mahasiswa == 0 || $settings->jml_dosen == 0)
<div class="alert-box alert-danger" style="margin-bottom: 20px;">
    <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
    </svg>
    <div>
        <strong>Perhatian!</strong> Jumlah mahasiswa/dosen prodi masih bernilai 0. Silakan isi terlebih dahulu di menu 
        <a href="{{ route('adminprodi.pengaturan.index') }}" style="color: inherit; text-decoration: underline; font-weight: bold;">Pengaturan Sistem</a> 
        agar kalkulasi pencapaian target persentase berjalan dengan benar.
    </div>
</div>
@endif

<div class="card" style="display: flex; flex-direction: column; gap: 20px;">
    <!-- Filter Year & Add Button -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px; border-bottom: 1px solid #1e293b; padding-bottom: 16px;">
        <form action="{{ route('adminprodi.pencapaian.index') }}" method="GET" style="display: flex; align-items: flex-end; gap: 12px; flex: 1; flex-wrap: wrap;">
            <div class="filter-item-custom" style="max-width: 200px;">
                <label for="tahun" class="form-label-custom">Pilih Tahun Akademik</label>
                <select id="tahun" name="tahun" class="form-select-custom" onchange="this.form.submit()">
                    @foreach($tahunList as $y)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item-custom" style="max-width: 250px;">
                <label for="search" class="form-label-custom">Pencarian</label>
                <div class="search-wrapper">
                    <input type="text" name="search" id="search" class="form-input-custom" placeholder="Cari IKU/IKT..." value="{{ request('search') }}">
                    <button type="submit" class="btn-search" title="Cari">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    <a href="{{ route('adminprodi.pencapaian.index', ['tahun' => $tahun]) }}" class="btn-reset" title="Reset Pencarian">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </a>
                </div>
            </div>

        </form>

        <button type="button" onclick="openModal('modalCreate')" class="btn btn-primary" style="padding: 10px 18px; font-size: 0.8rem;">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
            </svg>
            Atur Target Baru
        </button>
    </div>

    <!-- Table content -->
    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama Indikator IKU/IKT</th>
                    <th style="text-align: center;">Tahun</th>
                    <th style="text-align: center;">Target</th>
                    <th style="text-align: center;">Realisasi (Valid)</th>
                    <th style="text-align: center;">Status</th>
                    <th style="width: 180px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pencapaian as $index => $item)
                    <tr>
                        <td>{{ $pencapaian->firstItem() + $index }}</td>
                        <td>
                            <div style="font-weight: 600; color: var(--text-primary);">{{ $item->iku->nama_iku }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">Kategori: {{ $item->iku->kategori->nama_kategori }}</div>
                        </td>
                        <td style="text-align: center; font-weight: 600;">{{ $item->tahun }}</td>
                        <td style="text-align: center; font-weight: 700; color: #3b82f6;">
                            {{ $item->target }}{{ $item->satuan === 'persen' ? '%' : '' }}
                            <span style="font-size: 0.7rem; color: var(--text-muted); display: block; font-weight: normal;">({{ $item->objek }})</span>
                        </td>
                        <td style="text-align: center; font-weight: 700;">
                            {{ round($item->realisasi) }} Bukti
                        </td>
                        <td style="text-align: center;">
                            @if($item->status === 'Tercapai')
                                <span class="badge-custom badge-green">Tercapai</span>
                            @elseif($item->status === 'Perlu Perhatian')
                                <span class="badge-custom badge-yellow" style="background-color: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.2); color: #fbbf24;">Perlu Perhatian</span>
                            @else
                                <span class="badge-custom badge-rose">Tidak Tercapai</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <div style="display: inline-flex; gap: 8px; justify-content: center; align-items: center;">
                                <button type="button" onclick="openEditModal({{ $item->id }}, '{{ htmlspecialchars(addslashes($item->iku->nama_iku)) }}', '{{ htmlspecialchars(addslashes($item->target)) }}', '{{ htmlspecialchars(addslashes($item->satuan)) }}', '{{ htmlspecialchars(addslashes($item->objek)) }}', '{{ htmlspecialchars(addslashes($item->keterangan)) }}')" class="btn-action-edit" title="Edit">
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>
                                <form action="{{ route('adminprodi.pencapaian.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus target IKU/IKT ini?');" style="display: inline-flex;">
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 40px;">
                            Belum ada target IKU/IKT yang dikonfigurasi untuk tahun akademik {{ $tahun }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div>
        {{ $pencapaian->appends(['tahun' => $tahun, 'search' => request('search')])->onEachSide(10)->links() }}
    </div>
</div>

<!-- Modal Tambah -->
<div id="modalCreate" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Tambah Target IKU/IKT Tahun {{ $tahun }}</h5>
            <button type="button" class="btn-close" onclick="closeModal('modalCreate')">
                <svg style="width:20px;height:20px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('adminprodi.pencapaian.store') }}" method="POST" class="ajax-form">
            @csrf
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <div class="modal-body">
                <div style="margin-bottom: 16px;">
                    <label for="id_iku" class="form-label-custom">Pilih Indikator IKU/IKT <span style="color: #ef4444;">*</span></label>
                    <select id="id_iku" name="id_iku" class="form-select-custom" required>
                        <option value="">-- Pilih Indikator --</option>
                        @foreach($ikuList as $item)
                            <option value="{{ $item->id }}">{{ $item->nama_iku }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label for="target" class="form-label-custom">Nilai Target (Angka) <span style="color: #ef4444;">*</span></label>
                        <input type="text" class="form-input-custom" id="target" name="target" required placeholder="Contoh: 80">
                    </div>
                    <div>
                        <label for="satuan" class="form-label-custom">Satuan Target <span style="color: #ef4444;">*</span></label>
                        <select id="satuan" name="satuan" class="form-select-custom" required>
                            <option value="persen">Persen (%)</option>
                            <option value="angka">Angka</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <label for="objek" class="form-label-custom">Objek Sasaran / Pembagi <span style="color: #ef4444;">*</span></label>
                    <select id="objek" name="objek" class="form-select-custom" required>
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="dosen">Dosen</option>
                    </select>
                </div>

                <div style="margin-bottom: 16px;">
                    <label for="keterangan" class="form-label-custom">Keterangan Tambahan</label>
                    <textarea class="form-input-custom" id="keterangan" name="keterangan" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-header" style="justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalCreate')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Target</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Edit Target IKU/IKT Tahun {{ $tahun }}</h5>
            <button type="button" class="btn-close" onclick="closeModal('modalEdit')">
                <svg style="width:20px;height:20px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="formEdit" method="POST" class="ajax-form">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div style="margin-bottom: 16px;">
                    <label class="form-label-custom">Indikator IKU/IKT</label>
                    <input type="text" class="form-input-custom" id="edit_nama_iku" readonly style="background-color: var(--bg-surface2); cursor: not-allowed;">
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label for="edit_target" class="form-label-custom">Nilai Target (Angka) <span style="color: #ef4444;">*</span></label>
                        <input type="text" class="form-input-custom" id="edit_target" name="target" required>
                    </div>
                    <div>
                        <label for="edit_satuan" class="form-label-custom">Satuan Target <span style="color: #ef4444;">*</span></label>
                        <select id="edit_satuan" name="satuan" class="form-select-custom" required>
                            <option value="persen">Persen (%)</option>
                            <option value="angka">Angka (Absolut)</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <label for="edit_objek" class="form-label-custom">Objek Sasaran / Pembagi <span style="color: #ef4444;">*</span></label>
                    <select id="edit_objek" name="objek" class="form-select-custom" required>
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="dosen">Dosen</option>
                    </select>
                </div>

                <div style="margin-bottom: 16px;">
                    <label for="edit_keterangan" class="form-label-custom">Keterangan Tambahan</label>
                    <textarea class="form-input-custom" id="edit_keterangan" name="keterangan" rows="2"></textarea>
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
    function openEditModal(id, nama_iku, target, satuan, objek, keterangan) {
        // Set form action
        document.getElementById('formEdit').action = `/adminprodi/pencapaian/${id}`;
        
        // Populate inputs
        document.getElementById('edit_nama_iku').value = nama_iku;
        document.getElementById('edit_target').value = target;
        document.getElementById('edit_satuan').value = satuan;
        document.getElementById('edit_objek').value = objek;
        document.getElementById('edit_keterangan').value = keterangan;
        
        // Open modal
        openModal('modalEdit');
    }
</script>
@endsection
