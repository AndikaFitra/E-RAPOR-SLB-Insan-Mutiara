<x-academic-layout :title="$siswa->exists ? 'Edit Siswa' : 'Tambah Siswa'" :periode="$periode">
@php
    $agamaList = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Kepercayaan'];
    $selectedAgama = old('agama', $siswa->agama);
    if ($selectedAgama && !in_array($selectedAgama, $agamaList, true)) $agamaList[] = $selectedAgama;
@endphp
<form method="POST" action="{{ $siswa->exists ? route('admin.siswa.update', $siswa->id) : route('admin.siswa.store') }}" class="panel space-y-6" data-dirty-form>
    @csrf @if($siswa->exists) @method('PUT') @endif
    <h2>Identitas utama</h2>
    <div class="grid sm:grid-cols-2 gap-4">
        @foreach(['nama_lengkap'=>'Nama lengkap','nis'=>'NIS','nisn'=>'NISN (10 angka)','tempat_lahir'=>'Tempat lahir','no_hp'=>'Nomor HP'] as $key=>$label)
            <label>{{ $label }}
                <input name="{{ $key }}" value="{{ old($key, $siswa->$key) }}" @required(in_array($key, ['nama_lengkap','nis','nisn'])) @if($key === 'nisn') pattern="[0-9]{10}" maxlength="10" @endif
                    @class(['border-red-500 ring-1 ring-red-500' => $errors->has($key)]) @if($errors->has($key)) aria-invalid="true" aria-describedby="error-{{ $key }}" @endif>
                @error($key)<span id="error-{{ $key }}" class="block text-sm text-red-700">{{ $message }}</span>@enderror
            </label>
        @endforeach
        <label>Agama
            <select name="agama" @class(['border-red-500 ring-1 ring-red-500' => $errors->has('agama')]) @if($errors->has('agama')) aria-invalid="true" aria-describedby="error-agama" @endif>
                <option value="">Pilih agama</option>
                @foreach($agamaList as $agama)<option value="{{ $agama }}" @selected($selectedAgama === $agama)>{{ $agama }}</option>@endforeach
            </select>
            @error('agama')<span id="error-agama" class="block text-sm text-red-700">{{ $message }}</span>@enderror
        </label>
        @foreach(['jenis_kelamin'=>['L'=>'Laki-laki','P'=>'Perempuan'],'kekhususan'=>['Tunarungu'=>'Tunarungu','Tunagrahita'=>'Tunagrahita','Autis'=>'Autis'],'is_aktif'=>['1'=>'Aktif','0'=>'Tidak aktif / pindah sekolah']] as $key=>$options)
            <label>{{ ['jenis_kelamin'=>'Jenis kelamin','kekhususan'=>'Kekhususan','is_aktif'=>'Status'][$key] }}
                <select name="{{ $key }}" @class(['border-red-500 ring-1 ring-red-500' => $errors->has($key)]) @if($errors->has($key)) aria-invalid="true" aria-describedby="error-{{ $key }}" @endif>
                    @foreach($options as $value=>$label)<option value="{{ $value }}" @selected((string) old($key, $siswa->$key) === (string) $value)>{{ $label }}</option>@endforeach
                </select>
                @error($key)<span id="error-{{ $key }}" class="block text-sm text-red-700">{{ $message }}</span>@enderror
            </label>
        @endforeach
        @foreach(['tgl_lahir'=>'Tanggal lahir','tanggal_diterima'=>'Tanggal diterima'] as $key=>$label)
            <label>{{ $label }}
                <input type="date" name="{{ $key }}" value="{{ old($key, $key === 'tgl_lahir' ? $siswa->tgl_lahir?->format('Y-m-d') : $siswa->tanggal_diterima) }}"
                    @class(['border-red-500 ring-1 ring-red-500' => $errors->has($key)]) @if($errors->has($key)) aria-invalid="true" aria-describedby="error-{{ $key }}" @endif>
                @error($key)<span id="error-{{ $key }}" class="block text-sm text-red-700">{{ $message }}</span>@enderror
            </label>
        @endforeach
        <label>Alamat
            <textarea name="alamat" maxlength="2000" @class(['border-red-500 ring-1 ring-red-500' => $errors->has('alamat')]) @if($errors->has('alamat')) aria-invalid="true" aria-describedby="error-alamat" @endif>{{ old('alamat', $siswa->alamat) }}</textarea>
            @error('alamat')<span id="error-alamat" class="block text-sm text-red-700">{{ $message }}</span>@enderror
        </label>
    </div>
    <h2>Penempatan pada periode aktif</h2>
    <div class="grid sm:grid-cols-3 gap-4">
        <label>Kelas
            <select name="kelas_id" @class(['border-red-500 ring-1 ring-red-500' => $errors->has('kelas_id')]) @if($errors->has('kelas_id')) aria-invalid="true" aria-describedby="error-kelas_id" @endif>
                <option value="">Belum ditugaskan</option>
                @foreach($kelasList as $k)<option value="{{ $k->id }}" @selected(old('kelas_id', $penugasan?->kelas_id) == $k->id)>{{ $k->nama }}</option>@endforeach
            </select>
            @error('kelas_id')<span id="error-kelas_id" class="block text-sm text-red-700">{{ $message }}</span>@enderror
        </label>
        @foreach(['tingkat'=>['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'],'fase'=>['A','B','C','D','E','F']] as $key=>$options)
            <label>{{ ucfirst($key) }}
                <select name="{{ $key }}" @class(['border-red-500 ring-1 ring-red-500' => $errors->has($key)]) @if($errors->has($key)) aria-invalid="true" aria-describedby="error-{{ $key }}" @endif>
                    <option value="">Pilih</option>
                    @foreach($options as $v)<option value="{{ $v }}" @selected(old($key, $penugasan?->$key) === $v)>{{ $v }}</option>@endforeach
                </select>
                @error($key)<span id="error-{{ $key }}" class="block text-sm text-red-700">{{ $message }}</span>@enderror
            </label>
        @endforeach
    </div>
    @if($penugasan)
        <label><input type="checkbox" name="konfirmasi_pindah" value="1"> Saya menyetujui perpindahan dari kelas {{ $penugasan->kelas?->nama }} jika kelas tujuan diubah.</label>
        @error('konfirmasi_pindah')<span class="block text-sm text-red-700">{{ $message }}</span>@enderror
    @endif
    <div class="flex gap-3"><button class="btn">Simpan siswa</button><a class="btn-secondary" href="{{ route('admin.siswa.index') }}">Batal</a></div>
</form>
</x-academic-layout>
