@props([
    'periode' => '2026/2027 · Semester Ganjil',
    'status' => 'Aktif',
    'userName' => 'Admin Sekolah',
])

@php
$periode = \App\Models\TahunAjaran::getAktif()?->label ?? 'Belum ada periode aktif';
$userName = auth()->user()?->name ?? 'Pengguna';
$status = \App\Models\TahunAjaran::getAktif() ? 'Aktif' : null;
$back = \App\Support\BackNavigation::current();
@endphp
<header class="h-16 bg-white border-b border-[#E8E7ED] px-8 flex items-center justify-between sticky top-0 z-30">
    <button type="button" class="mobile-menu-button" aria-label="Buka menu" onclick="document.body.classList.toggle('menu-open')">Menu</button>
    <!-- Active Academic Period -->
    <div class="topbar-period flex min-w-0 items-center gap-2.5">
        @if($back)
            <a href="{{ $back['url'] }}" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-[#E8E7ED] text-[#555783] transition-colors hover:border-[#555783] hover:bg-[#F3F2F8] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#555783]" aria-label="Kembali ke {{ $back['label'] }}" title="Kembali ke {{ $back['label'] }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m7 7-7-7 7-7"/></svg>
            </a>
        @endif
        <span class="topbar-period-label text-[14px] font-semibold text-[#1A1A2E]">{{ $periode }}</span>
        @if($status)
            <span class="topbar-status inline-flex items-center gap-1.5 text-[12px] font-medium text-[#16A34A]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#16A34A]"></span>
                {{ $status }}
            </span>
        @endif
    </div>

    <!-- User Profile Dropdown -->
    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
        <button type="button"
                @click="open = !open"
                aria-label="Buka menu akun"
                class="flex items-center gap-1.5 cursor-pointer select-none text-[14px] font-medium text-[#1A1A2E] hover:text-[#555783] transition-colors focus:outline-none">
            <svg class="topbar-user-icon h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20a7.5 7.5 0 0115 0"/></svg>
            <span class="topbar-user-name">{{ $userName }}</span>
            <svg class="w-4 h-4 text-gray-500 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div x-show="open"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-[#E8E7ED] py-1.5 z-40"
             style="display: none;">
            
            <div class="px-4 py-2 border-b border-gray-100">
                <p class="text-xs font-semibold text-[#1A1A2E]">{{ auth()->user()->name ?? $userName }}</p>
                <p class="text-[11px] text-gray-400 truncate">{{ auth()->user()->email ?? 'admin@slbinsanmutiara.sch.id' }}</p>
            </div>

            @if(auth()->user()?->role === 'admin')
            <a href="{{ route('admin.akun.index') }}"
               class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 hover:text-[#555783] transition-colors">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Kelola Akun & Profil
            </a>

            <a href="{{ route('admin.sekolah.index') }}"
               class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 hover:text-[#555783] transition-colors">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Profil Sekolah
            </a>

            <div class="my-1 border-t border-gray-100"></div>

            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left flex items-center gap-2 px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 transition-colors">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar dari Sistem
                </button>
            </form>
        </div>
    </div>
</header>
