<!doctype html>
<html lang="id"><head><meta charset="utf-8"><title>Rapor {{ $data['siswa']['nama_lengkap'] }}</title>
<style>
@page { size:A4; margin:17mm 18mm }
* { box-sizing:border-box } body { margin:0; color:#171717; font:10px/1.5 DejaVu Sans,sans-serif }
h1,h2,h3,p { margin:0 } .document-page { page-break-after:always } .cover { text-align:center }
.cover-heading { padding-top:18mm; font: bold 17px/1.6 DejaVu Serif,serif }
.cover-subtitle { margin-top:4mm; font-weight:bold; font-size:11px }
.cover-logo { display:block; width:32mm; height:32mm; margin:20mm auto 13mm; object-fit:contain }
.cover-logo-space { height:65mm } .cover-label { margin:7mm 0 2mm; font:bold 11px DejaVu Serif,serif }
.cover-box { width:110mm; margin:auto; padding:2mm 4mm; border:1.5px solid #222; font:bold 16px DejaVu Serif,serif; text-transform:uppercase }
.cover-footer { margin-top:26mm; font:bold 12px/1.7 DejaVu Serif,serif }
.section-title { text-align:center; text-transform:uppercase; font:bold 14px/1.55 DejaVu Serif,serif; margin-bottom:10mm }
.section-subtitle { text-align:center; font-weight:bold; margin:-8mm 0 11mm }
.detail-table { width:100%; border-collapse:collapse; font:10px DejaVu Serif,serif }
.detail-table td { border:0; vertical-align:top; padding:2.2mm 1mm }
.detail-table .label { width:61mm } .detail-table .colon { width:4mm; text-align:center }
.identity-signature { margin-top:12mm; width:100% } .identity-signature td { width:50%; text-align:center; vertical-align:top }
.photo-box { display:inline-block; width:30mm; height:40mm; border:1px solid #555 } .signature-gap { height:22mm }
.report-body { font-size:9.5px } .report-body h2 { text-align:center; font:bold 14px DejaVu Serif,serif; margin-bottom:6mm }
.report-body h3 { font-size:10px; margin:6mm 0 2mm } .report-body table { width:100%; border-collapse:collapse; margin:2mm 0 4mm; table-layout:fixed }
.report-body th,.report-body td { border:1px solid #444; padding:2mm; text-align:left; vertical-align:top; overflow-wrap:break-word }
.report-body th { background:#eee } .report-body thead { display:table-header-group } .report-body tr { page-break-inside:avoid }
.report-body .report-identity td,.report-body .report-signatures td { border:0 } .report-body .report-signatures td { text-align:center; width:50% }
.report-body .attendance { page-break-inside:avoid }
.report-body .narrative { white-space:pre-line } .report-body .note-box,.report-body .decision { border:1px solid #444; padding:3mm; page-break-inside:avoid }
.report-body .note-box { min-height:13mm } .report-body .decision { margin-top:6mm }
@media screen { body { background:#ececec } .document-page,.report-body { width:210mm; min-height:297mm; margin:14px auto; padding:17mm 18mm; background:white; box-shadow:0 2px 12px #0002 } }
@media print { body { background:white } .document-page,.report-body { margin:0; padding:0; width:auto; min-height:0; box-shadow:none } }
</style></head><body>
@php
    $grade = $data['tingkat'];
    $level = in_array($grade, ['I','II','III','IV','V','VI'], true) ? 'SEKOLAH DASAR LUAR BIASA (SDLB)' : (in_array($grade, ['VII','VIII','IX'], true) ? 'SEKOLAH MENENGAH PERTAMA LUAR BIASA (SMPLB)' : 'SEKOLAH MENENGAH ATAS LUAR BIASA (SMALB)');
    $shown = fn ($value) => filled($value) ? $value : '—';
    $parents = $data['orang_tua'] ?? [];
@endphp
<section class="document-page cover">
    <div class="cover-heading">LAPORAN<br>HASIL BELAJAR PESERTA DIDIK<br>{{ $level }}</div>
    <p class="cover-subtitle">JENIS KEKHUSUSAN: {{ strtoupper($shown($data['siswa']['kekhususan'] ?? null)) }}</p>
    @if(!empty($data['logo']))<img class="cover-logo" src="{{ $data['logo'] }}" alt="Logo sekolah">@else<div class="cover-logo-space"></div>@endif
    <p class="cover-label">NAMA PESERTA DIDIK</p><div class="cover-box">{{ $data['siswa']['nama_lengkap'] }}</div>
    <p class="cover-label">NISN</p><div class="cover-box">{{ $shown($data['siswa']['nisn']) }}</div>
    <div class="cover-footer">{{ strtoupper($data['sekolah']['nama']) }}<br>{{ strtoupper($shown($data['sekolah']['kota'] ?? null)) }}</div>
</section>
<section class="document-page">
    <h2 class="section-title">Rapor Peserta Didik<br>{{ $level }}</h2>
    <p class="section-subtitle">JENIS KEKHUSUSAN: {{ $shown($data['siswa']['kekhususan'] ?? null) }}</p>
    <table class="detail-table">
    @foreach(['Nama sekolah'=>$data['sekolah']['nama'] ?? null,'NPSN'=>$data['sekolah']['npsn'] ?? null,'Alamat sekolah'=>$data['sekolah']['alamat'] ?? null,'Kabupaten / kota'=>$data['sekolah']['kota'] ?? null,'Provinsi'=>$data['sekolah']['provinsi'] ?? null,'Kode pos'=>$data['sekolah']['kode_pos'] ?? null,'Telepon'=>$data['sekolah']['telepon'] ?? null,'Email'=>$data['sekolah']['email'] ?? null] as $label=>$value)
        <tr><td class="label">{{ $label }}</td><td class="colon">:</td><td>{{ $shown($value) }}</td></tr>
    @endforeach
    </table>
</section>
<section class="document-page">
    <h2 class="section-title">Keterangan Diri Peserta Didik</h2>
    <table class="detail-table">
    @foreach(['Nama peserta didik'=>$data['siswa']['nama_lengkap'],'NIS'=>$data['siswa']['nis'] ?? null,'NISN'=>$data['siswa']['nisn'],'Tempat, tanggal lahir'=>trim(($data['siswa']['tempat_lahir'] ?? '').', '.($data['tgl_lahir'] ?? ''), ', '),'Jenis kelamin'=>($data['siswa']['jenis_kelamin'] ?? null) === 'L' ? 'Laki-laki' : (($data['siswa']['jenis_kelamin'] ?? null) === 'P' ? 'Perempuan' : null),'Agama'=>$data['siswa']['agama'] ?? null,'Jenis kekhususan'=>$data['siswa']['kekhususan'] ?? null,'Alamat peserta didik'=>$data['siswa']['alamat'] ?? null,'Telepon peserta didik'=>$data['siswa']['no_hp'] ?? null,'Diterima di kelas'=>$data['tingkat'] ?? null,'Tanggal diterima'=>$data['tanggal_diterima'] ?? null,'Nama ayah'=>$parents['ayah']['nama'] ?? null,'Nama ibu'=>$parents['ibu']['nama'] ?? null,'Nama wali'=>$parents['wali']['nama'] ?? null,'Telepon orang tua / wali'=>$parents['ayah']['no_whatsapp'] ?? $parents['ibu']['no_whatsapp'] ?? $parents['wali']['no_whatsapp'] ?? null] as $label=>$value)
        <tr><td class="label">{{ $label }}</td><td class="colon">:</td><td>{{ $shown($value) }}</td></tr>
    @endforeach
    </table>
    <table class="identity-signature"><tr><td><span class="photo-box"></span><br><small>Pas foto 3 × 4</small></td><td>{{ $shown($data['sekolah']['kota'] ?? null) }}, {{ $shown($data['tanggal']) }}<br>Kepala Sekolah<div class="signature-gap"></div><strong>{{ $shown($data['sekolah']['kepala_sekolah'] ?? null) }}</strong><br>NIP {{ $shown($data['sekolah']['nip_kepala'] ?? null) }}</td></tr></table>
</section>
@include('reports.content')
</body></html>
