<x-academic-layout title="Penugasan Siswa dan Pembimbing Ekstrakurikuler" :periode="$periode">
<form method="GET" class="panel flex gap-3 items-end"><label>Kegiatan<select name="ekstrakurikuler_id">@foreach($activities as $e)<option value="{{ $e->id }}" @selected($activity?->id === $e->id)>{{ $e->nama }}</option>@endforeach</select></label><button class="btn">Tampilkan</button></form>
@if($activity)
<form method="POST" action="{{ route('admin.ekskul.penugasan.store') }}" class="panel space-y-5" data-dirty-form>@csrf<input type="hidden" name="ekstrakurikuler_id" value="{{ $activity->id }}">
<div class="grid sm:grid-cols-2 gap-4"><label>Pembimbing<select name="guru_id" required>@foreach($guruList as $g)<option value="{{ $g->id }}" @selected(old('guru_id', $assignment?->guru_id) == $g->id)>{{ $g->nama_lengkap }}</option>@endforeach</select></label><label>Jadwal<input name="jadwal" maxlength="150" value="{{ old('jadwal', $assignment?->jadwal) }}"></label></div>
<h2>Peserta {{ $activity->nama }}</h2><div class="grid sm:grid-cols-2 gap-3">@foreach($students as $s)<label><input type="checkbox" name="siswa[]" value="{{ $s->id }}" @checked(in_array($s->id, old('siswa', $selected)))> {{ $s->nama_lengkap }} · {{ $s->nisn }}</label>@endforeach</div><button class="btn">Simpan penugasan</button></form>
@else<div class="panel">Tambahkan ekstrakurikuler terlebih dahulu.</div>@endif
</x-academic-layout>
