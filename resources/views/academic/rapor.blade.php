<x-academic-layout title="Rapor Saya" :periode="$periode">
    <div class="flex flex-wrap gap-3">@foreach(['draft','diajukan','dikembalikan','disetujui'] as $status)<a class="btn-secondary" href="{{ route('wali.rapor.index', ['status'=>$status]) }}">{{ ucfirst($status) }} · {{ $counts->get($status, 0) }}</a>@endforeach<a class="link" href="{{ route('wali.rapor.index') }}">Semua</a></div>
    <form method="GET" class="panel flex gap-3"><input aria-label="Cari rapor" name="search" placeholder="Nama atau NISN" value="{{ request('search') }}"><button class="btn">Cari</button></form>
    <div class="panel overflow-x-auto"><table><thead><tr><th>Siswa</th><th>Kelas / tingkat / fase</th><th>Kelengkapan</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
    @forelse($rows as $r)<tr><td>{{ $r['siswa']->nama_lengkap }}</td><td>{{ $r['data']['kelas'] }} · {{ $r['data']['tingkat'] }} / {{ $r['data']['fase'] }}</td><td>{{ count(array_filter($r['checklist'])) }} / {{ count($r['checklist']) }} komponen</td><td>{{ ucfirst($r['rapor']?->status ?? 'draft') }}</td><td><a class="link" href="{{ route('wali.rapor.show', $r['siswa']->id) }}">Preview / periksa kelengkapan</a></td></tr>@empty<tr><td colspan="5">Tidak ada rapor sesuai filter.</td></tr>@endforelse
    </tbody></table></div>
    <a class="link" href="{{ route('wali.arsip.index') }}">Cari rapor semester sebelumnya</a>
</x-academic-layout>
