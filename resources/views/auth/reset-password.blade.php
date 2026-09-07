@extends('layouts.auth')

@section('title', 'Atur Ulang Password - Sistem Early Warning IKU/IKT')

@section('container-class', 'max-w-[480px]')

@section('content')
<style>
    .auth-card-reset {
        background-color: var(--auth-surface);
        border: 1px solid var(--auth-border);
        border-radius: 24px;
        padding: 36px 32px;
        box-shadow: 0 20px 25px -5px var(--auth-card-shadow), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
        width: 100%;
        position: relative;
    }

    html[data-theme="light"] .auth-card-reset {
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

    .input-icon-right {
        position: absolute;
        right: 16px;
        color: var(--auth-text-muted);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        background: none;
        border: none;
        outline: none;
        padding: 0;
        transition: color 0.2s ease;
    }

    .input-icon-right:hover {
        color: #38bdf8;
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

    .form-input-with-icon-both {
        width: 100%;
        background-color: var(--auth-input-bg);
        border: 1px solid var(--auth-input-border);
        border-radius: 12px;
        padding: 12px 44px 12px 48px;
        font-size: 0.95rem;
        color: var(--auth-text);
        transition: all 0.2s ease;
        outline: none;
    }

    html[data-theme="light"] .form-input-with-icon,
    html[data-theme="light"] .form-input-with-icon-both {
        background-color: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    .form-input-with-icon:focus,
    .form-input-with-icon-both:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 10px rgba(56, 189, 248, 0.15);
    }

    .btn-submit-reset {
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
    
    .btn-submit-reset:hover {
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

    .alert-error {
        background-color: rgba(244, 63, 94, 0.12);
        border: 1px solid rgba(244, 63, 94, 0.3);
        color: #f43f5e;
    }

    .hidden {
        display: none !important;
    }
</style>

<div class="auth-card-reset">
    <!-- Header Logo -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; width: 100%;">
        <a href="{{ route('login') }}" class="text-slate-500 hover:text-slate-800 transition cursor-pointer flex items-center gap-1.5 text-sm" style="text-decoration: none;">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Login</span>
        </a>

        <div>
            <img src="{{ asset('images/LOGO POLTEKKKKK.jpg') }}" alt="Logo Politeknik Sukabumi" style="height: 40px; width: auto; object-fit: contain;">
        </div>
    </div>

    <!-- Title & Subtitle -->
    <div>
        <h2 class="auth-title" style="text-align: left; margin-bottom: 8px; font-size: 1.5rem;">Atur Ulang Password</h2>
        <p class="auth-subtitle" style="text-align: left; margin-bottom: 24px; font-size: 0.875rem;">
            Silakan masukkan kata sandi baru Anda di bawah ini.
        </p>
    </div>

    <!-- Error Alerts -->
    @if ($errors->any())
        <div class="alert-banner alert-error">
            <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <!-- Form Reset Password -->
    <form action="{{ route('password.update') }}" method="POST">
        @csrf

        <!-- Token Reset Password -->
        <input type="hidden" name="token" value="{{ $token }}">

        <div style="display: flex; flex-direction: column; gap: 16px;">
            <!-- Email (Terisi Otomatis) -->
            <div class="form-group" style="margin-bottom: 0;">
                <label for="email" class="form-label">Email Terdaftar <span style="color: #f43f5e;">*</span></label>
                <div class="input-wrapper">
                    <div class="input-icon-left">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                    </div>
                    <input type="email" name="email" id="email" value="{{ old('email', $email) }}" placeholder="Masukkan email Anda" required class="form-input-with-icon">
                </div>
            </div>

            <!-- Password Baru -->
            <div class="form-group" style="margin-bottom: 0;">
                <label for="password" class="form-label">Password Baru <span style="color: #f43f5e;">*</span></label>
                <div class="input-wrapper">
                    <div class="input-icon-left">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" required autofocus class="form-input-with-icon-both">
                    <button type="button" id="password-toggle" class="input-icon-right">
                        <svg id="eye-open" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg id="eye-closed" style="width: 20px; height: 20px;" class="hidden" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Konfirmasi Password Baru -->
            <div class="form-group" style="margin-bottom: 0;">
                <label for="password_confirmation" class="form-label">Konfirmasi Password Baru <span style="color: #f43f5e;">*</span></label>
                <div class="input-wrapper">
                    <div class="input-icon-left">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password baru" required class="form-input-with-icon-both">
                    <button type="button" id="password-confirm-toggle" class="input-icon-right">
                        <svg id="eye-open-confirm" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg id="eye-closed-confirm" style="width: 20px; height: 20px;" class="hidden" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit-reset">
                <span>Simpan Password Baru</span>
                <svg style="width: 16px; height: 16px; display: inline-block; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
         // Mengatur fitur tampil/sembunyikan password
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('password-toggle');
        const eyeOpenIcon = document.getElementById('eye-open');
        const eyeClosedIcon = document.getElementById('eye-closed');

        if (passwordInput && passwordToggle) {
             // Mengubah tampilan password saat tombol diklik
            passwordToggle.addEventListener('click', () => {
                if (passwordInput.type === 'password') {
                     // Menampilkan password
                    passwordInput.type = 'text';
                    eyeOpenIcon.classList.add('hidden');
                    eyeClosedIcon.classList.remove('hidden');
                } else {
                    // Menyembunyikan password kembali
                    passwordInput.type = 'password';
                    eyeOpenIcon.classList.remove('hidden');
                    eyeClosedIcon.classList.add('hidden');
                }
            });
        }

         // Mengatur fitur tampil/sembunyikan konfirmasi password
        const confirmInput = document.getElementById('password_confirmation');
        const confirmToggle = document.getElementById('password-confirm-toggle');
        const confirmOpenIcon = document.getElementById('eye-open-confirm');
        const confirmClosedIcon = document.getElementById('eye-closed-confirm');

        if (confirmInput && confirmToggle) {
            // Mengubah tampilan konfirmasi password saat tombol diklik
            confirmToggle.addEventListener('click', () => {
                if (confirmInput.type === 'password') {
                    // Menampilkan konfirmasi password
                    confirmInput.type = 'text';
                    confirmOpenIcon.classList.add('hidden');
                    confirmClosedIcon.classList.remove('hidden');
                } else {
                    // Menyembunyikan konfirmasi password kembali
                    confirmInput.type = 'password';
                    confirmOpenIcon.classList.remove('hidden');
                    confirmClosedIcon.classList.add('hidden');
                }
            });
        }
    });
</script>
@endsection
