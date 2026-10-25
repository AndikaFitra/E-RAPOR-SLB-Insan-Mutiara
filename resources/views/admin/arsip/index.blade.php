<x-layouts.admin
    title="Admin · Arsip Rapor"
    periode="2026/2027 · Semester Ganjil"
    status="Aktif"
    userName="Admin Sekolah">

    <!-- Page Header -->
    <x-page-header
        breadcrumb="Admin / Arsip"
        title="Arsip Rapor & Leger Historis"
        subtitle="Dokumentasi hasil belajar dan buku induk siswa semester terdahulu yang terkunci permanen.">
    </x-page-header>

    <!-- Table of Archive -->
    <x-card class="mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F7F6F2] text-[#1A1A2E] text-[13px] font-semibold">
                        <th class="py-3 px-4 rounded-l-lg font-semibold">Tahun ajaran</th>
                        <th class="py-3 px-4 font-semibold">Semester</th>
                        <th class="py-3 px-4 font-semibold">Kelas rombel</th>
                        <th class="py-3 px-4 font-semibold">Wali kelas saat itu</th>
                        <th class="py-3 px-4 font-semibold">Total siswa</th>
                        <th class="py-3 px-4 font-semibold">Status arsip</th>
                        <th class="py-3 px-4 rounded-r-lg font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-[13px]">
                    @foreach($arsipList as $ar)
                        <tr class="hover:bg-gray-50/40 transition-colors">
                            <td class="py-4.5 px-4 font-semibold text-[#1A1A2E] whitespace-nowrap">
                                {{ $ar['tahun'] }}
                            </td>
                            <td class="py-4.5 px-4 text-gray-700 whitespace-nowrap">
                                {{ $ar['semester'] }}
                            </td>
                            <td class="py-4.5 px-4 font-medium text-[#555783] whitespace-nowrap">
                                {{ $ar['kelas'] }}
                            </td>
                            <td class="py-4.5 px-4 text-gray-600 whitespace-nowrap">
                                {{ $ar['wali_kelas'] }}
                            </td>
                            <td class="py-4.5 px-4 text-gray-600 whitespace-nowrap">
                                {{ $ar['total_siswa'] }} siswa
                            </td>
                            <td class="py-4.5 px-4 whitespace-nowrap font-medium text-gray-500">
                                {{ $ar['status'] }}
                            </td>
                            <td class="py-4.5 px-4 whitespace-nowrap text-gray-500">
                                <div class="flex items-center gap-1.5 text-[13px]">
                                    <a href="#" class="text-[#555783] hover:underline font-semibold transition-colors">Buka Buku Induk</a>
                                    <span class="text-gray-300">/</span>
                                    <a href="#" class="text-gray-700 hover:text-[#555783] font-medium transition-colors">Unduh Leger (XLSX)</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-pagination :total="3" :count="3" :perPage="10" />
    </x-card>

    <x-callout-note
        title="Histori Rapor Dilindungi Kebijakan Arsip Permanen"
        subtitle="Data nilai dan catatan deskriptif capaian pada periode arsip tidak dapat diubah lagi untuk menjamin keabsahan dokumen hasil belajar." />

</x-layouts.admin>

