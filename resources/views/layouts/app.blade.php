<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'E-Rapor SLB Insan Mutiara') }}</title>

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .glass-dark {
            background: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(16px);
            border-right: 1px solid rgba(51, 65, 85, 0.6);
        }
    </style>
</head>
<body class="h-full text-slate-800 antialiased flex flex-col md:flex-row overflow-x-hidden">

    <!-- Sidebar -->
    <aside class="glass-dark text-slate-200 w-full md:w-64 md:min-h-screen flex flex-col justify-between shrink-0 shadow-xl z-20">
        <div>
            <!-- School Brand / Header -->
            <div class="px-6 py-5 border-b border-slate-800/80 flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-cyan-400 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 font-bold text-lg">
                    IM
                </div>
                <div>
                    <h1 class="text-sm font-bold text-white tracking-tight leading-none">SLB INSAN MUTIARA</h1>
                    <span class="text-[11px] font-medium text-cyan-400/90 tracking-wide uppercase">E-Rapor Digital</span>
                </div>
            </div>

            <!-- Role Badge & Active Year -->
            <div class="px-6 py-3 bg-slate-900/60 border-b border-slate-800/50 flex items-center justify-between text-xs">
                <span class="text-slate-400">Tahun Ajaran:</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ \App\Models\TahunAjaran::getAktif()?->tahun ?? '2026/2027' }} · {{ \App\Models\TahunAjaran::getAktif()?->semester ?? 'Ganjil' }}
                </span>
            </div>

            <!-- Navigation Links -->
            <nav class="px-4 py-4 space-y-1 text-sm font-medium">
                @php
                    $role = auth()->user()->role ?? 'guest';
                @endphp

                @if($role === 'admin')
                    <div class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Utama</div>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>

                    <div class="pt-3 px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Data Master</div>
                    <a href="#" class="flex items-center px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-all">
                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Data Guru
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-all">
                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Data Siswa
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-all">
                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Data Orang Tua
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-all">
                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Data Kelas
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-all">
                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        Mata Pelajaran
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-all">
                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Tahun Ajaran
                    </a>

                    <div class="pt-3 px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Penugasan & Akun</div>
                    <a href="#" class="flex items-center px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-all">
                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Penugasan Kelas
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-all">
                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        Akun & Hak Akses
                    </a>
                @elseif($role === 'kepala_sekolah')
                    <div class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Menu Kepala Sekolah</div>
                    <a href="{{ route('kepala.dashboard') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('kepala.dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Beranda
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-all">
                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Persetujuan Rapor
                        <span class="ml-auto inline-block py-0.5 px-1.5 text-[10px] font-bold rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">1</span>
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-all">
                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Leger Rekap Nilai
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-all">
                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        Arsip Rapor
                    </a>
                @elseif($role === 'wali_kelas')
                    <div class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Menu Wali Kelas</div>
                    <a href="{{ route('wali.dashboard') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('wali.dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Beranda
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-all">
                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        Nilai Asesmen
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-all">
                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Evaluasi Non-Akademik
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-all">
                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Rapor Saya
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-all">
                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Distribusi WhatsApp
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-all">
                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        Arsip
                    </a>
                @endif
            </nav>
        </div>

        <!-- User Profile Bottom Bar -->
        <div class="p-4 border-t border-slate-800/80 bg-slate-900/40">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-cyan-500 to-indigo-600 flex items-center justify-center text-white font-semibold text-xs shrink-0 shadow">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                        <p class="text-[11px] text-slate-400 capitalize">{{ str_replace('_', ' ', auth()->user()->role ?? '-') }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" id="btn-logout-sidebar" title="Keluar" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen overflow-x-hidden">
        <!-- Top Navbar -->
        <header class="glass-panel sticky top-0 z-10 px-6 py-4 shadow-sm flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <h2 class="text-lg font-bold text-slate-800 tracking-tight">@yield('page_title', 'Dashboard')</h2>
                <span class="hidden sm:inline-block text-xs text-slate-400">|</span>
                <span class="hidden sm:inline-block text-xs font-medium text-slate-500">SLB Insan Mutiara Pekanbaru</span>
            </div>

            <div class="flex items-center space-x-3">
                <!-- Role Switcher Quick Info / Demo Indicator -->
                <div class="flex items-center space-x-2 text-xs">
                    <span class="px-2.5 py-1 rounded-full font-medium {{ auth()->user()->role === 'admin' ? 'bg-purple-100 text-purple-700' : (auth()->user()->role === 'kepala_sekolah' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                        Role: {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                    </span>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-6 pt-4">
            @if(session('status'))
                <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-sm">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between shadow-sm">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Page Main Slot -->
        <main class="flex-1 px-6 py-4">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="px-6 py-4 border-t border-slate-200 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} E-Rapor SLB Insan Mutiara Pekanbaru · Sistem Penilaian Kurikulum Merdeka Khusus Disabilitas
        </footer>
    </div>

</body>
</html>
