<x-academic-layout title="Arsip dan Riwayat Perkembangan" :periode="$periode">
    <form method="GET" class="panel flex flex-wrap gap-3 items-end">
        <label>Periode<select name="tahun_ajaran_id">@foreach($tahunList as $t)<option value="{{ $t->id }}" @selected($periode->id === $t->id)>{{ $t->label }}</option>@endforeach</select></label>
        <label>Siswa<input name="search" value="{{ request('search') }}" placeholder="Nama atau NISN"></label>
        <button class="btn">Tampilkan</button>
    </form>

    <div class="panel archive-panel">
        <p class="archive-scroll-hint">Geser tabel ke samping untuk melihat status dan aksi.</p>
        <div class="archive-scroll">
            <table class="archive-table">
                <colgroup>
                    <col style="width:24%"><col style="width:25%"><col style="width:19%"><col style="width:14%"><col style="width:18%">
                </colgroup>
                <thead>
                    <tr>
                        <th scope="col">Siswa</th>
                        <th scope="col">Kelas / tingkat / fase saat itu</th>
                        <th scope="col">Periode</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        @php
                            $status = $row['rapor']->status;
                            $statusClass = match ($status) {
                                'disetujui' => 'is-approved',
                                'dikembalikan' => 'is-returned',
                                'diajukan' => 'is-submitted',
                                default => 'is-draft',
                            };
                        @endphp
                        <tr>
                            <td class="archive-student">{{ $row['data']['siswa']['nama_lengkap'] }}</td>
                            <td>{{ $row['data']['kelas'] }} / {{ $row['data']['tingkat'] }} / {{ $row['data']['fase'] }}</td>
                            <td>{{ $row['data']['tahun'] }} · {{ $row['data']['semester'] }}</td>
                            <td><span class="archive-status {{ $statusClass }}">{{ ucfirst($status) }}</span></td>
                            <td>
                                <div class="archive-actions">
                                    <a href="{{ route('reports.show', ['id'=>$row['rapor']->id, 'from'=>'arsip', 'tahun_ajaran_id'=>$periode->id, 'search'=>request('search')]) }}">Detail semester</a>
                                    <a href="{{ route('reports.cetak', ['id'=>$row['rapor']->id, 'pdf'=>1]) }}">PDF</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="archive-empty">Belum ada rapor pada periode dan filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-academic-layout>
