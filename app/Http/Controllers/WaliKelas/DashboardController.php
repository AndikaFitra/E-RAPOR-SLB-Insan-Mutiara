<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\{ActivityLog, TahunAjaran};
use App\Services\RaporService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request,RaporService $reports)
    {
        $periode=TahunAjaran::getAktif();
        $kelasList=$periode?$reports->kelas($request->user(),$periode->id)->get():collect();
        $students=$periode?$reports->siswa($request->user(),$periode->id)->get():collect();
        $total=$students->count();$academic=0;$target=0;$nonacademic=0;$approved=0;$revision=0;
        foreach($students as $s) {
            $rapor=$s->rapor()->where('tahun_ajaran_id',$periode->id)->first();
            $data=$reports->data($s,$periode,$rapor);
            $target+=count($data['nilai']);
            $academic+=collect($data['nilai'])->filter(fn($n)=>$n['nilai']!==null && filled($n['narasi']))->count();
            $checks=$reports->checklist($data);
            $nonacademic+=(int)($checks['Kehadiran dan perkembangan'] && $checks['Prestasi atau pernyataan tidak ada prestasi'] && $checks['Penilaian kegiatan ekstrakurikuler yang diikuti'] && $checks['Catatan wali kelas'] && $checks['Tanggal rapor']);
            $approved+=(int)($rapor?->isDisetujui()??false);$revision+=(int)($rapor?->isDikembalikan()??false);
        }
        $logs=ActivityLog::with('user')->where('user_id',$request->user()->id)->latest()->take(10)->get();
        return view('academic.dashboard',compact('periode','kelasList','total','academic','target','nonacademic','approved','revision','logs'));
    }
}
