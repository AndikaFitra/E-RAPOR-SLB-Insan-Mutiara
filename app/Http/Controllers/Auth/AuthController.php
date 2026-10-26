<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->getDashboardRoute());
        }

        $sekolah = Sekolah::instance();

        return view('auth.login', compact('sekolah'));
    }

    /**
     * Proses autentikasi pengguna.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $usernameInput = trim($request->input('username'));
        $passwordInput = $request->input('password');
        $remember      = $request->boolean('remember');

        // Cari pengguna berdasarkan email langsung atau mapping username
        $sanitized = str_replace(['.', '_', '-'], '', strtolower($usernameInput));
        $user = User::where('email', $usernameInput)
            ->orWhere('email', $sanitized . '@slbinsanmutiara.sch.id')
            ->first();

        // Validasi kredensial pengguna
        if (! $user || ! Hash::check($passwordInput, $user->password)) {
            return back()
                ->withInput($request->only('username', 'remember'))
                ->withErrors([
                    'credentials' => 'Periksa kembali penulisan username dan password, lalu coba lagi.',
                ]);
        }

        // Validasi status keaktifan akun
        if (! $user->is_aktif) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors([
                    'inactive' => 'Akun Anda tidak aktif. Hubungi administrator sekolah untuk mengaktifkan kembali akun.',
                ]);
        }

        // Lakukan autentikasi
        Auth::login($user, $remember);
        $request->session()->regenerate();

        ActivityLog::log('Login ke sistem', null, 'Sukses', $user);

        return redirect()->intended(route($user->getDashboardRoute()));
    }

    /**
     * Proses logout pengguna.
     */
    public function logout(Request $request): RedirectResponse
    {
        if ($user = Auth::user()) {
            ActivityLog::log('Logout dari sistem', null, 'Sukses', $user);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah berhasil keluar dari sistem.');
    }
}
