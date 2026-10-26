<x-layouts.admin
    title="Admin · Dashboard"
    periode="2026/2027 · Semester Ganjil"
    userName="Admin Sekolah">

    <!-- Breadcrumb & Page Heading -->
    <div class="space-y-1 mb-6">
        <div class="text-[13px] font-normal text-gray-500">
            Admin / Beranda
        </div>
        <h1 class="text-[28px] font-bold text-[#1A1A2E] tracking-tight leading-snug">
            Ringkasan administrasi
        </h1>
        <p class="text-[14px] text-gray-500 leading-normal">
            Pantau kesiapan semester dan lanjutkan pekerjaan yang membutuhkan perhatian.
        </p>
    </div>

    <!-- Hero Banner Card -->
    <x-banner-hero
        :tag="$tahunAktif?->label ?? 'Belum ada periode aktif'"
        :title="$tahunAktif ? 'Semester '.$tahunAktif->semester.', tersusun lebih terarah.' : 'Siapkan periode akademik sekolah'"
        description="Pantau kesiapan rapor semester ini. Arsip semester terdahulu tetap tersimpan."
        buttonText="Kelola data siswa"
        :buttonHref="route('admin.siswa.index')"
    />

    <!-- Row of 4 Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Stat 1: Siswa Aktif -->
        <x-stat-card
            title="Siswa aktif"
            :value="$stats['siswa_aktif']"
            :subtext="$stats['kelas_campuran']"
            :href="route('admin.siswa.index')">
            <x-slot:icon>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-[#555783] transition-colors" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 21v-2a4 4 0 00-3-3.87"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Stat 2: Guru Aktif -->
        <x-stat-card
            title="Guru aktif"
            :value="$stats['guru_aktif']"
            :subtext="$stats['wali_ditugaskan']"
            :href="route('admin.guru.index')">
            <x-slot:icon>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-[#555783] transition-colors" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                    <rect x="5" y="6" width="14" height="15" rx="2" ry="2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 3h4v3h-4z"/>
                    <circle cx="12" cy="11.5" r="2.5"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 18a3.5 3.5 0 017 0"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Stat 3: Rapor Disetujui -->
        <x-stat-card
            title="Rapor disetujui"
            :value="$stats['rapor_disetujui']"
            :subtext="$stats['persen_rapor']"
            :href="route('admin.arsip.index')">
            <x-slot:icon>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-[#555783] transition-colors" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 13.5l2 2 4-4"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Stat 4: Perlu Diperiksa -->
        <x-stat-card
            title="Perlu diperiksa"
            :value="$stats['perlu_diperiksa']"
            :subtext="$stats['status_periksa']"
            :href="route('admin.siswa.index', ['filter' => 'belum_lengkap'])">
            <x-slot:icon>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-[#555783] transition-colors" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    <!-- Two Columns: Action Needed (65%) & Quick Actions (35%) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <!-- Yang Perlu Ditindaklanjuti -->
        <x-card
            title="Yang perlu ditindaklanjuti"
            subtitle="Mulai dari pekerjaan yang paling membutuhkan perhatian."
            class="lg:col-span-2">
            <div class="divide-y divide-gray-100">
                @foreach($tindakLanjut as $item)
                    <x-action-item
                        :title="$item['title']"
                        :subtitle="$item['subtitle']"
                        :buttonText="$item['button_text']"
                        :buttonHref="$item['button_href']"
                    />
                @endforeach
            </div>
        </x-card>

        <!-- Aksi Cepat -->
        <x-card title="Aksi cepat">
            <div class="divide-y divide-gray-100">
                @foreach($aksiCepat as $item)
                    <x-quick-action-item
                        :title="$item['title']"
                        :subtitle="$item['subtitle']"
                        :href="$item['href']"
                    />
                @endforeach
            </div>
        </x-card>
    </div>

    <!-- Aktivitas Terbaru -->
    <x-card title="Aktivitas terbaru">
        <x-activity-table :activities="$aktivitasTerbaru" />
    </x-card>

</x-layouts.admin>
