<?php

namespace Tests\Feature;

use App\Models\User;
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
        $wali=User::factory()->create(['role'=>'wali_kelas','is_aktif'=>true]);
        $this->actingAs($wali)->get('/admin/dashboard')->assertForbidden();
    }
}
