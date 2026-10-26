<aside class="w-60 bg-[#555783] min-h-screen flex flex-col justify-between shrink-0 text-white select-none sticky top-0 h-screen overflow-y-auto">
    <div class="px-3.5 py-5 flex-1">
        <!-- Brand Header -->
        <a href="{{ route('wali.dashboard') }}" class="flex items-center gap-3 px-1.5 mb-3 group">
            <img src="{{ route('school.logo') }}" alt="Logo SLB Insan Mutiara" class="w-9 h-9 object-contain shrink-0 rounded-full group-hover:scale-105 transition-transform" />
            <div class="leading-tight">
                <div class="text-[13px] font-semibold text-white tracking-tight">E-Rapor</div>
                <div class="text-[13px] font-bold text-white tracking-tight">SLB Insan Mutiara</div>
            </div>
        </a>

        <!-- Role Subtitle -->
        <div class="px-3 pt-2 pb-3 text-[12px] font-medium text-[#C8CAE2]">
            Wali Kelas
        </div>

        <!-- Menu Navigation -->
        <nav class="space-y-1">
            <!-- Beranda -->
            <x-sidebar.item href="{{ route('wali.dashboard') }}" :active="request()->routeIs('wali.dashboard')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3m10-11l-2-2m-2-2v14a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </x-slot:icon>
                Beranda
            </x-sidebar.item>

            <!-- Nilai Asesmen -->
            <x-sidebar.item href="{{ route('wali.asesmen.index') }}" :active="request()->routeIs('wali.asesmen.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </x-slot:icon>
                Nilai Asesmen
            </x-sidebar.item>

            <!-- Evaluasi Nonakademik -->
            <x-sidebar.item href="{{ route('wali.nonakademik.index') }}" :active="request()->routeIs('wali.nonakademik.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </x-slot:icon>
                Evaluasi Nonakademik
            </x-sidebar.item>

            <!-- Rapor Saya -->
            <x-sidebar.item href="{{ route('wali.rapor.index') }}" :active="request()->routeIs('wali.rapor.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </x-slot:icon>
                Rapor Saya
            </x-sidebar.item>

            <!-- Distribusi -->
            <x-sidebar.item href="{{ route('wali.distribusi.index') }}" :active="request()->routeIs('wali.distribusi.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </x-slot:icon>
                Distribusi
            </x-sidebar.item>

            <!-- Arsip -->
            <x-sidebar.item href="{{ route('wali.arsip.index') }}" :active="request()->routeIs('wali.arsip.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="4" y="4" width="16" height="16" rx="2"/>
                        <circle cx="12" cy="12" r="2" fill="currentColor"/>
                    </svg>
                </x-slot:icon>
                Arsip
            </x-sidebar.item>
        </nav>
    </div>

    <!-- Bottom Action & Info -->
    <div class="px-3.5 pb-6 pt-2">
        <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form-wali">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-[#E3E4F0] hover:text-white hover:bg-white/10 transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Keluar</span>
            </button>
        </form>

        <div class="px-3.5 pt-4 text-[11px] text-[#C4C7DE] leading-relaxed">
            <div>Butuh bantuan?</div>
            <div>Hubungi administrator sekolah.</div>
        </div>
    </div>
</aside>

