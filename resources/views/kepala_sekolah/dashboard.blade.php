<x-layouts.kepala-sekolah
    title="Kepala Sekolah · Ringkasan Persetujuan Rapor"
    periode="2026/2027 · Semester Ganjil"
    userName="Novia Parenri · Kepala Sekolah">

    <!-- Breadcrumb & Page Heading -->
    <div class="space-y-1 mb-6">
        <div class="text-[13px] font-normal text-gray-500">
            Kepala Sekolah / Beranda
        </div>
        <h1 class="text-[28px] font-bold text-[#1A1A2E] tracking-tight leading-snug">
            Ringkasan persetujuan rapor
        </h1>
        <p class="text-[14px] text-gray-500 leading-normal">
            Pantau kesiapan semester dan lanjutkan pekerjaan yang membutuhkan perhatian.
        </p>
    </div>

    <!-- Hero Banner Card -->
    <x-banner-hero
        tag="TAHUN AJARAN 2026/2027"
        title="Semester Ganjil, tersusun lebih terarah."
        description="Pantau kesiapan rapor semester ini. Arsip semester terdahulu tetap tersimpan."
        buttonText="Tinjau 3 rapor"
        :buttonHref="route('kepala.persetujuan.index')"
    />

    <!-- Row of 4 Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Stat 1: Menunggu tinjauan -->
        <x-stat-card
            title="Menunggu tinjauan"
            :value="$stats['menunggu_tinjauan']"
            :subtext="$stats['subtext_tinjauan']"
            :href="route('kepala.persetujuan.index')">
            <x-slot:icon>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-[#555783] transition-colors" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 13.5l2 2 4-4"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Stat 2: Rapor disetujui -->
        <x-stat-card
            title="Rapor disetujui"
            :value="$stats['rapor_disetujui']"
            :subtext="$stats['subtext_disetujui']"
            :href="route('kepala.arsip.index')">
            <x-slot:icon>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-[#555783] transition-colors" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Stat 3: Dikembalikan -->
        <x-stat-card
            title="Dikembalikan"
            :value="$stats['dikembalikan']"
            :subtext="$stats['subtext_kembali']"
            :href="route('kepala.persetujuan.index', ['status' => 'dikembalikan'])">
            <x-slot:icon>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-[#555783] transition-colors" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Stat 4: Kelas lengkap -->
        <x-stat-card
            title="Kelas lengkap"
            :value="$stats['kelas_lengkap']"
            :subtext="$stats['subtext_kelas']"
            :href="route('kepala.persetujuan.index')">
            <x-slot:icon>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-[#555783] transition-colors" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    <!-- Two Columns: Antrean Tinjauan (65%) & Penyelesaian Per Kelas (35%) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <!-- Antrean Tinjauan -->
        <x-card
            title="Antrean tinjauan"
            subtitle="Mulai dari pekerjaan yang paling membutuhkan perhatian."
            class="lg:col-span-2">
            <div class="divide-y divide-gray-100">
                @foreach($antreanTinjauan as $item)
                    <div class="py-4 flex items-center justify-between gap-4 first:pt-1 last:pb-1">
                        <div class="space-y-0.5">
                            <h4 class="text-sm font-bold text-[#1A1A2E]">
                                {{ $item['nama_siswa'] }} · {{ $item['kelas'] }}
                            </h4>
                            <p class="text-xs text-gray-500">
                                {{ $item['wali_kelas'] }} • {{ $item['tanggal'] }}
                            </p>
                        </div>
                        <a
                            href="{{ route('kepala.persetujuan.show', ['id' => $item['id']]) }}"
                            class="shrink-0 px-4 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-[#1A1A2E] bg-white hover:bg-[#F7F6F2] hover:border-gray-300 transition-colors shadow-xs"
                        >
                            Review Rapor
                        </a>
                    </div>
                @endforeach
            </div>
        </x-card>

        <!-- Penyelesaian per kelas -->
        <x-card title="Penyelesaian per kelas">
            <div class="space-y-5 py-1">
                @foreach($penyelesaianKelas as $prog)
                    <x-progress-item
                        :title="$prog['kelas']"
                        :subtitle="$prog['sublabel']"
                        :percentage="$prog['percentage']"
                    />
                @endforeach
            </div>
        </x-card>
    </div>

    <!-- Aktivitas Terbaru -->
    <x-card title="Aktivitas terbaru">
        <x-activity-table :activities="$aktivitasTerbaru" />
    </x-card>

</x-layouts.kepala-sekolah>
