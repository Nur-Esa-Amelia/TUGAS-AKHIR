@extends('layouts.auth')

@section('title', 'Lupa Password - Sistem Early Warning IKU/IKT')

@section('container-class', 'max-w-[480px]')

@section('content')
<style>
    .auth-card-forgot {
        background-color: var(--auth-surface);
        border: 1px solid var(--auth-border);
        border-radius: 24px;
        padding: 36px 32px;
        box-shadow: 0 20px 25px -5px var(--auth-card-shadow), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
        width: 100%;
        position: relative;
    }

    html[data-theme="light"] .auth-card-forgot {
        background-color: #ffffff !important;
        border-color: #cbd5e1 !important;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .input-icon-left {
        position: absolute;
        left: 16px;
        color: var(--auth-text-muted);
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-input-with-icon {
        width: 100%;
        background-color: var(--auth-input-bg);
        border: 1px solid var(--auth-input-border);
        border-radius: 12px;
        padding: 12px 16px 12px 48px;
        font-size: 0.95rem;
        color: var(--auth-text);
        transition: all 0.2s ease;
        outline: none;
    }

    html[data-theme="light"] .form-input-with-icon {
        background-color: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    .form-input-with-icon:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 10px rgba(56, 189, 248, 0.15);
    }

    .btn-submit-forgot {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        font-size: 0.95rem;
        font-weight: 600;
        padding: 14px 20px;
        border-radius: 12px;
        cursor: pointer;
        background-color: #38bdf8;
        color: #0b0f19;
        border: none;
        transition: all 0.2s ease;
        margin-top: 8px;
    }
    
    .btn-submit-forgot:hover {
        background-color: #7dd3fc;
        box-shadow: 0 0 20px rgba(56, 189, 248, 0.4);
        transform: translateY(-2px);
    }

    .alert-banner {
        padding: 14px 16px;
        border-radius: 12px;
        font-size: 0.875rem;
        margin-bottom: 20px;
        line-height: 1.5;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .alert-success {
        background-color: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #10b981;
    }

    .alert-error {
        background-color: rgba(244, 63, 94, 0.12);
        border: 1px solid rgba(244, 63, 94, 0.3);
        color: #f43f5e;
    }
</style>

<div class="auth-card-forgot">
    <!-- Header: Kembali & Logo -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; width: 100%;">
        <!-- Arrow Back Button -->
        <a href="{{ route('login') }}" class="text-slate-500 hover:text-slate-800 transition cursor-pointer flex items-center gap-1.5 text-sm" style="text-decoration: none;">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Login</span>
        </a>

        <!-- Logo -->
        <div>
            <img src="{{ asset('images/LOGO POLTEKKKKK.jpg') }}" alt="Logo Politeknik Sukabumi" style="height: 40px; width: auto; object-fit: contain;">
        </div>
    </div>

    <!-- Title & Subtitle -->
    <div>
        <h2 class="auth-title" style="text-align: left; margin-bottom: 8px; font-size: 1.5rem;">Lupa Password?</h2>
        <p class="auth-subtitle" style="text-align: left; margin-bottom: 24px; font-size: 0.875rem;">
            Masukkan alamat email yang terdaftar pada akun Anda. Kami akan mengirimkan tautan untuk menyetel ulang kata sandi Anda.
        </p>
    </div>

    <!-- Alert Notifications -->
    @if (session('status'))
        <div class="alert-banner alert-success">
            <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>{{ session('status') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-banner alert-error">
            <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <!-- Forgot Password Form -->
    <form action="{{ route('password.email') }}" method="POST">
        @csrf

        <div style="display: flex; flex-direction: column; gap: 16px;">
            <!-- Email Input -->
            <div class="form-group" style="margin-bottom: 0;">
                <label for="email" class="form-label">Email Terdaftar <span style="color: #f43f5e;">*</span></label>
                <div class="input-wrapper">
                    <div class="input-icon-left">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                    </div>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="contoh: user@polteksmi.ac.id" required autofocus class="form-input-with-icon">
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit-forgot">
                <span>Kirim Link Reset Password</span>
                <svg style="width: 16px; height: 16px; display: inline-block; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </div>
    </form>
</div>
@endsection
