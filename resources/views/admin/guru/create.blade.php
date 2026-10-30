<x-layouts.admin
    title="Admin · Tambah Guru"
    periode="2026/2027 · Semester Ganjil"
    status="Aktif"
    userName="Admin Sekolah">

    <!-- Breadcrumb & Back Link -->
    <div class="space-y-1 mb-6">
        <div class="text-[13px] font-normal text-gray-500">
            Admin / Data Guru / Tambah Guru
        </div>
        <a href="{{ route('admin.guru.index') }}" class="inline-flex items-center gap-1.5 text-[13px] font-medium text-[#555783] hover:underline">
            <span>← Kembali ke Data Guru</span>
        </a>
        <h1 class="text-[28px] font-bold text-[#1A1A2E] tracking-tight leading-snug pt-1">
            Tambah Guru
        </h1>
        <p class="text-[14px] text-gray-500 leading-normal">
            Kelola identitas guru, dukungan 3 ketunaan SLB Insan Mutiara, dan kesiapan penugasan kelas.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            <p class="font-bold mb-1">Periksa isian form berikut:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.guru.store') }}">
        @csrf

        <!-- Identitas Guru -->
        <x-card class="mb-6">
            <h3 class="text-base font-bold text-[#1A1A2E] mb-5">Identitas guru</h3>

            <div class="space-y-4 text-[13px]">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Nama lengkap & Gelar *</label>
                        <input
                            type="text"
                            name="nama_lengkap"
                            value="{{ old('nama_lengkap') }}"
                            required
                            placeholder="contoh: Rina Pratiwi, S.Pd."
                            class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">NIP</label>
                        <input
                            type="text"
                            name="nip"
                            value="{{ old('nip') }}"
                            placeholder="Masukkan NIP atau biarkan kosong"
                            class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                        />
                        <p class="text-[11px] text-gray-400 mt-1">NIP 18 digit wajib diisi jika membuat akun. Kosongkan jika belum memiliki NIP resmi dan tidak membuat akun.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Jenis Kelamin *</label>
                        <select
                            name="jenis_kelamin"
                            required
                            class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-700 bg-white focus:outline-none focus:border-[#555783]"
                        >
                            <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Jenis Kekhususan yang Didukung (3 Ketunaan) *</label>
                        <select
                            name="kekhususan"
                            required
                            class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-700 bg-white focus:outline-none focus:border-[#555783]"
                        >
                            <option value="Tunarungu" {{ old('kekhususan') === 'Tunarungu' ? 'selected' : '' }}>Tunarungu (Program PKPBI)</option>
                            <option value="Tunagrahita" {{ old('kekhususan') === 'Tunagrahita' ? 'selected' : '' }}>Tunagrahita (Program Bina Diri)</option>
                            <option value="Autis" {{ old('kekhususan') === 'Autis' ? 'selected' : '' }}>Autis (Program Komunikasi & Interaksi)</option>
                            <option value="Tunarungu, Tunagrahita" {{ old('kekhususan') === 'Tunarungu, Tunagrahita' ? 'selected' : '' }}>Tunarungu & Tunagrahita</option>
                            <option value="Tunarungu, Tunagrahita, Autis" {{ old('kekhususan') === 'Tunarungu, Tunagrahita, Autis' ? 'selected' : '' }}>Semua (Tunarungu, Tunagrahita, Autis)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Nomor WhatsApp *</label>
                        <input
                            type="text"
                            name="no_whatsapp"
                            value="{{ old('no_whatsapp') }}"
                            required
                            placeholder="08xxxxxxxxxx"
                            class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Email Akun (Opsional)</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="guru@slbinsanmutiara.sch.id"
                            class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white placeholder-gray-400 focus:outline-none focus:border-[#555783]"
                        />
                        <p class="text-[11px] text-gray-400 mt-1">Isi email, NIP, dan sandi jika guru langsung diberi akun.</p>
                    </div>
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Status Guru *</label>
                        <select
                            name="status"
                            required
                            class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-700 bg-white focus:outline-none focus:border-[#555783]"
                        >
                            <option value="Aktif" {{ old('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Tidak aktif" {{ old('status') === 'Tidak aktif' ? 'selected' : '' }}>Tidak aktif</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5" for="guru-password">Sandi akun (jika email diisi)</label>
                        <input id="guru-password" type="password" name="password" minlength="12" autocomplete="new-password" class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white focus:outline-none focus:border-[#555783]" />
                        <p class="text-[11px] text-gray-400 mt-1">Minimal 12 karakter. Berikan sandi kepada guru melalui jalur yang aman.</p>
                    </div>
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5" for="guru-password-confirmation">Ulangi sandi akun</label>
                        <input id="guru-password-confirmation" type="password" name="password_confirmation" minlength="12" autocomplete="new-password" class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white focus:outline-none focus:border-[#555783]" />
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Bottom Callout Note -->
        <div class="mb-6">
            <x-callout-note
                title="Penugasan Kelas Tidak Berubah Otomatis"
                subtitle="Atur kelas guru melalui menu Penugasan Siswa & Wali Kelas. Guru dengan akun aktif dapat login ke portal e-Rapor." />
        </div>

        <!-- Bottom Actions Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2 pb-6">
            <p class="text-[13px] text-gray-500">
                Periksa kembali kelengkapan data guru sebelum menyimpan.
            </p>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.guru.index') }}"
                   class="px-5 py-2.5 border border-[#D5D7E2] bg-white hover:bg-gray-50 text-[13px] font-semibold text-gray-700 rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-[#555783] hover:bg-[#47496E] text-[13px] font-semibold text-white rounded-xl shadow-sm transition-colors cursor-pointer">
                    Simpan guru
                </button>
            </div>
        </div>
    </form>

</x-layouts.admin>
