<x-layouts.admin
    title="Admin · Tambah Siswa"
    periode="2026/2027 · Semester Ganjil"
    status="Aktif"
    userName="Admin Sekolah">

    <!-- Breadcrumb & Back Link -->
    <div class="space-y-1 mb-6">
        <div class="text-[13px] font-normal text-gray-500">
            Admin / Data Siswa / Tambah Siswa
        </div>
        <a href="{{ route('admin.siswa.index') }}"
           @click.prevent="$dispatch('open-unsaved-modal', { name: 'siswa', url: '{{ route('admin.siswa.index') }}' })"
           class="inline-flex items-center gap-1.5 text-[13px] font-medium text-[#555783] hover:underline cursor-pointer">
            <span>← Kembali ke Data Siswa</span>
        </a>
        <h1 class="text-[28px] font-bold text-[#1A1A2E] tracking-tight leading-snug pt-1">
            Tambah Siswa
        </h1>
        <p class="text-[14px] text-gray-500 leading-normal">
            Lengkapi identitas siswa. Tanda * berarti wajib diisi. Hubungan orang tua/wali dapat ditetapkan nanti.
        </p>
    </div>

    <!-- Error Alert Banner if session error or tested via ?notif=gagal -->
    @if(session('error') || request('notif') === 'gagal' || $errors->any())
        <x-alert-banner
            type="error"
            title="Data siswa gagal disimpan."
            description="{{ $errors->any() ? $errors->first() : 'Perubahan belum tersimpan. Isian Anda tetap tersedia; pilih Coba lagi untuk menyimpan kembali.' }}"
            class="mb-6"
        />
    @endif

    <!-- Loading Alert Banner if tested via ?notif=loading -->
    @if(request('notif') === 'loading')
        <x-alert-banner
            type="loading"
            title="Sedang menyimpan data siswa"
            description="Tunggu hingga proses selesai. Jangan meninggalkan halaman ini."
            class="mb-6"
        />
    @endif

    <form method="POST" action="{{ route('admin.siswa.store') }}">
        @csrf

        <!-- Section 1: Identitas Utama -->
        <x-card class="mb-6">
            <div class="mb-5">
                <h3 class="text-base font-bold text-[#1A1A2E]">Identitas utama</h3>
                <p class="text-xs text-gray-500 mt-0.5">Gunakan identitas sesuai dokumen resmi siswa.</p>
            </div>

            <div class="space-y-4 text-[13px]">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Nama lengkap *</label>
                        <input type="text" 
                               name="nama_lengkap" 
                               value="{{ old('nama_lengkap') }}" 
                               required 
                               placeholder="Masukkan nama lengkap" 
                               class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white placeholder-gray-400 focus:outline-none focus:border-[#555783]" />
                    </div>
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">NISN *</label>
                        <input type="text" 
                               name="nisn" 
                               value="{{ old('nisn') }}" 
                               required 
                               placeholder="Masukkan NISN" 
                               class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white placeholder-gray-400 focus:outline-none focus:border-[#555783]" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Jenis kelamin *</label>
                        <select name="jenis_kelamin" required class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-700 bg-white focus:outline-none focus:border-[#555783]">
                            <option value="">Masukkan jenis kelamin</option>
                            <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Jenis kekhususan (3 Ketunaan) *</label>
                        <select name="kekhususan" required class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-700 bg-white focus:outline-none focus:border-[#555783]">
                            <option value="">Pilih jenis kekhususan</option>
                            <option value="Tunarungu" {{ old('kekhususan') === 'Tunarungu' ? 'selected' : '' }}>Tunarungu (Program: PKPBI)</option>
                            <option value="Tunagrahita" {{ old('kekhususan') === 'Tunagrahita' ? 'selected' : '' }}>Tunagrahita (Program: Bina Diri)</option>
                            <option value="Autis" {{ old('kekhususan') === 'Autis' ? 'selected' : '' }}>Autis (Program: Pengembangan Komunikasi & Interaksi)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Tempat lahir</label>
                        <input type="text" 
                               name="tempat_lahir" 
                               value="{{ old('tempat_lahir') }}" 
                               placeholder="Masukkan tempat lahir" 
                               class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white placeholder-gray-400 focus:outline-none focus:border-[#555783]" />
                    </div>
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Tanggal lahir</label>
                        <input type="date" 
                               name="tgl_lahir" 
                               value="{{ old('tgl_lahir') }}" 
                               class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white focus:outline-none focus:border-[#555783]" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Alamat siswa</label>
                        <textarea rows="3" name="alamat" placeholder="Masukkan alamat siswa" class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white placeholder-gray-400 focus:outline-none focus:border-[#555783]">{{ old('alamat') }}</textarea>
                    </div>
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">No. Handphone / WhatsApp</label>
                        <input type="text" 
                               name="no_hp" 
                               value="{{ old('no_hp') }}" 
                               placeholder="Contoh: 08123456789" 
                               class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-800 bg-white placeholder-gray-400 focus:outline-none focus:border-[#555783]" />
                        <p class="text-[11px] text-gray-400 mt-1">Opsional; untuk koordinasi dengan wali murid.</p>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Section 2: Informasi Akademik & Kelas -->
        <x-card class="mb-6">
            <div class="mb-5">
                <h3 class="text-base font-bold text-[#1A1A2E]">Informasi akademik</h3>
                <p class="text-xs text-gray-500 mt-0.5">Kelas dapat berisi beberapa tingkat. Tingkat dan fase disimpan per siswa.</p>
            </div>

            <div class="space-y-4 text-[13px]">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Kelas *</label>
                        <select name="kelas_id" class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-700 bg-white focus:outline-none focus:border-[#555783]">
                            <option value="">Pilih kelas</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama }} ({{ $k->kekhususan }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Tingkat</label>
                        <select name="tingkat" class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-700 bg-white focus:outline-none focus:border-[#555783]">
                            @foreach(['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'] as $t)
                                <option value="{{ $t }}" {{ old('tingkat', 'VII') == $t ? 'selected' : '' }}>Tingkat {{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-[#1A1A2E] mb-1.5">Fase</label>
                        <select name="fase" class="w-full px-3.5 py-2.5 border border-[#D5D7E2] rounded-xl text-gray-700 bg-white focus:outline-none focus:border-[#555783]">
                            @foreach(['A', 'B', 'C', 'D', 'E', 'F'] as $f)
                                <option value="{{ $f }}" {{ old('fase', 'D') == $f ? 'selected' : '' }}>Fase {{ $f }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Bottom Actions Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2 pb-6">
            <p class="text-[13px] text-gray-500">
                Periksa kembali data sebelum menyimpan.
            </p>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.siswa.index') }}"
                   @click.prevent="$dispatch('open-unsaved-modal', { name: 'siswa baru', url: '{{ route('admin.siswa.index') }}' })"
                   class="px-5 py-2.5 border border-[#D5D7E2] bg-white hover:bg-gray-50 text-[13px] font-semibold text-gray-700 rounded-xl transition-colors cursor-pointer">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-[#555783] hover:bg-[#47496E] text-[13px] font-semibold text-white rounded-xl shadow-sm transition-colors cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    <span>Simpan Siswa</span>
                </button>
            </div>
        </div>
    </form>

    <!-- Reusable Modal Unsaved Changes (Alpine.js) -->
    <x-modal.unsaved-changes
        itemName="Siswa Baru"
        leaveUrl="{{ route('admin.siswa.index') }}"
    />

</x-layouts.admin>
