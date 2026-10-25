<x-layouts.wali-kelas
    title="Wali Kelas · Arsip & Riwayat Perkembangan"
    periode="2026/2027 · Semester Ganjil"
    userName="Rina Pratiwi · Wali Kelas">

    <!-- Page Header -->
    <x-page-header
        breadcrumb="Wali Kelas / Arsip & Riwayat"
        title="Arsip & Riwayat Perkembangan"
        subtitle="Telusuri perkembangan siswa dan dokumen rapor pada periode sebelumnya."
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
                    placeholder="Cari siswa, NISN, atau tahun"
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

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-1">
            <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                <option>Tahun: Semua</option>
            </select>
            <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                <option>Semester: Semua</option>
            </select>
            <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                <option>Kelas: Semua</option>
            </select>
            <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                <option>Kekhususan: Semua</option>
            </select>
            <select class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                <option>Status siswa: Semua</option>
            </select>
        </div>
    </x-card>

    <!-- Data Table Card -->
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#F7F6F2] border-b border-[#E8E7ED] font-semibold text-gray-600">
                        <th class="py-3.5 px-6">Siswa</th>
                        <th class="py-3.5 px-4">Tahun / Semester</th>
                        <th class="py-3.5 px-4">Kelas saat itu</th>
                        <th class="py-3.5 px-4">Tingkat · Fase</th>
                        <th class="py-3.5 px-4">Status siswa</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $dataArsipWali = [
                            ['nama' => 'Alya Putri', 'periode' => '2025/2026 · Genap', 'kelas' => 'Tunarungu SD A', 'tingkat' => 'VI · C', 'status' => 'Aktif'],
                            ['nama' => 'Alya Putri', 'periode' => '2025/2026 · Ganjil', 'kelas' => 'Tunarungu SD A', 'tingkat' => 'VI · C', 'status' => 'Aktif'],
                            ['nama' => 'Bima Pratama', 'periode' => '2025/2026 · Genap', 'kelas' => 'Tunarungu A', 'tingkat' => 'VII · D', 'status' => 'Aktif'],
                            ['nama' => 'Friska Carolin Monalisa', 'periode' => '2025/2026 · Genap', 'kelas' => 'SMALB Tunarungu', 'tingkat' => 'XII · F', 'status' => 'Lulus'],
                            ['nama' => 'Reza Maulana', 'periode' => '2025/2026 · Genap', 'kelas' => 'Tunarungu B', 'tingkat' => 'X · E', 'status' => 'Pindah'],
                        ];
                    @endphp

                    @foreach($dataArsipWali as $row)
                        <tr class="hover:bg-[#FBFBFA]">
                            <td class="py-4 px-6 font-bold text-[#1A1A2E]">{{ $row['nama'] }}</td>
                            <td class="py-4 px-4 text-gray-600">{{ $row['periode'] }}</td>
                            <td class="py-4 px-4 text-gray-600">{{ $row['kelas'] }}</td>
                            <td class="py-4 px-4 text-gray-600">{{ $row['tingkat'] }}</td>
                            <td class="py-4 px-4">
                                <span class="font-semibold {{ $row['status'] === 'Aktif' ? 'text-[#16A34A]' : 'text-gray-600' }}">
                                    {{ $row['status'] }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('wali.rapor.show', 1) }}" class="font-semibold text-[#555783] hover:underline">
                                    Detail / Unduh PDF
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-pagination current="1" total="1" totalItems="5" perPage="10" />
    </x-card>

    <!-- Callout Note -->
    <x-callout-note
        title="Arsip menyimpan konteks pada setiap periode"
        description="Kelas, tingkat, fase, narasi, dan alamat pada dokumen lama tetap sesuai periode aslinya. Gunakan Detail Siswa untuk melihat linimasa perkembangan lintas semester."
    />

</x-layouts.wali-kelas>

