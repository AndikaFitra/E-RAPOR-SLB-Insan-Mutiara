<x-academic-layout title="Persetujuan Rapor" :periode="$periode">
    <nav aria-label="Filter status persetujuan" class="flex flex-wrap gap-2">
        @foreach(['diajukan', 'dikembalikan', 'disetujui'] as $status)
            <a class="btn-secondary {{ request('status') === $status ? 'ring-2 ring-[#555783]' : '' }}"
               href="{{ request()->fullUrlWithQuery(['status' => $status]) }}">{{ ucfirst($status) }} · {{ $counts->get($status, 0) }}</a>
        @endforeach
        <a class="btn-secondary {{ request('status') ? '' : 'ring-2 ring-[#555783]' }}"
           href="{{ request()->fullUrlWithoutQuery('status') }}">Semua</a>
    </nav>

    <form method="GET" class="panel flex flex-wrap items-end gap-3">
        @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
        <label>Periode<select name="tahun_ajaran_id">@foreach($tahunList as $t)<option value="{{ $t->id }}" @selected($periode->id === $t->id)>{{ $t->label }}</option>@endforeach</select></label>
        <label>Kelas<select name="kelas_id"><option value="">Semua kelas</option>@foreach($kelasList as $k)<option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>{{ $k->nama }}</option>@endforeach</select></label>
        <label>Kekhususan<select name="kekhususan"><option value="">Semua</option>@foreach($kelasList->pluck('kekhususan')->unique() as $k)<option @selected(request('kekhususan') === $k)>{{ $k }}</option>@endforeach</select></label>
        <label>Siswa<input name="search" value="{{ request('search') }}" placeholder="Nama atau NISN"></label>
        <button class="btn">Terapkan</button>
    </form>

    <div class="panel overflow-x-auto">
        <table class="dashboard-table dashboard-table--wide">
            <thead><tr>
                <th scope="col">Siswa</th><th scope="col">Kelas / tingkat / fase</th>
                <th scope="col">Wali kelas</th><th scope="col">Diajukan</th>
                <th scope="col">Kelengkapan</th><th scope="col">Status</th><th scope="col">Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($rows as $r)
                    @php
                        $status = $r['rapor']->status;
                        $statusClass = match($status) {
                            'diajukan' => 'status-pill--submitted',
                            'dikembalikan' => 'status-pill--returned',
                            'disetujui' => 'status-pill--approved',
                            default => 'status-pill--draft',
                        };
                    @endphp
                    <tr>
                        <td class="table-student">{{ $r['data']['siswa']['nama_lengkap'] }}</td>
                        <td>{{ $r['data']['kelas'] }} / {{ $r['data']['tingkat'] }} / {{ $r['data']['fase'] }}</td>
                        <td>{{ $r['data']['wali'] }}</td>
                        <td>{{ $r['rapor']->tgl_pengajuan?->format('d-m-Y H:i') ?? '—' }}</td>
                        <td>{{ count(array_filter($r['checklist'])) }} / {{ count($r['checklist']) }}</td>
                        <td><span class="status-pill {{ $statusClass }}">{{ ucfirst($status) }}</span></td>
                        <td><a class="table-action" href="{{ route('reports.show', ['id' => $r['rapor']->id] + request()->only(['tahun_ajaran_id','kelas_id','kekhususan','search','status'])) }}">Review detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="table-empty">Tidak ada rapor sesuai filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-academic-layout>
