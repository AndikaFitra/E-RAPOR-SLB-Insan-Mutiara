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
    @elseif(session('error'))
        <div class="mb-6">
            <x-alert-banner type="error" title="Gagal" :message="session('error')" />
        </div>
    @endif

    <div x-data="{
        showCreateModal: false,
        nipInput: @js(old('nip', '')),
        openDelete(id, name) {
            this.$dispatch('open-delete-modal', {
                name: name,
                details: 'Akun pengguna',
                action: '{{ url('admin/akun') }}/' + id
            });
        }
    }">

        <!-- Page Header -->
        <x-page-header
            breadcrumb="Admin / Akun & Hak Akses"
            title="Akun & Hak Akses"
            subtitle="Kelola kredensial login, peran pengguna (Admin, Kepala Sekolah, Wali Kelas), dan status aktivasi.">
            <x-slot:action>
                <button
                    type="button"
                    @click="showCreateModal = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#555783] hover:bg-[#47496E] text-white text-[14px] font-semibold rounded-xl shadow-sm transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    <span>Tambah Akun Baru</span>
                </button>
            </x-slot:action>
        </x-page-header>

        <!-- Filter Card -->
        <x-card class="p-5 mb-6 space-y-4">
            <form method="GET" action="{{ route('admin.akun.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="relative flex-1 w-full">
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
                        placeholder="Cari nama, NIP, atau email akun..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-[#1A1A2E] placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                    />
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <select name="role" onchange="this.form.submit()" class="px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-[#1A1A2E] focus:outline-none focus:border-[#555783]">
                        <option value="">Role: Semua</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="kepala_sekolah" {{ request('role') === 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                        <option value="wali_kelas" {{ request('role') === 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                    </select>

                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#555783] text-white text-xs font-semibold hover:bg-[#46486E] transition-colors shadow-xs">
                        Cari
                    </button>

                    @if(request('search') || request('role'))
                        <a href="{{ route('admin.akun.index') }}" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 bg-white hover:bg-[#F7F6F2] transition-colors flex items-center gap-2">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </x-card>

        <!-- Table of Users -->
        <x-card class="mb-6 p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dashboard-table min-w-[1140px] table-fixed text-left">
                    <colgroup>
                        <col class="w-[17%]"><col class="w-[26%]"><col class="w-[14%]">
                        <col class="w-[14%]"><col class="w-[9%]"><col class="w-[20%]">
                    </colgroup>
                    <thead>
                        <tr class="bg-[#F7F6F2] text-[#555783] text-[12px] font-bold border-b border-[#E8E7ED]">
                            <th scope="col" class="px-5 py-4">Nama pengguna</th>
                            <th scope="col" class="px-5 py-4">NIP / Email kontak</th>
                            <th scope="col" class="px-4 py-4">Hak akses</th>
                            <th scope="col" class="px-4 py-4">Guru terkait</th>
                            <th scope="col" class="px-4 py-4">Status</th>
                            <th scope="col" class="px-4 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8E7ED]">
                        @forelse($akunList as $u)
                            <tr class="align-middle hover:bg-[#FBFBFA] transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#EEEEF5] text-[#555783] font-bold" aria-hidden="true">{{ strtoupper(substr($u['name'], 0, 1)) }}</span>
                                        <span class="font-semibold leading-5 text-[#1A1A2E] break-words">{{ $u['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="block font-semibold text-[#1A1A2E]">{{ $u['nip'] ?: 'NIP belum diisi' }}</span>
                                    <span class="mt-1 block break-all text-[12px] leading-5 text-gray-500" title="{{ $u['email'] }}">{{ $u['email'] }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold whitespace-nowrap {{ $u['role'] === 'Admin' ? 'bg-purple-50 text-purple-700' : ($u['role'] === 'Kepala Sekolah' ? 'bg-amber-50 text-amber-700' : 'bg-indigo-50 text-indigo-700') }}">
                                        {{ $u['role'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 leading-5 text-gray-600">
                                    {{ $u['guru'] ?: 'Belum terhubung' }}
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold whitespace-nowrap {{ $u['status'] === 'Aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $u['status'] === 'Aktif' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        {{ $u['status'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="grid grid-cols-2 gap-2 text-center text-[11px] font-semibold">
                                        <a href="{{ route('admin.akun.edit', $u['id']) }}" class="inline-flex min-h-9 items-center justify-center rounded-lg border border-[#D8DAE4] bg-white px-2 text-[#555783] hover:bg-[#F3F2F8] transition-colors">Edit akun</a>
                                        <a href="{{ route('admin.akun.edit', $u['id']) }}" class="inline-flex min-h-9 items-center justify-center rounded-lg border border-[#D8DAE4] bg-white px-2 text-[#555783] hover:bg-[#F3F2F8] transition-colors" title="Atur password akun">Password</a>
                                        @if($u['id'] !== auth()->id())
                                            <form method="POST" action="{{ route('admin.akun.toggle-status', $u['id']) }}">@csrf
                                                <button type="submit" class="flex min-h-9 w-full items-center justify-center whitespace-nowrap rounded-lg border border-[#D8DAE4] bg-[#F7F6F2] px-1 text-center text-gray-700 hover:bg-[#ECEAE5] transition-colors">{{ $u['status'] === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                            </form>
                                            <button type="button" data-email="{{ $u['email'] }}" @click="openDelete({{ $u['id'] }}, $el.dataset.email)" class="min-h-9 rounded-lg border border-rose-200 bg-rose-50 px-2 text-rose-700 hover:bg-rose-100 transition-colors">Hapus</button>
                                        @else
                                            <span class="col-span-2 self-center text-[11px] font-normal text-gray-400">Akun yang sedang digunakan</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    Tidak ada data akun yang cocok dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :current="1" :total="1" :totalItems="count($akunList)" :perPage="10" />
        </x-card>

        <!-- Modal Tambah Akun Baru -->
        <div
            x-show="showCreateModal"
            style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-black/40 backdrop-blur-xs flex items-center justify-center p-4"
            @keydown.escape.window="showCreateModal = false"
        >
            <div
                @click.away="showCreateModal = false"
                class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-[#E8E7ED] space-y-4"
            >
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-[#1A1A2E]">Tambah Akun Pengguna Baru</h3>
                    <button type="button" @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.akun.store') }}" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Nama Lengkap *</label>
                        <input
                            type="text"
                            name="name"
                            required
                            placeholder="Contoh: Rina Pratiwi, S.Pd."
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Email *</label>
                        <input
                            type="email"
                            name="email"
                            required
                            placeholder="nama@slbinsanmutiara.sch.id"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">NIP login *</label>
                        <input type="text" name="nip" inputmode="numeric" pattern="[0-9]{18}" maxlength="18" required x-model="nipInput" placeholder="18 digit; mengikuti guru terkait jika ada" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]" />
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Password *</label>
                        <input
                            type="password"
                            name="password"
                            required
                            minlength="8"
                            placeholder="Minimal 8 karakter"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Role / Peran *</label>
                            <select name="role" required class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]">
                                <option value="wali_kelas">Wali Kelas</option>
                                <option value="kepala_sekolah">Kepala Sekolah</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Hubungkan ke Data Guru</label>
                            <select name="guru_id" @change="nipInput = $event.target.selectedOptions[0].dataset.nip || nipInput" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#555783]">
                                <option value="">— Tidak terhubung —</option>
                                @foreach($guruList as $g)
                                    <option value="{{ $g->id }}" data-nip="{{ $g->nip }}">{{ $g->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2.5 rounded-xl border border-gray-200 font-semibold text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#555783] hover:bg-[#46486E] text-white font-bold shadow-xs transition-colors">
                            Simpan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2-Step Verification Delete Modal -->
        <x-modal.confirm-delete
            title="Hapus Akun Pengguna"
            message="Apakah Anda yakin ingin menghapus akun pengguna ini? Pengguna tidak akan dapat login lagi ke sistem e-rapor."
        />

    </div>

    <x-callout-note
        title="Keamanan Akses Multi-Peran SLB Insan Mutiara"
        description="Wali kelas hanya dapat menginput nilai untuk kelas yang ditugaskan. Kepala sekolah memiliki hak review, persetujuan rapor resmi, dan export leger WhatsApp." />

</x-layouts.admin>
