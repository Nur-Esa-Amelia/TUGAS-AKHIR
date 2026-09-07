@extends('layouts.auth')

@section('title', 'Login ke Sistem - Sistem Early Warning IKU/IKT')

@section('container-class', 'max-w-[1000px]')

@section('content')
<style>
    .hidden {
        display: none !important;
    }

    .auth-split-container {
        display: grid;
        grid-template-columns: 1fr;
        background-color: var(--auth-surface);
        border: 1px solid var(--auth-border);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.18), 0 10px 10px -5px rgba(15, 23, 42, 0.12);
        width: 100%;
    }

    @media (min-width: 768px) {
        .auth-split-container {
            grid-template-columns: 1.15fr 1fr;
        }
    }

    .auth-left-panel {
        display: none;
        flex-direction: column;
        justify-content: space-between;
        padding: 40px;
        background: linear-gradient(135deg, #0080FF 100%);
        position: relative;
        overflow: hidden;
        border-right: 1px solid var(--auth-border);
    }

    @media (min-width: 768px) {
        .auth-left-panel {
            display: flex;
        }
    }

    /* Glow overlay effect */
    .auth-left-panel::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.12) 0%, rgba(0, 0, 0, 0) 70%);
        top: -100px;
        left: -100px;
        pointer-events: none;
    }

    /* Halaman Login khusus: tanpa tombol mode, tanpa scroll, panel form latar putih */
    #auth-theme-btn {
        display: none !important;
    }
    
    html, body {
        overflow: hidden !important;
        height: 100vh !important;
        max-height: 100vh !important;
        margin: 0 !important;
    }

    body {
        background-image: linear-gradient(rgba(11, 15, 25, 0.75), rgba(11, 15, 25, 0.75)), url('{{ asset("images/gambar2.jpeg") }}') !important;
        background-size: cover !important;
        background-position: center !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed !important;
    }

    .auth-right-panel {
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        background-color: #ffffff !important;
        color: #0f172a !important;
    }

    .auth-right-panel .auth-title {
        color: #0f172a !important;
    }

    .auth-right-panel .auth-subtitle {
        color: #475569 !important;
    }

    .auth-right-panel .form-label {
        color: #334155 !important;
    }

    .auth-right-panel .checkbox-label {
        color: #334155 !important;
    }

    .auth-right-panel .checkbox-input {
        background-color: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
    }

    .auth-right-panel .checkbox-input:checked {
        background-color: #38bdf8 !important;
        border-color: #38bdf8 !important;
    }

    .auth-right-panel .form-input-with-icon,
    .auth-right-panel .form-input-with-icon-both {
        background-color: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    .auth-right-panel .form-input-with-icon::placeholder,
    .auth-right-panel .form-input-with-icon-both::placeholder {
        color: #94a3b8 !important;
    }

    .auth-right-panel .input-icon-left,
    .auth-right-panel .input-icon-right {
        color: #64748b !important;
    }

    @media (max-width: 640px) {
        .auth-right-panel {
            padding: 32px 24px;
        }
    }

    .brand-title {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1.25;
        color: #ffffff;
        margin-bottom: 12px;
    }

    .brand-subtitle {
        font-size: 0.9rem;
        color: #ffffff;
        border-left: 3px solid rgba(255,255,255,0.8);
        padding-left: 12px;
        margin-bottom: 36px;
        line-height: 1.5;
    }

    .highlight-list {
        display: flex;
        flex-direction: column;
        gap: 24px;
        margin-bottom: 32px;
    }

    .highlight-item {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .highlight-icon {
        width: 40px;
        height: 40px;
        background-color: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        flex-shrink: 0;
    }

    .highlight-text {
        font-size: 0.9rem;
        font-weight: 500;
        color: #ffffff;
    }

    /* Input Field Styling with Icons */
    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .input-icon-left {
        position: absolute;
        left: 16px;
        color: #475569;
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .input-icon-right {
        position: absolute;
        right: 16px;
        color: #475569;
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

    .form-input-with-icon:focus, .form-input-with-icon-both:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 10px rgba(56, 189, 248, 0.15);
    }

    .btn-login-submit {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        font-size: 1rem;
        font-weight: 600;
        padding: 14px 20px;
        border-radius: 12px;
        cursor: pointer;
        background-color: #38bdf8;
        color: #0b0f19;
        border: none;
        transition: all 0.2s ease;
    }
    
    .btn-login-submit:hover {
        background-color: #7dd3fc;
        box-shadow: 0 0 20px rgba(56, 189, 248, 0.4);
        transform: translateY(-2px);
    }

    .btn-login-submit:active {
        transform: translateY(0);
    }

    .brand-footer-text {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.9);
    }
</style>

<div class="auth-split-container">
    <!-- Left Pane: Info & Branding (Desktop only) -->
    <div class="auth-left-panel">
        <div>
            <h1 class="brand-title">Sistem Monitoring IKU/IKT Politeknik Sukabumi</h1>
            <p class="brand-subtitle">Pemantauan & Evaluasi Pencapaian IKU/IKT</p>
        </div>

        <div class="highlight-list">
            <!-- Item 1 -->
            <div class="highlight-item">
                <div class="highlight-icon">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="highlight-text">Pemantauan Capaian IKU/IKT Real-time</span>
            </div>
            <!-- Item 2 -->
            <div class="highlight-item">
                <div class="highlight-icon">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <span class="highlight-text">Integrasi Data Capaian Program Studi</span>
            </div>
            <!-- Item 3 -->
            <div class="highlight-item">
                <div class="highlight-icon">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <span class="highlight-text">Peringatan Dini Kinerja IKU/IKT</span>
            </div>
            <!-- Item 4 -->
            <div class="highlight-item">
                <div class="highlight-icon">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <span class="highlight-text">Rekomendasi Otomatis Tindak Lanjut</span>
            </div>
        </div>

        <div class="brand-footer-text">
            Politeknik Sukabumi &copy; {{ date('Y') }}
        </div>
    </div>

    <!-- Right Pane: Login Form -->
    <div class="auth-right-panel">
        <!-- Header: Back & Logo -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; width: 100%;">
            <!-- Arrow Back Button -->
            <a href="{{ route('home') }}" class="text-slate-500 hover:text-slate-800 transition cursor-pointer flex items-center gap-1.5 text-sm" style="text-decoration: none;">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali</span>
            </a>

            <!-- Logo -->
            <div>
                <img src="{{ asset('images/LOGO POLTEKKKKK.jpg') }}" alt="Logo Politeknik Sukabumi" style="height: 40px; width: auto; object-fit: contain;">
            </div>
        </div>

        <div>
            <h2 class="auth-title" style="text-align: left; margin-bottom: 6px;">Selamat Datang</h2>
            <p class="auth-subtitle" style="text-align: left; margin-bottom: 28px;">Silakan masukkan akun anda untuk mengakses.</p>
        </div>

        <!-- Login Form -->
        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">Email <span style="color: #f43f5e;">*</span></label>
                    <div class="input-wrapper">
                        <div class="input-icon-left">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Masukkan email yang terdaftar" required class="form-input-with-icon">
                    </div>
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="password" class="form-label">Password<span style="color: #f43f5e;">*</span></label>
                    <div class="input-wrapper">
                        <div class="input-icon-left">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input type="password" name="password" id="password" placeholder="Masukkan Password" required class="form-input-with-icon-both">
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
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <div class="checkbox-container" style="margin-bottom: 0;">
                        <input id="remember" name="remember" type="checkbox" class="checkbox-input">
                        <label for="remember" class="checkbox-label" style="cursor: pointer;">
                            Ingat saya
                        </label>
                    </div>
                    <a href="{{ route('password.request') }}" class="text-sm transition" style="text-decoration: none; font-weight: 500; font-size: 0.85rem; color: #0284c7;">
                        Lupa Password?
                    </a>
                </div>


                <!-- Submit Button -->
                <button type="submit" class="btn-login-submit">
                    <span>Masuk</span>
                    <svg style="width: 16px; height: 16px; display: inline-block; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.documentElement.setAttribute('data-theme', 'dark');

         // Mengambil elemen input password dan tombol tampilkan password
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('password-toggle');
        const eyeOpenIcon = document.getElementById('eye-open');
        const eyeClosedIcon = document.getElementById('eye-closed');

        if (passwordInput && passwordToggle) {
             // Mengatur tampilan password saat tombol diklik
        passwordToggle.addEventListener('click', () => {
            passwordToggle.addEventListener('click', () => {
                if (passwordInput.type === 'password') {
                    // Menampilkan password dalam bentuk teks
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
    });
</script>
@endsection
