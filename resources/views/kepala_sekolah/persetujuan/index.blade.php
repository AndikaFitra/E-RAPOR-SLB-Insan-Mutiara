<x-layouts.kepala-sekolah
    title="Kepala Sekolah · Persetujuan Rapor"
    periode="2026/2027 · Semester Ganjil"
    userName="Novia Parenri · Kepala Sekolah">

    <!-- Flash Alert -->
    @if(session('success'))
        <div class="mb-4">
            <x-alert-banner type="success" title="Persetujuan Berhasil" :message="session('success')" />
        </div>
    @elseif(session('info'))
        <div class="mb-4">
            <x-alert-banner type="info" title="Rapor Dikembalikan" :message="session('info')" />
        </div>
    @endif

    <!-- Page Header -->
    <x-page-header
        breadcrumb="Kepala Sekolah / Persetujuan Rapor"
        title="Persetujuan Rapor"
        subtitle="Menampilkan rapor lengkap yang sudah diajukan."
    />

    <!-- Filter Card -->
    <x-card class="p-5">
        <div class="space-y-4">
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
                        placeholder="Cari siswa atau wali kelas"
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-[#1A1A2E] placeholder-gray-400 focus:outline-none focus:border-[#555783] focus:ring-1 focus:ring-[#555783]"
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
                    <option>Tahun: 2026/2027</option>
                </select>
                <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option>Semester: Ganjil</option>
                </select>
                <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option>Kelas: Semua</option>
                </select>
                <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                    <option>Kekhususan: Semua</option>
                </select>
            </div>
        </div>
    </x-card>

    <!-- Data Table Card -->
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F7F6F2] border-b border-[#E8E7ED] text-[12px] font-semibold text-gray-600">
                        <th class="py-3.5 px-6">Siswa</th>
                        <th class="py-3.5 px-4">Kelas / Tingkat / Fase</th>
                        <th class="py-3.5 px-4">Wali kelas</th>
                        <th class="py-3.5 px-4">Diajukan</th>
                        <th class="py-3.5 px-4">Kelengkapan</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @foreach($antrean as $item)
                        <tr class="hover:bg-[#FBFBFA] transition-colors">
                            <td class="py-4 px-6 font-bold text-[#1A1A2E]">
                                {{ $item['nama_siswa'] }}
                            </td>
                            <td class="py-4 px-4 text-xs text-gray-600">
                                {{ $item['kelas'] }} · {{ $item['fase'] }}
                            </td>
                            <td class="py-4 px-4 text-xs text-gray-600">
                                {{ $item['wali_kelas'] }}
                            </td>
                            <td class="py-4 px-4 text-xs text-gray-500">
                                {{ $item['tanggal'] }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-xs font-semibold text-[#16A34A]">
                                    Lengkap
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-xs font-medium text-gray-600">
                                    {{ $item['status'] }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a
                                    href="{{ route('kepala.persetujuan.show', ['id' => $item['id']]) }}"
                                    class="text-xs font-semibold text-[#555783] hover:underline"
                                >
                                    Review Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <x-pagination current="1" total="1" totalItems="3" perPage="10" />
    </x-card>

    <!-- Callout Note -->
    <x-callout-note
        title="Persetujuan menghasilkan dokumen final"
        description="Rapor yang disetujui dikunci dan PDF siap dibagikan. Untuk perbaikan, pilih Kembalikan dan isi catatan revisi."
    />

</x-layouts.kepala-sekolah>

