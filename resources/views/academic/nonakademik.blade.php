<x-academic-layout title="Evaluasi Non-Akademik" :periode="$periode">
    @if(!$selected)
        <form method="GET" class="panel flex gap-3"><input name="search" aria-label="Cari siswa" placeholder="Cari siswa atau NISN" value="{{ request('search') }}"><button class="btn">Cari</button></form>
        <div class="panel overflow-x-auto"><table class="dashboard-table dashboard-table--medium"><thead><tr><th>Siswa</th><th>NISN</th><th>Aksi</th></tr></thead><tbody>
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
                <div x-data="{}">
                    <label for="contoh-perkembangan">Perkembangan fisik dan kesehatan</label>
                    <select id="contoh-perkembangan" aria-label="Contoh perkembangan fisik dan kesehatan"
                        @change="if ($event.target.value) { $refs.perkembangan.value = $event.target.value; $refs.perkembangan.dispatchEvent(new Event('input', { bubbles: true })); }">
                        <option value="">Pilih contoh, lalu sesuaikan</option>
                        <option value="{{ $selected->nama_lengkap }} menunjukkan kondisi fisik yang baik dan dapat mengikuti kegiatan belajar secara aktif.">Kondisi fisik baik</option>
                        <option value="{{ $selected->nama_lengkap }} semakin mampu mengikuti aktivitas fisik sesuai kemampuannya dengan arahan guru.">Kemajuan aktivitas fisik</option>
                        <option value="{{ $selected->nama_lengkap }} dapat mengikuti kegiatan fisik dengan penyesuaian dan waktu istirahat yang cukup.">Perlu penyesuaian kegiatan</option>
                        <option value="{{ $selected->nama_lengkap }} masih memerlukan pendampingan dalam menjaga kebiasaan hidup sehat dan mengikuti aktivitas fisik.">Perlu pendampingan</option>
                    </select>
                    <small class="mt-1 block text-gray-500">Kalimat bisa diubah atau ditulis sendiri.</small>
                    <textarea x-ref="perkembangan" aria-label="Perkembangan fisik dan kesehatan" name="perkembangan" maxlength="5000" rows="4" placeholder="Tulis berdasarkan pengamatan Anda">{{ old('perkembangan', $data['evaluasi']['perkembangan'] ?? '') }}</textarea>
                </div>
                <h2>Ekstrakurikuler</h2>
                @forelse($data['ekskul'] as $e)<label>{{ $e['nama'] }}<textarea name="ekskul[{{ $e['id'] }}]" maxlength="200">{{ old('ekskul.'.$e['id'], $e['keterangan']) }}</textarea></label>@empty<p>Belum ada kegiatan yang ditugaskan oleh admin.</p>@endforelse
                <h2>Prestasi</h2>
                <label class="flex items-center gap-2"><input type="checkbox" name="tidak_ada_prestasi" value="1" @checked(old('tidak_ada_prestasi', $data['evaluasi']['tidak_ada_prestasi'] ?? false))> Tidak ada prestasi pada semester ini</label>
                @php($prestasiRows = old('prestasi', $data['prestasi']))
                <div x-data="{ rows: {{ \Illuminate\Support\Js::from(array_values($prestasiRows ?: [['kegiatan'=>'', 'keterangan'=>'']])) }} }" class="space-y-3">
                    <template x-for="(item, index) in rows" :key="index"><div class="grid sm:grid-cols-[1fr_1fr_auto] gap-3"><input aria-label="Nama prestasi" placeholder="Kegiatan / prestasi" :name="'prestasi['+index+'][kegiatan]'" x-model="item.kegiatan" maxlength="200"><input aria-label="Keterangan prestasi" placeholder="Keterangan" :name="'prestasi['+index+'][keterangan]'" x-model="item.keterangan" maxlength="200"><button type="button" class="btn-secondary" @click="rows.splice(index, 1)">Hapus</button></div></template>
                    <button type="button" class="btn-secondary" @click="rows.push({kegiatan:'', keterangan:''})">Tambah prestasi</button>
                </div>
                <div x-data="{}">
                    <label for="contoh-catatan-wali">Catatan wali kelas</label>
                    <select id="contoh-catatan-wali" aria-label="Contoh catatan wali kelas"
                        @change="if ($event.target.value) { $refs.catatan.value = $event.target.value; $refs.catatan.dispatchEvent(new Event('input', { bubbles: true })); }">
                        <option value="">Pilih contoh, lalu sesuaikan</option>
                        <option value="{{ $selected->nama_lengkap }} menunjukkan kemajuan yang baik selama semester ini. Pertahankan latihan dan kebiasaan belajar di rumah.">Kemajuan baik</option>
                        <option value="{{ $selected->nama_lengkap }} semakin percaya diri mengikuti kegiatan kelas. Dukungan keluarga di rumah akan membantu menjaga kemajuan ini.">Semakin percaya diri</option>
                        <option value="{{ $selected->nama_lengkap }} membutuhkan latihan yang konsisten dan pendampingan bertahap untuk menguatkan keterampilan yang telah dipelajari.">Perlu latihan rutin</option>
                        <option value="Kerja sama antara sekolah dan keluarga perlu dilanjutkan agar perkembangan {{ $selected->nama_lengkap }} dapat dipantau secara teratur.">Kerja sama dengan keluarga</option>
                    </select>
                    <small class="mt-1 block text-gray-500">Kalimat bisa diubah atau ditulis sendiri.</small>
                    <textarea x-ref="catatan" aria-label="Catatan wali kelas" name="catatan_wali" rows="4" maxlength="5000" placeholder="Tulis pesan dan saran untuk siswa">{{ old('catatan_wali', $data['catatan']) }}</textarea>
                </div>
                <div class="grid sm:grid-cols-2 gap-4"><label>Tanggal rapor<input type="date" name="tgl_rapor" value="{{ old('tgl_rapor', $rapor?->tgl_rapor?->format('Y-m-d')) }}"></label>
                <label>Keputusan akhir tahun (bila berlaku)<select name="keputusan"><option value="">Belum / tidak ditetapkan</option>@foreach(['Naik ke kelas berikutnya','Tinggal kelas','Lulus','Belum lulus'] as $decision)<option @selected(old('keputusan', $data['keputusan']) === $decision)>{{ $decision }}</option>@endforeach</select></label></div>
                <button class="btn">Simpan evaluasi</button>
            </fieldset>
            <a class="link" href="{{ route('wali.rapor.show', $selected->id) }}">Periksa kelengkapan dan preview rapor</a>
        </form>
    @endif
</x-academic-layout>
