<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ActivityLog, Guru, PenugasanSiswaKelas, Rapor, Sekolah, TahunAjaran, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function years()
    {
        $periode = TahunAjaran::getAktif();
        $years = TahunAjaran::orderByDesc('tahun')->get();
        $progress = collect();
        $graduatingCount = 0;
        if ($periode) {
            $assignments = PenugasanSiswaKelas::with('kelas')->where('tahun_ajaran_id', $periode->id)
                ->whereHas('siswa', fn ($q) => $q->where('is_aktif', true))->get();
            $approved = Rapor::where('tahun_ajaran_id', $periode->id)->where('status', Rapor::STATUS_DISETUJUI)
                ->pluck('siswa_id')->flip();
            $progress = $assignments->groupBy('kelas_id')->map(fn ($group) => [
                'kelas' => $group->first()->kelas?->nama ?? 'Kelas tidak tersedia',
                'total' => $group->count(),
                'disetujui' => $group->filter(fn ($assignment) => $approved->has($assignment->siswa_id))->count(),
            ])->values();
            $graduatingCount = $assignments->where('tingkat', 'XII')->count();
        }
        return view('academic.years', compact('periode', 'years', 'progress', 'graduatingCount'));
    }

    public function saveYear(Request $request, ?int $id = null)
    {
        $year = $id ? TahunAjaran::findOrFail($id) : new TahunAjaran();
        $input = $request->validate([
            'tahun'=>['required','regex:/^\d{4}\/\d{4}$/', Rule::unique('tahun_ajaran')->where('semester',$request->semester)->ignore($year->id)],
            'semester'=>'required|in:Ganjil,Genap', 'tgl_mulai'=>'nullable|date', 'tgl_selesai'=>'nullable|date|after_or_equal:tgl_mulai',
            'batas_pengisian'=>'nullable|date',
        ]);
        [$start,$end] = explode('/', $input['tahun']);
        if ((int)$end !== (int)$start + 1) return back()->withInput()->withErrors(['tahun'=>'Tahun ajaran harus dua tahun berurutan.']);
        $year->fill($input)->save();
        ActivityLog::log('Menyimpan tahun ajaran '.$year->label,$year,'Sukses');
        return back()->with('success','Tahun ajaran disimpan.');
    }

    public function school()
    {
        $periode = TahunAjaran::getAktif();
        $sekolah = Sekolah::instance();
        return view('academic.school',compact('periode','sekolah'));
    }

    public function saveSchool(Request $request)
    {
        $input = $request->validate([
            'nama'=>'required|string|max:150','npsn'=>'required|string|max:20','alamat'=>'required|string|max:1000',
            'kota'=>'required|string|max:100','provinsi'=>'required|string|max:100','kode_pos'=>'nullable|string|max:10',
            'telepon'=>'nullable|string|max:20','email'=>'nullable|email|max:100','kepala_sekolah'=>'required|string|max:100',
            'nip_kepala'=>'nullable|string|max:30','logo'=>'nullable|image|mimes:png,jpg,jpeg|max:8192',
        ]);
        unset($input['logo']);
        if ($request->hasFile('logo')) $input['logo'] = $request->file('logo')->store('school-logos','public');
        $school=Sekolah::instance();
        $school->update($input);
        ActivityLog::log('Memperbarui profil sekolah',$school,'Sukses');
        return back()->with('success','Profil sekolah disimpan.');
    }

    public function account(int $id)
    {
        $user=User::findOrFail($id);
        $guruList=Guru::all();
        $periode=TahunAjaran::getAktif();
        return view('academic.account',compact('user','guruList','periode'));
    }

    public function saveAccount(Request $request,int $id)
    {
        $user=User::findOrFail($id);
        $guruNip = $request->filled('guru_id') ? Guru::find($request->input('guru_id'))?->nip : null;
        if ($guruNip) $request->merge(['nip' => $guruNip]);
        $input=$request->validate([
            'name'=>'required|string|max:255', 'email'=>['required','email',Rule::unique('users')->ignore($id)],
            'nip'=>['required','digits:18',Rule::unique('users','nip')->ignore($id)],
            'role'=>'required|in:admin,kepala_sekolah,wali_kelas',
            'guru_id'=>['nullable','required_if:role,wali_kelas','exists:guru,id',Rule::unique('users')->ignore($id)],
            'is_aktif'=>'required|boolean','password'=>'nullable|string|min:8|confirmed',
        ]);
        if ($user->id === $request->user()->id && ($input['role'] !== 'admin' || !$input['is_aktif'])) {
            return back()->withErrors(['role'=>'Anda tidak dapat menonaktifkan atau mencabut akses admin akun yang sedang digunakan.']);
        }
        if (!empty($input['password'])) $input['password']=Hash::make($input['password']); else unset($input['password']);
        $user->update($input);
        ActivityLog::log('Memperbarui akun '.$user->email,$user,'Sukses');
        return back()->with('success','Akun disimpan.');
    }
}
