<div class="report-body">
    <h2>LAPORAN HASIL BELAJAR (RAPOR)</h2>
    <table class="report-identity">
        <tr><td style="width:19%">Nama peserta didik</td><td style="width:33%">: {{ $data['siswa']['nama_lengkap'] }}</td><td style="width:16%">Kelas / fase</td><td style="width:32%">: {{ $data['tingkat'] ?? '—' }} / {{ $data['fase'] ?? '—' }}</td></tr>
        <tr><td>NISN</td><td>: {{ $data['siswa']['nisn'] }}</td><td>Semester</td><td>: {{ $data['semester'] }}</td></tr>
        <tr><td>Sekolah</td><td>: {{ $data['sekolah']['nama'] }}</td><td>Tahun pelajaran</td><td>: {{ $data['tahun'] }}</td></tr>
        <tr><td>Alamat</td><td>: {{ $data['sekolah']['alamat'] ?? '—' }}</td><td>Rombongan belajar</td><td>: {{ $data['kelas'] ?? '—' }}</td></tr>
    </table>
    <table>
        <thead><tr><th style="width:7%;text-align:center">No.</th><th style="width:26%">Muatan Pelajaran</th><th style="width:12%;text-align:center">Nilai Akhir</th><th style="width:55%">Capaian Kompetensi</th></tr></thead>
        <tbody>
            @forelse($data['nilai'] as $index => $subject)
                <tr><td style="text-align:center">{{ $index + 1 }}</td><td>{{ $subject['nama'] }}</td><td style="text-align:center">{{ $subject['nilai'] ?? '—' }}</td><td class="narrative">{{ $subject['narasi'] ?: 'Belum diisi' }}</td></tr>
            @empty<tr><td colspan="4">Mata pelajaran belum ditetapkan.</td></tr>@endforelse
        </tbody>
    </table>
    <h3>Ekstrakurikuler</h3>
    <table><thead><tr><th style="width:7%;text-align:center">No.</th><th style="width:31%">Ekstrakurikuler</th><th>Nilai Akhir / Keterangan</th></tr></thead><tbody>
        @forelse($data['ekskul'] as $index => $activity)
            <tr><td style="text-align:center">{{ $index + 1 }}</td><td>{{ $activity['nama'] }}</td><td class="narrative">{{ $activity['keterangan'] ?: 'Belum diisi' }}</td></tr>
        @empty<tr><td colspan="3">Tidak ada kegiatan yang ditugaskan.</td></tr>@endforelse
    </tbody></table>
    <h3>Ketidakhadiran</h3>
    <table class="attendance" style="width:62%"><tbody>
        <tr><td style="width:70%">Sakit</td><td>{{ $data['evaluasi']['sakit'] ?? '—' }} hari</td></tr>
        <tr><td>Izin</td><td>{{ $data['evaluasi']['izin'] ?? '—' }} hari</td></tr>
        <tr><td>Tanpa keterangan</td><td>{{ $data['evaluasi']['alpa'] ?? '—' }} hari</td></tr>
    </tbody></table>
    <h3>Prestasi</h3>
    <table><thead><tr><th style="width:7%;text-align:center">No.</th><th style="width:45%">Kegiatan</th><th>Keterangan</th></tr></thead><tbody>
        @forelse($data['prestasi'] as $index => $achievement)
            <tr><td style="text-align:center">{{ $index + 1 }}</td><td>{{ $achievement['kegiatan'] }}</td><td>{{ $achievement['keterangan'] ?: '—' }}</td></tr>
        @empty<tr><td colspan="3">{{ ($data['evaluasi']['tidak_ada_prestasi'] ?? false) ? 'Tidak ada prestasi pada semester ini.' : 'Belum diisi.' }}</td></tr>@endforelse
    </tbody></table>
    <h3>Perkembangan Fisik dan Kesehatan</h3>
    <div class="note-box narrative">{{ $data['evaluasi']['perkembangan'] ?? 'Belum diisi' }}</div>
    <h3>Catatan Wali Kelas</h3>
    <div class="note-box narrative">{{ $data['catatan'] ?: 'Belum diisi' }}</div>
    <div class="decision"><strong>Keputusan:</strong> {{ $data['keputusan'] ?: 'Belum ditetapkan.' }}</div>
    <p style="text-align:right;margin-top:7mm">{{ $data['sekolah']['kota'] ?? '—' }}, {{ $data['tanggal'] ?: 'Tanggal belum ditetapkan' }}</p>
    <table class="report-signatures"><tr><td>Orang Tua / Wali<div class="signature-gap"></div>........................................</td><td>Wali Kelas<div class="signature-gap"></div><strong>{{ $data['wali'] ?? '—' }}</strong><br>NIP {{ $data['nip_wali'] ?? '—' }}</td></tr></table>
    <p style="text-align:center;margin-top:7mm">Mengetahui,<br>Kepala Sekolah</p><div class="signature-gap"></div>
    <p style="text-align:center"><strong>{{ $data['sekolah']['kepala_sekolah'] ?? '—' }}</strong><br>NIP {{ $data['sekolah']['nip_kepala'] ?? '—' }}</p>
</div>
