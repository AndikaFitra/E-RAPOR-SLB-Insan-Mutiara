<x-academic-layout title="Nilai Asesmen" :periode="$periode">
    <form method="GET" class="panel flex flex-wrap gap-4 items-end">
        <label>Kelas<select name="kelas_id" required><option value="">Pilih kelas</option>@foreach($kelasList as $k)<option value="{{ $k->id }}" @selected($kelas?->id === $k->id)>{{ $k->nama }}</option>@endforeach</select></label>
        <button class="btn">Tampilkan kelas</button>
    </form>
    @if(!$kelas)
        <div class="panel">Belum ada penugasan wali kelas untuk periode ini. Hubungi admin sekolah.</div>
    @elseif(!$subject)
        <div class="panel overflow-x-auto">
            <table class="editable-table"><thead><tr><th>Mata pelajaran</th><th>Nilai terisi</th><th>Narasi terisi</th><th>Aksi</th></tr></thead><tbody>
            @forelse($subjects as $m)
                <tr><td>{{ $m->nama }}</td><td>{{ $rows->filter(fn($r) => $r['nilai']->get($m->id)?->nilai_akhir !== null)->count() }} / {{ $rows->count() }}</td><td>{{ $rows->filter(fn($r) => filled($r['nilai']->get($m->id)?->narasi_capaian))->count() }} / {{ $rows->count() }}</td><td><a class="link" href="{{ route('wali.asesmen.index', ['kelas_id' => $kelas->id, 'mapel_id' => $m->id]) }}">Input / edit nilai</a></td></tr>
            @empty <tr><td colspan="4">Mata pelajaran kelas belum ditetapkan. Hubungi admin.</td></tr> @endforelse
            </tbody></table>
        </div>
    @else
        <div class="panel"><h2>{{ $subject->nama }} · {{ $kelas->nama }}</h2><p>Nilai akhir 0–100 dan capaian individual. Isian belum lengkap dapat disimpan sebagai draft. Rapor yang diajukan atau disetujui terkunci.</p></div>
        <form method="POST" action="{{ route('wali.asesmen.store') }}" data-dirty-form class="panel space-y-5">
            @csrf<input type="hidden" name="kelas_id" value="{{ $kelas->id }}"><input type="hidden" name="mapel_id" value="{{ $subject->id }}">
            <div class="overflow-x-auto"><table class="editable-table"><thead><tr><th>Siswa / tingkat / fase</th><th>Nilai akhir</th><th>Narasi capaian</th></tr></thead><tbody>
            @foreach($rows as $i => $row)
                @php($locked = $row['rapor']?->isLocked())
                @php($score = $row['nilai']->get($subject->id))
                <tr><td>{{ $row['siswa']->nama_lengkap }}<small class="block">{{ $row['assignment']?->tingkat }} / {{ $row['assignment']?->fase }} · {{ $locked ? 'Terkunci' : 'Draft' }}</small></td>
                <td>
                    @unless($locked)<input type="hidden" name="nilai[{{ $i }}][siswa_id]" value="{{ $row['siswa']->id }}">@endunless
                    <input aria-label="Nilai {{ $row['siswa']->nama_lengkap }}" type="number" min="0" max="100" name="nilai[{{ $i }}][nilai_akhir]" value="{{ old('nilai.'.$i.'.nilai_akhir', $score?->nilai_akhir) }}" @disabled($locked)>
                </td><td><textarea aria-label="Capaian {{ $row['siswa']->nama_lengkap }}" rows="4" maxlength="5000" name="nilai[{{ $i }}][narasi_capaian]" @disabled($locked)>{{ old('nilai.'.$i.'.narasi_capaian', $score?->narasi_capaian) }}</textarea></td></tr>
            @endforeach
            </tbody></table></div>
            <div class="flex gap-3"><a class="btn-secondary" href="{{ route('wali.asesmen.index', ['kelas_id' => $kelas->id]) }}">Kembali</a>@if($rows->contains(fn($r) => !$r['rapor']?->isLocked()))<button class="btn">Simpan nilai kelas</button>@endif</div>
        </form>
    @endif
</x-academic-layout>
