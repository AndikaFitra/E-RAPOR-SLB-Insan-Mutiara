<aside class="w-60 bg-[#555783] min-h-screen flex flex-col justify-between shrink-0 text-white select-none sticky top-0 h-screen overflow-y-auto">
    <div class="px-3.5 py-5 flex-1">
        <!-- Brand Header -->
        <a href="{{ route('kepala.dashboard') }}" class="flex items-center gap-3 px-1.5 mb-3 group">
            <img src="{{ asset('images/logo-slb.png') }}" alt="Logo SLB Insan Mutiara" class="w-9 h-9 object-contain shrink-0 rounded-full group-hover:scale-105 transition-transform" />
            <div class="leading-tight">
                <div class="text-[13px] font-semibold text-white tracking-tight">E-Rapor</div>
                <div class="text-[13px] font-bold text-white tracking-tight">SLB Insan Mutiara</div>
            </div>
        </a>

        <!-- Role Subtitle -->
        <div class="px-3 pt-2 pb-3 text-[12px] font-medium text-[#C8CAE2]">
            Kepala Sekolah
        </div>

        <!-- Menu Navigation -->
        <nav class="space-y-1">
            <!-- Beranda -->
            <x-sidebar.item href="{{ route('kepala.dashboard') }}" :active="request()->routeIs('kepala.dashboard')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3m10-11l-2-2m-2-2v14a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </x-slot:icon>
                Beranda
            </x-sidebar.item>

            <!-- Persetujuan Rapor -->
            <x-sidebar.item href="{{ route('kepala.persetujuan.index') }}" :active="request()->routeIs('kepala.persetujuan.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </x-slot:icon>
                Persetujuan Rapor
            </x-sidebar.item>

            <!-- Leger -->
            <x-sidebar.item href="{{ route('kepala.leger.index') }}" :active="request()->routeIs('kepala.leger.*')">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/>
                        <rect x="14" y="14" width="7" height="7" rx="1"/>
                    </svg>
                </x-slot:icon>
                Leger
            </x-sidebar.item>

            <!-- Arsip -->
            <x-sidebar.item href="{{ route('kepala.arsip.index') }}" :active="request()->routeIs('kepala.arsip.*')">
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
        <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form-kepala">
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

