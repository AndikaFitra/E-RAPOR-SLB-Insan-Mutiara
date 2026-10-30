<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Guru;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AkunController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $guruList = Guru::all();

        $search = $request->query('search');
        $roleFilter = $request->query('role');

        $query = User::with('guru');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($roleFilter) {
            $query->where('role', $roleFilter);
        }

        $userModels = $query->orderBy('name')->get();

        $akunList = [];
        foreach ($userModels as $u) {
            $roleLabel = match($u->role) {
                'admin'          => 'Admin',
                'kepala_sekolah' => 'Kepala Sekolah',
                'wali_kelas'     => 'Wali Kelas',
                default          => ucfirst($u->role),
            };

            $akunList[] = [
                'id'         => $u->id,
                'name'       => $u->name,
                'username'   => explode('@', $u->email)[0],
                'email'      => $u->email,
                'nip'        => $u->nip,
                'role'       => $roleLabel,
                'role_raw'   => $u->role,
                'status'     => $u->is_aktif ? 'Aktif' : 'Tidak aktif',
                'last_login' => ActivityLog::where('user_id',$u->id)->where('subjek','Login ke sistem')->latest()->first()?->created_at?->format('d-m-Y H:i') ?? 'Belum tercatat',
                'guru'       => $u->guru?->nama_lengkap,
                'raw'        => $u,
            ];
        }

        return view('admin.akun.index', compact('tahunAktif', 'akunList', 'guruList', 'search', 'roleFilter'));
    }

    public function store(Request $request): RedirectResponse
    {
        $guruNip = $request->filled('guru_id') ? Guru::find($request->input('guru_id'))?->nip : null;
        if ($guruNip) $request->merge(['nip' => $guruNip]);
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'nip'      => ['required', 'digits:18', Rule::unique('users', 'nip')],
            'password' => 'required|string|min:8',
            'role'     => 'required|in:admin,kepala_sekolah,wali_kelas',
            'guru_id'  => 'nullable|required_if:role,wali_kelas|exists:guru,id|unique:users,guru_id',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'nip'      => $validated['nip'] ?? null,
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
            'guru_id'  => $validated['guru_id'] ?? null,
            'is_aktif' => true,
        ]);

        ActivityLog::log("Membuat akun pengguna baru: {$user->email} ({$user->role})", null, 'Sukses');

        return redirect()->route('admin.akun.index')
            ->with('success', "Akun {$user->email} berhasil ditambahkan ke sistem.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.akun.index')->with('error', 'Tidak dapat menghapus akun yang sedang aktif digunakan.');
        }

        $email = $user->email;
        $user->delete();

        ActivityLog::log("Mengarsipkan akun pengguna: {$email}", $user, 'Diarsipkan');

        return redirect()->route('admin.akun.index')->with('success', "Akun {$email} berhasil dihapus.");
    }

    public function resetPassword(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        return redirect()->route('admin.akun.edit',$user->id)->with('info','Isi password baru dan konfirmasinya untuk mereset akun.');
    }

    public function toggleStatus(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) return back()->with('error','Akun yang sedang digunakan tidak dapat dinonaktifkan.');
        $user->update(['is_aktif' => !$user->is_aktif]);

        ActivityLog::log('Mengubah status aktif akun: ' . $user->email, null, 'Sukses');

        return redirect()->route('admin.akun.index')
            ->with('success', 'Status akun ' . $user->email . ' berhasil diubah menjadi ' . ($user->is_aktif ? 'Aktif' : 'Tidak aktif') . '.');
    }
}
