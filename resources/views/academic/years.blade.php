<x-academic-layout title="Tahun Ajaran" :periode="$periode">
    @if($periode)
        <section class="panel space-y-4">
            <div>
                <h2>Pengisian rapor · {{ $periode->label }}</h2>
                <p>Batas pengisian: {{ $periode->batas_pengisian?->format('d-m-Y') ?? 'Belum ditetapkan' }} · Status: {{ $periode->pengisian_ditutup_pada ? 'Ditutup manual' : ($periode->isPengisianTerbuka() ? 'Terbuka' : 'Melewati batas waktu') }}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="dashboard-table dashboard-table--medium">
                    <thead><tr><th>Kelas</th><th>Rapor disetujui</th><th>Jumlah siswa</th></tr></thead>
                    <tbody>
                        @forelse($progress as $item)<tr><td>{{ $item['kelas'] }}</td><td>{{ $item['disetujui'] }}</td><td>{{ $item['total'] }}</td></tr>
                        @empty<tr><td colspan="3" class="table-empty">Belum ada siswa yang ditugaskan pada periode ini.</td></tr>@endforelse
                    </tbody>
                </table>
            </div>
            <p>Total: {{ $progress->sum('disetujui') }} dari {{ $progress->sum('total') }} rapor disetujui.</p>
            @if($periode->pengisian_ditutup_pada)
                <form method="POST" action="{{ route('admin.tahun-ajaran.buka-pengisian', $periode->id) }}" class="flex flex-wrap items-end gap-3" data-confirm="Buka kembali pengisian rapor periode ini?">
                    @csrf
                    <label>Alasan membuka kembali<input name="alasan" required minlength="10" maxlength="500" value="{{ old('alasan') }}" placeholder="Contoh: koreksi data siswa"></label>
                    <label>Batas pengisian baru (jika perlu)<input type="date" name="batas_pengisian" min="{{ now('Asia/Jakarta')->toDateString() }}"></label>
                    <button class="btn-secondary">Buka kembali</button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.tahun-ajaran.tutup-pengisian', $periode->id) }}" data-confirm="Tutup pengisian rapor? Wali kelas tidak dapat mengubah data sampai dibuka kembali.">
                    @csrf<button class="btn" @disabled($progress->sum('total') === 0 || $progress->sum('disetujui') !== $progress->sum('total'))>Tutup pengisian rapor</button>
                </form>
            @endif
        </section>
    @endif

    @if($periode?->semester === 'Genap' && !$periode->ditutup_pada)
        <section class="panel space-y-3">
            <h2>Tutup Tahun Ajaran</h2>
            <p>Periode berikutnya: {{ (int) substr($periode->tahun, 5, 4) }}/{{ (int) substr($periode->tahun, 5, 4) + 1 }} · Semester Ganjil. {{ $graduatingCount }} siswa kelas XII ditandai lulus. Siswa lain naik satu tingkat, kecuali yang memiliki keputusan “Tinggal kelas” atau “Belum lulus”. Penugasan disalin, nilai dan rapor baru dimulai kosong.</p>
            <form method="POST" action="{{ route('admin.tahun-ajaran.tutup-tahun', $periode->id) }}" data-confirm="Tutup tahun ajaran dan buat periode Ganjil berikutnya? Tindakan ini memindahkan siswa aktif ke tingkat berikutnya.">
                @csrf
                <button class="btn" @disabled(!$periode->pengisian_ditutup_pada || $progress->sum('total') === 0 || $progress->sum('disetujui') !== $progress->sum('total'))>Tutup Tahun Ajaran</button>
            </form>
            @unless($periode->pengisian_ditutup_pada)<p>Tutup pengisian rapor terlebih dahulu.</p>@endunless
        </section>
    @endif

    <section class="panel space-y-4">
        <h2>Tambah periode</h2>
        <form method="POST" action="{{ route('admin.tahun-ajaran.store') }}" class="grid sm:grid-cols-3 lg:grid-cols-6 gap-3 items-end">
            @csrf
            <label>Tahun<input required name="tahun" placeholder="2026/2027" value="{{ old('tahun') }}"></label>
            <label>Semester<select name="semester"><option>Ganjil</option><option>Genap</option></select></label>
            <label>Mulai<input type="date" name="tgl_mulai"></label>
            <label>Selesai<input type="date" name="tgl_selesai"></label>
            <label>Batas pengisian<input type="date" name="batas_pengisian"></label>
            <button class="btn">Tambah</button>
        </form>
    </section>

    @foreach($years as $y)
        <section class="panel space-y-3">
            <h2>{{ $y->label }} · {{ $y->is_aktif ? 'Aktif' : ($y->ditutup_pada ? 'Ditutup' : 'Tidak aktif') }}</h2>
            <form method="POST" action="{{ route('admin.tahun-ajaran.update', $y->id) }}" class="grid sm:grid-cols-3 lg:grid-cols-6 gap-3 items-end">
                @csrf @method('PUT')
                <label>Tahun<input required name="tahun" value="{{ $y->tahun }}"></label>
                <label>Semester<select name="semester"><option @selected($y->semester === 'Ganjil')>Ganjil</option><option @selected($y->semester === 'Genap')>Genap</option></select></label>
                <label>Mulai<input type="date" name="tgl_mulai" value="{{ $y->tgl_mulai }}"></label>
                <label>Selesai<input type="date" name="tgl_selesai" value="{{ $y->tgl_selesai }}"></label>
                <label>Batas pengisian<input type="date" name="batas_pengisian" value="{{ $y->batas_pengisian?->format('Y-m-d') }}"></label>
                <button class="btn-secondary">Simpan perubahan</button>
            </form>
            @unless($y->is_aktif)
                <form method="POST" action="{{ route('admin.tahun-ajaran.aktifkan', $y->id) }}" data-confirm="Aktifkan periode ini dan nonaktifkan periode sebelumnya?">@csrf<button class="btn">Aktifkan periode</button></form>
            @endunless
        </section>
    @endforeach
</x-academic-layout>
