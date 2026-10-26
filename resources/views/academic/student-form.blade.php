<x-academic-layout :title="$siswa->exists ? 'Edit Siswa' : 'Tambah Siswa'" :periode="$periode">
<form method="POST" action="{{ $siswa->exists ? route('admin.siswa.update',$siswa->id) : route('admin.siswa.store') }}" class="panel space-y-6" data-dirty-form>@csrf @if($siswa->exists) @method('PUT') @endif
<h2>Identitas utama</h2><div class="grid sm:grid-cols-2 gap-4">
@foreach(['nama_lengkap'=>'Nama lengkap','nis'=>'NIS','nisn'=>'NISN (10 angka)','tempat_lahir'=>'Tempat lahir','agama'=>'Agama','no_hp'=>'Nomor HP'] as $key=>$label)<label>{{ $label }}<input name="{{ $key }}" value="{{ old($key,$siswa->$key) }}" @required(in_array($key,['nama_lengkap','nisn'])) @if($key==='nisn') pattern="[0-9]{10}" maxlength="10" @endif></label>@endforeach
<label>Jenis kelamin<select name="jenis_kelamin" required><option value="L" @selected(old('jenis_kelamin',$siswa->jenis_kelamin)==='L')>Laki-laki</option><option value="P" @selected(old('jenis_kelamin',$siswa->jenis_kelamin)==='P')>Perempuan</option></select></label>
<label>Kekhususan<select name="kekhususan" required>@foreach(['Tunarungu','Tunagrahita','Autis'] as $v)<option @selected(old('kekhususan',$siswa->kekhususan)===$v)>{{ $v }}</option>@endforeach</select></label>
<label>Tanggal lahir<input type="date" name="tgl_lahir" value="{{ old('tgl_lahir',$siswa->tgl_lahir?->format('Y-m-d')) }}"></label><label>Tanggal diterima<input type="date" name="tanggal_diterima" value="{{ old('tanggal_diterima',$siswa->tanggal_diterima) }}"></label>
<label>Alamat<textarea name="alamat" maxlength="2000">{{ old('alamat',$siswa->alamat) }}</textarea></label><label>Status<select name="is_aktif"><option value="1" @selected(old('is_aktif',$siswa->is_aktif))>Aktif</option><option value="0" @selected(!old('is_aktif',$siswa->is_aktif))>Tidak aktif / pindah sekolah</option></select></label></div>
<h2>Penempatan pada periode aktif</h2><div class="grid sm:grid-cols-3 gap-4"><label>Kelas<select name="kelas_id"><option value="">Belum ditugaskan</option>@foreach($kelasList as $k)<option value="{{ $k->id }}" @selected(old('kelas_id',$penugasan?->kelas_id)==$k->id)>{{ $k->nama }}</option>@endforeach</select></label>
<label>Tingkat<select name="tingkat"><option value="">Pilih</option>@foreach(['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'] as $v)<option @selected(old('tingkat',$penugasan?->tingkat)===$v)>{{ $v }}</option>@endforeach</select></label>
<label>Fase<select name="fase"><option value="">Pilih</option>@foreach(['A','B','C','D','E','F'] as $v)<option @selected(old('fase',$penugasan?->fase)===$v)>{{ $v }}</option>@endforeach</select></label></div>
@if($penugasan)<label><input type="checkbox" name="konfirmasi_pindah" value="1"> Saya menyetujui perpindahan dari kelas {{ $penugasan->kelas?->nama }} jika kelas tujuan diubah.</label>@endif
<div class="flex gap-3"><button class="btn">Simpan siswa</button><a class="btn-secondary" href="{{ route('admin.siswa.index') }}">Batal</a></div>
</form>
</x-academic-layout>
