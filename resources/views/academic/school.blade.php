<x-academic-layout title="Data Sekolah" :periode="$periode">
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.sekolah.update') }}" class="panel space-y-5" data-dirty-form>@csrf @method('PUT')
<div class="grid sm:grid-cols-2 gap-4">@foreach(['nama'=>'Nama sekolah','npsn'=>'NPSN','alamat'=>'Alamat','kota'=>'Kota','provinsi'=>'Provinsi','kode_pos'=>'Kode pos','telepon'=>'Telepon','email'=>'Email','kepala_sekolah'=>'Kepala sekolah','nip_kepala'=>'NIP kepala sekolah'] as $key=>$label)<label>{{ $label }}<input name="{{ $key }}" value="{{ old($key,$sekolah->$key) }}" @required(in_array($key,['nama','npsn','alamat','kota','provinsi','kepala_sekolah']))></label>@endforeach</div>
<label>Logo sekolah (PNG/JPEG, maksimum 8 MB)<input type="file" name="logo" accept="image/png,image/jpeg"></label>
@if($sekolah->logo)<img src="{{ route('school.logo') }}" alt="Logo sekolah" width="100">@endif
<button class="btn">Simpan profil sekolah</button></form>
</x-academic-layout>
