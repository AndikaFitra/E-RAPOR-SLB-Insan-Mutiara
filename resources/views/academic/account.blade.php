<x-academic-layout title="Edit Akun dan Hak Akses" :periode="$periode">
<form method="POST" action="{{ route('admin.akun.update',$user->id) }}" class="panel space-y-4" data-dirty-form>@csrf @method('PUT')
<label>Nama<input required name="name" value="{{ old('name',$user->name) }}"></label><label>Email<input required type="email" name="email" value="{{ old('email',$user->email) }}"></label>
<label>NIP login <span class="font-normal text-gray-500">(mengikuti guru terkait jika guru memiliki NIP)</span><input required name="nip" inputmode="numeric" pattern="[0-9]{18}" maxlength="18" value="{{ old('nip',$user->nip) }}"></label>
<label>Role<select name="role">@foreach(['admin'=>'Admin','wali_kelas'=>'Wali Kelas','kepala_sekolah'=>'Kepala Sekolah'] as $value=>$label)<option value="{{ $value }}" @selected(old('role',$user->role)===$value)>{{ $label }}</option>@endforeach</select></label>
<label>Guru terkait<select name="guru_id"><option value="">Tidak terkait guru</option>@foreach($guruList as $g)<option value="{{ $g->id }}" @selected(old('guru_id',$user->guru_id)==$g->id)>{{ $g->nama_lengkap }}</option>@endforeach</select></label>
<label>Status<select name="is_aktif"><option value="1" @selected($user->is_aktif)>Aktif</option><option value="0" @selected(!$user->is_aktif)>Tidak aktif</option></select></label>
<label>Password baru (kosongkan jika tetap)<input type="password" name="password" minlength="8" autocomplete="new-password"></label><label>Konfirmasi password baru<input type="password" name="password_confirmation" autocomplete="new-password"></label>
<button class="btn">Simpan perubahan</button></form>
</x-academic-layout>
