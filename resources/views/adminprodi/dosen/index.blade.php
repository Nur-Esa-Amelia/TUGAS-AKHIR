@extends('adminprodi.layouts.app')

@section('title', 'Dosen Program Studi - Admin Prodi')
@section('page_title', 'Data Dosen')
@section('page_subtitle', 'Melihat daftar dosen program studi ' . $prodiName . ' beserta tugas indikator IKU/IKT')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Filter Year Card -->
    <div class="card" style="display: flex; flex-direction: column; gap: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px;">
            <div>
                <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary);">Pilih Tahun Akademik</h3>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">Tampilkan tugas dosen berdasarkan tahun akademik yang dipilih.</p>
            </div>
            
            <form action="{{ route('adminprodi.dosen') }}" method="GET" style="display: flex; align-items: flex-end; gap: 12px; width: 220px;">
                <div class="filter-item-custom" style="width: 100%;">
                    <select id="tahun" name="tahun" class="form-select-custom" onchange="this.form.submit()">
                        @foreach($tahunList as $y)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Lecturers Table Card -->
    <div class="card" style="display: flex; flex-direction: column; gap: 20px;">
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Dosen</th>
                        <th style="width: 150px; text-align: center;">Total Tugas</th>
                        <th style="width: 200px;">Progress Validasi</th>
                        <th>Status Rincian</th>
                        <th style="width: 150px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dosenList as $index => $dosenItem)
                        <tr onclick="toggleDetail({{ $dosenItem->id }})" style="cursor: pointer;">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background-color: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); color: #3b82f6; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.95rem; flex-shrink: 0;">
                                        {{ substr($dosenItem->name, 0, 2) }}
                                    </div>
                                    <div style="min-width: 0;">
                                        <h4 style="font-size: 0.875rem; font-weight: 700; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0;">{{ $dosenItem->name }}</h4>
                                        <p style="font-size: 0.72rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 2px 0 0 0;">{{ $dosenItem->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-custom badge-blue" style="font-size: 0.7rem; padding: 4px 8px;">
                                    {{ $dosenItem->assignments->count() }} Tugas
                                </span>
                            </td>
                            <td>
                                @if($dosenItem->assignments->count() > 0)
                                    @php
                                        $total = $dosenItem->assignments->count();
                                        $valid = $dosenItem->assignments->where('proof_status', 'valid')->count();
                                        $percentage = round(($valid / $total) * 100);
                                    @endphp
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="flex: 1; min-width: 100px; height: 6px; background-color: var(--bg-surface2); border-radius: 3px; overflow: hidden;">
                                            <div style="width: {{ $percentage }}%; height: 100%; background-color: #10b981; border-radius: 3px;"></div>
                                        </div>
                                        <span style="font-size: 0.75rem; font-weight: 700; color: #10b981;">{{ $percentage }}%</span>
                                    </div>
                                @else
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">-</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                    @php
                                        $statusCounts = [
                                            'valid' => $dosenItem->assignments->where('proof_status', 'valid')->count(),
                                            'pending' => $dosenItem->assignments->where('proof_status', 'pending')->count(),
                                            'invalid' => $dosenItem->assignments->where('proof_status', 'invalid')->count(),
                                            'belum_isi' => $dosenItem->assignments->where('proof_status', 'belum_isi')->count(),
                                        ];
                                    @endphp
                                    
                                    @if($statusCounts['valid'] > 0)
                                        <span class="badge-custom badge-green" style="font-size: 0.625rem; padding: 2px 6px;">{{ $statusCounts['valid'] }} Valid</span>
                                    @endif
                                    
                                    @if($statusCounts['pending'] > 0)
                                        <span class="badge-custom badge-yellow" style="font-size: 0.625rem; padding: 2px 6px;">{{ $statusCounts['pending'] }} Pending</span>
                                    @endif
                                    
                                    @if($statusCounts['invalid'] > 0)
                                        <span class="badge-custom badge-rose" style="font-size: 0.625rem; padding: 2px 6px;">{{ $statusCounts['invalid'] }} Perbaikan</span>
                                    @endif
                                    
                                    @if($statusCounts['belum_isi'] > 0)
                                        <span class="badge-custom badge-gray" style="font-size: 0.625rem; padding: 2px 6px;">{{ $statusCounts['belum_isi'] }} Belum</span>
                                    @endif

                                    @if($dosenItem->assignments->count() == 0)
                                        <span style="font-size: 0.75rem; color: var(--text-muted);">Tidak ada tugas</span>
                                    @endif
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-secondary" style="padding: 6px 10px; font-size: 0.7rem; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                                    <span>Rincian</span>
                                    <svg id="chevron-{{ $dosenItem->id }}" style="width: 12px; height: 12px; transition: transform 0.2s;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr id="detail-row-{{ $dosenItem->id }}" style="display: none; background-color: var(--tr-hover-bg);">
                            <td colspan="6" style="padding: 20px; border-bottom: 1px solid var(--border);">
                                <div style="display: flex; flex-direction: column; gap: 16px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <h5 style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                                            <span style="display: inline-block; width: 4px; height: 14px; background-color: #3b82f6; border-radius: 2px;"></span>
                                            Daftar Rincian Tugas IKU/IKT: {{ $dosenItem->name }}
                                        </h5>
                                        <span style="font-size: 0.7rem; color: var(--text-muted);">Tahun Akademik: {{ $tahun }}</span>
                                    </div>
                                    
                                    @if($dosenItem->assignments->count() > 0)
                                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px;">
                                            @foreach($dosenItem->assignments as $assign)
                                                <div style="padding: 14px; background-color: var(--bg-surface); border: 1px solid var(--border); border-radius: 10px; display: flex; flex-direction: column; gap: 10px; justify-content: space-between; transition: all 0.2s;" class="assignment-item-hover">
                                                    <div>
                                                        <div style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary); line-height: 1.4; margin-bottom: 6px;">
                                                            {{ $assign->iku->nama_iku }}
                                                        </div>
                                                        <div style="font-size: 0.72rem; color: var(--text-muted);">
                                                            Kategori: {{ $assign->iku->kategori->nama_kategori }}
                                                        </div>
                                                    </div>
                                                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 8px; margin-top: 4px;">
                                                        <span style="font-size: 0.65rem; color: var(--text-faint);">ID: {{ $assign->iku->kode_iku ?? '-' }}</span>
                                                        @if($assign->proof_status === 'valid')
                                                            <span class="badge-custom badge-green" style="font-size: 0.65rem;">Valid</span>
                                                        @elseif($assign->proof_status === 'invalid')
                                                            <span class="badge-custom badge-rose" style="font-size: 0.65rem;">Minta Perbaikan</span>
                                                        @elseif($assign->proof_status === 'pending')
                                                            <span class="badge-custom badge-yellow" style="font-size: 0.65rem;">Awaiting Validasi</span>
                                                        @else
                                                            <span class="badge-custom badge-gray" style="font-size: 0.65rem;">Belum Unggah</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div style="text-align: center; color: var(--text-muted); padding: 24px 12px; font-size: 0.8rem; background-color: var(--bg-surface2); border: 1px dashed var(--border); border-radius: 8px;">
                                            Belum ada penugasan IKU/IKT untuk tahun {{ $tahun }}.
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 48px;">
                                <svg style="width: 36px; height: 36px; margin: 0 auto 12px; color: var(--text-faint); display: block;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Tidak ada data dosen yang terdaftar di program studi Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .assignment-item-hover:hover {
            border-color: rgba(59, 130, 246, 0.4) !important;
            background-color: var(--bg-surface2) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
    </style>

    <script>
        //buka/tutup detail suatu data pada tabel
        function toggleDetail(id) {
            const row = document.getElementById('detail-row-' + id);
            const chevron = document.getElementById('chevron-' + id);
            if (row.style.display === 'none') {
                row.style.display = 'table-row';
                chevron.style.transform = 'rotate(180deg)';
            } else {
                row.style.display = 'none';
                chevron.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</div>
@endsection
