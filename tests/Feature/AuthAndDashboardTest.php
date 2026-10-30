<?php

namespace Tests\Feature;

use App\Models\{Guru, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_forgot_help_render_without_demo_controls(): void
    {
        $this->get('/login')->assertOk()->assertSee('Lupa password?')->assertSee('Masukkan NIP')->assertDontSee('Akses Cepat Pengujian');
    }

    public function test_invalid_credentials_and_inactive_accounts_are_rejected(): void
    {
        User::factory()->create(['email'=>'inactive@example.test','nip'=>'199909092020012001','is_aktif'=>false,'password'=>bcrypt('secret123')]);
        $this->post('/login',['nip'=>'unknown','password'=>'wrong'])->assertSessionHasErrors('credentials');
        $this->post('/login',['nip'=>'199909092020012001','password'=>'secret123'])->assertSessionHasErrors('inactive');
        $this->assertGuest();
    }

    public function test_each_role_authenticates_to_its_dashboard_and_can_logout(): void
    {
        foreach (['admin'=>'admin.dashboard','wali_kelas'=>'wali.dashboard','kepala_sekolah'=>'kepala.dashboard'] as $role=>$route) {
            $user=User::factory()->create(['role'=>$role,'nip'=>sprintf('%018d', $role === 'admin' ? 1 : ($role === 'wali_kelas' ? 2 : 3)),'is_aktif'=>true,'password'=>bcrypt('secret123')]);
            $this->post('/login',['nip'=>$user->nip,'password'=>'secret123'])->assertRedirect(route($route));
            $this->assertAuthenticatedAs($user);
            $this->post('/logout')->assertRedirect(route('login'));
            $this->assertGuest();
        }
    }

    public function test_email_cannot_be_used_for_login(): void
    {
        $admin = User::factory()->create(['nip' => null, 'password' => bcrypt('secret123')]);
        $this->post('/login', ['nip' => $admin->email, 'password' => 'secret123'])->assertSessionHasErrors('credentials');
        $this->assertGuest();
    }

    public function test_role_access_is_enforced(): void
    {
        foreach (['admin', 'wali_kelas', 'kepala_sekolah'] as $role) {
            $user = User::factory()->create(['role' => $role, 'is_aktif' => true]);
            foreach (['admin' => '/admin/dashboard', 'wali_kelas' => '/wali-kelas/dashboard', 'kepala_sekolah' => '/kepala-sekolah/dashboard'] as $allowedRole => $path) {
                if ($role !== $allowedRole) {
                    $this->actingAs($user)->get($path)->assertForbidden();
                }
            }
        }
    }

    public function test_deactivated_account_loses_access_during_an_existing_session(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $this->actingAs($user)->get('/admin/dashboard')->assertOk();
        $user->update(['is_aktif' => false]);

        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_creating_teacher_account_requires_a_unique_nip_and_explicit_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        $details = [
            'nama_lengkap' => 'Guru Baru', 'nip' => '200101012026011001', 'jenis_kelamin' => 'P',
            'kekhususan' => 'Tunarungu', 'no_whatsapp' => '081234567890',
            'email' => 'gurubaru@example.test', 'status' => 'Aktif',
        ];

        $this->actingAs($admin)->post(route('admin.guru.store'), $details)->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('guru', ['nama_lengkap' => 'Guru Baru']);
        $this->post(route('admin.guru.store'), $details + [
            'password' => 'SandiBaruYangKuat!2026', 'password_confirmation' => 'SandiBaruYangKuat!2026',
        ])->assertRedirect(route('admin.guru.index'));

        $user = User::where('email', 'gurubaru@example.test')->firstOrFail();
        $this->assertSame($details['nip'], $user->nip);
        $this->assertSame(Guru::where('nama_lengkap', 'Guru Baru')->firstOrFail()->id, $user->guru_id);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('SandiBaruYangKuat!2026', $user->password));
        $this->assertFalse(\Illuminate\Support\Facades\Hash::check('password', $user->password));
    }

    public function test_admin_password_reset_requires_at_least_twelve_characters(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'nip' => '198001012000011001', 'is_aktif' => true]);
        $input = [
            'name' => $admin->name, 'email' => $admin->email, 'nip' => $admin->nip,
            'role' => 'admin', 'is_aktif' => 1,
        ];

        $this->actingAs($admin)->put(route('admin.akun.update', $admin->id), $input + [
            'password' => 'pendek1234', 'password_confirmation' => 'pendek1234',
        ])->assertSessionHasErrors('password');
        $this->put(route('admin.akun.update', $admin->id), $input + [
            'password' => 'SandiAdminBaruYangKuat!', 'password_confirmation' => 'SandiAdminBaruYangKuat!',
        ])->assertSessionHasNoErrors();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('SandiAdminBaruYangKuat!', $admin->fresh()->password));
    }
}
