<x-academic-layout title="Evaluasi Non-Akademik" :periode="$periode">
    @if(!$selected)
        <form method="GET" class="panel flex gap-3"><input name="search" aria-label="Cari siswa" placeholder="Cari siswa atau NISN" value="{{ request('search') }}"><button class="btn">Cari</button></form>
        <div class="panel"><table><thead><tr><th>Siswa</th><th>NISN</th><th>Aksi</th></tr></thead><tbody>
        @forelse($students as $s)<tr><td>{{ $s->nama_lengkap }}</td><td>{{ $s->nisn }}</td><td><a class="link" href="{{ route('wali.nonakademik.index', ['siswa_id' => $s->id]) }}">Input / edit evaluasi</a></td></tr>@empty<tr><td colspan="3">Tidak ada siswa kelas binaan.</td></tr>@endforelse
        </tbody></table></div>
    @else
        <div class="panel"><h2>{{ $selected->nama_lengkap }} · {{ $data['tingkat'] }} / {{ $data['fase'] }}</h2><p>Isi evaluasi → periksa kelengkapan → preview → ajukan → perbaiki jika dikembalikan → ajukan ulang.</p></div>
        @if($rapor?->isLocked())<div class="panel">Rapor terkunci. Evaluasi hanya dapat dibaca.</div>@endif
        <form method="POST" action="{{ route('wali.nonakademik.store') }}" data-dirty-form class="panel space-y-6">
            @csrf<input type="hidden" name="siswa_id" value="{{ $selected->id }}">
            <fieldset @disabled($rapor?->isLocked()) class="space-y-6">
                <h2>Ketidakhadiran</h2>
                <div class="grid sm:grid-cols-3 gap-4">@foreach(['sakit'=>'Sakit', 'izin'=>'Izin', 'alpa'=>'Tanpa keterangan'] as $field => $label)<label>{{ $label }} (hari)<input required type="number" min="0" max="366" name="{{ $field }}" value="{{ old($field, $data['evaluasi'][$field] ?? 0) }}"></label>@endforeach</div>
                <label>Perkembangan fisik dan kesehatan<textarea name="perkembangan" maxlength="5000" rows="4">{{ old('perkembangan', $data['evaluasi']['perkembangan'] ?? '') }}</textarea></label>
                <h2>Ekstrakurikuler</h2>
                @forelse($data['ekskul'] as $e)<label>{{ $e['nama'] }}<textarea name="ekskul[{{ $e['id'] }}]" maxlength="200">{{ old('ekskul.'.$e['id'], $e['keterangan']) }}</textarea></label>@empty<p>Belum ada kegiatan yang ditugaskan oleh admin.</p>@endforelse
                <h2>Prestasi</h2>
                <label class="flex items-center gap-2"><input type="checkbox" name="tidak_ada_prestasi" value="1" @checked(old('tidak_ada_prestasi', $data['evaluasi']['tidak_ada_prestasi'] ?? false))> Tidak ada prestasi pada semester ini</label>
                @php($prestasiRows = old('prestasi', $data['prestasi']))
                <div x-data="{ rows: {{ \Illuminate\Support\Js::from(array_values($prestasiRows ?: [['kegiatan'=>'', 'keterangan'=>'']])) }} }" class="space-y-3">
                    <template x-for="(item, index) in rows" :key="index"><div class="grid sm:grid-cols-[1fr_1fr_auto] gap-3"><input aria-label="Nama prestasi" placeholder="Kegiatan / prestasi" :name="'prestasi['+index+'][kegiatan]'" x-model="item.kegiatan" maxlength="200"><input aria-label="Keterangan prestasi" placeholder="Keterangan" :name="'prestasi['+index+'][keterangan]'" x-model="item.keterangan" maxlength="200"><button type="button" class="btn-secondary" @click="rows.splice(index, 1)">Hapus</button></div></template>
                    <button type="button" class="btn-secondary" @click="rows.push({kegiatan:'', keterangan:''})">Tambah prestasi</button>
                </div>
                <label>Catatan wali kelas<textarea name="catatan_wali" rows="4" maxlength="5000">{{ old('catatan_wali', $data['catatan']) }}</textarea></label>
                <div class="grid sm:grid-cols-2 gap-4"><label>Tanggal rapor<input type="date" name="tgl_rapor" value="{{ old('tgl_rapor', $rapor?->tgl_rapor?->format('Y-m-d')) }}"></label>
                <label>Keputusan akhir tahun (bila berlaku)<select name="keputusan"><option value="">Belum / tidak ditetapkan</option>@foreach(['Naik ke kelas berikutnya','Tinggal kelas','Lulus','Belum lulus'] as $decision)<option @selected(old('keputusan', $data['keputusan']) === $decision)>{{ $decision }}</option>@endforeach</select></label></div>
                <button class="btn">Simpan evaluasi</button>
            </fieldset>
            <a class="link" href="{{ route('wali.rapor.show', $selected->id) }}">Periksa kelengkapan dan preview rapor</a>
        </form>
    @endif
</x-academic-layout>
