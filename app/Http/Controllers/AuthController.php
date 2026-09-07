<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Prodi;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegisterForm()
    {
        $prodis = Prodi::all();
        return view('auth.register', compact('prodis'));
    }

    /**
     * Handle registration post request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'prodi_id' => ['required', 'exists:prodi,id'],
        ], [
            'prodi_id.required' => 'Silakan pilih Program Studi terlebih dahulu.',
            'prodi_id.exists' => 'Program Studi tidak valid.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'prodi_id' => $request->prodi_id,
            'role' => 'dosen', // tetapkan role secara mutlak menjadi 'dosen'
        ]);

        Auth::login($user);

        ActivityLog::log('Registrasi', 'Autentikasi', 'User baru berhasil mendaftar akun');

        return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil! Selamat datang di dashboard Anda.');
    }

    /**
     * Tampilkan form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses request login post.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            ActivityLog::log('Login', 'Autentikasi', 'User berhasil login ke sistem');

            return redirect()->intended(route('dashboard'))->with('success', 'Anda berhasil masuk.');
        }

        return back()->withErrors([
            'email' => 'Email atau Password yang Anda masukkan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        ActivityLog::log('Logout', 'Autentikasi', 'User keluar dari sistem');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah keluar dari sistem.');
    }

    /**
     * Tampilkan form Lupa Password.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Kirim email dengan link reset password.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Silakan masukkan alamat email Anda.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        // Cek apakah email terdaftar
        $userExists = User::where('email', $request->email)->exists();
        if (!$userExists) {
            return back()->withErrors(['email' => 'Kami tidak dapat menemukan akun dengan alamat email tersebut.'])->withInput();
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            ActivityLog::log('Lupa Password', 'Autentikasi', 'Permintaan link reset password dikirim ke email: ' . $request->email);
            return back()->with('status', 'Link reset password telah dikirim ke email Anda! Silakan cek kotak masuk atau folder spam Anda.');
        }

        return back()->withErrors(['email' => 'Gagal mengirim link reset password. Silakan coba beberapa saat lagi.'])->withInput();
    }

    /**
     * Tampilkan form Reset Password.
     */
    public function showResetPasswordForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with([
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Proses reset password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            ActivityLog::log('Reset Password', 'Autentikasi', 'User berhasil mereset kata sandi via token email');
            return redirect()->route('login')->with('success', 'Kata sandi Anda berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.');
        }

        return back()->withErrors(['email' => 'Token reset password tidak valid atau telah kadaluwarsa. Silakan ajukan ulang link reset password.'])->withInput();
    }
}

