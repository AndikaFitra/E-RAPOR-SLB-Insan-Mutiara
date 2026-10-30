<x-academic-layout title="Leger / Rekap Nilai" :periode="$periode">
<form method="GET" class="panel grid sm:grid-cols-3 gap-4">
<label>Periode<select name="tahun_ajaran_id">@foreach($tahunList as $t)<option value="{{ $t->id }}" @selected($periode->id === $t->id)>{{ $t->label }}</option>@endforeach</select></label>
<label>Kelas<select name="kelas_id"><option value="">Semua kelas</option>@foreach($kelasList as $k)<option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>{{ $k->nama }}</option>@endforeach</select></label>
<label>Mapel<select name="mapel_id"><option value="">Semua mapel</option>@foreach($mapelList as $m)<option value="{{ $m->id }}" @selected(request('mapel_id') == $m->id)>{{ $m->nama }}</option>@endforeach</select></label>
<label>Siswa<input name="search" value="{{ request('search') }}" placeholder="Nama atau NISN"></label><label>Nilai minimum<input type="number" min="0" max="100" name="minimum" value="{{ request('minimum') }}"></label><label>Nilai maksimum<input type="number" min="0" max="100" name="maksimum" value="{{ request('maksimum') }}"></label>
<label>Ketuntasan<select name="ketuntasan"><option value="">Semua</option>@foreach(['Tuntas','Belum Tuntas','Belum lengkap','Belum ditetapkan'] as $s)<option @selected(request('ketuntasan') === $s)>{{ $s }}</option>@endforeach</select></label>
<div class="flex gap-3 items-end"><button class="btn">Terapkan filter</button><a class="btn-secondary" href="{{ route('kepala.leger.index') }}">Reset</a></div>
</form>
@if(config('rapor.nilai_tuntas') === null)<div class="panel">Kriteria ketuntasan belum ditetapkan oleh sekolah. Rata-rata tetap dihitung dari nilai yang tersimpan; angka sekitar 80 merupakan gambaran hasil belajar, bukan batas resmi untuk status “Tuntas”. Siswa umumnya naik kelas; pengecualian mengikuti keputusan sekolah berdasarkan perkembangan siswa. Jika sekolah menetapkan kriteria ketuntasan, administrator dapat mengaturnya kemudian.</div>@endif
<div class="panel"><h2>Ekspor leger</h2><p>{{ $periode->label }} · {{ $rows->count() }} siswa · {{ $subjects->count() }} mapel. Ekspor mengikuti filter yang sedang diterapkan. Rentang nilai berlaku pada mapel terpilih, atau rata-rata bila semua mapel dipilih.</p><div class="flex gap-3 mt-4"><a class="btn" href="{{ route('kepala.leger.export', request()->query() + ['format'=>'pdf']) }}">Unduh PDF</a><a class="btn-secondary" href="{{ route('kepala.leger.export', request()->query() + ['format'=>'xlsx']) }}">Unduh Excel (.xlsx)</a></div></div>
<div class="panel overflow-x-auto">@include('reports.leger-table')</div>
</x-academic-layout>
