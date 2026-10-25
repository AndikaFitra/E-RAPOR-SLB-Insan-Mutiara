<x-layouts.admin
    title="Admin · Akun & Hak Akses"
    periode="2026/2027 · Semester Ganjil"
    status="Aktif"
    userName="Admin Sekolah">

    <!-- Flash Alert -->
    @if(session('success'))
        <div class="mb-6">
            <x-alert-banner type="success" title="Operasi Berhasil" :message="session('success')" />
        </div>
    @endif

    <!-- Page Header -->
    <x-page-header
        breadcrumb="Admin / Akun & Hak Akses"
        title="Akun & Hak Akses"
        subtitle="Kelola kredensial login, peran pengguna (Admin, Kepala Sekolah, Wali Kelas), dan status aktivasi.">
        <x-slot:action>
            <a href="{{ route('admin.guru.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#555783] hover:bg-[#47496E] text-white text-[14px] font-semibold rounded-xl shadow-sm transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>Tambah Akun / Guru</span>
            </a>
        </x-slot:action>
    </x-page-header>

    <!-- Table of Users -->
    <x-card class="mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F7F6F2] text-[#1A1A2E] text-[13px] font-semibold">
                        <th class="py-3 px-4 rounded-l-lg font-semibold">Nama pengguna</th>
                        <th class="py-3 px-4 font-semibold">Username / Email</th>
                        <th class="py-3 px-4 font-semibold">Hak akses (Role)</th>
                        <th class="py-3 px-4 font-semibold">Login terakhir</th>
                        <th class="py-3 px-4 font-semibold">Status</th>
                        <th class="py-3 px-4 rounded-r-lg font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-[13px]">
                    @foreach($akunList as $u)
                        <tr class="hover:bg-gray-50/40 transition-colors">
                            <td class="py-4.5 px-4 font-semibold text-[#1A1A2E] whitespace-nowrap">
                                {{ $u['name'] }}
                            </td>
                            <td class="py-4.5 px-4 text-gray-600 whitespace-nowrap">
                                <span class="font-medium text-gray-800">{{ $u['username'] }}</span> · {{ $u['email'] }}
                            </td>
                            <td class="py-4.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $u['role'] === 'Admin' ? 'bg-purple-50 text-purple-700' : ($u['role'] === 'Kepala Sekolah' ? 'bg-amber-50 text-amber-700' : 'bg-indigo-50 text-indigo-700') }}">
                                    {{ $u['role'] }}
                                </span>
                            </td>
                            <td class="py-4.5 px-4 text-gray-500 whitespace-nowrap text-xs">
                                {{ $u['last_login'] }}
                            </td>
                            <td class="py-4.5 px-4 whitespace-nowrap font-medium {{ $u['status'] === 'Aktif' ? 'text-[#16A34A]' : 'text-rose-600' }}">
                                {{ $u['status'] }}
                            </td>
                            <td class="py-4.5 px-4 whitespace-nowrap text-right text-gray-500">
                                <div class="flex items-center justify-end gap-2 text-[13px]">
                                    <form method="POST" action="{{ route('admin.akun.toggle-status', $u['id']) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-gray-700 hover:text-[#555783] font-medium transition-colors cursor-pointer">
                                            {{ $u['status'] === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <span class="text-gray-300">/</span>
                                    <form method="POST" action="{{ route('admin.akun.reset-password', $u['id']) }}" class="inline" onsubmit="return confirm('Reset password akun {{ $u['email'] }} menjadi default?')">
                                        @csrf
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-medium transition-colors cursor-pointer">
                                            Reset Password
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-pagination :total="count($akunList)" :count="count($akunList)" :perPage="10" />
    </x-card>

    <x-callout-note
        title="Keamanan Akses Multi-Peran SLB Insan Mutiara"
        subtitle="Wali kelas hanya dapat menginput nilai untuk kelas yang ditugaskan. Kepala sekolah (Novia Parenri, S.Si, M.Si) memiliki hak review, persetujuan rapor resmi, dan export leger WhatsApp." />

</x-layouts.admin>
