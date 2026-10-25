<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthAndDashboardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('E-Rapor SLB Insan Mutiara');
        $response->assertSee('Akses Cepat Akun Demo');
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'email'    => 'admin@slbinsanmutiara.sch.id',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_deactivated_user_cannot_login(): void
    {
        $response = $this->post('/login', [
            'email'    => 'nonaktif@slbinsanmutiara.sch.id',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi Administrator sekolah.']);
    }

    public function test_admin_can_authenticate_and_access_dashboard(): void
    {
        $response = $this->post('/login', [
            'email'    => 'admin@slbinsanmutiara.sch.id',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard'));

        $dashboardResponse = $this->get('/admin/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Dashboard Administrator');
    }

    public function test_kepala_sekolah_can_authenticate_and_access_dashboard(): void
    {
        $response = $this->post('/login', [
            'email'    => 'kepsek@slbinsanmutiara.sch.id',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('kepala.dashboard'));

        $dashboardResponse = $this->get('/kepala-sekolah/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Dashboard Kepala Sekolah');
    }

    public function test_wali_kelas_can_authenticate_and_access_dashboard(): void
    {
        $response = $this->post('/login', [
            'email'    => 'rinapratiwi@slbinsanmutiara.sch.id',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('wali.dashboard'));

        $dashboardResponse = $this->get('/wali-kelas/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Beranda Wali Kelas');
        $dashboardResponse->assertSee('Kelas Campuran Tingkat SLB');
    }

    public function test_wali_kelas_cannot_access_admin_dashboard(): void
    {
        $wali = User::where('role', 'wali_kelas')->first();

        $response = $this->actingAs($wali)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_user_can_logout(): void
    {
        $user = User::first();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
