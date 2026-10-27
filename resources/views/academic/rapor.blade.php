<x-academic-layout title="Rapor Saya" :periode="$periode">
    <nav aria-label="Filter status rapor" class="flex flex-wrap items-center gap-2">
        @foreach(['draft', 'diajukan', 'dikembalikan', 'disetujui'] as $status)
            <a class="btn-secondary {{ request('status') === $status ? 'ring-2 ring-[#555783]' : '' }}"
               href="{{ route('wali.rapor.index', ['status' => $status]) }}">{{ ucfirst($status) }} · {{ $counts->get($status, 0) }}</a>
        @endforeach
        <a class="btn-secondary {{ request('status') ? '' : 'ring-2 ring-[#555783]' }}" href="{{ route('wali.rapor.index') }}">Semua</a>
    </nav>

    <form method="GET" class="panel flex flex-wrap items-end gap-3">
        @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
        <label class="min-w-[220px] flex-1">Cari siswa<input name="search" placeholder="Nama atau NISN" value="{{ request('search') }}"></label>
        <button class="btn">Cari</button>
    </form>

    <div class="panel overflow-x-auto">
        <table class="dashboard-table dashboard-table--wide">
            <thead><tr>
                <th scope="col">Siswa</th><th scope="col">Kelas / tingkat / fase</th>
                <th scope="col">Kelengkapan</th><th scope="col">Status</th><th scope="col">Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($rows as $r)
                    @php
                        $status = $r['rapor']?->status ?? 'draft';
                        $statusClass = match($status) {
                            'diajukan' => 'status-pill--submitted',
                            'dikembalikan' => 'status-pill--returned',
                            'disetujui' => 'status-pill--approved',
                            default => 'status-pill--draft',
                        };
                    @endphp
                    <tr>
                        <td class="table-student">{{ $r['siswa']->nama_lengkap }}</td>
                        <td>{{ $r['data']['kelas'] }} / {{ $r['data']['tingkat'] }} / {{ $r['data']['fase'] }}</td>
                        <td>{{ count(array_filter($r['checklist'])) }} / {{ count($r['checklist']) }} komponen</td>
                        <td><span class="status-pill {{ $statusClass }}">{{ ucfirst($status) }}</span></td>
                        <td><a class="table-action" href="{{ route('wali.rapor.show', $r['siswa']->id) }}">Preview / periksa kelengkapan</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="table-empty">Tidak ada rapor sesuai filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <a class="link" href="{{ route('wali.arsip.index') }}">Cari rapor semester sebelumnya</a>
</x-academic-layout>
