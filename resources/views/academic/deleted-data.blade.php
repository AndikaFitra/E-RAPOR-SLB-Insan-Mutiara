<x-academic-layout title="Data Terhapus" :periode="\App\Models\TahunAjaran::getAktif()">
    <div class="panel">
        <h2>Data yang dapat dipulihkan</h2>
        <p>Data di bawah ini disembunyikan dari daftar aktif. Pemulihan akun tetap menonaktifkan login sampai admin mengaktifkannya kembali.</p>
        <div class="overflow-x-auto mt-5">
            <table class="dashboard-table dashboard-table--medium">
                <thead><tr><th>Jenis data</th><th>Nama</th><th>Terhapus pada</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td>{{ $row['label'] }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['deleted_at']->format('d-m-Y H:i') }}</td>
                            <td><form method="POST" action="{{ route('admin.data-terhapus.restore', [$row['type'], $row['id']]) }}" data-confirm="Pulihkan {{ $row['label'] }} ini ke daftar aktif?">@csrf<button class="btn-secondary" type="submit">Pulihkan</button></form></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="table-empty">Belum ada data terhapus.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-academic-layout>
