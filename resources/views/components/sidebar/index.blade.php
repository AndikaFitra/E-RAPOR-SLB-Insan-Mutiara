<aside class="w-60 bg-[#555783] min-h-screen flex flex-col justify-between shrink-0 text-white select-none sticky top-0 h-screen overflow-y-auto">
    <div class="px-3.5 py-5 flex-1">
        <!-- Brand Header -->
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-1.5 mb-3 group">
            <img src="{{ route('school.logo') }}" alt="Logo SLB Insan Mutiara" class="w-9 h-9 object-contain shrink-0 rounded-full group-hover:scale-105 transition-transform" />
            <div class="leading-tight">
                <div class="text-[13px] font-semibold text-white tracking-tight">E-Rapor</div>
                <div class="text-[13px] font-bold text-white tracking-tight">SLB Insan Mutiara</div>
            </div>
        </a>

        <!-- Role Subtitle -->
        <div class="px-3 pt-2 pb-2 text-[12px] font-medium text-[#C8CAE2]">
            Admin
        </div>

        <!-- Menu Navigation -->
        <nav class="space-y-1">
            <!-- Beranda (Active if on dashboard) -->
            <x-sidebar.item href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3m10-11l-2-2m-2-2v14a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </x-slot:icon>
                Beranda
            </x-sidebar.item>

            <!-- Section: DATA AKADEMIK -->
            <div class="pt-4 pb-1 px-3 text-[10px] font-bold tracking-wider text-[#A4A7C9] uppercase">
                DATA AKADEMIK
            </div>

            <x-sidebar.item href="{{ route('admin.siswa.index') }}" :active="request()->routeIs('admin.siswa.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </x-slot:icon>
                Data Siswa
            </x-sidebar.item>

            <x-sidebar.item href="{{ route('admin.kelas.index') }}" :active="request()->routeIs('admin.kelas.*') || request()->routeIs('admin.penugasan.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/>
                        <rect x="14" y="14" width="7" height="7" rx="1"/>
                    </svg>
                </x-slot:icon>
                Data Kelas
            </x-sidebar.item>

            <x-sidebar.item href="{{ route('admin.tahun-ajaran.index') }}" :active="request()->routeIs('admin.tahun-ajaran.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="4" y="4" width="16" height="16" rx="2"/>
                        <circle cx="12" cy="12" r="2" fill="currentColor"/>
                    </svg>
                </x-slot:icon>
                Tahun Ajaran
            </x-sidebar.item>

            <!-- Section: MASTER DATA -->
            <div class="pt-4 pb-1 px-3 text-[10px] font-bold tracking-wider text-[#A4A7C9] uppercase">
                MASTER DATA
            </div>

            <x-sidebar.item href="{{ route('admin.guru.index') }}" :active="request()->routeIs('admin.guru.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </x-slot:icon>
                Data Guru
            </x-sidebar.item>

            <x-sidebar.item href="{{ route('admin.orang-tua.index') }}" :active="request()->routeIs('admin.orang-tua.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 00-3-3.87"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                </x-slot:icon>
                Orang Tua/Wali
            </x-sidebar.item>

            <x-sidebar.item href="{{ route('admin.mapel.index') }}" :active="request()->routeIs('admin.mapel.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </x-slot:icon>
                Mata Pelajaran
            </x-sidebar.item>

            <x-sidebar.item href="{{ route('admin.ekskul.index') }}" :active="request()->routeIs('admin.ekskul.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </x-slot:icon>
                Ekstrakurikuler
            </x-sidebar.item>

            <x-sidebar.item href="{{ route('admin.sekolah.index') }}" :active="request()->routeIs('admin.sekolah.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="4" y="4" width="16" height="16" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 9h6M9 13h6M9 17h6"/>
                    </svg>
                </x-slot:icon>
                Data Sekolah
            </x-sidebar.item>

            <x-sidebar.item href="{{ route('admin.akun.index') }}" :active="request()->routeIs('admin.akun.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </x-slot:icon>
                Akun & Hak Akses
            </x-sidebar.item>

            <x-sidebar.item href="{{ route('admin.arsip.index') }}" :active="request()->routeIs('admin.arsip.*')">
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
        <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form">
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
