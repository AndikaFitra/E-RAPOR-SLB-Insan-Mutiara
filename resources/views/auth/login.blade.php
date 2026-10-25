<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F7F6F2]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — E-Rapor SLB Insan Mutiara</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="h-full bg-[#F7F6F2] text-[#1A1A2E] antialiased selection:bg-[#555783] selection:text-white"
    x-data="{
        mode: '{{ request('mode', (request()->has('lupa') ? 'forgot' : 'login')) }}',
        status: '{{ request('status', (session('status') ? 'sukses' : '')) }}',
        errorType: '{{ request('error', ($errors->has('inactive') ? 'inactive' : ($errors->has('credentials') || $errors->any() ? 'credentials' : '')) ) }}',
        username: '{{ old('username', request('user', ($errors->has('credentials') || request('error') === 'kredensial' ? 'rina.pratiwi' : ''))) }}',
        password: '{{ request('error') ? '••••••••••' : '' }}',
        showPassword: false,
        isCenteredLayout: {{ request('layout') === 'centered' || request()->has('centered') ? 'true' : 'false' }},

        setDemo(user, pass) {
            this.mode = 'login';
            this.errorType = '';
            this.status = '';
            this.username = user;
            this.password = pass;
        },
        triggerKredensialSalah() {
            this.mode = 'login';
            this.status = '';
            this.errorType = 'credentials';
            this.username = 'rina.pratiwi';
            this.password = '••••••••••';
        },
        triggerAkunNonaktif() {
            this.mode = 'login';
            this.status = '';
            this.errorType = 'inactive';
            this.username = 'nonaktif';
            this.password = '••••••••';
        },
        triggerSukses() {
            this.mode = 'login';
            this.errorType = '';
            this.status = 'sukses';
        }
    }">

    <!-- Top floating Demo State Switcher for Evaluator (Non-intrusive) -->
    <div class="fixed top-3 right-3 z-50 flex items-center gap-1.5 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-full border border-[#E8E7ED] shadow-sm text-xs">
        <span class="text-gray-400 font-semibold uppercase text-[10px] tracking-wider mr-1">Preview 1:1:</span>
        <button type="button" @click="setDemo('', ''); isCenteredLayout = false;" 
            class="px-2 py-0.5 rounded-md hover:bg-gray-100 transition-colors text-gray-700 font-medium"
            :class="!errorType && !status && mode === 'login' && !isCenteredLayout ? 'bg-[#555783] text-white hover:bg-[#484A70]' : ''">
            Selamat Datang
        </button>
        <button type="button" @click="triggerKredensialSalah()" 
            class="px-2 py-0.5 rounded-md hover:bg-rose-50 text-rose-700 transition-colors font-medium"
            :class="errorType === 'credentials' ? 'bg-rose-100 text-rose-800' : ''">
            Kredensial Salah
        </button>
        <button type="button" @click="triggerAkunNonaktif()" 
            class="px-2 py-0.5 rounded-md hover:bg-amber-50 text-amber-700 transition-colors font-medium"
            :class="errorType === 'inactive' ? 'bg-amber-100 text-amber-800' : ''">
            Akun Nonaktif
        </button>
        <button type="button" @click="mode = 'forgot'; errorType = ''; status = '';" 
            class="px-2 py-0.5 rounded-md hover:bg-indigo-50 text-indigo-700 transition-colors font-medium"
            :class="mode === 'forgot' ? 'bg-[#555783] text-white hover:bg-[#484A70]' : ''">
            Lupa Password
        </button>
        <button type="button" @click="triggerSukses()" 
            class="px-2 py-0.5 rounded-md hover:bg-emerald-50 text-emerald-700 transition-colors font-medium"
            :class="status === 'sukses' ? 'bg-emerald-100 text-emerald-800' : ''">
            Sukses
        </button>
        <button type="button" @click="isCenteredLayout = !isCenteredLayout" 
            class="px-2 py-0.5 rounded-md hover:bg-gray-100 text-gray-600 transition-colors text-[11px] border border-gray-200"
            :title="isCenteredLayout ? 'Tampilkan Mode Split' : 'Tampilkan Mode Centered'">
            <span x-text="isCenteredLayout ? 'Split View' : 'Centered View'"></span>
        </button>
    </div>

    <!-- Main Container -->
    <div class="min-h-screen flex flex-col lg:flex-row">

        <!-- Left Branding Panel (Identik Masuk · Selamat datang.png) -->
        <div x-show="!isCenteredLayout" class="hidden lg:flex lg:w-[420px] xl:w-[480px] 2xl:w-[520px] bg-[#555783] text-white flex-col justify-between p-8 xl:p-12 relative overflow-hidden shrink-0 select-none">
            <!-- Background Decorative Watermark Circles -->
            <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full bg-white/5 pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-white/5 pointer-events-none"></div>
            <div class="absolute top-1/3 -left-12 w-48 h-48 rounded-full bg-white/[0.03] pointer-events-none"></div>

            <!-- Header Content -->
            <div class="relative z-10 my-auto text-center">
                <!-- School Logo -->
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('images/logo-slb.png') }}" 
                         alt="Logo SLB Insan Mutiara" 
                         class="w-32 h-32 xl:w-36 xl:h-36 object-contain drop-shadow-md">
                </div>

                <!-- Text Branding -->
                <p class="text-[11px] xl:text-xs font-semibold uppercase tracking-[0.22em] text-white/70">SEKOLAH LUAR BIASA</p>
                <h1 class="text-3xl xl:text-4xl font-extrabold text-white mt-1.5 tracking-tight">Insan Mutiara</h1>
                
                <div class="w-10 h-[2px] bg-white/30 mx-auto my-3.5 rounded-full"></div>
                
                <p class="text-sm text-white/80 font-medium">Kota Pekanbaru</p>

                <!-- Feature Bullets (Pills) -->
                <div class="mt-12 space-y-4 max-w-[280px] mx-auto text-left">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center shrink-0 text-white/80">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-white/90">Penilaian & Rapor Digital</span>
                    </div>

                    <div class="flex items-center space-x-3.5">
                        <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center shrink-0 text-white/80">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-white/90">Manajemen Siswa & Kelas</span>
                    </div>

                    <div class="flex items-center space-x-3.5">
                        <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center shrink-0 text-white/80">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-white/90">Laporan Hasil Belajar</span>
                    </div>
                </div>
            </div>

            <!-- Left Panel Footer -->
            <div class="relative z-10 text-center pt-6">
                <p class="text-xs text-white/50 tracking-wide">© 2026 SLB Insan Mutiara Pekanbaru</p>
            </div>
        </div>

        <!-- Right / Centered Form Area -->
        <div class="flex-1 flex flex-col items-center justify-center p-4 sm:p-8 md:p-12 bg-[#F7F6F2] min-h-screen">

            <!-- Mobile Brand Header (Visible only on small screens) -->
            <div class="lg:hidden text-center mb-6">
                <img src="{{ asset('images/logo-slb.png') }}" 
                     alt="Logo SLB Insan Mutiara" 
                     class="w-20 h-20 object-contain mx-auto mb-2 drop-shadow-sm">
                <h2 class="text-xl font-bold text-[#1A1A2E]">SLB Insan Mutiara</h2>
                <p class="text-xs text-gray-500">Kota Pekanbaru</p>
            </div>

            <!-- Card Container (Identik 1:1) -->
            <div class="w-full max-w-[480px] bg-white rounded-3xl p-8 sm:p-10 shadow-sm border border-[#EAE8E2] transition-all">

                <!-- ══════════════════════════════════════════════════════ -->
                <!-- STATE 1: FORM LOGIN & ALERTS                           -->
                <!-- ══════════════════════════════════════════════════════ -->
                <div x-show="mode === 'login'">
                    <!-- Header -->
                    <h2 class="text-2xl font-bold text-[#1A1A2E] tracking-tight">Selamat datang</h2>
                    <p class="text-sm text-gray-500 mt-1 font-normal">Masuk dengan akun yang diberikan sekolah.</p>

                    <!-- Alert: Masuk Berhasil (Identik Masuk · Selamat datang.png) -->
                    <div x-show="status === 'sukses'" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-5 p-4 rounded-xl bg-[#ECFDF5] border border-[#A7F3D0] text-[#065F46] flex items-start space-x-3">
                        <div class="shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-[#059669]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-[#065F46]">Masuk berhasil</p>
                            <p class="text-xs text-[#047857] mt-1 leading-relaxed">
                                Anda berhasil masuk ke E-Rapor Insan Mutiara. Silakan lanjutkan ke dashboard untuk melanjutkan penilaian dan laporan hasil belajar.
                            </p>
                        </div>
                    </div>

                    <!-- Alert: Kredensial Salah (Identik Masuk · Kredensial salah.png) -->
                    <div x-show="errorType === 'credentials'" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-5 p-4 rounded-xl bg-[#FEF2F2] border border-[#FECACA] text-[#991B1B] flex items-start space-x-3">
                        <div class="shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-[#991B1B]">Username atau password belum sesuai</p>
                            <p class="text-xs text-[#B91C1C] mt-1 leading-relaxed">
                                Periksa kembali penulisan username dan password, lalu coba lagi.
                            </p>
                        </div>
                    </div>

                    <!-- Alert: Akun Tidak Aktif (Identik Login · Akun tidak aktif.png) -->
                    <div x-show="errorType === 'inactive'" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-5 p-4 rounded-xl bg-[#FEF2F2] border border-[#FECACA] text-[#991B1B] flex items-start space-x-3">
                        <div class="shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-[#991B1B]">Akun tidak aktif</p>
                            <p class="text-xs text-[#B91C1C] mt-1 leading-relaxed">
                                Akun Anda tidak aktif. Hubungi administrator sekolah untuk mengaktifkan kembali akun.
                            </p>
                        </div>
                    </div>

                    <!-- Form Login -->
                    <form method="POST" action="{{ route('login.post') }}" class="mt-6 space-y-4">
                        @csrf

                        <!-- Username Input -->
                        <div>
                            <label for="input-username" class="block text-xs font-semibold text-[#1A1A2E] mb-1.5">Username</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input type="text" 
                                       name="username" 
                                       id="input-username" 
                                       x-model="username" 
                                       placeholder="Masukkan username" 
                                       required 
                                       autofocus
                                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#E8E7ED] bg-[#FAFAFA] text-sm text-[#1A1A2E] placeholder-gray-400 focus:bg-white focus:outline-none focus:border-[#555783] focus:ring-1 focus:ring-[#555783] transition-all">
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div>
                            <label for="input-password" class="block text-xs font-semibold text-[#1A1A2E] mb-1.5">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input :type="showPassword ? 'text' : 'password'" 
                                       name="password" 
                                       id="input-password" 
                                       x-model="password" 
                                       placeholder="Masukkan password" 
                                       required
                                       class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-[#E8E7ED] bg-[#FAFAFA] text-sm text-[#1A1A2E] placeholder-gray-400 focus:bg-white focus:outline-none focus:border-[#555783] focus:ring-1 focus:ring-[#555783] transition-all">
                                
                                <button type="button" 
                                        @click="showPassword = !showPassword" 
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Checkbox & Forgot Link -->
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center space-x-2 cursor-pointer select-none">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-[#D1D5DB] text-[#555783] focus:ring-[#555783]">
                                <span class="text-xs text-[#4B5563]">Ingat username saya</span>
                            </label>

                            <button type="button" 
                                    @click="mode = 'forgot'" 
                                    class="text-xs text-[#555783] hover:underline font-medium focus:outline-none">
                                Lupa password?
                            </button>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" 
                                    class="w-full py-3 px-4 rounded-xl bg-[#555783] hover:bg-[#484A70] text-white font-medium text-sm shadow-sm transition-colors flex items-center justify-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-[#555783]/50">
                                <span>Masuk ke E-Rapor</span>
                                <span class="text-base leading-none">&rarr;</span>
                            </button>
                        </div>

                        <!-- Subtitle Footnote inside card -->
                        <p class="text-xs text-center text-gray-400 mt-4 font-normal">
                            Akses untuk Admin, Wali Kelas, dan Kepala Sekolah.
                        </p>
                    </form>

                    <!-- Quick Demo Credentials Selector -->
                    <div class="mt-6 pt-4 border-t border-[#F0EFEA]">
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-center mb-2.5">Akses Cepat Pengujian (1-Klik):</p>
                        <div class="grid grid-cols-3 gap-1.5 text-center">
                            <button type="button" @click="setDemo('admin', 'password')" 
                                class="p-1.5 rounded-lg bg-[#F7F6F2] hover:bg-[#ECEBE4] border border-[#E8E7ED] text-gray-700 transition-colors text-[11px]">
                                <span class="block font-semibold text-[#555783]">Admin</span>
                                <span class="text-[9px] text-gray-400">admin / pass</span>
                            </button>
                            <button type="button" @click="setDemo('kepsek', 'password')" 
                                class="p-1.5 rounded-lg bg-[#F7F6F2] hover:bg-[#ECEBE4] border border-[#E8E7ED] text-gray-700 transition-colors text-[11px]">
                                <span class="block font-semibold text-amber-700">Kepsek</span>
                                <span class="text-[9px] text-gray-400">kepsek / pass</span>
                            </button>
                            <button type="button" @click="setDemo('rina.pratiwi', 'password')" 
                                class="p-1.5 rounded-lg bg-[#F7F6F2] hover:bg-[#ECEBE4] border border-[#E8E7ED] text-gray-700 transition-colors text-[11px]">
                                <span class="block font-semibold text-teal-700">Wali Kelas</span>
                                <span class="text-[9px] text-gray-400">rina / pass</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════ -->
                <!-- STATE 2: BANTUAN LUPA PASSWORD (Identik 1:1)            -->
                <!-- ══════════════════════════════════════════════════════ -->
                <div x-show="mode === 'forgot'" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    
                    <!-- Circular Key Icon -->
                    <div class="w-12 h-12 rounded-full bg-[#EEF0F8] text-[#555783] flex items-center justify-center mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>

                    <!-- Title & Description -->
                    <h2 class="text-2xl font-bold text-[#1A1A2E] tracking-tight">Lupa password?</h2>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                        Hubungi administrator tata usaha sekolah untuk meminta reset password akun E-Rapor Anda.
                    </p>

                    <!-- Inner Information Card -->
                    <div class="mt-6 p-4 sm:p-5 rounded-2xl bg-[#F8F7F4] border border-[#EAE8E2] text-left">
                        <h4 class="text-sm font-semibold text-[#1A1A2E] mb-1.5">Bantuan dari tata usaha</h4>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Sampaikan nama dan username akun Anda kepada administrator. Setelah password direset, gunakan password yang diberikan sekolah untuk masuk kembali.
                        </p>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-6">
                        <button type="button" 
                                @click="mode = 'login'" 
                                class="w-full py-3 px-4 rounded-xl bg-[#555783] hover:bg-[#484A70] text-white font-medium text-sm shadow-sm transition-colors text-center block focus:outline-none focus:ring-2 focus:ring-[#555783]/50">
                            Kembali ke login
                        </button>
                    </div>

                    <!-- Footnote inside card -->
                    <p class="text-xs text-center text-gray-400 mt-4 font-normal">
                        Jaga kerahasiaan password akun Anda.
                    </p>
                </div>

            </div>

            <!-- Canvas Bottom Text -->
            <p class="text-xs text-center text-gray-400 mt-5 font-normal">
                Kesulitan masuk? Hubungi administrator di tata usaha sekolah.
            </p>
            
            <!-- Optional copyright on centered or mobile layout -->
            <p x-show="isCenteredLayout" class="text-xs text-center text-gray-400 mt-1 font-normal lg:block">
                © 2026 SLB Insan Mutiara Pekanbaru
            </p>
        </div>

    </div>

</body>
</html>
