<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ActivityLog, Kelas, PenugasanSiswaKelas, Rapor, Siswa, TahunAjaran};
use App\Services\RaporService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif=TahunAjaran::getAktif();
        $kelasList=Kelas::all();
        $query=Siswa::with('penugasanKelas.kelas')
            ->when($request->search,fn($q)=>$q->search($request->search))
            ->when($request->kekhususan,fn($q)=>$q->where('kekhususan',$request->kekhususan))
            ->when($request->kelas_id,fn($q)=>$q->whereHas('penugasanKelas',fn($q)=>$q->where('kelas_id',$request->kelas_id)->where('tahun_ajaran_id',$tahunAktif?->id)))
            ->when($request->tingkat,fn($q)=>$q->whereHas('penugasanKelas',fn($q)=>$q->where('tingkat',$request->tingkat)->where('tahun_ajaran_id',$tahunAktif?->id)))
            ->when($request->status === 'aktif',fn($q)=>$q->where('is_aktif',true))
            ->when($request->status === 'nonaktif',fn($q)=>$q->where('is_aktif',false))
            ->when($request->filter === 'belum_lengkap',fn($q)=>$q->where(fn($q)=>$q->whereNull('tempat_lahir')->orWhereNull('tgl_lahir')));
        $siswaList=$query->orderByDesc('is_aktif')->orderBy('nama_lengkap')->get()->map(function($s) {
            $a=$s->penugasanAktif();
            return ['id'=>$s->id,'nama'=>$s->nama_lengkap,'nis'=>$s->nis ?? '—','nisn'=>$s->nisn,'kelas'=>$a?->kelas?->nama ?? 'Belum ditugaskan','tingkat_fase'=>($a?->tingkat ?? '—').' / '.($a?->fase ?? '—'),'kekhususan'=>$s->kekhususan,'status'=>$s->is_aktif?'Aktif':'Tidak Aktif'];
        })->all();
        return view('admin.siswa.index',compact('tahunAktif','kelasList','siswaList'));
    }

    public function create()
    {
        return $this->form(new Siswa(['is_aktif'=>true]));
    }

    public function edit(int $id)
    {
        return $this->form(Siswa::findOrFail($id));
    }

    private function form(Siswa $siswa)
    {
        $periode=TahunAjaran::getAktif();
        $kelasList=Kelas::all();
        $penugasan=$siswa->exists?$siswa->penugasanAktif():null;
        return view('academic.student-form',compact('siswa','periode','kelasList','penugasan'));
    }

    public function store(Request $request)
    {
        return $this->save($request,new Siswa());
    }

    public function update(Request $request,int $id)
    {
        return $this->save($request,Siswa::findOrFail($id));
    }

    private function save(Request $request,Siswa $siswa)
    {
        $input=$request->validate([
            'nama_lengkap'=>'required|string|max:100',
            'nisn'=>['required','digits:10',Rule::unique('siswa')->ignore($siswa->id)],
            'nis'=>['nullable','string','max:30',Rule::unique('siswa')->ignore($siswa->id)],
            'jenis_kelamin'=>'required|in:L,P','kekhususan'=>'required|in:Tunarungu,Tunagrahita,Autis',
            'tempat_lahir'=>'nullable|string|max:100','tgl_lahir'=>'nullable|date|before_or_equal:today',
            'agama'=>'nullable|string|max:50','alamat'=>'nullable|string|max:2000',
            'no_hp'=>'nullable|string|max:20','tanggal_diterima'=>'nullable|date','is_aktif'=>'required|boolean',
            'kelas_id'=>'nullable|integer|exists:kelas,id','tingkat'=>'nullable|required_with:kelas_id|in:I,II,III,IV,V,VI,VII,VIII,IX,X,XI,XII',
            'fase'=>'nullable|required_with:kelas_id|in:A,B,C,D,E,F','konfirmasi_pindah'=>'sometimes|accepted',
        ]);
        DB::transaction(function() use($input,$request,$siswa) {
            if($siswa->exists) Siswa::whereKey($siswa->id)->lockForUpdate()->firstOrFail();
            $periode=TahunAjaran::getAktif();
            $old=$siswa->exists?$siswa->penugasanAktif():null;
            $changed=($old?->kelas_id != ($input['kelas_id'] ?? null)) || ($old && ($old->tingkat !== ($input['tingkat'] ?? null) || $old->fase !== ($input['fase'] ?? null)));
            if($changed && $siswa->exists && Rapor::where('siswa_id',$siswa->id)->where('tahun_ajaran_id',$periode?->id)->whereIn('status',['diajukan','disetujui'])->exists()) {
                throw ValidationException::withMessages(['kelas_id'=>'Penempatan siswa dengan rapor terkunci tidak dapat diubah.']);
            }
            if($old && $old->kelas_id != ($input['kelas_id'] ?? null) && !$request->boolean('konfirmasi_pindah')) {
                throw ValidationException::withMessages(['konfirmasi_pindah'=>'Centang konfirmasi perpindahan setelah memeriksa kelas tujuan.']);
            }
            $siswa->fill(collect($input)->except(['kelas_id','tingkat','fase','konfirmasi_pindah'])->all())->save();
            if(!empty($input['kelas_id'])) {
                abort_unless($periode,422,'Aktifkan tahun ajaran sebelum menetapkan kelas.');
                PenugasanSiswaKelas::updateOrCreate(['siswa_id'=>$siswa->id,'tahun_ajaran_id'=>$periode->id],['kelas_id'=>$input['kelas_id'],'tingkat'=>$input['tingkat'],'fase'=>$input['fase']]);
                $wali = \App\Models\PenugasanWaliKelas::where('kelas_id', $input['kelas_id'])->where('tahun_ajaran_id', $periode->id)->first();
                if ($wali) Rapor::where('siswa_id', $siswa->id)->where('tahun_ajaran_id', $periode->id)->whereIn('status', ['draft', 'dikembalikan'])->update(['wali_kelas_id' => $wali->guru_id]);
            } elseif($old) $old->delete();
            ActivityLog::log('Menyimpan biodata siswa '.$siswa->nama_lengkap,$siswa,'Sukses');
        });
        return redirect()->route('admin.siswa.index')->with('success','Biodata siswa disimpan.');
    }

    public function destroy(Request $request,int $id)
    {
        $siswa=Siswa::findOrFail($id);
        // Preserve historical assignments and reports when archiving the student.
        $siswa->update(['is_aktif'=>false]);
        ActivityLog::log('Menonaktifkan siswa '.$siswa->nama_lengkap.'; arsip dipertahankan.',$siswa,'Sukses');
        return back()->with('success','Siswa dinonaktifkan. Arsip dan riwayat penilaian tetap tersedia.');
    }

    public function show(int $id,RaporService $reports)
    {
        $siswa=Siswa::with(['orangTua','penugasanKelas.kelas','penugasanKelas.tahunAjaran','rapor.tahunAjaran'])->findOrFail($id);
        $periode=TahunAjaran::getAktif();
        $history=$siswa->rapor->map(fn($r)=>['rapor'=>$r,'data'=>$reports->data($siswa,$r->tahunAjaran,$r)]);
        return view('academic.student-detail',compact('siswa','periode','history'));
    }
}
