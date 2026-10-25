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
                  ->orWhere('email', 'like', "%{$search}%");
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
                'role'       => $roleLabel,
                'role_raw'   => $u->role,
                'status'     => $u->is_aktif ? 'Aktif' : 'Tidak aktif',
                'last_login' => 'Hari ini, ' . now()->format('H.i'),
                'guru'       => $u->guru?->nama_lengkap,
                'raw'        => $u,
            ];
        }

        return view('admin.akun.index', compact('tahunAktif', 'akunList', 'guruList', 'search', 'roleFilter'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,kepala_sekolah,wali_kelas',
            'guru_id'  => 'nullable|exists:guru,id',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
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

        ActivityLog::log("Menghapus akun pengguna: {$email}", null, 'Sukses');

        return redirect()->route('admin.akun.index')->with('success', "Akun {$email} berhasil dihapus.");
    }

    public function resetPassword(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update(['password' => Hash::make('password')]);

        ActivityLog::log('Reset password akun: ' . $user->email, null, 'Sukses');

        return redirect()->route('admin.akun.index')
            ->with('success', 'Password akun ' . $user->email . ' berhasil direset menjadi "password".');
    }

    public function toggleStatus(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update(['is_aktif' => !$user->is_aktif]);

        ActivityLog::log('Mengubah status aktif akun: ' . $user->email, null, 'Sukses');

        return redirect()->route('admin.akun.index')
            ->with('success', 'Status akun ' . $user->email . ' berhasil diubah menjadi ' . ($user->is_aktif ? 'Aktif' : 'Tidak aktif') . '.');
    }
}
