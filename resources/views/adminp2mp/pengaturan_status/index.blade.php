@extends('adminp2mp.layouts.app')

@section('title', 'Pengaturan Status Ketercapaian - Admin P2MP')
@section('page_title', 'Pengaturan Status Ketercapaian')
@section('page_subtitle', 'Tentukan threshold persentase ketercapaian target IKU/IKT secara dinamis')

@section('content')
<div class="card" style="max-width: 750px; margin: 0 auto; padding: 24px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid var(--border-color, #e2e8f0); padding-bottom: 14px;">
        <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-heading, #0f172a); margin: 0;">
            Parameter Threshold Status Ketercapaian Target
        </h3>
        <span class="badge-custom badge-blue" style="font-size: 0.8rem; padding: 4px 10px;">Dinamis</span>
    </div>

    @if(session('success'))
        <div style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; font-size: 0.9rem;">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width: 20px; height: 20px; flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @include('adminp2mp.pengaturan_status.modal_content')
</div>
@endsection
