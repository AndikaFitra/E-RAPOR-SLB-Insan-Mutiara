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
        subtitle="Tunarungu A · 2026/2027 Ganjil · Rapor hanya dapat diajukan setelah lengkap."
    />

    <!-- Filter Card -->
    <x-card class="p-5 space-y-4">
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
                    placeholder="Cari nama siswa atau NIS"
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-[#1A1A2E] placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                />
            </div>
            <button type="button" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 bg-white hover:bg-[#F7F6F2] transition-colors flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Reset filter
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
            <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                <option>Kelas: Tunarungu A</option>
            </select>
            <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                <option>Tahun: 2026/2027</option>
            </select>
            <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                <option>Semester: Ganjil</option>
            </select>
            <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                <option>Status: Semua</option>
            </select>
        </div>
    </x-card>

    <!-- Status Badges Quick Bar -->
    <div class="flex items-center gap-2 text-xs">
        <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-medium">
            Draft · 2
        </span>
        <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-800 border border-blue-200 font-medium">
            Diajukan · 2
        </span>
        <span class="px-3 py-1 rounded-full bg-rose-50 text-rose-800 border border-rose-200 font-medium">
            Dikembalikan · 1
        </span>
        <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 font-medium">
            Disetujui · 1
        </span>
    </div>

    <!-- Data Table Card -->
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#F7F6F2] border-b border-[#E8E7ED] font-semibold text-gray-600">
                        <th class="py-3.5 px-6">Siswa</th>
                        <th class="py-3.5 px-4">Tingkat · Fase</th>
                        <th class="py-3.5 px-4">Akademik</th>
                        <th class="py-3.5 px-4">Non-akademik</th>
                        <th class="py-3.5 px-4">Status rapor</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $dataRapor = [];
                        foreach($siswaList as $s) {
                            $penugasan = $s->penugasanAktif();
                            $rapor = $s->rapor->first();
                            $status = $rapor ? ucfirst($rapor->status) : 'Draft';
                            $aksi = match(strtolower($status)) {
                                'disetujui' => 'Preview / Unduh PDF',
                                'dikembalikan' => 'Preview / Revisi',
                                'diajukan' => 'Preview / Cetak Rapor',
                                default => 'Preview / Periksa Kelengkapan',
                            };
                            $dataRapor[] = [
                                'id' => $s->id,
                                'nama' => $s->nama_lengkap,
                                'tingkat' => ($penugasan?->tingkat ?? 'VII') . ' · ' . ($penugasan?->fase ?? 'D'),
                                'akademik' => '13 / 13',
                                'nonakademik' => 'Lengkap',
                                'status' => $status,
                                'aksi' => $aksi,
                            ];
                        }
                    @endphp

                    @foreach($dataRapor as $row)
                        <tr class="hover:bg-[#FBFBFA]">
                            <td class="py-4 px-6 font-bold text-[#1A1A2E]">{{ $row['nama'] }}</td>
                            <td class="py-4 px-4 text-gray-600">{{ $row['tingkat'] }}</td>
                            <td class="py-4 px-4 text-gray-700">{{ $row['akademik'] }}</td>
                            <td class="py-4 px-4 font-semibold {{ $row['nonakademik'] === 'Lengkap' ? 'text-[#16A34A]' : 'text-[#D97706]' }}">
                                {{ $row['nonakademik'] }}
                            </td>
                            <td class="py-4 px-4 font-semibold">
                                @if($row['status'] === 'Disetujui')
                                    <span class="text-[#16A34A]">{{ $row['status'] }}</span>
                                @elseif($row['status'] === 'Dikembalikan')
                                    <span class="text-[#DC2626]">{{ $row['status'] }}</span>
                                @elseif($row['status'] === 'Diajukan')
                                    <span class="text-gray-700">{{ $row['status'] }}</span>
                                @else
                                    <span class="text-amber-700">{{ $row['status'] }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('wali.rapor.show', $row['id']) }}" class="font-semibold text-[#555783] hover:underline">
                                    {{ $row['aksi'] }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-pagination current="1" total="1" totalItems="6" perPage="10" />
    </x-card>

    <!-- Callout Note -->
    <x-callout-note
        title="Pengajuan menjaga data rapor tetap konsisten"
        description="Diajukan: penilaian terkunci hingga ditinjau. Dikembalikan: revisi sesuai catatan, lalu ajukan ulang. PDF dapat dibagikan setelah Disetujui."
    />

    <!-- Cari Rapor Semester Sebelumnya Card -->
    <x-card title="Cari rapor semester sebelumnya">
        <p class="text-xs text-gray-500 mb-4">
            Rapor Friska Carolin Monalisa tersedia di arsip 2025/2026 Genap, tingkat XII / F. Siswa telah Lulus dan bukan siswa aktif 2026/2027.
        </p>
        <a
            href="{{ route('wali.arsip.index') }}"
            class="inline-block px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 bg-white hover:bg-[#F7F6F2] transition-colors"
        >
            Buka arsip rapor
        </a>
    </x-card>

</x-layouts.wali-kelas>

