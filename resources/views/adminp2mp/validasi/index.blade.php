@extends('adminp2mp.layouts.app')

@section('title', 'Validasi Bukti IKU/IKT - Sistem Early Warning IKU/IKT')
@section('page_title', 'Validasi Bukti IKU/IKT')
@section('page_subtitle', 'Tinjau dan validasi berkas bukti IKU/IKT yang diunggah oleh Dosen')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Filters Card -->
    <div class="card" style="display: flex; flex-direction: column; gap: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div>
                <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary);">Filter Bukti IKU/IKT</h3>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">Saring pengajuan bukti IKU/IKT berdasarkan program studi, status validasi, atau tahun akademik.</p>
            </div>
        </div>

        <form action="{{ route('adminp2mp.validasi') }}" method="GET" class="filter-row-custom">
            <!-- Prodi Filter -->
            <div class="filter-item-custom">
                <label for="prodi_id" class="form-label-custom">Program Studi</label>
                <select name="prodi_id" id="prodi_id" onchange="this.form.submit()" class="form-select-custom">
                    <option value="">Semua Program Studi</option>
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}" {{ $prodiId == $p->id ? 'selected' : '' }}>{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="filter-item-custom">
                <label for="status" class="form-label-custom">Status Validasi</label>
                <select name="status" id="status" onchange="this.form.submit()" class="form-select-custom">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Awaiting Validasi</option>
                    <option value="valid" {{ $status === 'valid' ? 'selected' : '' }}>Valid</option>
                    <option value="invalid" {{ $status === 'invalid' ? 'selected' : '' }}>Perlu Perbaikan</option>
                </select>
            </div>

            <!-- Tahun Filter -->
            <div class="filter-item-custom">
                <label for="tahun" class="form-label-custom">Tahun Akademik</label>
                <select name="tahun" id="tahun" onchange="this.form.submit()" class="form-select-custom">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $y)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endforeach
                </select>
            </div>
            
            @if($prodiId || $status || $tahun)
                <div style="width: 100%; display: flex; justify-content: flex-end; margin-top: -8px;">
                    <a href="{{ route('adminp2mp.validasi') }}" style="font-size: 0.75rem; font-weight: 600; color: #f43f5e; text-decoration: none;">
                        Hapus Filter
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Table Card -->
    <div class="card" style="padding: 0; overflow: hidden;">
        @php
            $pendingCount = $riwayat->where('status', 'pending')->count();
        @endphp
        
        @if($pendingCount > 0)
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--border); background-color: rgba(56, 189, 248, 0.05); display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
                <div>
                    <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary); margin: 0; margin-bottom: 2px;">🚀 Setujui Semua Pending</h4>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0;">{{ $pendingCount }} bukti IKU/IKT menunggu validasi - setujui semua sekaligus</p>
                </div>
                <button type="button" onclick="showBulkApproveModal()" class="btn btn-primary" style="padding: 10px 20px; font-size: 0.8rem; background-color: #10b981; border-color: #10b981; white-space: nowrap;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Setujui Semua Sekarang
                </button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="min-width: 160px;">Pengunggah / Prodi</th>
                        <th style="min-width: 180px;">Indikator IKU/IKT</th>
                        <th style="min-width: 140px;">Jenis Bukti</th>
                        <th style="min-width: 150px;">Berkas Lampiran</th>
                        <th>Keterangan</th>
                        <th style="text-align: center; width: 80px;">Tahun</th>
                        <th style="text-align: center; min-width: 180px;">Status / Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $index => $item)
                        <tr>
                            <td>{{ $riwayat->firstItem() + $index }}</td>
                            
                            <!-- Dosen / Prodi -->
                            <td>
                                <div style="font-weight: 700; color: var(--text-primary);">{{ $item->user->name }}</div>
                                <div style="font-size: 0.72rem; color: var(--text-secondary); margin-top: 2px; font-weight: 600;">
                                    Role: {{ $item->user->role === 'kaprodi' ? 'Kaprodi' : 'Dosen' }}
                                </div>
                                <div style="font-size: 0.72rem; color: #38bdf8; margin-top: 2px;">
                                    {{ $item->user->prodi ? $item->user->prodi->nama_prodi : 'Umum / Tanpa Prodi' }}
                                </div>
                            </td>

                            <!-- Indikator IKU/IKT -->
                            <td>
                                <div style="font-weight: 600; color: var(--text-primary);">{{ $item->iku->nama_iku }}</div>
                                <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">Kategori: {{ $item->iku->kategori->nama_kategori }}</div>
                            </td>

                            <!-- Jenis Bukti -->
                            <td>
                                <span style="font-weight: 500; color: var(--text-secondary);">{{ $item->buktiIku->nama_bukti }}</span>
                            </td>

                            <!-- Berkas Lampiran -->
                            <td>
                                @if($item->files->isNotEmpty())
                                    <details>
                                        <summary style="font-size: 0.8rem; color: #38bdf8; cursor: pointer; user-select: none; display: inline-flex; align-items: center; gap: 6px; font-weight: 600; text-decoration: underline; outline: none;">
                                            <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"></path>
                                            </svg>
                                            Detail Berkas ({{ $item->files->count() }})
                                        </summary>
                                        <div style="display: flex; flex-direction: column; gap: 6px; margin-top: 8px; padding: 8px; background-color: var(--bg-surface2); border: 1px solid var(--border); border-radius: 6px; min-width: 180px;">
                                            @foreach($item->files as $file)
                                                <a href="{{ asset($file->file_bukti) }}" target="_blank" style="color: #10b981; text-decoration: underline; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px; word-break: break-all;">
                                                    <svg style="width: 12px; height: 12px; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                    {{ $file->nama_file }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </details>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.75rem;">Tidak ada berkas</span>
                                @endif
                            </td>

                            <!-- Keterangan -->
                            <td style="color: var(--text-secondary); font-size: 0.8rem; max-width: 250px; white-space: normal; word-wrap: break-word;">
                                <div style="display: flex; flex-direction: column; gap: 6px;">
                                    @forelse($item->files as $file)
                                        <div style="line-height: 1.4;">
                                            {!! preg_replace('~(https?://[^\s<]+)~i', '<a href="$1" target="_blank" style="color: #38bdf8; text-decoration: underline; word-break: break-all;">$1</a>', e($file->keterangan ?? '-')) !!}
                                        </div>
                                    @empty
                                        <span style="color: var(--text-muted);">-</span>
                                    @endforelse
                                </div>
                            </td>

                            <!-- Tahun -->
                            <td style="text-align: center; color: var(--text-secondary); font-size: 0.85rem;">
                                {{ $item->tahun }}
                            </td>

                            <!-- Status / Aksi -->
                            <td style="text-align: center;">
                                <!-- Current Status / Ubah validation toggle -->
                                <div id="status-display-{{ $item->id }}" style="{{ $item->status === 'pending' ? 'display: none;' : '' }}">
                                    @if($item->status === 'valid')
                                        <span class="badge-custom badge-green">Valid</span>
                                    @elseif($item->status === 'invalid')
                                        <span class="badge-custom badge-rose">Perlu Perbaikan</span>
                                    @else
                                        <span class="badge-custom badge-yellow">Awaiting Validasi</span>
                                    @endif
                                    
                                    @if($item->catatan_validator)
                                        <div style="font-size: 0.72rem; color: #fb7185; text-align: left; margin-top: 6px; padding: 6px; background-color: rgba(244,63,94,0.06); border-radius: 4px; border: 1px solid rgba(244,63,94,0.1); max-width: 200px; margin-left: auto; margin-right: auto; line-height: 1.3;">
                                            <strong>Catatan:</strong> {{ $item->catatan_validator }}
                                        </div>
                                    @endif

                                    @if($item->status !== 'pending')
                                        <div style="margin-top: 8px;">
                                            <button type="button" onclick="toggleValidationForm({{ $item->id }})" class="btn btn-secondary" style="padding: 4px 10px; font-size: 0.7rem; border-radius: 6px; width: 100%; justify-content: center;">
                                                Ubah Status
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Forms -->
                                <div id="status-form-{{ $item->id }}" style="{{ $item->status === 'pending' ? '' : 'display: none;' }}">
                                    <!-- Approve Action -->
                                    <form action="{{ route('adminp2mp.validasi.update', $item->id) }}" method="POST" style="display: block; margin-bottom: 6px; width: 100%;">
                                        @csrf
                                        <input type="hidden" name="status" value="valid">
                                        <button type="submit" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.75rem; width: 100%; justify-content: center; background-color: #10b981; border-color: #10b981;">
                                            Setujui (Valid)
                                        </button>
                                    </form>

                                    <!-- Reject Toggle -->
                                    <button type="button" onclick="showRejectInput({{ $item->id }})" id="btn-reject-trigger-{{ $item->id }}" class="btn btn-rose" style="padding: 6px 12px; font-size: 0.75rem; width: 100%; justify-content: center;">
                                        Minta Perbaikan
                                    </button>

                                    <!-- Reject Form -->
                                    <form id="form-reject-{{ $item->id }}" action="{{ route('adminp2mp.validasi.update', $item->id) }}" method="POST" style="display: none; margin-top: 8px;">
                                        @csrf
                                        <input type="hidden" name="status" value="invalid">
                                        <div style="display: flex; flex-direction: column; gap: 6px; text-align: left;">
                                            <label for="catatan-{{ $item->id }}" class="form-label-custom" style="font-size: 0.7rem; color: var(--text-muted);">Catatan Perbaikan</label>
                                            <textarea name="catatan_validator" id="catatan-{{ $item->id }}" class="form-input-custom" style="padding: 6px 8px; font-size: 0.75rem; height: 60px; resize: vertical; background-color: var(--bg-base);" placeholder="Alasan penolakan / revisi..." required>{{ $item->catatan_validator }}</textarea>
                                            <div style="display: flex; gap: 6px;">
                                                <button type="submit" class="btn btn-rose" style="padding: 4px 8px; font-size: 0.7rem; flex: 1; justify-content: center;">
                                                    Kirim
                                                </button>
                                                <button type="button" onclick="hideRejectInput({{ $item->id }})" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.7rem; flex: 1; justify-content: center;">
                                                    Batal
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Cancel Edit for previously validated entry -->
                                    @if($item->status !== 'pending')
                                        <button type="button" onclick="toggleValidationForm({{ $item->id }})" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.75rem; width: 100%; justify-content: center; margin-top: 6px;">
                                            Batal Ubah
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 48px; color: var(--text-muted);">
                                <svg style="width: 32px; height: 32px; margin: 0 auto 12px; color: var(--text-muted); display: block;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Belum ada pengajuan bukti IKU/IKT yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($riwayat->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border); background-color: rgba(15, 23, 42, 0.2);">
                {{ $riwayat->appends(['prodi_id' => $prodiId, 'status' => $status, 'tahun' => $tahun])->onEachSide(10)->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    //membuka/menutup form validasi sesuai ID data dan mereset input penolakan saat form ditutup.
    function toggleValidationForm(id) {
        const form = document.getElementById('status-form-' + id);
        const display = document.getElementById('status-display-' + id);
        if (form.style.display === 'none') {
            form.style.display = 'block';
            display.style.display = 'none';
        } else {
            form.style.display = 'none';
            display.style.display = 'block';
            hideRejectInput(id); // reset form penolakan
        }
    }

    //menampilkan form/input alasan penolakan
    function showRejectInput(id) {
        document.getElementById('form-reject-' + id).style.display = 'block';
        document.getElementById('btn-reject-trigger-' + id).style.display = 'none';
    }

    function hideRejectInput(id) {
        document.getElementById('form-reject-' + id).style.display = 'none';
        document.getElementById('btn-reject-trigger-' + id).style.display = 'block';
    }

    //mengumpulkan ID bukti yang menunggu validasi, mengecek apakah ada, 
    // lalu menampilkan modal untuk menyetujui semua bukti tersebut sekaligus.
    function showBulkApproveModal() {
        const pendingIds = [];
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const statusForm = row.querySelector('[id^="status-form-"]');
            if (statusForm && statusForm.style.display !== 'none') {
                const formId = statusForm.id.replace('status-form-', '');
                pendingIds.push(parseInt(formId));
            }
        });

        if (pendingIds.length === 0) {
            alert('Tidak ada bukti IKU/IKT yang menunggu validasi.');
            return;
        }

        document.getElementById('bulk-count').textContent = `${pendingIds.length} bukti siap disetujui`;
        document.getElementById('bulk-count-number').textContent = pendingIds.length;
        document.getElementById('bulk-ids').value = JSON.stringify(pendingIds);
        document.getElementById('bulk-approve-modal').style.display = 'flex';
    }

    function closeBulkApproveModal() {
        document.getElementById('bulk-approve-modal').style.display = 'none';
    }

    function submitBulkApprove() {
        const form = document.getElementById('bulk-approve-form');
        form.submit();
    }

    window.addEventListener('click', function(e) {
        const modal = document.getElementById('bulk-approve-modal');
        if (e.target === modal) {
            closeBulkApproveModal();
        }
    });
</script>

<!-- Bulk Approve Modal -->
<div id="bulk-approve-modal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-surface); border: 1px solid rgba(16, 185, 129, 0.4); box-shadow: 0 0 30px rgba(16, 185, 129, 0.15); border-radius: 12px; width: 100%; max-width: 420px; display: flex; flex-direction: column; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding: 20px 24px; background: var(--bg-surface);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(16, 185, 129, 0.15); display: flex; align-items: center; justify-content: center; color: #10b981;">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin: 0;">Setujui Semua Bukti IKU/IKT?</h3>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin: 2px 0 0 0;" id="bulk-count"></p>
                </div>
            </div>
            <button type="button" onclick="closeBulkApproveModal()" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; padding: 6px; border-radius: 6px; transition: all 0.2s;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div style="padding: 20px 24px; color: var(--text-secondary); font-size: 0.875rem; line-height: 1.6;">
            <div style="padding: 12px 16px; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.2); border-radius: 8px; margin-bottom: 16px; color: #38bdf8;">
                ⚠️ Tindakan ini akan menandai <strong>semua bukti IKU/IKT yang menunggu validasi</strong> sebagai <strong>Valid</strong>. Pastikan Anda sudah meriview semua bukti sebelum melanjutkan.
            </div>
            <p style="margin: 0 0 4px 0; color: var(--text-muted);">Jumlah bukti yang akan disetujui:</p>
            <p style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--text-primary);" id="bulk-count-number">-</p>
        </div>

        <div style="display: flex; gap: 10px; padding: 16px 24px; border-top: 1px solid var(--border); background: var(--bg-surface2);">
            <button type="button" onclick="closeBulkApproveModal()" class="btn btn-secondary" style="flex: 1; padding: 10px 16px; font-size: 0.8rem;">
                Batal
            </button>
            <button type="button" onclick="submitBulkApprove()" class="btn btn-primary" style="flex: 1; padding: 10px 16px; font-size: 0.8rem; background-color: #10b981; border-color: #10b981;">
                Ya, Setujui Semua
            </button>
        </div>
    </div>
</div>

<form id="bulk-approve-form" action="{{ route('adminp2mp.validasi.bulk-approve') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="ids" id="bulk-ids" value="">
</form>
@endsection

