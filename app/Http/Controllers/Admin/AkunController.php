<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AkunController extends Controller
{
    public function index(): View
    {
        $tahunAktif = TahunAjaran::getAktif();
        $userModels = User::with('guru')->get();

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
                'status'     => $u->is_aktif ? 'Aktif' : 'Tidak aktif',
                'last_login' => 'Hari ini, ' . now()->format('H.i'),
                'raw'        => $u,
            ];
        }

        return view('admin.akun.index', compact('tahunAktif', 'akunList'));
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
