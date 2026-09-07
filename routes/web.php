<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminSistem\DashboardController as AdminSistemDashboardController;
use App\Http\Controllers\AdminSistem\UserController as AdminSistemUserController;
use App\Http\Controllers\AdminSistem\ProdiController as AdminSistemProdiController;
use App\Http\Controllers\AdminSistem\ModelTokenAiController as AdminSistemModelTokenAiController;
use App\Http\Controllers\AdminP2mp\DashboardController as AdminP2mpDashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Http;

Route::get('/test-gemini', function () {

    $response = Http::post(
        'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key=' . env('GEMINI_API_KEY'),
        [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => 'Halo Gemini, siapa kamu?'
                        ]
                    ]
                ]
            ]
        ]
    );

    return $response->json();
});

// Rute Publik Penilaian Expert (TIDAK MEMERLUKAN LOGIN / Google Form Style)
Route::get('/evaluasi-expert', [\App\Http\Controllers\PenilaianExpertController::class, 'index'])->name('evaluasi-expert.index');
Route::get('/evaluasi-expert/rekomendasi/{id}', [\App\Http\Controllers\PenilaianExpertController::class, 'getRekomendasiData'])->name('evaluasi-expert.rekomendasi');
Route::post('/evaluasi-expert/store', [\App\Http\Controllers\PenilaianExpertController::class, 'store'])->name('evaluasi-expert.store');

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rute Guest (Pengunjung)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Rute Reset Password / Lupa Password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});


// Rute dengan Autentikasi
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->role === 'admin_sistem') {
            return redirect()->route('adminsistem.dashboard');
        }
        if ($user->role === 'admin_p2mp') {
            return redirect()->route('adminp2mp.dashboard');
        }
        if ($user->role === 'admin_prodi' || $user->role === 'kaprodi') {
            return redirect()->route('adminprodi.dashboard');
        }
        if ($user->role === 'dosen') {
            return redirect()->route('dosen.dashboard');
        }
        return view('dashboard');
    })->name('dashboard');

    // Rute Admin P2MP
    Route::middleware('role:admin_p2mp')->prefix('adminp2mp')->name('adminp2mp.')->group(function () {
        Route::get('/dashboard', [AdminP2mpDashboardController::class, 'index'])->name('dashboard');
        Route::get('/validasi', [AdminP2mpDashboardController::class, 'validasi'])->name('validasi');
        Route::post('/validasi/{id}', [AdminP2mpDashboardController::class, 'updateValidasi'])->name('validasi.update');
        Route::post('/validasi-bulk-approve', [\App\Http\Controllers\AdminP2mp\BulkApproveController::class, 'bulkApprove'])->name('validasi.bulk-approve');
        Route::get('/monitoring', [AdminP2mpDashboardController::class, 'monitoring'])->name('monitoring');
        Route::get('/monitoring/export-excel', [AdminP2mpDashboardController::class, 'exportExcel'])->name('monitoring.export-excel');
        Route::get('/hasil-evaluasi', [\App\Http\Controllers\AdminSistem\HasilEvaluasiController::class, 'index'])->name('hasil-evaluasi.index');
    });

    // Rute Admin Sistem
    Route::middleware('role:admin_sistem')->prefix('adminsistem')->name('adminsistem.')->group(function () {
        Route::get('/dashboard', [AdminSistemDashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', AdminSistemUserController::class);
        Route::resource('prodi', AdminSistemProdiController::class);
        Route::get('/model-ai', [AdminSistemModelTokenAiController::class, 'index'])->name('model_ai.index');
        Route::post('/model-ai/activate-all', [AdminSistemModelTokenAiController::class, 'activateAll'])->name('model_ai.activate_all');
        Route::post('/model-ai/destroy-all', [AdminSistemModelTokenAiController::class, 'destroyAll'])->name('model_ai.destroy_all');
        Route::post('/model-ai', [AdminSistemModelTokenAiController::class, 'store'])->name('model_ai.store');
        Route::put('/model-ai/{id}', [AdminSistemModelTokenAiController::class, 'update'])->name('model_ai.update');
        Route::delete('/model-ai/{id}', [AdminSistemModelTokenAiController::class, 'destroy'])->name('model_ai.destroy');
        Route::post('/model-ai/{id}/activate', [AdminSistemModelTokenAiController::class, 'activate'])->name('model_ai.activate');
        Route::get('/aktivitas', [\App\Http\Controllers\AdminSistem\ActivityLogController::class, 'index'])->name('aktivitas.index');
        Route::get('/hasil-evaluasi', [\App\Http\Controllers\AdminSistem\HasilEvaluasiController::class, 'index'])->name('hasil-evaluasi.index');
    });

    // Rute yang dapat diakses oleh Admin Prodi, Kaprodi & Admin P2MP
    Route::middleware('role:admin_prodi,kaprodi,admin_p2mp')->prefix('adminprodi')->name('adminprodi.')->group(function () {
        Route::get('/laporan', [\App\Http\Controllers\AdminProdi\LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export-excel', [\App\Http\Controllers\AdminProdi\LaporanController::class, 'exportExcel'])->name('laporan.export-excel');

        // Read-only access to Master Data
        Route::resource('kategori', \App\Http\Controllers\AdminProdi\KategoriController::class)->only(['index', 'show']);
        Route::resource('iku', \App\Http\Controllers\AdminProdi\IkuController::class)->only(['index', 'show']);
        Route::resource('bukti', \App\Http\Controllers\AdminProdi\BuktiIkuController::class)->only(['index', 'show']);
    });

    // Rute yang HANYA dapat diakses oleh Admin P2MP (Konfigurasi Utama & Akses Tulis)
    Route::middleware('role:admin_p2mp')->prefix('adminprodi')->name('adminprodi.')->group(function () {
        Route::get('/pengaturan', [\App\Http\Controllers\AdminProdi\PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::post('/pengaturan', [\App\Http\Controllers\AdminProdi\PengaturanController::class, 'store'])->name('pengaturan.store');

        Route::resource('kategori', \App\Http\Controllers\AdminProdi\KategoriController::class)->except(['index', 'show']);
        Route::resource('iku', \App\Http\Controllers\AdminProdi\IkuController::class)->except(['index', 'show']);
        Route::resource('bukti', \App\Http\Controllers\AdminProdi\BuktiIkuController::class)->except(['index', 'show']);
    });

    // Rute yang HANYA dapat diakses oleh Admin Prodi & Kaprodi
    Route::middleware('role:admin_prodi,kaprodi')->prefix('adminprodi')->name('adminprodi.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\AdminProdi\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('pencapaian', \App\Http\Controllers\AdminProdi\IkuPencapaianController::class);
        Route::resource('penugasan', \App\Http\Controllers\AdminProdi\PenugasanController::class);
        Route::get('/bukti-dosen', [\App\Http\Controllers\AdminProdi\DashboardController::class, 'buktiDosen'])->name('bukti-dosen');
        Route::get('/dosen', [\App\Http\Controllers\AdminProdi\DashboardController::class, 'dosen'])->name('dosen');

        // Rute Pengisian Bukti khusus Kaprodi
        Route::middleware('role:kaprodi')->group(function () {
            Route::get('/pengisian/create', [\App\Http\Controllers\AdminProdi\PengisianController::class, 'create'])->name('pengisian.create');
            Route::post('/pengisian', [\App\Http\Controllers\AdminProdi\PengisianController::class, 'store'])->name('pengisian.store');
            Route::get('/pengisian/{id}/edit', [\App\Http\Controllers\AdminProdi\PengisianController::class, 'edit'])->name('pengisian.edit');
            Route::put('/pengisian/{id}', [\App\Http\Controllers\AdminProdi\PengisianController::class, 'update'])->name('pengisian.update');
        });
    });

    // Rute Dosen
    Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Dosen\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/pengisian', [\App\Http\Controllers\Dosen\PengisianController::class, 'index'])->name('pengisian.index');
        Route::get('/pengisian/create', [\App\Http\Controllers\Dosen\PengisianController::class, 'create'])->name('pengisian.create');
        Route::post('/pengisian', [\App\Http\Controllers\Dosen\PengisianController::class, 'store'])->name('pengisian.store');
        Route::get('/pengisian/{id}/edit', [\App\Http\Controllers\Dosen\PengisianController::class, 'edit'])->name('pengisian.edit');
        Route::put('/pengisian/{id}', [\App\Http\Controllers\Dosen\PengisianController::class, 'update'])->name('pengisian.update');
        Route::get('/pencapaian', [\App\Http\Controllers\Dosen\DashboardController::class, 'pencapaian'])->name('pencapaian.index');
    });
    
    // AJAX Endpoint for Generating Recommendation
    Route::post('/rekomendasi/generate-ajax/{id}', [\App\Http\Controllers\RekomendasiAiController::class, 'generateAjax'])->name('rekomendasi.generate-ajax');
});

