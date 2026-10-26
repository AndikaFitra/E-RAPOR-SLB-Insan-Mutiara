<x-academic-layout title="Penugasan Siswa dan Wali Kelas" :periode="$periode">
<form method="GET" class="panel flex gap-3 items-end"><label>Kelas<select name="kelas_id">@foreach($kelasList as $k)<option value="{{ $k->id }}" @selected($kelas?->id === $k->id)>{{ $k->nama }}</option>@endforeach</select></label><button class="btn">Tampilkan</button></form>
@if($kelas)
<form method="POST" action="{{ route('admin.penugasan.store') }}" class="panel space-y-5" data-dirty-form>@csrf<input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
<label>Wali kelas<select name="guru_id" required>@foreach($guruList as $g)<option value="{{ $g->id }}" @selected(old('guru_id', $wali?->guru_id) == $g->id)>{{ $g->nama_lengkap }}</option>@endforeach</select></label>
<p>Pilih anggota kelas {{ $kelas->nama }}. Tingkat dan fase ditentukan per siswa. Menghapus centang akan melepas siswa dari kelas ini pada periode aktif.</p>
<div class="overflow-x-auto"><table class="editable-table"><thead><tr><th>Pilih siswa</th><th>Kelas saat ini</th><th>Tingkat</th><th>Fase</th></tr></thead><tbody>
@foreach($students as $s) @php($a = $s->penugasanKelas->first())
<tr><td><label><input type="checkbox" name="siswa[]" value="{{ $s->id }}" @checked(in_array($s->id, old('siswa', $students->filter(fn($x) => $x->penugasanKelas->first()?->kelas_id === $kelas->id)->pluck('id')->all())))> {{ $s->nama_lengkap }}</label></td><td>{{ $a?->kelas?->nama ?? 'Belum ditugaskan' }}</td>
<td><select name="tingkat[{{ $s->id }}]"><option value="">Pilih</option>@foreach(['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'] as $level)<option @selected(old('tingkat.'.$s->id, $a?->tingkat) === $level)>{{ $level }}</option>@endforeach</select></td>
<td><select name="fase[{{ $s->id }}]"><option value="">Pilih</option>@foreach(['A','B','C','D','E','F'] as $phase)<option @selected(old('fase.'.$s->id, $a?->fase) === $phase)>{{ $phase }}</option>@endforeach</select></td></tr>
@endforeach</tbody></table></div>
<label><input type="checkbox" name="konfirmasi_pindah" value="1"> Saya telah memeriksa dan menyetujui perpindahan siswa yang berasal dari kelas lain.</label>
<h2>Mata pelajaran kelas pada periode ini</h2><div class="grid sm:grid-cols-2 gap-3">@foreach($subjects as $m)<label><input type="checkbox" name="mapel[]" value="{{ $m->id }}" @checked(in_array($m->id, old('mapel', $selectedSubjects)))> {{ $m->nama }} · {{ $m->kelompok }}</label>@endforeach</div><button class="btn">Simpan penugasan</button>
</form>@else<div class="panel">Tambahkan kelas terlebih dahulu.</div>@endif
</x-academic-layout>
