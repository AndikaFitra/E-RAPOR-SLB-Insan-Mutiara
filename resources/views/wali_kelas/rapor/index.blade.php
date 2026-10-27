<x-layouts.wali-kelas
    title="Wali Kelas · Rapor Saya"
    periode="2026/2027 · Semester Ganjil"
    userName="Rina Pratiwi · Wali Kelas">

    <!-- Flash Alert -->
    @if(session('success'))
        <div class="mb-4">
            <x-alert-banner type="success" title="Operasi Berhasil" :message="session('success')" />
        </div>
    @endif

    <!-- Page Header -->
    <x-page-header
        breadcrumb="Wali Kelas / Rapor Saya"
        title="Rapor Saya"
        subtitle="Kelola penilaian dan pengajuan rapor ke Kepala Sekolah secara tepat waktu."
    />

    <!-- Filter Card -->
    <x-card class="p-5 space-y-4">
        <form method="GET" action="{{ route('wali.rapor.index') }}" class="space-y-4">
            <input type="hidden" name="status" value="{{ request('status') }}" />

            <div class="flex items-center justify-between gap-4">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
                        </svg>
                    </span>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama siswa atau NISN..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-[#1A1A2E] placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#555783] text-white text-xs font-semibold hover:bg-[#46486E] transition-colors shadow-xs">
                        Cari
                    </button>
                    @if(request('search') || request('kelas_id') || request('status'))
                        <a href="{{ route('wali.rapor.index') }}" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 bg-white hover:bg-[#F7F6F2] transition-colors flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset filter
                        </a>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
                <select name="kelas_id" onchange="this.form.submit()" class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ (string)request('kelas_id') === (string)$k->id ? 'selected' : '' }}>Kelas: {{ $k->nama }} ({{ $k->kekhususan }})</option>
                    @endforeach
                </select>
                <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]" disabled>
                    <option>Tahun: {{ $tahunAktif?->tahun ?? '2026/2027' }}</option>
                </select>
                <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]" disabled>
                    <option>Semester: {{ $tahunAktif?->semester ?? 'Ganjil' }}</option>
                </select>
                <select name="status" onchange="this.form.submit()" class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option value="">Status: Semua</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="dikembalikan" {{ request('status') === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                </select>
            </div>
        </form>
    </x-card>

    <!-- Status Badges Quick Bar -->
    <div class="flex items-center gap-2 text-xs flex-wrap">
        <a href="{{ route('wali.rapor.index', array_merge(request()->except('status'), ['status' => 'draft'])) }}"
           class="px-3 py-1 rounded-full border font-medium transition-colors {{ request('status') === 'draft' ? 'bg-amber-100 text-amber-900 border-amber-400 font-bold' : 'bg-amber-50 text-amber-800 border-amber-200 hover:bg-amber-100' }}">
            Draft · {{ $counts['draft'] }}
        </a>
        <a href="{{ route('wali.rapor.index', array_merge(request()->except('status'), ['status' => 'diajukan'])) }}"
           class="px-3 py-1 rounded-full border font-medium transition-colors {{ request('status') === 'diajukan' ? 'bg-blue-100 text-blue-900 border-blue-400 font-bold' : 'bg-blue-50 text-blue-800 border-blue-200 hover:bg-blue-100' }}">
            Diajukan · {{ $counts['diajukan'] }}
        </a>
        <a href="{{ route('wali.rapor.index', array_merge(request()->except('status'), ['status' => 'dikembalikan'])) }}"
           class="px-3 py-1 rounded-full border font-medium transition-colors {{ request('status') === 'dikembalikan' ? 'bg-rose-100 text-rose-900 border-rose-400 font-bold' : 'bg-rose-50 text-rose-800 border-rose-200 hover:bg-rose-100' }}">
            Dikembalikan · {{ $counts['dikembalikan'] }}
        </a>
        <a href="{{ route('wali.rapor.index', array_merge(request()->except('status'), ['status' => 'disetujui'])) }}"
           class="px-3 py-1 rounded-full border font-medium transition-colors {{ request('status') === 'disetujui' ? 'bg-emerald-100 text-emerald-900 border-emerald-400 font-bold' : 'bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100' }}">
            Disetujui · {{ $counts['disetujui'] }}
        </a>
        @if(request('status'))
            <a href="{{ route('wali.rapor.index', request()->except('status')) }}" class="text-xs text-gray-500 hover:text-gray-800 underline ml-2">
                Tampilkan Semua ({{ $counts['total'] }})
            </a>
        @endif
    </div>

    <!-- Data Table Card -->
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="dashboard-table dashboard-table--wide text-left">
                <thead>
                    <tr class="bg-[#F7F6F2] border-b border-[#E8E7ED] font-semibold text-gray-600">
                        <th class="py-3.5 px-6">Siswa</th>
                        <th class="py-3.5 px-4">Tingkat / Fase</th>
                        <th class="py-3.5 px-4">Akademik</th>
                        <th class="py-3.5 px-4">Non-akademik</th>
                        <th class="py-3.5 px-4">Status rapor</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($siswaList as $s)
                        @php
                            $penugasan = $s->penugasanAktif();
                            $rapor = $s->rapor->first();
                            $status = $rapor ? ucfirst($rapor->status) : 'Draft';
                            $statusLower = strtolower($status);
                            $aksi = match($statusLower) {
                                'disetujui' => 'Preview / Unduh PDF',
                                'dikembalikan' => 'Preview / Revisi',
                                'diajukan' => 'Preview / Ajukan',
                                default => 'Lengkapi & Ajukan',
                            };
                            $badgeColor = match($statusLower) {
                                'disetujui' => 'bg-emerald-50 text-[#16A34A]',
                                'dikembalikan' => 'bg-rose-50 text-[#DC2626]',
                                'diajukan' => 'bg-blue-50 text-[#2563EB]',
                                default => 'bg-amber-50 text-[#D97706]',
                            };
                        @endphp
                        <tr class="hover:bg-[#FBFBFA]">
                            <td class="py-4 px-6 font-bold text-[#1A1A2E]">
                                <div>{{ $s->nama_lengkap }}</div>
                                <div class="text-[11px] font-normal text-gray-400">NISN: {{ $s->nisn }} · {{ $s->kekhususan }}</div>
                            </td>
                            <td class="py-4 px-4 text-gray-600">
                                {{ $penugasan?->tingkat ?? 'VII' }} / {{ $penugasan?->fase ?? 'D' }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-medium text-[#16A34A]">Tuntas</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-medium text-[#16A34A]">Lengkap</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-semibold {{ $badgeColor }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('wali.rapor.show', $s->id) }}" class="font-semibold text-[#555783] hover:underline">
                                    {{ $aksi }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">
                                Tidak ada data rapor siswa yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :current="1" :total="1" :totalItems="count($siswaList)" :perPage="15" />
    </x-card>

</x-layouts.wali-kelas>
